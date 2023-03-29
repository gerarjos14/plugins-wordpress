
 (function( $ ) {
	'use strict';
	var $precargador = $('.precargador'); 
    
    /**
     * Evento click para guardar la data general del usuario 
     * (token) en la base de datos, por medio de la implementación de AJAX
     * @author Matias
     * 
     */
    $('#send-keys').on( 'click', function(e){
        e.preventDefault();
        $precargador.css( 'display', 'flex' );
        // Billconnector_base.quitarInvalid(".form-keys input");
        // var emptyForm = Billconnector_base.validarCamposVacios(".form-keys input")
                        
        var token      = $('#token').val();
        var bc_service = {
            nonce: $('#service-nonce').val(),
            url: '/wp-admin/admin-ajax.php'
        };


        // Envío de AJAX
        $.ajax({
            url         : bc_service.url,
            type        : 'POST',
            data : {
                action          : 'bc_config',
                nonce           : bc_service.nonce,
                token         	: token,
            }, success  : function( response ) {
                var response = $.parseJSON(response);
                if(response['status'] == 'error'){
                    swal({
                        title: "Error",
                        text: 'El token ingresado no se encuentra en nuestros registros',
                        icon: 'error',
                    }).then(
                        function(){
                            window.location.reload();
                        }
                    );
                }else{
                    swal({
                        title: "Ok!",
                        text: 'Se ha modificado la configuración del plugin con éxito',
                        icon: 'success',
                    }).then(
                        function(){
                            window.location.reload();
                        }
                    );
                }
                

            }, error: function( d,x,v ) {
                swal({
                    title: "Error",
                    text: 'Ha ocurrido un error al momento de guardar la configuración',
                    icon: 'error',
                }).then(
                    function(){
                        window.location.reload();
                    }
                );
                console.log(d);
                console.log(x);
                console.log(v);
            }
        });

    });
    
})( jQuery );