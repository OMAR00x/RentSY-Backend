<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'طلبات التسجيل';
    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('status', 'pending');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')->label('الاسم الأول')->searchable(),
                TextColumn::make('last_name')->label('الاسم الأخير')->searchable(),
                TextColumn::make('phone')->label('رقم الهاتف')->searchable(),
                BadgeColumn::make('role')
                    ->label('النوع')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'owner' => 'مؤجر',
                        'renter' => 'مستأجر',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'owner',
                        'primary' => 'renter',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('لا يوجد طلبات تسجيل جديدة')
            ->recordActions([
                \Filament\Actions\ViewAction::make()
                    ->label('عرض التفاصيل')
                    ->button(),
                Action::make('approve')
                    ->label('قبول')
                    ->button()
                    ->color('success')
                    ->action(fn (User $record) => $record->update(['status' => 'approved'])),
                Action::make('reject')
                    ->label('رفض')
                    ->button()
                    ->color('danger')
                    ->action(fn (User $record) => $record->update(['status' => 'rejected'])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\UserResource\Pages\ListUsers::route('/'),
            'view' => \App\Filament\Resources\UserResource\Pages\ViewUser::route('/{record}'),
        ];
    }
}
