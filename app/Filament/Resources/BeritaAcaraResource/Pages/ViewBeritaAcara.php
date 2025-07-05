<?php

namespace App\Filament\Resources\BeritaAcaraResource\Pages;

use App\Filament\Resources\BeritaAcaraResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBeritaAcara extends ViewRecord
{
    protected static string $resource = BeritaAcaraResource::class;

    protected function getActions(): array
    {
        return [
            Actions\Action::make('downloadPDF')
                ->label('Download PDF')
                ->action(fn() => $this->record->generatePDF()),
        ];
    }
}
