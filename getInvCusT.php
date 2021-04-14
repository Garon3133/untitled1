<?php
class ControllerPrilGetInvCusT extends Controller {
	public function index() {
		require_once 'function.php';
		
		//$customer_id = '81363';
		$customer_id = '3158';
		$key_pril = '100-200';
		
		$Arrr = object_to_array(retrieveJsonPostData());
		if (is_array(@$Arrr) || is_object(@$Arrr)){
			foreach ($Arrr as $pril_key => $pril_value) {
				switch ($pril_key) {
					case 'customer_id':
						$customer_id = $pril_value;
						break;
					case 'key_pril':
						$key_pril = $pril_value;
						break;
				}
			}
			
		}
		if ($customer_id == '' || $key_pril == '') {
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

		define('APIKEY_USERSIDE', $arr_customer[1]);
		define('USERSIDE', $arr_customer[2]);
		define('INVENTORY', serialize (array("Mac", "Huawei HG8245H", "Irix", "Linux")));
		
		$get_inventory_amount_data = array(
			'key' 		=> APIKEY_USERSIDE,
			'cat' 		=> 'inventory',
			'action' 	=> 'get_inventory_amount',
			'location'	=> 'customer',
			'object_id' => $customer_id,
		);
		
		$get_inventory_amount = object_to_array(json_decode(create_ticket($get_inventory_amount_data, USERSIDE)));
		
		//print_r($get_inventory_amount);
		
		if ($get_inventory_amount['Result'] == 'OK') {
			if(!empty($get_inventory_amount['data'])){
				foreach ($get_inventory_amount['data'] as $inv_amount) {
					//print_r($inv_amount['id']." Da ");
						
					$inv_amount_data = array(
						'key' 		=> APIKEY_USERSIDE,
						'cat' 		=> 'inventory',
						'action' 	=> 'get_inventory',
						'id' => $inv_amount['id'],
					);
					$inv_am = object_to_array(json_decode(create_ticket($inv_amount_data, USERSIDE)));
					//print_r($inv_am);
					
					if ($inv_am['Result'] == 'OK') {
						//print_r(unserialize(INVENTORY));
						foreach (unserialize(INVENTORY) as $inventory) { 
							if (in_array($inventory, $inv_am['data'])) {
								//echo $inv_am['data']['device_id'];
								//print_r($get_inventory_amount);
								//print_r($inv_am);
								$onu_data = array(
									'key' 			=> APIKEY_USERSIDE,
									'cat' 			=> 'device',
									'action' 		=> 'get_pon_level_history',
									'onu_name'		=> $inv_am['data']['serial_number'],
								   // 'limit'			=> 2,
									'is_desc'		=> 1,
									//'object_id' 	=> $inv_am['data']['device_id'],
								);
								//print_r($onu_data);
								$onu_arr = object_to_array(json_decode(create_ticket($onu_data, USERSIDE)));
								//print_r($onu_arr);
								$first = array_keys($onu_arr)[0];
								$second = array_keys($onu_arr[$first]);
								$result = null;
								//print_r($second);
								if(is_null(@$second[0])){
									print_r("Нет сохранённого уровня сигнала");
								} else {
									$third = array_keys($onu_arr[$first][$second[0]])[0];
									$four = array_keys($onu_arr[$first][$second[0]][$third])[0];
									$Arr = $onu_arr[$first][$second[0]][$third];
									$result = "";
									foreach($Arr as $Das){
										$result .= "===".$Das['date_from']."LPL".$Das['date_to']."LPL".$Das['level'];
									}
									//print_r($result);
								}
							}
						}
					}
				}
			} else {
				print_r("Нет оборудования в ТМЦ");
			}
		}
	}
}
?>
