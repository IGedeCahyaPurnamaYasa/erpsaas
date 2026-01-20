<?php

namespace App\Filament\Company\Resources\Inventory;

use App\Filament\Company\Resources\Inventory\InventoryResource\Pages;
use App\Models\Inventory\Inventory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class InventoryResource extends Resource
{
    protected static ?string $model = Inventory::class;

    protected static ?string $tenantRelationshipName = 'inventories';

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Inventory';

    protected static ?string $modelLabel = 'Inventory Item';

    protected static ?string $pluralModelLabel = 'Inventory Items';

    protected static ?string $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Item Information')
                    ->schema([
                        Forms\Components\Select::make('offering_id')
                            ->relationship('offering', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Product/Service')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                            ->options(function (callable $get) {
                                return \App\Models\Common\Offering::where('purchasable', true)
                                    ->whereDoesntHave('inventory')
                                    ->pluck('name', 'id');
                            })
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('quantity_on_hand')
                            ->numeric()
                            ->required()
                            ->label('Quantity on Hand')
                            ->default(0),
                        Forms\Components\TextInput::make('reorder_level')
                            ->numeric()
                            ->label('Reorder Level')
                            ->default(0)
                            ->helperText('Minimum quantity before reordering'),
                        Forms\Components\TextInput::make('max_stock_level')
                            ->numeric()
                            ->label('Max Stock Level')
                            ->default(0)
                            ->helperText('Maximum stock to maintain'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Notes')
                    ->schema([
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
            ->columns([
                Tables\Columns\TextColumn::make('offering.name')
                    ->label('Product/Service')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity_on_hand')
                    ->label('Quantity on Hand')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => 
                        $record->quantity_on_hand <= $record->reorder_level ? 'danger' :
                        ($record->quantity_on_hand <= $record->max_stock_level * 0.8 ? 'warning' : 'success')
                    ),
                Tables\Columns\TextColumn::make('reorder_level')
                    ->label('Reorder Level')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_stock_level')
                    ->label('Max Stock Level')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('low_stock')
                    ->query(fn ($query) => $query->whereColumn('quantity_on_hand', '<=', 'reorder_level'))
                    ->label('Low Stock'),
                Tables\Filters\SelectFilter::make('offering_id')
                    ->relationship('offering', 'name')
                    ->label('Product/Service')
                    ->options(function () {
                        return \App\Models\Common\Offering::where('purchasable', true)
                            ->where('company_id', Auth::user()->current_company_id)
                            ->pluck('name', 'id');
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
            'index' => Pages\ListInventoryData::route('/'),
            'create' => Pages\CreateInventoryData::route('/create'),
            'edit' => Pages\EditInventoryData::route('/{record}/edit'),
        ];
    }
}