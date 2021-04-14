<?php
class ControllerPrilNode extends Controller {
	public function index() {
		
		require_once 'function.php';
		
		$node = '';
		$key_pril = '100-200';
		
		foreach (object_to_array(retrieveJsonPostData()) as $pril_key => $pril_value) {
			switch ($pril_key) {
				case 'node':
					$node = $pril_value;
					break;
				case 'key_pril':
					$key_pril = $pril_value;
					break;
			}
		}
			
		if ($node == '' || $key_pril == '') {
			echo "Данные переданы не верно!";
			exit;
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

		$node_data = array(
			'key' => APIKEY_USERSIDE,
			'cat' => 'node',
			'action' => 'get',
			'id' => $node,
		);

		$node_arr = object_to_array(json_decode(create_ticket($node_data, USERSIDE)));

		$lat = "";
		$lon = "";
		$typeNode = "";
		$numberNode = "";
		if ($node_arr['Result'] == 'OK') {
			foreach ($node_arr['data'] as $node_list) {

				foreach ($node_list['coordinates'] as $coord_key => $coord_value) {
					switch ($coord_key) {
						case "lat":
							$lat = $coord_value;
							break;
						case "lon":
							$lon = $coord_value;
							break;
					}
				}
				switch ($node_list['type']) {
					case '0':
					$typeNode = "узел связи";
						break;
					case '1':
						$typeNode = "муфта";
						break;
					case '2':
						$typeNode = "опора";
						break;
					case '3':
						$typeNode = "колодец";
						break;
				}
				$numberNode = $node_list['number'];
			}
			print_r ('{"0":'.$lat.',"1":'.$lon.',"2":"'.$typeNode.' '.$numberNode.'"}');
		} else {
			print_r ("");
		}
		
	}
}
