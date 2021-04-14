<?php
class ControllerPrilOperator extends Controller {
	public function index() {
		
		require_once 'function.php';
		
		$operator = '0';
		$key_pril = '100-200';
		
		foreach (object_to_array(retrieveJsonPostData()) as $pril_key => $pril_value) {
			switch ($pril_key) {
				case 'operator':
					$operator = $pril_value;
					break;
				case 'key_pril':
					$key_pril = $pril_value;
					break;
			}
		}
			
		if ($operator == '' || $key_pril == '') {
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

		if ($operator != "0") {

			$operator_data = array(
				'key' => APIKEY_USERSIDE,
				'cat' => 'operator',
				'action' => 'get',
				'id' => $operator,
			);

			$operator_arr = object_to_array(json_decode(create_ticket($operator_data, USERSIDE)));
			if ($operator_arr['Result'] == 'OK') {
				if ($operator_arr['data'][$operator]['staff_id'] != '') {
				
					$staff_data = array(
						'key' => APIKEY_USERSIDE,
						'cat' => 'staff',
						'action' => 'get_data',
						'id' => $operator_arr['data'][$operator]['staff_id'],
					);

					$staff_arr = object_to_array(json_decode(create_ticket($staff_data, USERSIDE)));
					
					if ($staff_arr['Result'] == 'OK') {
						$staff = $operator_arr['data'][$operator]['staff_id'];
						print_r($staff_arr['data'][$staff]['name']);
					} else {
						print_r("Ошибка! Не могу найти персонал с таким ID.");
					}
				} else {
					print_r("Ошибка! У данного сотрудника нет staff_ID");
				}
			} else {
				print_r("Ошибка! ID оператора не верный.");
			}
		} else {
			print_r("Служебный");
		}
		
	}
}