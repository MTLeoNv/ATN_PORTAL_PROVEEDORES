<header class="site-header">
<style>
        /* Contenedor principal del header */
        .site-header .container-fluid {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 5px 20px;
        }

        /* Ajustes para el contenido del header */
        .site-header-content-in {
            display: flex;
            align-items: center;
            width: -1%;
        }

        /* Ajustes para la sección mostrada en el header */
        .site-header-shown {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* Dropdown y elementos dentro del header */
        .dropdown.dropdown-typical {
            display: flex;
            align-items: center;
            white-space: nowrap;
            margin-right: auto;
        }

        /* Ajustes específicos para el icono del usuario */
        .font-icon.font-icon-user {
            margin-right: 5px;
            display: inline-flex; 
            align-items: center; 
        }

        @media (min-width: 769px) {
            .font-icon.font-icon-user {
                transform: translateY(-10px);
            }
        }

        /* Ajustes para el nombre del usuario */
        .lblcontactonomx {
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: auto; 
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            margin-left: 10px;
            color: white;
        }

        /* Estilos para dispositivos móviles */
        @media (max-width: 768px) {
            .site-header-content-in {
                justify-content: flex-end;
                width: auto;
            }
            .site-header-shown {
                gap: 10px;
                order: 2;
            }
            .dropdown-typical {
                order: 1;
                margin-right: 15px;
            }
            .dropdown-typical .lblcontactonomx {
                display: none;
            }
            .dropdown-typical:hover .lblcontactonomx {
                display: block;
                position: absolute;
                background: #fa424a;
                padding: 5px 10px;
                border-radius: 5px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                top: 50px;
                left: 50%;
                transform: translateX(-50%);
                white-space: nowrap;
            }
        }

        /* Estilos para dispositivos de escritorio */
        @media (min-width: 769px) {
            .dropdown-typical {
                margin-right: auto;
            }
            .dropdown-typical .lblcontactonomx {
                display: inline-block;
            }
            .site-header-shown {
                margin-left: auto;
            }
        }
    </style>

    <div class="container-fluid">
        <a href="../Home/" class="site-logo">
            <img src="../../public/logo.png" alt="Logo">
        </a>

        <button id="show-hide-sidebar-toggle" class="show-hide-sidebar">
            <span>toggle menu</span>
        </button>

        <button class="hamburger hamburger--htla">
            <span>toggle menu</span>
        </button>

        <div class="site-header-content">
            <div class="site-header-content-in">
                <div class="dropdown dropdown-typical">
                    <a href="#" class="dropdown-toggle no-arr">
                        <span class="font-icon font-icon-user"></span>
                        <span class="lblcontactonomx"><?php echo $_SESSION["usu_nom"] ?> <?php echo $_SESSION["usu_ape"] ?></span>
                    </a>
                </div>
                <div class="site-header-shown">
                    <div class="dropdown dropdown-notification notif">
                        <a href="../MntNotificacion/" class="header-alarm">
                            <i class="font-icon-alarm"></i>
                        </a>
                    </div>
                    <div class="dropdown user-menu">
                        <button class="dropdown-toggle" id="dd-user-menu" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="../../public/<?php echo $_SESSION["rol_id"] ?>.jpg" alt="">
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dd-user-menu">
                            <a class="dropdown-item" href="../MntPerfil/"><span class="font-icon glyphicon glyphicon-user"></span>Perfil</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="../Logout/logout.php"><span class="font-icon glyphicon glyphicon-log-out"></span>Cerrar Sesion</a>
                        </div>
                    </div>
                </div>

                <div class="mobile-menu-right-overlay"></div>

                <input type="hidden" id="user_idx" value="<?php echo $_SESSION["usu_id"] ?>"><!-- ID del Usuario-->
                <input type="hidden" id="rol_idx" value="<?php echo $_SESSION["rol_id"] ?>"><!-- Rol del Usuario-->

                <div class="dropdown dropdown-typical">
                    <a href="#" class="dropdown-toggle no-arr">
                    </a>
                </div>

            </div>
        </div>
    </div>
</header>
