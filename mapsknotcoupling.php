<?php
class ControllerPrilMapsknotcoupling extends Controller {
	public function index() {

		require_once 'function.php';
		
		/*$lat = '56.29571';
		$lng = '39.372967';
		$range = '100';
		$key_pril = '100-200-148';*/
		$lat = '55.595649';
		$lng = '38.472445';
		$range = '500';
		$key_pril = '100-200';
		
		foreach (object_to_array(retrieveJsonPostData()) as $pril_key => $pril_value) {
			switch ($pril_key) {
				case 'lat':
					$lat = $pril_value;
					break;
				case 'lng':
					$lng = $pril_value;
					break;
				case 'range':
					$range = $pril_value;
					break;
				case 'key_pril':
					$key_pril = $pril_value;
					break;
			}
		}
		
		if ($lat == '' || $lng == '' || $range == '' || $key_pril == '') {
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
	
		$userKnotCoupling = array(
			'key'			=> APIKEY_USERSIDE,
			'cat'			=> 'node',
			'action'		=> 'get_id_by_coord',
			'lat'			=> $lat,
			'lon'			=> $lng,
			'range'			=> $range,
			'type'			=> '0,1',
		);
		//print_r($userKnotCoupling);
		$userside_KnotCoupling = object_to_array(json_decode(create_ticket($userKnotCoupling, USERSIDE)));

		if ($userside_KnotCoupling['Result'] == 'OK') {
			//print_r ($userside_KnotCoupling);
			
			$node_arr = array();
			
			foreach ($userside_KnotCoupling['data'] as $nodeKey => $nodeVal) {
					
				$node_id = $nodeVal['id'];
				$node_lat = $nodeVal['lat'];
				$node_lon = $nodeVal['lon'];
				$node_distance = $nodeVal['distance'];
					
				$userNode = array(
					'key' => APIKEY_USERSIDE,
					'cat' => 'node',
					'action' => 'get',
					'id' => $node_id,
				);

				$userside_node = object_to_array(json_decode(create_ticket($userNode, USERSIDE)));
		
				if ($userside_node['Result'] == 'OK') {
					//print_r ($userside_node);

					$node_arr[] = array(
						"id"		=> $node_id,
						"lat"		=> $node_lat,
						"lon"		=> $node_lon,
						"distance"	=> $node_distance,
						"type"		=> $userside_node['data'][$node_id]['type'],
						"location"	=> $userside_node['data'][$node_id]['location'],
						"name"		=> $userside_node['data'][$node_id]['name'],
						"number"	=> $userside_node['data'][$node_id]['number'],
					);
							
				} else {
					print_r ($userside_node['ErrorText']);
					exit;
				}
				
			}			
			//print_r($node_arr);
			echo json_encode($node_arr);
							
		} else {
			print_r ($userside_KnotCoupling['ErrorText']);
			exit;
		}
	
	}
}