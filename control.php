<?php
class ControllerPrilControl extends Controller {
	public function index() {

		require_once 'function.php';
		
		/*$login 	= 'admin_ru';
		$server = '100-100';
		$imei 	= 'a06cf9c0925a6f51';*/
		
		$login 	= '';
		$server = '';
		$imei 	= '';

		$Arrr = object_to_array(retrieveJsonPostData());
		if (is_array(@$Arrr) || is_object(@$Arrr)){
			foreach ($Arrr as $pril_key => $pril_value) {
				switch ($pril_key) {
					case 'login':
						$login = $pril_value;
						break;
					case 'server':
						$server = $pril_value;
						break;
					case 'imei':
						$imei = $pril_value;
						break;
				}
			}
		}

		if ($login == '' || $server == '' || $imei == '') {
			
			echo "Данные переданы не верно!";
			exit;
		}
		
		$this->load->model('pril/control');
		
		$server = $this->model_pril_control->getServer($server);
		
		//print_r($server);
		//print_r($server[0]['lastname']);
		
		if (!$server) {
			echo "Получите лицензию!";
		} else {
			// проверка на запись данного imei
			$getimei = $this->model_pril_control->getImei($imei);
			//print_r($getimei);
			
			if (!$getimei) {
				// проверка количества установок
				$getcountimei = $this->model_pril_control->getCountImei($server[0]['customer_id']);
				//print_r($getcountimei);
				
				// сверяемся с тарифом
				if ($server[0]['lastname'] == 1) {
					if (count($getcountimei) < 10) {
						// записываем imei
						$this->model_pril_control->insertImei($server[0]['customer_id'], $imei, $login);
						print_r ("Разрешено.");
					} else {
						print_r ("Достигнуто максимальное количество установок приложения");
					}
				}
				
				if ($server[0]['lastname'] == 2) {
					if (count($getcountimei) < 30) {
						// записываем imei
						$this->model_pril_control->insertImei($server[0]['customer_id'], $imei, $login);
						print_r ("Разрешено.");
					} else {
						print_r ("Достигнуто максимальное количество установок приложения");
					}
				}
				
				if ($server[0]['lastname'] == 3) {
					// записываем imei
					$this->model_pril_control->insertImei($server[0]['customer_id'], $imei, $login);
					print_r ("Разрешено.");
				}
				
			} else {
				print_r ("Разрешено.");
			}
		}
		
	}
}
