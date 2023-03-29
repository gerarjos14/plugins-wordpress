(function( global, jQuery, nucleo ) {
    
    nucleo( global, jQuery );
    
})( typeof window !== 'undefined' ? window : this, jQuery, function( window, $ ) {
        
    var SiigoConnector = (function(){
        
        var core = {  
            /* Validando que los campos no estén vacíos */
            validarCamposVacios     : function( selector ) {
                var $inputs = $( selector ),
                    result  = false;

                $.each( $inputs, function(k,v){

                    var $input      = $(v),
                        inputVal    = $input.val();

                    if( inputVal == '' && $input.attr('type') != 'file' ) {

                        if( ! $input.hasClass( 'is-invalid' ) ) {

                            $input.addClass( 'is-invalid' );

                        }

                        result = true;

                    }

                });

                if( result ) {
                    return true;
                } else {
                    return false;
                }

            },
            /* Método para validar los correos electrónicos */
            validarEmail            : function ( email ) {
        
                var er  = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;        

                return er.test( email );

            },
            /* Método para validar las url */
            validarUrl           : function ( url ) {
        
                var er  = /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/;        

                return er.test( url );

            },
            /**
             * Permite quitar las clases invalid
             * de los formularios 
             */
            quitarInvalid           : function( selector ) {
        
                var $inputs = $( selector );

                $.each( $inputs, function(k,v){

                    var $input = $(v);

                    if( $input.hasClass( 'is-invalid' ) ) {
                        $input.removeClass( 'is-invalid' );
                    }

                });

            },   
        }
        
        return core;
        
    })();
    
    window.SiigoConnector = window.$sc = SiigoConnector;
    
});























