<?php

declare(strict_types=1);

namespace App\Filament\Resources\ChatMessageResource\Pages;

use App\Filament\Resources\ChatMessageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateChatMessage extends CreateRecord
{
    protected static string $resource = ChatMessageResource::class;
}
