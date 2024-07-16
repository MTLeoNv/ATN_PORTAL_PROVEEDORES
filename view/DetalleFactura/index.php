<?php
require_once("../../config/conexion.php");
if (isset($_SESSION["usu_id"])) {

?>
  <!DOCTYPE html>
  
  <html>
  <?php require_once("../MainHead/head.php"); ?>
  <title>Detalle Factura</title>
  </head>

  <body class="with-side-menu">

    <?php require_once("../MainHeader/header.php"); ?>

    <div class="mobile-menu-left-overlay"></div>

    <?php require_once("../MainNav/nav.php"); ?>

    <!-- Contenido -->
    <div class="page-content">
      <div class="container-fluid">

        <header class="section-header">
          <div class="tbl">
            <div class="tbl-row">
              <div class="tbl-cell">
                <h3 id="lblnomidfactura">Detalle Factura - 1</h3>
                <div id="lblestado"></div>
                <span class="label label-pill label-primary" id="lblnomusuario"></span>
                <span class="label label-pill label-default" id="lblfechcrea"></span>
                <ol class="breadcrumb breadcrumb-simple">
                  <li><a href="../Home/">Home</a></li>
                  <li class="active">Detalle Factura</li>
                </ol>
              </div>
            </div>
          </div>
        </header>

        <div class="box-typical box-typical-padding">
          <div class="row">

              <div class="col-lg-12">
                <fieldset class="form-group">
                  <label class="form-label semibold" for="fact_titulo">Titulo</label>
                  <input type="text" class="form-control" id="fact_titulo" name="fact_titulo" readonly>
                </fieldset>
              </div>

              <div class="col-lg-4">
                <fieldset class="form-group">
                  <label class="form-label semibold" for="cat_nom">Categoria</label>
                  <input type="text" class="form-control" id="cat_nom" name="cat_nom" readonly>
                </fieldset>
              </div>

              <div class="col-lg-4">
                <fieldset class="form-group">
                  <label class="form-label semibold" for="cat_nom">SubCategoria</label>
                  <input type="text" class="form-control" id="cats_nom" name="cats_nom" readonly>
                </fieldset>
              </div>

              <div class="col-lg-4">
                <fieldset class="form-group">
                  <label class="form-label semibold" for="cat_nom">Prioridad</label>
                  <input type="text" class="form-control" id="prio_nom" name="prio_nom" readonly>
                </fieldset>
              </div>

              <div class="col-lg-12">
                <fieldset class="form-group">
                      <label class="form-label semibold" for="fact_titulo">Documentos Adicionales</label>
                      <table id="documentos_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                          <thead>
                              <tr>
                                  <th style="width: 90%;">Nombre</th>
                                  <th class="text-center" style="width: 10%;">Acción</th>
                              </tr>
                          </thead>
                          <tbody>
                              <!-- Aquí se cargarán dinámicamente los datos -->
                          </tbody>
                      </table>
                  </fieldset>
              </div>


              <div class="col-lg-12">
                <fieldset class="form-group">
                  <label class="form-label semibold" for="factd_descripusu">Descripción</label>
                  <div class="summernote-theme-1">
                    <textarea id="factd_descripusu" name="factd_descripusu" class="summernote" name="name"></textarea>
                  </div>

                </fieldset>
              </div>

          </div>
        </div>

        <section class="activity-line" id="lbldetalle">

        </section>

        <div class="box-typical box-typical-padding" id="pnldetalle">
          <p>
            Ingrese su duda o consulta
          </p>
          <div class="row">
              <div class="col-lg-12">
                <fieldset class="form-group">
                  <label class="form-label semibold" for="factd_descrip">Descripción</label>
                  <div class="summernote-theme-1">
                    <textarea id="factd_descrip" name="factd_descrip" class="summernote" name="name"></textarea>
                  </div>
                </fieldset>
              </div>

              <!-- TODO: Agregar archivos adjuntos -->
              <div class="col-lg-12">
							<fieldset class="form-group">
								<label class="form-label semibold" for="fileElem">Documentos Adicionales</label>
								<div class="custom-file">
									<input type="file" name="fileElem" id="fileElem" class="custom-file-input" multiple>
									<label class="custom-file-label" for="fileElem">Elegir archivos</label>
								</div>
							</fieldset>
						</div>

              <div class="col-lg-12">
                <button type="button" id="btnenviar" class="btn btn-rounded btn-inline btn-primary">Enviar</button>
                <button type="button" id="btncerrarfactura" class="btn btn-rounded btn-inline btn-warning">Cerrar Factura</button>
              </div>
          </div>
			  </div>

      </div>
    </div>
    <!-- Contenido -->

    <?php require_once("../MainJs/js.php"); ?>

    <script type="text/javascript" src="detallefactura.js"></script>

    <script type="text/javascript" src="../notificacion.js"></script>

  </body>

  </html>
<?php
} else {
  header("Location:" . Conectar::ruta() . "index.php");
}
?>