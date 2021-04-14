<?php
class ControllerPrilSklad extends Controller {
	public function index() {

		require_once 'function.php';
		
		$user_id = '';
		$key_pril = '100-200';
		
		foreach (object_to_array(retrieveJsonPostData()) as $pril_key => $pril_value) {
			switch ($pril_key) {
				case 'user_id':
					$user_id = $pril_value;
					break;
				case 'key_pril':
					$key_pril = $pril_value;
					break;
			}
		}
		
		if ($user_id == '' || $key_pril == '') {
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
	
		// Получение наименований каталога ТМЦ
		$data_inventory_section_catalog = array(
			'key'           => APIKEY_USERSIDE,
			'cat'           => 'inventory',
			'action'        => 'get_inventory_section_catalog',
		);
			
		$arr_inventory_section_catalog = object_to_array(json_decode(create_ticket($data_inventory_section_catalog, USERSIDE)));

		if ($arr_inventory_section_catalog['Result'] == 'OK') {

			// список разделов каталога
			$data_inventory_catalog = array(
				'key'           => APIKEY_USERSIDE,
				'cat'           => 'inventory',
				'action'        => 'get_inventory_catalog',
			);
			
			$arr_inventory_catalog = object_to_array(json_decode(create_ticket($data_inventory_catalog, USERSIDE)));

			if ($arr_inventory_catalog['Result'] == 'OK') {
			
				$data_sklad = array(
					'key'           => APIKEY_USERSIDE,
					'cat'           => 'inventory',
					'action'        => 'get_inventory_amount',
					'location'      => 'staff',
					'object_id'		=> $user_id,
				);
			
				$userside_sklad = object_to_array(json_decode(create_ticket($data_sklad, USERSIDE)));

				if ($userside_sklad['Result'] == 'OK') {
					
					$tmc = [];
					$count_userside_sklad  = count($userside_sklad['data']);
					
					foreach ($userside_sklad['data'] as $sklad) {
						
						$result = []; // список разделов каталога
						$result1 = []; // наименования каталога ТМЦ
						search_key($sklad['catalog_id'], $arr_inventory_catalog['data'], $result);
						$inventory_catalog = $result[0]['name'];
						search_key($result[0]['inventory_section_catalog_id'], $arr_inventory_section_catalog['data'], $result1);
						$inventory_section_catalog = $result1[0]['name'];
						
						$result2 = [];
						search_key($inventory_section_catalog, $tmc, $result2);
						if (!empty($result2)) {
							
							$array_key_inventory = $tmc[$inventory_section_catalog];
							$array_inventory = [
								"name" => html_entity_decode($inventory_catalog),
								"document_date" => $sklad['document_date'],
								"serial_number" => $sklad['serial_number'],
							];
							array_push($array_key_inventory, $array_inventory);
							
							$tmc = array_replace($tmc,
								array_fill_keys(
									array_keys($tmc, $tmc[$inventory_section_catalog]),
										$array_key_inventory
								)
							);
						} else {
							$array_key_inventory = [];
							$array_inventory = [
								"name" => html_entity_decode($inventory_catalog),
								"document_date" => $sklad['document_date'],
								"serial_number" => $sklad['serial_number'],
							];
							array_push($array_key_inventory, $array_inventory);

							$tmc[$inventory_section_catalog] = $array_key_inventory;
						}
					}
					
					print_r(json_encode($tmc)."=_=".$count_userside_sklad);
				}
			} else {
				print_r("Каталог не доступен");
			}
		} else {
			print_r("Разделы каталога не доступны");
		}
	
	}
}
