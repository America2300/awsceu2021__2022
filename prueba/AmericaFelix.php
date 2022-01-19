<?php

$string = file_get_contents(".claves");

$lineas = explode(" ",$string);

//datos de la conexión
$bbdd=$lineas[0]; //la base de datos es la linea 0
$usuario=$lineas[1];//el usuario es la linea 1
$clave=$lineas[2];//el usuario es la linea 2
//Conexión mediante PDO
$opciones = [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"];

//Conexión mediante PDO
try {
    $db = new PDO("mysql:host=localhost;dbname=$bbdd", $usuario, $clave,$opciones);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Falló la conexión: ' . $e->getMessage();
}

//Comprobación de la versión
echo $db->getAttribute(PDO::ATTR_SERVER_VERSION);