<?php
class ControllerPrilNewtask extends Controller {
	public function index() {

		require_once 'function.php';
		$user = '';
		$date = '';
		$opis = '';
		$key_pril = '100-200';
		
		foreach (object_to_array(retrieveJsonPostData()) as $pril_key => $pril_value) {
			switch ($pril_key) {
				case 'user':
					$user = $pril_value;
					break;
				case 'date':
					$date = $pril_value;
					break;
				case 'opis':
					$opis = $pril_value;
					break;
				case 'key_pril':
					$key_pril = $pril_value;
					break;
				case 'task_Id':
					$taskID = $pril_value;
					break;
			}
		}
		
		if ($user == '' || $date == '' || $opis == '' || $key_pril == '') {
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
		define('WORK_TYPER', $arr_customer[4]); //id типа задания для создания задания в юс
		define('UNIT_ID', $arr_customer[6]); //ID исполнителя (допускается несколько значений через запятую)
		define('DIVISION_ID', $arr_customer[7]); //ID подразделения (допускается несколько значений через запятую)
		define('PERFORMER', $arr_customer[5]); //назначать создателя задания исполнителем

		$userUser = array(
			'key' => APIKEY_USERSIDE,
			'cat' => 'operator',
			'action' => 'get_id_by_login',
			'login' => $user,
		);
		$userside_user = object_to_array(json_decode(create_ticket($userUser, USERSIDE)));

		$id_user = '';
		if ($userside_user['Result'] == 'OK') {
			$id_user = $userside_user['id'];
		} else {
			print_r ($userside_user['ErrorText']); //Operator Not Found
			exit;
		}

		$userID = array(
			'key' => APIKEY_USERSIDE,
			'cat' => 'operator',
			'action' => 'get',
			'id' => $id_user,
		);
		$userside_userid = object_to_array(json_decode(create_ticket($userID, USERSIDE)));
		if ($userside_userid['Result'] == 'OK') {
			$userid = $userside_userid['data'][$id_user]['staff_id'];
		} else {
			$userid = 0;
		}

		if (PERFORMER == 0) {
			$unit_id = UNIT_ID;
		} else {
			$unit_id = $userid.",".UNIT_ID;
		}
		if($taskID == 1){
			$userdata = array(
				'key' => APIKEY_USERSIDE,
				'cat' => 'task',
				'subcat' => 'add',
				'work_typer' => WORK_TYPER,
				'work_datedo' => $date,
				'author_operator_id' => $id_user,
				'opis' => $opis,
				'unit_id' => $unit_id,
				'division_id' => DIVISION_ID,
			);
			$userside_newtask = object_to_array(json_decode(create_ticket($userdata, USERSIDE)));
		}else{
			$userdata = array(
				'key' => APIKEY_USERSIDE,
				'cat' => 'task',
				'subcat' => 'add',
				'work_typer' => WORK_TYPER,
				'work_datedo' => $date,
				'author_operator_id' => $id_user,
				'opis' => $opis,
				'unit_id' => $unit_id,
				'division_id' => DIVISION_ID,
				'parent_task_id' => $taskID,
			);
			$userside_newtask = object_to_array(json_decode(create_ticket($userdata, USERSIDE)));
		}
			
		//print_r($userdata);	
		//print_r($userside_newtask);
			
		if ($userside_newtask['Result'] == 'OK') {
			$task_id = $userside_newtask['Id'];
			
			// проверяем папку с изображениями для загрузки в юзерсайд
			$entries = scandir("image/catalog/img-pril");
			$files = array();
			foreach($entries as $entry) {
				if (strpos($entry, $user) === 0) {
					$files[] = $entry;
				}
			}
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
					
					$urlImg = $http.$_SERVER['HTTP_HOST']."/".dirname($_SERVER["SCRIPT_NAME"])."/image/catalog/img-pril/";
					$addAttach = array(
						'key' => APIKEY_USERSIDE,
						'cat' => 'attach',
						'action' => 'add',
						'object_type' => 'task',
						'object_id' => $task_id,
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
			print_r ("Задание успешно создано");
			
	
		} else {
			print_r ($userside_newtask['ErrorText']);
		}
	
	}
}
