<?php
class ControllerPrilNotificationTask extends Controller {
	public function index($data) {
		if(Date("G") > 5 && Date("G") < 18){
			require_once 'function.php';
			require_once 'firebaseNotification.php';
			$this->load->model('pril/allow');
			$customerId = $data['customerId'];
			$apiKey = $data['key'];
			$userSide = $data['userside'];
			$customer = $this->model_pril_allow->getCompanyCustomField($customerId);
			$arr_customer = object_to_array(json_decode($customer[0]['custom_field']));
			define('STATEID', $arr_customer[3]);
			$server = $this->model_pril_allow->getTokenFor($customerId);
			if(!empty($server)){
				foreach($server as $element){
					$fcmToken = $element['fcm_token'];
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
												'staff_id' 		=> $userside_StaffId['id'],
												'type_id'		=> $taskTypeId,
											);
										}
										$userside_StaffTasks = object_to_array(json_decode(create_ticket($StaffTasks, $userSide)));
										if($userside_StaffTasks['Result'] == 'OK'){
											$TaskCount = $userside_StaffTasks['count'];// Кол-во тасков, записанных в юзерсайде
											//if($login == 'test')$TaskCount = $TaskCount + 1;
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
														$newTasks = $newTasks."-z";				
														$Notification = new FirebaseNotificationClass;
														$Notification2 = new FirebaseNotificationClass2;
														$Notification3 = new FirebaseNotificationClass3;
														$Notification->send_notification($fcmToken, $newTasks);
														$Notification2->send_notification($fcmToken, $newTasks);
														$Notification3->send_notification($fcmToken, $newTasks);
														print_r("<-Выслано-> ".$login.$taskTypeId.$newTasks." <-Выслано-> ".date("r")." <-Выслано->\n");
													}else{
														$newTasks = $TaskCount - $countTask;			
														$newTasks = $newTasks."-z";
														$Notification = new FirebaseNotificationClassUnity;
														$Notification->send_notification($fcmToken, $newTasks);
														print_r("<-Выслано-> ".$login.$taskTypeId."Dlya ne vzroslogo aga"." <-Выслано-> ".date("r")." <-Выслано->\n");
													}
												}else{
													if($countTask > $TaskCount){
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
						
						print_r("/Нет FCM токена\\ ");
					}	
				}
			} else {
				print_r("\nОтсутсвуют рабочие данной компании в базе");
			}
		}else{
			print_r("Слишком рано для отправки уведомлений, сейчас - ".Date("r")." ".Date("G"));	
		}
	}
}
?>