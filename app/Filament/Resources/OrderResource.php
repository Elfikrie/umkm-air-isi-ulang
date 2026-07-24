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
use Filament\Forms\Components\Repeater;

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
                Forms\Components\DateTimePicker::make('order_date')
                    ->required()
                    ->default(now()),
                Forms\Components\Select::make('processed_by')
                    ->relationship('processor', 'name', function (Builder $query) {
                        return $query->where('role', 'kasir');
                    })
                    ->label('Diproses Oleh')
                    ->default(auth()->user()?->id),
                    // ->disabled(),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull()
                    ->label('Catatan'),
                Repeater::make('items')
                    ->relationship()
                    ->label('Item Pesanan')
                    ->addActionLabel('Tambah Item')
                    ->schema([
                        Forms\Components\TextInput::make('quantity')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $price = (float) $get('price_at_order') ?? 0;
                                $set('subtotal', $price * $state);
                            }),
                        Forms\Components\Select::make('product_id')
                            ->relationship('product', 'name')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $product = \App\Models\Product::find($state);
                                if ($product) {
                                    $set('price_at_order', $product->price);
                                    $qty = (float) $get('quantity') ?? 1;
                                    $set('subtotal', $product->price * $qty);
                                }
                            }),
                        Forms\Components\TextInput::make('price_at_order')
                            ->required()
                            ->numeric()
                            ->live()
                            ->disabled(),
                        Forms\Components\TextInput::make('subtotal')
                            ->required()
                            ->numeric('float')
                            ->live()
                            ->disabled()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $quantity = $get('quantity') ?? 1;
                                $price = $get('price_at_order') ?? 0;
                                $set('subtotal', $price * $quantity);
                            }),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withSum('items', 'subtotal'))
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('processor.name')
                    ->label('Diproses Oleh')
                    ->default('kasir')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('items.product.name')
                    ->label('Produk')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state){
                        'pending' => 'warning',
                        'diterima' => 'success',
                        'dibatalkan' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('items_sum_subtotal')
                    ->label('Total_amount')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_date')
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
