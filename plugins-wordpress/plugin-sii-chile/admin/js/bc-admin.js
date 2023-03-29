(function( $ ) {
	'use strict';
	var $precargador = $('.precargador');
    
    /**
     * Modal con el Formulario para guardar claves
     */
    $('#changeKeys').click(function (e) {
        e.preventDefault();
        $('#modalChangeKey').modal('show');
    });

    /**
     * Evento click para guardar
     * el registro en la base de datos
     * utilizando AJAX
     */
    $('#send-keys').on( 'click', function(e){
        e.preventDefault();
        $precargador.css( 'display', 'flex' );
        Billconnector.quitarInvalid(".form-keys input");
        var emptyForm = Billconnector.validarCamposVacios(".form-keys input")
        if( emptyForm ){
            $precargador.css( 'display', 'none' );
            return false;
        }    
       
        
        var token = $('#token').val(),
		    order_status = $('#order_status').val();
     

        // Envío de AJAX
        $.ajax({
            url         : bc.url,
            type        : 'POST',
            dataType    : 'json',
            data : {
                action          : 'bc_config',
                nonce           : bc.seguridad,
                token         	: token,
                order_status    : order_status,
            }, success  : function( data ) {

                console.log(data);
                $('#modalChangeKey').modal('hide');
                $precargador.css( 'display', 'none' );
                
                // $('#modalChangeKey').modal('hide');

            }, error: function( d,x,v ) {

                console.log(d);
                console.log(x);
                console.log(v);

                $precargador.css( 'display', 'none' );

            }
        });

    });


    $('#search-order').click(function (e) {
        const btn = $(this);
        btn.prop('disabled', true);

        var nro_order = $('#nro-order').val();
        nro_order=parseInt(nro_order);
        if (isNaN(nro_order)==true){
            btn.prop('disabled', false);
            return false;
        }
        $.ajax({
            url      : bc.url,
            type     : 'POST',
            datatype : 'json',
            data: {
                action    : 'bc_search_order',
                nonce     : bc.seguridad,
                nro_order : nro_order,
            },
            success: function (data) {
                btn.prop('disabled', false);
                let response = JSON.parse(data);
                $('#search-result').html(response.html);
            },
            error: function (d, x, v) {
                btn.prop('disabled', false);
                console.log(d);
                console.log(x);
                console.log(v);
            }
        });

    });
})( jQuery );