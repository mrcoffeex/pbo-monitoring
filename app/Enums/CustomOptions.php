<?php

namespace App\Enums;

class CustomOptions
{
    public const FUNDS = [
        'GEN_FUND' => 'General Funds',
        'LGD_FUND' => 'Local Government Development Fund',
        'SE_FUND' => 'Special Education Fund',
        '20_FUND' => '20% Development Fund',
        'SH_FUND' => 'Special Health Fund',
    ];

    public const PROJECT_STATUS = [
        'released' => 'Released',
        'unreleased' => 'Unreleased',
        'canceled' => 'Canceled',
    ];

    public const PAYMENTS = [
        'mobilization' => 'Mobilization Payment',
        'first_partial' => 'First Partial Payment',
        'second_partial' => 'Second Partial Payment',
        'third_partial' => 'Third Partial Payment',
        'fourth_partial' => 'Fourth Partial Payment',
        'final_payment' => 'Final Payment',
    ];
}
