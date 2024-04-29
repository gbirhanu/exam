<?php

namespace App\Filament\Imports;

use App\Models\Question;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class QuestionImporter extends Importer
{
    protected static ?string $model = Question::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('exam')
                ->requiredMapping()
                ->relationship(resolveUsing: 'id')
                ->rules(['required', 'integer'])
                ->example(Question::first()?->exam_id),
            ImportColumn::make('question_text')
                ->requiredMapping()
                ->rules(['required'])
                ->example('What is the capital of Ethiopia?'),
            ImportColumn::make('option_1')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('Addis Ababa'),
            ImportColumn::make('option_2')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('Nairobi'),
            ImportColumn::make('option_3')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('Kampala'),
            ImportColumn::make('option_4')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('Dar es Salaam'),
            ImportColumn::make('correct_option')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('option_1'),
        ];
    }

    public function resolveRecord(): ?Question
    {
        // return Question::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Question();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your question import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
