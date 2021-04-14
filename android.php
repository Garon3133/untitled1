<?php
class ControllerPrilAndroid extends Controller {
	public function index() {

		require_once 'function.php';
		
		$user_name = 'admin_ru';
		$password = '1234';
		$key_pril = '100-100';
		
		foreach (object_to_array(retrieveJsonPostData()) as $pril_key => $pril_value) {
			switch ($pril_key) {
				case 'user_name':
					$user_name = $pril_value;
					break;
				case 'password':
					$password = $pril_value;
					break;
				case 'key_pril':
					$key_pril = $pril_value;
					break;
			}
		}
		
		if ($user_name == '' || $password == '' || $key_pril == '') {
			echo "Данные переданы не верно!";
			exit;
		}
		
		$this->load->model('pril/android');
		
		$customer = $this->model_pril_android->getCustomer($key_pril);
		
		if (!$customer) {
			print_r ("Авторизоваться не удалось!");
			exit;
		}

		$arr_customer = object_to_array(json_decode($customer[0]['custom_field']));
		//print_r($arr_customer);
		//$custom_field_id = implode(',', $arr_customer[16]);
		//$customerRole = $this->model_pril_android->getCustomField($custom_field_id);
		//print_r($customerRole);
		
		
		
		define('APIKEY_USERSIDE', $arr_customer[1]);
		define('USERSIDE', $arr_customer[2]);
	
		$data_check = array (
			'key'	=> APIKEY_USERSIDE,
			'cat'	=> 'operator',
			'action'=> 'check_pass',
			'login'	=> $user_name,
			'pass'	=> $password,
		);

		$userside_check = object_to_array(json_decode(create_ticket($data_check, USERSIDE)));
		//print_r($data_check);
		//print_r($userside_check);
		//https://inetmail.ru/api.php?key=keyus&cat=task&cat=operator&action=check_pass&login=test1506&pass=test1506
		//print(create_ticket($data_check, USERSIDE));
		if ($userside_check['Result'] == 'OK') {

			$data_id = array (
				'key' => APIKEY_USERSIDE,
				'cat' => 'operator',
				'action' => 'get_id_by_login',
				'login' => $user_name,
			);
			// http://89.22.52.19/api.php?key=1597535&cat=operator&action=get_id_by_login&login=andrey
			$userside_id = object_to_array(json_decode(create_ticket($data_id, USERSIDE)));
			
			if ($userside_id['Result'] == 'OK') {
				$data_oper = array (
					'key' => APIKEY_USERSIDE,
					'cat' => 'operator',
					'action' => 'get',
					'id' => $userside_id['id'],
				);
				// http://89.22.52.19/api.php?key=1597535&cat=operator&action=get&id=12
				$userside_oper = object_to_array(json_decode(create_ticket($data_oper, USERSIDE)));

				if ($userside_oper['Result'] == 'OK') {
					foreach ($userside_oper['data'] as $oper_key) {
						foreach ($oper_key as $op_key => $op_value) {
							if ($op_key == 'staff_id') {
								$data_staff = array (
									'key' => APIKEY_USERSIDE,
									'cat' => 'staff',
									'action' => 'get_data',
									'id' => $op_value,
								);
								$userside_staff = object_to_array(json_decode(create_ticket($data_staff, USERSIDE)));
								foreach ($userside_staff['data'] as $staff_key) {
									print_r($staff_key['name'].'::'.$op_value);
									/*if (!empty($staff_key['division'])) {
										print_r($staff_key['name'].'::'.$op_value);
									} else {
										print_r ($op_value."::Сотрудник не найден!");
									}*/
								}
							}
						}
					}
				} else {
					print_r ("::Оператор не найден!");
				}
				
			} else {
				print_r ("::Оператор не найден!");
			}

		} else {
			print_r ("Авторизоваться не удалось!");
		}
	
	}
}