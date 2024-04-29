<?php

namespace App\Providers\Filament;

use App\Models\Competency;
use App\Models\Exam;
use App\Models\Question;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('app')
            ->path('app')
            ->login()
            ->brandName("Gx Exam System")
            ->colors([
                'primary' => Color::Purple,
            ])
            ->font('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap')
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])

            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {


                return $builder
                    ->item(
                        NavigationItem::make('Dashboard')
                            ->icon('heroicon-o-squares-2x2')
                            ->isActiveWhen(function () {
                                return request()->is('app');
                            })
                            ->url('/app')
                    )->groups([

                        NavigationGroup::make('Competency')
                            ->icon('heroicon-o-cpu-chip')
                            ->items(

                                array_filter(
                                    Competency::where('department_id', auth()->user()->department_id)
                                        ->get()
                                        ->flatMap(function ($competency) {
                                            return [
                                                NavigationItem::make($competency->name)
                                                    ->isActiveWhen(function () use ($competency) {
                                                        return request()->is('app/competencies/' . $competency->id);
                                                    })
                                                    ->url('/app/competencies/' . $competency->id),
                                            ];
                                        })->toArray()
                                )
                            ),
                        NavigationGroup::make('Exams')
                            ->icon('heroicon-o-queue-list')
                            ->items(
                                array_filter(
                                    Exam::whereHas('course', function ($query) {
                                        $query->where('department_id', auth()->user()->department_id);
                                    })->get()
                                        ->flatMap(function ($exam) {
                                            return [
                                                NavigationItem::make($exam->name)
                                                    ->isActiveWhen(function () use ($exam) {
                                                        return request()->is('app/exams/' . $exam->id);
                                                    })
                                                    ->url('/app/exams/' . $exam->id),
                                            ];
                                        })->toArray()
                                )
                            ),


                    ]);
            })





            ->userMenuItems([
                MenuItem::make()
                    ->label('Settings')
                    ->icon('heroicon-o-cog-6-tooth'),
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\\Filament\\App\\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
