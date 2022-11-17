<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateWorkerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_worker_creation_on_post()
    {
        $response = $this->post('/api/workers');
        $response->assertStatus(200);
        $response->assertJsonStructure(['id']);
        $response->assertJsonCount(1);
    }
}
