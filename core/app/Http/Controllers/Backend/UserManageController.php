<?php

namespace App\Http\Controllers\Backend;

use App\Data\TransactionData;
use App\Enums\AmountFlow;
use App\Enums\Gender;
use App\Enums\KycStatus;
use App\Enums\TrxStatus;
use App\Enums\TrxType;
use App\Enums\UserStatus;
use App\Exceptions\NotifyErrorException;
use App\Models\KycSubmission;
use App\Models\Referral;
use App\Models\Ticket;
use App\Models\Transaction as TransactionModel;
use App\Models\User;
use App\Models\UserFeature;
use App\Notifications\TemplateNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Enum;
use Throwable;
use Transaction;
use Wallet;

class UserManageController extends BaseController
{
    public static function permissions(): array
    {
        return [
            'manageUser|infoUpdate|passwordUpdate|notifyUser|handleTransactions' => 'user-manage',
            'updateBalance'                                                      => 'user-balance-manage',
            'loginAsUser'                                                        => 'user-login-as',
            'handleActivities'                                                   => 'user-activity-log',
            'updateFeatureStatus'                                                => 'user-features-manage',
            'notifyUser'                                                         => 'custom-notify-users',
            'handleTickets'                                                      => 'support-list',
        ];
    }

    /**
     * Manage user profile, features, settings, permissions, transactions and more
     *
     * This method will show a page with all the user's information and features,
     * and allow the admin to update the user's profile, toggle features on/off,
     * update the user's settings, and assign permissions, etc.
     *
     * This is the primary page for managing a user's entire profile and settings.
     * It is a central hub for managing a user from the admin side.
     */
    public function manageUser(Request $request, string $username, ?string $param = 'statistics')
    {
        $user = User::where('username', $username)->firstOrFail();

        return match ($param) {
            'transactions' => $this->handleTransactions($request, $user),
            'referrals'    => $this->handleReferrals($user),
            'tickets'      => $this->handleTickets($user),
            'activities'   => $this->handleActivities($request, $user),
            'info'         => view('backend.user.manage.info', compact('user')),
            'merchants'    => $user->isMerchant() ? view('backend.user.manage.merchant', compact('user')) : abort(404),
            'statistics'   => $this->handleDashboard($request, $user),
        };
    }

    public function infoUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'first_name'       => 'nullable|string|max:50',
            'last_name'        => 'nullable|string|max:50',
            'business_name'    => 'nullable|string|max:255',
            'business_address' => 'nullable|string|max:255',
            'phone'            => 'nullable|string|max:20|regex:/^[\d\-\+\(\) ]+$/',
            'gender'           => ['required', new Enum(Gender::class)],
            'birthday'         => 'nullable|date|before:today',
            'state'            => 'nullable',
            'city'             => 'nullable',
            'postal_code'      => 'nullable',
            'address'          => 'nullable|string|max:255',
        ], [
            'phone.regex'     => __('Invalid phone number format.'),
            'birthday.before' => __('Birthday must be a past date.'),
        ]);

        // Fetch the user by ID
        $user = User::findOrFail($id);
        $user->update($validated);

        notifyEvs('success', __('User information updated successfully'));

        return redirect()->back();
    }

    public function passwordUpdate($id, Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'password' => 'required|confirmed',
        ]);

        // Fetch the user by ID
        $user = User::findOrFail($id);

        // Update the user's password
        $user->password = Hash::make($validated['password']);
        $user->save();

        notifyEvs('success', __('Password updated successfully'));

        return redirect()->back();
    }

    /**
     * Login as a user.
     *
     * This method will allow the admin to login as a user.
     * It will be used to troubleshoot issues, view the user's dashboard,
     * and perform actions on behalf of the user.
     */
    public function loginAsUser(int $id): \Illuminate\Http\RedirectResponse
    {
        $user = User::findOrFail($id);
        Auth::guard('web')->login($user, true);

        return redirect()->route('user.dashboard');
    }

    /**
     * Update the user balance.
     *
     * This method will update the user's wallet balance.
     * It will be used to add or subtract money from the user's wallet.
     *
     * @throws NotifyErrorException|Throwable
     */
    public function updateBalance(Request $request)
    {
        // Validate request data
        $validated = $request->validate([
            'user_id'          => 'required|exists:users,id',
            'wallet_id'        => 'required|exists:wallets,id',
            'transaction_type' => 'required|in:add,subtract',
            'amount'           => 'required|numeric|min:0.01',
            'note'             => 'nullable|string|max:255',
        ]);

        // Fetch user and wallet
        $user   = User::findOrFail($validated['user_id']);
        $wallet = $user->wallets()->where('id', $validated['wallet_id'])->first();

        if (! $wallet) {
            throw new NotifyErrorException(__('Wallet not found'));
        }

        $amount   = abs($validated['amount']);
        $isAdding = $validated['transaction_type'] === 'add';

        // Determine transaction description (Use note if provided)
        $description = $validated['note'] ?? ($isAdding
            ? __('Added balance From Admin')
            : __('Subtract balance From Admin'));

        // Start a database transaction
        DB::beginTransaction();

        try {

            // Handle balance operation
            if (! $isAdding && $wallet->balance < $amount) {
                throw new NotifyErrorException(__('Insufficient balance in the wallet.'));
            }

            // Define transaction data
            $trxData = new TransactionData(
                user_id: $user->id,
                trx_type: $isAdding ? TrxType::ADD_BALANCE : TrxType::SUBTRACT_BALANCE,
                amount: $amount,
                amount_flow: $isAdding ? AmountFlow::PLUS : AmountFlow::MINUS,
                currency: $wallet->currency->code,
                net_amount: $amount,
                payable_amount: $amount,
                payable_currency: $wallet->currency->code,
                wallet_reference: $wallet->uuid,
                description: $description,
                status: TrxStatus::COMPLETED
            );

            // Create transaction log
            $trx = Transaction::create($trxData);

            // Perform balance update
            $isAdding ? Wallet::addMoney($wallet, $amount) : Wallet::subtractMoney($wallet, $amount);

            if ($isAdding) {
                $user->notify(new TemplateNotification(
                    identifier: 'admin_balance_added',
                    data: [
                        'amount' => $amount.' '.$wallet->currency->code,
                        'admin'  => auth()->guard('admin')->user()->name,
                        'trx'    => $trx->trx_id,
                    ],
                    action: route('user.transaction.index')
                ));
            }

            if (! $isAdding) {
                $user->notify(new TemplateNotification(
                    identifier: 'admin_balance_subtracted',
                    data: [
                        'amount' => $amount.' '.$wallet->currency->code,
                        'admin'  => auth()->guard('admin')->user()->name,
                        'trx'    => $trx->trx_id,
                    ],
                    action: route('user.transaction.index')
                ));
            }

            // Commit transaction
            DB::commit();

            notifyEvs('success', __('User balance updated successfully'));

            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();
            throw new NotifyErrorException(__('An error occurred while updating balance.'));
        }
    }

    /**
     * Update the user feature status.
     * This method will update the status of a user feature.
     * It will be used to toggle features on/off for a user.
     */
    public function updateFeatureStatus(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'feature' => 'required|string|exists:user_features,feature',
            'status'  => 'required|boolean',
        ]);

        // Update the user feature status
        UserFeature::where('user_id', $request->user_id)
            ->where('feature', $request->feature)
            ->update(['status' => $request->status]);

        // Fetch the user
        $user = User::findOrFail($request->user_id);

        // Check if account status is toggled
        if ($request->feature === 'account_status') {
            $user->status = $request->status ? UserStatus::ACTIVE : UserStatus::INACTIVE;
        }

        // Check if email verification is toggled
        if ($request->feature === 'email_verification') {
            $user->email_verified_at = $request->status ? now() : null;
        }

        // Check if KYC verification is toggled
        if ($request->feature === 'kyc_verification') {
            $kycSubmission = KycSubmission::where('user_id', $request->user_id)->first();

            if (! $kycSubmission) {
                return response()->json([
                    'type'    => 'error',
                    'message' => __('KYC submission not found'),
                ]);
            }

            $kycSubmission->status = $request->status ? KycStatus::APPROVED : KycStatus::REJECTED;
            $kycSubmission->save();
        }

        // Save changes to the user table
        $user->save();

        return response()->json([
            'type' => 'success', 'message' => __('Feature status updated successfully'),
        ]);
    }

    public function getUserTransactionSummaryChart(Request $request, $userId)
    {

        $start = Carbon::parse($request->start_date)->startOfDay();
        $end   = Carbon::parse($request->end_date)->endOfDay();

        $dates = collect();
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dates->push($date->format('Y-m-d'));
        }

        $transactions = TransactionModel::selectRaw('DATE(created_at) as date, status, COUNT(*) as total')
            ->where('user_id', $userId)
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date', 'status')
            ->orderBy('date')
            ->get()
            ->groupBy('status');

        $statuses = [
            TrxStatus::COMPLETED->value => 'Completed',
            TrxStatus::PENDING->value   => 'Pending',
            TrxStatus::FAILED->value    => 'Failed',
        ];

        $series = [];

        foreach ($statuses as $key => $label) {
            $group  = $transactions[$key] ?? collect();
            $counts = $dates->map(fn ($d) => (int) optional($group->firstWhere('date', $d))->total ?? 0);

            $series[] = [
                'name' => $label,
                'data' => $counts,
            ];
        }

        return response()->json([
            'series'     => $series,
            'categories' => $dates,
        ]);
    }

    protected function handleTransactions(Request $request, User $user)
    {
        $transactions = Transaction::getTransactions(
            user_id: $user->id,
            trx_type: $request->type,
            status: $request->status,
            search: $request->search,
            dateRange: $request->daterange
        );

        return view('backend.user.manage.transactions', compact('user', 'transactions'));
    }

    protected function handleReferrals(User $user)
    {
        $referrals = Referral::with('childReferrals.referredUser')
            ->where('user_id', $user->id)
            ->get();

        return view('backend.user.manage.referrals', compact('user', 'referrals'));
    }

    protected function handleTickets(User $user)
    {
        $tickets = Ticket::where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('backend.user.manage.tickets', compact('user', 'tickets'));
    }

    protected function handleActivities(Request $request, User $user)
    {
        $activities = $user->loginActivities()
            ->filter($request)
            ->paginate(10);

        return view('backend.user.manage.activities_log', compact('user', 'activities'));
    }

    protected function handleDashboard(Request $request, User $user)
    {
        if ($request->ajax()) {
            return $this->getUserTransactionSummaryChart($request, $user->id);
        }

        $stats = Transaction::getTransactionStatistics();

        $othersStats = [
            [
                'title'       => __('Pending Tickets'),
                'value'       => Ticket::pending()->count(),
                'icon'        => 'tickets',
                'color_class' => 'tickets',
            ],
        ];

        $stats = array_merge($stats->toArray(), $othersStats);

        return view('backend.user.manage.statistics', compact('stats', 'user'));
    }
}
