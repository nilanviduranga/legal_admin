<?php

declare(strict_types=1);

namespace App\Filament\Resources\GazetteResource\Pages;

use App\Filament\Resources\GazetteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGazettes extends ListRecords
{
    protected static string $resource = GazetteResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
