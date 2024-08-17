<?php

namespace App\Filament\Widgets;

use App\Models\Transactions;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 0;

    protected function getStats(): array
    {

        $startDate = Carbon::parse($this->filters['startDate']);
        $endDate = Carbon::parse($this->filters['endDate']);

        $income = Transactions::incomes()->whereBetween('date_transaction', [$startDate, $endDate])->sum('amount');
        $expense = Transactions::expenses()->whereBetween('date_transaction', [$startDate, $endDate])->sum('amount');
        $diff = $income - $expense;

        return [
            Stat::make('Incomes', numberToIdr($income)),
            Stat::make('Expenses', numberToIdr($expense)),
            Stat::make('Differents', numberToIdr($diff)),
        ];
    }
}
