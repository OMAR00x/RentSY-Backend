<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Resources\Resource;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ApprovedUserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'سجل المستخدمين';
    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('status', ['approved', 'rejected'])
            ->whereIn('role', ['renter', 'owner']);
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
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'owner' => 'مؤجر',
                        'renter' => 'مستأجر',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'owner',
                        'primary' => 'renter',
                    ]),
                BadgeColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'approved' => 'موافق',
                        'rejected' => 'مرفوض',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'approved' => 'موافق',
                        'rejected' => 'مرفوض',
                    ]),
                SelectFilter::make('role')
                    ->label('النوع')
                    ->options([
                        'owner' => 'مؤجر',
                        'renter' => 'مستأجر',
                    ]),
            ])
            ->recordActions([
                \Filament\Actions\Action::make('delete')
                    ->label('حذف')
                    ->button()
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->action(function (User $record) {
                        foreach ($record->images as $image) {
                            \Storage::disk('public')->delete($image->url);
                            $image->delete();
                        }
                        $record->delete();
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('لا يوجد مستخدمين');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ApprovedUserResource\Pages\ListApprovedUsers::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
