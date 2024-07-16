var tabla;
var usu_id = $('#user_idx').val();
var rol_id = $('#rol_idx').val();

function init() {
    $("#factura_form").on("submit", function (e) {
        guardar(e);
    });
}

$(document).ready(function () {
    console.log("Document ready");

    // Llenar Combo Categoria
    $.post("../../controller/categoria.php?op=combo", function (data) {
        console.log("Categoria combo data:", data);
        $('#cat_id').html(data);
    });

    // Llenar Combo Prioridad
    $.post("../../controller/prioridad.php?op=combo", function (data) {
        console.log("Prioridad combo data:", data);
        $('#prio_id').html(data);
    });

    // Llenar Combo usuario asignar
    $.post("../../controller/usuario.php?op=combo", function (data) {
        console.log("Usuario combo data:", data);
        $('#usu_asig').html(data);
    });

    // Rol si es 1 entonces es usuario
    if (rol_id == 1) {
        console.log("Rol ID es 1, es usuario");
        $('#viewuser').hide();

        tabla = $('#factura_data').DataTable({
            "aProcessing": true,
            "aServerSide": true,
            dom: 'Bfrtip',
            "searching": true,
            lengthChange: false,
            colReorder: true,
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
            ],
            "ajax": {
                url: '../../controller/factura.php?op=listar_x_usu',
                type: "post",
                dataType: "json",
                data: { usu_id: usu_id },
                error: function (e) {
                    console.log("Error en listar_x_usu:", e.responseText);
                }
            },
            "ordering": true,
            "order": [[0, 'asc']], // Ordenar por la primera columna, asumiendo que es el número de factura
            "bDestroy": true,
            "responsive": true,
            "bInfo": true,
            "iDisplayLength": 10,
            "autoWidth": false,
            "language": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            }
        });
        
    } else {
        console.log("Rol ID no es 1, es soporte");
        // Filtro avanzado en caso de ser soporte
        var fact_titulo = $('#fact_titulo').val();
        var cat_id = $('#cat_id').val();
        var prio_id = $('#prio_id').val();

        listardatatable(fact_titulo, cat_id, prio_id);
    }
});

// Link para poder ver el detalle de factura en otra ventana
$(document).on("click", ".btn-inline", function () {
    const ciphertext = $(this).data("ciphertext");
    console.log("Detalle factura ciphertext:", ciphertext);
    window.open('http://localhost/ATN_PORTAL_PROVEEDORES/view/DetalleFactura/?ID=' + ciphertext);
});

// Mostrar datos antes de asignar
function asignar(fact_id){
    $.post("../../controller/factura.php?op=mostrar_noencry", {fact_id : fact_id}, function (data) {
        data = JSON.parse(data);
        $('#fact_id').val(data.fact_id);

        $('#mdltitulo').html('Asignar Agente');
        $("#modalasignar").modal('show');
    });
}

// Guardar asignacion de usuario de soporte
function guardar(e) {
    e.preventDefault();
    var formData = new FormData($("#factura_form")[0]);
    console.log("Formulario de asignación de usuario:", formData);
    $.ajax({
        url: "../../controller/factura.php?op=asignar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (datos) {
            var fact_id = $('#fact_id').val();
            console.log("Asignación exitosa. Fact ID:", fact_id);
            // Enviar Email de alerta de asignacion
            $.post("../../controller/email.php?op=factura_asignada", { fact_id: fact_id }, function (data) {
                console.log("Email de asignación enviado. Respuesta:", data);
            });

            // Alerta de confirmacion
            swal("Correcto!", "Asignado Correctamente", "success");

            // Ocultar Modal
            $("#modalasignar").modal('hide');
            // Recargar Datatable JS
            $('#factura_data').DataTable().ajax.reload();
        },
        error: function (e) {
            console.log("Error en asignar usuario:", e);
        }
    });
}

// Reabrir factura
function CambiarEstado(fact_id) {
    console.log("Cambiar estado de factura:", fact_id);
    swal({
        title: "ATNPortal",
        text: "Esta seguro de reabrir la factura?",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-warning",
        confirmButtonText: "Si",
        cancelButtonText: "No",
        closeOnConfirm: false
    },
        function (isConfirm) {
            if (isConfirm) {
                // Enviar actualizacion de estado
                $.post("../../controller/factura.php?op=reabrir", { fact_id: fact_id, usu_id: usu_id }, function (data) {
                    console.log("Estado de factura cambiado. Respuesta:", data);
                });

                // Recargar datatable js
                $('#factura_data').DataTable().ajax.reload();

                // Mensaje de Confirmacion
                swal({
                    title: "ATNPortal!",
                    text: "Factura Abierta",
                    type: "success",
                    confirmButtonClass: "btn-success"
                });
            }
        });
}

// Filtro avanzado
$(document).on("click", "#btnfiltrar", function () {
    limpiar();

    var fact_titulo = $('#fact_titulo').val();
    var cat_id = $('#cat_id').val();
    var prio_id = $('#prio_id').val();
    console.log("Filtro avanzado aplicado:", { fact_titulo, cat_id, prio_id });
    listardatatable(fact_titulo, cat_id, prio_id);
});

// Restaurar Datatable js y limpiar
$(document).on("click", "#btntodo", function () {
    limpiar();

    $('#fact_titulo').val('');
    $('#cat_id').val('').trigger('change');
    $('#prio_id').val('').trigger('change');
    console.log("Filtro avanzado limpiado.");
    listardatatable('', '', '');
});

// Listar datatable con filtro avanzado
function listardatatable(fact_titulo, cat_id, prio_id) {
    console.log("Listar datatable con filtros:", { fact_titulo, cat_id, prio_id });
    tabla = $('#factura_data').DataTable({
        "aProcessing": true,
        "aServerSide": true,
        dom: 'Bfrtip',
        "searching": true,
        lengthChange: false,
        colReorder: true,
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ],
        "ajax": {
            url: '../../controller/factura.php?op=listar_filtro',
            type: "post",
            dataType: "json",
            data: { fact_titulo: fact_titulo, cat_id: cat_id, prio_id: prio_id },
            error: function (e) {
                console.log("Error en listar_filtro:", e.responseText);
            }
        },
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 10,
        "autoWidth": false,
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }
    });
}


/* TODO: Limpiamos restructurando el html del datatable js */
function limpiar(){
    $('#table').html(
        "<table id='factura_data' class='table table-bordered table-striped table-vcenter js-dataTable-full'>"+
            "<thead>"+
                "<tr>"+
                    "<th style='width: 5%;'>Nro.Factura</th>"+
                    "<th style='width: 15%;'>Categoria</th>"+
                    "<th class='d-none d-sm-table-cell' style='width: 30%;'>Titulo</th>"+
                    "<th class='d-none d-sm-table-cell' style='width: 5%;'>Prioridad</th>"+
                    "<th class='d-none d-sm-table-cell' style='width: 5%;'>Estado</th>"+
                    "<th class='d-none d-sm-table-cell' style='width: 10%;'>Fecha Creación</th>"+
                    "<th class='d-none d-sm-table-cell' style='width: 10%;'>Fecha Asignación</th>"+
                    "<th class='d-none d-sm-table-cell' style='width: 10%;'>Fecha Cierre</th>"+
                    "<th class='d-none d-sm-table-cell' style='width: 10%;'>Soporte</th>"+
                    "<th class='text-center' style='width: 5%;'></th>"+
                "</tr>"+
            "</thead>"+
            "<tbody>"+

            "</tbody>"+
        "</table>"
    );
}

init();