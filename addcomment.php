<?php
class ControllerPrilAddcomment extends Controller {
	public function index() {
		
		require_once 'function.php';
		
		/*$comment = 'Scan SN:00266214160 MAC:98:45:62:80:dc:7e';
		$task_id = '38358';
		$name_comm = 'test test test';
		$user = 'test';
		$key_pril = '100-200';*/
		$comment = " ";
		$task_id = " ";
		$name_comm = " ";
		$user = " ";
		$key_pril = " ";
		$Arrr = object_to_array(retrieveJsonPostData());
		//print_r($Arrr);
		if (is_array(@$Arrr) || is_object(@$Arrr)){
			foreach ($Arrr as $pril_key => $pril_value) {
				switch ($pril_key) {
					case 'comment':
						$comment = $pril_value;
						break;
					case 'task_id':
						$task_id = $pril_value;
						break;
					case 'name_comm':
						$name_comm = $pril_value;
						break;
					case 'user':
						$user = $pril_value;
						break;
					case 'key_pril':
						$key_pril = $pril_value;
						break;
				}
			}
		}
			
		if ($comment == " " || $task_id == " " || $key_pril == " " || $user == "") {
			echo "Данные переданы не верно!";
			//echo " Comment: ".$comment." Task_id: ".$task_id." Key pril: ".$key_pril." User: ".$user;
			exit;
		} 
		else 
		{
		
			$this->load->model('pril/android');
			
			$customer = $this->model_pril_android->getCustomer($key_pril);
			
			if (!$customer) {
				print_r("Ошибка!");
				exit;
			}
	
			$arr_customer = object_to_array(json_decode($customer[0]['custom_field']));
	
			define('APIKEY_USERSIDE', $arr_customer[1]);
			define('USERSIDE', $arr_customer[2]);
	
			if ($comment == "555*555") {
				$comment = $name_comm." отправил(а) фото:";
			} else {
				$comment = $name_comm.":<br />".$comment;
			}
	
			$textAttach = array(
				'key' 		=> APIKEY_USERSIDE,
				'cat' 		=> 'task',
				'subcat' 	=> 'comment_add',
				'id' 		=> $task_id,
				'comment' 	=> $comment,
			);
	
			$userside_add_comment = object_to_array(json_decode(create_ticket($textAttach, USERSIDE)));
	
			if ($userside_add_comment['Result'] == 'OK') {
				// проверяем папку с изображениями для загрузки в юзерсайд
				$entries = scandir("image/catalog/img-pril");
				$files = array();
				$filesOther = array();
				foreach($entries as $entry) {
					if (strpos($entry, $user) === 0) {
						if (stripos($entry, $task_id) !== false ) {
							$files[] = $entry;
						} else {
							$filesOther[] = $entry;
						}
					}
				}
				//print_r($files);
				if ($files) {
					foreach ($files as $value) {
						
						if (isset($_SERVER['HTTPS'])) {
							$scheme = $_SERVER['HTTPS'];
						} else {
							$scheme = '';
						}
	
						if (($scheme) || ($scheme != 'off')) { 
							$http = 'https://';
						} else { 
							$http = 'http://';
						}
	
						$urlImg = $http.$_SERVER['HTTP_HOST'].dirname($_SERVER["SCRIPT_NAME"])."/image/catalog/img-pril/";
						//print_r($userside_add_comment['Id']);
						$addAttach = array(
							'key' => APIKEY_USERSIDE,
							'cat' => 'attach',
							'action' => 'add',
							'object_type' => 'task_comment',
							'object_id' => $task_id,
							'comment_id' => $userside_add_comment['Id'],
							'src' => $urlImg.$value,
						);
	
						$userside_attach = object_to_array(json_decode(create_ticket($addAttach, USERSIDE)));
	
						if ($userside_attach['Result'] == 'ERROR') {
							print_r ($userside_attach['ErrorText']);
							exit;
						}
						// удаляем изображение
						if ( !(@unlink("image/catalog/img-pril/".$value)) ) die('Ошибка удаления изображения');
					}
				}
				// доотправка не отправленных фото
				if ($filesOther) {
					foreach ($filesOther as $value) {
						$imgTaskId = explode("-", $value);
						$textAttach = array(
							'key' 		=> APIKEY_USERSIDE,
							'cat' 		=> 'task',
							'subcat' 	=> 'comment_add',
							'id' 		=> $imgTaskId[1],
							'comment' 	=> $name_comm." (Доотправка не отправленных фото)",
						);
						$userside_add_comment_other = object_to_array(json_decode(create_ticket($textAttach, USERSIDE)));
	
						if ($userside_add_comment_other['Result'] == 'OK') {
							
							if (isset($_SERVER['HTTPS'])) {
								$scheme = $_SERVER['HTTPS'];
							} else {
								$scheme = '';
							}
							if (($scheme) || ($scheme != 'off')) { 
								$http = 'https://';
							} else { 
								$http = 'http://';
							}
							
							$urlImg = $http.$_SERVER['HTTP_HOST'].dirname($_SERVER["SCRIPT_NAME"])."/image/catalog/img-pril/";
							$addAttach = array(
								'key' => APIKEY_USERSIDE,
								'cat' => 'attach',
								'action' => 'add',
								'object_type' => 'task_comment',
								'object_id' => $imgTaskId[1],
								'comment_id' => $userside_add_comment_other['Id'],
								'src' => $urlImg.$value,
							);
	
							$userside_attach = object_to_array(json_decode(create_ticket($addAttach, USERSIDE)));
							
							if ($userside_attach['Result'] == 'ERROR') {
								print_r ($userside_attach['ErrorText']);
								exit;
							}
							// удаляем изображение
							if ( !(@unlink("image/catalog/img-pril/".$value)) ) 
							{
								die('Ошибка удаления изображения');
							}
						} else {
							print_r($userside_add_comment_other);
							exit;
						}
					}
				}
				print_r ("Отправка прошла успешно.");
			} else {
				print_r ($userside_add_comment);
			}
		}
	}
}
