<?php

namespace App\Filament\Admin\Widgets;

use App\Models\User;
use App\Models\Venue;
use App\Models\Deal;
use App\Models\Event;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Venues', Venue::count())
                ->icon('heroicon-o-building-office-2')
                ->color('primary'),

            Stat::make('Total Users', User::count())
                ->icon('heroicon-o-users')
                ->color('success'),

            Stat::make('Active Deals', Deal::where('is_active', true)->count())
                ->icon('heroicon-o-tag')
                ->color('warning'),

            Stat::make('Upcoming Events', Event::where('start_date', '>', now())->count())
                ->icon('heroicon-o-calendar-days')
                ->color('info'),

            Stat::make('Featured Venues', Venue::where('featured', true)->count())
                ->icon('heroicon-o-star')
                ->color('danger'),

            Stat::make('Venue Moderators', User::role('venue_moderator')->count())
                ->icon('heroicon-o-shield-check')
                ->color('gray'),
        ];
    }
}
