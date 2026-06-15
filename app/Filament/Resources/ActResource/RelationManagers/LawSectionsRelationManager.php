<?php

declare(strict_types=1);

namespace App\Filament\Resources\ActResource\RelationManagers;

use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class LawSectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'legalNodes';

    protected static ?string $title = 'Legal Nodes';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('node_type')
                    ->options(array_combine(
                        ['PART','CHAPTER','SECTION','SUBSECTION','PARAGRAPH','SUBPARAGRAPH','ITEM','SCHEDULE','PREAMBLE','DEFINITIONS'],
                        ['PART','CHAPTER','SECTION','SUBSECTION','PARAGRAPH','SUBPARAGRAPH','ITEM','SCHEDULE','PREAMBLE','DEFINITIONS']
                    ))
                    ->required(),
                Forms\Components\TextInput::make('node_no')->maxLength(20),
                Forms\Components\TextInput::make('heading')->maxLength(500),
                Forms\Components\Textarea::make('text_content')->columnSpanFull()->rows(5),
                Forms\Components\TextInput::make('level')->numeric()->required()->default(1),
                Forms\Components\TextInput::make('sort_order')->numeric()->required()->default(0),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('node_type')->badge()->sortable(),
                Tables\Columns\TextColumn::make('node_no')->label('No')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('heading')->limit(40)->placeholder('-'),
                Tables\Columns\TextColumn::make('level')->sortable(),
                Tables\Columns\TextColumn::make('status')->badge(),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
            ]);
    }
}
