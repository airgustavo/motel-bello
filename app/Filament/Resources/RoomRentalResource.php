<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomRentalResource\Pages;
use App\Filament\Resources\RoomRentalResource\RelationManagers;
use App\Filament\Exports\RoomRentalExporter; // Importar tu Exporter personalizado
use App\Models\RoomRental;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Grid; // Importar Grid
use Filament\Forms\Components\DatePicker; // Importar DatePicker
use Filament\Forms\Components\Select; // Importar Select
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter; // Importar SelectFilter
use Filament\Tables\Filters\Filter; // Importar Filter
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction; // Asegúrate que esta es la acción que usas
use pxlrbt\FilamentExcel\Exports\ExcelExport; // Necesario para usar withExporter

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
                Select::make('room_id')
                    ->label('Habitación')
                    ->relationship('room', 'name') // Asumiendo que 'name' es el campo que quieres mostrar
                    ->required()
                    ->searchable()
                    ->preload() // Carga los datos al abrir el formulario
                    ->placeholder('Selecciona una habitación'),
                Select::make('rent_id')
                    ->label('Tipo de Alquiler')
                    ->relationship('rent', 'name') // Asumiendo que 'name' es el campo que quieres mostrar
                    ->searchable()
                    ->required()
                    ->preload() // Carga los datos al abrir el formulario
                    ->placeholder('Selecciona un tipo de alquiler'),
                DateTimePicker::make('start_time')
                    ->label('Hora de Entrada')
                    ->required(),
                DateTimePicker::make('end_time')
                    ->label('Hora de Salida')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc') // Añade esta línea
            ->columns([
                TextColumn::make('room.name') // Cambiado de 'room_id' a 'room.name'
                    ->label('Habitación')
                    ->searchable()
                    // ->numeric() // Ya no es numérico, es el nombre
                    ->sortable(),
                TextColumn::make('rent.name') // Asumo que también querrás el nombre del tipo de alquiler
                    ->label('Tipo de Alquiler')
                    ->searchable()
                    // ->numeric() // Ya no es numérico, es el nombre
                    ->sortable(),
                TextColumn::make('rent.cost')
                    ->label('Costo de la Habitación')
                    ->sortable(),
                TextColumn::make('start_time')
                    ->label('Hora de Entrada')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('end_time')
                    ->label('Hora de Salida')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('start_time')                    
                    ->form([
                         Grid::make(2)->schema([
                            DatePicker::make('start_from')->label('Desde la Fecha'),
                            DatePicker::make('start_until')->label('Hasta la Fecha'),
                        ]),
                    ])                    
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['start_from'] ?? null, fn (Builder $query, $date): Builder => $query->whereDate('start_time', '>=', $date))
                            ->when($data['start_until'] ?? null, fn (Builder $query, $date): Builder => $query->whereDate('start_time', '<=', $date));
                    }),
                    SelectFilter::make('room')
                        ->placeholder('Selecciona una habitación')
                        ->label('Habitación')
                        ->relationship('room', 'name'),
                    SelectFilter::make('rent')
                        ->placeholder('Selecciona un tipo de alquiler')
                        ->label('Tipo de Alquiler')
                        ->relationship('rent', 'name'),
                
                ],layout: FiltersLayout::AboveContent) // Cambiado a FiltersLayout::AboveContent para que los filtros aparezcan encima del contenido de la tabla
            ->filtersFormColumns(3) // Ajusta el número de columnas del formulario de filtros
            ->actions([
                Tables\Actions\EditAction::make()                
                ->label('Editar'),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
                ExportBulkAction::make('export_selected')
                    ->label('Exportar Seleccionados')
                    ->exports([
                        RoomRentalExporter::make('rentas_export') // Usamos tu clase exportadora directamente
                            ->withFilename(fn () => 'rentas-habitaciones-' . date('Y-m-d'))
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
