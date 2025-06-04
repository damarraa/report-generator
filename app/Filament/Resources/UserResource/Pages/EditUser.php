<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function beforeSave(): void
    {
        $this->data['roles'] = array_unique(array_filter((array)($this->data['roles'] ?? [])));
        $this->data['direct_permissions'] = array_unique(array_filter((array)($this->data['direct_permissions'] ?? [])));
    }

    protected function afterSave(): void
    {
        try {
            DB::transaction(function () {
                // Validasi dan sync roles
                $roles = Role::where('guard_name', 'web')
                    ->whereIn('name', $this->data['roles'])
                    ->get();

                $this->record->syncRoles($roles);

                // Validasi dan sync permissions
                $permissions = Permission::where('guard_name', 'web')
                    ->whereIn('name', $this->data['direct_permissions'])
                    ->get();

                $this->record->syncPermissions($permissions);
            });

            Notification::make()
                ->title('Update Berhasil')
                ->body('Role dan permission telah diperbarui')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal Update')
                ->body($e->getMessage())
                ->danger()
                ->send();

            $this->record->refresh();
        }
    }


    protected function normalizeRolePermissionInput(array $items): array
    {
        return collect($items)->map(function ($item) {
            if (is_numeric($item)) {
                return Role::find($item)?->name ?? Permission::find($item)?->name;
            }
            return $item;
        })->filter()->toArray();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn() => auth()->user()->hasRole('Admin')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()?->hasRole('Admin');
    }
}
