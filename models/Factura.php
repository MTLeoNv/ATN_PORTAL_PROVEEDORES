<?php
    class Factura extends Conectar{

        /* TODO: insertar nuevo factura */
        public function insert_factura($usu_id,$cat_id,$cats_id,$fact_titulo,$fact_descrip,$prio_id){
            $conectar= parent::conexion();
            parent::set_names();
            $sql="INSERT INTO atn_factura (fact_id,usu_id,cat_id,cats_id,fact_titulo,fact_descrip,fact_estado,fech_crea,usu_asig,fech_asig,prio_id,est) VALUES (NULL,?,?,?,?,?,'Abierto',now(),NULL,NULL,?,'1');";
            $sql=$conectar->prepare($sql);
            $sql->bindValue(1, $usu_id);
            $sql->bindValue(2, $cat_id);
            $sql->bindValue(3, $cats_id);
            $sql->bindValue(4, $fact_titulo);
            $sql->bindValue(5, $fact_descrip);
            $sql->bindValue(6, $prio_id);
            $sql->execute();

            $sql1="select last_insert_id() as 'fact_id';";
            $sql1=$conectar->prepare($sql1);
            $sql1->execute();
            return $resultado=$sql1->fetchAll(pdo::FETCH_ASSOC);
        }

        /* TODO: Listar factura segun id de usuario */
        public function listar_factura_x_usu($usu_id){
            $conectar= parent::conexion();
            parent::set_names();
            $sql="SELECT 
                atn_factura.fact_id,
                atn_factura.usu_id,
                atn_factura.cat_id,
                atn_factura.fact_titulo,
                atn_factura.fact_descrip,
                atn_factura.fact_estado,
                atn_factura.fech_crea,
                atn_factura.fech_cierre,
                atn_factura.usu_asig,
                atn_factura.fech_asig,
                atn_usuario.usu_nom,
                atn_usuario.usu_ape,
                atn_categoria.cat_nom,
                atn_factura.prio_id,
                atn_prioridad.prio_nom
                FROM 
                atn_factura
                INNER join atn_categoria on atn_factura.cat_id = atn_categoria.cat_id
                INNER join atn_usuario on atn_factura.usu_id = atn_usuario.usu_id
                INNER join atn_prioridad on atn_factura.prio_id = atn_prioridad.prio_id
                WHERE
                atn_factura.est = 1
                AND atn_usuario.usu_id=?
                ORDER BY atn_factura.fact_id DESC";
            $sql=$conectar->prepare($sql);
            $sql->bindValue(1, $usu_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* TODO: Mostrar factura segun id de factura */
        public function listar_factura_x_id($fact_id){
            $conectar= parent::conexion();
            parent::set_names();
            $sql="SELECT 
                atn_factura.fact_id,
                atn_factura.usu_id,
                atn_factura.cat_id,
                atn_factura.cats_id,
                atn_factura.fact_titulo,
                atn_factura.fact_descrip,
                atn_factura.fact_estado,
                atn_factura.fech_crea,
                atn_factura.fech_cierre,
                atn_factura.fact_estre,
                atn_factura.fact_coment,
                atn_factura.usu_asig,
                atn_usuario.usu_nom,
                atn_usuario.usu_ape,
                atn_usuario.usu_correo,
                atn_categoria.cat_nom,
                atn_subcategoria.cats_nom,
                atn_factura.prio_id,
                atn_prioridad.prio_nom
                FROM 
                atn_factura
                INNER join atn_categoria on atn_factura.cat_id = atn_categoria.cat_id
                INNER join atn_subcategoria on atn_factura.cats_id = atn_subcategoria.cats_id
                INNER join atn_usuario on atn_factura.usu_id = atn_usuario.usu_id
                INNER join atn_prioridad on atn_factura.prio_id = atn_prioridad.prio_id
                WHERE
                atn_factura.est = 1
                AND atn_factura.fact_id = ?";
            $sql=$conectar->prepare($sql);
            $sql->bindValue(1, $fact_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* TODO: Mostrar todos los factura */
        public function listar_factura(){
            $conectar= parent::conexion();
            parent::set_names();
            $sql="SELECT
                atn_factura.fact_id,
                atn_factura.usu_id,
                atn_factura.cat_id,
                atn_factura.fact_titulo,
                atn_factura.fact_descrip,
                atn_factura.fact_estado,
                atn_factura.fech_crea,
                atn_factura.fech_cierre,
                atn_factura.usu_asig,
                atn_factura.fech_asig,
                atn_usuario.usu_nom,
                atn_usuario.usu_ape,
                atn_categoria.cat_nom,
                atn_factura.prio_id,
                atn_prioridad.prio_nom
                FROM 
                atn_factura
                INNER join atn_categoria on atn_factura.cat_id = atn_categoria.cat_id
                INNER join atn_usuario on atn_factura.usu_id = atn_usuario.usu_id
                INNER join atn_prioridad on atn_factura.prio_id = atn_prioridad.prio_id
                WHERE
                atn_factura.est = 1
                ";
            $sql=$conectar->prepare($sql);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* TODO: Mostrar detalle de factura por id de factura */
        public function listar_facturadetalle_x_factura($fact_id){
            $conectar= parent::conexion();
            parent::set_names();
            $sql="SELECT
                at_facturadetalle.factd_id,
                at_facturadetalle.factd_descrip,
                at_facturadetalle.fech_crea,
                atn_usuario.usu_nom,
                atn_usuario.usu_ape,
                atn_usuario.rol_id
                FROM 
                at_facturadetalle
                INNER join atn_usuario on at_facturadetalle.usu_id = atn_usuario.usu_id
                WHERE 
                fact_id =?";
            $sql=$conectar->prepare($sql);
            $sql->bindValue(1, $fact_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* TODO: Insert factura detalle */
        public function insert_facturadetalle($fact_id,$usu_id,$factd_descrip){
            $conectar= parent::conexion();
            parent::set_names();

            /* TODO:Obtener usuario asignado del fact_id */
            $factura = new Factura();
            $datos = $factura->listar_factura_x_id($fact_id);
            foreach ($datos as $row){
                $usu_asig = $row["usu_asig"];
                $usu_crea = $row["usu_id"];
            }

            /* TODO: si Rol es 1 = Usuario insertar alerta para usuario soporte */
            if ($_SESSION["rol_id"]==1){
                /* TODO: Guardar Notificacion de nuevo Comentario */
                /* $sql0="INSERT INTO atn_notificacion (not_id,usu_id,not_mensaje,fact_id,est) VALUES (null, $usu_asig ,'Tiene una nueva respuesta del usuario con nro de factura : ',$fact_id,2)";
                $sql0=$conectar->prepare($sql0);
                $sql0->execute(); */
            /* TODO: Else Rol es = 2 Soporte Insertar alerta para usuario que genero el factura */
            }else{
                /* TODO: Guardar Notificacion de nuevo Comentario */
                /* $sql0="INSERT INTO atn_notificacion (not_id,usu_id,not_mensaje,fact_id,est) VALUES (null,$usu_crea,'Tiene una nueva respuesta de soporte del factura Nro : ',$fact_id,2)";
                $sql0=$conectar->prepare($sql0);
                $sql0->execute(); */
            }

            $sql="INSERT INTO at_facturadetalle (factd_id,fact_id,usu_id,factd_descrip,fech_crea,est) VALUES (NULL,?,?,?,now(),'1');";
            $sql=$conectar->prepare($sql);
            $sql->bindValue(1, $fact_id);
            $sql->bindValue(2, $usu_id);
            $sql->bindValue(3, $factd_descrip);
            $sql->execute();

            /* TODO: Devuelve el ultimo ID (Identty) ingresado */
            $sql1="select last_insert_id() as 'factd_id';";
            $sql1=$conectar->prepare($sql1);
            $sql1->execute();
            return $resultado=$sql1->fetchAll(pdo::FETCH_ASSOC);
        }

        /* TODO: Insertar linea adicional de detalle al cerrar el factura */
        public function insert_facturadetalle_cerrar($fact_id,$usu_id){
            $conectar= parent::conexion();
            parent::set_names();
                $sql="call sp_i_facturadetalle_01(?,?)";
            $sql=$conectar->prepare($sql);
            $sql->bindValue(1, $fact_id);
            $sql->bindValue(2, $usu_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* TODO: Insertar linea adicional al reabrir el factura */
        public function insert_facturadetalle_reabrir($fact_id,$usu_id){
            $conectar= parent::conexion();
            parent::set_names();
                $sql="	INSERT INTO at_facturadetalle 
                    (factd_id,fact_id,usu_id,factd_descrip,fech_crea,est) 
                    VALUES 
                    (NULL,?,?,'Factura Re-Abierto...',now(),'1');";
            $sql=$conectar->prepare($sql);
            $sql->bindValue(1, $fact_id);
            $sql->bindValue(2, $usu_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* TODO: actualizar factura */
        public function update_factura($fact_id){
            $conectar= parent::conexion();
            parent::set_names();
            $sql="update atn_factura 
                set	
                    fact_estado = 'Cerrado',
                    fech_cierre = now()
                where
                    fact_id = ?";
            $sql=$conectar->prepare($sql);
            $sql->bindValue(1, $fact_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* TODO:Cambiar estado del factura al reabrir */
        public function reabrir_factura($fact_id){
            $conectar= parent::conexion();
            parent::set_names();
            $sql="update atn_factura 
                set	
                    fact_estado = 'Abierto'
                where
                    fact_id = ?";
            $sql=$conectar->prepare($sql);
            $sql->bindValue(1, $fact_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* TODO:Actualizar usu_asig con usuario de soporte asignado */
        public function update_factura_asignacion($fact_id,$usu_asig){
            $conectar= parent::conexion();
            parent::set_names();
            $sql="update atn_factura 
                set	
                    usu_asig = ?,
                    fech_asig = now()
                where
                    fact_id = ?";
            $sql=$conectar->prepare($sql);
            $sql->bindValue(1, $usu_asig);
            $sql->bindValue(2, $fact_id);
            $sql->execute();

            /* TODO: Guardar Notificacion en la tabla */
            $sql1="INSERT INTO atn_notificacion (not_id,usu_id,not_mensaje,fact_id,est) VALUES (null,?,'Se le ha asignado el factura Nro : ',?,2)";
            $sql1=$conectar->prepare($sql1);
            $sql1->bindValue(1, $usu_asig);
            $sql1->bindValue(2, $fact_id);
            $sql1->execute();

            return $resultado=$sql->fetchAll();
        }

        /* TODO: Obtener total de facturas */
        public function get_factura_total(){
            $conectar= parent::conexion();
            parent::set_names();
            $sql="SELECT COUNT(*) as TOTAL FROM atn_factura";
            $sql=$conectar->prepare($sql);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* TODO: Total de factura Abiertos */
        public function get_factura_totalabierto(){
            $conectar= parent::conexion();
            parent::set_names();
            $sql="SELECT COUNT(*) as TOTAL FROM atn_factura where fact_estado='Abierto'";
            $sql=$conectar->prepare($sql);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* TODO: Total de factura Cerrados */
        public function get_factura_totalcerrado(){
            $conectar= parent::conexion();
            parent::set_names();
            $sql="SELECT COUNT(*) as TOTAL FROM atn_factura where fact_estado='Cerrado'";
            $sql=$conectar->prepare($sql);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        } 

        /* TODO:Total de factura por categoria */
        public function get_factura_grafico(){
            $conectar= parent::conexion();
            parent::set_names();
            $sql="SELECT atn_categoria.cat_nom as nom,COUNT(*) AS total
                FROM   atn_factura  JOIN  
                    atn_categoria ON atn_factura.cat_id = atn_categoria.cat_id  
                WHERE    
                atn_factura.est = 1
                GROUP BY 
                atn_categoria.cat_nom 
                ORDER BY total DESC";
            $sql=$conectar->prepare($sql);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* TODO: Actualizar valor de estrellas de encuesta */
        public function insert_encuesta($fact_id,$fact_estre,$fact_comment){
            $conectar= parent::conexion();
            parent::set_names();
            $sql="update atn_factura 
                set	
                    fact_estre = ?,
                    fact_coment = ?
                where
                    fact_id = ?";
            $sql=$conectar->prepare($sql);
            $sql->bindValue(1, $fact_estre);
            $sql->bindValue(2, $fact_comment);
            $sql->bindValue(3, $fact_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* TODO: Filtro Avanzado de factura */
        public function filtrar_factura($fact_titulo, $cat_id, $prio_id) {
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "CALL filtrar_factura(?, ?, ?)";
            $sql = $conectar->prepare($sql);
        
            // Ajustar los valores para manejar cadenas vacías y asegurarse de que sean enteros o NULL
            $fact_titulo = !empty($fact_titulo) ? "%".$fact_titulo."%" : NULL;
            $cat_id = !empty($cat_id) ? intval($cat_id) : NULL;
            $prio_id = !empty($prio_id) ? intval($prio_id) : NULL;
        
            $sql->bindValue(1, $fact_titulo, PDO::PARAM_STR);
            $sql->bindValue(2, $cat_id, $cat_id === NULL ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $sql->bindValue(3, $prio_id, $prio_id === NULL ? PDO::PARAM_NULL : PDO::PARAM_INT);
        
            $sql->execute();
            return $resultado = $sql->fetchAll();
        }
        
        
        

        public function get_calendar_all(){
            $conectar= parent::conexion();
            parent::set_names();
            $sql="SELECT 
                    atn_factura.fact_id as id,
                    concat(atn_usuario.usu_nom,' ',atn_usuario.usu_ape) as title,
                    atn_factura.fech_crea as start,
                    CASE
                        WHEN atn_factura.fact_estado = 'Abierto' THEN '#46c35f'
                        WHEN atn_factura.fact_estado = 'Cerrado' THEN '#fa424a'
                        ELSE 'white'
                    END as color
                    FROM
                    atn_factura
                    INNER join atn_usuario on atn_factura.usu_id = atn_usuario.usu_id;";
            $sql=$conectar->prepare($sql);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        public function get_calendar_usu($usu_id){
            $conectar= parent::conexion();
            parent::set_names();
            $sql="SELECT 
                    atn_factura.fact_id as id,
                    concat(atn_usuario.usu_nom,' ',atn_usuario.usu_ape) as title,
                    atn_factura.fech_crea as start,
                    CASE
                        WHEN atn_factura.fact_estado = 'Abierto' THEN '#46c35f'
                        WHEN atn_factura.fact_estado = 'Cerrado' THEN '#fa424a'
                        ELSE 'white'
                    END as color
                    FROM
                    atn_factura
                    INNER join atn_usuario on atn_factura.usu_id = atn_usuario.usu_id
                    WHERE
                    atn_factura.usu_id=?";
            $sql=$conectar->prepare($sql);
            $sql->bindValue(1, $usu_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

    }
?>