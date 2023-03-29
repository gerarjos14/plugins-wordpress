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
        SiigoConnector.quitarInvalid(".form-keys input");
        var emptyForm = SiigoConnector.validarCamposVacios(".form-keys input")
        if( emptyForm ){
            $precargador.css( 'display', 'none' );
            return false;
        }             
        
        var username = $('#username').val(),
            access_key = $('#access_key').val(),
            website = $('#website').val(),
            consumer_key = $('#consumer_key').val(),
            consumer_secret = $('#consumer_secret').val();

        var isValidUrl = SiigoConnector.validarUrl(website); 
        if(!isValidUrl){   
            if( ! $('#website').hasClass( 'is-invalid' ) ) {
                $('#website').addClass( 'is-invalid' );
            }
            $precargador.css( 'display', 'none' );
            return false;
        }
        // Envío de AJAX
        $.ajax({
            url         : sc.url,
            type        : 'POST',
            dataType    : 'json',
            data : {
                action          : 'sc_keys',
                nonce           : sc.seguridad,
                website         : website,
                username        : username,
                access_key      : access_key,
                consumer_key    : consumer_key,
                consumer_secret : consumer_secret,
            }, success  : function( data ) {

                console.log(data);

                $precargador.css( 'display', 'none' );
                
                $('#modalChangeKey').modal('hide');

            }, error: function( d,x,v ) {

                console.log(d);
                console.log(x);
                console.log(v);

                $precargador.css( 'display', 'none' );

            }
        });

    });
   
})( jQuery );
