<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('customer_id')
                    ->relationship('customer', 'name', fn (Builder $query) => $query->where('role', 'pelanggan'))
                    ->required()
                    ->searchable()
                    ->label('Pelanggan'),

                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'diterima' => 'Diterima',
                        'dibatalkan' => 'Dibatalkan',
                    ])
                    ->default('pending')
                    ->required(),

                Forms\Components\DateTimePicker::make('order_date')
                    ->required()
                    ->default(now()),

                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull(),

                Repeater::make('items')
                    ->relationship()
                    ->label('Item Pesanan')
                    ->addActionLabel('Tambah Item')
                    ->columnSpanFull()
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->relationship('product', 'name')
                            ->required()
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $product = Product::find($state);
                                $price = $product?->price ?? 0;
                                $set('price_at_order', $price);
                                $set('subtotal', $price * (float) ($get('quantity') ?? 1));
                            }),

                        Forms\Components\TextInput::make('quantity')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $price = (float) ($get('price_at_order') ?? 0);
                                $set('subtotal', $price * (float) $state);
                            }),

                        Forms\Components\TextInput::make('price_at_order')
                            ->label('Harga')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                    ->default('-'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'diterima' => 'success',
                        'dibatalkan' => 'danger',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('order_date')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('processed_at')
                    ->dateTime()
                    ->placeholder('-'),

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
                Tables\Actions\Action::make('accept')
                    ->label('Terima')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Terima Pesanan')
                    ->modalDescription('Apakah kamu yakin ingin menerima pesanan ini?')
                    ->visible(fn (Order $record) => $record->status === 'pending')
                    ->action(function (Order $record) {
                        $record->update([
                            'status' => 'diterima',
                            'processed_by' => auth()->id(),
                            'processed_at' => now(),
                        ]);
                    }),

                Tables\Actions\Action::make('cancel')
                    ->label('Batalkan')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Batalkan Pesanan')
                    ->modalDescription('Apakah kamu yakin ingin membatalkan pesanan ini?')
                    ->visible(fn (Order $record) => $record->status === 'pending')
                    ->action(function (Order $record) {
                        $record->update([
                            'status' => 'dibatalkan',
                            'processed_by' => auth()->id(),
                            'processed_at' => now(),
                        ]);
                    }),

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

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->role === 'admin';
    }
}
