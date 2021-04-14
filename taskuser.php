<?php
class ControllerPrilTaskuser extends Controller {
	public function index() {
		
		require_once 'function.php';
		
		$user_id = '47';
		$task_date = '2020-11-09';
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
		//print_r($arr_customer);
		if ($arr_customer[14] == 1) {
			$operator_data = array(
				'key' => APIKEY_USERSIDE,
				'cat' => 'staff',
				'action' => 'get_data',
				'id' => $user_id,
			);
			
			$operator_arr = object_to_array(json_decode(create_ticket($operator_data, USERSIDE)));
			if ($operator_arr['Result'] == 'OK') {
				$division_arr = $operator_arr['data'][$user_id]['division'];
				
				foreach ($division_arr as $division_key) {
					$division_id = $division_key['division_id'];
				}
				if(!empty($division_id)){
					$data_task = array(
						'key'           => APIKEY_USERSIDE,
						'cat'           => 'task',
						'action'        => 'get_list',
						'division_id'   => $division_id,
						'date_do_from'	=> $task_date,
						'date_do_to'	=> $task_date,
						'order_by'		=> 'date_do',
						'state_id' 		=> STATEID,
					);
					$data_task_two = array(
						'key'           => APIKEY_USERSIDE,
						'cat'           => 'task',
						'action'        => 'get_list',
						'staff_id'      => $user_id,
						'date_do_from'	=> $task_date,
						'date_do_to'	=> $task_date,
						'order_by'		=> 'date_do',
						'state_id' 		=> STATEID,
					);
					$userside_task = object_to_array(json_decode(create_ticket($data_task, USERSIDE)));
					$userside_task_two = object_to_array(json_decode(create_ticket($data_task_two, USERSIDE)));
					if (($userside_task['Result'] == 'OK' && $userside_task_two['Result'] == 'OK') && $userside_task_two['count'] != 0) {
						
						if($userside_task['list'] != $userside_task_two['list']){
							$userside_task['count'] = $userside_task['count'] + $userside_task_two['count'];
							if(!empty($userside_task['list'])){
								$userside_task['list'] = $userside_task['list'].", ".$userside_task_two['list'];
							} else {
								$userside_task['list'] = $userside_task_two['list'];
							}
						}
					}
				} else {
					
					$data_task = array(
						'key'           => APIKEY_USERSIDE,
						'cat'           => 'task',
						'action'        => 'get_list',
						'staff_id'      => $user_id,
						'date_do_from'	=> $task_date,
						'date_do_to'	=> $task_date,
						'order_by'		=> 'date_do',
						'state_id' 		=> STATEID,
					);
					$userside_task = object_to_array(json_decode(create_ticket($data_task, USERSIDE)));
					//print_r($userside_task);
				}
				// https://user.intronex.ru/api.php?key=1597535&cat=task&subcat=get_list&staff_id=24&state_id=15,6,3,1&date_do_from=2020-04-16&date_do_to=2020-04-16
				// https://userside.kichkas.net/api.php?key=A64578x&cat=task&subcat=get_list&division_id=22&state_id=1,2,3,4&date_do_from=2019-12-09&date_do_to=2019-12-09
				
			} else {
				print_r("Ошибка!");
				exit;
			}
		} else {
			
			$data_task = array(
				'key'           => APIKEY_USERSIDE,
				'cat'           => 'task',
				'action'        => 'get_list',
				'staff_id'      => $user_id,
				'date_do_from'	=> $task_date,
				'date_do_to'	=> $task_date,
				'order_by'		=> 'date_do',
				'state_id' 		=> STATEID,
			);

			// http://89.22.52.19/api.php?key=1597535&cat=task&subcat=get_list&staff_id=24&state_id=15,6,3,1&date_do_from=2019-04-08&date_do_to=2019-04-08
			// https://userside.kichkas.net/api.php?key=A64578x&cat=task&subcat=get_list&staff_id=25&state_id=1,2,3,4&date_do_from=2019-12-09&date_do_to=2019-12-09
			$userside_task = object_to_array(json_decode(create_ticket($data_task, USERSIDE)));
		}
		
		if ($userside_task['Result'] == 'OK') {
				if ($userside_task['count'] != "0") {
					
					$delTaskCount = 0;
					
					$user_task = $userside_task['list'];
					
					$task_list_arr = explode(",", $userside_task['list']);
					//print_r($task_list_arr);
					foreach ($task_list_arr as $task_list) {

						//https://user.intronex.ru/api.php?key=1597535&cat=task&subcat=show&staff_id=24&id=38357
						$data_task_list = array(
							'key'    => APIKEY_USERSIDE,
							'cat'    => 'task',
							'action' => 'show',
							'id'     => $task_list,
						);

						$task = create_ticket($data_task_list, USERSIDE);
						$taskres = object_to_array(json_decode($task));
						if ($taskres['Result'] == 'OK') {
							// получаем описание для типа задания
							$descr = array(
								'key' => APIKEY_USERSIDE,
								'cat' => 'task',
								'action' => 'get_catalog_type',
								'id' => $taskres['Data']['type']['id'],
							);
							$descr_arr = object_to_array(json_decode(create_ticket($descr, USERSIDE)));
							// получаем информацию о доме
							$notes = array(
								'key' => APIKEY_USERSIDE,
								'cat' => 'address',
								'action' => 'get_house',
								'id' => $taskres['Data']['address']['addressId'],
							);
							$notes_arr = object_to_array(json_decode(create_ticket($notes, USERSIDE)));
							if($notes_arr['Result'] == 'OK')// Вставляем комментарий к дому(заметку) в массив
							{
								$arrToInsert = json_decode($task, true);
								$arrToInsert['Data'] += ['task_comment' => $notes_arr['data'][$taskres['Data']['address']['addressId']]['task_comment']];
								$arrToInsert['data'] += ['task_comment' => $notes_arr['data'][$taskres['Data']['address']['addressId']]['task_comment']];
								$task = json_encode($arrToInsert, JSON_UNESCAPED_UNICODE);
							}
							else// Если запрос не прошёл, отправляем пустоту для проверки в приложении
							{
								$arrToInsert = json_decode($task, true);
								$arrToInsert['Data'] += ['task_comment' => ''];
								$arrToInsert['data'] += ['task_comment' => ''];
								$task = json_encode($arrToInsert, JSON_UNESCAPED_UNICODE);
							}
							
							if ($descr_arr['Result'] == 'OK') {
								$description_type_task = $descr_arr['Data'][$taskres['Data']['type']['id']]['description'];
							}
							
							print_r (STATEIDCLOSE."djkgjdksfgjkldsfjg".$task."djkgjdksfgjkldsfjg".$description_type_task."::");
							
						} else {
							print_r ("По данному заданию нет информации");
						}
					}
					$checkTasks = $userside_task['count'];
					if($checkTasks == '0'){
						print_r("Заданий нет.");
					}
				} else {
					print_r("Заданий нет.");
				}
		} else {
				print_r("Ошибка!");
		}
		
	}
}