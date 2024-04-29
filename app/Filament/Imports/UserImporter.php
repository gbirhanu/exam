<?php

namespace App\Filament\Imports;

use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class UserImporter extends Importer
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('John Doe'),
            ImportColumn::make('email')
                ->requiredMapping()
                ->rules(['required', 'email', 'max:255'])
                ->example('john@example.com'),
            ImportColumn::make('department')
                ->requiredMapping()
                ->relationship(resolveUsing: 'id')
                ->rules(['integer'])
                ->example(User::first()?->department_id),
            ImportColumn::make('phone_number')
                ->rules(['max:255'])
                ->example('1234567890'),
            ImportColumn::make('address')
                ->rules(['max:255'])
                ->example('123 Main St'),
        ];
    }

    public function resolveRecord(): ?User
    {
        $user =  User::firstOrNew([
            // Update existing records, matching them by `$this->data['column_name']`
            'password' => 'password',
            'role' => 'Student',
            'payment_status' => 0,
        ]);

        $user->assignRole('Student');

        //add password to the user and role to Student 


        return $user;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your user import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
