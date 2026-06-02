<?php

use App\Models\Closing;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

function printUrlFor(Closing $closing): string
{
    return route('print-closing-statement', [
        'year' => $closing->year,
        'month' => $closing->month,
        'number' => $closing->number,
    ]);
}

test('an open counter cannot be printed', function () {
    $user = User::factory()->create();
    $closing = Closing::factory()->create(['status' => 'OPEN']);

    actingAs($user);

    get(printUrlFor($closing))->assertForbidden();
});

test('a closed counter can be printed', function () {
    $user = User::factory()->create();
    $closing = Closing::factory()->closed()->create();

    actingAs($user);

    get(printUrlFor($closing))->assertOk();
});
