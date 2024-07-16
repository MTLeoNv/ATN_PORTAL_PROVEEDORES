<?php
    /* TODO: Rol 1 es de Usuario */
    if ($_SESSION["rol_id"]==1){
        ?>
            <nav class="side-menu">
                <ul class="side-menu-list">
                    <li class="blue-dirty">
                        <a href="..\Home\">
                            <span class="glyphicon glyphicon-home"></span>
                            <span class="lbl">Inicio</span>
                        </a>
                    </li>

                    <li class="blue-dirty">
                        <a href="..\NuevaFactura\">
                            <span class="glyphicon glyphicon-file"></span>
                            <span class="lbl">Nueva Factura</span>
                        </a>
                    </li>

                    <li class="blue-dirty">
                        <a href="..\ConsultarFactura\">
                            <span class="glyphicon glyphicon-folder-open"></span>
                            <span class="lbl">Consultar Factura</span>
                        </a>
                    </li>
                </ul>
            </nav>
        <?php
    }else{
        ?>
            <nav class="side-menu">
                <ul class="side-menu-list">
                    <li class="blue-dirty">
                        <a href="..\Home\">
                            <span class="glyphicon glyphicon-home"></span>
                            <span class="lbl">Inicio</span>
                        </a>
                    </li>

                    <li class="blue-dirty">
                        <a href="..\NuevaFactura\">
                            <span class="glyphicon glyphicon-file"></span>
                            <span class="lbl">Nueva Factura</span>
                        </a>
                    </li>

                    <li class="blue-dirty">
                        <a href="..\MntUsuario\">
                            <span class="glyphicon glyphicon-user"></span>
                            <span class="lbl">Mant. Usuario</span>
                        </a>
                    </li>

                    <li class="blue-dirty">
                        <a href="..\MntPrioridad\">
                            <span class="glyphicon glyphicon-sort-by-attributes-alt"></span>
                            <span class="lbl">Mant. Prioridad</span>
                        </a>
                    </li>

                    <li class="blue-dirty">
                        <a href="..\MntCategoria\">
                            <span class="glyphicon glyphicon-align-left"></span>
                            <span class="lbl">Mant. Categoria</span>
                        </a>
                    </li>

                    <li class="blue-dirty">
                        <a href="..\MntSubCategoria\">
                            <span class="glyphicon glyphicon-indent-left"></span>
                            <span class="lbl">Mant. Sub Categoria</span>
                        </a>
                    </li>

                    <li class="blue-dirty">
                        <a href="..\ConsultarFactura\">
                            <span class="glyphicon glyphicon-folder-open"></span>
                            <span class="lbl">Consultar Factura</span>
                        </a>
                    </li>
                </ul>
            </nav>
        <?php
    }
?>
