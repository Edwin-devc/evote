<?php

namespace App\Filament\Widgets;

use App\Models\Candidate;
use App\Models\Position;
use App\Models\Voter;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ElectionOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $participation = Voter::query()->where('has_voted', 1)->count();
        $percentage_participation = (Voter::query()->where('has_voted', 1)->count())/(Voter::count())*100;
        return [
            Stat::make('Total Voters', Voter::count()),
            Stat::make('Participation', round($percentage_participation,2).'%')
            ->description($participation.' Voters')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('success'),
            Stat::make('Positions', Position::count()),
            Stat::make('Candidates', Candidate::count()),
        ];
    }
}
