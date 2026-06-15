<?php

declare(strict_types=1);

namespace App\Filament\Resources\ChatSessionResource\Pages;

use App\Filament\Resources\ChatSessionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateChatSession extends CreateRecord
{
    protected static string $resource = ChatSessionResource::class;
}
