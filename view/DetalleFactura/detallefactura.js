function init() {}

$(document).ready(function() {
    const url = window.location.href;
    const params = new URLSearchParams(new URL(url).search);
    const fact_id = params.get("ID");
    const decoded_id = decodeURIComponent(fact_id);
    const id = decoded_id.replace(/\s/g, '+');

    mostraryvalidar(id);

    $('#factd_descrip').summernote({
        height: 150,
        lang: "es-ES",
        popover: {
            image: [],
            link: [],
            air: []
        },
        callbacks: {
            onImageUpload: function(image) {
                console.log("Image detect...");
                myimagetreat(image[0]);
            },
            onPaste: function(e) {
                console.log("Text detect...");
            }
        },
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']]
        ]
    });

    $('#factd_descripusu').summernote({
        height: 150,
        lang: "es-ES",
        popover: {
            image: [],
            link: [],
            air: []
        },
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']]
        ]
    });

    $('#factd_descripusu').summernote('disable');

    $('#documentos_data').dataTable({
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
            url: '../../controller/documento.php?op=listar',
            type: "post",
            data: { fact_id: id },
            dataType: "json",
            error: function(e) {
                console.log(e.responseText);
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
            "sInfoPostFix": "",
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
    }).DataTable();

    document.querySelectorAll('.custom-file-input').forEach(function(input) {
        input.addEventListener('change', function(e) {
            var fileName = e.target.files.length > 1
                ? e.target.files.length + ' archivos seleccionados'
                : e.target.value.split('\\').pop();
            var nextSibling = e.target.nextElementSibling;
            nextSibling.innerText = fileName;
        });
    });

    $(document).on("click", "#btnenviar", function() {
        const url = window.location.href;
        const params = new URLSearchParams(new URL(url).search);
        const fact_id = params.get("ID");
        const decoded_id = decodeURIComponent(fact_id);
        const id = decoded_id.replace(/\s/g, '+');

        var usu_id = $('#user_idx').val();
        var factd_descrip = $('#factd_descrip').val();

        if ($('#factd_descrip').summernote('isEmpty')) {
            swal("Advertencia!", "Falta Descripción", "warning");
        } else {
            var formData = new FormData();
            formData.append('fact_id', id);
            formData.append('usu_id', usu_id);
            formData.append('factd_descrip', factd_descrip);
            var totalfiles = $('#fileElem')[0].files.length;
            for (var i = 0; i < totalfiles; i++) {
                formData.append("files[]", $('#fileElem')[0].files[i]);
            }

            $.ajax({
                url: "../../controller/factura.php?op=insertdetalle",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(data) {
                    console.log(data);

                    mostraryvalidar(id);

                    $('#fileElem').val('');
                    $('.custom-file-label').html('Elegir archivos');
                    $('#factd_descrip').summernote('reset');
                    swal("Correcto!", "Registrado Correctamente", "success");

                    $.unblockUI();
                },
                beforeSend: function() {
                    $.blockUI({
                        overlayCSS: {
                            background: 'rgba(142, 159, 167, 0.3)',
                            opacity: 1,
                            cursor: 'wait'
                        },
                        css: {
                            width: 'auto',
                            top: '45%',
                            left: '45%'
                        },
                        message: '<div class="blockui-default-message">Espere...</div>',
                        blockMsgClass: 'block-msg-message-loader'
                    });
                },
            });
        }
    });

    $(document).on("click", "#btncerrarfactura", function() {
        swal({
            title: "ATN Portal",
            text: "Esta seguro de cerrar la factura?",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-warning",
            confirmButtonText: "Si",
            cancelButtonText: "No",
            closeOnConfirm: false
        }, function(isConfirm) {
            if (isConfirm) {
                const url = window.location.href;
                const params = new URLSearchParams(new URL(url).search);
                const fact_id = params.get("ID");
                const decoded_id = decodeURIComponent(fact_id);
                const id = decoded_id.replace(/\s/g, '+');

                $.ajax({
                    url: "../../controller/factura.php?op=update",
                    type: "POST",
                    data: { fact_id: id },
                    success: function(datos) {
                        console.log(datos);

                        mostraryvalidar(id);

                        swal("Correcto!", "Factura Cerrada", "success");

                        $.unblockUI();
                    },
                    beforeSend: function() {
                        $.blockUI({
                            overlayCSS: {
                                background: 'rgba(142, 159, 167, 0.3)',
                                opacity: 1,
                                cursor: 'wait'
                            },
                            css: {
                                width: 'auto',
                                top: '45%',
                                left: '45%'
                            },
                            message: '<div class="blockui-default-message">Espere...</div>',
                            blockMsgClass: 'block-msg-message-loader'
                        });
                    },
                });

                swal.close();
            }
        });
    });
});

function mostraryvalidar(id) {
    console.log("Fetching details for factura ID: ", id);

    $.post("../../controller/factura.php?op=listardetalle", { fact_id: id }, function(data) {
        $('#lbldetalle').html(data);
        console.log("Detalle de factura obtenido: ", data);
    });

    $.post("../../controller/factura.php?op=mostrar", { fact_id: id }, function(data) {
        data = JSON.parse(data);
        $('#lblestado').html(data.fact_estado);
        $('#lblnomusuario').html(data.usu_nom + ' ' + data.usu_ape);
        $('#lblfechcrea').html(data.fech_crea);

        $('#lblnomidfactura').html("Detalle Factura - " + data.fact_id);

        $('#cat_nom').val(data.cat_nom);
        $('#cats_nom').val(data.cats_nom);
        $('#fact_titulo').val(data.fact_titulo);
        $('#factd_descripusu').summernote('code', data.fact_descrip);

        $('#prio_nom').val(data.prio_nom);

        if (data.fact_estado_texto == "Cerrado") {
            $('#pnldetalle').hide();
        }
        console.log("Información del factura obtenida: ", data);
    });
}

function init() {
    $("#factura_form").on("submit", function(e) {
        guardaryeditar(e);
    });
}

$(document).ready(function() {
    $('#fact_descrip').summernote({
        height: 150,
        lang: "es-ES",
        popover: {
            image: [],
            link: [],
            air: []
        },
        callbacks: {
            onImageUpload: function(image) {
                console.log("Image detect...");
                myimagetreat(image[0]);
            },
            onPaste: function(e) {
                console.log("Text detect...");
            }
        },
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']]
        ]
    });

    $.post("../../controller/categoria.php?op=combo", function(data, status) {
        $('#cat_id').html(data);
        $('#cat_id').prepend('<option value="" disabled selected>Seleccionar</option>'); 
    });

    $("#cat_id").change(function() {
        var cat_id = $(this).val();
        $.post("../../controller/subcategoria.php?op=combo", { cat_id: cat_id }, function(data, status) {
            console.log(data);
            $('#cats_id').html(data);
        });
    });

    $.post("../../controller/prioridad.php?op=combo", function(data, status) {
        $('#prio_id').html(data);
    });
});

function guardaryeditar(e) {
    e.preventDefault();
    
    var formData = new FormData($("#factura_form")[0]);
    
    if ($('#fact_descrip').summernote('isEmpty') || $('#fact_titulo').val() === '' || $('#cats_id').val() == 0 || $('#cat_id').val() == 0 || $('#prio_id').val() == 0) {
        swal("Advertencia!", "Campos Vacios", "warning");
    } else {
        var totalfiles = $('#fileElem')[0].files.length;
        for (var index = 0; index < totalfiles; index++) {
            formData.append("file[]", $('#fileElem')[0].files[index]);
        }
        $.ajax({
            url: "../../controller/factura.php?op=guardaryeditar",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(datos) {
                console.log(datos);
                $('#factura_form')[0].reset();
                $('#fact_descrip').summernote('reset');
                $('#fileElem').val('');
                $('.custom-file-label').html('Elegir archivos');
                $("#lbldetalle").empty();
                swal("Correcto!", "Registrado Correctamente", "success");
                window.location.href = "../factura/";
            }
        });
    }
}

init();
