<?php


//Requiero el archivo de conexión
require_once "conexion.php";

//insertar dos registros
$registros = $db->exec('INSERT INTO personas (nombre) VALUES ("José"),("Luís")');
if ($registros) {
    echo "Se han activado $registros registros.";
}

/*$registros = $db->exec('DELETE FROM personas WHERE id>3');
if ($registros){
    echo "Se han activado $registros registros.";
}*/



