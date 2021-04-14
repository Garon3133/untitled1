<?php
class ControllerPrilTestNotif extends Controller {
	public function index() {
			//date_default_timezone_set('Europe/Moskow');
			require_once 'function.php';
			require_once 'firebaseNotification.php';
			$this->load->model('pril/allow');
			$Notification = new FirebaseNotificationClassAndr;
			$Notification->send_notification("cxVPvxedSL6IJHQayoVKs0:APA91bEf2_H9pK9XMCKwigwaT0jyo0ZIdw8FtJxQmld7ke3FetlCO5LvqgJHAh6vBwVqi4BsyAbk0-n29GrjcF0EJBz4RicGvyzOdxZNU6xVzp8PrvWjRZtAC8z__SlctdqtepqTx8cZ", "Da smert");
													
	}
}
?>