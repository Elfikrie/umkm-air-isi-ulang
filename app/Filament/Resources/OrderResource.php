<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('customer_id')
                    ->relationship('customer', 'name', function (Builder $query) {
                        return $query->where('role', 'pelanggan');
                    })
                    ->required()
                    ->searchable()
                    ->label('pelanggan'),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'diterima' => 'Diterima',
                        'dibatalkan' => 'Dibatalkan'
                    ])
                    ->default('pending')
                    ->required(),
                Forms\Components\Select::make('product_id')
                    ->relationship('items', 'product_id')
                    ->multiple()
                    ->preload()
                    ->label('Produk')
                    ->required(),
                Forms\Components\TextInput::make('total_amount')
                        ->required()
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0.00)
                        ->disabled()
                        ->dehydrated(false)
                        ->label('Total Otomatis'),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull()
                    ->label('Catatan'),
                Forms\Components\DateTimePicker::make('order_date')
                    ->required()
                    ->default(now()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('processor.name')
                    ->label('Diproses Oleh')
                    ->default('-'),
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state){
                        'pending' => 'warning',
                        'diterima' => 'success',
                        'dibatalkan' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('processed_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'diterima' => 'Diterima',
                        'dibatalkan' => 'Dibatalkan',
                    ]),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()->role, ['admin', 'kasir']);
    }

    public static function canCreate(): bool
    {
        return in_array(auth()->user()->role, ['admin', 'kasir']);
    }

    public static function canEdit(Model $record): bool
    {
        return in_array(auth()->user()->role, ['admin', 'kasir']);
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->role === 'admin';
    }
}
