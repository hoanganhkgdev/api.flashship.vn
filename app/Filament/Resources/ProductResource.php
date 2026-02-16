<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationLabel = 'Sản phẩm';

    protected static ?string $navigationGroup = 'Quản lý Đơn hàng';

    protected static ?string $modelLabel = 'Sản phẩm';

    protected static ?string $pluralModelLabel = 'Sản phẩm';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin món ăn')
                    ->schema([
                        Forms\Components\Select::make('store_id')
                            ->label('Cửa hàng')
                            ->relationship('store', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('service_category_id')
                            ->label('Danh mục hệ thống')
                            ->relationship('serviceCategory', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('name')
                            ->label('Tên món')
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->label('Mô tả')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('price')
                            ->label('Giá bán')
                            ->required()
                            ->numeric()
                            ->suffix('đ'),
                        Forms\Components\FileUpload::make('image')
                            ->label('Hình ảnh')
                            ->image()
                            ->directory('products'),
                        Forms\Components\Toggle::make('is_available')
                            ->label('Sẵn sàng bán')
                            ->default(true)
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Tùy chọn đi kèm (Toppings)')
                    ->schema([
                        Forms\Components\Repeater::make('optionGroups')
                            ->relationship('optionGroups')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Tên nhóm (VD: Chọn Size, Topping...)')
                                    ->required(),
                                Forms\Components\Toggle::make('is_required')
                                    ->label('Bắt buộc chọn')
                                    ->default(false),
                                Forms\Components\TextInput::make('max_selectable')
                                    ->label('Số lượng tối đa được chọn')
                                    ->numeric()
                                    ->default(1)
                                    ->required(),
                                Forms\Components\Repeater::make('options')
                                    ->relationship('options')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('tên tùy chọn')
                                            ->required(),
                                        Forms\Components\TextInput::make('price')
                                            ->label('Giá thêm')
                                            ->numeric()
                                            ->required()
                                            ->default(0),
                                        Forms\Components\Toggle::make('is_available')
                                            ->label('Sẵn sàng')
                                            ->default(true),
                                    ])->columns(3)
                                    ->columnSpanFull()
                                    ->label('Các món trong nhóm'),
                            ])->columns(3)
                            ->label('Các nhóm tùy chọn')
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('store.name')
                    ->label('Cửa hàng')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('serviceCategory.name')
                    ->label('Danh mục')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên món')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Giá')
                    ->money('VND', locale: 'vi_VN')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Hình ảnh'),
                Tables\Columns\IconColumn::make('is_available')
                    ->label('Sẵn sàng')
                    ->boolean(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
