<?php
class ControllerPrilSaveIspolnitelToken extends Controller {
	public function index() {
		require_once 'function.php';
		$Login = 'test';
		$ID = '358583100137663';
		$Token = "TEST"; 
		foreach (object_to_array(retrieveJsonPostData()) as $pril_key => $pril_value) {
			switch ($pril_key) {
				case 'Login':
					$Login = $pril_value;
					//print_r("Login: ".$Login."//");
					break;
				case 'Token':
					$Token = $pril_value;
					//print_r("Token: ".$Token."//");
					break;
				case 'ID':
					$ID = $pril_value;
					//print_r("Imei: ".$ID."//");
					break;
			}
		}
		$this->load->model('pril/allow');
		if($Token != '' && $Login != '' && $ID != ''){
			$Yes = false;
			if($Login != ''){
				if(!$Yes){
					$server = $this->model_pril_allow->getCustomerId($Login, $ID);
					$customerId = $server[0]['customer_id'];
					$Yes = true;
				}
				if($Yes){
					$isDeviceIdChanged = false;
					$server = $this->model_pril_allow->checkId($Login, $customerId); 
					foreach($server as $element){ 
						foreach($element as $IdForZapros){ 
							$server = $this->model_pril_allow->checkImei($Login, $customerId, $IdForZapros);
							if($server['0']['imei'] == $ID){  
								$server = $this->model_pril_allow->updateBD("fcm_token", $Token, "imei", $ID);
								$isDeviceIdChanged = true; 
								break; 
							}else if($server['0']['imei'] == ''){
								$server = $this->model_pril_allow->updateBD("imei", $ID, "login_us", $Login."' AND id = '".$IdForZapros); // 
								$server = $this->model_pril_allow->updateBD("fcm_token", $Token, "imei", $ID);
								$isDeviceIdChanged = true;
								break;
							}
						}
						if($isDeviceIdChanged){
							break; // Выход из цикла
						}
					}
				}
			}
		}
		else{
			print_r("Данные переданы не верно!");
		}
	}
}
?>	