<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Filament\Resources\ReviewResource\RelationManagers;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationLabel = 'Đánh giá';
    protected static ?string $modelLabel = 'Đánh giá';
    protected static ?string $pluralModelLabel = 'Đánh giá';
    protected static ?string $navigationGroup = 'Quản lý Đối tác';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin đánh giá')
                    ->schema([
                        Forms\Components\Select::make('order_id')
                            ->label('Mã đơn hàng')
                            ->relationship('order', 'id')
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('user_id')
                            ->label('Khách hàng')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('store_id')
                            ->label('Cửa hàng')
                            ->relationship('store', 'name')
                            ->searchable(),
                        Forms\Components\Select::make('driver_id')
                            ->label('Tài xế')
                            ->relationship('driver', 'name')
                            ->searchable(),
                        Forms\Components\Select::make('rating')
                            ->label('Xếp hạng')
                            ->options([
                                5 => '5 Sao',
                                4 => '4 Sao',
                                3 => '3 Sao',
                                2 => '2 Sao',
                                1 => '1 Sao',
                            ])
                            ->required()
                            ->default(5),
                        Forms\Components\Textarea::make('comment')
                            ->label('Nội dung nhận xét')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Khách hàng')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('store.name')
                    ->label('Cửa hàng')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Xếp hạng')
                    ->formatStateUsing(fn(int $state): string => str_repeat('⭐', $state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('comment')
                    ->label('Nhận xét')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày gửi')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rating')
                    ->label('Xếp hạng')
                    ->options([
                        5 => '5 Sao',
                        4 => '4 Sao',
                        3 => '3 Sao',
                        2 => '2 Sao',
                        1 => '1 Sao',
                    ]),
                Tables\Filters\SelectFilter::make('store_id')
                    ->label('Cửa hàng')
                    ->relationship('store', 'name'),
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
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
