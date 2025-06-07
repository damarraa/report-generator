<?php

namespace App\Filament\widgets;

use App\Models\BeritaAcara;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BeritaAcaraStatsOverview extends BaseWidget
{
    protected static bool $isCard = true;
    protected static ?int $sort = 3;

    public string|int|array $columnSpan = 'full';

    protected function getStats(): array
    {
        return [
            Stat::make('Total Berita Acara', BeritaAcara::count())
                ->description('Semua Waktu')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Berita Acara Bulan ini', BeritaAcara::whereMonth('created_at', now()->month)->count())
                ->description(now()->format('F Y'))
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('success'),

            Stat::make('Petugas Aktif', User::role('Petugas')->where('is_active', true)->count())
                ->description('Petugas lapangan')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('info')
                ->url(route('filament.admin.resources.users.index', [
                    'tableFilters[roles][values][0]' => 'Petugas',
                ])),
        ];
    }
}
