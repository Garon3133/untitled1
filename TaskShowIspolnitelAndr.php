<?php
class ControllerPrilTaskShowIspolnitelAndr extends Controller {
	public function index() { 
		require_once 'function.php';
		
		$ID_Type_Task = '1';
		$Login 		  = "test";
		$fcmToken 		  = "c6prf4YM4eM:APA91bHSudr7bwdLrFnSjEBjot1mSDF7e3RpxrVVPLoVBA4WUapPkxhMf5wdV56MUfmo0EeQg8rF-lIYSlofbFY28luzCM3jgEBitdfqEwCzkcXZsXMFafMz4gBmRkOrXjqE5-te_Mvn";
		$ID 		  = "a06cf9c0925a6f50";
		$TaskCount	  = 0;
		
		$this->load->model('pril/allow');
		
		foreach (object_to_array(retrieveJsonPostData()) as $pril_key => $pril_value) {
			switch ($pril_key) {
				case 'ID_Type_Task':
					$ID_Type_Task = $pril_value;
					break;
				case 'Login':
					$Login = $pril_value;
					break;
				case 'ID':
					$ID = $pril_value;
					break;
				case 'Token':
					$fcmToken = $pril_value;
					break;
			}
		}
		$Yes = false;
		if(!$Yes){
			$server = $this->model_pril_allow->getCustomerId($Login, $ID);
			$customerId = $server[0]['customer_id'];
			$server = $this->model_pril_allow->getCompanyUser($customerId);
			$customerInfo = $server[0]['custom_field'];
			$Data = json_decode($customerInfo, true);
			define('APIKEY_USERSIDE', $Data[1]);
			define('USERSIDE', $Data[2]);
			define('ID_TYPE', $Data[3]);
			$Yes = true;
		}
		if($Yes){
			if($ID_Type_Task != ''){
				$arr_id = explode(",", ID_TYPE);
			
				switch($ID_Type_Task){
					case '1':
						$work_typer = $arr_id[0];
						break;
					case '2':
						$work_typer = $arr_id[1];
						break;
				}
			
				switch($work_typer){//свич-выбор мастера работ, в следствии с этим, показываются задания для него.
					case '291':
						$ElectrTasks = array(
							'key'      	  => APIKEY_USERSIDE,
							'cat'      	  => 'task',
							'subcat'   	  => 'get_list',
							//'date_do_from'=> date("Y-m-d"),
							//'date_do_to'  => date("Y-m-d"),
							'state_id' 	  => 6,					// вывод только заданий, которые еще не приняты и не отменены
							//'limit'     => 1,					//Лимит одновременного показа заказов
							'type_id'  	  => $work_typer,
						);
						$userside_AllTasks = object_to_array(json_decode(create_ticket($ElectrTasks, USERSIDE)));
						break;
					case '2':
						break;
				}
			}
			if($userside_AllTasks['Result'] == 'OK' && $userside_AllTasks['count'] != 0){
				
				$arr_TaskIds= explode(",", $userside_AllTasks['list']);
				$TaskCount = count($arr_TaskIds);
				foreach( $arr_TaskIds as $TaskId){
					
					$TaskShow = array(
						'key'    => APIKEY_USERSIDE,
						'cat'    => 'task',
						'subcat' => 'show',
						'id'     => $TaskId,
					);
					
					$userside_TaskShow = object_to_array(json_decode(create_ticket($TaskShow, USERSIDE)));
					
					$TaskCategory = $userside_TaskShow['Data']['type']['name'];		//Название задания
					$TaskDate = $userside_TaskShow['Data']['date']['create'];		//Дата создания
					$TaskInfo = $userside_TaskShow['Data']['description'];			//описание(внутренностей заказа)	
					
					//print_r($TaskId);												//id задания
					$TaskFioAdressPhoneOpis = explode("::", $TaskInfo);
					$FIOAbon = $TaskFioAdressPhoneOpis[1];
					$AdressAbon = $TaskFioAdressPhoneOpis[2];
					$PhoneAbon = $TaskFioAdressPhoneOpis[3];
					$OpisAbon = $TaskFioAdressPhoneOpis[4];
					$CoordsAbon = $TaskFioAdressPhoneOpis[5];
					$TaskState = $userside_TaskShow['Data']['state']['id'];
					$ResultString ="::".$TaskCategory."::".$TaskDate."::".$FIOAbon."::".$AdressAbon."::".$PhoneAbon."::".$OpisAbon."::".$CoordsAbon."::Комментарий задания:"."\n";
					$i = '0';
					$Propusk = '0';
					$PhotoSearch = '0';
					$PhotoFolderURL = 'user.intronex.ru/main/attach/';
					foreach($userside_TaskShow['Data']['comments'] as $arr){
						$ResultString .="===".$arr['operatorId']."\n";
						$ResultString .=$arr['dateAdd']."\n";
						$ResultString .=$arr['comment']."===";
						foreach($arr['attach'] as $photo){
							$ResultString .=$PhotoFolderURL.$photo['fileSystemPath']."//=//";
						}
						//print_r("////////////////");
					}
					$arr_PhotoPath = array(); //массив путей фото прикреплённых к заданию
					$i = '0';
					$ResultString .= "::Пути к фото: ";
					foreach($userside_TaskShow['Data']['attach'] as $arr){
						$arr_PhotoPath[$i] = $PhotoFolderURL.$arr['fileSystemPath'];
						$ResultString .=$arr_PhotoPath[$i].";;";
						//print_r($arr_PhotoPath[$i]);								//вывод пути к фото(для проверки)
						$i++;
						//print_r(" ");
					}
					if ($TaskState == ''){
						$TaskState = 6;
					}
					$ResultString .="::".$TaskId."::".$TaskState;						//id задания
					print_r($ResultString);
					
				}
			}
			if($Login != '' && $ID != ''){
				print_r($Login."  ".$ID."  ".$fcmToken);
				//if($Login != "test"){
					$server = $this->model_pril_allow->getTypeId($ID, $Login, $fcmToken);
					$taskTypeId = $server[0]['group_id'];
					print_r($server);
					$server = $this->model_pril_allow->updateBD("count_task", $TaskCount, "Login_us", $Login."' AND customer_id = '".$customerId."' AND group_id = '".$taskTypeId);
					$server = $this->model_pril_allow->updateBD("fcm_token", $fcmToken, "Login_us", $Login."' AND customer_id = '".$customerId."' AND group_id = '".$taskTypeId);
					print_r("Кол-во тасков изменено их = ".$TaskCount." Логин = ".$Login);
				//}
			}else{
				//print_r("Не отправлен логин");
			}		
		}
	}
}

?>	