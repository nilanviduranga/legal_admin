<?php

declare(strict_types=1);

namespace App\Filament\Resources\LawSectionResource\Pages;

use App\Filament\Resources\LawSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLawSections extends ListRecords
{
    protected static string $resource = LawSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
