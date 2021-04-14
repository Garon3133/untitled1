<?php
class ControllerPrilTaskuser extends Controller {
	public function index() {
		
		require_once 'function.php';
		
		$user_id = '24';
		$task_date = '2019-08-27';
		$key_pril = '100-200';
		
		foreach (object_to_array(retrieveJsonPostData()) as $pril_key => $pril_value) {
			switch ($pril_key) {
				case 'user_id':
					$user_id = $pril_value;
					break;
				case 'task_date':
					$task_date = $pril_value;
					break;
				case 'key_pril':
					$key_pril = $pril_value;
					break;
			}
		}
			
		if ($user_id == '' || $task_date == '' || $key_pril == '') {
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
		define('STATEIDCLOSE', $arr_customer[8]);
		define('TYPE_ID', $arr_customer[9]);
		define('STATE_ID_TYPE', $arr_customer[10]);
		
		$data_task = array(
			'key'           => APIKEY_USERSIDE,
			'cat'           => 'task',
			'subcat'        => 'get_list',
			'staff_id'      => $user_id,
			'date_do_from'	=> $task_date,
			'date_do_to'	=> $task_date,
			'state_id' 		=> STATEID,
		);

		// http://89.22.52.19/api.php?key=1597535&cat=task&subcat=get_list&staff_id=24&state_id=15,6,3,1&date_do_from=2019-04-08&date_do_to=2019-04-08
		$userside_task = object_to_array(json_decode(create_ticket($data_task, USERSIDE)));
		//print_r($userside_task);
		if ($userside_task['Result'] == 'OK') {
				if ($userside_task['count'] != "0") {
					
					$stateid = explode(",", TYPE_ID);
					$stateidclose = explode(",", STATE_ID_TYPE);
					
					$user_task = $userside_task['list'];
					
					$task_list_arr = explode(",", $userside_task['list']);
					foreach ($task_list_arr as $task_list) {

						$data_task_list = array(
							'key'    => APIKEY_USERSIDE,
							'cat'    => 'task',
							'subcat' => 'show',
							'id'     => $task_list,
						);

						$task = create_ticket($data_task_list, USERSIDE);
						$taskres = object_to_array(json_decode($task));
						if ($taskres['Result'] == 'OK') {
							
							// получаем описание для типа задания
							$descr = array(
								'key' => APIKEY_USERSIDE,
								'cat' => 'task',
								'subcat' => 'get_catalog_type',
								'id' => $taskres['Data']['type']['id'],
							);
							$descr_arr = object_to_array(json_decode(create_ticket($descr, USERSIDE)));
							if ($descr_arr['Result'] == 'OK') {
								$description_type_task = $descr_arr['Data'][$taskres['Data']['type']['id']]['description'];
							}
							
							if (TYPE_ID == '0') {
								print_r (STATEIDCLOSE."***".$task."***".$description_type_task."::");
							} else {
								
								$pos = strpos(TYPE_ID, ",");

								if ($pos === false) {
									
									if ($taskres['Data']['type']['id'] == TYPE_ID) {
										print_r (STATE_ID_TYPE."***".$task."***".$description_type_task."::");
									} else {
										print_r (STATEIDCLOSE."***".$task."***".$description_type_task."::");
									}

								} else {
									
									$key = array_search($taskres['Data']['type']['id'], $stateid);
					
									if ($key) {
										print_r ($stateidclose[$key]."***".$task."***".$description_type_task."::");
									} else {
										print_r (STATEIDCLOSE."***".$task."***".$description_type_task."::");
									}
									
								}
								
							}
								
						} else {
							print_r ("По данному заданию нет информации");
						}
					}

				} else {
					print_r("Заданий нет.");
				}
		} else {
				print_r("Ошибка!");
		}
		
	}
}
