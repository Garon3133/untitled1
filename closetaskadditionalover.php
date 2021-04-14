<?php
class ControllerPrilClosetaskadditionalover extends Controller {
	public function index() {
		
		require_once 'function.php';
		
		//$arrAddField = '[["32","38357","number","1111111111"],["43","38357","list","Да, был выезд к клиенту"],["35","38357","list_custom","sssssssssssssssssssssssssss"],["33","38357","flag","1"],["51","38357","string","2222222222222222"],["53","38357","date","30.8.2019"],["54","38357","text","ddddddd ddddddd d fffffff"],["55","38357","list_custom_multiply","1\n3"]]';
		//$arrAddField = '[[&#34;32&#34;,&#34;38358&#34;,&#34;number&#34;,&#34;11111&#34;],[&#34;43&#34;,&#34;38358&#34;,&#34;list&#34;,&#34;Да, был выезд к клиенту&#34;],[&#34;35&#34;,&#34;38358&#34;,&#34;list_custom&#34;,&#34;Да&#34;],[&#34;33&#34;,&#34;38358&#34;,&#34;flag&#34;,null],[&#34;51&#34;,&#34;38358&#34;,&#34;string&#34;,null],[&#34;53&#34;,&#34;38358&#34;,&#34;date&#34;,&#34;13.9.2019&#34;],[&#34;54&#34;,&#34;38358&#34;,&#34;text&#34;,null],[&#34;55&#34;,&#34;38358&#34;,&#34;list_custom_multiply&#34;,null]]';
		$arrAddField = '';
		$key_pril = '';
			
		foreach (object_to_array(retrieveJsonPostData()) as $pril_key => $pril_value) {
			switch ($pril_key) {
				case 'arr_add_field':
					$arrAddField = $pril_value;
					break;
				case 'key_pril':
					$key_pril = $pril_value;
					break;
			}
		}
		
		//print_r($arrAddField."\n");
		//print_r($key_pril."\n");
			
		if ($arrAddField == '' || $key_pril == '') {
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
		
		$bodytag = str_replace("&#34;", "\"", $arrAddField);
		//print_r($bodytag);
		$results_json = json_decode(str_replace("&#34;", "\"", $arrAddField));
		//print_r($results_json);
		
		foreach ($results_json as $jsonkey) {
			if ($jsonkey[2] == "number" && $jsonkey[3] != "") {
				// id доп.поля
				//print_r($jsonkey[0]."\n");
				// id задания
				//print_r($jsonkey[1]."\n");
				// тип доп.поля
				//print_r($jsonkey[2]."\n");
				// значение
				//print_r($jsonkey[3]."\n\n");
				
				// http://user.intronex.ru/api.php?key=1597535&cat=additional_data&action=change_value&field_id=32&object_id=38357&value=8888888888888888888888
				$arrAddField = array(
					'key' 		=> APIKEY_USERSIDE,
					'cat' 		=> 'additional_data',
					'action' 	=> 'change_value',
					'field_id' 	=> $jsonkey[0],
					'object_id' => $jsonkey[1],
					'value' 	=> $jsonkey[3],
				);
				
				$us_add_field = object_to_array(json_decode(create_ticket($arrAddField, USERSIDE)));
				print_r($us_add_field);
			}
			
			if ($jsonkey[2] == "list" && $jsonkey[3] != "") {
				
				$arrAddField = array(
					'key' 		=> APIKEY_USERSIDE,
					'cat' 		=> 'additional_data',
					'action' 	=> 'change_value',
					'field_id' 	=> $jsonkey[0],
					'object_id' => $jsonkey[1],
					'value' 	=> $jsonkey[3],
				);
				
				$us_add_field = object_to_array(json_decode(create_ticket($arrAddField, USERSIDE)));
				print_r($us_add_field);
			}
			
			if ($jsonkey[2] == "list_custom" && $jsonkey[3] != "") {
				
				$arrAddField = array(
					'key' 		=> APIKEY_USERSIDE,
					'cat' 		=> 'additional_data',
					'action' 	=> 'change_value',
					'field_id' 	=> $jsonkey[0],
					'object_id' => $jsonkey[1],
					'value' 	=> $jsonkey[3],
				);
				
				$us_add_field = object_to_array(json_decode(create_ticket($arrAddField, USERSIDE)));
				print_r($us_add_field);
			}
			
			if ($jsonkey[2] == "flag" && $jsonkey[3] != "") {
				
				$arrAddField = array(
					'key' 		=> APIKEY_USERSIDE,
					'cat' 		=> 'additional_data',
					'action' 	=> 'change_value',
					'field_id' 	=> $jsonkey[0],
					'object_id' => $jsonkey[1],
					'value' 	=> $jsonkey[3],
				);
				
				$us_add_field = object_to_array(json_decode(create_ticket($arrAddField, USERSIDE)));
				print_r($us_add_field);
			}
			
			if ($jsonkey[2] == "string" && $jsonkey[3] != "") {
				
				$arrAddField = array(
					'key' 		=> APIKEY_USERSIDE,
					'cat' 		=> 'additional_data',
					'action' 	=> 'change_value',
					'field_id' 	=> $jsonkey[0],
					'object_id' => $jsonkey[1],
					'value' 	=> $jsonkey[3],
				);
				
				$us_add_field = object_to_array(json_decode(create_ticket($arrAddField, USERSIDE)));
				print_r($us_add_field);
			}
			
			if ($jsonkey[2] == "date" && $jsonkey[3] != "") {
				
				$arrAddField = array(
					'key' 		=> APIKEY_USERSIDE,
					'cat' 		=> 'additional_data',
					'action' 	=> 'change_value',
					'field_id' 	=> $jsonkey[0],
					'object_id' => $jsonkey[1],
					'value' 	=> $jsonkey[3],
				);
				
				$us_add_field = object_to_array(json_decode(create_ticket($arrAddField, USERSIDE)));
				print_r($us_add_field);
			}
			
			if ($jsonkey[2] == "text" && $jsonkey[3] != "") {
				
				$arrAddField = array(
					'key' 		=> APIKEY_USERSIDE,
					'cat' 		=> 'additional_data',
					'action' 	=> 'change_value',
					'field_id' 	=> $jsonkey[0],
					'object_id' => $jsonkey[1],
					'value' 	=> $jsonkey[3],
				);
				
				$us_add_field = object_to_array(json_decode(create_ticket($arrAddField, USERSIDE)));
				print_r($us_add_field);
			}
			
			if ($jsonkey[2] == "list_custom_multiply" && $jsonkey[3] != "") {
				
				$jsonValue = json_encode(explode("***", $jsonkey[3]));
				
				$arrAddField = array(
					'key' 		=> APIKEY_USERSIDE,
					'cat' 		=> 'additional_data',
					'action' 	=> 'change_value',
					'field_id' 	=> $jsonkey[0],
					'object_id' => $jsonkey[1],
					'value' 	=> $jsonValue,
				);
				
				$us_add_field = object_to_array(json_decode(create_ticket($arrAddField, USERSIDE)));
				print_r($us_add_field);
			}
		}

	}
}
