<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ChatSessionResource\Pages;
use App\Filament\Resources\ChatSessionResource\RelationManagers;
use App\Models\ChatSession;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class ChatSessionResource extends Resource
{
    protected static ?string $model = ChatSession::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string|UnitEnum|null $navigationGroup = 'Chat Data';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Forms\Components\TextInput::make('title')
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('last_message_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->placeholder('Untitled')
                    ->limit(40),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->placeholder('Guest'),
                Tables\Columns\TextColumn::make('messages_count')
                    ->label('Messages')
                    ->counts('messages')
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_message_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('last_message_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->label('User'),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MessagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChatSessions::route('/'),
            'create' => Pages\CreateChatSession::route('/create'),
            'edit' => Pages\EditChatSession::route('/{record}/edit'),
        ];
    }
}
