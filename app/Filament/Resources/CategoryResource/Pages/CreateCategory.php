<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Models\UserHasCategory;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();
        $data['created_by'] = $user->id;
        $category = static::getModel()::create($data);

        if(!$user->hasRole('admin')) {
            $user_id = $user->id;
            UserHasCategory::create(['user_id' => $user_id, 'category_id' => $category->id]);
        }

        return $category;
    }
}
