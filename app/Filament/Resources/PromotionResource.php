<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromotionResource\Pages;
use App\Models\Promotion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PromotionResource extends Resource
{
    protected static ?string $model = Promotion::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Marketing';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Promotion Information')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('e.g. FLASHSHIP50')
                            ->extraInputAttributes(['style' => 'text-transform: uppercase'])
                            ->dehydrateStateUsing(fn($state) => strtoupper($state)),
                        Forms\Components\TextInput::make('title')
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Discount Configuration')
                    ->schema([
                        Forms\Components\Select::make('discount_type')
                            ->options([
                                'fixed' => 'Fixed Amount',
                                'percent' => 'Percentage',
                            ])
                            ->required()
                            ->reactive(),
                        Forms\Components\TextInput::make('discount_value')
                            ->numeric()
                            ->required()
                            ->prefix(fn($get) => $get('discount_type') === 'fixed' ? 'đ' : '%'),
                        Forms\Components\TextInput::make('min_order_amount')
                            ->numeric()
                            ->default(0)
                            ->prefix('đ'),
                        Forms\Components\TextInput::make('max_discount_amount')
                            ->numeric()
                            ->hidden(fn($get) => $get('discount_type') === 'fixed')
                            ->prefix('đ'),
                    ])->columns(2),

                Forms\Components\Section::make('Usage Limits & Expiry')
                    ->schema([
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->required(),
                        Forms\Components\TextInput::make('usage_limit')
                            ->numeric()
                            ->placeholder('Infinite if null'),
                        Forms\Components\TextInput::make('used_count')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('discount_value')
                    ->formatStateUsing(fn($record) => $record->discount_type === 'fixed'
                        ? number_format($record->discount_value) . ' đ'
                        : $record->discount_value . ' %')
                    ->label('Discount'),
                Tables\Columns\TextColumn::make('min_order_amount')
                    ->money('VND')
                    ->sortable(),
                Tables\Columns\TextColumn::make('used_count')
                    ->label('Used')
                    ->description(fn($record) => $record->usage_limit ? "/ {$record->usage_limit}" : ''),
                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable()
                    ->color(fn($state) => $state < now() ? 'danger' : 'success'),
            ])
            ->filters([
                //
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
            'index' => Pages\ListPromotions::route('/'),
            'create' => Pages\CreatePromotion::route('/create'),
            'edit' => Pages\EditPromotion::route('/{record}/edit'),
        ];
    }
}
