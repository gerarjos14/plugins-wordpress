<?php

function lars_pos_array_product_variations($product, $apiProduct, $woocommerce, $attr, $terms, $price){
    $options = array();
    $variations = array();
    foreach ($apiProduct as $value){
        $stock = $value['totalDisponible'];
        if(!($stock > 0)){
            $stock = 0;
        }
        $indice = strtolower($value['color']);
        if(!isset($terms[$indice])){            
            $term = wcStoreTerms($woocommerce, $attr, $value['color']);
            $terms[$indice] = [
                'id' => $term->id,
                'name' => $term->name,
                'slug' => $term->slug,
            ];
        }
        array_push($options, $value['color']);        
        $variations['create'][] = [
            'regular_price'  => strval($price),                
            'manage_stock'   => true,
            'stock_quantity' => $stock, 
            'stock_status'   => ($stock > 0) ? 'instock' : 'outofstock',
            'attributes'     => [
                [
                    'id'             => $attr,
                    'option'         => $terms[$indice]['name'],
                ],
            ], 
        ];
    }
    $categories = explode(',',substr($product->categories, 1, -1));    
    $data = [
        'type'           => 'variable',
        'manage_stock'   => false,
        'name'           => $product->nombre,
        'description'    => $product->descripcion,
        'categories'     => dbGetCategoriesById($categories),
        'attributes'     => [
            [
                'id' => $attr,
                'visible' => true,
                'variation' => true,
                'options' => $options,
            ],
        ],
    ];    
    return [$data, $variations];
}
function lars_pos_array_update_price($products){
    $response = array();
    $response['variable'] = [];
    $response['simple'] = [];
    foreach($products as $product){

        $precio = resolvePrice($product->precio, $product->increase, $product->type_increase);
        
        $variations = array();
        
        if($product->lumise){
            $data = [
                'regular_price' => strval($precio),
            ];
            $response['simple'][$product->w_product_id] = $data;
        }else{
            if(!empty($product->variations)){
                $dbVariations = explode(',', $product->variations);       
                foreach($dbVariations as $dbVar){
                    $variation = explode(':', $dbVar);
                    $id = $variation[0];
                    $variations['update'][] = [
                        'id'             => $id,
                        'regular_price'  => strval($precio),         
                    ];                
                }
            }
            $response['variable'][$product->w_product_id] = $variations;
        } 
    }
    return $response;
}
function lars_pos_product_update_price($product, $newPrice){
    $variations = array();
    if(isset($product->variations)){
        $dbVariations = explode(',', $product->variations);       
        foreach($dbVariations as $dbVar){
            $variation = explode(':', $dbVar);
            $id = $variation[0];
            $variations['update'][] = [
                'id'             => $id,
                'regular_price'  => $newPrice,              
            ];                
        }
    }
    return $variations;
}

function wcUpdateProduct($woocommerce, $id, $data){
    return $woocommerce->put("products/{$id}", $data);
}

function wcGetProductVariations($woocommerce, $id){
    return $woocommerce->get("products/{$id}/variations");
}