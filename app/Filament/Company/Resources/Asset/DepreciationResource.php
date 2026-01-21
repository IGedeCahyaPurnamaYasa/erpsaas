<?php

namespace App\Filament\Company\Resources\Asset;

use App\Filament\Company\Resources\Asset\DepreciationResource\Pages;
use App\Models\Asset\Depreciation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class DepreciationResource extends Resource
{
    protected static ?string $model = Depreciation::class;

    protected static ?string $tenantRelationshipName = 'depreciations';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-down';

    protected static ?string $navigationLabel = 'Depreciation';

    protected static ?string $modelLabel = 'Depreciation';

    protected static ?string $pluralModelLabel = 'Depreciations';

    protected static ?string $navigationGroup = 'Assets';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Depreciation Information')
                    ->schema([
                        Forms\Components\Select::make('asset_id')
                            ->relationship('asset', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Asset')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                            ->options(function (callable $get) {
                                return \App\Models\Asset\Asset::where('company_id', Auth::user()->current_company_id)
                                    ->pluck('name', 'id');
                            }),
                        Forms\Components\DatePicker::make('date')
                            ->required()
                            ->label('Depreciation Date'),
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->money()
                            ->label('Depreciation Amount'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('asset.name')
                    ->label('Asset')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('asset_id')
                    ->relationship('asset', 'name')
                    ->label('Asset')
                    ->options(function () {
                        return \App\Models\Asset\Asset::where('company_id', Auth::user()->current_company_id)
                            ->pluck('name', 'id');
                    }),
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('Date from'),
                        Forms\Components\DatePicker::make('date_until')
                            ->label('Date until'),
                    ])
                    ->query(function (array $data): \Illuminate\Database\Eloquent\Builder {
                        return Depreciation::query()
                            ->when(
                                $data['date_from'],
                                fn (\Illuminate\Database\Eloquent\Builder $query, $date): \Illuminate\Database\Eloquent\Builder => $query->whereDate('date', '>=', $date)
                            )
                            ->when(
                                $data['date_until'],
                                fn (\Illuminate\Database\Eloquent\Builder $query, $date): \Illuminate\Database\Eloquent\Builder => $query->whereDate('date', '<=', $date)
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['date_from'] ?? null) {
                            $indicators['date_from'] = 'Date from: ' . $data['date_from'];
                        }
                        if ($data['date_until'] ?? null) {
                            $indicators['date_until'] = 'Date until: ' . $data['date_until'];
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
            'index' => Pages\ListDepreciations::route('/'),
            'create' => Pages\CreateDepreciation::route('/create'),
            'edit' => Pages\EditDepreciation::route('/{record}/edit'),
        ];
    }
}