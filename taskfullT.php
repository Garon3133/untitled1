<?php
class ControllerPrilTaskfullT extends Controller {
	public function index() {
		
		require_once 'function.php';
		
		$task_id = '75398';
		$user = '70';
		$key_pril = '100-200';
		
		foreach (object_to_array(retrieveJsonPostData()) as $pril_key => $pril_value) {
			switch ($pril_key) {
				case 'task_id':
					$task_id = $pril_value;
					break;
				case 'user':
					$user = $pril_value;
					break;
				case 'key_pril':
					$key_pril = $pril_value;
					break;
			}
		}
			
		if ($task_id == '' || $user == '' || $key_pril == '') {
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
		define('STATEIDCLOSE', $arr_customer[8]);
		define('RELATEDTASKID', $arr_customer[12]);
		define('TYPE_ID', $arr_customer[9]);
		define('STATE_ID_TYPE', $arr_customer[10]);
		
		// получаем id оператора
		$operator_list = array(
			'key' 			=> APIKEY_USERSIDE,
			'cat' 			=> 'operator',
			'action' 		=> 'get_id_by_login',
			'login' 		=> $user,
		);
		$operator_arr = object_to_array(json_decode(create_ticket($operator_list, USERSIDE)));
		if ($operator_arr['Result'] == 'OK') {
			$user_id = $operator_arr['id'];
			//print_r($user_id);
		} else {
			$user_id = "";
		}
		$data_task_list = array(
			'key' 			=> APIKEY_USERSIDE,
			'cat' 			=> 'task',
			'subcat' 		=> 'show',
			'id' 			=> $task_id,
			'operator_id' 	=> $user_id,
		);
		$task = create_ticket($data_task_list, USERSIDE);
	
		// получаем дополнительную инфу по дому
		$house = object_to_array(json_decode($task));
		if ($house['Result'] == 'OK') {	
			if ($house['Data']['address']['addressId'] != 0) {
				$house_list = array(
					'key' => APIKEY_USERSIDE,
					'cat' => 'address',
					'action' => 'get_house',
					'id' => $house['Data']['address']['addressId'],
				);
				$house_ok = object_to_array(json_decode(create_ticket($house_list, USERSIDE)));
				if ($house_ok['Result'] == 'OK') {
					$house_arr = create_ticket($house_list, USERSIDE);
					$arrToInsert = json_decode($task, true);
					$arrToInsert['Data'] += ['task_comment' => $house_ok['data'][$house['Data']['address']['addressId']]['task_comment']];
					$arrToInsert['data'] += ['task_comment' => $house_ok['data'][$house['Data']['address']['addressId']]['task_comment']];
					$task = json_encode($arrToInsert, JSON_UNESCAPED_UNICODE);
					
				} else {
					$arrToInsert = json_decode($task, true);
					$arrToInsert['Data'] += ['task_comment' => ''];
					$arrToInsert['data'] += ['task_comment' => ''];
					$task = json_encode($arrToInsert, JSON_UNESCAPED_UNICODE);
					$house_arr = 0;
				}
			} else {
				$arrToInsert = json_decode($task, true);
				$arrToInsert['Data'] += ['task_comment' => ''];
				$arrToInsert['data'] += ['task_comment' => ''];
				$task = json_encode($arrToInsert, JSON_UNESCAPED_UNICODE);
				$house_arr = 0;
				$house_arr = 0;
			}
		}
		if ($house['Result'] == 'OK') {
			$arrToInsert = json_decode($task, true);
			//print_r($arrToInsert);
			foreach ($arrToInsert['Data']['comments'] as $val => $ids){
				if(!isset($arrToInsert['Data']['comments'][$val]['id'])) unset($arrToInsert['Data']['comments'][$val]);
			}		
			$task = json_encode($arrToInsert, JSON_UNESCAPED_UNICODE);
			// связанные задания
			$related_task_id = array(
				'key' => APIKEY_USERSIDE,
				'cat' => 'task',
				'subcat' => 'get_related_task_id',
				'id' => $task_id,
			);
			$task_related_arr = object_to_array(json_decode(create_ticket($related_task_id, USERSIDE)));
			$task_related = "";
			if ($task_related_arr['Result'] == 'OK') {
				if ($task_related_arr['Result'] != '-1') {
					$task_related_list = explode(",", $task_related_arr['Data']);
					foreach ($task_related_list as $task_list) {
						if (RELATEDTASKID != 0) {
							$data_task_list = array(
								'key'    		=> APIKEY_USERSIDE,
								'cat'    		=> 'task',
								'subcat' 		=> 'show',
								'id'     		=> $task_list,
								'operator_id' 	=> $user_id,
							);
							$task_type = object_to_array(json_decode(create_ticket($data_task_list, USERSIDE)));
							$task_related_list_conf = explode(",", RELATEDTASKID);
							foreach ($task_related_list_conf as $list_conf_key) {
								if ($list_conf_key == $task_type['Data']['type']['id']) {
									$task_related = $task_related.$task_list."||";
								}
							}
						} else {
							$task_related = $task_related.$task_list."||";
						}
					}
				} else {
					$task_related = $task_related_arr['Data'];
				}
			} else {
				$task_related = $task_related_arr['ErrorText']; // unknown_id
			}
			if ($task_related == '') {$task_related = '-1';}
		
			// получаем описание для типа задания
			$descr = array(
				'key' => APIKEY_USERSIDE,
				'cat' => 'task',
				'subcat' => 'get_catalog_type',
				'id' => $house['Data']['type']['id'],
			);
			$descr_arr = object_to_array(json_decode(create_ticket($descr, USERSIDE)));
			if ($descr_arr['Result'] == 'OK') {
				$description_type_task = $descr_arr['Data'][$house['Data']['type']['id']]['description'];
			}		
		
			if (TYPE_ID == '0') {
				print_r (STATEIDCLOSE."***".$task."***".$task_related."***".$house_arr."***".$description_type_task);
				//print_r(json_decode($task, true));
			} else {
									
				$pos = strpos(TYPE_ID, ",");
		
				if ($pos === false) {
		
					if ($house['Data']['type']['id'] == TYPE_ID) {
						print_r (STATE_ID_TYPE."***".$task."***".$task_related."***".$house_arr."***".$description_type_task);
					} else {
						print_r (STATEIDCLOSE."***".$task."***".$task_related."***".$house_arr."***".$description_type_task);
					}							
		
				} else {
		
					$stateid = explode(",", TYPE_ID);
					$stateidclose = explode(",", STATE_ID_TYPE);
		
					$key = array_search($house['Data']['type']['id'], $stateid);
		
					if ($key) {
						print_r ($stateidclose[$key]."***".$task."***".$task_related."***".$house_arr."***".$description_type_task);
					} else {
						print_r (STATEIDCLOSE."***".$task."***".$task_related."***".$house_arr."***".$description_type_task);
					}
					
				}
				
			}
		
		} else {
			print_r("Ошибка юзерсайда");
		}
	}
}
