<?php

namespace App\Filament\Resources\ApprovedUserResource\Pages;

use App\Filament\Resources\ApprovedUserResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;

class ViewApprovedUser extends Page
{
    protected static string $resource = ApprovedUserResource::class;
    public $record;

    public function mount($record): void
    {
        $this->record = ApprovedUserResource::resolveRecordRouteBinding($record);
    }

    public function getView(): string
    {
        return 'filament.resources.approved-user-resource.pages.view-approved-user';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addToWallet')
                ->label('إضافة 1000$')
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->action(function () {
                    $this->record->increment('wallet', 1000);
                    \Filament\Notifications\Notification::make()
                        ->title('تم إضافة 1000$ للمحفظة')
                        ->success()
                        ->send();
                }),
        ];
    }
}
