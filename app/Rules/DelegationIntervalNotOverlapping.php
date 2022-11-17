<?php

namespace App\Rules;

use App\Delegation\Delegation;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Database\Eloquent\Builder;

class DelegationIntervalNotOverlapping implements InvokableRule, DataAwareRule
{
    private array $data;

    public function __invoke($attribute, $value, $fail)
    {
        $start = Carbon::parse($this->data['start']);
        $end = Carbon::parse($this->data['end']);
        $count = Delegation::query()
            ->whereBetween('start', [$start, $end])
            ->orWhereBetween('end', [$start, $end])
            ->orWhere(function (Builder $query) use ($start, $end) {
                $query->whereDate('start', '<', $start)->whereDate('end', '>', $end);
            })
            ->count();
        if ($count > 0) {
            $fail('validation.delegation_interval')->translate();
        }
    }

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}
