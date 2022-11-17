<?php

namespace App\Delegation;

use Cknow\Money\Money;
use Illuminate\Support\Collection;

class DelegationRuleRepository extends Collection
{
    private function __construct(Collection $rules)
    {
        parent::__construct($rules);
    }

    public static function create(array $rulesArray, Money $base): self
    {
        $rules = collect($rulesArray)->map(function (array $rule) use ($base) {
            return new DelegationRule($rule['calculation'], $rule['value'], $base);
        });
        $rules->prepend(new DelegationRule(CalculationType::MULTIPLICATION, 1, $base), 0);
        return new self($rules);
    }
}
