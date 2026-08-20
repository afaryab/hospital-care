<?php

use App\Filament\Admin\Resources\Closings\Pages\EditClosing;
use App\Models\Administrator;
use App\Models\Closing;
use App\Models\User;
use Filament\Actions\DeleteAction;

use function Pest\Laravel\actingAs;

test('deleting a closing from the Filament admin panel soft-deletes it', function () {
    $admin = User::factory()->create();
    Administrator::create(['user_id' => $admin->id, 'authority' => 'administrator']);
    actingAs($admin);

    $closing = Closing::factory()->create();

    Livewire\Livewire::test(EditClosing::class, ['record' => $closing->id])
        ->callAction(DeleteAction::class);

    expect(Closing::find($closing->id))->toBeNull()
        ->and(Closing::withTrashed()->find($closing->id))->not->toBeNull();
});
