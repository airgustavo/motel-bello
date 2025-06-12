<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomRentalResource\Pages;
use App\Filament\Resources\RoomRentalResource\RelationManagers;
use App\Models\RoomRental;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoomRentalResource extends Resource
{
    protected static ?string $model = RoomRental::class;
    protected static ?string $navigationGroup = 'Reportes'; // Agrupa este recurso bajo "Reportes"
    protected static ?int $navigationSort = 3; // Ajusta el orden de navegación según sea necesario
    protected static ?string $title = 'Rentas de Habitaciones';
    protected static ?string $navigationLabel = 'Rentas de Habitaciones';

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('room_id')
                    ->label('Habitación')
                    ->relationship('room', 'name') // Asumiendo que 'name' es el campo que quieres mostrar
                    ->required()
                    ->searchable()
                    ->preload() // Carga los datos al abrir el formulario
                    ->placeholder('Selecciona una habitación'),
                Forms\Components\Select::make('rent_id')
                    ->label('Tipo de Alquiler')
                    ->relationship('rent', 'name') // Asumiendo que 'name' es el campo que quieres mostrar
                    ->searchable()
                    ->required()
                    ->preload() // Carga los datos al abrir el formulario
                    ->placeholder('Selecciona un tipo de alquiler'),
                Forms\Components\DateTimePicker::make('start_time')
                    ->label('Hora de Entrada')
                    ->required(),
                Forms\Components\DateTimePicker::make('end_time')
                    ->label('Hora de Salida')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('room.name') // Cambiado de 'room_id' a 'room.name'
                    ->label('Habitación')
                    ->searchable()
                    // ->numeric() // Ya no es numérico, es el nombre
                    ->sortable(),
                Tables\Columns\TextColumn::make('rent.name') // Asumo que también querrás el nombre del tipo de alquiler
                    ->label('Tipo de Alquiler')
                    ->searchable()
                    // ->numeric() // Ya no es numérico, es el nombre
                    ->sortable(),
                tables\Columns\TextColumn::make('rent.cost')
                    ->label('Costo de la Habitación')
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Hora de Entrada')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('Hora de Salida')
                    ->dateTime()
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
            'index' => Pages\ListRoomRentals::route('/'),
            'create' => Pages\CreateRoomRental::route('/create'),
            'edit' => Pages\EditRoomRental::route('/{record}/edit'),
        ];
    }
    public static function getModelLabel(): string
    {
        return 'Renta de Habitación';
    }
    public static function getPluralModelLabel(): string
    {
        return 'Rentas de Habitaciones';
    }
}
