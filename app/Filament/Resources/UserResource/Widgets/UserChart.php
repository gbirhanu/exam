<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class UserChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';


    protected function getData(): array
    {
        $data = Trend::model(User::class)
            ->between(
                start: now()->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'User Trends',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'fill' => "start",
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),


        ];
    }



    protected function getType(): string
    {
        return 'line';
    }
}
