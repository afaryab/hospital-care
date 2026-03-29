<?php

use App\Filament\Admin\Resources\ExpenseCategories\Pages\ManageExpenseCategories;
use App\Models\Administrator;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('expense category manage page renders', function () {
    $user = User::factory()->create();
    Administrator::create(['user_id' => $user->id, 'authority' => 'administrator']);
    actingAs($user);

    Livewire\Livewire::test(ManageExpenseCategories::class)->assertSuccessful();
});
