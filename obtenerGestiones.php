<?php
session_start();
// $_SESSION['plazaBD'] = 'implementtaTijuanaA';
$plaza = 'implementtaTecateA';
require "cnx/cnx.php";
$cnx = conexion($plaza);

$rango_fechas = $_GET['rango_fechas'];
$cuenta = $_GET['cuenta'];




$gestiones = array(); 
// Construir la consulta SQL
$sql = "select r.idRegistroReductores as id, r.Cuenta as cuenta,f.id as id_folio,f.folio, i.Propietario as propietario, CONCAT(i.Calle, ', ',i.Colonia, ', ', i.CP, ', ',i.Poblacion) as domicilio,
g.Nombre as gestor, i.seriemedidor,r.fechaCaptura as fecha,r.lectura,r.observaciones, r.Latitud as latitud, r.Longitud as longitud 
from RegistroReductores as r
left join foliosRegistroReductor as f on r.idRegistroReductores=f.idRegistroReductores
inner join implementta as i on r.Cuenta=i.Cuenta
inner join AspNetUsers as g on r.IdAspUser=g.Id
where convert(date,r.fechaCaptura) >= '2024-10-01'";

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

if ($resultado === false) {
    // Manejo de errores
    die(print_r(sqlsrv_errors(), true));
}

    // Recorrer los resultados y almacenarlos en el array de calificaciones
while ($row = sqlsrv_fetch_array($resultado, SQLSRV_FETCH_ASSOC)) {
    $fecha = $row['fecha'] ->format('Y-m-d H:i:s');
    if ($row['id_folio'] == null) {
        $id_folio='';
        $folio='';
    }else{
        $id_folio = $row['id_folio'];
        $folio = $row['folio'];
    }
   

    $gestiones[] = array(
        'id' => utf8_encode($row['id']),
        'cuenta' => utf8_encode($row['cuenta']),
        'id_folio' => utf8_encode($id_folio),
        'folio' => utf8_encode($folio),
        'propietario' => utf8_encode($row['propietario']),
        'domicilio' => utf8_encode($row['domicilio']),
        'gestor' => utf8_encode($row['gestor']),
        'seriemedidor' => utf8_encode($row['seriemedidor']),
        'fecha' => $fecha,
        'lectura' => utf8_encode($row['lectura']),
        'observaciones' => utf8_encode($row['observaciones']),
        'latitud' => utf8_encode($row['latitud']),
        'longitud' => utf8_encode($row['longitud']),
    );
    
}



// Liberar los recursos
sqlsrv_free_stmt($resultado);

// Devolver los resultados en formato JSON
echo json_encode([
    "data" => $gestiones,
]);
?>
