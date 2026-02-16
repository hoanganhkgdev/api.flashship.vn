<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Cài đặt hệ thống';

    protected static ?string $navigationGroup = 'Cấu hình Hệ thống';

    protected static ?string $modelLabel = 'Cài đặt';

    protected static ?string $pluralModelLabel = 'Cài đặt';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('key')
                    ->label('Khóa')
                    ->disabled()
                    ->required(),
                TextInput::make('value')
                    ->label('Giá trị')
                    ->required(),
                TextInput::make('group')
                    ->label('Nhóm')
                    ->disabled()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label('Khóa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextInputColumn::make('value')
                    ->label('Giá trị'),
                TextColumn::make('group')
                    ->label('Nhóm')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Cập nhật mới nhất')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->label('Nhóm')
                    ->options([
                        'bike' => 'Xe ôm',
                        'general' => 'Chung',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
