<?php
class ControllerPrilClosetask extends Controller {
	public function index() {
		
		require_once 'function.php';
		
		$id_task = '1';
		$user = 'test';
		$key_pril = '100-200';
		
		foreach (object_to_array(retrieveJsonPostData()) as $pril_key => $pril_value) {
			switch ($pril_key) {
				case 'id_task':
					$id_task = $pril_value;
					break;
				case 'user':
					$user = $pril_value;
					break;
				case 'key_pril':
					$key_pril = $pril_value;
					break;
			}
		}
			
		if ($id_task == '' || $user == '' || $key_pril == '') {
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
		
		$userOperator = array(
			'key' => APIKEY_USERSIDE,
			'cat' => 'operator',
			'action' => 'get_id_by_login',
			'login' => $user,
		);

		$userside_operator = object_to_array(json_decode(create_ticket($userOperator, USERSIDE)));

		$operator_id = '';
		if ($userside_operator['Result'] == 'OK') {
			$operator_id = $userside_operator['id'];
		} else {
			print_r ($userside_operator['ErrorText']); //Operator Not Found
			exit;
		}
			//print_r($operator_id);
		if (TYPE_ID == '0') {
			
			$userClose = array(
				'key' => APIKEY_USERSIDE,
				'cat' => 'task',
				'action' => 'change_state',
				'id' => $id_task,
				'state_id' => STATE_ID_CLOSE,
				'operator_id' => $operator_id,
			);

			$userside_closeTask = object_to_array(json_decode(create_ticket($userClose, USERSIDE)));

			if ($userside_closeTask['Result'] == 'OK') {
				print_r ("Задание отправлено на проверку");	
			} else {
				print_r ($userside_closeTask['ErrorText']); //Operator Not Found
				exit;
			}
			
		} else {
			
			$pos = strpos(TYPE_ID, ",");

			if ($pos === false) {
				
				$userClose = array(
					'key' => APIKEY_USERSIDE,
					'cat' => 'task',
					'action' => 'change_state',
					'id' => $id_task,
					'state_id' => STATE_ID_TYPE,
					'operator_id' => $operator_id,
				);

				$userside_closeTask = object_to_array(json_decode(create_ticket($userClose, USERSIDE)));

				if ($userside_closeTask['Result'] == 'OK') {
					print_r ("Статус задания изменен");	
				} else {
					print_r ($userside_closeTask['ErrorText']); //Operator Not Found
					exit;
				}	
			
			} else {
				
				$stateid = explode(",", TYPE_ID);
				$stateidclose = explode(",", STATE_ID_TYPE);
				
				$getstate = array(
					'key' => APIKEY_USERSIDE,
					'cat' => 'task',
					'action' => 'show',
					'id' => $id_task,
				);

				$getstateTask = object_to_array(json_decode(create_ticket($getstate, USERSIDE)));

				if ($getstateTask['Result'] == 'OK') {

					$key = array_search($getstateTask['Data']['type']['id'], $stateid);
					
					if ($key) {
					
						$userClose = array(
							'key' => APIKEY_USERSIDE,
							'cat' => 'task',
							'action' => 'change_state',
							'id' => $id_task,
							'state_id' => $stateidclose[$key],
							'operator_id' => $operator_id,
						);

						$userside_closeTask = object_to_array(json_decode(create_ticket($userClose, USERSIDE)));

						if ($userside_closeTask['Result'] == 'OK') {
							print_r ("Статус задания изменен");	
						} else {
							print_r ($userside_closeTask['ErrorText']); //Operator Not Found
							exit;
						}
					
					} else {
						
						$userClose = array(
							'key' => APIKEY_USERSIDE,
							'cat' => 'task',
							'action' => 'change_state',
							'id' => $id_task,
							'state_id' => STATE_ID_CLOSE,
							'operator_id' => $operator_id,
						);

						$userside_closeTask = object_to_array(json_decode(create_ticket($userClose, USERSIDE)));

						if ($userside_closeTask['Result'] == 'OK') {
							print_r ("Задание отправлено на проверку");	
						} else {
							print_r ($userside_closeTask['ErrorText']); //Operator Not Found
							exit;
						}
						
					}
					
				} else {
					print_r ($getstateTask['ErrorText']);
					exit;
				}
				
			}
			
		}
		
	}
}
