<?php
class ControllerPrilMyscript extends Controller {
	public function index() {

		require_once 'function.php';
		
		$user_name = 'andrey';
		$idtask = '38358';
		$key_pril = '100-200';
		
		foreach (object_to_array(retrieveJsonPostData()) as $pril_key => $pril_value) {
			switch ($pril_key) {
				case 'user_name':
					$user_name = $pril_value;
					break;
				case 'idtask':
					$idtask = $pril_value;
					break;
				case 'key_pril':
					$key_pril = $pril_value;
					break;
			}
		}
		
		if ($user_name == '' || $idtask == '' || $key_pril == '') {
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
		
		define('MYSCRIPT', $arr_customer[15]);
		
		print_r (MYSCRIPT);
		
		$info = array(
			"user_name" => $user_name,
			"idtask" => $idtask,
		);
		
		// создаем подключение
		$ch = curl_init(MYSCRIPT);
		// устанавлваем даные для отправки
		curl_setopt($ch, CURLOPT_POSTFIELDS, $info);
		// флаг о том, что нужно получить результат
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// отправляем запрос
		$response = curl_exec($ch);
		// закрываем соединение
		curl_close($ch);
		
		var_export($response);
	
	}
}