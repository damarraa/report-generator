<?php

namespace App\Filament\Resources\BeritaAcaraResource\Pages;

use App\Filament\Resources\BeritaAcaraResource;
use Filament\Resources\Pages\CreateRecord;
use Livewire\WithFileUploads;

class CustomCreateBeritaAcara extends CreateRecord
{
    use WithFileUploads;
    
    public $isUploading = false;
    
    protected static string $resource = BeritaAcaraResource::class;
    
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->model($this->getModel())
                    ->statePath('data')
                    ->schema(BeritaAcaraResource::getFormSchema()),
        )];
    }
}