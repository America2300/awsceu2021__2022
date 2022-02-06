<?php
namespace App\Controller;

use App\Helper\ViewHelper;
use App\Helper\DbHelper;
use App\Model\Mascota;


class MascotaController
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

    //Listado de mascotas
    public function index(){

        //Permisos
        $this->view->permisos("mascotas");

        //Recojo las mascotas de la base de datos
        $rowset = $this->db->query("SELECT * FROM mascotas ORDER BY fecha DESC");

        //Asigno resultados a un array de instancias del modelo
        $mascota = array();
        while ($row = $rowset->fetch(\PDO::FETCH_OBJ)){
            array_push($mascota,new Mascota($row));
        }

        $this->view->vista("admin","mascotas/index", $mascota);

    }

    //Para activar o desactivar
    public function activar($id){

        //Permisos
        $this->view->permisos("mascotas");

        //Obtengo la mascota
        $rowset = $this->db->query("SELECT * FROM mascotas WHERE id='$id' LIMIT 1");
        $row = $rowset->fetch(\PDO::FETCH_OBJ);
        $mascota = new Mascota($row);

        if ($mascota->activo == 1){

            //Desactivo la mascota
            $consulta = $this->db->exec("UPDATE mascotas SET activo=0 WHERE id='$id'");

            //Mensaje y redirección
            ($consulta > 0) ? //Compruebo consulta para ver que no ha habido errores
                $this->view->redireccionConMensaje("admin/mascotas","green","La mascota <strong>$mascota->titulo</strong> se ha desactivado correctamente.") :
                $this->view->redireccionConMensaje("admin/mascotas","red","Hubo un error al guardar en la base de datos.");
        }

        else{

            //Activo la mascota
            $consulta = $this->db->exec("UPDATE mascotas SET activo=1 WHERE id='$id'");

            //Mensaje y redirección
            ($consulta > 0) ? //Compruebo consulta para ver que no ha habido errores
                $this->view->redireccionConMensaje("admin/mascotas","green","La mascota <strong>$mascota->titulo</strong> se ha activado correctamente.") :
                $this->view->redireccionConMensaje("admin/mascotas","red","Hubo un error al guardar en la base de datos.");
        }

    }

    //Para mostrar o no en la home
    public function home($id){

        //Permisos
        $this->view->permisos("mascotas");

        //Obtengo la mascota
        $rowset = $this->db->query("SELECT * FROM mascotas WHERE id='$id' LIMIT 1");
        $row = $rowset->fetch(\PDO::FETCH_OBJ);
        $mascota = new Mascota($row);

        if ($mascota->home == 1){

            //Quito la mascota de la home
            $consulta = $this->db->exec("UPDATE mascotas SET home=0 WHERE id='$id'");

            //Mensaje y redirección
            ($consulta > 0) ? //Compruebo consulta para ver que no ha habido errores
                $this->view->redireccionConMensaje("admin/mascotas","green","La mascota <strong>$mascota->titulo</strong> ya no se muestra en la home.") :
                $this->view->redireccionConMensaje("admin/mascotas","red","Hubo un error al guardar en la base de datos.");
        }

        else{

            //Muestro la mascota en la home
            $consulta = $this->db->exec("UPDATE mascotas SET home=1 WHERE id='$id'");

            //Mensaje y redirección
            ($consulta > 0) ? //Compruebo consulta para ver que no ha habido errores
                $this->view->redireccionConMensaje("admin/mascotas","green","La mascota <strong>$mascota->titulo</strong> ahora se muestra en la home.") :
                $this->view->redireccionConMensaje("admin/mascotas","red","Hubo un error al guardar en la base de datos.");
        }

    }

    public function borrar($id){

        //Permisos
        $this->view->permisos("mascotas");

        //Obtengo la mascota
        $rowset = $this->db->query("SELECT * FROM mascotas WHERE id='$id' LIMIT 1");
        $row = $rowset->fetch(\PDO::FETCH_OBJ);
        $mascota = new Mascota($row);

        //Borro la mascota
        $consulta = $this->db->exec("DELETE FROM mascotas WHERE id='$id'");

        //Borro la imagen asociada
        $archivo = $_SESSION['public']."img/".$mascota->imagen;
        $texto_imagen = "";
        if (is_file($archivo)){
            unlink($archivo);
            $texto_imagen = " y se ha borrado la imagen asociada";
        }

        //Mensaje y redirección
        ($consulta > 0) ? //Compruebo consulta para ver que no ha habido errores
            $this->view->redireccionConMensaje("admin/mascotas","green","La mascota se ha borrado correctamente$texto_imagen.") :
            $this->view->redireccionConMensaje("admin/mascotas","red","Hubo un error al guardar en la base de datos.");

    }

    public function crear(){

        //Permisos
        $this->view->permisos("mascotas");

        //Creo un nuevo usuario vacío
        $mascota = new Mascota();

        //Llamo a la ventana de edición
        $this->view->vista("admin","mascotas/editar", $mascota);

    }

    public function editar($id){

        //Permisos
        $this->view->permisos("mascotas");

        //Si ha pulsado el botón de guardar
        if (isset($_POST["guardar"])){

            //Recupero los datos del formulario
            $titulo = filter_input(INPUT_POST, "titulo", FILTER_SANITIZE_STRING);
            $entradilla = filter_input(INPUT_POST, "entradilla", FILTER_SANITIZE_STRING);
            $animal = filter_input(INPUT_POST, "animal", FILTER_SANITIZE_STRING);
            $fecha = filter_input(INPUT_POST, "fecha", FILTER_SANITIZE_STRING);
            $texto = filter_input(INPUT_POST, "texto", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $personalidad = filter_input(INPUT_POST, "personalidad", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            //Formato de fecha para SQL
            $fecha = \DateTime::createFromFormat("d-m-Y", $fecha)->format("Y-m-d H:i:s");

            //Genero slug (url amigable)
            $slug = $this->view->getSlug($titulo);

            //Imagen
            $imagen_recibida = $_FILES['imagen'];
            $imagen = ($_FILES['imagen']['name']) ? $_FILES['imagen']['name'] : "";
            $imagen_subida = ($_FILES['imagen']['name']) ? '/var/www/html'.$_SESSION['public']."img/".$_FILES['imagen']['name'] : "";
            $texto_img = ""; //Para el mensaje

            if ($id == "nuevo"){

                //Creo una nueva mascota
                $consulta = $this->db->exec("INSERT INTO mascotas 
                    (titulo, entradilla, animal, fecha, texto,personalidad, slug, imagen) VALUES 
                    ('$titulo','$entradilla','$animal','$fecha','$texto','$personalidad','$slug','$imagen')");

                //Subo la imagen
                if ($imagen){
                    if (is_uploaded_file($imagen_recibida['tmp_name']) && move_uploaded_file($imagen_recibida['tmp_name'], $imagen_subida)){
                        $texto_img = " La imagen se ha subido correctamente.";
                    }
                    else{
                        $texto_img = " Hubo un problema al subir la imagen.";
                    }
                }

                //Mensaje y redirección
                ($consulta > 0) ?
                    $this->view->redireccionConMensaje("admin/mascotas","green","La mascota <strong>$titulo</strong> se creado correctamente.".$texto_img) :
                    $this->view->redireccionConMensaje("admin/mascotas","red","Hubo un error al guardar en la base de datos.");
            }
            else{

                //Actualizo la mascota
                $this->db->exec("UPDATE mascotas SET 
                    titulo='$titulo',entradilla='$entradilla',animal='$animal',
                    fecha='$fecha',texto='$texto',personalidad='$personalidad',slug='$slug' WHERE id='$id'");

                //Subo y actualizo la imagen
                if ($imagen){
                    if (is_uploaded_file($imagen_recibida['tmp_name']) && move_uploaded_file($imagen_recibida['tmp_name'], $imagen_subida)){
                        $texto_img = " La imagen se ha subido correctamente.";
                        $this->db->exec("UPDATE mascotas SET imagen='$imagen' WHERE id='$id'");
                    }
                    else{
                        $texto_img = " Hubo un problema al subir la imagen.";
                    }
                }

                //Mensaje y redirección
                $this->view->redireccionConMensaje("admin/mascotas","green","La mascota <strong>$titulo</strong> se guardado correctamente.".$texto_img);

            }
        }

        //Si no, obtengo mascota y muestro la ventana de edición
        else{

            //Obtengo la mascota
            $rowset = $this->db->query("SELECT * FROM mascotas WHERE id='$id' LIMIT 1");
            $row = $rowset->fetch(\PDO::FETCH_OBJ);
            $mascota = new Mascota($row);

            //Llamo a la ventana de edición
            $this->view->vista("admin","mascotas/editar", $mascota);
        }

    }

}