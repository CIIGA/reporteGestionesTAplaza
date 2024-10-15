<?php
session_start();
$plaza = 'implementtaTecateA';
require "cnx/cnx.php";
$cnx = conexion($plaza);

if (!$cnx) {
    die(print_r(sqlsrv_errors(), true));
}

$id_gestion = $_POST['id_gestion'];
$comentario = $_POST['comentario'];
$fecha = date('Y-m-d H:i:s'); // Fecha actual
$estado = 1; // Estado predeterminado

// Consulta SQL para insertar el nuevo comentario
$sql = "
    INSERT INTO comentariosPlaza (comentario, fecha, id_gestion, estado)
    VALUES (?, ?, ?, ?)
";

// Preparar la consulta
$params = array($comentario, $fecha, $id_gestion, $estado);
$stmt = sqlsrv_query($cnx, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Cerrar la conexión
sqlsrv_free_stmt($stmt);
sqlsrv_close($cnx);

// Devolver una respuesta exitosa
echo "Comentario agregado correctamente";
?>
