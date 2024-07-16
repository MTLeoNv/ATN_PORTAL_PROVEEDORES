<?php
    /* TODO:Cadena de Conexion */
    require_once("../config/conexion.php");
    /* TODO:Clases Necesarias */
    require_once("../models/Factura.php");
    $factura = new Factura();

    require_once("../models/Usuario.php");
    $usuario = new Usuario();

    require_once("../models/Documento.php");
    $documento = new Documento();

    require_once("../models/Email.php");
    $email = new Email();

    $key="mi_key_secret";
    $cipher="aes-256-cbc";
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));

    /*TODO: opciones del controlador Factura*/
    switch($_GET["op"]){

        /* TODO: Insertar nuevo Factura */
        case "insert":
            $datos=$factura->insert_factura($_POST["usu_id"],$_POST["cat_id"],$_POST["cats_id"],$_POST["fact_titulo"],$_POST["fact_descrip"],$_POST["prio_id"]);
            /* TODO: Obtener el ID del ultimo registro insertado */
            if (is_array($datos)==true and count($datos)>0){
                foreach ($datos as $row){
                    $output["fact_id"] = $row["fact_id"];

                    /* TODO: Validamos si vienen archivos desde la Vista */
                    if (empty($_FILES['files']['name'])){

                    }else{
                        /* TODO:Contar Cantidad de Archivos desde la Vista */
                        $countfiles = count($_FILES['files']['name']);
                        /* TODO: Generamos ruta segun el ID del ultimo registro insertado */
                        $ruta = "../public/document/".$output["fact_id"]."/";
                        $files_arr = array();

                        /* TODO: Preguntamos si la ruta existe, en caso no exista la creamos */
                        if (!file_exists($ruta)) {
                            mkdir($ruta, 0777, true);
                        }

                        /* TODO:Recorremos los archivos, y insertamos tantos detalles como documentos vinieron desde la vista */
                        for ($index = 0; $index < $countfiles; $index++) {
                            $doc1 = $_FILES['files']['tmp_name'][$index];
                            $destino = $ruta.$_FILES['files']['name'][$index];

                            /* TODO: Insertamos Documentos */
                            $documento->insert_documento( $output["fact_id"],$_FILES['files']['name'][$index]);

                            /* TODO: Movemos los archivos hacia la carpeta creada */
                            move_uploaded_file($doc1,$destino);
                        }
                    }
                }
            }
            $email->factura_abierta($datos[0]["fact_id"]);
            echo json_encode($datos);
            break;

        /* TODO: Actualizamos el factura a cerrado y adicionamos una linea adicional */
        case "update":
            $iv_dec = substr(base64_decode($_POST["fact_id"]), 0, openssl_cipher_iv_length($cipher));
            $cifradoSinIV = substr(base64_decode($_POST["fact_id"]), openssl_cipher_iv_length($cipher));
            $decifrado = openssl_decrypt($cifradoSinIV, $cipher, $key, OPENSSL_RAW_DATA, $iv_dec);

            $factura->update_factura($decifrado);
            $factura->insert_facturadetalle_cerrar($decifrado,$_SESSION["usu_id"]);

            $email->factura_cerrada($decifrado);

            echo $decifrado;
            break;

        /* TODO: Reabrimos el factura y adicionamos una linea adicional */
        case "reabrir":
            $factura->reabrir_factura($_POST["fact_id"]);
            $factura->insert_facturadetalle_reabrir($_POST["fact_id"],$_POST["usu_id"]);
            break;

        /* TODO: Asignamos el factura  */
        case "asignar":
            $factura->update_factura_asignacion($_POST["fact_id"],$_POST["usu_asig"]);
            $email->factura_asignada($_POST["fact_id"]);
            echo "1";
            break;

        /* TODO: Listado de facturas segun usuario,formato json para Datatable JS */
        case "listar_x_usu":
            $datos=$factura->listar_factura_x_usu($_POST["usu_id"]);
            $data= Array();
            foreach($datos as $row){
                $sub_array = array();
                $sub_array[] = $row["fact_id"];
                $sub_array[] = $row["cat_nom"];
                $sub_array[] = $row["fact_titulo"];

                $sub_array[] = $row["prio_nom"];

                if ($row["fact_estado"]=="Abierto"){
                    $sub_array[] = '<span class="label label-pill label-success">Abierto</span>';
                }else{
                    $sub_array[] = '<a onClick="CambiarEstado('.$row["fact_id"].')"><span class="label label-pill label-danger">Cerrado</span></a>';
                }

                $sub_array[] = date("d/m/Y H:i:s", strtotime($row["fech_crea"]));

                if($row["fech_asig"]==null){
                    $sub_array[] = '<span class="label label-pill label-default">Sin Asignar</span>';
                }else{
                    $sub_array[] = date("d/m/Y H:i:s", strtotime($row["fech_asig"]));
                }

                if($row["fech_cierre"]==null){
                    $sub_array[] = '<span class="label label-pill label-default">Sin Cerrar</span>';
                }else{
                    $sub_array[] = date("d/m/Y H:i:s", strtotime($row["fech_cierre"]));
                }

                if($row["usu_asig"]==null){
                    $sub_array[] = '<span class="label label-pill label-warning">Sin Asignar</span>';
                }else{
                    $datos1=$usuario->get_usuario_x_id($row["usu_asig"]);
                    foreach($datos1 as $row1){
                        $sub_array[] = '<span class="label label-pill label-success">'. $row1["usu_nom"].'</span>';
                    }
                }

                $cifrado = openssl_encrypt($row["fact_id"], $cipher, $key, OPENSSL_RAW_DATA, $iv);
                $textoCifrado = base64_encode($iv . $cifrado);

                $sub_array[] = '<button type="button" data-ciphertext="'.$textoCifrado.'" id="'.$textoCifrado.'" class="btn btn-inline btn-primary btn-sm ladda-button">Ver</button>';
                $data[] = $sub_array;
            }

            $results = array(
                "sEcho"=>1,
                "iTotalRecords"=>count($data),
                "iTotalDisplayRecords"=>count($data),
                "aaData"=>$data);
            echo json_encode($results);
            break;

        /* TODO: Listado de facturas,formato json para Datatable JS */
        case "listar":
            $datos=$factura->listar_factura();
            $data= Array();
            foreach($datos as $row){
                $sub_array = array();
                $sub_array[] = $row["fact_id"];
                $sub_array[] = $row["cat_nom"];
                $sub_array[] = $row["fact_titulo"];

                $sub_array[] = $row["prio_nom"];

                if ($row["fact_estado"]=="Abierto"){
                    $sub_array[] = '<span class="label label-pill label-success">Abierto</span>';
                }else{
                    $sub_array[] = '<a onClick="CambiarEstado('.$row["fact_id"].')"><span class="label label-pill label-danger">Cerrado</span><a>';
                }

                $sub_array[] = date("d/m/Y H:i:s", strtotime($row["fech_crea"]));

                if($row["fech_asig"]==null){
                    $sub_array[] = '<span class="label label-pill label-default">Sin Asignar</span>';
                }else{
                    $sub_array[] = date("d/m/Y H:i:s", strtotime($row["fech_asig"]));
                }

                if($row["fech_cierre"]==null){
                    $sub_array[] = '<span class="label label-pill label-default">Sin Cerrar</span>';
                }else{
                    $sub_array[] = date("d/m/Y H:i:s", strtotime($row["fech_cierre"]));
                }

                if($row["usu_asig"]==null){
                    $sub_array[] = '<a onClick="asignar('.$row["fact_id"].');"><span class="label label-pill label-warning">Sin Asignar</span></a>';
                }else{
                    $datos1=$usuario->get_usuario_x_id($row["usu_asig"]);
                    foreach($datos1 as $row1){
                        $sub_array[] = '<span class="label label-pill label-success">'. $row1["usu_nom"].'</span>';
                    }
                }

                $sub_array[] = '<button type="button" onClick="ver('.$row["fact_id"].');"  id="'.$row["fact_id"].'" class="btn btn-inline btn-primary btn-sm ladda-button">Ver</button>';
                $data[] = $sub_array;
            }

            $results = array(
                "sEcho"=>1,
                "iTotalRecords"=>count($data),
                "iTotalDisplayRecords"=>count($data),
                "aaData"=>$data);
            echo json_encode($results);
            break;

        /* TODO: Listado de facturas,formato json para Datatable JS, filtro avanzado*/
        case "listar_filtro":
            $datos = $factura->filtrar_factura($_POST["fact_titulo"], $_POST["cat_id"], $_POST["prio_id"]);
            $data = Array();
            foreach($datos as $row){
                $sub_array = array();
                $sub_array[] = $row["fact_id"];
                $sub_array[] = $row["cat_nom"];
                $sub_array[] = $row["fact_titulo"];
                $sub_array[] = $row["prio_nom"];
                if ($row["fact_estado"]=="Abierto"){
                    $sub_array[] = '<span class="label label-pill label-success">Abierto</span>';
                } else {
                    $sub_array[] = '<a onClick="CambiarEstado('.$row["fact_id"].')"><span class="label label-pill label-danger">Cerrado</span><a>';
                }
                $sub_array[] = date("d/m/Y H:i:s", strtotime($row["fech_crea"]));
                if($row["fech_asig"] == null){
                    $sub_array[] = '<span class="label label-pill label-default">Sin Asignar</span>';
                } else {
                    $sub_array[] = date("d/m/Y H:i:s", strtotime($row["fech_asig"]));
                }
                if($row["fech_cierre"] == null){
                    $sub_array[] = '<span class="label label-pill label-default">Sin Cerrar</span>';
                } else {
                    $sub_array[] = date("d/m/Y H:i:s", strtotime($row["fech_cierre"]));
                }
                if($row["usu_asig"] == null){
                    $sub_array[] = '<a onClick="asignar('.$row["fact_id"].');"><span class="label label-pill label-warning">Sin Asignar</span></a>';
                } else {
                    $datos1 = $usuario->get_usuario_x_id($row["usu_asig"]);
                    foreach($datos1 as $row1){
                        $sub_array[] = '<span class="label label-pill label-success">'. $row1["usu_nom"].'</span>';
                    }
                }
                $cifrado = openssl_encrypt($row["fact_id"], $cipher, $key, OPENSSL_RAW_DATA, $iv);
                $textoCifrado = base64_encode($iv . $cifrado);
                $sub_array[] = '<button type="button" data-ciphertext="'.$textoCifrado.'" id="'.$textoCifrado.'" class="btn btn-inline btn-primary btn-sm">Ver</button>';
                $data[] = $sub_array;
            }
            $results = array(
                "sEcho"=>1,
                "iTotalRecords"=>count($data),
                "iTotalDisplayRecords"=>count($data),
                "aaData"=>$data);
            echo json_encode($results);
            break;
        
        

        /* TODO: Formato HTML para mostrar detalle de factura con comentarios */
        case "listardetalle":

            $iv_dec = substr(base64_decode($_POST["fact_id"]), 0, openssl_cipher_iv_length($cipher));
            $cifradoSinIV = substr(base64_decode($_POST["fact_id"]), openssl_cipher_iv_length($cipher));
            $decifrado = openssl_decrypt($cifradoSinIV, $cipher, $key, OPENSSL_RAW_DATA, $iv_dec);

            /* TODO: Listar todo el detalle segun fact_id */
            $datos=$factura->listar_facturadetalle_x_factura($decifrado);
            ?>
                <?php
                    /* TODO: Repetir tantas veces se obtenga de la varible datos$ */
                    foreach($datos as $row){
                        ?>
                            <article class="activity-line-item box-typical">
                                <div class="activity-line-date">
                                    <!-- TODO: Formato de fecha creacion -->
                                    <?php echo date("d/m/Y", strtotime($row["fech_crea"]));?>
                                </div>
                                <header class="activity-line-item-header">
                                    <div class="activity-line-item-user">
                                        <div class="activity-line-item-user-photo">
                                            <a href="#">
                                                <img src="../../public/<?php echo $row['rol_id'] ?>.jpg" alt="">
                                            </a>
                                        </div>
                                        <div class="activity-line-item-user-name"><?php echo $row['usu_nom'].' '.$row['usu_ape'];?></div>
                                        <div class="activity-line-item-user-status">
                                            <!-- TODO: Mostrar perfil del usuario segun rol -->
                                            <?php
                                                if ($row['rol_id']==1){
                                                    echo 'Usuario';
                                                }else{
                                                    echo 'Soporte';
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </header>
                                <div class="activity-line-action-list">
                                    <section class="activity-line-action">
                                        <div class="time"><?php echo date("H:i:s", strtotime($row["fech_crea"]));?></div>
                                        <div class="cont">
                                            <div class="cont-in">
                                                <p>
                                                    <?php echo $row["factd_descrip"];?>
                                                </p>

                                                <br>

                                                <!-- TODO: Mostrar documentos adjunto en el detalle de factura -->
                                                <?php
                                                    $datos_det=$documento->get_documento_detalle_x_facturad($row["factd_id"]);
                                                    if(is_array($datos_det)==true and count($datos_det)>0){
                                                        ?>
                                                            <p><strong>Documentos Adicionales</strong></p>

                                                            <p>
                                                            <table class="table table-bordered table-striped table-vcenter js-dataTable-full">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width: 60%;"> Nombre</th>
                                                                        <th style="width: 40%;"></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                        <!-- TODO: Mostrar tantos documentos tenga el factura detalle -->
                                                                        <?php
                                                                            foreach ($datos_det as $row_det){ 
                                                                        ?>
                                                                            <tr>
                                                                                <td><?php echo $row_det["det_nom"]; ?></td>
                                                                                <td>
                                                                                    <a href="../../public/document_detalle/<?php echo $row_det["factd_id"]; ?>/<?php echo $row_det["det_nom"]; ?>" target="_blank" class="btn btn-inline btn-primary btn-sm">Ver</a>
                                                                                </td>
                                                                            </tr>
                                                                        <?php
                                                                            }
                                                                        ?>
                                                                </tbody>
                                                            </table>

                                                            </p>
                                                        <?php
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                    </section>
                                </div>
                            </article>
                        <?php
                    }
                ?>
            <?php
            break;
                    

        /* TODO: Mostrar informacion de factura en formato JSON para la vista */
        case "mostrar";
            $iv_dec = substr(base64_decode($_POST["fact_id"]), 0, openssl_cipher_iv_length($cipher));
            $cifradoSinIV = substr(base64_decode($_POST["fact_id"]), openssl_cipher_iv_length($cipher));
            $decifrado = openssl_decrypt($cifradoSinIV, $cipher, $key, OPENSSL_RAW_DATA, $iv_dec);

            $datos=$factura->listar_factura_x_id($decifrado);
            if(is_array($datos)==true and count($datos)>0){
                foreach($datos as $row)
                {
                    $output["fact_id"] = $row["fact_id"];
                    $output["usu_id"] = $row["usu_id"];
                    $output["cat_id"] = $row["cat_id"];

                    $output["fact_titulo"] = $row["fact_titulo"];
                    $output["fact_descrip"] = $row["fact_descrip"];

                    if ($row["fact_estado"]=="Abierto"){
                        $output["fact_estado"] = '<span class="label label-pill label-success">Abierto</span>';
                    }else{
                        $output["fact_estado"] = '<span class="label label-pill label-danger">Cerrado</span>';
                    }

                    $output["fact_estado_texto"] = $row["fact_estado"];

                    $output["fech_crea"] = date("d/m/Y H:i:s", strtotime($row["fech_crea"]));
                    $output["fech_cierre"] = date("d/m/Y H:i:s", strtotime($row["fech_cierre"]));
                    $output["usu_nom"] = $row["usu_nom"];
                    $output["usu_ape"] = $row["usu_ape"];
                    $output["cat_nom"] = $row["cat_nom"];
                    $output["cats_nom"] = $row["cats_nom"];
                    $output["fact_estre"] = $row["fact_estre"];
                    $output["fact_coment"] = $row["fact_coment"];
                    $output["prio_nom"] = $row["prio_nom"];
                }
                echo json_encode($output);
            }
            break;

        case "mostrar_noencry";
            $datos=$factura->listar_factura_x_id($_POST["fact_id"]);
            if(is_array($datos)==true and count($datos)>0){
                foreach($datos as $row)
                {
                    $output["fact_id"] = $row["fact_id"];
                    $output["usu_id"] = $row["usu_id"];
                    $output["cat_id"] = $row["cat_id"];

                    $output["fact_titulo"] = $row["fact_titulo"];
                    $output["fact_descrip"] = $row["fact_descrip"];

                    if ($row["fact_estado"]=="Abierto"){
                        $output["fact_estado"] = '<span class="label label-pill label-success">Abierto</span>';
                    }else{
                        $output["fact_estado"] = '<span class="label label-pill label-danger">Cerrado</span>';
                    }

                    $output["fact_estado_texto"] = $row["fact_estado"];

                    $output["fech_crea"] = date("d/m/Y H:i:s", strtotime($row["fech_crea"]));
                    $output["fech_cierre"] = date("d/m/Y H:i:s", strtotime($row["fech_cierre"]));
                    $output["usu_nom"] = $row["usu_nom"];
                    $output["usu_ape"] = $row["usu_ape"];
                    $output["cat_nom"] = $row["cat_nom"];
                    $output["cats_nom"] = $row["cats_nom"];
                    $output["fact_estre"] = $row["fact_estre"];
                    $output["fact_coment"] = $row["fact_coment"];
                    $output["prio_nom"] = $row["prio_nom"];
                }
                echo json_encode($output);
            }
            break;

        case "insertdetalle":
            $iv_dec = substr(base64_decode($_POST["fact_id"]), 0, openssl_cipher_iv_length($cipher));
            $cifradoSinIV = substr(base64_decode($_POST["fact_id"]), openssl_cipher_iv_length($cipher));
            $decifrado = openssl_decrypt($cifradoSinIV, $cipher, $key, OPENSSL_RAW_DATA, $iv_dec);

            $datos=$factura->insert_facturadetalle($decifrado,$_POST["usu_id"],$_POST["factd_descrip"]);
            if (is_array($datos)==true and count($datos)>0){
                foreach ($datos as $row){
                    /* TODO: Obtener tikd_id de $datos */
                    $output["factd_id"] = $row["factd_id"];
                    /* TODO: Consultamos si vienen archivos desde la vista */
                    if (empty($_FILES['files']['name'])){

                    }else{
                        /* TODO:Contar registros */
                        $countfiles = count($_FILES['files']['name']);
                        /* TODO:Ruta de los documentos */
                        $ruta = "../public/document_detalle/".$output["factd_id"]."/";
                        /* TODO: Array de archivos */
                        $files_arr = array();
                        /* TODO: Consultar si la ruta existe en caso no exista la creamos */
                        if (!file_exists($ruta)) {
                            mkdir($ruta, 0777, true);
                        }

                        /* TODO:recorrer todos los registros */
                        for ($index = 0; $index < $countfiles; $index++) {
                            $doc1 = $_FILES['files']['tmp_name'][$index];
                            $destino = $ruta.$_FILES['files']['name'][$index];

                            $documento->insert_documento_detalle($output["factd_id"],$_FILES['files']['name'][$index]);

                            move_uploaded_file($doc1,$destino);
                        }
                    }
                }
            }
            echo json_encode($datos);
            break;

        /* TODO: Total de factura para vista de soporte */
        case "total";
            $datos=$factura->get_factura_total();  
            if(is_array($datos)==true and count($datos)>0){
                foreach($datos as $row)
                {
                    $output["TOTAL"] = $row["TOTAL"];
                }
                echo json_encode($output);
            }
            break;

        /* TODO: Total de factura Abierto para vista de soporte */
        case "totalabierto";
            $datos=$factura->get_factura_totalabierto();  
            if(is_array($datos)==true and count($datos)>0){
                foreach($datos as $row)
                {
                    $output["TOTAL"] = $row["TOTAL"];
                }
                echo json_encode($output);
            }
            break;

        /* TODO: Total de factura Cerrados para vista de soporte */
        case "totalcerrado";
            $datos=$factura->get_factura_totalcerrado();  
            if(is_array($datos)==true and count($datos)>0){
                foreach($datos as $row)
                {
                    $output["TOTAL"] = $row["TOTAL"];
                }
                echo json_encode($output);
            }
            break;

        /* TODO: Formato Json para grafico de soporte */
        case "grafico";
            $datos=$factura->get_factura_grafico();  
            echo json_encode($datos);
            break;

        /* TODO: Insertar valor de encuesta,estrellas y comentarios */
        case "encuesta":
            $factura->insert_encuesta($_POST["fact_id"],$_POST["fact_estre"],$_POST["fact_coment"]);
            break;

        case "all_calendar":
            $datos=$factura->get_calendar_all();
            echo json_encode($datos);
            break;

        case "usu_calendar":
            $datos=$factura->get_calendar_usu($_POST["usu_id"]);
            echo json_encode($datos);
            break;

    }
?>