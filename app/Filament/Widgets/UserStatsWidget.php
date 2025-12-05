<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $approvedOwners = User::where('role', 'owner')->where('status', 'approved')->count();
        $approvedRenters = User::where('role', 'renter')->where('status', 'approved')->count();

        return [
            Stat::make('المؤجرين', $approvedOwners)
                ->description('عدد المؤجرين الموافق عليهم')
                ->icon('heroicon-o-building-office')
                ->color('success'),
            Stat::make('المستأجرين', $approvedRenters)
                ->description('عدد المستأجرين الموافق عليهم')
                ->icon('heroicon-o-user-group')
                ->color('primary'),
            Stat::make('إجمالي المستخدمين', $approvedOwners + $approvedRenters)
                ->description('إجمالي المستخدمين الموافق عليهم')
                ->icon('heroicon-o-users')
                ->color('info'),
        ];
    }
}
