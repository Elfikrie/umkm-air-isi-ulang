<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderItemResource\Pages;
use App\Filament\Resources\OrderItemResource\RelationManagers;
use App\Models\OrderItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderItemResource extends Resource
{
    protected static ?string $model = OrderItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('order_id')
                    ->relationship('order', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "Order #{$record->id} - {$record->customer->name} - {$record->order_date->format('d M Y')}")
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $product = \App\Models\Product::find($state);
                        if ($product) {
                            $set('price_at_order', $product->price);

                            $qty = $get('quantity') ?? 0;

                            $set('subtotal', $product->price * $qty);
                        }
                    }),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $price = $get('price_at_order')?? 0;
                        $set('subtotal', $price * $state);
                    }),
                Forms\Components\TextInput::make('price_at_order')
                    ->label('Harga')
                    ->disabled()
                    ->dehydrated(false)
                    ->numeric(),
                Forms\Components\TextInput::make('subtotal')
                    ->label('Subtotal')
                    ->disabled()
                    ->dehydrated(false)
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_id')
                    ->getStateUsing(fn ($record) => "Order #{$record->order->id} - {$record->order->customer->name} - {$record->order->order_date->format('d M Y')}")
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Produk')
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_at_order')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subtotal')
                    ->numeric()
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
            'index' => Pages\ListOrderItems::route('/'),
            'create' => Pages\CreateOrderItem::route('/create'),
            'edit' => Pages\EditOrderItem::route('/{record}/edit'),
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

    public static function canEdit($record): bool
    {
        return in_array(auth()->user()->role, ['admin', 'kasir']);
    }

    public static function canDelete($record): bool
    {
        return in_array(auth()->user()->role, ['admin']);
    }
}
