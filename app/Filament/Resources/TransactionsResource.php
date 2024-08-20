<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionsResource\Pages;
use App\Models\Transactions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransactionsResource extends Resource
{
    protected static ?string $model = Transactions::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Transactions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name', modifyQueryUsing: fn(Builder $query) => self::getDropdownData($query))
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('Name Transaction')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('date_transaction')
                    ->label('Date Transaction')
                    ->default(now()->timezone('Asia/Jakarta'))
                    ->format('Y-m-d')
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->prefix(label: 'IDR')
                    ->numeric(),
                Forms\Components\Textarea::make('note')
                    ->rows(5)
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('bill')
                    ->image()
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => Transactions::listTransactions($query))
            ->columns([
                Tables\Columns\ImageColumn::make('category.image')
                    ->label('#'),
                Tables\Columns\TextColumn::make('category.name')
                    ->description(fn(Transactions $transactions): string => $transactions->name)
                    ->label('Transactions')
                    ->sortable(),
                Tables\Columns\IconColumn::make('category.is_expenses')
                    ->label('Expenses')
                    ->boolean()
                    ->trueIcon('heroicon-o-arrow-up-circle')
                    ->trueColor('danger')
                    ->falseIcon('heroicon-o-arrow-down-circle')
                    ->falseColor('success'),
                Tables\Columns\TextColumn::make('date_transaction')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->getStateUsing(fn(Transactions $model): string => numberToIdr($model->amount))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user')
                    ->visible(fn($record) => auth()->user()->hasRole('admin'))
                    ->getStateUsing(fn(Transactions $model): string => ucfirst($model->user->name) ?? '-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('D, d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('D, d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime('D, d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransactions::route('/create'),
            'edit' => Pages\EditTransactions::route('/{record}/edit'),
        ];
    }

    private static function getDropdownData(Builder $query): Builder
    {
        $user = auth()->user();
        if (!$user->hasRole('admin')) {
            return $query->whereHas('users', function (Builder $query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }

        return $query;
    }
}
