<?php
session_start();

class conectar {
    protected $dbh;

    protected function conexion() {
        try {
            $conectar = $this->dbh = new PDO("mysql:host=viaduct.proxy.rlwy.net;port=20555;dbname=railway", "root", "crcXTVeuMqIkQJyNLaZErHMFBXxRJbYO");
            return $conectar;
        } catch (Exception $e) {
            print "Error BD: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    public function set_names() {
        return $this->dbh->query("SET NAMES 'utf8'");
    }
    
    public static function ruta() {
        return "http://localhost/ATN_PORTAL_PROVEEDORES/";
    }
}
?>