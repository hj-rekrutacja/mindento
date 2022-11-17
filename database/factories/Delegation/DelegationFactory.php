<?php

namespace Database\Factories\Delegation;

use App\Delegation\Delegation;
use App\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Delegation\Delegation>
 */
class DelegationFactory extends Factory
{
    protected $model = Delegation::class;

    public function definition()
    {
        $startDate = Carbon::createFromTimestamp($this->faker->dateTimeBetween('-1 year')->getTimestamp());
        return [
            'worker_id' => Worker::factory()->create(),
            'start' => $startDate->clone(),
            'end' => $startDate->addDays($this->faker->numberBetween(1, 20)),
            'country' => $this->faker->shuffleArray(['PL', 'DE', 'GB'])[0],
            'amount_due' => $this->faker->numberBetween(20, 2000),
            'currency' => 'PLN'
        ];
    }
}
