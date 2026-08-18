<?php

namespace App\Services\DataPortability;

use App\Filament\Exports\AssetCategoryExporter;
use App\Filament\Exports\AssetExporter;
use App\Filament\Exports\BankAccountExporter;
use App\Filament\Exports\DrugCategoryExporter;
use App\Filament\Exports\DrugExporter;
use App\Filament\Exports\ExpenseCategoryExporter;
use App\Filament\Exports\PanelChequeExporter;
use App\Filament\Exports\PanelExporter;
use App\Filament\Exports\PatientExporter;
use App\Filament\Exports\ServiceDepartmentExporter;
use App\Filament\Exports\ServiceExporter;
use App\Filament\Exports\ServiceRecestationExporter;
use App\Filament\Exports\StockCategoryExporter;
use App\Filament\Exports\StockItemExporter;
use App\Filament\Exports\UserExporter;
use App\Filament\Exports\WardExporter;
use App\Filament\Imports\AssetCategoryImporter;
use App\Filament\Imports\AssetImporter;
use App\Filament\Imports\BankAccountImporter;
use App\Filament\Imports\DrugCategoryImporter;
use App\Filament\Imports\DrugImporter;
use App\Filament\Imports\ExpenseCategoryImporter;
use App\Filament\Imports\PanelChequeImporter;
use App\Filament\Imports\PanelImporter;
use App\Filament\Imports\PatientImporter;
use App\Filament\Imports\ServiceDepartmentImporter;
use App\Filament\Imports\ServiceImporter;
use App\Filament\Imports\ServiceRecestationImporter;
use App\Filament\Imports\StockCategoryImporter;
use App\Filament\Imports\StockItemImporter;
use App\Filament\Imports\UserImporter;
use App\Filament\Imports\WardImporter;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\BankAccount;
use App\Models\Drug;
use App\Models\DrugCategory;
use App\Models\ExpenseCategory;
use App\Models\Panel;
use App\Models\PanelCheque;
use App\Models\Patient;
use App\Models\Service;
use App\Models\ServiceDepartment;
use App\Models\ServiceRecestation;
use App\Models\StockCategory;
use App\Models\StockItem;
use App\Models\User;
use App\Models\Ward;

/**
 * Central catalog of models available in the "Data Import / Export" admin
 * page — pick a record type here, get an importer (column mapping + upsert)
 * and an exporter for it. Adding a new type means adding one entry here
 * alongside its Importer/Exporter pair under app/Filament/Imports|Exports.
 *
 * Deliberately excludes Transaction and ServiceOrder: both are one side of
 * a required multi-table relationship (line items, closing/shift context,
 * patient+service+doctor linkage normally created together through the
 * counter checkout flow) that a naive column-mapped import can't safely
 * reconstruct — see the accompanying PR description for the full reasoning.
 */
class RecordTypeRegistry
{
    /**
     * @return array<string, array{label: string, model: class-string, importer: class-string, exporter: class-string}>
     */
    public static function all(): array
    {
        return [
            'expense_categories' => [
                'label' => 'Expense Categories',
                'model' => ExpenseCategory::class,
                'importer' => ExpenseCategoryImporter::class,
                'exporter' => ExpenseCategoryExporter::class,
            ],
            'asset_categories' => [
                'label' => 'Asset Categories',
                'model' => AssetCategory::class,
                'importer' => AssetCategoryImporter::class,
                'exporter' => AssetCategoryExporter::class,
            ],
            'assets' => [
                'label' => 'Assets',
                'model' => Asset::class,
                'importer' => AssetImporter::class,
                'exporter' => AssetExporter::class,
            ],
            'bank_accounts' => [
                'label' => 'Bank Accounts',
                'model' => BankAccount::class,
                'importer' => BankAccountImporter::class,
                'exporter' => BankAccountExporter::class,
            ],
            'services' => [
                'label' => 'Services',
                'model' => Service::class,
                'importer' => ServiceImporter::class,
                'exporter' => ServiceExporter::class,
            ],
            'panels' => [
                'label' => 'Panels',
                'model' => Panel::class,
                'importer' => PanelImporter::class,
                'exporter' => PanelExporter::class,
            ],
            'service_departments' => [
                'label' => 'Service Departments',
                'model' => ServiceDepartment::class,
                'importer' => ServiceDepartmentImporter::class,
                'exporter' => ServiceDepartmentExporter::class,
            ],
            'panel_cheques' => [
                'label' => 'Panel Cheques',
                'model' => PanelCheque::class,
                'importer' => PanelChequeImporter::class,
                'exporter' => PanelChequeExporter::class,
            ],
            'users' => [
                'label' => 'Users',
                'model' => User::class,
                'importer' => UserImporter::class,
                'exporter' => UserExporter::class,
            ],
            'patients' => [
                'label' => 'Patients',
                'model' => Patient::class,
                'importer' => PatientImporter::class,
                'exporter' => PatientExporter::class,
            ],
            'drugs' => [
                'label' => 'Drugs',
                'model' => Drug::class,
                'importer' => DrugImporter::class,
                'exporter' => DrugExporter::class,
            ],
            'service_recestations' => [
                'label' => 'Service Recestations',
                'model' => ServiceRecestation::class,
                'importer' => ServiceRecestationImporter::class,
                'exporter' => ServiceRecestationExporter::class,
            ],
            'wards' => [
                'label' => 'Wards',
                'model' => Ward::class,
                'importer' => WardImporter::class,
                'exporter' => WardExporter::class,
            ],
            'drug_categories' => [
                'label' => 'Drug Categories',
                'model' => DrugCategory::class,
                'importer' => DrugCategoryImporter::class,
                'exporter' => DrugCategoryExporter::class,
            ],
            'stock_categories' => [
                'label' => 'Stock Categories',
                'model' => StockCategory::class,
                'importer' => StockCategoryImporter::class,
                'exporter' => StockCategoryExporter::class,
            ],
            'stock_items' => [
                'label' => 'Stock Items',
                'model' => StockItem::class,
                'importer' => StockItemImporter::class,
                'exporter' => StockItemExporter::class,
            ],
        ];
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::all())->map(fn (array $entry) => $entry['label'])->all();
    }

    /** @return array{label: string, model: class-string, importer: class-string, exporter: class-string}|null */
    public static function find(string $key): ?array
    {
        return self::all()[$key] ?? null;
    }
}
