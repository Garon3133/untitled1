<?php
class ControllerPrilMap extends Controller {
	public function index() {
		
		require_once 'function.php';
		
		$house_id = '';
		$key_pril = '100-200';
		
		foreach (object_to_array(retrieveJsonPostData()) as $pril_key => $pril_value) {
			switch ($pril_key) {
				case 'house_id':
					$house_id = $pril_value;
					break;
				case 'key_pril':
					$key_pril = $pril_value;
					break;
			}
		}
			
		if ($house_id == '' || $key_pril == '') {
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

		$house_data = array(
			'key' => APIKEY_USERSIDE,
			'cat' => 'address',
			'action' => 'get_house',
			'id' => $house_id,
		);
		$house = object_to_array(json_decode(create_ticket($house_data, USERSIDE)));

		if ($house['Result'] == 'OK') {
			foreach ($house['data'] as $house_coord) {
				foreach ($house_coord as $house_coord_key => $house_coord_value) {
					if ($house_coord_key == "coordinates") {
						if(!empty($house_coord_value)) {
							print_r (json_encode(current($house_coord_value), JSON_FORCE_OBJECT));
						}
					}
				}
			}
		}
		
	}
}
