<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\Department;
use App\Models\Exam;
use App\Models\Question;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    //write a funtion which return number of student registered this week

    public function numberOfRegisteredStudentThisWeek()
    {
        return User::where('role', 'Student')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
    }



    protected function getStats(): array
    {
        return [
            Stat::make('Student', User::query()->where(
                'role',
                'Student'
            )->count())
                ->icon('heroicon-o-users')
                ->color('success')
                ->description($this->numberOfRegisteredStudentThisWeek() . ' Weekly Increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Department', Department::query()->count())
                ->icon('heroicon-o-building-office')
                ->color('blue'),
            Stat::make('Exam', Exam::query()->count())
                ->icon('heroicon-o-academic-cap')
                ->color('blue'),
            Stat::make('Questions', Question::query()->count())
                ->icon('heroicon-o-question-mark-circle')
                ->color('blue'),

        ];
    }
}
