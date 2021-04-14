<?php
class ControllerPrilTesttest extends Controller {
	public function index() {

		require_once 'function.php';
		
		$key_pril = '100-200';
		
		$this->load->model('pril/android');
		$customer = $this->model_pril_android->getCustomer($key_pril);
		
		$arr_customer = object_to_array(json_decode($customer[0]['custom_field']));
		print_r($arr_customer);
		
		$verUs = $this->model_pril_android->getCustomFieldUS($arr_customer[17]);
		print_r($verUs);
	
	}
}