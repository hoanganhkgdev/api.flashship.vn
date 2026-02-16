<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoreResource\Pages;
use App\Filament\Resources\StoreResource\RelationManagers;
use App\Models\Store;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StoreResource extends Resource
{
    protected static ?string $model = Store::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationLabel = 'Cửa hàng';

    protected static ?string $navigationGroup = 'Quản lý Đối tác';

    protected static ?string $modelLabel = 'Cửa hàng';

    protected static ?string $pluralModelLabel = 'Cửa hàng';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin cơ bản')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Chủ sở hữu')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('service_id')
                            ->label('Dịch vụ')
                            ->relationship('service', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->label('Tên cửa hàng')
                            ->required(),
                        Forms\Components\TextInput::make('address')
                            ->label('Địa chỉ')
                            ->required(),
                        Forms\Components\TextInput::make('phone')
                            ->label('Số điện thoại')
                            ->tel(),
                        Forms\Components\TextInput::make('lat')
                            ->label('Vĩ độ')
                            ->numeric(),
                        Forms\Components\TextInput::make('lng')
                            ->label('Kinh độ')
                            ->numeric(),
                        Forms\Components\FileUpload::make('image')
                            ->label('Ảnh bìa')
                            ->image()
                            ->directory('stores'),
                        Forms\Components\FileUpload::make('logo')
                            ->label('Logo cửa hàng')
                            ->image()
                            ->directory('stores/logos'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Đang hoạt động')
                            ->default(true)
                            ->required(),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Cửa hàng nổi bật ⭐')
                            ->default(false)
                            ->helperText('Hiển thị cửa hàng này trong danh sách nổi bật trên trang chủ'),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Chủ sở hữu')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('service.name')
                    ->label('Dịch vụ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên cửa hàng')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Địa chỉ')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Điện thoại')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Ảnh bìa')
                    ->size(50),
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo')
                    ->size(40)
                    ->circular(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Hoạt động')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Nổi bật')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Đánh giá')
                    ->badge()
                    ->color(fn($state) => $state >= 4.5 ? 'success' : ($state >= 4.0 ? 'warning' : 'danger'))
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
                Tables\Filters\SelectFilter::make('service_id')
                    ->label('Dịch vụ')
                    ->relationship('service', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Trạng thái hoạt động'),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Nổi bật'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggle_featured')
                    ->label('Đánh dấu nổi bật')
                    ->icon('heroicon-o-star')
                    ->action(function (Store $record) {
                        $record->update(['is_featured' => !$record->is_featured]);
                    })
                    ->color(fn(Store $record) => $record->is_featured ? 'warning' : 'gray')
                    ->requiresConfirmation(false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('mark_featured')
                        ->label('Đánh dấu nổi bật')
                        ->icon('heroicon-o-star')
                        ->action(fn($records) => $records->each->update(['is_featured' => true]))
                        ->color('warning')
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('unmark_featured')
                        ->label('Bỏ đánh dấu nổi bật')
                        ->icon('heroicon-o-star')
                        ->action(fn($records) => $records->each->update(['is_featured' => false]))
                        ->color('gray')
                        ->requiresConfirmation(),
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
            'index' => Pages\ListStores::route('/'),
            'create' => Pages\CreateStore::route('/create'),
            'edit' => Pages\EditStore::route('/{record}/edit'),
        ];
    }
}
