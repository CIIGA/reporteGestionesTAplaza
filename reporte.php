<?php
set_time_limit(1000000); // 5 minutos

session_start();

// $_SESSION['plazaBD'] = 'implementtaTijuanaA';
$plaza = 'implementtaTecateA';
require "cnx/cnx.php";
$cnx = conexion($plaza);


require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

$rango_fechas = $_GET['rango_fechas'];
$cuenta = $_GET['cuenta'];



// Construir la consulta SQL
$sql = "select r.idRegistroReductores as id, r.Cuenta as cuenta,f.id as id_folio,f.folio, i.Propietario as propietario, CONCAT(i.Calle, ', ',i.Colonia, ', ', i.CP, ', ',i.Poblacion) as domicilio,
g.Nombre as gestor, i.seriemedidor,r.fechaCaptura as fecha,r.lectura,r.observaciones, r.Latitud as latitud, r.Longitud as longitud 
from RegistroReductores as r
left join foliosRegistroReductor as f on r.idRegistroReductores=f.idRegistroReductores
inner join implementta as i on r.Cuenta=i.Cuenta
inner join AspNetUsers as g on r.IdAspUser=g.Id
where convert(date,r.fechaCaptura) >= '2024-10-01' ";

if (!empty($cuenta)) {
    $sql .= " AND r.Cuenta='$cuenta'";
}

if (!empty($rango_fechas)) {
    list($fechaInicio, $fechaFin) = explode(" - ", $rango_fechas);
    $sql .= " AND convert(date,r.fechaCaptura) between '$fechaInicio' and '$fechaFin'";
}

$sql .= " order by r.fechaCaptura desc";

// Ejecutar la consulta
$resultado = sqlsrv_query($cnx, $sql);

// Crear un nuevo objeto Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Agregar el título
$title = "REPORTE DE GESTIONES";
$sheet->setCellValue('A1', $title);
$sheet->mergeCells('A1:K1');
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A1')->getFont()->setBold(true);

// Escribir los encabezados de la tabla
$headers = [
    "Cuenta",
    "Folio",
    "Propietario",
    "Domilicio",
    "Gestor",
    "Serie Medidor",
    "Fecha Captura",
    "Lectura",
    "Observaciones",
    "Geo Punto",
    "Fotos"
];
$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '2', $header);
    $col++;
}

// Escribir los datos de la consulta
$fila = 3;
while ($row = sqlsrv_fetch_array($resultado, SQLSRV_FETCH_ASSOC)) {
    // $fecha = $row['fecha']->format('Y-m-d H:i:s');

    $sheet->setCellValue('A' . $fila, trim($row['cuenta']));
    $sheet->setCellValue('B' . $fila, trim($row['folio']));
    $sheet->setCellValue('C' . $fila, utf8_encode($row['propietario']));
    $sheet->setCellValue('D' . $fila, utf8_encode($row['domicilio']));
    $sheet->setCellValue('E' . $fila, utf8_encode($row['gestor']));
    $sheet->setCellValue('F' . $fila, utf8_encode($row['seriemedidor']));
    $sheet->setCellValue('G' . $fila, $row['fecha']);
    $sheet->setCellValue('H' . $fila, $row['lectura']);
    $sheet->setCellValue('I' . $fila, utf8_encode($row['observaciones']));

    $latitud = $row['latitud'];
    $longitud = $row['longitud'];
    $geopunto = '';

    if (!empty($latitud) && !empty($longitud)) {
        // Construir el geopunto como una URL de Google Maps
        $geopunto = "https://www.google.com/maps/search/?api=1&query={$latitud},{$longitud}";

        // Establecer el texto de la celda (por ejemplo, "Ubicación")
        $sheet->setCellValue('J' . $fila, "Ubicación");

        // Configurar el hipervínculo con la URL del geopunto
        $sheet->getCell('J' . $fila)->getHyperlink()->setUrl($geopunto);

        // Cambiar el color del texto de la celda a azul
        $sheet->getStyle('J' . $fila)->getFont()->getColor()->setARGB(Color::COLOR_BLUE);
    } else {
        // Si no hay latitud o longitud, dejar la celda vacía
        $sheet->setCellValue('J' . $fila, "");
    }


    $num_fotos = 1;
    $foto_text = "Foto";




    $id_gestion = $row['id'];
    if (!empty($id_gestion)) {
        $sql_fotos = "
                SELECT f.urlImagen FROM RegistroReductores a INNER JOIN [dbo].Registrofotomovil f ON a.cuenta = f.cuenta WHERE 
            a.IdRegistroReductores = '$id_gestion'
            AND CONVERT(DATE, a.fechacaptura) = CONVERT(DATE, f.fechacaptura)
            ";

        $cnx_sql_fotos = sqlsrv_query($cnx, $sql_fotos);
        $col_fotos = 'K';

        while ($row_fotos = sqlsrv_fetch_array($cnx_sql_fotos, SQLSRV_FETCH_ASSOC)) {
            // Agregar la etiqueta "Foto X" en la celda
            $sheet->setCellValue($col_fotos . $fila, $foto_text . ' ' . $num_fotos);

            // Agregar el enlace a la foto
            $sheet->getCell($col_fotos . $fila)->getHyperlink()->setUrl($row_fotos['urlImagen']);
            $sheet->getStyle($col_fotos . $fila)->getFont()->getColor()->setARGB(Color::COLOR_BLUE);

            // Agregar bordes a la celda de la foto
            $styleArrayFoto = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ];
            $sheet->getStyle($col_fotos . $fila)->applyFromArray($styleArrayFoto);

            // Incrementar el número de foto y avanzar a la siguiente columna
            $num_fotos++;
            $col_fotos++;
        }
    }


    $fila++;
}

// Aplicar bordes a todas las celdas de la tabla
$styleArray = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
        ],
    ],
];
$sheet->getStyle('A1:K' . ($fila - 1))->applyFromArray($styleArray);
// Calcular el ancho automático de las columnas basado en el contenido
foreach (range('A', 'K') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}
// Guardar el archivo Excel temporalmente en el servidor
$filename = 'Reporte_Gestiones_' . date('YmdHis') . '.xlsx';
$writer = new Xlsx($spreadsheet);
$writer->save($filename);

// Enviar el archivo Excel al navegador
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
header('Content-Length: ' . filesize($filename));
header('Pragma: public');
readfile($filename);

// Eliminar el archivo temporal del servidor
unlink($filename);

exit;
