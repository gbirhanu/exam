<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Filament\Resources\CourseResource;
use App\Models\Department;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListCourses extends ListRecords
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        //add tabs for diffrent departments
        //user relation ship to department

        //get all departments 
        $departments = Department::all();
        $tabs = [
            'all' => Tab::make()->icon('heroicon-o-bars-4'),
        ];
        foreach ($departments as $department) {
            $tabs[$department->id] = Tab::make()->query(function ($query) use ($department) {
                $query->where('department_id', $department->id);
            })->label($department->name)->icon('heroicon-o-building-office-2');
        }
        return $tabs;
    }
}
