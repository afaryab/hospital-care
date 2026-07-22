<?php

use App\Models\Closing;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('my counter list shows the latest counter at the top', function () {
    $user = User::factory()->create();

    $first = Closing::factory()->create(['receptionist_id' => $user->id]);
    $second = Closing::factory()->create(['receptionist_id' => $user->id]);
    $latest = Closing::factory()->create(['receptionist_id' => $user->id]);

    actingAs($user);

    get(route('my-counter-list'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('counter/list')
            ->where('closings.data.0.id', $latest->id)
            ->where('closings.data.2.id', $first->id)
        );
});
