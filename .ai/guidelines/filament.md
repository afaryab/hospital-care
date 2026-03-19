# Filament Conventions

Project-specific Filament v4 patterns extracted from the codebase. Follow these when creating or editing Filament resources.

---

## Resource Structure

- **Location:** `app/Filament/{PanelName}/Resources/{ResourceName}/`
- Two panels exist: **Admin** (`/admin`) and **Accounts** (`/accounts`)
- Separate schema classes in `Schemas/` subdirectory: `{Resource}Form.php`, `{Resource}Infolist.php`
- Separate table config in `Tables/` subdirectory: `{Resource}sTable.php` (pluralized)
- Page classes in `Pages/` subdirectory: `Create{Resource}`, `Edit{Resource}`, `List{Resources}`, `View{Resource}`

```
app/Filament/Admin/Resources/ClosingResource/
├── ClosingResource.php
├── Pages/
│   ├── CreateClosing.php
│   ├── EditClosing.php
│   ├── ListClosings.php
│   └── ViewClosing.php
├── Schemas/
│   ├── ClosingForm.php
│   └── ClosingInfolist.php
└── Tables/
    └── ClosingsTable.php
```

---

## Resource Class

- Extends `Resource`
- Use `Heroicon` enum for navigation icons (not string icon names)
- Delegate form/table/infolist to separate schema classes via static `configure()` method

```php
class ClosingResource extends Resource
{
    protected static ?string $model = Closing::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $recordTitleAttribute = 'ct_number';

    public static function form(Schema $schema): Schema
    {
        return ClosingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ClosingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClosingsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClosings::route('/'),
            'create' => CreateClosing::route('/create'),
            'view' => ViewClosing::route('/{record}'),
            'edit' => EditClosing::route('/{record}/edit'),
        ];
    }
}
```

---

## Form Schemas

- **Pattern:** Static method `configure(Schema $schema): Schema`
- Components: `TextInput`, `Select`, `DateTimePicker`, `Toggle`, etc.
- Use `->live()` for reactive fields, `fn ($get)` for conditional rendering
- Use `->relationship()` for model relationships

```php
class ClosingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('ct_number')->required(),
            TextInput::make('opening_amount')->required()->numeric()->default(0.0),
            DateTimePicker::make('closed_at'),
        ]);
    }
}
```

---

## Table Schemas

- **Pattern:** Static method `configure(Table $table): Table`
- Use `->formatStateUsing()` with `->html()` for custom rendering
- Use `->sortable()`, `->searchable()`, `->toggleable()` for interactions
- Use `->description()` for sub-text
- Filters: `SelectFilter::make()`, `Filter::make()` with closures
- Groups: `Group::make()` for grouping records, `->defaultGroup()`
- Enum options via `collect(Enum::cases())->mapWithKeys(fn ($s) => [$s->name => ucfirst(strtolower($s->name))])->toArray()`

```php
class ClosingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ct_number')
                    ->label('CT Information')
                    ->formatStateUsing(fn ($record) => "CT: {$record->ct_number}<br>Reception: {$record->reception?->name}")
                    ->html()
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(CounterStatus::cases())
                        ->mapWithKeys(fn (CounterStatus $s) => [$s->name => ucfirst(strtolower($s->name))])
                        ->toArray()),
            ])
            ->groups([
                Group::make('status')->label('Status'),
            ])
            ->defaultGroup('status');
    }
}
```

---

## Infolists (Read-only Views)

- **Pattern:** Static method `configure(Schema $schema): Schema`
- Use `Tabs::make()` with `Tab::make()` for tabbed layouts
- Use `ViewEntry::make()` with `->view()` and `->viewData()` for custom Blade views

```php
class ClosingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Tabs')->tabs([
                Tab::make('Summary')->schema([
                    ViewEntry::make('closing_overview')
                        ->label(false)
                        ->view('filament.closings.summary')
                        ->viewData(fn (Closing $record) => ['closing' => $record])
                        ->columnSpanFull(),
                ]),
            ])->columnSpanFull(),
        ]);
    }
}
```

---

## Page Classes

- Extend `CreateRecord`, `EditRecord`, `ListRecords`, `ViewRecord`
- Minimal code — inherit from parent, override only when needed

```php
class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
```

---

## Namespace Reference

| Component | Correct Namespace |
|---|---|
| Form fields (TextInput, Select, etc.) | `Filament\Forms\Components\` |
| Infolist entries (TextEntry, IconEntry, etc.) | `Filament\Infolists\Components\` |
| Layout (Grid, Section, Tabs, Wizard, etc.) | `Filament\Schemas\Components\` |
| Schema utilities (Get, Set) | `Filament\Schemas\Components\Utilities\` |
| Actions | `Filament\Actions\` |
| Icons | `Filament\Support\Icons\Heroicon` enum |

---

## Icons

- Use `Heroicon` enum: `Heroicon::OutlinedRectangleStack`, `Heroicon::PencilSquare`
- Set on resource via `protected static string|BackedEnum|null $navigationIcon`

---

## Key Reminders

- File visibility is `private` by default — use `->visibility('public')` for public access
- `Grid`, `Section`, and `Fieldset` no longer span all columns by default in v4
- Always authenticate before testing panel functionality
- Use `livewire()` or `Livewire::test()` for testing (Filament is built on Livewire)
