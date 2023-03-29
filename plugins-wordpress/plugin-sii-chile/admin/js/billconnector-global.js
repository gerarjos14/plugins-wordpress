(function( global, jQuery, nucleo ) {
    
    nucleo( global, jQuery );
    
})( typeof window !== 'undefined' ? window : this, jQuery, function( window, $ ) {
        
    var Billconnector = (function(){
        
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
    
    window.Billconnector = window.$bc = Billconnector;
    
});























