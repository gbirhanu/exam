<?php

namespace App\Filament\Resources\QuestionResource\Pages;

use App\Filament\Resources\QuestionResource;
use App\Models\Department;
use App\Models\Question;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListQuestions extends ListRecords
{
    protected static string $resource = QuestionResource::class;

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
            'all' => Tab::make()->icon('heroicon-o-bars-4')->badge(function ($query) {
                return $query->count();
            })->badge(
                Question::count()
            )->label('All'),

        ];

        //ok, i have other thing i have question which blongs to exam and exam which blongs to course with course relation and course blongs to department i wanto to filete by deparment
        foreach ($departments as $department) {
            $tabs[$department->id] = Tab::make()->query(function ($query) use ($department) {
                $query->whereHas('exam', function ($query) use ($department) {
                    $query->whereHas('course', function ($query) use ($department) {
                        $query->where('department_id', $department->id);
                    });
                });
            })->badge(
                Question::whereHas('exam', function ($query) use ($department) {
                    $query->whereHas('course', function ($query) use ($department) {
                        $query->where('department_id', $department->id);
                    });
                })->count()

            )->label($department->name)->icon('heroicon-o-building-office-2');
        }

        return $tabs;
    }
}
