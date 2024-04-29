<?php

namespace App\Filament\Resources;

use App\Filament\Exports\QuestionExporter;
use App\Filament\Imports\QuestionImporter;
use App\Filament\Resources\QuestionResource\Pages;
use App\Filament\Resources\QuestionResource\RelationManagers;
use App\Models\Question;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationGroup = 'Exams';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 10 ? 'warning' : 'success';
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make("Question Details")
                    ->description("Enter the details of the question.")
                    ->schema([
                        Forms\Components\Select::make('exam_id')
                            ->required()
                            ->relationship('exam', 'name')
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->prefixIcon('heroicon-o-queue-list'),
                        Forms\Components\Select::make('correct_option')
                            ->required()
                            ->options([
                                'option_1' => 'Option 1',
                                'option_2' => 'Option 2',
                                'option_3' => 'Option 3',
                                'option_4' => 'Option 4',
                            ])
                            ->native(false)
                            ->searchable()
                            ->prefixIcon('heroicon-o-check-circle'),
                        Forms\Components\Textarea::make('question_text')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),
                Forms\Components\Section::make("Options")
                    ->description("Enter the options for the question.")
                    ->schema([
                        Forms\Components\TextInput::make('option_1')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-list-bullet'),
                        Forms\Components\TextInput::make('option_2')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-list-bullet'),

                        Forms\Components\TextInput::make('option_3')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-list-bullet'),
                        Forms\Components\TextInput::make('option_4')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-list-bullet'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ExportAction::make()
                    ->exporter(QuestionExporter::class)
                    ->icon('heroicon-o-arrow-down-on-square-stack')
                    ->label('Export')
                    ->formats([
                        ExportFormat::Csv,
                        ExportFormat::Xlsx,
                    ]),
                ImportAction::make()
                    ->label('Import')
                    ->icon('heroicon-o-arrow-up-on-square-stack')
                    ->importer(QuestionImporter::class)

            ])
            ->columns([
                Tables\Columns\TextColumn::make('exam.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('option_1')
                    ->searchable(),
                Tables\Columns\TextColumn::make('option_2')
                    ->searchable(),
                Tables\Columns\TextColumn::make('option_3')
                    ->searchable(),
                Tables\Columns\TextColumn::make('option_4')
                    ->searchable(),
                Tables\Columns\TextColumn::make('correct_option')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'view' => Pages\ViewQuestion::route('/{record}'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}