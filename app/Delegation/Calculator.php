<?php

namespace App\Delegation;

use Carbon\Carbon;
use Cknow\Money\Money;

class Calculator
{
    private string $currency;
    private DelegationRuleRepository $rules;
    private DelegationRule $currentRule;

    public function __construct(private readonly DaysValidator $daysValidator = new DaysValidator())
    {
    }

    public function calculate(Carbon $start, Carbon $end, string $country): Money
    {
        $this->hydrateRulesFromConfig($country);

        $this->currentRule = $this->rules->get(0);

        if ($start->isSameDay($end)) {
            return $this->calculateAmountForSingleDay($start, $end);
        }
        if ($this->daysValidator->consecutiveDays($start, $end)) {
            return $this->calculateTwoDays($start, $end);
        }

        return $this->calculateInterval($start, $end);
    }

    /**
     * @param  string  $country
     * @return void
     */
    public function hydrateRulesFromConfig(string $country): void
    {
        $delegationConfig = DelegationConfig::create($country);
        $this->currency = $delegationConfig->currency;
        $this->rules = $delegationConfig->ruleRepository;
    }

    private function calculateAmountForSingleDay(Carbon $start, Carbon $end): Money
    {
        if ($this->daysValidator->validateSingleDay($start, $end)) {
            return $this->currentRule->getAmount();
        }
        return $this->getEmptyMoney();
    }

    private function getEmptyMoney(): Money
    {
        return Money::parse(0, $this->currency);
    }

    private function calculateTwoDays(Carbon $start, Carbon $end): Money
    {
        $amount = $this->calculateStart($start);
        $this->currentRule = $this->rules->get(1) ?? $this->currentRule;
        return $amount->add($this->calculateEnd($end));
    }

    private function calculateStart(Carbon $start): Money
    {
        if ($this->daysValidator->validateStart($start)) {
            return $this->currentRule->getAmount();
        }
        return $this->getEmptyMoney();
    }

    private function calculateEnd(Carbon $end): Money
    {
        if ($this->daysValidator->validateEnd($end)) {
            return $this->currentRule->getAmount();
        }
        return $this->getEmptyMoney();
    }

    public function calculateInterval(Carbon $start, Carbon $end): Money
    {
        $amount = $this->calculateStart($start);
        $daysCounter = 1;
        $fullDay = $start->clone();
        while (!$fullDay->addDay()->isSameDay($end)) {
            $this->currentRule = $this->rules->get($daysCounter) ?? $this->currentRule;
            $amount = $amount->add($this->calculateFullDay($fullDay));
            $daysCounter++;
        }
        $this->currentRule = $this->rules->get($daysCounter) ?? $this->currentRule;
        return $amount->add($this->calculateEnd($end));
    }

    private function calculateFullDay(Carbon $date): Money
    {
        if (!$this->daysValidator->bannedDay($date)) {
            return $this->currentRule->getAmount();
        }
        return $this->getEmptyMoney();
    }
}
