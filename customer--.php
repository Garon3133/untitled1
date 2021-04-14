<?php
class ControllerPrilCustomer extends Controller {
	public function index() {
		
		require_once 'function.php';
		
		$customer_id = '3158';
		$key_pril = '100-200';
		
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

		$customer = create_ticket($customer_task_list, USERSIDE);

		print_r ($customer);
		
	}
}
