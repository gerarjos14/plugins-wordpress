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

    // formulario configuración almacen para importación de datos
    $('#sendWarehouseForm').click(function(e){
        // facturación
        var warehouse = $('#id_warehouse').val();
        

        // envio datos por medio de AJAX para guardarlos en la BD
        $.ajax({
            url: ajaxData.ajax_url,
            type: 'POST',
            datatype: 'json',
            data: {
                action: 'lars_pos_change_config_warehouse',
                nonce: ajaxData.nonce,
                warehouse: warehouse,
            },
            success: function (data) {
                swal({
                    title: "Ok!",
                    text: 'Se ha modificado el almacen con éxito',
                    icon: 'success',
                }).then(
                    function(){
                        window.location.reload();
                    }
                );

            },
            error: function (d, x, v) {
                swal({
                    title: "Error",
                    text: 'Ha ocurrido un error al momento de guardar el almacen',
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

    // formulario configuracion de la ventas
    $('#sendSaleForm').click(function(e){
        // facturación
        var factura              = $('#id_factura').val();
        var impuesto             = $('#id_impuesto').val();
        var motivo_traslado      = $('#id_motivo_traslado').val();
        var peso                 = $('#id_peso_total').val();
        var trasbordo            = $('#id_trasbordo').val();
        // envío
        var modalidad_transporte = $('#id_modalidad_transporte').val();
        var transportista        = $('#id_name_transportista').val();
        var id_transporte        = $('#id_num_transporte').val();

        // envio datos por medio de AJAX para guardarlos en la BD
        $.ajax({
            url: ajaxData.ajax_url,
            type: 'POST',
            datatype: 'json',
            data: {
                action: 'lars_pos_chane_config_sales',
                nonce: ajaxData.nonce,
                factura: factura,
                impuesto: impuesto,
                motivo_traslado: motivo_traslado,
                peso: peso,
                trasbordo: trasbordo,
                modalidad_transporte: modalidad_transporte,
                transportista: transportista,
                id_transporte: id_transporte
            },
            success: function (data) {
                swal({
                    title: "Ok!",
                    text: 'Se han modificado los valores de la configuración de ventas con éxito',
                    icon: 'success',
                }).then(
                    function(){
                        window.location.reload();
                    }
                );

            },
            error: function (d, x, v) {
                swal({
                    title: "Error",
                    text: 'Ha ocurrido un error al momento de guardar las modificaciones de la configuración de ventas',
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
                    swal({
                        title: "Ok!",
                        text: 'Se han modificado los valores de la configuración del plugin con éxito',
                        icon: 'success',
                    }).then(
                        function(){
                            window.location.reload();
                        }
                    );
                },
                error: function (d, x, v) {
                    swal({
                        title: "Error",
                        text: 'Ha ocurrido un error al momento de guardar las modificaciones de la configuración del plugin',
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
                    swal({
                        title: "Ok!",
                        text: 'Se han modificado los valores de la configuración del plugin con éxito',
                        icon: 'success',
                    }).then(
                        function(){
                            window.location.reload();
                        }
                    );
                },
                error: function (d, x, v) {
                    swal({
                        title: "Error",
                        text: 'Ha ocurrido un error al momento de guardar las modificaciones de la configuración del plugin',
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
