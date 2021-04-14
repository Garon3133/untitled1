<?php
class ControllerPrilNotificationTaskT extends Controller {
	public function index() {
		require_once 'function.php';
		require_once 'firebaseNotification.php';
		$this->load->model('pril/allow');
		$customerId = "63";
		$apiKey = "nhfqntr";
		$userSide = "https://us.trytek.ru";
		$customer = $this->model_pril_allow->getCompanyCustomField($customerId);
		$arr_customer = object_to_array(json_decode($customer[0]['custom_field']));
		define('STATEID', $arr_customer[3]);
		$server = $this->model_pril_allow->getTokenFor($customerId);
		foreach($server as $element){
			$fcmToken = $element['fcm_token'];
			print_r("\n?????? ".$fcmToken." Проверка входящих токенов ");
			if($fcmToken != ''){
				$server = $this->model_pril_allow->checkImeiWToken($fcmToken);
				$Imei = $server['0']['imei'];
				if($Imei != ''){
					$server = $this->model_pril_allow->operatorLoginSelect($Imei, $fcmToken);
					$login = $server['0']['login_us'];
					$server = $this->model_pril_allow->getTypeId($Imei, $login, $fcmToken);
					$taskTypeId = $server[0]['group_id'];
					$getOperatorId = array(
						'key'    => $apiKey,
						'cat'    => 'operator',
						'action' => 'get_id_by_login',
						'login'  => $login,
					);
					$userside_OperatorId = object_to_array(json_decode(create_ticket($getOperatorId, $userSide)));
					print_r($getOperatorId);
					if($userside_OperatorId['Result'] == 'OK'){
						$getOperatorData = array(
							'key'    => $apiKey,
							'cat'    => 'operator',
							'action' => 'get',
							'id'  => $userside_OperatorId['id'],
						);
						$userside_OperatorData = object_to_array(json_decode(create_ticket($getOperatorData, $userSide)));
						if($userside_OperatorData['Result'] == 'OK'){
							$getStaffId = array(
								'key'    	 => $apiKey,
								'cat'    	 => 'staff',
								'action' 	 => 'get_staff_id',
								'data_typer' => 'name',
								'data_value' => $userside_OperatorData['data'][$userside_OperatorId['id']]['name'],
							);
							$userside_StaffId = object_to_array(json_decode(create_ticket($getStaffId, $userSide)));
							if($userside_StaffId['Result'] == 'OK'){
								if($taskTypeId == '0'){
									$StaffTasks = array(
										'key'      		=> $apiKey,
										'cat'      		=> 'task',
										'subcat'   		=> 'get_list',
										'state_id' 		=> STATEID,
										'date_do_from'	=> date("Y-m-d"),
										'date_do_to'	=> date("Y-m-d"),
										'staff_id' 		=> $userside_StaffId['id'],
										
									);
								}else{
									$StaffTasks = array(
										'key'      		=> $apiKey,
										'cat'      		=> 'task',
										'subcat'   		=> 'get_list',
										'state_id' 		=> '6',
										'date_do_from'	=> date("Y-m-d"),
										'date_do_to'	=> date("Y-m-d"),
										'type_id'		=> $taskTypeId,
									);
								}
								$userside_StaffTasks = object_to_array(json_decode(create_ticket($StaffTasks, $userSide)));
								if($userside_StaffTasks['Result'] == 'OK'){
									$TaskCount = $userside_StaffTasks['count'];// Кол-во тасков, записанных в юзерсайде
									//$TaskCount = $TaskCount + 1;
									print_r("\nЛогин: ".$login.", Айди: ".$userside_StaffId['id'].".   \nТаски:\n");
									print_r($userside_StaffTasks);
									$server = $this->model_pril_allow->checkCountTasks($Imei, $fcmToken);
									$countTask = $server['0']['count_task'];// Кол-во тасков, записанных в БД
									if($server != null){
										//if($login == 'andrey' || $login == 'test'){
											print_r("Тасков в БД - ".$countTask."\nТасков в юзерсайде - ".$TaskCount."\n");
											if($countTask < $TaskCount){
												if($taskTypeId == '0'){
													$newTasks = $TaskCount - $countTask;				
													//$Notification = new FirebaseNotificationClass;
													//$Notification->send_notification($fcmToken, $newTasks);
													print_r("<-Выслано-> ".$login." <-Выслано-> ".date("r")." <-Выслано->\n");
												}else{
													$newTasks = $TaskCount - $countTask;				
													//$Notification = new FirebaseNotificationClassAndr;
													//$Notification->send_notification($fcmToken, $newTasks);
													//$Notification = new FirebaseNotificationClassUnity;
													//$Notification->send_notification($fcmToken, $newTasks);
													print_r("<-Выслано-> ".$login." <-Выслано-> ".date("r")." <-Выслано->\n");
												}
											}else{
												if($countTask > $TaskCount){
													$server = $this->model_pril_allow->getTypeId($Imei, $login);
													if($taskTypeId != '0'){
														$server = $this->model_pril_allow->updateBD("count_task", $TaskCount, "Login_us", $login."' AND customer_id = '".$customerId."' AND group_id = '".$taskTypeId);
														print_r("Кол-во тасков изменено их = ".$TaskCount." Логин = ".$login);
													}else{
														$server = $this->model_pril_allow->updateBD("count_task", $TaskCount, "Login_us", $login."' AND customer_id = '".$customerId."' AND group_id = '0");
														print_r("Кол-во тасков изменено их = ".$TaskCount." Логин = ".$login);
													}
												}else{
													if(Date("G") > 4 && Date("G") < 21){
														print_r(" TEST".Date("G"));
													}
													print_r("{Отсутсвуют новые таски} ".$login." {Отсутсвуют новые таски} ".date("r")." {Отсутсвуют новые таски}\n"."----------------------\n");
												}
											}
										//}
									}
								}
							
							}
						}
					}
				}else{
					print_r("\n(У данного пользователя отсутсвует прикреплённый имэй. Imei = ".$Imei.") ");
				}
			}else{
				print_r("TEST".Date("G"));
				
				print_r("/Нет FCM токена\\ ".$customerId." Для теста, да ");
			}	
		}
	}
}
?>