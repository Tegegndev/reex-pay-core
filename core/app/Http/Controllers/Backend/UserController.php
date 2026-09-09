<?php

namespace App\Http\Controllers\Backend;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Events\TransactionUpdated;
use App\Models\Currency;
use App\Models\Merchant;
use App\Models\User;
use App\Models\UserFeature;
use App\Traits\FileManageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends BaseController
{
    use FileManageTrait;

    public static function permissions(): array
    {
        return [
            'index|activeUser|suspendedUser|unverifiedUser' => 'user-list',
            'store'                                         => 'user-create',
            'destroy'                                       => 'user-delete',
            'transactionStats'                              => 'user-list',
            'convertToMerchant'                             => 'user-manage',
        ];
    }

    /**
     * Display a listing of the users with filters.
     */
    public function index(Request $request)
    {

        $title = __('Users List');
        $users = User::query()->filter($request)->latest()->paginate(10)->withQueryString();

        return view('backend.user.index', compact('users', 'title'));
    }

    /**
     * Show active users.
     */
    public function activeUser(Request $request)
    {
        $title = __('Active Users');
        $users = User::query()->filter($request)->where('status', UserStatus::ACTIVE)->latest()->paginate(10)->withQueryString();

        $statusFilter = false;

        return view('backend.user.index', compact('users', 'title', 'statusFilter'));
    }

    public function suspendedUser(Request $request)
    {
        $title = __('Suspended Users');

        $users = User::query()->filter($request)->where('status', UserStatus::INACTIVE)->latest()->paginate(10)->withQueryString();

        return view('backend.user.index', compact('users', 'title'));
    }

    public function unverifiedUser(Request $request)
    {
        $title = __('Unverified Users');
        $users = User::query()->whereNull('email_verified_at')->filter($request)->latest()->paginate(10)->withQueryString();

        return view('backend.user.index', compact('users', 'title'));
    }

    public function kycUnverifiedUser(Request $request)
    {
        $title = __('KYC Unverified Users');
        $users = User::query()->kycUnverified()->filter($request)->latest()->paginate(10)->withQueryString();

        return view('backend.user.index', compact('users', 'title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Step 1: Validate incoming request
        $validated = $request->validate([
            'avatar'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'username'   => 'required|string|max:255|unique:users,username',
            'country'    => 'required|string',
            'phone'      => 'required|string',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|same:confirm_password',
            'status'     => 'nullable|boolean',
            'role'       => 'required|string',
        ]);

        // Step 2: Process phone number
        [$countryName, $countryCode] = explode(':', $validated['country']);
        $formattedPhone              = formatPhone($countryCode, $validated['phone']);

        // Step 3: Handle avatar upload if provided
        $avatarPath = $request->hasFile('avatar')
            ? $this->uploadImage($request->file('avatar'))
            : null;

        // Step 4: Create user with transformed data
        $user = User::create([
            'avatar'     => $avatarPath,
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'username'   => $validated['username'],
            'country'    => $countryName,
            'phone'      => $formattedPhone,
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'role'       => $validated['role'],
            'status'     => $request->boolean('status') ? UserStatus::ACTIVE : UserStatus::INACTIVE,
        ]);

        // Step 5: Sync features and fire event
        UserFeature::syncWithConfigForUser($user->id);
        event(new TransactionUpdated($user));

        // Step 6: Notify and redirect
        notifyEvs('success', __('User created successfully'));

        return redirect()->route('admin.user.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $user = User::findOrFail($id);

                // Delete data from tables that don't have cascade delete or need manual deletion
                // These tables either don't have foreign key constraints or need special handling

                // Delete notifications (Laravel's default notification system uses morphs)
                DB::table('notifications')
                    ->where('notifiable_type', 'App\\Models\\User')
                    ->where('notifiable_id', $id)
                    ->delete();

                // Delete login activities (nullable user_id, no cascade)
                DB::table('login_activities')->where('user_id', $id)->delete();

                // Delete support tickets and related messages
                $ticketIds = DB::table('tickets')->where('user_id', $id)->pluck('id');
                if ($ticketIds->isNotEmpty()) {
                    DB::table('messages')->whereIn('ticket_id', $ticketIds)->delete();
                    DB::table('tickets')->where('user_id', $id)->delete();
                }

                // Delete KYC submissions (no cascade constraint)
                DB::table('kyc_submissions')->where('user_id', $id)->delete();

                // Delete vouchers (has cascade but let's be explicit)
                DB::table('vouchers')->where('user_id', $id)->delete();

                // Delete wallets (constrained but no cascade delete)
                DB::table('wallets')->where('user_id', $id)->delete();

                // Finally delete the user - this will trigger cascade deletes for related tables
                $user->delete();

                // Log the deletion action
                Log::info('User deleted successfully', [
                    'deleted_user_id'  => $id,
                    'deleted_by_admin' => auth()->id(),
                    'deleted_at'       => now(),
                    'user_data'        => [
                        'name'      => $user->name,
                        'username'  => $user->username,
                        'email'     => $user->email,
                        'user_type' => $user->user_type ?? 'user',
                    ],
                ]);
            });

            notifyEvs('success', __('User deleted successfully'));

            return redirect()->route('admin.user.index');

        } catch (\Exception $e) {

            // Log the error
            Log::error('Failed to delete user', [
                'user_id'  => $id,
                'admin_id' => auth()->id(),
                'error'    => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);

            notifyEvs('error', __('Failed to delete user. Please try again.'));

            return redirect()->back();
        }
    }

    /**
     * Get transaction statistics for a user (used in delete confirmation modal)
     */
    public function transactionStats(string $id)
    {
        try {
            $user = User::findOrFail($id);

            // Get all transactions for this user
            $allTransactions = $user->transactions();

            // Get successful transactions only
            $successfulTransactions = $user->transactions()->where('status', \App\Enums\TrxStatus::COMPLETED);

            // Calculate statistics
            $totalTransactions = $allTransactions->count();
            $totalAmount       = $allTransactions->sum('amount');
            $successfulCount   = $successfulTransactions->count();
            $successfulAmount  = $successfulTransactions->sum('amount');

            // Get default currency for formatting
            $currencySymbol = siteCurrency('symbol');

            return response()->json([
                'success' => true,
                'data'    => [
                    'total_transactions'      => number_format($totalTransactions),
                    'total_amount'            => $currencySymbol.number_format($totalAmount, 2),
                    'successful_transactions' => number_format($successfulCount),
                    'successful_amount'       => $currencySymbol.number_format($successfulAmount, 2),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to fetch transaction statistics'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Convert a regular user to merchant.
     */
    public function convertToMerchant($id)
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($id);

            // Check if user is already a merchant
            if ($user->isMerchant()) {
                notifyEvs('error', __('User is already a merchant'));

                return redirect()->back();
            }

            // Check if user already has a merchant record
            if (Merchant::where('user_id', $user->id)->exists()) {
                notifyEvs('error', __('User already has a merchant account'));

                return redirect()->back();
            }

            // Update user role to merchant
            $user->update(['role' => UserRole::MERCHANT]);

            DB::commit();

            // Log the conversion
            Log::info('User converted to merchant', [
                'user_id'            => $user->id,
                'converted_by_admin' => auth()->id(),
                'converted_at'       => now(),
            ]);

            notifyEvs('success', __('User successfully converted to merchant'));

            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to convert user to merchant', [
                'user_id'  => $id,
                'admin_id' => auth()->id(),
                'error'    => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);

            notifyEvs('error', __('Failed to convert user to merchant. Please try again.'));

            return redirect()->back();
        }
    }
}
