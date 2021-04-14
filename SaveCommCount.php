<?php
class ControllerPrilSaveCommCount extends Controller {
	public function index() {
		require_once 'function.php';
		
		$login = "test";
		$Imei = "a142f8d85c8a6d7f";
		$taskId = "78746";
		$countComm = 23;
		foreach (object_to_array(retrieveJsonPostData()) as $pril_key => $pril_value) {
			switch ($pril_key) {
				case 'login':
					$login = $pril_value;
					break;
				case 'Imei':
					$Imei = $pril_value;
					break;
				case 'taskId':
					$taskId = $pril_value;
					break;
				case 'countComm':
					$countComm = $pril_value;
					break;
			}
		}
		
		if ($login == '' || $Imei == '' || $taskId == '' || $countComm == '') {
			echo "Данные переданы не верно!";
			exit;
		}
		$this->load->model('pril/allow');
		
		$server = $this->model_pril_allow->getCustomerId($login, $Imei);
		
		$customerId = $server[0]['customer_id'];
		//if($login != "test"){
			$fd = file("catalog/controller/pril/people.txt");
			for($i = 0; $i < count($fd); $i++){
				$str_arr = explode("-",$fd[$i]);
				if($str_arr[0] == $login && $str_arr[1] == $Imei && $str_arr[2] == $customerId && $str_arr[3] == $taskId){
					print_r("Зашло");
					$fd[$i] = $login."-".$Imei."-".$customerId."-".$taskId."-".$countComm."\n";
					file_put_contents("catalog/controller/pril/people.txt",$fd);
					print_r("Заменено");
				}
			}
		//}
	}
}
?>
