jQuery(document).ready(function($){
    $('#changeKeys').click(function(e) {
        e.preventDefault();
        $('#modalChangeKey').modal('show');
    });
    $('#info').click(function(e) {
        e.preventDefault();
        $('#modalInfo').modal('show');
    });
    $('#sendForm').click(function(e) {
        var token = $('#token').val(),
            order_status = $('#order_status').val();
        if(validateEmpty()){
            $.ajax({
                url:        ajaxData.ajax_url,
                type:       'POST',
                datatype:   'json',
                data: {
                    action:       'wab_change_keys',
                    nonce:        ajaxData.nonce,
                    token:        token,
                    order_status: order_status,
                }, 
                success: function(data) {
                    console.log(data);
                    window.location.reload();
                },
                error: function( d, x, v) {
                    console.log(d);
                    console.log(x);
                    console.log(v);
                }
            });
        }
    });
    function validateEmpty() {           
        $('.form-keys input').removeClass('is-invalid');
        var $inputs = $('.form-keys input'),
            result  = true;
        $.each( $inputs, function(k,v){ 

            var $input      = $(v),
                inputVal    = $input.val();
            
            if( inputVal == '' && $input.attr('type') != 'file' ) {                
                if( ! $input.hasClass( 'is-invalid' ) ) {                    
                    $input.addClass( 'is-invalid' );                    
                }                
                result = false;                
            }   

        });
        return result;        
    }
});