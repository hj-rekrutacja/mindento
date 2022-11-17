<?php

namespace Tests\Feature;

use App\Delegation\CalculationType;
use App\Worker;
use Carbon\Carbon;
use Cknow\Money\Money;
use Config;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateDelegationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function validation_works_on_empty_request()
    {
        $response = $this->post('/api/delegations', [

        ], ['accept' => 'application/json']);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function delegation_works_on_normal_request()
    {
        Config::set('delegation.amount_rules.ZZ', [
            'base' => Money::PLN(2200),
            'currency' => 'PLN',
            'rules' => [
                7 => [
                    "calculation" => CalculationType::MULTIPLICATION,
                    "value" => 2
                ]
            ]
        ]);
        $start = Carbon::now()->setDate(2022, 11, 14)->setTime(8, 0);
        $threshold = config('delegation.hours_threshold');
        $worker = Worker::factory()->create();
        $response = $this->post('/api/delegations', [
            'start' => $start->format('Y-m-d H:i:s'),
            'end' => $start->addHours($threshold)->format('Y-m-d H:i:s'),
            'worker_id' => $worker->id,
            'country' => 'ZZ'
        ], ['accept' => 'application/json']);
        $response->assertStatus(201);
        $worker->fresh(['delegations']);
        $this->assertCount(1, $worker->delegations);
        $this->assertEquals(2200, $worker->delegations->first()->amount_due->getAmount());
    }

    /**
     * @test
     */
    public function validate_end_after_start()
    {
        Config::set('delegation.amount_rules.ZZ', [
            'base' => Money::PLN(2200),
            'currency' => 'PLN',
            'rules' => [
                7 => [
                    "calculation" => CalculationType::MULTIPLICATION,
                    "value" => 2
                ]
            ]
        ]);
        $worker = Worker::factory()->create();
        $response = $this->post('/api/delegations', [
            'start' => Carbon::now()->format('Y-m-d H:i:s'),
            'end' => Carbon::now()->subHour()->format('Y-m-d H:i:s'),
            'worker_id' => $worker->id,
            'country' => 'ZZ'
        ], ['accept' => 'application/json']);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function validate_overlaping_dates()
    {
        Config::set('delegation.amount_rules.ZZ', [
            'base' => Money::PLN(2200),
            'currency' => 'PLN',
            'rules' => [
                7 => [
                    "calculation" => CalculationType::MULTIPLICATION,
                    "value" => 2
                ]
            ]
        ]);
        $start = Carbon::now()->setDate(2022, 11, 14)->setTime(10, 0);
        $end = $start->clone()->addDays(4);
        $worker = Worker::factory()->create();
        $response = $this->post('/api/delegations', [
            'start' => $start->format('Y-m-d H:i:s'),
            'end' => $end->format('Y-m-d H:i:s'),
            'worker_id' => $worker->id,
            'country' => 'ZZ'
        ], ['accept' => 'application/json']);
        $response->assertStatus(201);
        $worker->fresh(['delegations']);
        $this->assertCount(1, $worker->delegations);
        $this->assertEquals(2200 * 5, $worker->delegations->first()->amount_due->getAmount());

        $response = $this->post('/api/delegations', [
            'start' => $start->clone()->addDay()->format('Y-m-d H:i:s'),
            'end' => $end->clone()->subDay()->format('Y-m-d H:i:s'),
            'worker_id' => $worker->id,
            'country' => 'ZZ'
        ], ['accept' => 'application/json']);
        $response->assertStatus(422);

        $response = $this->post('/api/delegations', [
            'start' => $start->clone()->addDay()->format('Y-m-d H:i:s'),
            'end' => $end->clone()->addDay()->format('Y-m-d H:i:s'),
            'worker_id' => $worker->id,
            'country' => 'ZZ'
        ], ['accept' => 'application/json']);
        $response->assertStatus(422);

        $response = $this->post('/api/delegations', [
            'start' => $start->clone()->subDay()->format('Y-m-d H:i:s'),
            'end' => $end->clone()->subDay()->format('Y-m-d H:i:s'),
            'worker_id' => $worker->id,
            'country' => 'ZZ'
        ], ['accept' => 'application/json']);
        $response->assertStatus(422);

        $response = $this->post('/api/delegations', [
            'start' => $start->clone()->subDay()->format('Y-m-d H:i:s'),
            'end' => $end->clone()->addDay()->format('Y-m-d H:i:s'),
            'worker_id' => $worker->id,
            'country' => 'ZZ'
        ], ['accept' => 'application/json']);
        $response->assertStatus(422);
    }
}
