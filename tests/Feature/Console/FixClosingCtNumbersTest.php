<?php

use App\Models\Closing;
use Symfony\Component\Console\Exception\InvalidOptionException;

test('fix-closing-ct-numbers no longer offers the old-HIMS sync phase', function () {
    $this->artisan('app:fix-closing-ct-numbers', ['--dry-run' => true, '--skip-sync' => true]);
})->throws(InvalidOptionException::class);

test('fix-closing-ct-numbers dry run previews renumbering without writing', function () {
    $first = Closing::factory()->create(['ct_number' => 'CT/2026/01/0007', 'created_at' => '2026-01-05 10:00:00']);
    $second = Closing::factory()->create(['ct_number' => 'CT/2026/01/0003', 'created_at' => '2026-01-06 10:00:00']);

    $this->artisan('app:fix-closing-ct-numbers', ['--dry-run' => true])
        ->expectsOutputToContain('Found 2 closings that need correction')
        ->expectsOutputToContain('[DRY RUN] No changes written.')
        ->doesntExpectOutputToContain('Syncing')
        ->assertExitCode(0);

    expect($first->fresh()->ct_number)->toBe('CT/2026/01/0007')
        ->and($second->fresh()->ct_number)->toBe('CT/2026/01/0003');
});
