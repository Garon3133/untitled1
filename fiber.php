<?php
class ControllerPrilFiber extends Controller {
	public function index() {
		
		require_once 'function.php';
		
		$related_id = '';
		$key_pril = '100-200';
		
		foreach (object_to_array(retrieveJsonPostData()) as $pril_key => $pril_value) {
			switch ($pril_key) {
				case 'related_id':
					$related_id = $pril_value;
					break;
				case 'key_pril':
					$key_pril = $pril_value;
					break;
			}
		}
			
		if ($related_id == '' || $key_pril == '') {
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

		$related_list = array(
			'key' 			=> APIKEY_USERSIDE,
			'cat' 			=> 'fiber',
			'action' 		=> 'get_list',
			'object_id' 	=> $related_id,
		);

		$related_json = create_ticket($related_list, USERSIDE);
		$related_arr = object_to_array(json_decode($related_json));

		if ($related_arr['Result'] == 'OK') {
			print_r ($related_json);
		} else {
			print_r ($related_arr['ErrorText']);
		}
		
	}
}
