<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Act;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\CrossReference;
use App\Models\Gazette;
use App\Models\LegalNode;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DatabaseStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Gazettes', Gazette::query()->count())
                ->description('Official gazette entries')
                ->icon('heroicon-o-newspaper')
                ->color('primary'),
            Stat::make('Acts', Act::query()->count())
                ->description(Act::query()->where('status', 'active')->count() . ' active')
                ->icon('heroicon-o-document-text')
                ->color('success'),
            Stat::make('Legal Nodes', LegalNode::query()->count())
                ->description(CrossReference::query()->count() . ' cross-references')
                ->icon('heroicon-o-book-open')
                ->color('info'),
            Stat::make('Chat Sessions', ChatSession::query()->count())
                ->description(ChatMessage::query()->count() . ' messages')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('warning'),
        ];
    }
}
