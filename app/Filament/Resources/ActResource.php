<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ActResource\Pages;
use App\Models\Act;
use App\Models\Gazette;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class ActResource extends Resource
{
    protected static ?string $model = Act::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|UnitEnum|null $navigationGroup = 'Legal Database';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('gazette_id')
                    ->label('Gazette')
                    ->options(Gazette::all()->pluck('gazette_no', 'gazette_id'))
                    ->required(),
                Forms\Components\TextInput::make('act_no')
                    ->label('Act No')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('act_year')
                    ->label('Year')
                    ->numeric()
                    ->length(4)
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(500),
                Forms\Components\TextInput::make('short_title')
                    ->maxLength(200),
                Forms\Components\DatePicker::make('commencement_date'),
                Forms\Components\Select::make('status')
                    ->options(['active' => 'Active', 'repealed' => 'Repealed', 'amended' => 'Amended'])
                    ->default('active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('act_no')->label('No')->sortable(),
                Tables\Columns\TextColumn::make('act_year')->label('Year')->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(50),
                Tables\Columns\TextColumn::make('gazette.gazette_no')->label('Gazette'),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn ($state) => match($state) {
                    'active' => 'success', 'repealed' => 'danger', default => 'warning',
                }),
                Tables\Columns\TextColumn::make('legalNodes_count')
                    ->label('Nodes')
                    ->counts('legalNodes')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->since()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['active' => 'Active', 'repealed' => 'Repealed', 'amended' => 'Amended']),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActs::route('/'),
            'create' => Pages\CreateAct::route('/create'),
            'edit' => Pages\EditAct::route('/{record}/edit'),
        ];
    }
}
