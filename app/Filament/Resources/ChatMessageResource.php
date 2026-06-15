<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ChatMessageResource\Pages;
use App\Models\ChatMessage;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class ChatMessageResource extends Resource
{
    protected static ?string $model = ChatMessage::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static string|UnitEnum|null $navigationGroup = 'Chat Data';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('chat_session_id')
                    ->relationship('session', 'title')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->title ?: "Session #{$record->id}")
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('role')
                    ->options([
                        'user' => 'User',
                        'assistant' => 'Assistant',
                        'system' => 'System',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('content')
                    ->required()
                    ->rows(8)
                    ->columnSpanFull(),
                Forms\Components\TagsInput::make('cited_section_ids')
                    ->label('Cited Section IDs')
                    ->placeholder('Add section ID'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('session.title')
                    ->label('Session')
                    ->formatStateUsing(fn ($state, ChatMessage $record): string => $state ?: "Session #{$record->chat_session_id}")
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('role')->badge()->sortable(),
                Tables\Columns\TextColumn::make('content')->limit(50)->wrap()->searchable(),
                Tables\Columns\TextColumn::make('cited_section_ids')
                    ->label('Citations')
                    ->formatStateUsing(fn (?array $state): string => $state ? implode(', ', $state) : '-')
                    ->limit(25),
                Tables\Columns\TextColumn::make('created_at')->since()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'user' => 'User',
                        'assistant' => 'Assistant',
                        'system' => 'System',
                    ]),
                Tables\Filters\SelectFilter::make('chat_session_id')
                    ->relationship('session', 'title')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->title ?: "Session #{$record->id}")
                    ->searchable()
                    ->preload()
                    ->label('Session'),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChatMessages::route('/'),
            'create' => Pages\CreateChatMessage::route('/create'),
            'edit' => Pages\EditChatMessage::route('/{record}/edit'),
        ];
    }
}
