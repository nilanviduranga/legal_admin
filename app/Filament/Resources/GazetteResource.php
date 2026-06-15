<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\GazetteResource\Pages;
use App\Models\Gazette;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class GazetteResource extends Resource
{
    protected static ?string $model = Gazette::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-newspaper';

    protected static string|UnitEnum|null $navigationGroup = 'Legal Database';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('gazette_no')->required()->maxLength(50),
                Forms\Components\DatePicker::make('gazette_date')->required(),
                Forms\Components\TextInput::make('title')->required()->maxLength(500)->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive'])
                    ->default('active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('gazette_no')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('gazette_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(50),
                Tables\Columns\TextColumn::make('acts_count')->counts('acts')->label('Acts')->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn ($state) => $state === 'active' ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('updated_at')->since()->sortable(),
            ])
            ->defaultSort('gazette_date', 'desc')
            ->actions([Actions\EditAction::make(), Actions\DeleteAction::make()])
            ->bulkActions([Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGazettes::route('/'),
            'create' => Pages\CreateGazette::route('/create'),
            'edit' => Pages\EditGazette::route('/{record}/edit'),
        ];
    }
}
