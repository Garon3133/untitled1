<?php
class ControllerPrilNotificationTask extends Controller {
	public function index($data) {
		require_once 'function.php';
		require_once 'firebaseNotification.php';
		$this->load->model('pril/allow');
		$customerId = $data['customerId'];
		$apiKey = $data['key'];
		$userSide = $data['userside'];
		$server = $this->model_pril_allow->getTokenFor($customerId);
		foreach($server as $element){
			$fcmToken = $element['fcm_token'];
			if($fcmToken != ''){
				$server = $this->model_pril_allow->checkImeiWToken($fcmToken);
				$Imei = $server['0']['imei'];
				if($Imei != ''){
					$server = $this->model_pril_allow->operatorLoginSelect($Imei, $fcmToken);
					$login = $server['0']['login_us'];
					$getOperatorId = array(
						'key'    => $apiKey,
						'cat'    => 'operator',
						'action' => 'get_id_by_login',
						'login'  => $login,
					);
					$userside_OperatorId = object_to_array(json_decode(create_ticket($getOperatorId, $userSide)));
					$getOperatorData = array(
						'key'    => $apiKey,
						'cat'    => 'operator',
						'action' => 'get',
						'id'  => $userside_OperatorId['id'],
					);
					$userside_OperatorData = object_to_array(json_decode(create_ticket($getOperatorData, $userSide)));
					$getStaffId = array(
						'key'    	 => $apiKey,
						'cat'    	 => 'staff',
						'action' 	 => 'get_staff_id',
						'data_typer' => 'name',
						'data_value' => $userside_OperatorData['data'][$userside_OperatorId['id']]['name'],
					);
					$userside_StaffId = object_to_array(json_decode(create_ticket($getStaffId, $userSide)));
					$StaffTasks = array(
						'key'      		=> $apiKey,
						'cat'      		=> 'task',
						'subcat'   		=> 'get_list',
						'date_do_from'	=> date("Y-m-d"),
						'date_do_to'	=> date("Y-m-d", strtotime("+1 day")),
						'staff_id' 		=> $userside_StaffId['id'],
					);
					$userside_StaffTasks = object_to_array(json_decode(create_ticket($StaffTasks, $userSide)));
					$delTaskCount = 0;
					if(is_array($userside_StaffTasks['list'])){
						foreach($userside_StaffTasks['list'] as $TaskId){
							$data_task_list = array(
								'key'    => APIKEY_USERSIDE,
								'cat'    => 'task',
								'subcat' => 'show',
								'id'     => $TaskId,
							);
							$userside_TaskId = object_to_array(json_decode(create_ticket($data_task_list, USERSIDE)));
							if($userside_TaskId['Data']['type']['id'] == '291'){
								$delTaskCount++;
							}
						}
					}
					$TaskCount = $userside_StaffTasks['count'] - $delTaskCount;// Кол-во тасков, записанных в юзерсайде
					//$TaskCount = $TaskCount + 1;
					print_r("\nЛогин: ".$login.", Айди: ".$userside_StaffId['id'].".   \nТаски:\n");
					print_r($userside_StaffTasks);
					$server = $this->model_pril_allow->checkCountTasks($Imei, $fcmToken);
					$countTask = $server['0']['count_task'];// Кол-во тасков, записанных в БД
					if($server != null){
						print_r("Тасков в БД - ".$countTask."\nТасков в юзерсайде - ".$TaskCount."\n");
						$server = $this->model_pril_allow->reloadCountTasks($login,$TaskCount);
					}
				}else{
					print_r("\n(У данного пользователя отсутсвует прикреплённый имэй. Imei = ".$Imei.") ");
				}
			}else{
				print_r("/Нет FCM токена\\ ");
			}	
		}
	}
}
?>
