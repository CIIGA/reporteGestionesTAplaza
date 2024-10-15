<?php
session_start();
$plaza = 'implementtaTecateA';
require "cnx/cnx.php";
$cnx = conexion($plaza);

if (!$cnx) {
    die(print_r(sqlsrv_errors(), true));
}

$id_gestion = $_POST['id_gestion'];

// Consulta para obtener los comentarios del municipio
$sqlMunicipio = "
    SELECT comentario, fecha, 'municipio' AS tipo
    FROM comentariosMunicipio
    WHERE id_gestion = ?
";

// Consulta para obtener los comentarios del gestor
$sqlGestor = "
    SELECT comentario, fecha, 'plaza' AS tipo
    FROM comentariosPlaza
    WHERE id_gestion = ?
";

// Unir ambas consultas y ordenar por fecha
$sql = "
    ($sqlMunicipio)
    UNION ALL
    ($sqlGestor)
    ORDER BY fecha asc
";

// Preparar la consulta
$params = array($id_gestion, $id_gestion); // Pasa el mismo id para ambas tablas
$stmt = sqlsrv_query($cnx, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Almacenar los resultados
$comentarios = array();
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $comentarios[] = array(
        'comentario' => $row['comentario'],
        'fecha' => $row['fecha']->format('Y-m-d H:i:s'), // Formatear la fecha
        'tipo' => $row['tipo']
    );
}

// Cerrar la conexión
sqlsrv_free_stmt($stmt);
sqlsrv_close($cnx);

// Devolver los comentarios como JSON
echo json_encode($comentarios);
?>
