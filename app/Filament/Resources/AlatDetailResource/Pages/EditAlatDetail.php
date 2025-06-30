<?php

namespace App\Filament\Resources\AlatDetailResource\Pages;

use App\Filament\Resources\AlatDetailResource;
use Filament\Resources\Pages\EditRecord;

class EditAlatDetail extends EditRecord
{
    protected static string $resource = AlatDetailResource::class;

    

    public function authorize($ability, $arguments = []): bool
{
    redirect($this->getResource()::getUrl('index'))->send();
    return false;
}

}
