<?php

namespace App\Filament\Admin\Resources\Tasks;

use App\Enum\TaskPriority;
use App\Enum\TaskStatus;
use App\Filament\Admin\Resources\Tasks\Pages\CreateTask;
use App\Filament\Admin\Resources\Tasks\Pages\EditTask;
use App\Filament\Admin\Resources\Tasks\Pages\ListTasks;
use App\Models\ServiceDepartment;
use App\Models\Task;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Operations';

    protected static ?string $label = 'Task';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(255)->columnSpan(2),
            Select::make('priority')
                ->options(collect(TaskPriority::cases())->mapWithKeys(fn ($c) => [$c->value => ucfirst($c->value)]))
                ->default(TaskPriority::Medium->value)
                ->required(),
            Select::make('status')
                ->options(collect(TaskStatus::cases())->mapWithKeys(fn ($c) => [$c->value => ucwords(str_replace('_', ' ', $c->value))]))
                ->default(TaskStatus::Todo->value)
                ->required(),
            Select::make('assigned_to')
                ->label('Assigned To')
                ->options(fn () => User::orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->nullable(),
            Select::make('department_id')
                ->label('Department')
                ->options(fn () => ServiceDepartment::orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->nullable(),
            DateTimePicker::make('due_date')->label('Due Date')->nullable(),
            Textarea::make('description')->columnSpanFull(),
            Textarea::make('completion_notes')->label('Completion Notes')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('due_date')
            ->columns([
                TextColumn::make('task_number')->label('Task#')->searchable()->sortable()->fontFamily('mono'),
                TextColumn::make('title')->searchable()->sortable()->limit(40),
                TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'urgent' => 'danger',
                        'high' => 'warning',
                        'medium' => 'info',
                        'low' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'completed' => 'success',
                        'in_progress' => 'info',
                        'blocked' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('assignedTo.name')->label('Assigned To')->searchable()->toggleable(),
                TextColumn::make('department.name')->label('Department')->toggleable(),
                TextColumn::make('due_date')->label('Due')->dateTime('d M Y H:i')->sortable(),
                TextColumn::make('completed_at')->label('Completed')->dateTime('d M Y')->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(TaskStatus::cases())->mapWithKeys(fn ($c) => [$c->value => ucwords(str_replace('_', ' ', $c->value))])),
                SelectFilter::make('priority')
                    ->options(collect(TaskPriority::cases())->mapWithKeys(fn ($c) => [$c->value => ucfirst($c->value)])),
                SelectFilter::make('assigned_to')
                    ->label('Assignee')
                    ->options(fn () => User::orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTasks::route('/'),
            'create' => CreateTask::route('/create'),
            'edit' => EditTask::route('/{record}/edit'),
        ];
    }
}
