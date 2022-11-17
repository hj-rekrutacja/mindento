<?php

namespace Tests\Unit;

use App\Delegation\DaysValidator;
use Carbon\Carbon;
use Tests\TestCase;

class DaysValidatorTest extends TestCase
{
    /**
     * @test
     */
    public function weekend_should_be_banned()
    {
        $validator = new DaysValidator();
        $saturday = Carbon::now()->setDate(2022, 11, 12);
        $this->assertTrue($validator->bannedDay($saturday));
        $sunday = Carbon::now()->setDate(2022, 11, 13);
        $this->assertTrue($validator->bannedDay($sunday));
    }

    /**
     * @test
     */
    public function weekdays_should_not_be_banned()
    {
        $validator = new DaysValidator();
        $monday = Carbon::now()->setDate(2022, 11, 14);
        $this->assertFalse($validator->bannedDay($monday));
    }

    /**
     * @test
     */
    public function check_consecutive_days()
    {
        $validator = new DaysValidator();
        $monday = Carbon::now()->setDate(2022, 11, 14);

        $this->assertFalse($validator->consecutiveDays($monday, $monday->clone()));
        $this->assertTrue($validator->consecutiveDays($monday, $monday->clone()->addDay()));
        $this->assertFalse($validator->consecutiveDays($monday, $monday->clone()->addDay()->addDay()));
    }

    /**
     * @test
     */
    public function check_single_day()
    {
        $validator = new DaysValidator();
        $monday = Carbon::now()->setDate(2022, 11, 14)->setTime(8, 0);
        $threshold = config('delegation.hours_threshold');

        $this->assertFalse($validator->validateSingleDay($monday, $monday->clone()->addHours($threshold)->subMinute()));
        $this->assertTrue($validator->validateSingleDay($monday, $monday->clone()->addHours($threshold)));
    }

    /**
     * @test
     */
    public function check_start_day()
    {
        $validator = new DaysValidator();
        $threshold = config('delegation.hours_threshold');
        $monday = Carbon::now()->setDate(2022, 11, 14)->setTime(24 - $threshold, 0);

        $this->assertTrue($validator->validateStart($monday));
        $this->assertFalse($validator->validateStart($monday->clone()->addMinute()));
    }

    /**
     * @test
     */
    public function check_end_day()
    {
        $validator = new DaysValidator();
        $threshold = config('delegation.hours_threshold');
        $monday = Carbon::now()->setDate(2022, 11, 14)->setTime($threshold, 0);

        $this->assertTrue($validator->validateEnd($monday));
        $this->assertFalse($validator->validateEnd($monday->clone()->subMinute()));
    }
}
