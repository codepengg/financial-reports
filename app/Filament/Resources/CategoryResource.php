<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-s-list-bullet';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Master Data';

    public static function canCreate(): bool
    {
        return auth()->user()->can('createcategory');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_expenses')
                    ->label('Is Expenses')
                    ->required(),
                Forms\Components\FileUpload::make('image')
                    ->label('Icon')
                    ->image()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => Category::listCategory($query))
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Icon'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_expenses')
                    ->label('Type')
                    ->trueIcon('heroicon-o-arrow-up-circle')
                    ->trueColor('danger')
                    ->falseIcon('heroicon-o-arrow-down-circle')
                    ->falseColor('success')
                    ->boolean(),
                TextColumn::make('created_by')
                    ->label('Created By')
                    ->visible(fn ($record) => auth()->user()->hasRole('admin'))
                    ->getStateUsing(fn(Category $model) => ucfirst($model->createdBy->name)),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('is_expenses', 'asc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => auth()->user()->hasRole('admin') || $record->created_by == auth()->user()->id),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
