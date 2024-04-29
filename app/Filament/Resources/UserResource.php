<?php

namespace App\Filament\Resources;

use App\Filament\Exports\UserExporter;
use App\Filament\Imports\UserImporter;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Contracts\Support\Htmlable;
use App\Models\User;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\GlobalSearch\Actions\Action;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'User Information';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getGlobalSearchResultActions(Model $record): array
    {
        return [
            Action::make('edit')
                ->url(static::getUrl('edit', ['record' => $record])),
        ];
    }
    protected static int $globalSearchResultsLimit = 20;


    public static function getGlobalSearchResultTitle(Model $record): string | Htmlable
    {
        return $record->name;
    }
    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 10 ? 'warning' : 'success';
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->description('This information will be visible to other users.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter your full name')
                            ->prefixIcon('heroicon-o-user'),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-envelope'),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-lock-closed'),
                        Forms\Components\Select::make('role')
                            ->options(Role::pluck('name', 'name')->toArray())
                            ->native(false)
                            ->searchable()
                            ->required()
                            ->prefixIcon('heroicon-o-shield-check'),
                        Forms\Components\Toggle::make('payment_status')
                            ->required()
                    ])->columns(2),
                Forms\Components\Section::make('Additional Information')
                    ->description('This information will be visible to other users.')
                    ->schema([
                        Forms\Components\TextInput::make('phone_number')
                            ->tel()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-phone'),
                        Forms\Components\TextInput::make('address')
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-map-pin'),
                        Forms\Components\Select::make('department_id')
                            ->relationship("department", "name")
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->prefixIcon('heroicon-o-building-office-2'),

                    ])->columns(3),

                Forms\Components\Section::make('profile_picture')
                    ->description('Update your profile picture.')
                    ->schema([

                        Forms\Components\FileUpload::make('profile_picture')

                    ])->columnSpanFull(),
            ]);
    }



    public function beforeSave(Model $record, Form $form)
    {
        if ($form->isCreating()) {
            $record->password = bcrypt($record->password);
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ExportAction::make()
                    ->exporter(UserExporter::class)
                    ->icon('heroicon-o-arrow-down-on-square-stack')
                    ->label('Export')
                    ->formats(
                        [
                            ExportFormat::Csv,
                            ExportFormat::Xlsx,
                        ]
                    ),
                ImportAction::make()
                    ->icon('heroicon-o-arrow-up-on-square-stack')
                    ->label('Import')
                    ->importer(UserImporter::class),
            ])

            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->color(fn ($record) => match ($record->role) {
                        'Admin' => 'primary',
                        'Student' => 'success',
                        'Coordinator' => 'warning',
                    }),
                Tables\Columns\IconColumn::make('payment_status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('department.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
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

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->native(false)
                            ->label('From'),
                        DatePicker::make('created_until')
                            ->native(false)
                            ->label('To'),

                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })->columnSpan(2)->columns(2)
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
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

    //on submit assign user to role




    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    //register widget 
    public static function getWidgets(): array
    {
        return [
            //
        ];
    }
}
