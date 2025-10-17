<?

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Mail\UserRegistrationMail;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;
use Noxo\FilamentActivityLog\Extensions\LogCreateRecord;

class CreateUser extends CreateRecord
{
    use LogCreateRecord;

    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        // Send registration notification email
        // Mail::to($this->record->email)->send(new UserRegistrationMail($this->record));
    }
}
