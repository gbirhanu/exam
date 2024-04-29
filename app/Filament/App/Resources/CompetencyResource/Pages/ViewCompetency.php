<?php

namespace App\Filament\App\Resources\CompetencyResource\Pages;

use App\Filament\App\Resources\CompetencyResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCompetency extends ViewRecord
{
    protected static string $resource = CompetencyResource::class;

    protected static ?string $title = 'Competency Details';

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
