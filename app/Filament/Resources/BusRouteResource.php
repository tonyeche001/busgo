<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusRouteResource\Pages;
use App\Filament\Resources\BusRouteResource\RelationManagers;
use App\Models\BusRoute;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BusRouteResource extends Resource
{
    protected static ?string $model = BusRoute::class;

    protected static ?string $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('origin')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('destination')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('distance_km')
                    ->label('Distance (km)')
                    ->numeric()
                    ->suffix('km')
                    ->default(null),
                Forms\Components\TextInput::make('base_fare')
                    ->label('Base Fare')
                    ->prefix('₦')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Active Route')
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('origin')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('destination')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('distance_km')
                    ->label('Distance')
                    ->formatStateUsing(fn ($state) => $state ? "{$state} km" : '—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('base_fare')
                    ->label('Base Fare')
                    ->formatStateUsing(fn ($state) => '₦' . number_format($state))
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBusRoutes::route('/'),
            'create' => Pages\CreateBusRoute::route('/create'),
            'edit' => Pages\EditBusRoute::route('/{record}/edit'),
        ];
    }
}
