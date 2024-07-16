<?php
    /*TODO: Librerias necesarias para que se puedan enviar emails*/
    require '../include/vendor/autoload.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require_once("../config/conexion.php");
    require_once("../models/Factura.php");
    require_once("../models/Usuario.php");

    class email extends PHPMailer {

        protected $gcorreo = 'univ30mt@gmail.com'; //Variable que contiene el correo del destinatario
        protected $gcontrasena = 'epsimnzrxxyhijxh'; //Variable que contiene la contraseña del correo del destinatario

        public function factura_abierta($fact_id){
            $factura = new Factura();
            $datos = $factura->listar_factura_x_id($fact_id);
            foreach ($datos as $row) {
                $id = $row["fact_id"];
                $usu = $row["usu_nom"];
                $titulo = $row["fact_titulo"];
                $categoria = $row["cat_nom"];
                $correo = $row["usu_correo"];
            }
            $this->isSMTP();
            $this->Host = 'smtp.gmail.com';//Servidor
            $this->Port = 465;//Puerto
            $this->SMTPAuth = true;
            $this->Username = $this->gcorreo;
            $this->Password = $this->gcontrasena;
            $this->setFrom($this->gcorreo, "Factura Abierta nº ".$id);

            $this->SMTPSecure = 'ssl';
            $this->CharSet = 'UTF8';
            $this->addAddress($correo);
            $this->addCC('univ30mt@gmail.com');
            
            $this->isHTML(true);
            $this->Subject = "Factura Abierta nº ".$id;

            $cuerpo = file_get_contents('../public/NuevaFactura.html');
            $cuerpo = str_replace("xnrofactura", $id, $cuerpo); //Ruta del template HTML
            $cuerpo = str_replace("lblNomUsu", $usu, $cuerpo);
            $cuerpo = str_replace("lblTitu", $titulo, $cuerpo); //Ruta del template HTML
            $cuerpo = str_replace("lblCate", $categoria, $cuerpo); 

            $this->Body = $cuerpo;
            $this->AltBody = strip_tags("Factura Abierta");

            try{
                $this->send();
                return true;
            }catch(Exception $e){
                return false;
            }
        }


        public function factura_cerrada($fact_id){
            $factura = new Factura();
            $datos = $factura->listar_factura_x_id($fact_id);
            foreach ($datos as $row) {
                $id = $row["fact_id"];
                $usu = $row["usu_nom"];
                $titulo = $row["fact_titulo"];
                $categoria = $row["cat_nom"];
                $correo = $row["usu_correo"];
            }
            $this->isSMTP();
            $this->Host = 'smtp.gmail.com';//Servidor
            $this->Port = 465;//Puerto
            $this->SMTPAuth = true;
            $this->Username = $this->gcorreo;
            $this->Password = $this->gcontrasena;
            $this->SMTPSecure = 'ssl';
            $this->setFrom($this->gcorreo, "Factura Cerrada nº ".$id);
            $this->CharSet = 'UTF8';
            $this->addAddress($correo);
            $this->addCC('univ30mt@gmail.com');
            
            $this->WordWrap = 50;
            $this->isHTML(true);
            $this->Subject = "Factura Cerrada nº ".$id;

            $cuerpo = file_get_contents('../public/CerradaFactura.html');
            $cuerpo = str_replace("xnrofactura", $id, $cuerpo); //Ruta del template HTML
            $cuerpo = str_replace("lblNomUsu", $usu, $cuerpo);
            $cuerpo = str_replace("lblTitu", $titulo, $cuerpo); //Ruta del template HTML
            $cuerpo = str_replace("lblCate", $categoria, $cuerpo); 

            $this->Body = $cuerpo;
            $this->AltBody = strip_tags("Factura Cerrada");
            try{
                $this->send();
                return true;
            }catch(Exception $e){
                return false;
            }
        }

        public function factura_asignada($fact_id){
            $factura = new Factura();
            $datos = $factura->listar_factura_x_id($fact_id);
            foreach ($datos as $row) {
                $id = $row["fact_id"];
                $usu = $row["usu_nom"];
                $titulo = $row["fact_titulo"];
                $categoria = $row["cat_nom"];
                $correo = $row["usu_correo"];
            }
            $this->isSMTP();
            $this->Host = 'smtp.gmail.com';//Servidor
            $this->Port = 465;//Puerto
            $this->SMTPAuth = true;
            $this->Username = $this->gcorreo;
            $this->Password = $this->gcontrasena;
            $this->SMTPSecure = 'ssl';
            $this->setFrom($this->gcorreo, "Factura Asignada nº ".$id);
            $this->CharSet = 'UTF8';
            $this->addAddress($correo);
            $this->addCC('univ30mt@gmail.com');
            
            $this->WordWrap = 50;
            $this->isHTML(true);
            $this->Subject = "Factura Asignada nº ".$id;

            $cuerpo = file_get_contents('../public/AsignarFactura.html');
            $cuerpo = str_replace("xnrofactura", $id, $cuerpo); //Ruta del template HTML
            $cuerpo = str_replace("lblNomUsu", $usu, $cuerpo);
            $cuerpo = str_replace("lblTitu", $titulo, $cuerpo); //Ruta del template HTML
            $cuerpo = str_replace("lblCate", $categoria, $cuerpo); 

            $this->Body = $cuerpo;
            $this->AltBody = strip_tags("Factura Asignada");
            try{
                $this->send();
                return true;
            }catch(Exception $e){
                return false;
            }
        }

        public function recuperar_contrasena($usu_correo){
            $usuario = new Usuario();

            $usuario->get_cambiar_contra_recuperar($usu_correo);

            $datos = $usuario->get_usuario_x_correo($usu_correo);
            foreach ($datos as $row) {
                $usu_id = $row["usu_id"];
                $usu_ape = $row["usu_ape"];
                $usu_nom = $row["usu_nom"];
                $correo = $row["usu_correo"];
                $usu_pass = $row["usu_pass"];
            }
            $this->isSMTP();
            $this->Host = 'smtp.gmail.com';//Servidor
            $this->Port = 465;//Puerto
            $this->SMTPAuth = true;
            $this->Username = $this->gcorreo;
            $this->Password = $this->gcontrasena;
            $this->setFrom($this->gcorreo, "Recuperar Contraseña");

            $this->SMTPSecure = 'ssl';
            $this->CharSet = 'UTF8';
            $this->addAddress($usu_correo);
            $this->addCC('univ30mt@gmail.com');
            
            $this->isHTML(true);
            $this->Subject = "Recuperar Contraseña";

            $cuerpo = file_get_contents('../public/RecuperarContra.html'); //Ruta del template HTML
            $cuerpo = str_replace("xusunom", $usu_nom, $cuerpo); 
            $cuerpo = str_replace("xusuape", $usu_ape, $cuerpo);
            $cuerpo = str_replace("xnuevopass", $usu_pass, $cuerpo);

            $this->Body = $cuerpo;
            $this->AltBody = strip_tags("Recuperar Contraseña");

            try{
                $this->send();
                $usuario->encriptar_nueva_contra($usu_id,$usu_pass);
                return true;
            }catch(Exception $e){
                return false;
            }
        }
    }
?>