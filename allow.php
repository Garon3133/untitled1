<?php
class ControllerPrilAllow extends Controller {
	public function index() {

		require_once 'function.php';
		
		$imei = 'a06cf9c0925a6f51';
		$tel = 'admin_ru';
		$lic = '100-100';
		
		foreach (object_to_array(retrieveJsonPostData()) as $pril_key => $pril_value) {
			switch ($pril_key) {
				case 'imei':
					$imei = $pril_value;
					break;
				case 'tel':
					$tel = $pril_value;
					break;
				case 'lic':
					$lic = $pril_value;
					break;
			}
		}
		
		if ($imei == '' || $tel == '' || $lic == '') {
			echo "Данные переданы не верно!";
			exit;
		}
		
		$this->load->model('pril/allow');
		
		$server = $this->model_pril_allow->getServer($lic);
		//print_r($server);
		if ($server) {
			$user = $this->model_pril_allow->getConnectedDevices($imei);
			//print_r($user);
			//print($server[0]['customer_id']);
			if ($user) {
				if ($user[0]['active'] == 1) {
					$this->model_pril_allow->updateConnectedDevices($imei, $tel, $server[0]['customer_id']);
				}

				print_r($user[0]['active']);
			} else {
				print_r("0");
			}
		} else {
			print_r("0");
		}
	
	}
}