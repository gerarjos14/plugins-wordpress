<?php

define('IVA_COLOMBIA', '0.19');

// Funcion para debuguear codigo
function write_log ( $log )  {
    if ( true === WP_DEBUG ) {
        if ( is_array( $log ) || is_object( $log ) ) {
            error_log( print_r( $log, true ) );
        } else {
            error_log( $log );
        }
    }
}
// Funcion para obtener las credenciales
function getKeys($wpdb){
    $query = "SELECT * FROM {$wpdb->prefix}".TABLE_KEYS." ORDER BY id ASC LIMIT 1;";
    return $wpdb->get_row( $query );
}


function db_insert_rows($row_arrays = array(), $wp_table_name, $update = false, $primary_key = null) {
	global $wpdb;
    $wp_table_name = $wpdb->prefix . $wp_table_name;
	$wp_table_name = esc_sql($wp_table_name);
	// Setup arrays for Actual Values, and Placeholders
	$values        = array();
	$place_holders = array();
	$query         = "";
	$query_columns = "";
	
	$query .= "INSERT INTO `{$wp_table_name}` (";
    $i = 0;
	foreach ($row_arrays as $count => $row_array) {
        
		foreach ($row_array as $key => $value) {
			if ($i == 0) {
				if ($query_columns) {
					$query_columns .= ", " . $key . "";
				} else {
					$query_columns .= "" . $key . "";
				}
			}
			
			$values[] = $value;
			
			$symbol = "%s";
			if (is_numeric($value)) {
				if (is_float($value)) {
					$symbol = "%f";
				} else {
					$symbol = "%d";
				}
			}
			if (isset($place_holders[$i])) {
				$place_holders[$i] .= ", '$symbol'";
			} else {
				$place_holders[$i] = "( '$symbol'";
			}
		}
		// mind closing the GAP
		$place_holders[$i] .= ")";
        $i++;
	}
	
	$query .= " $query_columns ) VALUES ";
	
	$query .= implode(', ', $place_holders);
	
	if ($update) {
		$update = " ON DUPLICATE KEY UPDATE ";
		// $update = " ON DUPLICATE KEY UPDATE $primary_key=VALUES( $primary_key ),";
		$cnt    = 0;
		foreach (array_values($row_arrays)[0] as $key => $value) {
			if($key != $primary_key){
                if ($cnt == 0) {
                    $update .= "$key=VALUES($key)";
                    $cnt = 1;
                } else {
                    $update .= ", $key=VALUES($key)";
                }
            }            
		}
		$query .= $update;
	}
	
	$sql = $wpdb->prepare($query, $values);
    
	if ($wpdb->query($sql)) {
		return true;
	} else {
		return false;
	}
}
function resolvePrice($price, $increase, $type_increase){

    $result = $price;
    if(!empty($increase)){
        if($type_increase){
            // POR VALOR 1 ES PORCENTAJE DE UTILIDAD
			$result = round($result / ( (100 - $increase) / 100));
        }else{
            // POR VALOR 0 ES SUMA            
            $increase = floatval(bcdiv($increase, 100, 2));
            $a = $result;
            $result  = $result + ($result * ($increase));
            
        }
    }
    //return $result;
    
    return ($result); //se incluye el IVA 
}
/**
 * FUNCTIONES DE CONSULTA A LA API EXTERNA
 */
function apiGetProduct($id){
    $urlAPI = API_URL . "api/api_categories/".WAREHOUSE."/product/{$id}";
    $response = wp_remote_request($urlAPI, ['method' => 'GET']);
    $result = json_decode(wp_remote_retrieve_body($response), true);    
    return $result;
}

function wcStoreVariants($woocommerce, $id, $data){
    return $woocommerce->post("products/{$id}/variations/batch", $data);
}


function dbGetCategoriesById($categories){
    global $wpdb;
    $response = array();
    if(count($categories)){
        foreach ($categories as $key => $value) {
            if($key == 0 ){
                $parameters = $value;
            }else{
                $parameters .= ','.$value;
            }
        }
        if($parameters){
            $query = "SELECT * FROM {$wpdb->prefix}".TABLE_CATEGORIES." WHERE api_category_id IN ({$parameters});";
            $results = $wpdb->get_results( $query );
            foreach ($results as $row){
                $response[] = ['id' => $row->w_category_id];
            }
        }
    }
    return $response;
}




// Obtener nombre del país a partir de su cód. ISO 3166-2
function countryCodeToCountry($code) {
    $code = strtoupper($code);
    if ($code == 'AF') return 'Afghanistan';
    if ($code == 'AX') return 'Aland Islands';
    if ($code == 'AL') return 'Albania';
    if ($code == 'DZ') return 'Algeria';
    if ($code == 'AS') return 'American Samoa';
    if ($code == 'AD') return 'Andorra';
    if ($code == 'AO') return 'Angola';
    if ($code == 'AI') return 'Anguilla';
    if ($code == 'AQ') return 'Antarctica';
    if ($code == 'AG') return 'Antigua and Barbuda';
    if ($code == 'AR') return 'Argentina';
    if ($code == 'AM') return 'Armenia';
    if ($code == 'AW') return 'Aruba';
    if ($code == 'AU') return 'Australia';
    if ($code == 'AT') return 'Austria';
    if ($code == 'AZ') return 'Azerbaijan';
    if ($code == 'BS') return 'Bahamas the';
    if ($code == 'BH') return 'Bahrain';
    if ($code == 'BD') return 'Bangladesh';
    if ($code == 'BB') return 'Barbados';
    if ($code == 'BY') return 'Belarus';
    if ($code == 'BE') return 'Belgium';
    if ($code == 'BZ') return 'Belize';
    if ($code == 'BJ') return 'Benin';
    if ($code == 'BM') return 'Bermuda';
    if ($code == 'BT') return 'Bhutan';
    if ($code == 'BO') return 'Bolivia';
    if ($code == 'BA') return 'Bosnia and Herzegovina';
    if ($code == 'BW') return 'Botswana';
    if ($code == 'BV') return 'Bouvet Island (Bouvetoya)';
    if ($code == 'BR') return 'Brazil';
    if ($code == 'IO') return 'British Indian Ocean Territory (Chagos Archipelago)';
    if ($code == 'VG') return 'British Virgin Islands';
    if ($code == 'BN') return 'Brunei Darussalam';
    if ($code == 'BG') return 'Bulgaria';
    if ($code == 'BF') return 'Burkina Faso';
    if ($code == 'BI') return 'Burundi';
    if ($code == 'KH') return 'Cambodia';
    if ($code == 'CM') return 'Cameroon';
    if ($code == 'CA') return 'Canada';
    if ($code == 'CV') return 'Cape Verde';
    if ($code == 'KY') return 'Cayman Islands';
    if ($code == 'CF') return 'Central African Republic';
    if ($code == 'TD') return 'Chad';
    if ($code == 'CL') return 'Chile';
    if ($code == 'CN') return 'China';
    if ($code == 'CX') return 'Christmas Island';
    if ($code == 'CC') return 'Cocos (Keeling) Islands';
    if ($code == 'CO') return 'Colombia';
    if ($code == 'KM') return 'Comoros the';
    if ($code == 'CD') return 'Congo';
    if ($code == 'CG') return 'Congo the';
    if ($code == 'CK') return 'Cook Islands';
    if ($code == 'CR') return 'Costa Rica';
    if ($code == 'CI') return 'Cote d\'Ivoire';
    if ($code == 'HR') return 'Croatia';
    if ($code == 'CU') return 'Cuba';
    if ($code == 'CY') return 'Cyprus';
    if ($code == 'CZ') return 'Czech Republic';
    if ($code == 'DK') return 'Denmark';
    if ($code == 'DJ') return 'Djibouti';
    if ($code == 'DM') return 'Dominica';
    if ($code == 'DO') return 'Dominican Republic';
    if ($code == 'EC') return 'Ecuador';
    if ($code == 'EG') return 'Egypt';
    if ($code == 'SV') return 'El Salvador';
    if ($code == 'GQ') return 'Equatorial Guinea';
    if ($code == 'ER') return 'Eritrea';
    if ($code == 'EE') return 'Estonia';
    if ($code == 'ET') return 'Ethiopia';
    if ($code == 'FO') return 'Faroe Islands';
    if ($code == 'FK') return 'Falkland Islands (Malvinas)';
    if ($code == 'FJ') return 'Fiji the Fiji Islands';
    if ($code == 'FI') return 'Finland';
    if ($code == 'FR') return 'France, French Republic';
    if ($code == 'GF') return 'French Guiana';
    if ($code == 'PF') return 'French Polynesia';
    if ($code == 'TF') return 'French Southern Territories';
    if ($code == 'GA') return 'Gabon';
    if ($code == 'GM') return 'Gambia the';
    if ($code == 'GE') return 'Georgia';
    if ($code == 'DE') return 'Germany';
    if ($code == 'GH') return 'Ghana';
    if ($code == 'GI') return 'Gibraltar';
    if ($code == 'GR') return 'Greece';
    if ($code == 'GL') return 'Greenland';
    if ($code == 'Gd') return 'Grenada';
    if ($code == 'GP') return 'Guadeloupe';
    if ($code == 'GU') return 'Guam';
    if ($code == 'GT') return 'Guatemala';
    if ($code == 'GG') return 'Guernsey';
    if ($code == 'GN') return 'Guinea';
    if ($code == 'GW') return 'Guinea-Bissau';
    if ($code == 'GY') return 'Guyana';
    if ($code == 'HT') return 'Haiti';
    if ($code == 'HM') return 'Heard Island and McDonald Islands';
    if ($code == 'VA') return 'Holy See (Vatican City State)';
    if ($code == 'HN') return 'Honduras';
    if ($code == 'HK') return 'Hong Kong';
    if ($code == 'HU') return 'Hungary';
    if ($code == 'IS') return 'Iceland';
    if ($code == 'IN') return 'India';
    if ($code == 'ID') return 'Indonesia';
    if ($code == 'IR') return 'Iran';
    if ($code == 'IQ') return 'Iraq';
    if ($code == 'IE') return 'Ireland';
    if ($code == 'IM') return 'Isle of Man';
    if ($code == 'IL') return 'Israel';
    if ($code == 'IT') return 'Italy';
    if ($code == 'JM') return 'Jamaica';
    if ($code == 'JP') return 'Japan';
    if ($code == 'JE') return 'Jersey';
    if ($code == 'JO') return 'Jordan';
    if ($code == 'KZ') return 'Kazakhstan';
    if ($code == 'KE') return 'Kenya';
    if ($code == 'KI') return 'Kiribati';
    if ($code == 'KP') return 'Korea';
    if ($code == 'KR') return 'Korea';
    if ($code == 'KW') return 'Kuwait';
    if ($code == 'KG') return 'Kyrgyz Republic';
    if ($code == 'LA') return 'Lao';
    if ($code == 'LV') return 'Latvia';
    if ($code == 'LB') return 'Lebanon';
    if ($code == 'LS') return 'Lesotho';
    if ($code == 'LR') return 'Liberia';
    if ($code == 'LY') return 'Libyan Arab Jamahiriya';
    if ($code == 'LI') return 'Liechtenstein';
    if ($code == 'LT') return 'Lithuania';
    if ($code == 'LU') return 'Luxembourg';
    if ($code == 'MO') return 'Macao';
    if ($code == 'MK') return 'Macedonia';
    if ($code == 'MG') return 'Madagascar';
    if ($code == 'MW') return 'Malawi';
    if ($code == 'MY') return 'Malaysia';
    if ($code == 'MV') return 'Maldives';
    if ($code == 'ML') return 'Mali';
    if ($code == 'MT') return 'Malta';
    if ($code == 'MH') return 'Marshall Islands';
    if ($code == 'MQ') return 'Martinique';
    if ($code == 'MR') return 'Mauritania';
    if ($code == 'MU') return 'Mauritius';
    if ($code == 'YT') return 'Mayotte';
    if ($code == 'MX') return 'Mexico';
    if ($code == 'FM') return 'Micronesia';
    if ($code == 'MD') return 'Moldova';
    if ($code == 'MC') return 'Monaco';
    if ($code == 'MN') return 'Mongolia';
    if ($code == 'ME') return 'Montenegro';
    if ($code == 'MS') return 'Montserrat';
    if ($code == 'MA') return 'Morocco';
    if ($code == 'MZ') return 'Mozambique';
    if ($code == 'MM') return 'Myanmar';
    if ($code == 'NA') return 'Namibia';
    if ($code == 'NR') return 'Nauru';
    if ($code == 'NP') return 'Nepal';
    if ($code == 'AN') return 'Netherlands Antilles';
    if ($code == 'NL') return 'Netherlands the';
    if ($code == 'NC') return 'New Caledonia';
    if ($code == 'NZ') return 'New Zealand';
    if ($code == 'NI') return 'Nicaragua';
    if ($code == 'NE') return 'Niger';
    if ($code == 'NG') return 'Nigeria';
    if ($code == 'NU') return 'Niue';
    if ($code == 'NF') return 'Norfolk Island';
    if ($code == 'MP') return 'Northern Mariana Islands';
    if ($code == 'NO') return 'Norway';
    if ($code == 'OM') return 'Oman';
    if ($code == 'PK') return 'Pakistan';
    if ($code == 'PW') return 'Palau';
    if ($code == 'PS') return 'Palestinian Territory';
    if ($code == 'PA') return 'Panama';
    if ($code == 'PG') return 'Papua New Guinea';
    if ($code == 'PY') return 'Paraguay';
    if ($code == 'PE') return 'Peru';
    if ($code == 'PH') return 'Philippines';
    if ($code == 'PN') return 'Pitcairn Islands';
    if ($code == 'PL') return 'Poland';
    if ($code == 'PT') return 'Portugal, Portuguese Republic';
    if ($code == 'PR') return 'Puerto Rico';
    if ($code == 'QA') return 'Qatar';
    if ($code == 'RE') return 'Reunion';
    if ($code == 'RO') return 'Romania';
    if ($code == 'RU') return 'Russian Federation';
    if ($code == 'RW') return 'Rwanda';
    if ($code == 'BL') return 'Saint Barthelemy';
    if ($code == 'SH') return 'Saint Helena';
    if ($code == 'KN') return 'Saint Kitts and Nevis';
    if ($code == 'LC') return 'Saint Lucia';
    if ($code == 'MF') return 'Saint Martin';
    if ($code == 'PM') return 'Saint Pierre and Miquelon';
    if ($code == 'VC') return 'Saint Vincent and the Grenadines';
    if ($code == 'WS') return 'Samoa';
    if ($code == 'SM') return 'San Marino';
    if ($code == 'ST') return 'Sao Tome and Principe';
    if ($code == 'SA') return 'Saudi Arabia';
    if ($code == 'SN') return 'Senegal';
    if ($code == 'RS') return 'Serbia';
    if ($code == 'SC') return 'Seychelles';
    if ($code == 'SL') return 'Sierra Leone';
    if ($code == 'SG') return 'Singapore';
    if ($code == 'SK') return 'Slovakia (Slovak Republic)';
    if ($code == 'SI') return 'Slovenia';
    if ($code == 'SB') return 'Solomon Islands';
    if ($code == 'SO') return 'Somalia, Somali Republic';
    if ($code == 'ZA') return 'South Africa';
    if ($code == 'GS') return 'South Georgia and the South Sandwich Islands';
    if ($code == 'ES') return 'Spain';
    if ($code == 'LK') return 'Sri Lanka';
    if ($code == 'SD') return 'Sudan';
    if ($code == 'SR') return 'Suriname';
    if ($code == 'SJ') return 'Svalbard & Jan Mayen Islands';
    if ($code == 'SZ') return 'Swaziland';
    if ($code == 'SE') return 'Sweden';
    if ($code == 'CH') return 'Switzerland, Swiss Confederation';
    if ($code == 'SY') return 'Syrian Arab Republic';
    if ($code == 'TW') return 'Taiwan';
    if ($code == 'TJ') return 'Tajikistan';
    if ($code == 'TZ') return 'Tanzania';
    if ($code == 'TH') return 'Thailand';
    if ($code == 'TL') return 'Timor-Leste';
    if ($code == 'TG') return 'Togo';
    if ($code == 'TK') return 'Tokelau';
    if ($code == 'TO') return 'Tonga';
    if ($code == 'TT') return 'Trinidad and Tobago';
    if ($code == 'TN') return 'Tunisia';
    if ($code == 'TR') return 'Turkey';
    if ($code == 'TM') return 'Turkmenistan';
    if ($code == 'TC') return 'Turks and Caicos Islands';
    if ($code == 'TV') return 'Tuvalu';
    if ($code == 'UG') return 'Uganda';
    if ($code == 'UA') return 'Ukraine';
    if ($code == 'AE') return 'United Arab Emirates';
    if ($code == 'GB') return 'United Kingdom';
    if ($code == 'US') return 'United States of America';
    if ($code == 'UM') return 'United States Minor Outlying Islands';
    if ($code == 'VI') return 'United States Virgin Islands';
    if ($code == 'UY') return 'Uruguay, Eastern Republic of';
    if ($code == 'UZ') return 'Uzbekistan';
    if ($code == 'VU') return 'Vanuatu';
    if ($code == 'VE') return 'Venezuela';
    if ($code == 'VN') return 'Vietnam';
    if ($code == 'WF') return 'Wallis and Futuna';
    if ($code == 'EH') return 'Western Sahara';
    if ($code == 'YE') return 'Yemen';
    if ($code == 'XK') return 'Kosovo';
    if ($code == 'ZM') return 'Zambia';
    if ($code == 'ZW') return 'Zimbabwe';
    return '';
}    

/**
 * Obtener nombre de la ciudad a partir del código del país y de la ciudad
 * SOLO APLICABLE CON LOS DEPARTAMENTOS DE PERÚ
 */
function provinceCode($code_country, $code_province){
    $code = strtoupper($code_country);
    
    if ($code == 'PE'){
        if($code_province == 'CAL') return 'El Callao';
		if($code_province == 'LMA') return 'Municipalidad Metropolitana de Lima';
		if($code_province == 'AMA') return 'Amazonas';
		if($code_province == 'ANC') return 'Ancash';
		if($code_province == 'APU') return 'Apurímac';
		if($code_province == 'ARE') return 'Arequipa';
		if($code_province == 'AYA') return 'Ayacucho';
		if($code_province == 'CAJ') return 'Cajamarca';
		if($code_province == 'CUS') return 'Cusco';
		if($code_province == 'HUV') return 'Huancavelica';
		if($code_province == 'HUC') return 'Huánuco';
		if($code_province == 'ICA') return 'Ica';
		if($code_province == 'JUN') return 'Junín';
		if($code_province == 'LAL') return 'La Libertad';
		if($code_province == 'LAM') return 'Lambayeque';
		if($code_province == 'LIM') return 'Lima';
		if($code_province == 'LOR') return 'Loreto';
		if($code_province == 'MDD') return 'Madre de Dios';
		if($code_province == 'MOQ') return 'Moquegua';
		if($code_province == 'PAS') return 'Pasco';
		if($code_province == 'PIU') return 'Piura';
		if($code_province == 'PUN') return 'Puno';
		if($code_province == 'SAM') return 'San Martín';
		if($code_province == 'TAC') return 'Tacna';
		if($code_province == 'TUM') return 'Tumbes';
		if($code_province == 'UCA') return 'Ucayali';
    }
    
    return '';
}

/**
 * @param string $message
 * @param int fk $order_id
 * @author
 * Función write_order_notes
 * 
 * Acá se crearán todas las notas relacionadas a los pedidos que hagan los usuarios en el sistema.
 * Se informará:
 * - Actualización de stock, esto en función del cambio de estado del pedido.
 * - Creación de factura o boleta electrónica del pedido.
 * 
 */
function write_order_notes($message, $order_id){
    global $wpdb;
    $table_comment = $wpdb->prefix . 'comments';
    $table_commentmeta = $wpdb->prefix . 'commentmeta';

    // ingreso nueva nota del pedido
    $wpdb->insert($table_comment, array(
        'comment_post_ID'      => $order_id,
        'comment_author'       => 'admin', // nombre del usuario admin
        'comment_author_email' => 'administrador@demo.com', // email del usuario admin
        'comment_date'         => date('Y-m-d G:i:s', time()),
        'comment_date_gmt'     => date('Y-m-d G:i:s', time()),
        'comment_content'      => $message,
        'comment_approved'     => '1',
        'comment_agent'        => 'WooCommerce',
        'comment_type'         => 'order_note',
        'comment_parent'       => '0',
        'user_id'              => '0',
    ));

    $id_comment = $wpdb->get_var('SELECT comment_ID FROM ' . $wpdb->prefix . 'comments' . ' ORDER BY comment_ID DESC LIMIT 1');

    // relaciono comentario en la tabla comments meta
    $wpdb->insert($table_commentmeta, array(
        'comment_id' => $id_comment,
        'meta_key'   => 'is_customer_note',
        'meta_value' => '1',
    ));
}

/**
 * Función validarInputRUC
 * Paso 1 - verifico si el RUC empieza con 10, 15, 16, 17 0 20
 * Paso 2 - verifico si el RUC tiene como extensión 11 dígitos
 * @author 
 */
function validarInputRUC($input_ruc){

    // PASO 1
    $validate_pass_1 = substr($input_ruc, 0, 2);
    if($validate_pass_1 == 10 || $validate_pass_1 == 15 || $validate_pass_1 == 16 || $validate_pass_1 == 17 || $validate_pass_1 == 20){
        
        // PASO 2
        $extension = strlen((string)$input_ruc);
        if($extension !=  11 ){
            write_log('Error en RUC (PASO 2)');
            return -2;
        }else{
            return 0;
        }
        
    }else{
        // ERROR PASO 1
        write_log('Error en RUC (PASO 1)');
        return -1;
    }
}