<?php
class ControllerPrilClosetaskadditional extends Controller {
	public function index() {
		
		require_once 'function.php';
		
		$id_type = '257';
		$user = '24';
		$key_pril = '100-200';
		
		foreach (object_to_array(retrieveJsonPostData()) as $pril_key => $pril_value) {
			switch ($pril_key) {
				case 'id_type':
					$id_type = $pril_value;
					break;
				case 'user':
					$user = $pril_value;
					break;
				case 'key_pril':
					$key_pril = $pril_value;
					break;
			}
		}
			
		if ($id_type == '' || $user == '' || $key_pril == '') {
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
		define('STATEID', $arr_customer[3]);
		define('STATE_ID_CLOSE', $arr_customer[8]);
		define('TYPE_ID', $arr_customer[9]);
		define('STATE_ID_TYPE', $arr_customer[10]);
		
		
		// http://user.intronex.ru/api.php?key=1597535&cat=task&subcat=get_catalog_type&id=257
		// http://user.intronex.ru/api.php?key=1597535&cat=additional_data&action=get_list&section=task
		
		$typearr = array(
			'key' => APIKEY_USERSIDE,
			'cat' => 'task',
			'subcat' => 'get_catalog_type',
			'id' => $id_type,
		);

		$us_type = object_to_array(json_decode(create_ticket($typearr, USERSIDE)));

		if ($us_type['Result'] == 'OK') {
			$add_field_type = $us_type['Data'][$id_type]['additional_field_finalize'];
			
			if ($add_field_type) { 
				$getlistarr = array(
					'key' => APIKEY_USERSIDE,
					'cat' => 'additional_data',
					'action' => 'get_list',
					'section' => 'task',
				);
				
				$us_getlist = object_to_array(json_decode(create_ticket($getlistarr, USERSIDE)));

				$add_data = array();
				
				foreach ($us_getlist['data'] as $getlistkey => $getlistvalue) {
					if (array_key_exists($getlistkey, $add_field_type)) {
						array_push($add_data, $getlistvalue);
					}
				}
				
				print_r(json_encode($add_data));
			
			} else {
				print_r("Пусто!");
			}

		} else {
			print_r("Ошибка!");
		}

	}
}
