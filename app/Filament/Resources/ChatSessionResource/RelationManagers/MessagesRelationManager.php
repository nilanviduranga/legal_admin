<?php

declare(strict_types=1);

namespace App\Filament\Resources\ChatSessionResource\RelationManagers;

use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class MessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'messages';

    protected static ?string $title = 'Messages';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('role')
                    ->options([
                        'user' => 'User',
                        'assistant' => 'Assistant',
                        'system' => 'System',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('content')
                    ->required()
                    ->rows(6)
                    ->columnSpanFull(),
                Forms\Components\TagsInput::make('cited_section_ids')
                    ->label('Cited Section IDs')
                    ->placeholder('Add section ID')
                    ->helperText('Law section IDs referenced in this message.'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('role')->badge()->sortable(),
                Tables\Columns\TextColumn::make('content')->limit(60)->wrap(),
                Tables\Columns\TextColumn::make('cited_section_ids')
                    ->label('Citations')
                    ->formatStateUsing(fn (?array $state): string => $state ? implode(', ', $state) : '-')
                    ->limit(30),
                Tables\Columns\TextColumn::make('created_at')->since()->sortable(),
            ])
            ->defaultSort('created_at', 'asc')
            ->headerActions([
                Actions\CreateAction::make(),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
            ]);
    }
}
