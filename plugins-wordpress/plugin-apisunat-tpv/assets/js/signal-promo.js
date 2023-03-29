jQuery(document).ready(function ($) {
    $('#changeKeys').click(function (e) {
        e.preventDefault();
        $('#modalChangeKey').modal('show');
    });

    $('#exampleModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var modal = $(this)
      })

    $('#tutorial_Key').click(function (e) {
        e.preventDefault();
        $('#modalViewTutorial').modal('show');
    });

    $('#info').click(function (e) {
        e.preventDefault();
        $('#modalInfo').modal('show');
    });


    $('#sendForm').click(function (e) {

        var url_sist = $('#url_sist_change').val();
        console.log(url_sist);
        if(url_sist == 1){
            var website_tpv = $('#website_tpv').val();
            console.log(website_tpv);
            $.ajax({
                url: ajaxData.ajax_url,
                type: 'POST',
                datatype: 'json',
                data: {
                    action: 'lars_pos_change_keys',
                    nonce: ajaxData.nonce,
                    website: website_tpv,
                    url_sist: url_sist,

                },
                success: function (data) {
                    console.log(data);
                    window.location.reload();
                },
                error: function (d, x, v) {
                    console.log(d);
                    console.log(x);
                    console.log(v);
                }
            });
        }else{
            var website = $('#website').val(),
                consumer_key = $('#consumer_key').val();
                consumer_secret = $('#consumer_secret').val();
                website_tpv = $('#website_tpv').val();

            console.log(url_sist);
            console.log('hola');
            $.ajax({
                url: ajaxData.ajax_url,
                type: 'POST',
                datatype: 'json',
                data: {
                    action: 'lars_pos_change_keys',
                    nonce: ajaxData.nonce,
                    website: website,
                    website_tpv: website_tpv,
                    consumer_key: consumer_key,
                    consumer_secret: consumer_secret,
                    url_sist: url_sist,
                },
                success: function (data) {
                    console.log(data);
                    window.location.reload();
                },
                error: function (d, x, v) {
                    console.log(d);
                    console.log(x);
                    console.log(v);
                }
            });
        }  
        
    });

    $('.send-category').click(function (e) {
        const category = $(this).siblings()[0];
        const percentage = $(this).siblings()[1];
        const btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: ajaxData.ajax_url,
            type: 'POST',
            datatype: 'json',
            data: {
                action: 'lars_pos_categories_prices',
                nonce: ajaxData.nonce,
                percentage: percentage['value'],
                category: category['value'],
            },
            success: function (data) {
                btn.prop('disabled', false);
                //btn.removeClass('button--loading');
                console.log(data);
                // window.location.reload();
            },
            error: function (d, x, v) {
                btn.prop('disabled', false);
                console.log(d);
                console.log(x);
                console.log(v);
            }
        });
    });
    
    $(".search-result").on("click", ".send-product", function () {
        //console.log( );

        const product = $("#nameProducto").val() ;
        const increase = $("#valorAumento").val();
        const isLumise = $("#lumise").val();
        const btn = $(this);
        console.log(product);

        btn.prop('disabled', true);
        $.ajax({
            url: ajaxData.ajax_url,
            type: 'POST',
            datatype: 'json',
            data: {
                action: 'lars_pos_products_prices',
                nonce: ajaxData.nonce,
                increase: increase,
                product: product,
                lumise: 'off',
            },
            success: function (data) {
                btn.prop('disabled', false);
                console.log(data);
                // window.location.reload();
            },
            error: function (d, x, v) {
                btn.prop('disabled', false);
                console.log(d);
                console.log(x);
                console.log(v);
            }
        });
    });

    $('#search-product').click(function (e) {
        const btn = $(this);
        btn.prop('disabled', true);
        product = $('#title-product').val();
        $.ajax({
            url: ajaxData.ajax_url,
            type: 'POST',
            datatype: 'json',
            data: {
                action: 'lars_pos_search_product',
                nonce: ajaxData.nonce,
                product: product,
            },
            success: function (data) {
                btn.prop('disabled', false);
                response = JSON.parse(data);
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

    function validateEmpty() {
        $('.form-keys input').removeClass('is-invalid');
        var $inputs = $('.form-keys input'),
            result = true;
        $.each($inputs, function (k, v) {

            var $input = $(v),
                inputVal = $input.val();

            if (inputVal == '' && $input.attr('type') != 'file') {
                if (!$input.hasClass('is-invalid')) {
                    $input.addClass('is-invalid');
                }
                result = false;
            }

        });
        return result;
    }
});
