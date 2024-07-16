function init(){
   
}

$(document).ready(function(){
    var fact_id = getUrlParameter('ID');
    listardetalle(fact_id);
    console.log(fact_id);

    /* TODO: inicializamos input de estrellas */
    $('#fact_estre').on('rating.change', function() {
        console.log($('#fact_estre').val());
    });

});

function listardetalle(fact_id){
    /* TODO: Mostra detalle de factura */
    $.post("../../controller/factura.php?op=mostrar_noencry", { fact_id : fact_id }, function (data) {
        data = JSON.parse(data);
        $('#lblestado').val(data.fact_estado_texto);
        $('#lblnomusuario').val(data.usu_nom +' '+data.usu_ape);
        $('#lblfechcrea').val(data.fech_crea);
        $('#lblnomidfactura').val(data.fact_id);
        $('#cat_nom').val(data.cat_nom);
        $('#cats_nom').val(data.cats_nom);
        $('#fact_titulo').val(data.fact_titulo);
        $('#prio_nom').val(data.prio_nom);
        $('#lblfechcierre').val(data.fech_cierre);

        if (data.fact_estado_texto=='Abierto') {
            window.open('http://localhost/ATN_PORTAL_PROVEEDORES/','_self');
        }else{
            if (data.fact_estre==null){

            }else{
                $('#panel1').hide();
            }
        }
    });
}


/* TODO: Obtener ID de la Url */
var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

/* TODO:Guardar Informacion de estrella del factura */
$(document).on("click","#btnguardar", function(){
    var fact_id = getUrlParameter('ID');
    var fact_estre = $('#fact_estre').val(); 
    var fact_coment = $('#fact_coment').val();

    $.post("../../controller/factura.php?op=encuesta", { fact_id : fact_id,fact_estre:fact_estre,fact_coment:fact_coment}, function (data) {
        console.log(data);
        $('#panel1').hide();
        swal("Correcto!", "Gracias por su Tiempo", "success");
    }); 
});