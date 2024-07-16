<?php
    class Documento extends Conectar{
        /* TODO: Insertar registro  */
        public function insert_documento($fact_id,$doc_nom){
            $conectar= parent::conexion();
            /* consulta sql */
            $sql="INSERT INTO at_documento (doc_id,fact_id,doc_nom,fech_crea,est) VALUES (null,?,?,now(),1);";
            $sql = $conectar->prepare($sql);
            $sql->bindParam(1,$fact_id);
            $sql->bindParam(2,$doc_nom);
            $sql->execute();
        }

        /* TODO: Obtener Documento por Factura */
        public function get_documento_x_factura($fact_id){
            $conectar= parent::conexion();
            /* consulta sql */
            $sql="SELECT * FROM at_documento WHERE fact_id=?";
            $sql = $conectar->prepare($sql);
            $sql->bindParam(1,$fact_id);
            $sql->execute();
            return $resultado = $sql->fetchAll(pdo::FETCH_ASSOC);
        }

        /* TODO: insertar documento detalle */
        public function insert_documento_detalle($factd_id,$det_nom){
            $conectar= parent::conexion();
            /* consulta sql */
            $sql="INSERT INTO at_documento_detalle (det_id,factd_id,det_nom,est) VALUES (null,?,?,1);";
            $sql = $conectar->prepare($sql);
            $sql->bindParam(1,$factd_id);
            $sql->bindParam(2,$det_nom);
            $sql->execute();
        }

        /* TODO: Obtener Documento x detalle */
        public function get_documento_detalle_x_facturad($factd_id){
            $conectar= parent::conexion();
            /* consulta sql */
            $sql="SELECT * FROM at_documento_detalle WHERE factd_id=?";
            $sql = $conectar->prepare($sql);
            $sql->bindParam(1,$factd_id);
            $sql->execute();
            return $resultado = $sql->fetchAll(pdo::FETCH_ASSOC);
        }
    }
?>