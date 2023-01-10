<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AutoTestingTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_failing_test_now_passes()
    {
        $this->assertTrue(true);
    }
}
