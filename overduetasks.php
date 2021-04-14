<?php
class ControllerPrilOverduetasks extends Controller {
	public function index() {
		
		require_once 'function.php';
		
		$user_id = '34';
		$task_date = '2021-03-09';
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
		define('LIMIT_OVERDUE_TASKS', $arr_customer[13]);
		
		$data_task = array(
			'key'           => APIKEY_USERSIDE,
			'cat'           => 'task',
			'subcat'        => 'get_list',
			'staff_id'      => $user_id,
			'date_do_to'	=> $task_date,
			'state_id' 		=> STATEID,
			'order_by'		=> 'date_do',
		);

		if (LIMIT_OVERDUE_TASKS != 0) {
			$data_task += ['limit' => LIMIT_OVERDUE_TASKS];
		}
			
		$userside_task = object_to_array(json_decode(create_ticket($data_task, USERSIDE)));

		if ($userside_task['Result'] == 'OK') {
				if ($userside_task['count'] != "0") {
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
						//$task = str_replace("<br \/>", "\n", $task);
						
						// получаем описание для типа задания
						$taskres = object_to_array(json_decode($task));
						if ($taskres['Result'] == 'OK') {
							$descr = array(
								'key' => APIKEY_USERSIDE,
								'cat' => 'task',
								'subcat' => 'get_catalog_type',
								'id' => $taskres['Data']['type']['id'],
							);
							$descr_arr = object_to_array(json_decode(create_ticket($descr, USERSIDE)));
							//print_r($taskres);
							if ($descr_arr['Result'] == 'OK') {
								$description_type_task = $descr_arr['Data'][$taskres['Data']['type']['id']]['description'];
							}else{
								$description_type_task = "Не выполнено";
							}
						}
						
						print_r ($task."***".$description_type_task."_::_");
						
					}
				} else {
					print_r("Заданий нет.");
				}
		} else {
				print_r("Ошибка!");
		}
		
	}
}
