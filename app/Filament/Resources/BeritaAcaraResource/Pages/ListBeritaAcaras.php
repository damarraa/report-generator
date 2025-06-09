<?php

namespace App\Filament\Resources\BeritaAcaraResource\Pages;

use App\Filament\Resources\BeritaAcaraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBeritaAcaras extends ListRecords
{
    protected static string $resource = BeritaAcaraResource::class;
    protected static ?string $title = 'Daftar Berita Acara';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah Berita Acara'),
        ];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->hasAnyRole(['Admin', 'Petugas']);
    }
}
