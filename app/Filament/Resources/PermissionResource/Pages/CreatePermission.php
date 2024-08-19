<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Resources\PermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreatePermission extends CreateRecord
{
    protected static string $resource = PermissionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        $name = Str::slug($data['name']);

        $permission_type = config('constants.PERMISSION_TYPE');

        $permission_data = [];

        foreach ($permission_type as $type) {
            $insert = static::getModel()::create(['name' => "$type-$name", 'guard_name' => 'web']);
            $permission_data[] = $insert;

        }

        return $permission_data[0];
    }
}
