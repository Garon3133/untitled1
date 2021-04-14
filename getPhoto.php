<?php
class ControllerPrilGetphoto extends Controller {
	public function index() {
		
		require_once 'function.php';
		
		$photoId = '110569';
		$key_pril = '100-200';
		$Arrr = object_to_array(retrieveJsonPostData());
		if (is_array(@$Arrr) || is_object(@$Arrr)){
			foreach ($Arrr as $pril_key => $pril_value) {
				switch ($pril_key) {
					case 'photoId':
						$photoId = $pril_value;
						break;
					case 'key_pril':
						$key_pril = $pril_value;
						break;
				}
			}
		}
		
		$this->load->model('pril/android');
			
		$customer = $this->model_pril_android->getCustomer($key_pril);
		
		if (!$customer) {
			print_r("Ошибка!");
			exit;
		}
	
		$arr_customer = object_to_array(json_decode($customer[0]['custom_field']));
	
		define('APIKEY_USERSIDE', $arr_customer[1]);
		define('USERSIDE', $arr_customer[2]);
	    $photosArr = "";
		$photoIds_arr = explode(" ", $photoId);
		//$i = 0;
		foreach($photoIds_arr as $photoID){
			
			$photo_data = array(
				'key' 	 => APIKEY_USERSIDE,
				'cat' 	 => 'attach',
				'action' => 'get_file',
				'id' 	 => $photoID,
			);
			
			$photo_arr = create_ticket($photo_data, USERSIDE);
			//print_r($photo_arr);
			$photo = base64_encode($photo_arr);
			//print_r($photo);
			//if(empty($photo)){
				//$i++;
				
			//}
			$photosArr .= "=*=".$photo;
		}
		//print_r($i);
		print_r($photosArr);
	}
}
