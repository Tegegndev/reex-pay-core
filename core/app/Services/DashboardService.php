<?php

namespace App\Services;

use App\Enums\TrxStatus;
use App\Enums\TrxType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Get transaction stats grouped by weekday (Sun–Sat).
     * Used for the user dashboard.
     */
    public function getTransactionStatsByDay(User $user, array $trxTypes = [TrxType::DEPOSIT, TrxType::WITHDRAW], int $days = 7): array
    {
        $dayLabels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        $transactions = $user->transactions()
            ->whereIn('trx_type', $trxTypes)
            ->whereDate('created_at', '>=', Carbon::now()->subDays($days))
            ->selectRaw("
                DATE_FORMAT(created_at, '%a') as label,
                trx_type,
                currency,
                SUM(CASE WHEN status = ? THEN amount ELSE 0 END) as success_total,
                SUM(CASE WHEN status IN (?, ?) THEN amount ELSE 0 END) as fail_total
            ", [
                TrxStatus::COMPLETED->value,
                TrxStatus::FAILED->value,
                TrxStatus::CANCELED->value,
            ])
            ->groupBy('label', 'trx_type', 'currency')
            ->get();

        return $this->mapChartData($transactions, $dayLabels, $trxTypes);
    }

    /**
     * Get transaction stats grouped by date (YYYY-MM-DD).
     * Used for admin dashboard or filtered charts.
     */
    public function getTransactionStatsByDate(array $trxTypes = [TrxType::DEPOSIT, TrxType::WITHDRAW], ?Carbon $from = null, ?Carbon $to = null): array
    {
        $from = $from ?? Carbon::now()->startOfMonth();
        $to   = $to   ?? Carbon::now()->endOfMonth();

        $transactions = DB::table('transactions')
            ->whereIn('trx_type', $trxTypes)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('
                DATE(created_at) as label,
                trx_type,
                currency,
                SUM(CASE WHEN status = ? THEN amount ELSE 0 END) as success_total,
                SUM(CASE WHEN status IN (?, ?) THEN amount ELSE 0 END) as fail_total
            ', [
                TrxStatus::COMPLETED->value,
                TrxStatus::FAILED->value,
                TrxStatus::CANCELED->value,
            ])
            ->groupBy('label', 'trx_type', 'currency')
            ->get();

        $dateLabels = collect();
        for ($date = $from->copy(); $date <= $to; $date->addDay()) {
            $dateLabels->push($date->format('Y-m-d'));
        }

        return $this->mapChartData($transactions, $dateLabels, $trxTypes);
    }

    /**
     * Get the total amount for a specific transaction type for a user in X days.
     * Used in user dashboard for quick stats.
     */
    public function getUserRecentTrxTotal(User $user, TrxType $type, int $days = 7): string
    {
        return $user->transactions()
            ->where('trx_type', $type)
            ->where('status', TrxStatus::COMPLETED)
            ->whereDate('created_at', '>=', Carbon::now()->subDays($days))
            ->selectRaw('currency, SUM(amount) as total')
            ->groupBy('currency')
            ->get()
            ->map(fn ($row) => getSymbol($row->currency).number_format($row->total, 2))
            ->implode(', ') ?: config('app.default_currency_symbol').'0';
    }

    /**
     * Get system-wide financial stats grouped by trx_type.
     * Used for admin dashboard.
     */
    public function getFinancialStatsByType(): array
    {
        return DB::table('transactions')
            ->where('status', TrxStatus::COMPLETED)
            ->selectRaw('trx_type, currency, SUM(amount) as total')
            ->groupBy('trx_type', 'currency')
            ->get()
            ->groupBy('trx_type')
            ->map(function ($items, $type) {
                return $items->map(fn ($row) => getSymbol($row->currency).number_format($row->total, 2))->implode(', ');
            })
            ->toArray();
    }

    /**
     * Common mapper to transform transaction stats for chart visualization.
     */
    private function mapChartData(Collection $transactions, Collection|array $labels, array $trxTypes): array
    {
        $result = [];

        foreach ($trxTypes as $type) {
            $result[$type->value] = collect($labels)->map(function ($label) use ($transactions, $type) {
                $filtered = $transactions->where('trx_type', $type)->where('label', $label);
                $currency = $filtered->first()?->currency ?? config('app.default_currency');

                return [
                    'label'         => $label,
                    'success_total' => $filtered->sum('success_total'),
                    'fail_total'    => $filtered->sum('fail_total'),
                    'symbol'        => getSymbol($currency),
                ];
            })->values();
        }

        return $result;
    }
}
