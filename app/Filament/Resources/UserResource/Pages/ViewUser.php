<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;

class ViewUser extends Page
{
    protected static string $resource = UserResource::class;
    public $record;

    public function mount($record): void
    {
        $this->record = UserResource::resolveRecordRouteBinding($record);
    }

    public function getView(): string
    {
        return 'filament.resources.user-resource.pages.view-user';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('قبول')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action(fn () => $this->record->update(['status' => 'approved']))
                ->visible(fn () => $this->record->status === 'pending'),
            Action::make('reject')
                ->label('رفض')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->action(fn () => $this->record->update(['status' => 'rejected']))
                ->visible(fn () => $this->record->status === 'pending'),
        ];
    }
}
