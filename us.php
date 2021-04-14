<?php
class ControllerPrilUs extends Controller {
	public function index() {
		
		require_once 'function.php';
		
		$key = '';
		$key_pril = '100-200';
		
		foreach (object_to_array(retrieveJsonPostData()) as $pril_key => $pril_value) {
			switch ($pril_key) {
				case 'key':
					$key = $pril_value;
					break;
				case 'key_pril':
					$key_pril = $pril_value;
					break;
			}
		}
			
		if ($key == '' || $key_pril == '') {
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

		define('USERSIDE', $arr_customer[2]);

		if ($key != '83fd6a28a1f6be4468dbbe46e8daf6ee') {
			echo "Данные переданы не верно!";
			exit;
		} else {
			print_r(USERSIDE);
		}
		
	}
}
