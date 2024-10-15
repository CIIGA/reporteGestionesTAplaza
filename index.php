<?php
session_start();
if ($_SESSION['user']) {
    
$plaza = 'implementtaTecateA';
require "cnx/cnx.php";
$cnx = conexion($plaza);


?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestiones</title>
    <link rel="icon" href="img/icon.png">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <!-- DataTables CSS y JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.2.2/css/fixedColumns.dataTables.min.css">
    <script src="https://cdn.datatables.net/fixedcolumns/4.2.2/js/dataTables.fixedColumns.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Incluye CSS de Select2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <!-- Incluye JS de Select2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">




    <style>
        /* Personaliza la posición y el estilo de Toastr */
        #toast-container>.toast {
            background-color: #dc3545;
            color: #ffffff;
        }

        body {
            font-family: sans-serif;
            font-style: normal;
            font-weight: normal;
            /* font-size: 12px; */
            /* Tamaño de fuente más pequeño */
            width: 100%;
            height: 100%;
            margin-top: -2%;
            padding-left: 30px;
            padding-right: 30px;
            background-repeat: repeat;
            background-size: 100%;
            background-attachment: fixed;
        }

        .dataTables_wrapper {
            overflow-x: auto;
            /* Hacer que el contenedor se desborde horizontalmente */
        }

        .dataTables_paginate {
            margin-top: 10px;
            /* Agregar espacio entre la tabla y la paginación */
        }

        .table-responsive {
            max-width: 100%;
            /* Para que el scroll no afecte a otros elementos */
            overflow-x: auto;
            /* Scroll horizontal solo en la tabla */
        }





        .tabla_gestiones {
            font-size: 10px !important;
        }

        #fotos {
            display: block;
            width: 100%;

            margin: auto;
            overflow-y: scroll;
            border: 1px solid #ccc;
            /* Borde opcional para mejor visualización */
        }
    </style>

    <style>
        /* Diseño para los comentarios */
        .comentario-municipio,
        .comentario-gestor {
            display: flex;
            align-items: flex-start;
            /* Alinea hacia la parte superior */
            margin-bottom: 10px;
        }

        .comentario-municipio {
            justify-content: flex-end;
        }

        .comentario-gestor {
            justify-content: flex-start;
        }

        .comentario-bubble {
            border-radius: 20px;
            padding: 10px 15px;
            max-width: 70%;
            font-size: 0.9em;
            line-height: 1.1em;
            /* Reducido para más cercanía entre texto y fecha */
            position: relative;
        }

        /* Mensaje del Municipio (lado derecho) */
        .comentario-municipio .comentario-bubble {
            background-color: #e0f7fa;
            color: #00796b;
            text-align: right;
        }

        /* Mensaje del Gestor (lado izquierdo) */
        .comentario-gestor .comentario-bubble {
            background-color: #f1f1f1;
            color: #000;
            text-align: left;
        }

        /* Pequeño estilo para la fecha y hora debajo del comentario */
        .comentario-fecha {
            font-size: 0.75em;
            margin-top: 1px;
            /* Reducido para estar más cerca del comentario */
            color: #888;
            display: block;
        }

        /* Estilo mejorado para el textarea */
        .swal2-textarea {
            border-radius: 10px;
            font-size: 1em;
            width: calc(100% - 20px);
            /* Asegura que ocupe todo el ancho con un pequeño margen */
            height: 20px;
            /* Reducido a dos líneas */
            padding: 8px;
            box-sizing: border-box;
            resize: none;
            /* Evita que el usuario pueda redimensionar */
            margin: 10px auto;
            /* Centra el textarea horizontalmente */
            display: block;
            /* Asegura que se muestre como bloque */
        }
    </style>

</head>

<body>

    <br>
    <nav class="navbar navbar-expand-lg navbar-light">
        <a href="#"><img src="img/logoImplementtaHorizontal.png" width="250" height="82" alt=""></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <a class="nav-item nav-link" href="logout.php"> Salir <i class="fas fa-sign-out-alt"></i></a>
            </ul>
        </div>
    </nav>

    <div class="text-center">
        <h5 style="text-shadow: 0px 0px 2px #717171;">
            <img src="https://img.icons8.com/external-smashingstocks-flat-smashing-stocks/40/external-Feedback-testing-services-smashingstocks-flat-smashing-stocks-5.png" alt="external-Feedback-testing-services-smashingstocks-flat-smashing-stocks-5" />
            Reporte de Gestiones U.N Tecate Agua
        </h5>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div id="tblreporte_wrapper" class="container-fluid tabla_gestiones">
                <div class="d-flex align-items-center justify-content-center mb-3">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="rangof" class="form-label">Fechas:</label>
                            <input type="text" class="form-control form-control-sm" id="rangof" value="" />
                        </div>
                    </div>
                    &nbsp;&nbsp;&nbsp;

                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="cuenta" class="form-label">Cuenta:</label>
                            <input type="text" class="form-control form-control-sm" id="cuenta" value="" />
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <button class="btn btn-primary btn-sm" id="buscar">Buscar</button>
                    </div>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <div class="form-group mb-0">
                        <button class="btn btn-outline-success btn-sm" id="reporte">
                            <img width="20" height="20" src="https://img.icons8.com/color/48/ms-excel.png" alt="ms-excel" /> Descargar Reporte
                        </button>
                    </div>
                </div>

                <div class="response_table hidden-box">
                    <div class="table-responsive">
                        <table id="tblreporte" class="table table-bordered nowrap table-sm table-hover" style="width: 100%;" data-page-length='10'>
                            <thead>
                                <tr>
                                    <th>Opciones</th>
                                    <th>Cuenta</th>
                                    <th>folio</th>
                                    <th>Propietario</th>
                                    <th>Domicilio</th>
                                    <th>Gestor</th>
                                    <th>Serie Medidor</th>
                                    <th>Fecha Captura</th>
                                    <th>Lectura</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <h6 class="text-shadow"><img src="https://img.icons8.com/fluency/35/visible.png" alt=""> Visualización de Fotos</h6>
            <div id="fotos" style="display: block; width: 100%; height: 70vh; margin: auto;">
                <!-- Las fotos se cargarán aquí dinámicamente -->
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/reporte.js?v=3"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid mb-5">
            <span class="navbar-text" style="font-size:12px;font-weight:normal;color: #7a7a7a;">
                Implementta Web <i class="far fa-registered"></i><br>
                Estrategas de México <i class="far fa-registered"></i><br>
                Centro de Inteligencia Informática y Geografía Aplicada CIIGA
                <hr style="width:105%;border-color:#7a7a7a;">
                Created and designed by <i class="far fa-copyright"></i> <?php echo date('Y') ?> Estrategas de México<br>
            </span>
            <span class="navbar-text" style="font-size:12px;font-weight:normal;color: #7a7a7a;">
                Contacto:<br>
                <i class="fas fa-phone-alt"></i> Red: 187<br>
                <i class="fas fa-phone-alt"></i> 66 4120 1451<br>
                <i class="fas fa-envelope"></i> sistemas@estrategas.mx<br>
            </span>
            <ul class="navbar-nav ml-auto">
                <form class="form-inline my-2 my-lg-0">
                    <a href="#"><img src="img/logoImplementta.png" width="155" height="150" alt=""></a>
                    <a href="http://estrategas.mx/" target="_blank"><img src="img/logoTop.png" width="200" height="85" alt=""></a>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </form>
            </ul>
        </div>
    </nav>


</body>

<!-- Modal en tu HTML -->
<div class="modal fade" id="modalGestion" tabindex="-1" aria-labelledby="modalGestionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalGestionLabel">Actualizar Gestión</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formGestion">

                    <input type="hidden" class="form-control" id="idGestion" readonly>
                    <input type="hidden" class="form-control" id="tabla" readonly>
                    <input type="hidden" class="form-control" id="idRegistro" readonly>

                    <div class="mb-3">
                        <label for="fechaGestion" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="fechaGestion">
                    </div>
                    <div class="mb-3">
                        <label for="selectGestores" class="form-label">Gestor</label>
                        <select class="form-select" id="selectGestores"></select>
                    </div>
                    <div class="mb-3">
                        <label for="selectTareas" class="form-label">Tarea</label>
                        <select class="form-select" id="selectTareas"></select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" id="btnActualizarGestion" class="btn btn-primary">Actualizar Gestión</button>

            </div>
        </div>
    </div>
</div>

</html>
    <?php
}else{
    echo '<meta http-equiv="refresh" content="1,url=logout.php">';
}