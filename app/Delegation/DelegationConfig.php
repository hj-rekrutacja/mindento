<?php

namespace App\Delegation;

use Cknow\Money\Money;

class DelegationConfig
{
    private function __construct(
        readonly Money $base,
        readonly string $currency,
        readonly DelegationRuleRepository $ruleRepository
    ) {
    }

    public static function create(string $country): self
    {
        $config = config('delegation.amount_rules.'.$country);
        return new self(
            $config['base'],
            $config['currency'],
            DelegationRuleRepository::create($config['rules'], $config['base'])
        );
    }
}
