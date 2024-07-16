
function init(){
    $("#factura_form").on("submit",function(e){
        guardaryeditar(e);
    });
}

$(document).ready(function() {
    /* TODO: Inicializar SummerNote */
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
            onPaste: function (e) {
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

    /* TODO: Llenar Combo categoria */
    $.post("../../controller/categoria.php?op=combo",function(data, status){
        $('#cat_id').html(data);
        $('#cat_id').prepend('<option value="" disabled selected>Seleccionar</option>'); 
    });

    $("#cat_id").change(function(){
        cat_id = $(this).val();
        /* TODO: llenar Combo subcategoria segun cat_id */
        $.post("../../controller/subcategoria.php?op=combo",{cat_id : cat_id},function(data, status){
            console.log(data);
            $('#cats_id').html(data);
        });
    });

    /* TODO: Llenar combo Prioridad  */
    $.post("../../controller/prioridad.php?op=combo",function(data, status){
        $('#prio_id').html(data);
    });

});

document.querySelector('.custom-file-input').addEventListener('change', function(e) {
    var fileName = document.getElementById("fileElem").files.length > 1 
        ? document.getElementById("fileElem").files.length + ' archivos seleccionados' 
        : e.target.value.split('\\').pop();
    var nextSibling = e.target.nextElementSibling;
    nextSibling.innerText = fileName;
});


function guardaryeditar(e) {
    e.preventDefault();

    var formData = new FormData($("#factura_form")[0]);

    if ($('#fact_descrip').summernote('isEmpty') || $('#fact_titulo').val() == '' || $('#cats_id').val() == 0 || $('#cat_id').val() == 0 || $('#prio_id').val() == 0) {
        swal("Advertencia!", "Campos Vacios", "warning");
    } else {
        var totalfiles = $('#fileElem')[0].files.length;
        for (var i = 0; i < totalfiles; i++) {
            formData.append("files[]", $('#fileElem')[0].files[i]);
        }

        $.ajax({
            url: "../../controller/factura.php?op=insert",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(data) {
                console.log(data);
                data = JSON.parse(data);
                console.log(data[0].fact_id);

                $.post("../../controller/email.php?op=factura_abierta", { fact_id: data[0].fact_id }, function(data) {
                    // Puedes añadir aquí más lógica si es necesario
                });

                $('#fact_titulo').val('');
                $('#fact_descrip').summernote('reset');
                $('#cat_id').val(''); // Limpiar selección de categoría
                $('#prio_id').val(''); // Limpiar selección de prioridad
                $('#cats_id').val(''); // Limpiar selección de subcategoría
                $('#fileElem').val(''); // Limpiar selección de documentos
                
                // Reiniciar el texto del label del campo de archivo
                var label = $('#fileElem').next('.custom-file-label');
                label.text('Elegir archivos');

                // Reiniciar el select de categoría correctamente
                $('#cat_id').html('<option value="" disabled selected>Seleccionar</option>');
                $.post("../../controller/categoria.php?op=combo", function(data, status) {
                    $('#cat_id').append(data);
                });

                swal("Correcto!", "Registrado Correctamente", "success");
            }
        });
    }
}


init();