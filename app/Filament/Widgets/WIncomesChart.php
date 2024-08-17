<?php

namespace App\Filament\Widgets;

use App\Models\Transactions;
use Illuminate\Support\Carbon;

;

use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class WIncomesChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Income';
    protected static string $color = 'success';

    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $startDate = Carbon::parse($this->filters['startDate']);
        $endDate = Carbon::parse($this->filters['endDate']);

        $data = Trend::query(Transactions::incomes())
            ->dateColumn('date_transaction')
            ->between(
                start: $startDate,
                end: $endDate,
            )
            ->perDay()
            ->sum('amount');

        return [
            'datasets' => [
                [
                    'label' => 'Incomes',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => Carbon::parse($value->date)->format('d')),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
