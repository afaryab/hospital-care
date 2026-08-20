<?php

use App\Models\Administrator;
use App\Models\User;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(function () {
        $this->withoutVite();
    })
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * A lightweight, unsaved Filament\Actions\Imports\Models\Import instance for
 * unit-testing an Importer's resolveRecord()/fillRecord()/save() pipeline
 * directly — new Importer($import, $columnMap, $options)($rowData) — without
 * simulating an actual file upload through the Livewire action.
 */
function makeFilamentImport(string $importerClass): Import
{
    return new Import([
        'importer' => $importerClass,
        'file_name' => 'test.csv',
        'file_path' => 'imports/test.csv',
        'total_rows' => 1,
    ]);
}

/**
 * A user with an Administrator profile — every Policy's before() hook
 * bypasses all object-level scoping for admins, so this is the right
 * "just let me hit this route" user for tests that exercise something
 * other than authorization itself (PDF rendering, view content, etc.).
 */
function adminUser(): User
{
    $user = User::factory()->create();
    Administrator::create(['user_id' => $user->id, 'authority' => 'administrator']);

    return $user;
}
