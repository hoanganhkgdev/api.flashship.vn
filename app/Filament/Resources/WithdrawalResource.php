<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WithdrawalResource\Pages;
use App\Models\Withdrawal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WithdrawalResource extends Resource
{
    protected static ?string $model = Withdrawal::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Rút tiền';

    protected static ?string $modelLabel = 'Yêu cầu rút tiền';

    protected static ?string $pluralModelLabel = 'Yêu cầu rút tiền';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('bank_name')
                    ->required(),
                Forms\Components\TextInput::make('account_number')
                    ->required(),
                Forms\Components\TextInput::make('account_holder')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Chờ duyệt',
                        'approved' => 'Đã duyệt',
                        'rejected' => 'Đã từ chối',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Tài xế')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Số tiền')
                    ->money('VND')
                    ->sortable(),
                Tables\Columns\TextColumn::make('bank_name')
                    ->label('Ngân hàng'),
                Tables\Columns\TextColumn::make('account_number')
                    ->label('Số tài khoản'),
                Tables\Columns\TextColumn::make('account_holder')
                    ->label('Chủ tài khoản'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('H:i d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Chờ duyệt',
                        'approved' => 'Đã duyệt',
                        'rejected' => 'Đã từ chối',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Duyệt')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(Withdrawal $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (Withdrawal $record) {
                        $record->update(['status' => 'approved']);
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Từ chối')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(Withdrawal $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('reject_reason')
                            ->label('Lý do từ chối')
                            ->required(),
                    ])
                    ->action(function (Withdrawal $record, array $data) {
                        \DB::transaction(function () use ($record, $data) {
                            $record->update([
                                'status' => 'rejected',
                                'notes' => $data['reject_reason'],
                            ]);
                            // Refund balance
                            $record->user->increment('balance', $record->amount);

                            // Record transaction
                            \App\Models\WalletTransaction::create([
                                'user_id' => $record->user_id,
                                'amount' => $record->amount,
                                'type' => 'topup',
                                'description' => 'Hoàn tiền lệnh rút #' . $record->id . ' (Bị từ chối)',
                                'reference_id' => $record->id,
                            ]);
                        });
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageWithdrawals::route('/'),
        ];
    }
}
