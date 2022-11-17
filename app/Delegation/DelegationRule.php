<?php

namespace App\Delegation;

use Cknow\Money\Money;

class DelegationRule
{
    public function __construct(
        readonly CalculationType $calculationType,
        readonly int|float $calculationValue,
        readonly Money $base
    ) {
    }

    public function getAmount(): Money
    {
        return match ($this->calculationType) {
            CalculationType::ADDITION => $this->base->add(
                Money::parse($this->calculationValue, $this->base->getCurrency())
            ),
            CalculationType::MULTIPLICATION => $this->base->multiply($this->calculationValue),
        };
    }
}
