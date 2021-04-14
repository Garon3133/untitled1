<?php
class ControllerPrilCheck extends Controller {
	public function index() {
		
		require_once 'function.php';
	
		$key_pril = '100-200';
		
		foreach (object_to_array(retrieveJsonPostData()) as $pril_key => $pril_value) {
			switch ($pril_key) {
				case 'key_pril':
					$key_pril = $pril_value;
					break;
			}
		}
		
		if ($key_pril == '') {
			echo "Данные переданы не верно!";
			exit;
		}
		
		$this->load->model('pril/android');
		
		$customer = $this->model_pril_android->getCustomer($key_pril);
		$arr_customer = object_to_array(json_decode($customer[0]['custom_field']));
		if (!$customer) {
			print_r("Ошибка!");
			exit;
		}
		print_r($customer[0]['custom_field']));
		
	}
}
?>