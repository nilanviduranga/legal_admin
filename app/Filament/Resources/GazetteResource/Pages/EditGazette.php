<?php

declare(strict_types=1);

namespace App\Filament\Resources\GazetteResource\Pages;

use App\Filament\Resources\GazetteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGazette extends EditRecord
{
    protected static string $resource = GazetteResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
