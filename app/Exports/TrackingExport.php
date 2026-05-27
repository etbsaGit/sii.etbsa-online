<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TrackingExport implements
    FromQuery,
    WithHeadings,
    ShouldQueue,
    WithChunkReading,
    WithStyles,
    WithColumnFormatting
{
    use Exportable;

    protected $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function styles(Worksheet $sheet)
    {
        // Wrap text
        $sheet->getStyle('A:P')
            ->getAlignment()
            ->setWrapText(true);

        // Bordes
        $sheet->getStyle(
            'A1:P' . $sheet->getHighestRow()
        )->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Encabezados
        $sheet->getStyle('A1:P1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);

        // Centrar encabezados
        $sheet->getStyle('A1:P1')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function query()
    {
        $params = $this->params;

        $query = DB::table('vw_tracking_export');

        // folio
        $query->when($params['folio'] ?? null, function ($q, $folio) {
            $q->where('folio', $folio);
        });

        // prospecto
        $query->when($params['prospect'] ?? null, function ($q, $prospect) {
            $q->where('prospect_id', $prospect);
        });

        // agencias
        $query->when($params['agencies'] ?? null, function ($q, $agencies) {

            $agencies = is_array($agencies)
                ? $agencies
                : explode(',', $agencies);

            $q->whereIn('agency_id', $agencies);
        });

        // departamentos
        $query->when($params['departments'] ?? null, function ($q, $departments) {

            $departments = is_array($departments)
                ? $departments
                : explode(',', $departments);

            $q->whereIn('department_id', $departments);
        });

        // vendedores
        $query->when($params['sellers'] ?? null, function ($q, $sellers) {

            $sellers = is_array($sellers)
                ? $sellers
                : explode(',', $sellers);

            $q->whereIn('attended_by', $sellers);
        });

        // assertiveness
        $query->when($params['assertiveness'] ?? null, function ($q, $assertiveness) {

            $assertiveness = is_array($assertiveness)
                ? $assertiveness
                : explode(',', $assertiveness);

            $q->whereIn('assertiveness', $assertiveness);
        });

        // estatus
        $query->when($params['estatus'] ?? null, function ($q, $estatus) {
            $q->where('estatus', $estatus);
        });

        // fechas
        $query->when($params['dates'] ?? null, function ($q, $dates) {

            if (!empty($dates)) {

                $dates = explode(',', $dates);

                if (count($dates) === 2) {

                    $q->whereBetween('created_at', [
                        trim($dates[0]),
                        trim($dates[1])
                    ]);
                }
            }
        });

        // ordenamiento
        $query->orderBy(
            $params['order_by'] ?? 'id',
            $params['order_sort'] ?? 'desc'
        );

        return $query->select([
            'folio',
            'vendedor',
            'prospecto',
            'company',
            'telefono',
            'email',
            'ubi',
            'referencia',
            'precio',
            'moneda',
            'info_seg',
            'creado',
            'updated_at',
            'date_invoice',
            'ultimo_comentario',
            'notas',
        ]);
    }

    public function headings(): array
    {
        return [
            'Folio',
            'Vendedor',
            'Prospecto',
            'Empresa',
            'Teléfono',
            'Email',
            'Ubicación',
            'Referencia',
            'Precio',
            'Moneda',
            'Información seguimiento',
            'Creado',
            'Actualizado',
            'Fecha de factura',
            'Último comentario',
            'Notas',
        ];
    }

    public function chunkSize(): int
    {
        return 2000;
    }
}