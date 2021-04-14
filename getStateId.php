<?php
class ControllerPrilGetStateId extends Controller {
	public function index() {
		require_once 'function.php';
		$key_pril = "100-200-119";
		$typeId	= "7";	
		foreach (object_to_array(retrieveJsonPostData()) as $pril_key => $pril_value) {
			switch ($pril_key) {
				case 'typeId':
					$typeId = $pril_value;
					//print_r("Token: ".$Token."//");
					break;
				case 'key-prill':
					$key_pril = $pril_value;
					//print_r("Imei: ".$ID."//");
					break;
			}
		}
		if($typeId != '' && $key_pril != ''){
			$this->load->model('pril/android');
			
			$customer = $this->model_pril_android->getCustomer($key_pril);
			
			if (!$customer) {
				print_r("Ошибка!");
				exit;
			}
	
			$arr_customer = object_to_array(json_decode($customer[0]['custom_field']));
	
			define('APIKEY_USERSIDE', $arr_customer[1]);
			define('USERSIDE', $arr_customer[2]);
			$customFieldId = implode(',', $arr_customer[16]);
			$customerRole = $this->model_pril_android->getCustomField($customFieldId);
			print_r($customerRole);
			$sysrole = 0;
			$AllSysRole = "";
			foreach($customerRole as $stateIdInfos){
				if($stateIdInfos['name'] == "Новое задание"){
					$sysrole = 1;
				}else if($stateIdInfos['name'] == "Отложено"){
					$sysrole = 2;
				}else if($stateIdInfos['name'] == "Не выполнено"){
					$sysrole = 4;
				}else if($stateIdInfos['name'] == "Выполняется"){
					$sysrole = 5;
				}else if($stateIdInfos['name'] == "Выполнено удачно"){
					$sysrole = 6;
				}else if($stateIdInfos['name'] == "Последующий контроль"){
					$sysrole = 7;
				}else if($stateIdInfos['name'] == "Выполнено неудачно"){
					$sysrole = 8;
				}else if($stateIdInfos['name'] == "Отменено")	{
					$sysrole = 9;
				}
				$AllSysRole .= $sysrole.",";
				
			}
			$state_data = array(
					'key' => APIKEY_USERSIDE,
					'cat' => 'task',
					'subcat' => 'get_catalog_state',
					'state_id' => mb_substr($AllSysRole, 0, -1),
			);
			print_r($state_data);
			$state_arr = object_to_array(json_decode(create_ticket($state_data, USERSIDE)));
			print_r($state_arr);
			$states_data = array(
					'key' => APIKEY_USERSIDE,
					'cat' => 'task',
					'subcat' => 'get_catalog_type',
					'id' => $typeId,
			);
			print_r($states_data);
			$states_arr = object_to_array(json_decode(create_ticket($states_data, USERSIDE)));	
			print_r($states_arr);
			$stateIds = "";
			$stateIdsInfo= "";
			foreach($states_arr['Data'][$typeId]['allow_state'] as $stateId){
				foreach($state_arr['Data'] as $statesId){
					if($statesId['id'] == $stateId)
					{
						$stateIdsInfo .= "=*=".$statesId['id']."=/=".$statesId['name'];
					}
				}
			} 
			print_r($stateIdsInfo);
		}
	}
}
?>