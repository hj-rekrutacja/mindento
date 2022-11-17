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
            ->where(function (Builder $query) use ($start, $end){
                $query->whereBetween('start', [$start, $end])
                    ->orWhereBetween('end', [$start, $end])
                    ->orWhere(function (Builder $query) use ($start, $end) {
                        $query->where('start', '<', $start)->where('end', '>', $end);
                    });
            })
            ->where('worker_id', '=', $this->data['worker_id'])
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
