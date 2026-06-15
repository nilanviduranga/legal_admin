<?php

declare(strict_types=1);

namespace App\Filament\Resources\ActResource\Pages;

use App\Filament\Resources\ActResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAct extends CreateRecord
{
    protected static string $resource = ActResource::class;
}
