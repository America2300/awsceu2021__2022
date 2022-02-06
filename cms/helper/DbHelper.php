<?php

namespace App\Helper;

class DbHelper {
//conexión a bd
    var $db;

    function __construct(){

        //Conexión mediante PDO
        $opciones = [\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"];
        try {
            $this->db = new \PDO(
                'mysql:host=localhost;dbname=cms2', //cambio la base de datos
                'root2',  //usuario de mi base de datos
                'Filanasa27$',
                $opciones);
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            echo 'Falló la conexión: ' . $e->getMessage();
        }

    }

}