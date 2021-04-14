<?php
class ControllerPrilGetConf extends Controller {
	public function index() {
		
		require_once 'function.php';
	
		$Check = '0';
		$key_pril = '100-200';
		
		foreach (object_to_array(retrieveJsonPostData()) as $pril_key => $pril_value) {
			switch ($pril_key) {
				case 'key_pril':
					$key_pril = $pril_value;
					break;
				case 'Check':
					$Check = $pril_value;
					break;
			}
		}
		
		if ($key_pril == '' || $Check == '') {
			echo "Данные переданы не верно!";
			exit;
		}
		
		$this->load->model('pril/android');
		
		$customer = $this->model_pril_android->getCustomer($key_pril);
		
		if (!$customer) {
			print_r("Ошибка!");
			exit;
		}
		
		if($Check == 1){
			$arr_customer = $customer[0]['custom_field'];
			print_r($arr_customer."INOINUIBVUHBNJJOIBUBI"."kije4jn5uj3hnjk8");
		} else if ($Check == 0){
			print_r("kije4jn5uj3hnjk8");
		}
		
		/*
		define('APIKEY_USERSIDE', $arr_customer[1]);
		define('USERSIDE', $arr_customer[2]);
		define('STATEIDCLOSE', $arr_customer[8]);
		define('RELATEDTASKID', $arr_customer[12]);
		define('TYPE_ID', $arr_customer[9]);
		define('STATE_ID_TYPE', $arr_customer[10]);
		*/
		
	}
}
?>