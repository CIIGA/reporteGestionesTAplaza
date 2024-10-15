<?php
session_start();
$plaza = 'implementtaTecateA';
require "cnx/cnx.php";
$cnx = conexion($plaza);

if (!$cnx) {
    die(print_r(sqlsrv_errors(), true));
}

$id_folio = $_GET['id_folio'];
$folio_nuevo = $_GET['folio_nuevo'];

// Consulta SQL para insertar el nuevo comentario
$sql = "
    update foliosRegistroReductor set folio='$folio_nuevo' where id ='$id_folio'
";

// Preparar la consulta
$stmt = sqlsrv_query($cnx, $sql);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Cerrar la conexión
sqlsrv_free_stmt($stmt);
sqlsrv_close($cnx);

// Devolver una respuesta exitosa
echo "Folio actualizado correctamente";
?>
