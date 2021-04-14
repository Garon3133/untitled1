<?php
class ControllerPrilGetInvOnt extends Controller {
	public function index() {
		require_once 'function.php';
		
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
		
								$ont_data = array(
									'key' 			=> APIKEY_USERSIDE,
									'cat' 			=> 'device',
									'action' 		=> 'get_ont_data',
									'id' 			=> $inv_am['data']['serial_number'],
								);
								//print_r($ont_data);
								$ont_arr = object_to_array(json_decode(create_ticket($ont_data, USERSIDE)));
								//print_r($ont_arr);
								$result = "";
								//print_r("Date add - ");
								$result .= $ont_arr['data']['date_add']."L=P=L".$ont_arr['data']['sn']."L=P=L".$ont_arr['data']['iface_olt_number']."L=P=L".$ont_arr['data']['iface_number']."L=P=L".$ont_arr['data']['iface_name']."L=P=L".$ont_arr['data']['description']."L=P=L";
								if($ont_arr['data']['reason_offline'] == ""){
									$result .= "Active";
								} else {
									$result .= $ont_arr['data']['reason_offline'];
								}								
								print_r($result);
							}
						}
					}
				}
			}
		}
	}
}

