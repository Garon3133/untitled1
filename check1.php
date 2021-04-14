<?php
class ControllerPrilCheck1 extends Controller {
	public function index() {
		
		require_once 'function.php';
	
		$key_pril = '100-200';
		$imei = '7aa865db4d0397a7';
		$Arrr = object_to_array(retrieveJsonPostData());
		if (is_array(@$Arrr) || is_object(@$Arrr)){
			foreach ($Arrr as $pril_key => $pril_value) {
				switch ($pril_key) {
				case 'key_pril':
					$key_pril = $pril_value;
					break;
				case 'imei':
					$imei = $pril_value;
					break;
				}
			}
		}
		
		if ($key_pril == '') {
			echo "Данные переданы не верно!";
			exit;
		}
		
		$this->load->model('pril/android');
		$this->load->model('pril/allow');
		
		$customer = $this->model_pril_android->getCustomer($key_pril);
		$arr_customer = object_to_array(json_decode($customer[0]['custom_field']));
		$customer_role = $this->model_pril_allow->getConnectedDevices($imei);
        //print_r($arr_customer);
		if (!$customer) {
			print_r("Ошибка!");
			exit;
		}
		if (!$customer_role) {
			$dss = base64_encode($arr_customer[1])."res6yrdy54sdx";
			$dsa = base64_encode($dss);
			$arr_customer[1] = $dsa;
			
			$dss = base64_encode($arr_customer[2])."hsk432jgkit5";
			$dsa = base64_encode($dss);
			$arr_customer[2] = $dsa;
			$arr_customer += ['Utilities1SA' => 'res6yrdy54sdx'];
			$arr_customer += ['Utilities1SU' => 'hsk432jgkit5'];
			$arr_customer += ['Role' => 0];
			print_r(json_encode($arr_customer,JSON_UNESCAPED_UNICODE));
		} else {
			$dss = base64_encode($arr_customer[1])."res6yrdy54sdx";
			$dsa = base64_encode($dss);
			$arr_customer[1] = $dsa;
			
			$dss = base64_encode($arr_customer[2])."hsk432jgkit5";
			$dsa = base64_encode($dss);
			$arr_customer[2] = $dsa;
			$arr_customer += ['Utilities1SA' => 'res6yrdy54sdx'];
			$arr_customer += ['Utilities1SU' => 'hsk432jgkit5'];
			$arr_customer += ['Role' => $customer_role[0]['role']];
			print_r(json_encode($arr_customer,JSON_UNESCAPED_UNICODE));
		}
	}
}
?>