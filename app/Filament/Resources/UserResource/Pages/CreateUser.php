<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        try {
            DB::beginTransaction();

            $user = parent::handleRecordCreation($data);

            // Handle roles
            $roles = $this->normalizeRolePermissionInput($data['roles'] ?? []);
            $user->syncRoles($roles);

            // Handle direct permissions
            $permissions = $this->normalizeRolePermissionInput($data['direct_permissions'] ?? []);
            $user->syncPermissions($permissions);

            DB::commit();

            Notification::make()
                ->title('User berhasil dibuat')
                ->success()
                ->send();

            return $user;
        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title('Gagal membuat user')
                ->body($e->getMessage())
                ->danger()
                ->send();

            throw $e;
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

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()?->hasRole('Admin');
    }
}
