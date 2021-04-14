<?php
require_once 'function.php';
require_once 'firebaseNotification.php';
	function Zapros($zapros){
		$link = mysqli_connect("localhost", "intronex", "5Q3d0E1f", "android_pril_server1");
		if ($link == false){
				print_r("Не удалось подключиться к БД." . mysqli_connect_error());
		}else{
			$result = mysqli_query($link, $zapros) or die("Ошибка запроса." . mysqli_error($link));
			$row = $result->fetch_all();
			if($result){
				return $row;
			}
		}
		mysqli_close($link);
	}
	
	function NotificationSend($data) {
		$customerId = $data['customerId'];
		$apiKey = $data['key'];
		$userSide = $data['userside'];
		$zapros = "SELECT fcm_token FROM oc_customer_connecteddevices WHERE customer_id = '".(string)$customerId."'";
		$result = Zapros($zapros);
		foreach($result as $element){
			$fcmToken = $element['0'];
			
			if($fcmToken != ''){
				$zapros = "SELECT imei FROM oc_customer_connecteddevices WHERE fcm_token = '".(string)$fcmToken."'";
				$result = Zapros($zapros);
				$Imei = $result['0']['0'];
				if($Imei != ''){
					$zapros = "SELECT login_us FROM oc_customer_connecteddevices WHERE imei = '".(string)$Imei."' AND fcm_token = '".(string)$fcmToken."'";
					$result = Zapros($zapros);
					$login = $result['0']['0'];
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
					$TaskCount = $userside_StaffTasks['count'];// Кол-во тасков, записанных в юзерсайде
					//if($login == 'test'|| $login == 'andrey')$TaskCount = $TaskCount + 1;
					/*print_r("Логин: ".$login.", Айди: ".$userside_StaffId['id'].".   \nТаски:\n");
					print_r($userside_StaffTasks);*/
					$zapros = "SELECT count_task FROM oc_customer_connecteddevices WHERE imei = '".(string)$Imei."' AND fcm_token = '".(string)$fcmToken."'";
					$result = Zapros($zapros);
					$countTask = $result['0']['0'];// Кол-во тасков, записанных в БД
					//print_r($countTask."/".$TaskCount."(".$login.")");
					if($result != null){
						if($countTask < $TaskCount){
							//print_r($countTask."/".$TaskCount."(".$login.")");
							$newTasks = $TaskCount - $countTask;		
							//$Notification = new FirebaseNotificationClass; //отправка комментов
							//$Notification->send_notification($fcmToken, $newTasks);
						}
					}
				}	
			}	
		}
	}
		
		

		$apiKey = '1597535';
		$userSide = "https://user.intronex.ru";
		$companyId = '3';
		
		$companyData = array(
			'key'         => $apiKey,
			'userside'    => $userSide,
			'customerId'  => $companyId,
		);

		$NotifResult = NotificationSend($companyData);
?>	