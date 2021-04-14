<?php
class ControllerPrilChangeStateId extends Controller {
	public function index() {
		
		require_once 'function.php';

		
		/*$stateId = "";
		$taskIdForStaff ="";
		$key_pril ="100-200";*/
		
		foreach (object_to_array(retrieveJsonPostData()) as $pril_key => $pril_value) {
			switch ($pril_key) {
				case 'stateId':
					$stateId = $pril_value;
					//print_r("Token: ".$Token."//");
					break;
				case 'taskIdForStaff':
					$taskIdForStaff = $pril_value;
					//print_r("Imei: ".$ID."//");
					break;
				case 'key-prill':
					$key_pril = $pril_value;
					//print_r("Imei: ".$ID."//");
					break;
			}
		}
		if($stateId != '' && $taskIdForStaff != '' && $key_pril != ''){
			$this->load->model('pril/android');
			
			$customer = $this->model_pril_android->getCustomer($key_pril);
			
			if (!$customer) {
				print_r("Ошибка!");
				exit;
			}
	
			$arr_customer = object_to_array(json_decode($customer[0]['custom_field']));
	
			define('APIKEY_USERSIDE', $arr_customer[1]);
			define('USERSIDE', $arr_customer[2]);
			
			$Change_state_id = array( 			// Изменение статуса для принятия задания
					'key' => APIKEY_USERSIDE,
					'cat' => 'task',
					'subcat' => 'change_state',
					'id' => $taskIdForStaff,
					'state_id' => $stateId			
			);
			
			$userside_Change_state_id = object_to_array(json_decode(create_ticket($Change_state_id, USERSIDE)));
			
			if($userside_Change_state_id['Result'] === 'OK'){
				print_r("Задание завершено".$stateId);
			}
		}
		
	}
}
