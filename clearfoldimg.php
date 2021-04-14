<?php
class ControllerPrilClearfoldimg extends Controller {
	public function index() {
		
		require_once 'function.php';
		
		$user = '';
		$task_id = '';
		
		foreach (object_to_array(retrieveJsonPostData()) as $pril_key => $pril_value) {
			switch ($pril_key) {
				case 'user':
					$user = $pril_value;
					break;
				case 'task_id':
					$task_id = $pril_value;
					break;
			}
		}
			
		if ($user == '' || $task_id == '') {
			echo "Данные переданы не верно!";
			exit;
		}
		
		$entries = scandir("image/catalog/img-pril");
		$filelist = array();
		foreach($entries as $entry) {
			if (strpos($entry, $user) === 0) {
				if (stripos($entry, $task_id) !== false ) {
					if ( !(@unlink("image/catalog/img-pril/".$entry)) ) die('Error Delete File.');
				}
			}
		}

		print_r ("Отправка комментария отменена");
		
	}
}
