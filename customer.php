<?php
class ControllerPrilCustomer extends Controller {
	public function index() {
		
		require_once 'function.php';
		
		$customer_id = '11025';
		$key_pril = '100-200';
		$checked = true;
		foreach (object_to_array(retrieveJsonPostData()) as $pril_key => $pril_value) {
			switch ($pril_key) {
				case 'customer_id':
					$customer_id = $pril_value;
					break;
				case 'key_pril':
					$key_pril = $pril_value;
					break;
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

		$customer_task_list = array(
			'key' => APIKEY_USERSIDE,
			'cat' => 'customer',
			'subcat' => 'get_data',
			'customer_id' => $customer_id,
		);
	
		$customer = object_to_array(json_decode(create_ticket($customer_task_list, USERSIDE)));
		
		if ($customer['Result'] == 'OK') {
			foreach ($customer['Data'] as $list_cust => $cust) {
				if($list_cust == "group"){
					
					foreach ($cust as $list_cust_id => $cust_id) {
						
						//https://user.intronex.ru/api.php?key=1597535&cat=module&request=get_user_group_list
						$cmodule_request = array(
							'key' => APIKEY_USERSIDE,
							'cat' => 'module',
							'request' => 'get_user_group_list',
						);
						
						$module = object_to_array(json_decode(create_ticket($cmodule_request, USERSIDE)));
											
						$customer['Data'] += ['group_customer' => $module[$cust_id['id']]];
						$customer['data'] += ['group_customer' => $module[$cust_id['id']]];
						
						$checked = false;
						$json = json_encode($customer, JSON_UNESCAPED_UNICODE);
						print_r($json);
						
					}
				}
			}
			if($checked){
				$json = json_encode($customer, JSON_UNESCAPED_UNICODE);
				print_r($json);
			}
		} else {
			$json = json_encode($customer, JSON_UNESCAPED_UNICODE);
			print_r($json);
		}
		
	}
}
