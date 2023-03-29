<?php

class SC_Helpers {
	/**
	 * Objeto wpdb
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $db @global $wpdb
	 */
    private $db;

    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
    }

	function write_log ( $log )  {
		if ( true === WP_DEBUG ) {
			if ( is_array( $log ) || is_object( $log ) ) {
				error_log( print_r( $log, true ) );
			} else {
				error_log( $log );
			}
		}
	}
	/**
	 *  Contruccion de arrays
	 */
	public function make_array_for_db($results)
	{
		// Precio en string porque asi lo recibe woocommerce
		$products = array();
		foreach ($results as $key => $value) {
			$product = [
				"siigo_id" 	 	 => $value["id"],
				"code"			 => $value["code"],
				"name"   		 => $value["name"],
				"price"   		 => "1",
				"stock_control"  => $value["stock_control"] ? 1 : 0,
				"description"	 => isset($value["description"]) ? $value["description"] : '',
				"stock_quantity" => $value["available_quantity"],
			];
			array_push($products, $product);
		}
		return $products;
	}

	public function make_array_for_wc($products)
	{
		$finalProducts = array(); //Acumula los productos de a 100 
		$array_products = array_chunk($products, 50);
		for ($i=0; $i < count($array_products); $i++) { 
			$createProducts = array();
			$updateProducts = array();
			foreach ($array_products[$i] as $product) {
				if(isset($product->woocommerce_id)){
					$data = [
						'id' 				=> $product->woocommerce_id,
						'type' 				=> 'simple',
						'regular_price' 	=> $product->price,
						'name' 				=> $product->name,
						'short_description'	=> $product->description,
						'description'		=> $product->description,
					];
					array_push($updateProducts, $data);
				}else{
					$data = [
						'siigo_id'	 => $product->siigo_id,	
						'name' 				=> $product->name,
						'description'		=> $product->description,
						'short_description'	=> $product->description,
						'type' 				=> 'simple',
						'regular_price' 	=> intval($product->price),

					];
					array_push($createProducts, $data);
				}
			}
			if(!empty($updateProducts)){ $finalProducts[$i]["update"] = $updateProducts; }
			if(!empty($createProducts)){ $finalProducts[$i]["create"] = $createProducts; }
		}

		return $finalProducts;
	}

	public function make_array_new_products_for_db($newProducts)
	{
		$productsForDb = array();
		foreach($newProducts as $product) {
			for ($i=0; $i < count($product[0]); $i++) { 
				$wcProduct = $product[0][$i];
				$dbProduct = $product[1][$i];
				
				$this->write_log($dbProduct);

				$data = [
					'siigo_id' => $dbProduct["siigo_id"],
					'woocommerce_id' => $wcProduct->id
				];
				array_push($productsForDb, $data);
			}
		}
		return $productsForDb;
	}

	public function make_array_new_customer_for_siigo($order)
	{
        $user = $order->get_user();
		if(empty($user)) {
		    throw new Exception("Order #". $order->get_id(). ": Relizada sin usuario.");
		}
		$country = $order->get_billing_country();
		if($country != "CO"){
		    throw new Exception("Order #". $order->get_id(). ": Pais incorrecto.");			 
		}

		$cedula = get_post_meta($order->get_id(), 'Cedula', true);
		$tipo_identificacion = get_post_meta($order->get_id(), 'TIPO_Identificacion', true);
        
		$state = $order->get_billing_state();
        $city = $order->get_billing_city();

		$stateId = $this->get_state($state);
        $placeId = $this->get_place($state, $city);

		$lastName   = $order->get_billing_last_name();
        $firstName  = $order->get_billing_first_name();

		$email      = $order->get_billing_email();
        $address    = $order->get_billing_address_1();
        $phone    	= $order->get_billing_phone();

		if( is_null($stateId) || is_null($placeId) ){
		    throw new Exception("Order #". $order->get_id(). ": Estado o ciudad incorrectos.");
		}
        
		$customer = [			
			"identification"      => $cedula,
			"tipo_identificacion" => $tipo_identificacion,
			"first_name"	      => $firstName,
			"last_name" 	      => $lastName,
			"phone" 		      => $phone,
			"address"             => $address,
			"state_code" 	      => $stateId,
			"city_code" 	      => $placeId,
			"email" 		      => $email ,
		];
		return $customer;
	}

	public function make_body_factura($order, $seller){
		
		$date = date("Y-m-d");
		//customer id
		$customer_id = get_post_meta($order->get_id(), 'Cedula', true);

		$sumatoria_items = 0.0;
		$order_items = $order->get_items();

		foreach($order_items as $products){
		    
			$product_id = $products->get_product_id();			

			$producto_codeSIIGO = $this->searc_code_byName($products->get_name());

			$producto_codeSIIGO = $producto_codeSIIGO->code;
			
			$cantidad = $products->get_quantity();
			
			$valor_base = sprintf('%0.2f', ($products->get_total() - 0));

			$PORCENTAJE_IVA = 0.19; // EN BASE A DOCUMENTACIÓN -> 19%

			$IVA = sprintf('%0.2f', ($valor_base * $PORCENTAJE_IVA));

			$total_item = sprintf('%0.2f', ($valor_base + $IVA));

			$precio_unitario = ($valor_base / $cantidad);

			$items[] = [
				'code'        => ''.$producto_codeSIIGO.'', //string
				'description' => $products->get_name(), //string
				'quantity'    => $cantidad, //number
				'price'       => $precio_unitario, //number
				'discount'    => 0,//REVISAR //number, es porcentaje de descuento 
				'taxes'		  => [
					[
						'id' => 13156
					]
				]
			];
			$sumatoria_items += $total_item;
		}
		
		$payments_id = 5636; //revisar 
		$payments_value = $sumatoria_items;

		$factura = [
			"date"           => $date,
			"customer"       => $customer_id,
			"seller"         => $seller,
			"items"          => $items,
			"payments_id"    => $payments_id,
			"payments_value" => $payments_value,
		];
		return $factura;
	}


	/**
	 * Funcion para consultar y obtener codigo de siigo del producto a partir del id 
	 * de woocommerce 
	 * @author
	 */
	public function search_code_byIDW($id_woocommerce){
		$query = "SELECT * FROM " . SC_TABLE_PRODUCTS . 
		" WHERE woocommerce_id=".$id_woocommerce." LIMIT 1;";
		$row = $this->db->get_row( $query );		
		return $row;
	}

	public function searc_code_byName($name){
		
		$query = "SELECT * FROM " . SC_TABLE_PRODUCTS . " WHERE name='".$name."' LIMIT 1;";
		$row = $this->db->get_row( $query );		
		return $row;
	}
	
	/**
	 * Helpers de interaccion con la Base de datos
	 */
	public function get_keys_db()
	{
		$query = "SELECT * FROM " . SC_TABLE . " ORDER BY id ASC LIMIT 1;";
		$row = $this->db->get_row( $query );
		return $row;
	}

	public function set_keys_db($website, $username, $access_key, $consumer_key, $consumer_secret)
	{
		$this->db->insert(SC_TABLE, [
			"website" => $website,
			"username" => $username,
			"access_key" => $access_key,
			"consumer_key" => $consumer_key,
			"consumer_secret" => $consumer_secret,
		]);
		return true;
	}

	public function update_keys_db($website, $username, $access_key, $consumer_key, $consumer_secret, $id)
	{		
		$this->db->update(SC_TABLE, [
			"website" => $website,
			"username" => $username,
			"access_key" => $access_key,
			"consumer_key" => $consumer_key,
			"consumer_secret" => $consumer_secret,
		], [ 'id' => $id ]);
		return true;
	}

	public function get_access_token()
	{
		$query = "SELECT access_token FROM " . SC_TABLE . " ORDER BY id ASC LIMIT 1;";
		$row = $this->db->get_row( $query );
		if(empty($row) || empty($row->access_token)){
		    throw new Exception("No hemos encontrado un token de acceso");
		}
		return $row->access_token;
	}

	public function set_access_token($access_token, $id)
	{
		$this->db->update(SC_TABLE, [
			"access_token" => $access_token,
		], [ 'id' => $id ]);
		return true;	
	}

	// PRODUCTS
	public function get_products_db()
	{
		$query = "SELECT * FROM ".SC_TABLE_PRODUCTS.";";
		return $this->db->get_results( $query );
	}

	// CUSTOMERS
	public function get_customer($id)
	{
		$query = "SELECT * FROM " . SC_TABLE_CUSTOMERS . 
		" WHERE woocommerce_id=".$id." ORDER BY id ASC LIMIT 1;";
		$row = $this->db->get_row( $query );		
		return $row;
	}

    public function db_insert_rows($row_arrays = array(), $wp_table_name, $update, $primary_key = null) {
		//$wp_table_name = $this->db->prefix . $wp_table_name;
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
		
		$sql = $this->db->prepare($query, $values);
		
		if ($this->db->query($sql)) {
			return true;
		} else {
			return false;
		}
	}

	private function get_state($index)
	{
		$states = [
			'AMZ' => '91',
			'ANT' => '5',
			'ARU' => '81',
			'ATL' => '8',
			'BDC' => '11',
			'BOL' => '13',
			'BOY' => '15',
			'CAL' => '17',
			'CAQ' => '18',
			'CAS' => '85',
			'CAU' => '19',
			'CES' => '20',
			'CHOC' => '27',
			'COR' => '23',
			'CUN' => '25',
			'GUA' => '94',
			'GUV' => '95',
			'HUI' => '41',
			'GUJ' => '44' ,
			'MAG' => '47',
			'MET' => '50',
			'NAR' => '52',
			'NOR' => '54',
			'PUT' => '86',
			'QUI' => '63',
			'RIS' => '66',
			'SAP' => '88',
			'SAN' => '68',
			'SUC' => '70',
			'TOL' => '73',
			'VAC' => '76',
			'VAU' => '97',
			'VIC' => '99'
		];
		return isset($states[$index]) ? $states[$index] : null;
	}

	private function get_place($state, $index)
	{
		$places = array(
			'AMZ' => array(
				"El Encanto" => '91263',
				"La Chorrera" => '91405', 
				"La Pedrera" => '91407',
				"La Victoria" => '91430',
				"Leticia" => '91001',
				"Miriti - Paraná" => '91460',
				"Puerto Alegría" => '91530',
				"Puerto Arica" => '91536',
				"Puerto Nariño" => '91540',
				"Puerto Santander" => '91669',
				"Tarapacá" => '91798',
			),
			'ANT' => array(
				"Abejorral" => '5002',
				"Abriaquí" => '5004',
				"Alejandría" => '5021',
				"Amagá" => '5030',
				"Amalfi" => '5031',
				"Andes" => '5034',
				"Angelópolis" => '5036',
				"Angostura" => '5038',
				"Anorí" => '5040',
				"Anza" => '5044',
				"Apartadó" => '5045',
				"Arboletes" => '5051',
				"Argelia" => '5055',
				"Armenia" => '5059',
				"Barbosa" => '5079',
				"Bello" => '5088',
				"Belmira" => '5086',
				"Betania" => '5091',
				"Betulia" => '5093',
				"Briceño" => '5107',
				"Buriticá" => '5113',
				"Cáceres" => '5120',
				"Caicedo" => '5125',
				"Caldas" => '5129',
				"Campamento" => '5134',
				"Cañasgordas" => '5138',
				"Caracolí" => '5142',
				"Caramanta" => '5145',
				"Carepa" => '5147',
				"Carolina" => '5150',
				"Caucasia" => '5154',
				"Chigorodó" => '5172',
				"Cisneros" => '5190',
				"Ciudad Bolívar" => '5101',
				"Cocorná" => '5197',
				"Concepción" => '5206',
				"Concordia" => '5209',
				"Copacabana" => '5212',
				"Dabeiba" => '5234',
				"Don Matías" => '5237',
				"Ebéjico" => '5240',
				"El Bagre" => '5250',
				"El Carmen De Viboral" => '5148',
				"El Santuario" => '5697',
				"Entrerrios" => '5264',
				"Envigado" => '5266',
				"Fredonia" => '5282',
				"Frontino" => '5284',
				"Giraldo" => '5306',
				"Girardota" =>'5308',
				"Gómez Plata" => '5310',
				"Granada" => '5313',
				"Guadalupe" => '5315',
				"Guarne" => '5318',
				"Guatape" => '5321',
				"Heliconia" => '5347',
				"Hispania" => '5353',
				"Itagui" => '5360',
				"Ituango" => '5361',
				"Jardín" => '5364',
				"Jericó" => '5368',
				"La Ceja" => '5376',
				"La Estrella" => '5380',
				"La Pintada" => '5390',
				"La Unión" => '5400',
				"Liborina" => '5411',
				"Maceo" => '5425',
				"Marinilla" => '5440',
				"Medellín" => '5001',
				"Montebello" => '5467',
				"Murindó" => '5475',
				"Mutatá" => '5480',
				"Nariño" => '5483',
				"Nechí" => '5495',
				"Necoclí" => '5490',
				"Olaya" => '5501',
				"Peñol" => '5541',
				"Peque" => '5543',
				"Pueblorrico" => '5576',
				"Puerto Berrío" => '5579',
				"Puerto Nare" => '5585',
				"Puerto Triunfo" => '5591',
				"Remedios" => '5604',
				"Retiro" => '5607',
				"Rionegro" => '5615',
				"Sabanalarga" => '5628',
				"Sabaneta" => '5631',
				"Salgar" => '5642',
				"San Andrés" => '5647',
				"San Carlos" => '5649',
				"San Francisco" => '5652',
				"San Jerónimo" => '5656',
				"San José De La Montaña" => '5658',
				"San Juan De Urabá" => '5659',
				"San Luis" => '5660',
				"San Pedro" => '5664',
				"San Pedro De Uraba" => '5665',
				"San Rafael" => '5667',
				"San Roque" => '5670',
				"San Vicente" => '5674',
				"Santa Bárbara" => '5679',
				"Santa Rosa De Osos" => '5686',
				"Santafé De Antioquia" => '5042',
				"Santo Domingo" => '5690',
				"Segovia" => '5736',
				"Sonson" => '5756',
				"Sopetrán" => '5761',
				"Támesis" => '5789',
				"Tarazá" => '5790',
				"Tarso" => '5792',
				"Titiribí" => '5809',
				"Toledo" => '5819',
				"Turbo" => '5837',
				"Uramita" => '5842',
				"Urrao" => '5847',
				"Valdivia" => '5854',
				"Valparaíso" => '5856',
				"Vegachí" => '5858',
				"Venecia" => '5861',
				"Vigía Del Fuerte" => '5873',
				"Yalí" => '5885',
				"Yarumal" => '5887',
				"Yolombó" => '5890',
				"Yondó" => '5893',
				"Zaragoza" => '5895',
		
			),
			'ARU' => array(
				"Arauca" => '81001',
				"Arauquita" => '81065',
				"Cravo Norte" => '81220',
				"Fortul" => '81300',
				"Puerto Rondón" => '81591',
				"Saravena" => '81736',
				"Tame" => '81794',        
			),
			'ATL' => array(
				"Baranoa" => '8078',
				"Barranquilla" => '8001',
				"Campo De La Cruz" => '8137',
				"Candelaria" => '8141',
				"Galapa" => '8296',
				"Juan De Acosta" => '8372',
				"Luruaco" => '8421',
				"Malambo" => '8433',
				"Manatí" => '8436',
				"Palmar De Varela" => '8520',
				"Piojó" => '8549',
				"Polonuevo" => '8558',
				"Ponedera" => '8560',
				"Puerto Colombia" => '8573',
				"Repelón" => '8606',
				"Sabanagrande" => '8634',
				"Sabanalarga" => '8638',
				"Santa Lucía" => '8675',
				"Santo Tomás" => '8685',
				"Soledad" => '8758',
				"Suan" => '8770',
				"Tubará" => '8832',
				"Usiacurí" => '8849',        
			),
			'BDC' => array(
				"Bogotá" => '11001',
			),
			'BOL' => array(
				"Achí" => '13006',
				"Altos Del Rosario" => '13030',
				"Arenal" => '13042',
				"Arjona" => '13052',
				"Arroyohondo" => '13062',
				"Barranco De Loba" => '13074',
				"Calamar" => '13140',
				"Cantagallo" => '13160',
				"Cartagena" => '13001',
				"Cicuco" => '13188',
				"Clemencia" => '13222',
				"Córdoba" => '13212',
				"El Carmen De Bolívar" => '13244',
				"El Guamo" => '13248',
				"El Peñón" => '13268',
				"Hatillo De Loba" => '13300',
				"Magangué" => '13430',
				"Mahates" => '13433',
				"Margarita" => '13440',
				"María La Baja" => '13442',
				"Mompós" => '13468',
				"Montecristo" => '13458',
				"Morales" => '13473',
				"Pinillos" => '13549',
				"Regidor" => '13580',
				"Río Viejo" => '13600',
				"San Cristóbal" => '13620',
				"San Estanislao" => '13647',
				"San Fernando" => '13650',
				"San Jacinto" => '13654',
				"San Jacinto Del Cauca" => '13655',
				"San Juan Nepomuceno" => '13657',
				"San Martín De Loba" => '13667',
				"San Pablo" => '13670',
				"Santa Catalina" => '13673',
				"Santa Rosa" => '13683',
				"Santa Rosa Del Sur" => '13688',
				"Simití" => '13744',
				"Soplaviento" => '13760',
				"Talaigua Nuevo" => '13780',
				"Tiquisio" => '13810',
				"Turbaco" => '13836',
				"Turbaná" => '13838',
				"Villanueva" => '13873',
				"Zambrano" => '13894',
		
			),
			'BOY' => array(
				"Almeida" => '15022',
				"Aquitania" => '15047',
				"Arcabuco" => '15051',
				"Belén" => '15087',
				"Berbeo" => '15090',
				"Betéitiva" => '15092',
				"Boavita" => '15097',
				"Boyacá" => '15104',
				"Briceño" => '15106',
				"Buenavista" => '15109',
				"Busbanzá" => '15114',
				"Caldas" => '15131',
				"Campohermoso" => '15135',
				"Cerinza" => '15162',
				"Chinavita" => '15172',
				"Chiquinquirá" => '15176',
				"Chíquiza" => '15232',
				"Chiscas" => '15180',
				"Chita" => '15183',
				"Chitaraque" => '15185',
				"Chivatá" => '15187',
				"Chivor" => '15236',
				"Ciénega" => '15189',
				"Cómbita" => '15204',
				"Coper" => '15212',
				"Corrales" => '15215',
				"Covarachía" => '15218',
				"Cubará" => '15223',
				"Cucaita" => '15224',
				"Cuítiva" => '15226',
				"Duitama" => '15238',
				"El Cocuy" => '15244',
				"El Espino" => '15248',
				"Firavitoba" => '15272',
				"Floresta" => '15276',
				"Gachantivá" => '15293',
				"Gameza" => '15296',
				"Garagoa" => '15299',
				"Guacamayas" => '15317',
				"Guateque" => '15322',
				"Guayatá" => '15325',
				"Güicán" => '15332',
				"Iza" => '15362',
				"Jenesano" => '15367',
				"Jericó" => '15368',
				"La Capilla" => '15380',
				"La Uvita" => '15403',
				"La Victoria" => '15401',
				"Labranzagrande" => '15377',
				"Macanal" => '15425',
				"Maripí" => '15442',
				"Miraflores" => '15455',
				"Mongua" => '15464',
				"Monguí" => '15466',
				"Moniquirá" => '15469',
				"Motavita" => '15476',
				"Muzo" => '15480',
				"Nobsa" => '15491',
				"Nuevo Colón" => '15494',
				"Oicatá" => '15500',
				"Otanche" => '15507',
				"Pachavita" => '15511',
				"Páez" => '15514',
				"Paipa" => '15516',
				"Pajarito" => '15518',
				"Panqueba" => '15522',
				"Pauna" => '15531',
				"Paya" => '15533',
				"Paz De Río" => '15537',
				"Pesca" => '15542',
				"Pisba" => '15550',
				"Puerto Boyacá" => '15572',
				"Quípama" => '15580',
				"Ramiriquí" => '15599',
				"Ráquira" => '15600',
				"Rondón" => '15621',
				"Saboyá" => '15632',
				"Sáchica" => '15638',
				"Samacá" => '15646',
				"San Eduardo" => '15660',
				"San José De Pare" => '15664',
				"San Luis De Gaceno" => '15667',
				"San Mateo" => '15673',
				"San Miguel De Sema" => '15676',
				"San Pablo De Borbur" => '15681',
				"Santa María" => '15690',
				"Santa Rosa De Viterbo" => '15693',
				"Santa Sofía" => '15696',
				"Santana" => '15686',
				"Sativanorte" => '15720',
				"Sativasur" => '15723',
				"Siachoque" => '15740',
				"Soatá" => '15753',
				"Socha" => '15757',
				"Socotá" => '15755',
				"Sogamoso" => '15759',
				"Somondoco" => '15761',
				"Sora" => '15762',
				"Soracá" => '15764',
				"Sotaquirá" => '15763',
				"Susacón" => '15774',
				"Sutamarchán" => '15776',
				"Sutatenza" => '15778',
				"Tasco" => '15790',
				"Tenza" => '15798',
				"Tibaná" => '15804',
				"Tibasosa" => '15806',
				"Tinjacá" => '15808',
				"Tipacoque" => '15810',
				"Toca" => '15814',
				"Togüí" => '15816',
				"Tópaga" => '15820',
				"Tota" => '15822',
				"Tunja" => '15001',
				"Tununguá" => '15832',
				"Turmequé" => '15835',
				"Tuta" => '15837',
				"Tutazá" => '15839',
				"Umbita" => '15842',
				"Ventaquemada" => '15861',
				"Villa De Leyva" => '15407',
				"Viracachá" => '15879',
				"Zetaquira" => '15897',
			),
			'CAL' => array(
				"Aguadas" => '17013',
				"Anserma" => '17042',
				"Aranzazu" => '17050',
				"Belalcázar" => '17088',
				"Chinchiná" => '17174',
				"Filadelfia" => '17272',
				"La Dorada" => '17380',
				"La Merced" => '17388',
				"Manizales" => '17001',
				"Manzanares" => '17433',
				"Marmato" => '17442',
				"Marquetalia" => '17444',
				"Marulanda" => '17446',
				"Neira" => '17486',
				"Norcasia" => '17495',
				"Pácora" => '17513',
				"Palestina" => '17524',
				"Pensilvania" => '17541',
				"Riosucio" => '17614',
				"Risaralda" => '17616',
				"Salamina" => '17653',
				"Samaná" => '17662',
				"San José" => '17665',
				"Supía" => '17777',
				"Victoria" => '17867',
				"Villamaría" => '17873',
				"Viterbo" => '17877',
			),
			'CAQ' => array(
				"Albania" => '18029',
				"Belén De Los Andaquies" => '18094',
				"Cartagena Del Chairá" => '18150',
				"Curillo" => '18205',
				"El Doncello" => '18247',
				"El Paujil" => '18256',
				"Florencia" => '18001',
				"La Montañita" => '18410',
				"Milán" => '18460',
				"Morelia" => '18479',
				"Puerto Rico" => '18592',
				"San José Del Fragua" => '18610',
				"San Vicente Del Caguán" => '18753',
				"Solano" => '18756',
				"Solita" => '18785',
				"Valparaíso" => '18860',
			),
			'CAS' => array(
				"Aguazul" => '85010',
				"Chameza" => '85015',
				"Hato Corozal" => '85125',
				"La Salina" => '85136',
				"Maní" => '85139',
				"Monterrey" => '85162',
				"Nunchía" => '85225',
				"Orocué" => '85230',
				"Paz De Ariporo" => '85250',
				"Pore" => '85263',
				"Recetor" => '85279',
				"Sabanalarga" => '85300',
				"Sácama" => '85315',
				"San Luis De Palenque" => '85325',
				"Támara" => '85400',
				"Tauramena" => '85410',
				"Trinidad" => '85430',
				"Villanueva" => '85440',
				"Yopal" => '85001',        
			),
			'CAU' => array(
				"Almaguer" => '19022',
				"Argelia" => '19050',
				"Balboa" => '19075',
				"Bolívar" => '19100',
				"Buenos Aires" => '19110',
				"Cajibío" => '19130',
				"Caldono" => '19137',
				"Caloto" => '19142',
				"Corinto" => '19212',
				"El Tambo" => '19256',
				"Florencia" => '19290',
				"Guapi" => '19318',
				"Inzá" => '19355',
				"Jambaló" => '19364',
				"La Sierra" => '19392',
				"La Vega" => '19397',
				"López" => '19418',
				"Mercaderes" => '19450',
				"Miranda" => '19455',
				"Morales" => '19473',
				"Padilla" => '19513',
				"Paez" => '19517',
				"Patía" => '19532',
				"Piamonte" => '19533',
				"Piendamó" => '19548',
				"Popayán" => '19001',
				"Puerto Tejada" => '19573',
				"Puracé" => '19585',
				"Rosas" => '19622',
				"San Sebastián" => '19693',
				"Santa Rosa" => '19701',
				"Santander De Quilichao" => '19698',
				"Silvia" => '19743',
				"Sotara" => '19760',
				"Suárez" => '19780',
				"Sucre" => '19785',
				"Timbío" => '19807',
				"Timbiquí" => '19809',
				"Toribio" => '19821',
				"Totoró" => '19824',
				"Villa Rica" => '19845',
				"Guachené" => '19300',
			),
			'CES' => array(
				"Aguachica" => '20011',
				"Agustín Codazzi" => '20013',
				"Astrea" => '20032',
				"Becerril" => '20045',
				"Bosconia" => '20060',
				"Chimichagua" => '20175',
				"Chiriguaná" => '20178',
				"Curumaní" => '20228',
				"El Copey" => '20238',
				"El Paso" => '20250',
				"Gamarra" => '20295',
				"González" => '20310',
				"La Gloria" => '20383',
				"La Jagua De Ibirico" => '20400',
				"La Paz" => '20621',
				"Manaure" => '20443',
				"Pailitas" => '20517',
				"Pelaya" => '20550',
				"Pueblo Bello" => '20570',
				"Río De Oro" => '20614',
				"San Alberto" => '20710',
				"San Diego" => '20750',
				"San Martín" => '20770',
				"Tamalameque" => '20787',
				"Valledupar" => '20001',
			),
			'CHOC'  => array(
				"Acandí" => '27006',
				"Alto Baudo" => '27025',
				"Atrato" => '27050',
				"Bagadó" => '27073',
				"Bahía Solano" => '27075',
				"Bajo Baudó" => '27077',
				"Belén De Bajirá" => '27086',
				"Bojaya" => '27099',
				"Carmen Del Darien" => '27150',
				"Cértegui" => '27160',
				"Condoto" => '27205',
				"El Cantón Del San Pablo" => '27135',
				"El Carmen De Atrato" => '27245',
				"El Litoral Del San Juan" => '27250',
				"Istmina" => '27361',
				"Juradó" => '27372',
				"Lloró" => '27413',
				"Medio Atrato" => '27425',
				"Medio Baudó" => '27430',
				"Medio San Juan" => '27450',
				"Nóvita" => '27491',
				"Nuquí" => '27495',
				"Quibdó" => '27001',
				"Río Iro" => '27580',
				"Río Quito" => '27600',
				"Riosucio" => '27615',
				"San José Del Palmar" => '27660',
				"Sipí" => '27745',
				"Tadó" => '27787',
				"Unguía" => '27800',
				"Unión Panamericana" => '27810',
			),
			'COR' => array(
				"Ayapel" => '23068',
				"Buenavista" => '23079',
				"Canalete" => '23090',
				"Cereté" => '23162',
				"Chimá" => '23168',
				"Chinú" => '23182',
				"Ciénaga De Oro" => '23189',
				"Cotorra" => '23300',
				"La Apartada" => '23350',
				"Lorica" => '23417',
				"Los Córdobas" => '23419',
				"Momil" => '23464',
				"Montelíbano" => '23466',
				"Montería" => '23001',
				"Moñitos" => '23500',
				"Planeta Rica" => '23555',
				"Pueblo Nuevo" => '23570',
				"Puerto Escondido" => '23574',
				"Puerto Libertador" => '23580',
				"Purísima" => '23586',
				"Sahagún" => '23660',
				"San Andrés Sotavento" => '23670',
				"San Antero" => '23672',
				"San Bernardo Del Viento" => '23675',
				"San Carlos" => '23678',
				"San Pelayo" => '23686',
				"Tierralta" => '23807',
				"Valencia" => '23855',
				"San José de Uré" => '23682',
			),
			'CUN' => array(
				"Agua De Dios" => '25001',
				"Albán" => '25019',
				"Anapoima" => '25035',
				"Anolaima" => '25040',
				"Apulo" => '25599',
				"Arbeláez" => '25053',
				"Beltrán" => '25086',
				"Bituima" => '25095',
				"Bojacá" => '25099',
				"Cabrera" => '25120',
				"Cachipay" => '25123',
				"Cajicá" => '25126',
				"Caparrapí" => '25148',
				"Caqueza" => '25151',
				"Carmen De Carupa" => '25154',
				"Chaguaní" => '25168',
				"Chía" => '25175',
				"Chipaque" => '25178',
				"Choachí" => '25181',
				"Chocontá" => '25183',
				"Cogua" => '25200',
				"Cota" => '25214',
				"Cucunubá" => '25224',
				"El Colegio" => '25245',
				"El Peñón" => '25258',
				"El Rosal" => '25260',
				"Facatativá" => '25269',
				"Fomeque" => '25279',
				"Fosca" => '25281',
				"Funza" => '25286',
				"Fúquene" => '25288',
				"Fusagasugá" => '25290',
				"Gachala" => '25293',
				"Gachancipá" => '25295',
				"Gachetá" => '25297',
				"Gama" => '25299',
				"Girardot" => '25307',
				"Granada" => '25312',
				"Guachetá" => '25317',
				"Guaduas" => '25320',
				"Guasca" => '25322',
				"Guataquí" => '25324',
				"Guatavita" => '25326',
				"Guayabal De Siquima" => '25328',
				"Guayabetal" => '25335',
				"Gutiérrez" => '25339',
				"Jerusalén" => '25368',
				"Junín" => '25372',
				"La Calera" => '25377',
				"La Mesa" => '25386',
				"La Palma" => '25394',
				"La Peña" => '25398',
				"La Vega" => '25402',
				"Lenguazaque" => '25407',
				"Macheta" => '25426',
				"Madrid" => '25430',
				"Manta" => '25436',
				"Medina" => '25438',
				"Mosquera" => '25473',
				"Nariño" => '25483',
				"Nemocón" => '25486',
				"Nilo" => '25488',
				"Nimaima" => '25489',
				"Nocaima" => '25491',
				"Pacho" => '25513',
				"Paime" => '25518',
				"Pandi" => '25524',
				"Paratebueno" => '25530',
				"Pasca" => '25535',
				"Puerto Salgar" => '25572',
				"Pulí" => '25580',
				"Quebradanegra" => '25592',
				"Quetame" => '25594',
				"Quipile" => '25596',
				"Ricaurte" => '25612',
				"San Antonio Del Tequendama" => '25645',
				"San Bernardo" => '25649',
				"San Cayetano" => '25653',
				"San Francisco" => '25658',
				"San Juan De Río Seco" => '25662',
				"Sasaima" => '25718',
				"Sesquilé" => '25736',
				"Sibaté" => '25740',
				"Silvania" => '25743',
				"Simijaca" => '25745',
				"Soacha" => '25754',
				"Sopó" => '25758',
				"Subachoque" => '25769',
				"Suesca" => '25772',
				"Supatá" => '25777',
				"Susa" => '25779',
				"Sutatausa" => '25781',
				"Tabio" => '25785',
				"Tausa" => '25793',
				"Tena" => '25797',
				"Tenjo" => '25799',
				"Tibacuy" => '25805',
				"Tibirita" => '25807',
				"Tocaima" => '25815',
				"Tocancipá" => '25817',
				"Topaipí" => '25823',
				"Ubalá" => '25839',
				"Ubaque" => '25841',
				"Une" => '25845',
				"Útica" => '25851',
				"Venecia" => '25506',
				"Vergara" => '25862',
				"Vianí" => '25867',
				"Villa De San Diego De Ubate" => '25843',
				"Villagómez" => '25871',
				"Villapinzón" => '25873',
				"Villeta" => '25875',
				"Viotá" => '25878',
				"Yacopí" => '25885',
				"Zipacón" => '25898',
				"Zipaquirá" => '25899',
			),
			'GUA' => array(
				"Barranco Minas" => '94343',
				"Cacahual" => '94886',
				"Inírida" => '94001',
				"La Guadalupe" => '94885',
				"Mapiripana" => '94663',
				"Morichal" => '94888',
				"Pana Pana" => '94887',
				"Puerto Colombia" => '94884',
				"San Felipe" => '94883',
			),
			'GUV' => array(
				"Calamar" => '95015',
				"El Retorno" => '95025',
				"Miraflores" => '95200',
				"San José del Guaviare" => '95001',
			),
			'HUI' => array(
				"Acevedo" => '41006',
				"Agrado" => '41013',
				"Aipe" => '41016',
				"Algeciras" => '41020',
				"Altamira" => '41026',
				"Baraya" => '41078',
				"Campoalegre" => '41132',
				"Colombia" => '41206',
				"Elías" => '41244',
				"Garzon" => '41298',
				"Gigante" => '41306',
				"Guadalupe" => '41319',
				"Hobo" => '41349',
				"Iquira" => '41357',
				"Isnos" => '41359',
				"La Argentina" => '41378',
				"La Plata" => '41396',
				"Nataga" => '41483',
				"Neiva" => '41001',
				"Oporapa" => '41503',
				"Paicol" => '41518',
				"Palermo" => '41524',
				"Palestina" => '41530',
				"Pital" => '41548',
				"Pitalito" => '41551',
				"Rivera" => '41615',
				"Saladoblanco" => '41660',
				"San Agustín" => '41668',
				"Santa María" => '41676',
				"Suaza" => '41770',
				"Tarqui" => '41791',
				"Tello" => '41799',
				"Teruel" => '41801',
				"Tesalia" => '41797',
				"Timaná" => '41807',
				"Villavieja" => '41872',
				"Yaguará" => '41885',
			),
			'GUJ' => array(
				"Albania" => '44035',
				"Barrancas" => '44078',
				"Dibulla" => '44090',
				"Distraccion" => '44098',
				"El Molino" => '44110',
				"Fonseca" => '44279',
				"Hatonuevo" => '44378',
				"La Jagua del Pilar" => '44420',
				"Maicao" => '44430',
				"Manaure" => '44560',
				"Riohacha" => '44001',
				"San Juan del Cesar" => '44650',
				"Uribia" => '44847',
				"Urumita" => '44855',
				"Villanueva" => '44874',
			),
			'MAG' => array(
				"Algarrobo" => '47030',
				"Aracataca" => '47053',
				"Ariguaní" => '47058',
				"Cerro San Antonio" => '47161',
				"Chibolo" => '47170',
				"Cienaga" => '47189',
				"Concordia" => '47205',
				"El Banco" => '47245',
				"El Piñon" => '47258',
				"El Retén" => '47268',
				"Fundación" => '47288',
				"Guamal" => '47318',
				"Nueva Granada" => '47460',
				"Pedraza" => '47541',
				"Pijiño del Carmen" => '47545',
				"Pivijay" => '47551',
				"Plato" => '47555',
				"Puebloviejo" => '47570',
				"Remolino" => '47605',
				"Sabanas de San Angel" => '47660',
				"Salamina" => '47675',
				"San Sebastián de Buenavista" => '47692',
				"San Zenón" => '47703',
				"Santa Ana" => '47707',
				"Santa Bárbara De Pinto" => '47720',
				"Sitionuevo" => '47745',
				"Tenerife" => '47798',
				"Zapayán" => '47960',
				"Zona Bananera" => '47980',
			),
			'MET' => array(
				"Acacias" => '50006',
				"Barranca De Upía" => '50110',
				"Cabuyaro" => '50124',
				"Castilla La Nueva" => '50150',
				"Cubarral" => '50223',
				"Cumaral" => '50226',
				"El Calvario" => '50245',
				"El Castillo" => '50251',
				"El Dorado" => '50270',
				"Fuente De Oro" => '50287',
				"Granada" => '50313',
				"Guamal" => '50318',
				"La Macarena" => '50350',
				"Lejanías" => '50400',
				"Mapiripán" => '50325',
				"Mesetas" => '50330',
				"Puerto Concordia" => '50450',
				"Puerto Gaitán" => '50568',
				"Puerto Lleras" => '50577',
				"Puerto López" => '50573',
				"Puerto Rico" => '50590',
				"Restrepo" => '50606',
				"San Carlos De Guaroa" => '50680',
				"San Juan De Arama" => '50683',
				"San Juanito" => '50686',
				"San Martín" => '50689',
				"Uribe" => '50370',
				"Villavicencio" => '50001',
				"Vistahermosa" => '50711',
			),
			'NAR' => array(
				"Albán" => '52019',
				"Aldana" => '52022',
				"Ancuyá" => '52036',
				"Arboleda" => '52051',
				"Barbacoas" => '52079',
				"Belén" => '52083',
				"Buesaco" => '52110',
				"Chachagüí" => '52240',
				"Colón" => '52203',
				"Consaca" => '52207',
				"Contadero" => '52210',
				"Córdoba" => '52215',
				"Cuaspud" => '52224',
				"Cumbal" => '52227',
				"Cumbitara" => '52233',
				"El Charco" => '52250',
				"El Peñol" => '52254',
				"El Rosario" => '52256',
				"El Tablón De Gómez" => '52258',
				"El Tambo" => '52260',
				"Francisco Pizarro" => '52520',
				"Funes" => '52287',
				"Guachucal" => '52317',
				"Guaitarilla" => '52320',
				"Gualmatan" => '52323',
				"Iles" => '52352',
				"Imues" => '52354',
				"Ipiales" => '52356',
				"La Cruz" => '52378',
				"La Florida" => '52381',
				"La Llanada" => '52385',
				"La Tola" => '52390',
				"La Unión" => '52399',
				"Leiva" => '52405',
				"Linares" => '52411',
				"Los Andes" => '52418',
				"Magüi" => '52427',
				"Mallama" => '52435',
				"Mosquera" => '52473',
				"Nariño" => '52480',
				"Olaya Herrera" => '52490',
				"Ospina" => '52506',
				"Pasto" => '52001',
				"Policarpa" => '52540',
				"Potosi" => '52560',
				"Providencia" => '52565',
				"Puerres" => '52573',
				"Pupiales" => '52585',
				"Ricaurte" => '52612',
				"Roberto Payán" => '52621',
				"Samaniego" => '52678',
				"San Bernardo" => '52685',
				"San Lorenzo" => '52687',
				"San Pablo" => '52693',
				"San Pedro de Cartago" => '52694',
				"Sandoná" => '52683',
				"Santa Bárbara" => '52696',
				"Santacruz" => '52699',
				"Sapuyes" => '52720',
				"Taminango" => '52786',
				"Tangua" => '52788',
				"Tumaco" => '52835',
				"Tuquerres" => '52838',
				"Yacuanquer" => '52885',
			),
			'NOR' => array(
				"Abrego" => '54003',
				"Arboledas" => '54051',
				"Bochalema" => '54099',
				"Bucarasica" => '54109',
				"Cachirá" => '54128',
				"Cácota" => '54125',
				"Chinácota" => '54172',
				"Chitaga" => '54174',
				"Convención" => '54206',
				"Cúcuta" => '54001',
				"Cucutilla" => '54223',
				"Durania" => '54239',
				"El Carmen" => '54245',
				"El Zulia" => '54261',
				"Gramalote" => '54313',
				"Hacarí" => '54344',
				"Herran" => '54347',
				"La Esperanza" => '54385',
				"Los Playa" => '54398',
				"Labateca" => '54377',
				"Los Patios" => '54405',
				"Lourdes" => '54418',
				"Mutiscua" => '54480',
				"Ocaña" => '54498',
				"Pamplona" => '54518',
				"Pamplonita" => '54520',
				"Puerto santander" => '54553',
				"Ragonvalia" => '54599',
				"Salazar" => '54660',
				"San Calixto" => '54670',
				"San Cayetano" => '54673',
				"Santiago" => '54680',
				"Sardinata" => '54720',
				"Silos" => '54743',
				"Teorama" => '54800',
				"Tibú" => '54810',
				"Toledo" => '54820',
				"Villa Caro" => '54871',
				"Villa del Rosario" => '54874',
			),
			'PUT' => array(
				"Colón" => '86219',
				"Leguízamo" => '86573',
				"Mocoa" => '86001',
				"Orito" => '86320',
				"Puerto Asis" => '86568',
				"Puerto Caicedo" => '86569',
				"Puerto Guzmán" => '86571',
				"San Francisco" => '86755',
				"San Miguel" => '86757',
				"Santiago" => '86760',
				"Sibundoy" => '86749',
				"Valle Del Guamuez" => '86865',
				"Villagarzón" => '86885',
			),
			'QUI' => array(
				"Armenia" => '63001',
				"Buenavista" => '63111',
				"Calarca" => '63130',
				"Circasia" => '63190',
				"Córdoba" => '63212',
				"Filandia" => '63272',
				"Genova" => '63302',
				"La Tebaida" => '63401',
				"Montenegro" => '63470',
				"Pijao" => '63548',
				"Quimbaya" => '63594',
				"Salento" => '63690',
			),
			'RIS' => array(
				"Apía" => '66045',
				"Balboa" => '66075',
				"Belén de Umbría" => '66088',
				"Dosquebradas" => '66170',
				"Guática" => '66318',
				"La Celia" => '66383',
				"La Virginia" => '66400',
				"Marsella" => '66440',
				"Mistrató" => '66456',
				"Pereira" => '66001',
				"Pueblo Rico" => '66572',
				"Quinchía" => '66594',
				"Santa Rosa de Cabal" => '66682',
				"Santuario" => '66687',
			),
			'SAP' => array(
				"Providencia" => '88564',
				"San Andrés" => '88001',
			),
			'SAN'  => array(   
				"Aguada" => '68013',
				"Albania" => '68020',
				"Aratoca" => '68051',
				"Barbosa" => '68077',
				"Barichara" => '68079',
				"Barrancabermeja" => '68081',
				"Betulia" => '68092',
				"Bolívar" => '68101',
				"Bucaramanga" => '68001',
				"Cabrera" => '68121',
				"California" => '68132',
				"Capitanejo" => '68147',
				"Carcasí" => '68152',
				"Cepitá" => '68160',
				"Cerrito" => '68162',
				"Charalá" => '68167',
				"Charta" => '68169',
				"Chima" => '68176',
				"Chipatá" => '68179',
				"Cimitarra" => '68190',
				"Concepción" => '68207',
				"Confines" => '68209',
				"Contratacion" => '68211',
				"Coromoro" => '68217',
				"Curití" => '68229',
				"El Carmen de Chucurí" => '68235',
				"El Guacamayo" => '68245',
				"El Peñón" => '68250',
				"El Playón" => '68255',
				"Encino" => '68264',
				"Enciso" => '68266',
				"Florián" => '68271',
				"Floridablanca" => '68276',
				"Galán" => '68296',
				"Gambita" => '68298',
				"Girón" => '68307',
				"Guaca" => '68318',
				"Guadalupe" => '68320',
				"Guapotá" => '68322',
				"Guavatá" => '68324',
				"Güepsa" => '68327',
				"Hato" => '68344',
				"Jesus María" => '68368',
				"Jordán" => '68370',
				"La Belleza" => '68377',
				"La Paz" => '68397',
				"Landázuri" => '68385',
				"Lebrija" => '68406',
				"Los Santos" => '68418',
				"Macaravita" => '68425',
				"Málaga" => '68432',
				"Matanza" => '68444',
				"Mogotes" => '68464',
				"Molagavita" => '68468',
				"Ocamonte" => '68498',
				"Oiba" => '68500',
				"Onzaga" => '68502',
				"Palmar" => '68522',
				"Palmas Del Socorro" => '68524',
				"Páramo" => '68533',
				"Piedecuesta" => '68547',
				"Pinchote" => '68549',
				"Puente Nacional" => '68572',
				"Puerto Parra" => '68573',
				"Puerto Wilches" => '68575',
				"Rionegro" => '68615',
				"Sabana de Torres" => '68655',
				"San Andrés" => '68669',
				"San Benito" => '68673',
				"San Gil" => '68679',
				"San Joaquín" => '68682',
				"San José de Miranda" => '68684',
				"San Miguel" => '68686',
				"San Vicente De Chucurí" => '68689',
				"Santa Bárbara" => '68705',
				"Santa Helena del Opón" => '68720',
				"Simacota" => '68745',
				"Socorro" => '68755',
				"Suaita" => '68770',
				"Sucre" => '68773',
				"Suratá" => '68780',
				"Tona" => '68820',
				"Valle de San José" => '68855',
				"Vélez" => '68861',
				"Vetas" => '68867',
				"Villanueva" => '68872',
				"Zapatoca" => '68895',
			),
			'SUC' => array(
				"Buenavista" => '70110',
				"Caimito" => '70124',
				"Chalán" => '70230',
				"Coloso" => '70204',
				"Corozal" => '70215',
				"Coveñas" => '70221',
				"El Roble" => '70233',
				"Galeras" => '70235',
				"Guaranda" => '70265',
				"La Unión" => '70400',
				"Los Palmitos" => '70418',
				"Majagual" => '70429',
				"Morroa" => '70473',
				"Ovejas" => '70508',
				"Palmito" => '70523',
				"Sampués" => '70670',
				"San Benito Abad" => '70678',
				"San Juan De Betulia" => '70702',
				"San Marcos" => '70708',
				"San Onofre" => '70713',
				"San Pedro" => '70717',
				"Santiago De Tolú" => '70820',
				"Sincé" => '70742',
				"Sincelejo" => '70001',
				"Sucre" => '70771',
				"Tolú viejo" => '70823',
			),
			'TOL' => array(
				"Alpujarra" => '73024',
				"Alvarado" => '73026',
				"Ambalema" => '73030',
				"Anzoátegui" => '73043',
				"Armero" => '73055',
				"Ataco" => '73067',
				"Cajamarca" => '73124',
				"Carmen de Apicalá" => '73148',
				"Casabianca" => '73152',
				"Chaparral" => '73168',
				"Coello" => '73200',
				"Coyaima" => '73217',
				"Cunday" => '73226',
				"Dolores" => '73236',
				"Espinal" => '73268',
				"Falan" => '73270',
				"Flandes" => '73275',
				"Fresno" => '73283',
				"Guamo" => '73319',
				"Herveo" => '73347',
				"Honda" => '73349',
				"Ibagué" => '73001',
				"Icononzo" => '73352',
				"Lérida" => '73408',
				"Líbano" => '73411',
				"Mariquita" => '73443',
				"Melgar" => '73449',
				"Murillo" => '73461',
				"Natagaima" => '73483',
				"Ortega" => '73504',
				"Palocabildo" => '73520',
				"Piedras" => '73547',
				"Planadas" => '73555',
				"Prado" => '73563',
				"Purificación" => '73585',
				"Rioblanco" => '73616',
				"Roncesvalles" => '73622',
				"Rovira" => '73624',
				"Saldaña" => '73671',
				"San Antonio" => '73675',
				"San Luis" => '73678',
				"Santa Isabel" => '73686',
				"Suárez" => '73770',
				"Valle de San Juan" => '73854',
				"Venadillo" => '73861',
				"Villahermosa" => '73870',
				"Villarica" => '73873',
			),
			'VAC' => array(
				"Alcalá" => '76020',
				"Andalucía" => '76036',
				"Ansermanuevo" => '76041',
				"Argelia" => '76054',
				"Bolívar" => '76100',
				"Buenaventura" => '76109',
				"Bugalagrande" => '76113',
				"Caicedonia" => '76122',
				"Cali" => '76001',
				"Calima" => '76126',
				"Candelaria" => '76130',
				"Cartago" => '76147',
				"Dagua" => '76233',
				"El Águila" => '76243',
				"El Cairo" => '76246',
				"El Cerrito" => '76248',
				"El Dovio" => '76250',
				"Florida" => '76275',
				"Ginebra" => '76306',
				"Guacarí" => '76318',
				"Guadalajara De Buga" => '76111',
				"Jamundí" => '76364',
				"La Cumbre" => '76377',
				"La Union" => '76400',
				"La Victoria" => '76403',
				"Obando" => '76497',
				"Palmira" => '76520',
				"Pradera" => '76563',
				"Restrepo" => '76606',
				"Riofrío" => '76616',
				"Roldanillo" => '76622',
				"San Pedro" => '76670',
				"Sevilla" => '76736',
				"Toro" => '76823',
				"Trujillo" => '76828',
				"Tuluá" => '76834',
				"Ulloa" => '76845',
				"Versalles" => '76863',
				"Vijes" => '76869',
				"Yotoco" => '76890',
				"Yumbo" => '76892',
				"Zarzal" => '76895',
			),
			'VAU' => array(
				"Carurú" => '97161',
				"Mitú" => '97001',
				"Pacoa" => '97511',
				"Papanaua" => '97777',
				"Taraira" => '97666',
				"Yavaraté" => '97889',
				),
			'VIC' => array(
				"Cumaribo" => '99773',
				"La Primavera" => '99524',
				"Puerto Carreño" => '99001',
				"Santa Rosalia" => '99624',
			)
		);
		if(empty($places[$state])) {
			return null;
		}
		if(empty($places[$state][$index])){
			return null;
		}
		return $places[$state][$index];
	}
}

