<?php
require_once("../config/conexion.php");
require_once("../models/documento.php");
$documento = new Documento();

$key = "mi_key_secret";
$cipher = "aes-256-cbc";

switch($_GET["op"]) {
    case "listar":
        error_log("Operación listar: fact_id cifrado recibido es " . $_POST["fact_id"]);

        // Desencriptar fact_id antes de usarlo
        $iv_length = openssl_cipher_iv_length($cipher);
        $decoded = base64_decode($_POST["fact_id"]);
        $iv = substr($decoded, 0, $iv_length);
        $cifradoSinIV = substr($decoded, $iv_length);
        $fact_id = openssl_decrypt($cifradoSinIV, $cipher, $key, OPENSSL_RAW_DATA, $iv);

        if ($fact_id === false) {
            error_log("Error desencriptando fact_id");
            echo json_encode(["error" => "Error al desencriptar fact_id"]);
            exit;
        }

        $datos = $documento->get_documento_x_factura($fact_id);
        $data = Array();

        if (empty($datos)) {
            error_log("No se encontraron documentos para la factura ID: " . $fact_id);
        } else {
            foreach($datos as $row){
                $document_path = "../../public/document/" . $fact_id . "/" . $row["doc_nom"];
                $sub_array = array();
                $sub_array[] = '<a href="' . $document_path . '" target="_blank">' . $row["doc_nom"] . '</a>';
                $sub_array[] = '<a type="button" href="' . $document_path . '" target="_blank" class="btn btn-inline btn-primary btn-sm ladda-button btn-sm">Ver</a>';
                $data[] = $sub_array;
                error_log("Documento agregado: " . $row["doc_nom"]);
            }
        }

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );

        echo json_encode($results);
        error_log("Resultados de listar: " . json_encode($results));
        break;
}
?>
