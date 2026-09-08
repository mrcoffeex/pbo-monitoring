<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Noxo\FilamentActivityLog\Extensions\LogEditRecord;

class EditPayment extends EditRecord
{
    use LogEditRecord;

    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $project = $this->getRecord()->project;

        $data['current_payments'] = number_format($project?->paidPaymentTotal($this->getRecord()) ?? 0, 2, '.', '');
        $data['balance'] = number_format($project?->remainingPaymentBalance($this->getRecord()) ?? 0, 2, '.', '');

        return $data;
    }
}
