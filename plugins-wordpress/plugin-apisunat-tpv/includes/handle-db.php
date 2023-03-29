<?php
require_once plugin_dir_path(__FILE__) . '/helper.php';
require_once plugin_dir_path(__FILE__) . '/functions-db.php';

use Automattic\WooCommerce\Client;

function lars_pos_ajax_products_prices()
{
    check_ajax_referer('data_security', 'nonce');
    if (current_user_can('manage_options')) {

        global $wpdb;
        $keys = getKeys($wpdb);
        print_r($keys);

        if ($keys) {

            $woocommerce = new Client(
                $keys->website,
                $keys->consumer_key,
                $keys->consumer_secret,
                ['wp_api' => true, 'version' => 'wc/v3', 'verify_ssl' => false]
            );
            $a = 0;
            $increase = isset($_POST['increase']) ? sanitize_text_field($_POST['increase']) : null;
            $product = isset($_POST['product']) ? sanitize_text_field($_POST['product']) : null;
            $lumise =  $_POST['lumise'] == "true" ? true : false;

            $length = strlen($increase); // Saber si vino vacio
            $increase = round((float) $increase);
            echo $product;
            $query = "SELECT * FROM {$wpdb->prefix}posts WHERE post_title='{$product}' LIMIT 1;";
            $row = $wpdb->get_row($query);
            if ($row) {
                $query = "SELECT * FROM {$wpdb->prefix}" . TABLE_PRODUCTS . " WHERE w_product_id={$row->ID} LIMIT 1;";
                $local_product = $wpdb->get_row($query);
                //print_r($row);
                if ($local_product) {
                    $a = 1;
                    $data = array();
                    if ($length && $increase >= 0) {
                        if (
                            $local_product->type_increase == 1 ||
                            ($local_product->type_increase == 0 && $local_product->increase != $increase)
                        ) {
                            $data['increase'] = $increase;
                            $data['type_increase'] = 0;
                        }
                    }
                    if ($local_product->lumise != $lumise) {
                        $data['lumise'] = $lumise;
                    }
                    if (count($data)) {
                        $resultUpdate = $wpdb->update("{$wpdb->prefix}" . TABLE_PRODUCTS, $data, ['w_product_id' => $row->ID]);

                        if (isset($data['lumise'])) {
                            if ($data['lumise']) {
                                $variations = wcGetProductVariations($woocommerce, $local_product->w_product_id);
                                $stock = 0;
                                foreach ($variations as $variation) {
                                    $stock = $stock + $variation->stock_quantity;
                                }
                                if (isset($data['increase'])) {
                                    $price = $local_product->precio + $data['increase'];
                                } else {
                                    $price = resolvePrice($local_product->precio, $local_product->increase, $local_product->type_increase);
                                }

                                $wc_data = array();
                                $wc_data['type'] = 'simple';
                                $wc_data['manage_stock'] = true;
                                $wc_data['stock_status'] = ($stock > 0) ? 'instock' : 'outofstock';
                                $wc_data['stock_quantity'] = $stock;
                                $wc_data['regular_price'] = strval($price);

                                $result = wcUpdateProduct($woocommerce, $local_product->w_product_id, $wc_data);

                                $json = json_encode([
                                    'status' => 'OK',
                                    'content' => 'Cambio a simple'
                                ]);
                                echo $json;
                                wp_die();
                            } else {
                                if (!isset($data['increase'])) {
                                    $price = resolvePrice($local_product->precio, $local_product->increase, $local_product->type_increase);

                                    //$price = $local_product->precio + $data['increase'];
                                } else {
                                    $price = resolvePrice($local_product->precio, $local_product->increase, $local_product->type_increase);
                                }
                                //Cambio de producto simple a variante        

                                $api_product = apiGetProduct($local_product->api_product_id);
                                if ($api_product) {
                                    $attr = $keys->attr;
                                    $terms = array();
                                    $control = true;
                                    $page = 1;
                                    while ($control) {
                                        $wc_terms = wcGetTerms($woocommerce, $attr, $page);
                                        if (count($wc_terms)) {
                                            foreach ($wc_terms as $term) {
                                                $indice = strtolower($term->name);
                                                $terms[$indice] = [
                                                    'id' => $term->id,
                                                    'name' => $term->name,
                                                    'slug' => $term->slug,
                                                ];
                                            }
                                            if (count($wc_terms) < 100) {
                                                $control = false;
                                            }
                                        } else {
                                            $control = false;
                                        }
                                        $page++;
                                    }
                                    $wc_data = lars_pos_array_product_variations($local_product, $api_product, $woocommerce, $attr, $terms, $price);
                                    $result = wcUpdateProduct($woocommerce, $local_product->w_product_id, $wc_data[0]);
                                    $result = wcStoreVariants($woocommerce, $local_product->w_product_id, $wc_data[1]);

                                    $json = json_encode([
                                        'status' => 'OK',
                                        'content' => 'Cambio a variable'
                                    ]);
                                    echo $json;
                                    wp_die();
                                }
                            }
                        } elseif (isset($data['increase'])) {
                            $price = resolvePrice($local_product->precio, $data['increase'], '0');

                            //$price = $local_product->precio + $data['increase'];

                            if ($local_product->lumise) {
                                // Simple
                                $wc_data = array();
                                $wc_data['regular_price'] = strval($price);
                                $result = wcUpdateProduct($woocommerce, $local_product->w_product_id, $wc_data);
                            } else {
                                // Variable
                                $variations = lars_pos_product_update_price($local_product, $price);
                                $result = wcStoreVariants($woocommerce, $local_product->w_product_id, $variations);
                            }
                        }
                    }
                    $json = json_encode([
                        'status' => 'OK',
                    ]);
                    echo $json;
                    wp_die();
                }
            }
        }
        $json = json_encode([
            'status' => 'ERROR' . $a,
        ]);
        echo $json;
        wp_die();
    }
    wp_die();
}

function lars_pos_ajax_search_product()
{
    check_ajax_referer('data_security', 'nonce');
    if (current_user_can('manage_options')) {
        global $wpdb;
        $query = "SELECT tax_rate FROM {$wpdb->prefix}woocommerce_tax_rates WHERE tax_rate_country = 'CO' AND tax_rate_name = 'IVA' ";
        $data_IVA = $wpdb->get_results($query);
        $data_IVA = floatval($data_IVA[0]->tax_rate);


        $product_title = isset($_POST['product']) ? sanitize_text_field($_POST['product']) : null;
        if (strlen($product_title) > 2) {
            // $wpdb->get_results("SELECT * FROM {$wpdb->prefix}posts WHERE post_title = '$nameP' ");
            $query = "SELECT * FROM {$wpdb->prefix}posts, {$wpdb->prefix}postmeta, {$wpdb->prefix}lars_pos_products WHERE ({$wpdb->prefix}posts.post_type='product') AND ({$wpdb->prefix}posts.post_title = '{$product_title}') AND ({$wpdb->prefix}postmeta.meta_key = '_price') AND ({$wpdb->prefix}postmeta.post_id = {$wpdb->prefix}posts.ID) AND {$wpdb->prefix}lars_pos_products.w_product_id = {$wpdb->prefix}posts.ID;";

            $products = $wpdb->get_results($query);

            if (empty($products)) {
                $response = '<p class="text-sp text-error-search">
                <i class="fas fa-exclamation-triangle"></i> No se encontraron productos con ese título</p>';
            } else {
                $response = '<table class="table table-bordered table-productos">';
                $response .= '
                    <thead class="thead-dark thead-dark-sp">
                        <th class="text-sp txt-tables" style="text-align:center;" width="5%">#</th>
                        <th class="text-sp txt-tables" style="text-align:center;" width="15%">Nombre</th>
                        <th class="text-sp txt-tables" style="text-align:center;" width="20%">Precio base</th>
                        <th class="text-sp txt-tables" style="text-align:center;" width="25%">Precio actual <br> (aumento)</th>
                        <th class="text-sp txt-tables" style="text-align:center;" width="60%">Nuevo Aumento (%)</th></tr>
                    </thead>                        
                    <tbody>';

                foreach ($products as $key => $product) {

                    $response .= '<tr>';
                    $response .= '<td style="text-align: center;">' . ($key + 1) . '</td>';
                    $response .= '<td  style="text-align: center;">' . $product->post_title . '</td>';
                    $response .= '<td style="text-align: center;"> $' . $product->precio . '</td>';
                    $response .= '<td style="text-align: center;"> $' . $product->meta_value . ' ( ' . $product->increase . '% aumento) </td>';

                    $response .= '<td style="text-align: center;">';
                    $response .= '<div class="row">';
                    $response .= '<div class="col-sm-6">';
                    $response .= '<input id="nameProducto"  value="' . $product->post_title . '" class="form-control" name="increase" type="hidden" style="text-align: center;" step="0" value="" class="mr-2" />';

                    $response .= '<input id="valorAumento" class="form-control" name="increase" type="number" style="text-align: center;" step="0" value="" class="mr-2" />';
                    $response .= '</div>';
                    $response .= '<div class="col-sm-2">';
                    $response .= '<input type="checkbox" name="lumise" id="lumise"><label for="lumise-' . $product->id . '" class="mr-2">Lumise</label>';
                    $response .= '</div>';
                    $response .= '<div class="col-sm-3">';
                    $response .= '<button type="button" class="btn btn-save send-product"><b>Guardar</b></button>';

                    $response .= '</div>';

                    $response .= '</div>';
                    $response .= '<input name="product" type="hidden" value="' . $product->id . '" />';

                    $response .= '</td>';
                    $response .= '</tr>';
                }
                $response .= '</tbody></table>';
            }

            $json = json_encode([
                'status' => 'OK',
                'html'   => $response,
                'ok' => $products
            ]);
            echo $json;
            wp_die();
        }

        $json = json_encode([
            'status' => 'ERROR',

        ]);
        echo $json;
        wp_die();
    }
    wp_die();
}


function lars_pos_ajax_change_keys()
{
    check_ajax_referer('data_security', 'nonce');
    if (current_user_can('manage_options')) {

        global $wpdb;
        $query = "SELECT * FROM {$wpdb->prefix}lars_pos_keys ORDER BY id ASC LIMIT 1;";
        $row = $wpdb->get_row($query);
        if ($_POST['url_sist']) {
            $website = lars_pos_sanitize_text_field($_POST['website_tpv'],  $row ? $row->website_tpv : null);

            $data = array(
                'website_tpv' => $website,
            );

            if ($row) {
                $result = $wpdb->update("{$wpdb->prefix}lars_pos_keys", $data, ['id' => $row->id]);
            } else {
                $result = $wpdb->insert("{$wpdb->prefix}lars_pos_keys", $data);
            }

            $json = json_encode([
                'result'    => $result,
            ]);

            echo $json;
            wp_die();
        } else {
            $website = lars_pos_sanitize_text_field($_POST['website'],  $row ? $row->website : null);
            $website_tpv = lars_pos_sanitize_text_field($_POST['website_tpv'],  $row ? $row->website : null);

            $consumer_key = lars_pos_sanitize_text_field($_POST['consumer_key'],  $row ? $row->consumer_key : null);
            $consumer_secret = lars_pos_sanitize_text_field($_POST['consumer_secret'],  $row ? $row->consumer_secret : null);

            $data = array(
                'website' => $website,
                'website_tpv' => $website_tpv,
                'consumer_key' => $consumer_key,
                'consumer_secret' => $consumer_secret,
            );

            if ($row) {
                $result = $wpdb->update("{$wpdb->prefix}lars_pos_keys", $data, ['id' => $row->id]);
            } else {
                $result = $wpdb->insert("{$wpdb->prefix}lars_pos_keys", $data);
            }

            $json = json_encode([
                'result'    => $result,
            ]);

            echo $json;
            wp_die();
        }
    }
    wp_die();
}

function lars_pos_ajax_change_keys_url_tpv()
{
    check_ajax_referer('data_security', 'nonce');
    if (current_user_can('manage_options')) {

        global $wpdb;
        $query = "SELECT * FROM {$wpdb->prefix}lars_pos_keys ORDER BY id ASC LIMIT 1;";
        $row = $wpdb->get_row($query);

        $website = lars_pos_sanitize_text_field($_POST['website_tpv'],  $row ? $row->website_tpv : null);

        $data = array(
            'website_tpv' => $website,
        );

        if ($row) {
            $result = $wpdb->update("{$wpdb->prefix}lars_pos_keys", $data, ['id' => $row->id]);
        } else {
            $result = $wpdb->insert("{$wpdb->prefix}lars_pos_keys", $data);
        }

        $json = json_encode([
            'result'    => $result,
        ]);

        echo $json;
        wp_die();
    }
    wp_die();
}


function lars_pos_ajax_categories_prices()
{
    check_ajax_referer('data_security', 'nonce');
    if (current_user_can('manage_options')) {

        global $wpdb;
        $keys = getKeys($wpdb);
        print_r($keys);
        if ($keys) {

            $woocommerce = new Client(
                $keys->website,
                $keys->consumer_key,
                $keys->consumer_secret,
                ['wp_api' => true, 'version' => 'wc/v3', 'verify_ssl' => false]
            );

            $percentage = isset($_POST['percentage']) ? sanitize_text_field($_POST['percentage']) : null;
            $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : null;
            $percentage = round((float) $percentage);
            if ($percentage >= 0 && $percentage <= 100) {
                $query = "SELECT * FROM {$wpdb->prefix}" . TABLE_CATEGORIES . " WHERE api_category_id={$category} LIMIT 1;";
                $row = $wpdb->get_row($query);
                if ($row) {
                    $result = $wpdb->update("{$wpdb->prefix}" . TABLE_CATEGORIES, ['increase' => $percentage], ['id' => $row->id]);

                    $queryUpdate = "UPDATE {$wpdb->prefix}" . TABLE_PRODUCTS . " SET increase={$percentage} , type_increase=1 WHERE categories LIKE '%,{$category},%' AND type_increase != 0;";
                    $wpdb->query($queryUpdate);

                    $query = "SELECT * FROM {$wpdb->prefix}" . TABLE_PRODUCTS . " WHERE (w_product_id IS NOT NULL) AND (categories LIKE '%,{$category},%');";
                    $products = $wpdb->get_results($query);

                    if (count($products)) {
                        $wc_products =  lars_pos_array_update_price($products);
                        foreach ($wc_products['variable'] as $key => $data) {
                            wcStoreVariants($woocommerce, $key, $data);
                        }
                        foreach ($wc_products['simple'] as $key => $data) {
                            wcUpdateProduct($woocommerce, $key, $data);
                        }
                        $json = json_encode([
                            'status' => 'OK',
                        ]);
                        echo $json;
                        wp_die();
                    }
                }
            }
        }

        $json = json_encode([
            'status' => 'ERROR',
        ]);
        echo $json;
        wp_die();
    }
    wp_die();
}
function lars_pos_sanitize_text_field($field, $previous)
{
    if (isset($field)) {
        return sanitize_text_field($field);
    }
    return $previous;
}

function check_percentage($id_categorie)
{

    global $wpdb;
    $query = "SELECT increase FROM {$wpdb->prefix}lars_pos_categories WHERE api_category_id = {$id_categorie}";
    $data = $wpdb->get_results($query);

    return $data[0];
}


function lars_pos_ajax_change_configsales()
{
    check_ajax_referer('data_security', 'nonce');
    if (current_user_can('manage_options')) {
        global $wpdb;
        $query = "SELECT * FROM {$wpdb->prefix}lars_pos_config_sales ORDER BY id ASC LIMIT 1;";
        $row = $wpdb->get_row($query);
       
        $factura       = lars_pos_sanitize_text_field($_POST['factura'],  $row ? $row->factura : null);
        $impuesto      = lars_pos_sanitize_text_field($_POST['impuesto'],  $row ? $row->impuesto : null);
        $motivo        = lars_pos_sanitize_text_field($_POST['motivo_traslado'], $row ? $row->motivo_traslado : null);
        $peso          = lars_pos_sanitize_text_field($_POST['peso'], $row ? $row->peso_total : null);
        $trasbordo     = lars_pos_sanitize_text_field($_POST['trasbordo'], $row  ? $row->trasbordo : null);
        $modalidad     = lars_pos_sanitize_text_field($_POST['modalidad_transporte'], $row ? $row->modalidad_transporte : null);
        $id_transporte = lars_pos_sanitize_text_field($_POST['id_transporte'], $row ? $row->id_transport : null);
        $transportista = lars_pos_sanitize_text_field($_POST['transportista'], $row ? $row->nombre_transportista : null);

        $data = array(
            'factura'              =>  $factura,
            'impuesto'             => $impuesto,
            'motivo_traslado'      => $motivo,
            'peso_total'           => $peso,
            'trasbordo'            => $trasbordo,
            'modalidad_transporte' => $modalidad,
            'id_transport'         => $id_transporte,
            'nombre_transportista' => $transportista,
            
        );

        if ($row) {
            $result = $wpdb->update("{$wpdb->prefix}lars_pos_config_sales", $data, ['id' => $row->id]);
        } else {
            $result = $wpdb->insert("{$wpdb->prefix}lars_pos_config_sales", $data);
        }

        $json = json_encode([
            'result'    => $result,
        ]);       
        wp_die();
    }
    wp_die();
}


function lars_pos_change_config_warehouse(){
    check_ajax_referer('data_security', 'nonce');
    if (current_user_can('manage_options')) {

        global $wpdb;
        $query = "SELECT * FROM {$wpdb->prefix}lars_pos_keys ORDER BY id ASC LIMIT 1;";
        $row = $wpdb->get_row($query);
       
        $factura = lars_pos_sanitize_text_field($_POST['warehouse'],  $row ? $row->id_warehouse : null);
       
        $data = array('id_warehouse' => $factura);

        if ($row) {
            $result = $wpdb->update("{$wpdb->prefix}lars_pos_keys", $data, ['id' => $row->id]);
        } else {
            $result = $wpdb->insert("{$wpdb->prefix}lars_pos_keys", $data);
        }

        $json = json_encode([
            'result'    => $result,
        ]);       
        wp_die();
    }
    wp_die();
}

add_action('wp_ajax_lars_pos_change_keys', 'lars_pos_ajax_change_keys');
add_action('wp_ajax_lars_pos_change_config_warehouse', 'lars_pos_change_config_warehouse');
add_action('wp_ajax_lars_pos_categories_prices', 'lars_pos_ajax_categories_prices');

add_action('wp_ajax_lars_pos_search_product', 'lars_pos_ajax_search_product');
add_action('wp_ajax_lars_pos_products_prices', 'lars_pos_ajax_products_prices');
add_action('wp_ajax_lars_pos_chane_config_sales', 'lars_pos_ajax_change_configsales');
