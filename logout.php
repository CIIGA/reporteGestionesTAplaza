<?php
session_start();
// Borrar todas las variables de sesión
$_SESSION = array();

// Destruir la sesión
session_destroy();
echo '<meta http-equiv="refresh" content="0,url=login.php">';
?>