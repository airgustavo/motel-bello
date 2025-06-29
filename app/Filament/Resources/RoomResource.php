<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomResource\Pages;
use App\Filament\Resources\RoomResource\RelationManagers;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;
    protected static ?string $navigationGroup = 'Catalogos Generales'; // Agrupa este recurso bajo "Catalogos Generales"
    protected static ?int $navigationSort = 1;
    protected static ?string $title = 'Habitaciones';
    protected static ?string $navigationLabel = 'Habitaciones';
    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre de la habitación')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->label('Estado de la habitación')
                    ->options([
                        'disponible' => 'Disponible',
                        'ocupada' => 'Ocupada',
                        'fuera_de_servicio' => 'Fuera de Servicio',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Habitación')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')               
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state ?? '')) {
                        'disponible' => 'success',
                        'ocupada' => 'danger',
                        'fuera_de_servicio' => 'warning',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
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
                Tables\Actions\EditAction::make()
                ->label('Editar'),
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
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
    }
}
