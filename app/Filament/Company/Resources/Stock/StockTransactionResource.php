<?php

namespace App\Filament\Company\Resources\Stock;

use App\Filament\Company\Resources\Stock\StockTransactionResource\Pages;
use App\Models\Stock\StockTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StockTransactionResource extends Resource
{
    protected static ?string $model = StockTransaction::class;

    protected static ?string $tenantRelationshipName = 'stockTransactions';

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationLabel = 'Inventory Transactions';

    protected static ?string $modelLabel = 'Transaction';

    protected static ?string $pluralModelLabel = 'Transactions';

    protected static ?string $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Transaction Information')
                    ->schema([
                        Forms\Components\Select::make('inventory_id')
                            ->relationship('stock', 'id')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Stock Item')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->offering ? $record->offering->name : 'Item #' . $record->id),
                        Forms\Components\Select::make('transaction_type')
                            ->options([
                                'in' => 'Stock In',
                                'out' => 'Stock Out',
                                'adjustment' => 'Adjustment',
                            ])
                            ->required()
                            ->label('Transaction Type')
                            ->default('in')
                            ->live(),
                        Forms\Components\TextInput::make('quantity')
                            ->numeric()
                            ->required()
                            ->label('Quantity')
                            ->helperText(function ($get) {
                                $type = $get('transaction_type');
                                switch ($type) {
                                    case 'in':
                                        return 'Quantity to add to inventory';
                                    case 'out':
                                        return 'Quantity to remove from inventory';
                                    case 'adjustment':
                                        return 'Quantity adjustment (+ or -)';
                                    default:
                                        return 'Enter quantity';
                                }
                            }),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->maxLength(500),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with(['stock.offering']);
            })
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock.offering.name')
                    ->label('Product/Service')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction_type')
                    ->label('Type')
                    ->badge()
                    ->color(function ($record) {
                        switch ($record->transaction_type) {
                            case 'in':
                                return 'success';
                            case 'out':
                                return 'danger';
                            case 'adjustment':
                                return 'warning';
                            default:
                                return 'gray';
                        }
                    })
                    ->formatStateUsing(function ($state) {
                        switch ($state) {
                            case 'in':
                                return 'Stock In';
                            case 'out':
                                return 'Stock Out';
                            case 'adjustment':
                                return 'Adjustment';
                            default:
                                return $state;
                        }
                    }),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($record) => $record->isInTransaction() ? 'success' : ($record->isOutTransaction() ? 'danger' : 'warning'))
                    ->formatStateUsing(function ($state, $record) {
                        switch ($record->transaction_type) {
                            case 'in':
                                return '+' . $state;
                            case 'out':
                                return '-' . $state;
                            case 'adjustment':
                                return ($state >= 0 ? '+' : '') . $state;
                            default:
                                return $state;
                        }
                    }),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Notes')
                    ->toggleable()
                    ->limit(50),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('transaction_type')
                    ->options([
                        'in' => 'Stock In',
                        'out' => 'Stock Out',
                        'adjustment' => 'Adjustment',
                    ])
                    ->label('Transaction Type'),
                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('To Date'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['start_date'], fn ($query) => $query->whereDate('created_at', '>=', $data['start_date']))
                            ->when($data['end_date'], fn ($query) => $query->whereDate('created_at', '<=', $data['end_date']));
                    })
                    ->indicateUsing(function (array $data) {
                        if ($data['start_date'] || $data['end_date']) {
                            return 'Date: ' . ($data['start_date'] ?? '...') . ' - ' . ($data['end_date'] ?? '...');
                        }
                        return null;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListStockTransactions::route('/'),
            'create' => Pages\CreateStockTransaction::route('/create'),
            'edit' => Pages\EditStockTransaction::route('/{record}/edit'),
        ];
    }
}