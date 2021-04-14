<?php
class ControllerPrilNewcoord extends Controller {
	public function index() {

		require_once 'function.php';
		
		$id_house = '';
		$coord = '';
		$key_pril = '100-200';
		
		foreach (object_to_array(retrieveJsonPostData()) as $pril_key => $pril_value) {
			switch ($pril_key) {
				case 'id_house':
					$id_house = $pril_value;
					break;
				case 'coord':
					$coord = $pril_value;
					break;
				case 'key_pril':
					$key_pril = $pril_value;
					break;
			}
		}
		
		if ($id_house == '' || $coord == '' || $key_pril == '') {
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
		
		define('APIKEY_USERSIDE', $arr_customer[1]);
		define('USERSIDE', $arr_customer[2]);
	
		$getHouse = array(
			'key'			=> APIKEY_USERSIDE,
			'cat'			=> 'address',
			'action'		=> 'get_house',
			'id'			=> $id_house,
		);

		$result_get_house = object_to_array(json_decode(create_ticket($getHouse, USERSIDE)));

		if ($result_get_house['Result'] == 'OK') {
			
			foreach ($result_get_house['data'] as $get_house_list) {
				$house_building_id = $get_house_list['building_id'];
			}
			
			$coordinates = "[[".$coord."],[".$coord."],[".$coord."],[".$coord."]]";

			if ($id_house != 0) {
				$userAdress = array(
					'key'			=> APIKEY_USERSIDE,
					'cat'			=> 'address',
					'action'		=> 'edit_building_coord',
					'id'			=> $house_building_id,
					'coord'			=> $coordinates,
				);

				$userside_adress = object_to_array(json_decode(create_ticket($userAdress, USERSIDE)));

				if ($userside_adress['Result'] == 'OK') {
					print_r ("555");
				} else {
					print_r ($userside_adress); //Operator Not Found
					exit;
				}
			} else {
				print_r ("Адрес объекта не указан");
			}

		} else {
			print_r ("Адрес объекта не указан");
		}
	
	}
}
