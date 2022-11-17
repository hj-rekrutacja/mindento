<?php

namespace Database\Seeders;

use App\Delegation\Delegation;
use App\Worker;
use Carbon\Carbon;
use Faker\Generator;
use Illuminate\Database\Seeder;

class DelegationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = new Generator();
        Worker::query()->cursor()->each(function (Worker $worker) use ($faker) {
            $date = Carbon::now()->subYear();
            while ($date->isBefore(Carbon::now())) {
                Delegation::factory()->create([
                    'worker_id' => $worker,
                    'start' => $date->addDays($faker->numberBetween(0, 30))->clone(),
                    'end' => $date->addDays($faker->numberBetween(1, 20)),
                ]);
            }
        });
    }
}
