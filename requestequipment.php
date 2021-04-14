<?php
class ControllerPrilRequestequipment extends Controller {
	public function index() {

		require_once 'function.php';
		
		$equipment_id = '1434';
		$log = 'test';
		$pas = '111';
		$key_pril = '100-200';
		
		foreach (object_to_array(retrieveJsonPostData()) as $pril_key => $pril_value) {
			switch ($pril_key) {
				case 'equipment_id':
					$equipment_id = $pril_value;
					break;
				case 'log':
					$log = $pril_value;
					break;
				case 'pas':
					$pas = $pril_value;
					break;
				case 'key_pril':
					$key_pril = $pril_value;
					break;
			}
		}
		
		if ($equipment_id == '' || $log == '' || $pas == '' || $key_pril == '') {
			echo "Данные переданы не верно!";
			exit;
		}
		
		$this->load->model('pril/android');
		
		$customer = $this->model_pril_android->getCustomer($key_pril);
		
		if (!$customer) {
			print_r ("Авторизоваться не удалось!");
			exit;
		}

		$arr_customer = object_to_array(json_decode($customer[0]['custom_field']));
		
		define('APIKEY_USERSIDE', $arr_customer[1]);
		define('USERSIDE', $arr_customer[2]);
	
		if (isset($_SERVER['HTTPS'])) {
			$scheme = $_SERVER['HTTPS'];
		} else {
			$scheme = '';
		}
		if (($scheme) || ($scheme != 'off')) { 
			$hhtp = 'https://';
		} else { 
			$hhtp = 'http://';
		}
		//print_r(PHANTOMJS." ".$_SERVER['DOCUMENT_ROOT'].substr($_SERVER['SCRIPT_NAME'], 0, strripos($_SERVER['SCRIPT_NAME'], '/'))."/catalog/controller/pril/phantomjs.js ".$equipment_id." ".USERSIDE." ".$log." ".$pas);
		/*passthru(PHANTOMJS." ".$_SERVER['DOCUMENT_ROOT'].substr($_SERVER['SCRIPT_NAME'], 0, strripos($_SERVER['SCRIPT_NAME'], '/'))."/catalog/controller/pril/phantomjs.js ".$equipment_id." ".USERSIDE." ".$log." ".$pas);
		print_r ($hhtp.$_SERVER['HTTP_HOST'].substr($_SERVER['SCRIPT_NAME'], 0, strripos($_SERVER['SCRIPT_NAME'], '/')).'/image/catalog/img-comm/'.$equipment_id.'.png');*/
		
		$house_list = array(
			'key' => APIKEY_USERSIDE,
			'cat' => 'node',
			'action' => 'get_scheme',
			'id' => $equipment_id,
		);
		$house_ok = create_ticket($house_list, USERSIDE);
		
		file_put_contents('image/catalog/img-comm/' . $equipment_id . '.svg', $house_ok);
		
		$image = new Imagick('image/catalog/img-comm/' . $equipment_id . '.svg');
		$image->setImageFormat("jpeg");
		$image->writeImage('image/catalog/img-comm/' . $equipment_id . '.jpg');
		
		unlink('image/catalog/img-comm/' . $equipment_id . '.svg');

		print_r ($hhtp.$_SERVER['HTTP_HOST'].substr($_SERVER['SCRIPT_NAME'], 0, strripos($_SERVER['SCRIPT_NAME'], '/')).'/image/catalog/img-comm/' . $equipment_id . '.jpg');
	
		
	
	}
}
