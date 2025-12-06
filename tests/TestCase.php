<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Bypass permission middleware for all tests
        $this->withoutMiddleware(\App\Http\Middleware\CheckPermission::class);
    }
}
