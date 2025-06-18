<?php

namespace App\Filament\Exports;

use App\Models\RoomRental;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use pxlrbt\FilamentExcel\Exports\ExcelExport; // Importar la clase base
use Illuminate\Database\Eloquent\Builder; // Importar la clase Builder correcta


class RoomRentalExporter extends ExcelExport implements FromQuery, WithHeadings, WithMapping, WithEvents
{
    // El constructor del padre (ExcelExport) toma un nombre, podemos definir uno por defecto.
    // La query filtrada será inyectada por la acción de exportación de Filament Excel.
    public static function make(string $name = 'room_rental_export'): static
    {
         $static = app(static::class, ['name' => $name]);
        $static->setUp();

        // Puedes añadir configuraciones por defecto aquí si lo deseas,
        // por ejemplo, un nombre de archivo por defecto.
        // $static->withFilename('exportacion-rentas-' . date('Y-m-d'));

        return $static;
    }

    /**
     * Define la consulta base para la exportación.
     * Aseguramos cargar las relaciones para usarlas en el mapeo.
     */
    public function query(): Builder
    {
        // parent::getQuery() devolverá la consulta que ExportBulkAction ha preparado
        // (filtrada por la tabla, registros seleccionados, etc.)
        return parent::getQuery()->with(['room', 'rent']);
    }

    /**
     * Define las cabeceras de las columnas en el archivo Excel.
     */
    public function headings(): array
    {
        return [
            'Habitación',
            'Tipo de Alquiler',
            'Costo de la Habitación',
            'Hora de Entrada', // Asegúrate que el nombre de la columna coincida
            'Hora de Salida',  // Asegúrate que el nombre de la columna coincida
            // Puedes añadir más cabeceras si mapeas más campos
            // 'Fecha de Creación',
            // 'Fecha de Actualización',
        ];
    }

    /**
     * Mapea cada registro (RoomRental) a un array para la fila del Excel.
     * @param RoomRental $roomRental
     */
    public function map($roomRental): array
    {
        return [
            $roomRental->rent?->name ?? 'N/A',
            $roomRental->rent?->cost ?? 0, // Este es el valor numérico
            $roomRental->start_time,
            $roomRental->end_time,
            // $roomRental->created_at->format('Y-m-d H:i:s'), // Ejemplo de formato de fecha
            // $roomRental->updated_at->format('Y-m-d H:i:s'), // Ejemplo de formato de fecha
        ];
    }

    /**
     * Registra eventos para manipular la hoja de cálculo.
     * Usaremos AfterSheet para añadir la fila de suma.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Clonamos la consulta original para obtener los datos y calcular la suma.
                // Esto asegura que la suma se basa en los mismos datos que se exportaron.
               // Usamos parent::getQuery() para obtener la consulta base ya filtrada.
                $queryForSum = clone parent::getQuery();
                $dataForSum = $queryForSum->with('rent')->get();
                $totalCost = $dataForSum->sum(fn($rental) => $rental->rent?->cost ?? 0);

                $sheet = $event->sheet->getDelegate(); // Obtener la instancia de PhpSpreadsheet
                $lastRowIndex = $sheet->getHighestRow();
                $summaryRowIndex = $lastRowIndex + 2; // Dejar una fila en blanco antes del resumen

                // Escribir la etiqueta y la suma
                $sheet->mergeCells("A{$summaryRowIndex}:B{$summaryRowIndex}"); // Combinar celdas para la etiqueta
                $sheet->setCellValue("A{$summaryRowIndex}", 'Total Ingresos Filtrados:');
                $sheet->setCellValue("C{$summaryRowIndex}", $totalCost);

                // Aplicar estilos a la fila de resumen (opcional)
                $styleArray = [
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ];
                $sheet->getStyle("A{$summaryRowIndex}")->applyFromArray($styleArray);
                
                $sheet->getStyle("C{$summaryRowIndex}")->getFont()->setBold(true);
                $sheet->getStyle("C{$summaryRowIndex}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD_SIMPLE); // Formato de moneda

                // Auto-ajustar el ancho de las columnas (opcional)
                foreach (range('A', $sheet->getHighestDataColumn()) as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }
            },
        ];
    }
}
