<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\LawSectionResource\Pages;
use App\Models\Act;
use App\Models\LegalNode;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class LawSectionResource extends Resource
{
    protected static ?string $model = LegalNode::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static string|UnitEnum|null $navigationGroup = 'Legal Database';

    protected static ?string $navigationLabel = 'Legal Nodes';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('act_id')
                    ->label('Act')
                    ->options(Act::all()->pluck('title', 'act_id'))
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('node_type')
                    ->options(array_combine(
                        ['PART','CHAPTER','SECTION','SUBSECTION','PARAGRAPH','SUBPARAGRAPH','ITEM','SCHEDULE','PREAMBLE','DEFINITIONS'],
                        ['PART','CHAPTER','SECTION','SUBSECTION','PARAGRAPH','SUBPARAGRAPH','ITEM','SCHEDULE','PREAMBLE','DEFINITIONS']
                    ))
                    ->required(),
                Forms\Components\TextInput::make('node_no')->maxLength(20),
                Forms\Components\TextInput::make('heading')->maxLength(500),
                Forms\Components\Textarea::make('text_content')
                    ->rows(6)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('level')->numeric()->required(),
                Forms\Components\TextInput::make('sort_order')->numeric()->default(0)->required(),
                Forms\Components\Select::make('status')
                    ->options(['active' => 'Active', 'repealed' => 'Repealed', 'amended' => 'Amended'])
                    ->default('active'),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('node_id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('act.title')->label('Act')->searchable()->limit(30),
                Tables\Columns\TextColumn::make('node_type')->badge()->sortable(),
                Tables\Columns\TextColumn::make('node_no')->label('No')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('heading')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('level')->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn ($state) => match($state) {
                    'active' => 'success', 'repealed' => 'danger', default => 'warning',
                }),
                Tables\Columns\TextColumn::make('updated_at')->since()->sortable(),
            ])
            ->defaultSort('node_id')
            ->filters([
                Tables\Filters\SelectFilter::make('act_id')
                    ->label('Act')
                    ->options(Act::all()->pluck('title', 'act_id')),
                Tables\Filters\SelectFilter::make('node_type')
                    ->options(array_combine(
                        ['PART','SECTION','SUBSECTION','PARAGRAPH','SCHEDULE','PREAMBLE'],
                        ['PART','SECTION','SUBSECTION','PARAGRAPH','SCHEDULE','PREAMBLE']
                    )),
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
            'index' => Pages\ListLawSections::route('/'),
            'create' => Pages\CreateLawSection::route('/create'),
            'edit' => Pages\EditLawSection::route('/{record}/edit'),
        ];
    }
}
