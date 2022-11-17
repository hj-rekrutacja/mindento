<?php

use App\Delegation\CalculationType;

return [
    'hours_threshold' => 8,
    "amount_rules" => [
        'PL' => [
            'base' => Cknow\Money\Money::PLN(1000),
            'currency' => 'PLN',
            'rules' => [
                7 => [
                    "calculation" => CalculationType::MULTIPLICATION,
                    "value" => 2
                ]
            ]
        ],
        'DE' => [
            'base' => Cknow\Money\Money::PLN(5000),
            'currency' => 'PLN',
            'rules' => [
                7 => [
                    "calculation" => CalculationType::MULTIPLICATION,
                    "value" => 2
                ]
            ]
        ],
        'GB' => [
            'base' => Cknow\Money\Money::PLN(7500),
            'currency' => 'PLN',
            'rules' => [
                7 => [
                    "calculation" => CalculationType::MULTIPLICATION,
                    "value" => 2
                ]
            ]
        ]
    ]
];
