<?php

namespace App\Filament\Loggers;

use App\Models\Procurement;
use App\Filament\Resources\ProcurementResource;
use Illuminate\Contracts\Support\Htmlable;
use Noxo\FilamentActivityLog\Loggers\Logger;
use Noxo\FilamentActivityLog\ResourceLogger\Field;
use Noxo\FilamentActivityLog\ResourceLogger\RelationManager;
use Noxo\FilamentActivityLog\ResourceLogger\ResourceLogger;

class ProcurementLogger extends Logger
{
    public static ?string $model = Procurement::class;

    public static function getLabel(): string | Htmlable | null
    {
        return ProcurementResource::getModelLabel();
    }

    public static function resource(ResourceLogger $logger): ResourceLogger
    {
        return $logger
            ->fields([
                Field::make('center.name')
                    ->label(__('Project')),

                Field::make('ib_number')
                    ->label(__('IB Number')),

                Field::make('pre_procurement_conference')
                    ->label(__('Pre Procurement Conference')),

                Field::make('pre_bid_conference')
                    ->label(__('Pre Bid Conference')),

                Field::make('bid_opening')
                    ->label(__('Bid Opening')),

                Field::make('ber')
                    ->label(__('Budget Evaluation Report (BER)')),

                Field::make('post_qua_date')
                    ->label(__('Post Qualification Date')),

                Field::make('remarks')
                    ->label(__('Remarks')),

                Field::make('noa_date_received')
                    ->label(__('NOA Date Received')),

                Field::make('contract_amount')
                    ->label(__('Contract Amount'))
                    ->money('PHP'),

                Field::make('contractor')
                    ->label(__('Contractor')),

                Field::make('ntp_number')
                    ->label(__('NTP Number')),

                Field::make('ntp_date')
                    ->label(__('NTP Date')),

                Field::make('contract_duration')
                    ->label(__('Contract Duration')),
            ])
            ->relationManagers([
                //
            ]);
    }
}
