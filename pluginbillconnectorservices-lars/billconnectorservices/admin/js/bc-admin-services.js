
(function( $ ) {
    /**
     * Funciones para activar desactivar servicios
     * @author
     */
    // SIIGO
    $('#toggle1').change(function(){
        var mode = $(this).prop('checked');
            consultaAJAX_Services('SIIGO', mode);
        
    });

    // ALEGRA
    $('#toggle2').change(function(){
        var mode = $(this).prop('checked');
            consultaAJAX_Services('ALEGRA', mode);
        
    });

    // SII
    $('#toggle3').change(function(){
        var mode = $(this).prop('checked');
            consultaAJAX_Services('SII', mode);
        
    });

    // PAGUE A TIEMPO
    $('#toggle4').change(function(){
        var mode = $(this).prop('checked');
            consultaAJAX_Services('PAGUE', mode);
        
    });

    // ANALITYCS
    $('#toggle5').change(function(){
        var mode = $(this).prop('checked');
            consultaAJAX_Services('ANALITYCS', mode);
         
    });

    // SUNAT
    $('#toggle6').change(function(){
        var mode = $(this).prop('checked');
            consultaAJAX_Services('SUNAT', mode);
        
    });

    function consultaAJAX_Services(services, mode){
        console.log(services);
        var bc_service = {
            seguridad: $('#service-nonce').val(),
            url: '/wp-admin/admin-ajax.php'
        };

        console.log(mode);
       // Envío de AJAX
        $.ajax({
            url         : bc_service.url,
            type        : 'POST',
            data : {
                action  : 'bc_config_services',
                nonce   : bc_service.seguridad,
                service : services,
                active  : mode,
            }, 
            success  : function( response ) {
                var response = $.parseJSON(response);
                console.log(response);
                
                if(mode == true){
                    message_show = 'activado';
                    message_mode = 'activar';
                }else{
                    message_show = 'desactivado';
                    message_mode = 'desactivar'

                }

                if(response['status'] == 'error'){
                    swal({
                        title: "Error",
                        text: 'Error al '+ message_mode +' el servicio ' + services,
                        icon: 'error',
                    }).then(
                        function(){
                            window.location.reload();
                        }
                    );
                }else{

                    if(mode == false){
                        var text = 'El servicio ' + services + ' ha sido desactivado con éxito';
                        swal({
                            title: "Ok!",
                            text: text,
                            icon: 'success',
                        }).then(
                            function(){
                                window.location.reload();
                            }
                        );
                    }else{
                        console.log(response['show_url']);

                        if(response['show_url'] == true){
                            // muestro url, el servicio no existe
                            if(response['paid']){
                                var text = 'Para activar el servicio ' + services + ' debes de instalar el siguiente plugin: (url) '+ response['url'] +'';
                                swal({
                                    title: "Ok!",
                                    text: text,
                                    icon: 'success',
                                }).then(
                                    function(){
                                        window.location.reload();
                                    }
                                );
                            }else{
                                var text = 'Obtené más servicios!. El servicio ' + services + ' no lo tienes pago ¿Deseas obtener la url de pago?';
                                swal({
                                    title: "Servicio no pago",
                                    text: text,
                                    icon: "warning",
                                    buttons: true,
                                    dangerMode: true,
                                  })
                                  .then((willDelete) => {
                                    if (willDelete) {
                                        swal({
                                            title: "Ok!",
                                            text: 'Url de pago de '+ services +' : '+ response['url'] +'',                                    icon: 'success',
                                        }).then(
                                            function(){
                                                window.location.reload();
                                            }
                                        );
                                    }
                                  });
                            }
                        }else{
                            var text = 'El servicio ' + services + ' ha sido activado con éxito';
                            swal({
                                title: "Ok!",
                                text: text,
                                icon: 'success',
                            }).then(
                                function(){
                                    window.location.reload();
                                }
                            );
                        }                        
                    }
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
    }
})( jQuery );