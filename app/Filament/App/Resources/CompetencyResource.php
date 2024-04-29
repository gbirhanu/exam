<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\CompetencyResource\Pages;
use App\Filament\App\Resources\CompetencyResource\RelationManagers;
use App\Filament\Resources\ExamResource;
use App\Models\Competency;
use App\Models\Exam;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompetencyResource extends Resource
{
    protected static ?string $model = Competency::class;

    //filter $model so that it contain competency in user department



    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {


        return $infolist
            ->schema([
                Split::make([
                    Fieldset::make('Details')
                        ->columns(1)
                        ->schema([
                            TextEntry::make('name')->label('Name'),
                            TextEntry::make('description')->label('Description'),
                        ]),
                    Fieldset::make('Courses')
                        ->columns(2)
                        ->schema([
                            TextEntry::make('courseNames')->label('Courses')->badge(function () {
                                return Competency::where('id', request()->route('record'))->first()->courseNames;
                            }),
                        ]),
                ])->columnSpanFull(),
                Section::make('Exams')
                    ->columns(3)
                    ->description('Here are available exams in this competency.')
                    ->schema([
                        TextEntry::make('examNames')->label('Exams'),
                        TextEntry::make('name')->label('Competency'),
                        TextEntry::make('courseNames')->label('Course')->suffixAction(
                            Action::make('Open Exam')
                                ->label('Open Exam')
                                ->icon('heroicon-m-link')
                                ->url(function () {
                                    return '/app/exams/' . Competency::where('id', request()->route('record'))->first()->examIds;
                                })

                                ->requiresConfirmation()

                        ),

                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Competency::query()
                    ->withoutGlobalScope(SoftDeletingScope::class)
                    ->where('department_id', auth()->user()->department_id)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->sortable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompetencies::route('/'),
            'create' => Pages\CreateCompetency::route('/create'),
            'view' => Pages\ViewCompetency::route('/{record}'),
            'edit' => Pages\EditCompetency::route('/{record}/edit'),
        ];
    }
}