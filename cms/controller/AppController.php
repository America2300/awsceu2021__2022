<?php
namespace App\Controller; //asigna un nombre

use App\Model\Mascota; //son namespaces , no nombres de carpetas
use App\Helper\ViewHelper;
use App\Helper\DbHelper;


class AppController
{
    var $db;
    var $view;

    function __construct()
    {
        //Conexión a la BBDD
        $dbHelper = new DbHelper();
        $this->db = $dbHelper->db;

        //Instancio el ViewHelper
        $viewHelper = new ViewHelper();
        $this->view = $viewHelper;
    }

    public function index(){

        //Consulta a la bbdd
        $rowset = $this->db->query("SELECT * FROM mascotas WHERE activo=1 AND home=1 ORDER BY fecha DESC");

        //Asigno resultados a un array de instancias del modelo
        $mascotas = array();
        while ($row = $rowset->fetch(\PDO::FETCH_OBJ)){
            array_push($mascotas,new Mascota($row)); //Mascota es la clase
        }

        //Llamo a la vista
        $this->view->vista("app", "index", $mascotas);
    }

    public function acercade(){

        //Llamo a la vista
        $this->view->vista("app", "acerca-de");

    }

    public function mascotas(){

        //Consulta a la bbdd
        $rowset = $this->db->query("SELECT * FROM mascotas WHERE activo=1 ORDER BY fecha DESC");

        //Asigno resultados a un array de instancias del modelo
        $mascotas = array();
        while ($row = $rowset->fetch(\PDO::FETCH_OBJ)){
            array_push($mascotas,new Mascota($row));
        }

        //Llamo a la vista
        $this->view->vista("app", "mascotas", $mascotas);

    }

    public function mascota($slug){ //slug es la direccion

        //Consulta a la bbdd
        $rowset = $this->db->query("SELECT * FROM mascotas WHERE activo=1 AND slug='$slug' LIMIT 1");

        //Asigno resultado a una instancia del modelo
        $row = $rowset->fetch(\PDO::FETCH_OBJ);
        $mascota = new Mascota($row);

        //Llamo a la vista
        $this->view->vista("app", "mascota", $mascota);

    }
}