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
        'TRUST_FUND' => 'Trust Fund',
        'CAL_FUND' => 'Calamity Fund',
        'SFNW_FUND' => 'Share from National Wealth Fund',
        'SP_FUND' => 'Special Purpose Fund',
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
        'fifth_partial' => 'Fifth Partial Payment',
        'sixth_partial' => 'Sixth Partial Payment',
        'final_payment' => 'Final Payment',
    ];

    public const PROJECT_TYPES = [
        'infrastructure_project' => 'Infrastructure Project',
        'non_infrastructure_project' => 'Non-Infrastructure Project',
        'land_project' => 'Land Project',
        'Scholarship' => 'Scholarship',
        'Loans Payment' => 'Loans Payment',
        'Livelihood' => 'Livelihood',
        'Relocation Site' => 'Relocation Site',
        'Other' => 'Other',
    ];
}
