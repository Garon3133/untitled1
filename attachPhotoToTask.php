<?php
class ControllerPrilAttachPhotoToTask extends Controller {
	public function index() {

		require_once 'function.php';
		
		$task_id = '85533';
		//$comment_id = '159031';
		$comment_id = '';
		$user = 'unity';
		$img_base64 = '';
		$key_pril = '100-200';
		
		$Arrr = object_to_array(retrieveJsonPostData());
		if (is_array(@$Arrr) || is_object(@$Arrr)){
			foreach ($Arrr as $pril_key => $pril_value) {
				switch ($pril_key) {
				case 'task_id':
					$task_id = $pril_value;
					break;
				case 'img_base64':
					$img_base64 = $pril_value;
					break;
				case 'comment_id':
					$comment_id = $pril_value;
					break;
				}
			}
		}
		if ($task_id == '' || $img_base64 == '') {
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
		
		
		
		
		$date = date('Y-m-d-H-i-s');
		$time = rand(100, 999);
		
		file_put_contents("image/catalog/img-pril/".$user."-".$task_id."-".$date."-".$time.".png", base64_decode($img_base64));
		
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
		
		$urlImg = $http.$_SERVER['HTTP_HOST']."/".dirname($_SERVER["SCRIPT_NAME"])."image/catalog/img-pril/".$user."-".$task_id."-".$date."-".$time.".png";
		if($comment_id == ''){
			$addAttach = array(
				'key' 	 	  => APIKEY_USERSIDE,
				'cat'		  => 'attach',
				'action'   	  => 'add',
				'object_type' => 'task',
				'object_id'	  => $task_id,
				'src' 		  => $urlImg,
			);
		} else {
			$addAttach = array(
				'key' 		  => APIKEY_USERSIDE,
				'cat' 		  => 'attach',
				'action' 	  => 'add',
				'object_type' => 'task_comment',
				'object_id'   => $task_id,
				'comment_id'  => $comment_id,
				'src' 	 	  => $urlImg,
			);
		}
		
		$userside_attach = create_ticket($addAttach, USERSIDE);
		// удаляем изображение
		if ( !(@unlink("image/catalog/img-pril/".$user."-".$task_id."-".$date."-".$time.".png")) ) die('Ошибка удаления изображения');
	}
}
