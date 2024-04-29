<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-user-plus'),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }


    public function getTabs(): array
    {
        return [
            'All' => Tab::make()->icon('heroicon-o-bars-4'),
            'Students' => Tab::make()->icon('heroicon-o-academic-cap')->query(function ($query) {
                $query->where('role', 'Student');
            })->badge(User::where('role', 'Student')->count()),

            'Coordinator' => Tab::make()->icon('heroicon-o-users')->query(function ($query) {
                $query->where('role', 'Coordinator');
            })->badge(
                User::where('role', 'Coordinator')->count()
            ),

            'Admin' => Tab::make()->icon('heroicon-o-user-circle')->query(function ($query) {
                $query->where('role', 'Admin');
            })->badge(
                User::where('role', 'Admin')->count()
            ),
        ];
    }
}
