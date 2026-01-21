<?php

namespace App\Filament\Company\Resources\Asset;

use App\Filament\Company\Resources\Asset\AssetResource\Pages;
use App\Models\Asset\Asset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $tenantRelationshipName = 'assets';

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Data';

    protected static ?string $modelLabel = 'Asset';

    protected static ?string $pluralModelLabel = 'Assets';

    protected static ?string $navigationGroup = 'Assets';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('AssetTabs')
                    ->columnSpanFull()
                    ->tabs([
                        static::getAssetInfoTab(),
                        static::getDepreciationTab(),
                    ]),
            ]);
    }

    protected static function getAssetInfoTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make('details')
            ->label('Details')
            ->icon('heroicon-o-cube')
            ->schema([
                Forms\Components\Section::make('Asset Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Asset Name'),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(500)
                            ->label('Description')
                            ->rows(2),
                        Forms\Components\Select::make('asset_type')
                            ->options([
                                'equipment' => 'Equipment',
                                'vehicle' => 'Vehicle',
                                'furniture' => 'Furniture',
                                'machinery' => 'Machinery',
                                'electronics' => 'Electronics',
                                'building' => 'Building',
                                'land' => 'Land',
                                'software' => 'Software',
                                'other' => 'Other',
                            ])
                            ->required()
                            ->label('Asset Type'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'maintenance' => 'Under Maintenance',
                                'retired' => 'Retired',
                                'disposed' => 'Disposed',
                            ])
                            ->required()
                            ->label('Status')
                            ->default('active'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Identification & Location')
                    ->schema([
                        Forms\Components\TextInput::make('serial_number')
                            ->maxLength(255)
                            ->label('Serial Number')
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('tag_number')
                            ->maxLength(255)
                            ->label('Tag Number')
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('location')
                            ->maxLength(255)
                            ->label('Location'),
                        Forms\Components\TextInput::make('assigned_to')
                            ->maxLength(255)
                            ->label('Assigned To'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Financial Information')
                    ->schema([
                        Forms\Components\DatePicker::make('acquisition_date')
                            ->label('Acquisition Date'),
                        Forms\Components\DatePicker::make('usage_date')
                            ->label('Usage Date'),
                        Forms\Components\TextInput::make('value')
                            ->money()
                            ->label('Current Value'),
                        Forms\Components\Select::make('depreciation_account_id')
                            ->relationship('depreciationAccount', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Depreciation Account')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name),
                        Forms\Components\Select::make('expense_account_id')
                            ->relationship('expenseAccount', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Expense Account')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->maxLength(1000),
                    ])
                    ->columns(1),
            ]);
    }

    protected static function getDepreciationTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make('depreciation')
            ->label('Depreciation')
            ->icon('heroicon-o-arrow-trending-down')
            ->schema([
                Forms\Components\Section::make('Asset Depreciation Records')
                    ->schema([
                        Forms\Components\Repeater::make('depreciations')
                            ->relationship()
                            ->schema([
                                Forms\Components\DatePicker::make('date')
                                    ->required()
                                    ->label('Depreciation Date'),
                                Forms\Components\TextInput::make('amount')
                                    ->required()
                                    ->money()
                                    ->label('Depreciation Amount'),
                            ])
                            ->columns(2)
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => 
                                isset($state['date']) && isset($state['amount']) 
                                    ? $state['date'] . ' - ' . '$' . number_format((float) str_replace(['$', ','], '', $state['amount']), 2)
                                    : null
                            )
                            ->addActionLabel('Add Depreciation')
                            ->collapsible()
                            ->reorderable(false),
                    ])
                    ->description('Manage depreciation records for this asset. Each record represents a depreciation entry with date and amount.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Asset Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('asset_type')
                    ->label('Type')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'equipment' => 'primary',
                        'vehicle' => 'success',
                        'furniture' => 'gray',
                        'machinery' => 'warning',
                        'electronics' => 'info',
                        'building' => 'danger',
                        'land' => 'secondary',
                        'software' => 'purple',
                        'other' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        'maintenance' => 'warning',
                        'retired' => 'danger',
                        'disposed' => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('serial_number')
                    ->label('Serial Number')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('location')
                    ->label('Location')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assigned_to')
                    ->label('Assigned To')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Value')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('acquisition_date')
                    ->label('Acquisition Date')
                    ->date()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('asset_type')
                    ->options([
                        'equipment' => 'Equipment',
                        'vehicle' => 'Vehicle',
                        'furniture' => 'Furniture',
                        'machinery' => 'Machinery',
                        'electronics' => 'Electronics',
                        'building' => 'Building',
                        'land' => 'Land',
                        'software' => 'Software',
                        'other' => 'Other',
                    ])
                    ->label('Asset Type'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'maintenance' => 'Under Maintenance',
                        'retired' => 'Retired',
                        'disposed' => 'Disposed',
                    ])
                    ->label('Status'),
                Tables\Filters\Filter::make('acquisition_date')
                    ->form([
                        Forms\Components\DatePicker::make('acquired_from')
                            ->label('Acquired from'),
                        Forms\Components\DatePicker::make('acquired_until')
                            ->label('Acquired until'),
                    ])
                    ->query(function (array $data): \Illuminate\Database\Eloquent\Builder {
                        return Asset::query()
                            ->when(
                                $data['acquired_from'],
                                fn (\Illuminate\Database\Eloquent\Builder $query, $date): \Illuminate\Database\Eloquent\Builder => $query->whereDate('acquisition_date', '>=', $date)
                            )
                            ->when(
                                $data['acquired_until'],
                                fn (\Illuminate\Database\Eloquent\Builder $query, $date): \Illuminate\Database\Eloquent\Builder => $query->whereDate('acquisition_date', '<=', $date)
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['acquired_from'] ?? null) {
                            $indicators['acquired_from'] = 'Acquired from: ' . $data['acquired_from'];
                        }
                        if ($data['acquired_until'] ?? null) {
                            $indicators['acquired_until'] = 'Acquired until: ' . $data['acquired_until'];
                        }
                        return $indicators;
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
            'index' => Pages\ListAssetData::route('/'),
            'create' => Pages\CreateAssetData::route('/create'),
            'edit' => Pages\EditAssetData::route('/{record}/edit'),
        ];
    }
}