<?php
class ControllerPrilNotificationComm extends Controller {
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
			$stateId = $arr_customer[3];
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
							if($userside_OperatorId['Result'] == 'OK') {
								//print_r($userside_OperatorId);
								$getOperatorData = array(
									'key'    => $apiKey,
									'cat'    => 'operator',
									'action' => 'get',
									'id'  => $userside_OperatorId['id'],
								);
								$userside_OperatorData = object_to_array(json_decode(create_ticket($getOperatorData, $userSide)));
								if($userside_OperatorData['Result'] == 'OK') {
									//print_r($userside_OperatorData);
									//if($login == "test") {
										$getStaffId = array(
											'key'    	 => $apiKey,
											'cat'    	 => 'staff',
											'action' 	 => 'get_staff_id',
											'data_typer' => 'name',
											'data_value' => $userside_OperatorData['data'][$userside_OperatorId['id']]['name'],
										);
										$userside_StaffId = object_to_array(json_decode(create_ticket($getStaffId, $userSide)));
										//print_r($userside_StaffId);
										if($taskTypeId == '0'){
											$StaffTasks = array(
												'key'      		=> $apiKey,
												'cat'      		=> 'task',
												'subcat'   		=> 'get_list',
												'state_id' 		=> $stateId,
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
										$userside_task = object_to_array(json_decode(create_ticket($StaffTasks, $userSide)));
										$i = 0;
										//print_r($userside_task);
										if($userside_task['count'] > 0) {
											$task_list_arr = explode(",", $userside_task['list']);
											foreach ($task_list_arr as $task_id) {
												$i = 0;
												$data_task_list = array(
													'key' 			=> $apiKey,
													'cat' 			=> 'task',
													'subcat' 		=> 'show',
													'id' 			=> $task_id,
													'operator_id' 	=> $userside_OperatorId['id'],
												);
												$task = object_to_array(json_decode(create_ticket($data_task_list, $userSide)));
												if(isset($task['Data']['comments'])) {
													foreach($task['Data']['comments'] as $comment) {
														if(isset($comment['id'])) {
															//print_r($comment);
															$i++;
														}
													}
												}
												/*if($login == "test"){
													$i++;
												}*/
												$fd = fopen("catalog/controller/pril/people.txt", 'r') or die("не удалось открыть файл");
												$check = false;
												while(!feof($fd)){
													$str = fgets($fd);
													$str = str_replace("\n","",$str);
													$str_arr = explode("-",$str);
													if($str_arr[0] == $login && $str_arr[1] == $Imei && $str_arr[2] == $customerId && $str_arr[3] == $task['Data']['id']){
														print_r("\nЛогин: ".$login.", Айди: ".$userside_StaffId['id'].", Таск айди: ".$task_id.".\n");
														print_r("Комментариев в БД - ".$str_arr[4]."\nКомментариев в юзерсайде - ".$i."\n");
														if($i > $str_arr[4]){
															if($taskTypeId == '0') {
																$newTasks = $i - $str_arr[4];	
																$newTasks = $newTasks."-c-".$task['Data']['id'];
																//if($login == "test" && $login == "andrey"){
																	$Notification = new FirebaseNotificationClass;
																	$Notification2 = new FirebaseNotificationClass2;
																	$Notification3 = new FirebaseNotificationClass3;
																	$Notification->send_notification($fcmToken, $newTasks);
																	$Notification2->send_notification($fcmToken, $newTasks);
																	$Notification3->send_notification($fcmToken, $newTasks);
																	print_r("<-Выслано-> ".$login.$taskTypeId.$Imei." <-Выслано-> ".date("r")." <-Выслано->\n");
																//}
															} else {
																$newTasks = $i - $str_arr[4];
																$newTasks = $newTasks."-c-".$task['Data']['id'];
																if($login == "test"){
																	$Notification = new FirebaseNotificationClassUnity;
																	$Notification->send_notification($fcmToken, $newTasks);
																}
																print_r("<-Выслано-> ".$login.$taskTypeId."Dlya ne vzroslogo aga"." <-Выслано-> ".date("r")." <-Выслано->\n");
															}
														} else if ($i == $str_arr[4]) {
															print_r("{Отсутсвуют новые комменты} ".$login.$Imei." {Отсутсвуют новые комменты} ".date("r")." {Отсутсвуют новые комменты}\n"."----------------------\n");
														} 
														$check = true;
													}
													//print_r($str_arr);
												}
												fclose($fd);
												if(!$check){
													print_r("\n Вставленно новое значение комментариев в файл");
													file_put_contents("catalog/controller/pril/people.txt",$login."-".$Imei."-".$customerId."-".$task['Data']['id']."-".$i."\n",FILE_APPEND);
												}
											}
										}
									//}
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