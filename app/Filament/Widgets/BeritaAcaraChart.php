<?php

namespace App\Filament\widgets;

use App\Models\BeritaAcara;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class BeritaAcaraChart extends ChartWidget
{
    protected static ?string $heading = 'Statistik Berita Acara';
    protected static ?string $maxHeight = '400px';
    protected static ?int $sort = 1;

    public string|int|array $columnSpan = 1;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => ['stepSize' => 1],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
        ];
    }

    protected function getData(): array
    {
        $data = Trend::model(BeritaAcara::class)
            ->between(
                now()->subMonths(6)->startOfMonth(),
                now()->endOfMonth()
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [[
                'label' => 'Berita Acara',
                'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                'backgroundColor' => '#f59e0b',
                'borderColor' => '#f59e0b',
                'fill' => false,
            ]],
            'labels' => $data->map(function (TrendValue $value) {
                $date = is_string($value->date) ? new \DateTime($value->date) : $value->date;
                return $date->format('M Y');
            }),
        ];
    }
}
