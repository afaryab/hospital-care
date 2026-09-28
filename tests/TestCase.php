<?php

namespace Tests;

use App\Services\OnlyOffice\OnlyOfficeHealth;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Cache;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // No OnlyOffice Document Server runs under test; treat it as up so the
        // Documents section is reachable. OnlyOfficeAvailabilityTest clears
        // this to exercise the real health check.
        Cache::put(OnlyOfficeHealth::CACHE_KEY, true, 3600);
    }
}
