<?php

namespace App\Filament\Resources\ExamResource\Pages;

use App\Filament\Resources\ExamResource;
use App\Models\Department;
use App\Models\Exam;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListExams extends ListRecords
{
    protected static string $resource = ExamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        $departments = Department::all();
        $tabs = [
            'all' => Tab::make()->icon('heroicon-o-bars-4')->badge(
                Exam::count()
            )->label('All'),
        ];
        //ok, i have other thing i have exam which blongs to course with course relation and course blongs to department i wanto to filete by deparment
        foreach ($departments as $department) {
            $tabs[$department->id] = Tab::make()->query(function ($query) use ($department) {
                $query->whereHas('course', function ($query) use ($department) {
                    $query->where('department_id', $department->id);
                });
            })->badge(
                Exam::whereHas('course', function ($query) use ($department) {
                    $query->where('department_id', $department->id);
                })->count()


            )->label($department->name)->icon('heroicon-o-building-office-2');
        }
        return $tabs;
    }
}
