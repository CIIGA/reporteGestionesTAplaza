<?php
function conexion($db)
{

    if ($db == 'implementtaGuadalajaraA') {
        $serverName = "51.79.98.210";
        $connectionInfo = array('Database' => $db, 'UID' => 'sa', 'PWD' => '=JeFGm[jFd%J?7j');
        $cnx = sqlsrv_connect($serverName, $connectionInfo);
        date_default_timezone_set('America/Mexico_City');
        return $cnx;
    } else {
        $serverName = "implementta.mx";
        $connectionInfo = array('Database' => $db, 'UID' => 'sa', 'PWD' => 'vrSxHH3TdC');
        $cnx = sqlsrv_connect($serverName, $connectionInfo);
        date_default_timezone_set('America/Mexico_City');
        return $cnx;
    }
}