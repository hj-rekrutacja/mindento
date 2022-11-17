<?php

namespace App\Delegation;

use Carbon\Carbon;

class DaysValidator
{
    private int $hoursThreshold;

    public function __construct()
    {
        $this->hoursThreshold = config('delegation.hours_threshold');
    }

    public function validateStart(Carbon $start): bool
    {
        return $this->validateSingleDay($start, $start->clone()->addDay()->startOfDay());
    }

    public function validateSingleDay(Carbon $start, Carbon $end): bool
    {
        if ($this->bannedDay($start)) {
            return false;
        }

        return $start->diffInHours($end) >= $this->hoursThreshold;
    }

    public function bannedDay(Carbon $day): bool
    {
        return $day->isWeekend();
    }

    public function validateEnd(Carbon $end): bool
    {
        return $this->validateSingleDay($end->clone()->startOfDay(), $end);
    }

    public function consecutiveDays(Carbon $start, Carbon $end): bool
    {
        return $start->clone()->addDay()->isSameDay($end);
    }
}
