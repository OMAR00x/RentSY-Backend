<?php

namespace App\Filament\Widgets;

use App\Models\Apartment;
use App\Models\Booking;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdvancedStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalApartments = Apartment::count();
        $pendingUsers = User::where('status', 'pending')->count();
        $totalBookings = Booking::count();

        return [
            Stat::make('عدد الشقق', $totalApartments)
                ->description('الشقق الموجودة في التطبيق')
                ->icon('heroicon-o-home')
                ->color('info')
                ->chart([7, 12, 15, 18, 22, 25, $totalApartments]),
            
            Stat::make('المستخدمين المعلقين', $pendingUsers)
                ->description('بانتظار الموافقة')
                ->icon('heroicon-o-clock')
                ->color($pendingUsers > 0 ? 'danger' : 'success')
                ->chart([2, 4, 3, 5, 4, 3, $pendingUsers]),
            
            Stat::make('إجمالي الحجوزات', $totalBookings)
                ->description('عدد الحجوزات في التطبيق')
                ->icon('heroicon-o-calendar')
                ->color('warning')
                ->chart([5, 10, 15, 20, 25, 30, $totalBookings]),
        ];
    }
}
