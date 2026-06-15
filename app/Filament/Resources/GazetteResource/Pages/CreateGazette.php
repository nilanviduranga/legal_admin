<?php

declare(strict_types=1);

namespace App\Filament\Resources\GazetteResource\Pages;

use App\Filament\Resources\GazetteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGazette extends CreateRecord
{
    protected static string $resource = GazetteResource::class;
}
