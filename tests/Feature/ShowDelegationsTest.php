<?php

namespace Tests\Feature;

use App\Delegation\Delegation;
use App\Worker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowDelegationsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function test_delegation_list()
    {
        $worker = Worker::factory()->create();
        Delegation::factory(5)->create([
            'worker_id' => $worker
        ]);

        $response = $this->get("/api/workers/$worker->id/delegations");

        $response->assertStatus(200);
        $response->assertJsonCount(5);
        $response->assertJsonStructure(['*' => ['start', 'end', 'country', 'amount_due', 'currency']]);
    }
}
