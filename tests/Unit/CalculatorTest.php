<?php

namespace Tests\Unit;

use App\Delegation\CalculationType;
use App\Delegation\Calculator;
use Carbon\Carbon;
use Cknow\Money\Money;
use Config;
use Tests\TestCase;

class CalculatorTest extends TestCase
{
    /**
     * @test
     */
    public function checkSingleDayCalculations()
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
        $threshold = config('delegation.hours_threshold');
        $calculator = new Calculator();
        $start = Carbon::now()->setDate(2022, 11, 14)->setTime(8, 0);

        $value = $calculator->calculate($start, $start->clone()->addHours($threshold), 'ZZ');
        $this->assertEquals(2200, $value->getAmount());

        $value = $calculator->calculate($start, $start->clone()->addHours($threshold)->subMinute(), 'ZZ');
        $this->assertEquals(0, $value->getAmount());

        $value = $calculator->calculate($start->nextWeekendDay(), $start->clone()->addHours($threshold), 'ZZ');
        $this->assertEquals(0, $value->getAmount());
    }

    /**
     * @test
     */
    public function checkTwoDayCalculations()
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
        $calculator = new Calculator();
        $start = Carbon::now()->setDate(2022, 11, 14)->setTime(10, 0);

        $value = $calculator->calculate($start, $start->clone()->addDay(), 'ZZ');
        $this->assertEquals(4400, $value->getAmount());

        $value = $calculator->calculate($start, $start->clone()->addDay()->setTime(1, 0), 'ZZ');
        $this->assertEquals(2200, $value->getAmount());

        $value = $calculator->calculate($start->clone()->setTime(23, 0), $start->clone()->addDay(), 'ZZ');
        $this->assertEquals(2200, $value->getAmount());

        $value = $calculator->calculate(
            $start->clone()->setTime(23, 0),
            $start->clone()->addDay()->setTime(1, 0),
            'ZZ'
        );
        $this->assertEquals(0, $value->getAmount());

        $value = $calculator->calculate($start->nextWeekendDay(), $start->clone()->addDay(), 'ZZ');
        $this->assertEquals(0, $value->getAmount());

        $value = $calculator->calculate($start->addDay(), $start->clone()->addDay(), 'ZZ');
        $this->assertEquals(2200, $value->getAmount());
    }

    /**
     * @test
     */
    public function checkLongIntervalCalculations()
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
        $calculator = new Calculator();
        $start = Carbon::now()->setDate(2022, 11, 14)->setTime(8, 0);

        $value = $calculator->calculate($start, $start->clone()->addDays(6), 'ZZ');
        $this->assertEquals(2200 * 5, $value->getAmount());

        $value = $calculator->calculate($start, $start->clone()->addWeek(), 'ZZ');
        $this->assertEquals(2200 * 5 + 2200 * 2, $value->getAmount());
    }
}
