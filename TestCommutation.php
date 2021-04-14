<?php
class ControllerPrilTestCommutation extends Controller {
	public function index() {
		
		require_once 'function.php';
		
		$commutation_id = '9429';
		$key_pril = '100-200';
		
		/*foreach (object_to_array(retrieveJsonPostData()) as $pril_key => $pril_value) {
			switch ($pril_key) {
				case 'commutation_id':
					$commutation_id = $pril_value;
					break;
				case 'key_pril':
					$key_pril = $pril_value;
					break;
			}
		}*/
			
		if ($commutation_id == '' || $key_pril == '') {
			echo "Данные переданы не верно!";
			exit;
		}
		
		$this->load->model('pril/android');
		
		$customer = $this->model_pril_android->getCustomer($key_pril);
		
		if (!$customer) {
			print_r("Ошибка!");
			exit;
		}

		$arr_customer = object_to_array(json_decode($customer[0]['custom_field']));
		
		/*define('APIKEY_USERSIDE', 'pOSPuyHxhigUePpb');
		define('USERSIDE', 'http://userside.elite-line.net');*/
		define('APIKEY_USERSIDE', $arr_customer[1]);
		define('USERSIDE', $arr_customer[2]);

		$userComm = array(
			'key'            => APIKEY_USERSIDE,
			'cat'            => 'commutation',
			'action'         => 'get_data',
			'object_type'    => 'customer',
			'object_id'    => $commutation_id,
			'is_finish_data' => 1
		);
		$usersideComm = object_to_array(json_decode(create_ticket($userComm, USERSIDE)));

		if ($usersideComm['Result'] == 'OK') {
			// [customer|switch|cross|fiber]
			
			$result_commutation_fiber = '';
			$result_commutation_switch = '';
			$result_commutation_end = '';
			$opticalen2 = '';
			$param2 = "Конечная точка отсутствует";
			$color = '';
			//print_r($usersideComm);
			$port = $usersideComm['data'][0]['interface'];

			switch ($usersideComm['data'][0]['object_type']) {
				case "fiber": // Ждать пока починится массив с юзера(юзер сломан), в массиве должны быть opticalen и opticalen2 и должны быть они не == 0
					
					$fiberData = array(
						'key'       => APIKEY_USERSIDE,
						'cat'       => 'fiber',
						'action'    => 'get_list',
						'object_id' => $usersideComm['data'][0]['object_id'],
						//'object_id' => 5383,
					);
					$usersidefiber = object_to_array(json_decode(create_ticket($fiberData, USERSIDE)));
					print_r($usersidefiber);
					if ($usersidefiber['Result'] == 'OK') {
						foreach ($usersidefiber['Data'] as $keyfiber => $valuefiber) {
							//print_r ("ВОЛС (".$valuefiber['fibers_count']."), ".$valuefiber['opticalen']."м, - до ".$valuefiber['object_a_name']);
							$fibers_count = $valuefiber['port'];
							$opticalen = $valuefiber['opticalen'];
							$opticalen2 = $valuefiber['opticalen2'];
							$object_a_name = $valuefiber['start_point_name'];
							foreach ($valuefiber['fibers'] as $keycolor => $valuecolor) {
								if ($valuecolor['color']['name'] != '') {
									$colorName = $valuecolor['color']['name'];
									($valuecolor['color']['name'] == "белый") ? $colorHtml = "#777777" : $colorHtml = $valuecolor['color']['htmlCode'];
									$color = '<font color="'.$colorHtml.'">'.$colorName.'</font>';
								}
								if ($valuecolor['moduleColor']['name'] != '') {
									$modulecolorName = $valuecolor['moduleColor']['name'];
									($valuecolor['moduleColor']['name'] == "белый") ? $modulecolorHtml = "#777777" : $modulecolorHtml = $valuecolor['moduleColor']['htmlCode'];
									$color = $color.' / <font color="'.$modulecolorHtml.'">'.$modulecolorName.'</font>';
								}
								$color = $color." <strong>(".$opticalen2."м)</strong>";
							}
						}
						
						$result_commutation_fiber = "ВОЛС (".$fibers_count."), ".$opticalen."м, - до ".$object_a_name." - ".$color ;
					} else {
						$result_commutation_fiber = "Что-то пошло не так. Fiber";
					}
					
					break;
					
				case "switch": // тут
					
					$switchData = array(
						'key'         => APIKEY_USERSIDE,
						'cat'         => 'device',
						'action'      => 'get_data',
						'object_type' => 'switch',
						'object_id'   => $usersideComm['data'][0]['object_id'], //на данный момент на данном обжект_айди отсутствует инфа по свичу
					);
					$usersideswitch = object_to_array(json_decode(create_ticket($switchData, USERSIDE)));
					//print_r ($usersideswitch);
					if ($usersideswitch['Result'] == 'OK') {
						foreach ($usersideswitch['data'] as $keyswitch => $valueswitch) {
							
							/*if ($keyswitch == "ip") {
								//преобразуем в правильный формат ip
								$long = long2ip($valueswitch['ip']);
							} else {
								$long = "0.0.0.0";
							}*/
							$long = $valueswitch['host'].":".$port;
							
							$result_commutation_switch = $valueswitch['name']." - IP: ".$long." - ".$valueswitch['location'];
						}
					} else {
						$result_commutation_switch = "Что-то пошло не так. Switch";
					}
					
					break;
					
				case 2:
					echo "i равно 2";
					break;
			}
			
			// если есть конечная точка
			if (array_key_exists("finish", $usersideComm['data'])) {
				switch ($usersideComm['data']['finish']['object_type']) {
					case "switch": // тут
						$switchData = array(
							'key'         => APIKEY_USERSIDE,
							'cat'         => 'device',
							'action'      => 'get_data',
							'object_type' => 'switch',
							'object_id'   => $usersideComm['data']['finish']['object_id'], //на данный момент на данном обжект_айди отсутствует инфа по свичу
						);
						$usersideswitch = object_to_array(json_decode(create_ticket($switchData, USERSIDE)));
						
						if ($usersideswitch['Result'] == 'OK') {
							foreach ($usersideswitch['data'] as $keyswitch => $valueswitch) {
								//преобразуем в правильный формат ip
								$long = long2ip($valueswitch['ip']);
								$result_commutation_end = $valueswitch['name']." - IP: ".$long." - ".$valueswitch['location'];
							}
						} else {
							$result_commutation_end = "Что-то пошло не так. Switch (конечная точка).";
						}
						break;
				}
			}
			
			$param1 = '';

			if ($result_commutation_fiber != '') {
				$param1 = $result_commutation_fiber;
			} 
			
			if ($result_commutation_switch != '') {
				$param1 = $result_commutation_switch;
			}
			
			if ($result_commutation_end != '') {
				$param2 = $result_commutation_end;
			}
			
			print_r ($param1."::::".$param2);
			
		} else {
			print_r ($usersideComm['ErrorText']); //not_commutation
		}
		
	}
}
