<?php

namespace App\Http\Controllers\Backend;

use App\Enums\MerchantStatus;
use App\Enums\TicketStatus;
use App\Enums\TrxStatus;
use App\Enums\TrxType;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Merchant;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Services\CurrencyConversionService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Transaction as TransactionServiceFacade;

class DashboardController extends Controller
{
    public function index(Request $request)
    {

        $start = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->subDays(14)->startOfDay();
        $end   = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();

        $chartData = $this->generateChartData($start, $end);

        if ($request->ajax() && ($request->has('trx_chart') || $request->has('fee_chart') || $request->has('wallet_chart') || $request->has('end_date'))) {
            return match (true) {
                $request->has('trx_chart')    => response()->json($chartData),
                $request->has('fee_chart')    => response()->json($this->generateFeeChartData($start, $end)),
                $request->has('wallet_chart') => response()->json($this->generateWalletChartData($start, $end)),
                default                       => response()->json(['error' => 'Invalid chart type'], 400),
            };
        }

        $userStats   = $this->getUserAndMerchantStats();
        $walletStats = TransactionServiceFacade::getTransactionStatistics();

        $stats = $userStats->concat($walletStats);

        $walletBalances = $this->walletBalances();

        $users        = User::latest()->limit(6)->get();
        $transactions = Transaction::latest()->limit(6)->get();

        return view('backend.dashboard.index', compact('stats', 'chartData', 'walletBalances', 'users', 'transactions'));
    }

    protected function getUserAndMerchantStats(): \Illuminate\Support\Collection
    {
        return collect([
            [
                'title'       => __('All Users'),
                'value'       => User::count(),
                'icon'        => 'users-1',
                'color_class' => 'total',
                'link'        => route('admin.user.index'),
            ],
            [
                'title'       => __('All Active Users'),
                'value'       => User::active()->count(),
                'icon'        => 'users-5',
                'color_class' => 'active-svg',
                'link'        => route('admin.user.active'),
            ],
            [
                'title'       => __('All Verified Users'),
                'value'       => User::where('email_verified_at', '!=', null)->count(),
                'icon'        => 'user-6',
                'color_class' => 'success-svg',
            ],
            [
                'title'       => __('All Unverified Users'),
                'value'       => User::where('email_verified_at', null)->count(),
                'icon'        => 'user-7',
                'color_class' => 'danger-svg',
                'link'        => route('admin.user.unverified'),
            ],
            [
                'title'       => __('Merchant Users'),
                'value'       => User::where('role', UserRole::MERCHANT)->count(),
                'icon'        => 'users-3',
                'color_class' => 'info-svg',
            ],
            [
                'title'       => __('Verified Merchant Users'),
                'value'       => User::where('email_verified_at', '!=', null)->where('role', UserRole::MERCHANT)->count(),
                'icon'        => 'user-8',
                'color_class' => 'success-svg',
            ],
            [
                'title'       => __('Merchant Shops'),
                'value'       => Merchant::active()->count(),
                'icon'        => 'shop',
                'color_class' => 'merchant',
                'link'        => route('admin.merchant.index', ['status' => MerchantStatus::APPROVED]),
            ],
            [
                'title'       => __('Pending Support Ticket'),
                'value'       => Ticket::where('status', TicketStatus::PENDING)->count(),
                'icon'        => 'tickets',
                'color_class' => 'info-svg',
            ],
        ]);
    }

    protected function generateChartData(Carbon $start, Carbon $end): array
    {
        $defaultCurrency   = siteCurrency();
        $conversionService = app(CurrencyConversionService::class);

        $trxTypes = [
            'deposit'  => [TrxType::DEPOSIT],
            'withdraw' => [TrxType::WITHDRAW],
            'payment'  => [TrxType::PAYMENT],
            'reward'   => [TrxType::REWARD, TrxType::REFERRAL_REWARD],
        ];

        $transactions = Transaction::query()
            ->whereBetween('created_at', [$start, $end])
            ->where('status', TrxStatus::COMPLETED)
            ->select('trx_type', 'amount', 'currency', 'created_at')
            ->get();

        $grouped = [];

        foreach ($trxTypes as $key => $types) {
            $typeValues = array_map(fn ($t) => $t->value, $types);

            $grouped[$key] = collect($transactions)
                ->filter(fn ($trx) => in_array($trx->trx_type->value, $typeValues))
                ->groupBy(fn ($trx) => Carbon::parse($trx->created_at)->format('Y-m-d'))
                ->map(function ($dailyTrxs) use ($defaultCurrency, $conversionService) {
                    return $dailyTrxs->sum(function ($trx) use ($defaultCurrency, $conversionService) {
                        return $trx->currency === $defaultCurrency
                            ? $trx->amount
                            : $conversionService->convertCurrency($trx->amount, $trx->currency, $defaultCurrency) ?? 0;
                    });
                });
        }

        $dates = collect(CarbonPeriod::create($start, $end))
            ->map(fn ($date) => $date->format('Y-m-d'))
            ->toArray();

        $formatted = collect($trxTypes)->mapWithKeys(function ($types, $key) use ($grouped, $dates) {
            $seriesData = collect($dates)->map(fn ($d) => round($grouped[$key][$d] ?? 0, 2));

            return [$key => [
                'name'  => ucfirst($key),
                'total' => $seriesData->sum(),
                'data'  => $seriesData,
            ]];
        });

        return [
            'dates'  => $dates,
            'series' => $formatted->values()->toArray(),
        ];
    }

    protected function walletBalances()
    {
        return Currency::withCount('wallets')
            ->with('wallets')
            ->whereHas('wallets')
            ->get()
            ->map(function ($currency) {
                return [
                    'flag'     => $currency->flag,
                    'code'     => $currency->code,
                    'symbol'   => $currency->symbol,
                    'total'    => $currency->wallets->sum('balance'),
                    'count'    => $currency->wallets_count,
                    'bg_class' => match ($currency->code) {
                        'UGX'   => 'bg-usd',
                        'BDT'   => 'bg-bdt',
                        'EUR'   => 'bg-eur',
                        default => 'bg-default',
                    },
                ];
            });
    }

    protected function generateFeeChartData(Carbon $start, Carbon $end): array
    {
        $data = Transaction::selectRaw('DATE(created_at) as date, SUM(fee) as total_fee')
            ->whereBetween('created_at', [$start, $end])
            ->where('status', TrxStatus::COMPLETED)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $dates   = [];
        $amounts = [];

        foreach ($start->daysUntil($end) as $day) {
            $dateStr   = $day->toDateString();
            $dates[]   = $dateStr;
            $amounts[] = round(optional($data->get($dateStr))->total_fee ?? 0, 2);
        }

        return [
            'series' => [
                [
                    'name' => __('Fee'),
                    'data' => $amounts,
                ],
            ],
            'categories' => $dates,
        ];
    }

    protected function generateWalletChartData(Carbon $start, Carbon $end): array
    {
        $wallets = Wallet::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->whereBetween('created_at', [$start, $end])
            ->where('status', true)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $dates  = [];
        $values = [];

        foreach ($start->daysUntil($end) as $day) {
            $dateStr  = $day->toDateString();
            $dates[]  = $dateStr;
            $values[] = $wallets[$dateStr]->total ?? 0;
        }

        return [
            'series' => [[
                'name' => __('New Wallets'),
                'data' => $values,
            ]],
            'categories' => $dates,
        ];
    }
}
