<?php
    /*TODO: llamada a las clases necesarias */
    require_once("../config/conexion.php");
    require_once("../models/Email.php");
    $email = new Email();

    /*TODO: opciones del controlador */
    switch ($_GET["op"]) {
        /*TODO: enviar factura abierto segun el ID */
        case "factura_abierta":
            $email->factura_abierta($_POST["fact_id"]);
            break;

        /*TODO: enviar factura Cerrado segun el ID */
        case "factura_cerrada":
            $email->factura_cerrada($_POST["fact_id"]);
            break;

        /*TODO: enviar factura asignado segun el ID */
        case "factura_asignada":
            $email->factura_asignada($_POST["fact_id"]);
            break;

        case "recuperar_contra":
            $email->recuperar_contrasena($_POST["usu_correo"]);
            break;
    }
?>