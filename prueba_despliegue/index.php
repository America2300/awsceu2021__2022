<?php
    echo "América";

    require_once "conexion.php";

    //insertar dos registros
    $registros = $db->exec('INSERT INTO personas (nombre) VALUES ("Maria")');
    if ($registros) {
        echo "<br/>Se han activado $registros registros.<br/>";
    }

    //$registros = $db->exec('DELETE FROM personas WHERE id>3');



    //muestro el contenido de la tabla
   $resultado = $db->query('SELECT * FROM personas');
    while ($personas = $resultado->fetch(PDO::FETCH_OBJ)){ //Recorro el resultado
        echo $personas->id." ".$personas->nombre." ".$personas->activo."<br>";
    }