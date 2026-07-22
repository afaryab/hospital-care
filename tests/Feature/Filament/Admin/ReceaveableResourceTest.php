<?php

use App\Filament\Admin\Resources\Receaveables\Pages\ListReceaveables;
use App\Models\Administrator;
use App\Models\Receaveable;
use App\Models\User;
use Livewire\Livewire;

function actingAsReceivablesAdmin(): User
{
    $user = User::factory()->create();
    Administrator::create(['user_id' => $user->id, 'authority' => 'full']);
    test()->actingAs($user);

    return $user;
}

test('receivables admin list page requires authentication', function () {
    $this->get('/admin/receaveables')->assertRedirect();
});

test('admin user can access receivables list page', function () {
    actingAsReceivablesAdmin();

    $this->get('/admin/receaveables')->assertSuccessful();
});

test('receivables status filter surfaces unpaid records (not the legacy pending vocabulary)', function () {
    actingAsReceivablesAdmin();

    $unpaid = Receaveable::factory()->create(['status' => 'unpaid']);
    $paid = Receaveable::factory()->create(['status' => 'paid']);

    Livewire::test(ListReceaveables::class)
        ->assertCanSeeTableRecords([$unpaid, $paid])
        ->filterTable('status', 'unpaid')
        ->assertCanSeeTableRecords([$unpaid])
        ->assertCanNotSeeTableRecords([$paid]);
});

test('receivables status filter for paid excludes unpaid records', function () {
    actingAsReceivablesAdmin();

    $unpaid = Receaveable::factory()->create(['status' => 'unpaid']);
    $paid = Receaveable::factory()->create(['status' => 'paid']);

    Livewire::test(ListReceaveables::class)
        ->filterTable('status', 'paid')
        ->assertCanSeeTableRecords([$paid])
        ->assertCanNotSeeTableRecords([$unpaid]);
});
