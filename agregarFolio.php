<?php
session_start();
$plaza = 'implementtaTecateA';
require "cnx/cnx.php";
$cnx = conexion($plaza);

if (!$cnx) {
    die(print_r(sqlsrv_errors(), true));
}

$id_gestion = $_GET['id_gestion'];
$folio = $_GET['folio'];

// Consulta SQL para insertar el nuevo comentario
$sql = "
    INSERT INTO foliosRegistroReductor (folio, idRegistroReductores)
    VALUES (?, ?)
";

// Preparar la consulta
$params = array($folio, $id_gestion);
$stmt = sqlsrv_query($cnx, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Cerrar la conexión
sqlsrv_free_stmt($stmt);
sqlsrv_close($cnx);

// Devolver una respuesta exitosa
echo "Folio agregado correctamente";
?>
