<?php
define( 'API_ACCESS_KEY', 'AAAAIuc2gws:APA91bHIosM78MLauumC6IF1waBT5sYvIQLHu3zHOmPY_AD-uekkSDOSLm6HmGV_LzFUIaXcae_YvMwwQ9MVj0__vy6B5r56QzU6L6nBAf5JfiXn24rvcpw3YHVbCvAWuez_z-jjSjbL' );
define( 'API_ACCESS_KEY2', 'AAAAJl23p1A:APA91bFZZrUzf6VBXHP3VPVeSxtJDRoo5DqMb0SLy62N5Rk0grn4svRZZgkveBtooZfJRVjvW8pxWgA1d2hHDXXydjswC7W27Fu9Mj9SNVl7PZhj6SOJaCgPdRYlDf5biMgZpbTa25B8' );
define( 'API_ACCESS_KEY3', 'AAAALuEZ63Q:APA91bGNQP-UQS46wmavDcaFbbbQz_hQhsNHvp-7-Ni7a_h0aRlyR1U8MqmTEs-dC_Ucg2RU5B2jaUe16EzzBs4JFZtyH_JqtAVkRb5NNHItbXrvW_1D53OLvDoIbRmyn1hLTRBMCs8d' );
define( 'API_ACCESS_KEY_UNITY', 'AAAAER_nG9Q:APA91bF-8yq9xJn6bidKdKK7O10U2QAQVWIufrQfm20uH3-mmxLz7sMLUQIq5L-eVCH9VjFbOhQtxmG7EEa1ICTPi42ncr-Y8W7-YpFUf-wQunGlO0jItVZC016u3M6HJWfAfDqmlMkL' );
define( 'FIREBASE_SEND_URL', 'https://fcm.googleapis.com/fcm/send' );

class FirebaseNotificationClass {
	function __construct() {

	}
    /**
     * Sending Push Notification
     */ 
    public function send_notification($registratoin_ids, $message) {
    	$msg = array
    	(
    		'title'		=> 'Firebase Notification',
    		'message'	=> $message,
    		'type'		=> 'message'
    	);
    	$fields = array
    	(
    		'registration_ids' 	=> array($registratoin_ids) ,
    		'data'			=> $msg
    	);

    	$headers = array
    	(
    		'Authorization: key=' . API_ACCESS_KEY,
    		'Content-Type: application/json'
    	);
    	$ch = curl_init();
    	curl_setopt( $ch,CURLOPT_URL, FIREBASE_SEND_URL );
    	curl_setopt( $ch,CURLOPT_POST, true );
    	curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    	curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    	curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    	curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
    	$result = curl_exec($ch );
		echo $result;
    	curl_close( $ch );
    }
}

class FirebaseNotificationClass2 {
	function __construct() {

	}
    /**
     * Sending Push Notification
     */ 
    public function send_notification($registratoin_ids, $message) {
    	$msg = array
    	(
    		'title'		=> 'Firebase Notification',
    		'message'	=> $message,
    		'type'		=> 'message'
    	);
    	$fields = array
    	(
    		'registration_ids' 	=> array($registratoin_ids) ,
    		'data'			=> $msg
    	);

    	$headers = array
    	(
    		'Authorization: key=' . API_ACCESS_KEY2,
    		'Content-Type: application/json'
    	);
    	$ch = curl_init();
    	curl_setopt( $ch,CURLOPT_URL, FIREBASE_SEND_URL );
    	curl_setopt( $ch,CURLOPT_POST, true );
    	curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    	curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    	curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    	curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
    	$result = curl_exec($ch );
		echo $result;
    	curl_close( $ch );
    }
}

class FirebaseNotificationClass3 {
	function __construct() {

	}
    /**
     * Sending Push Notification
     */ 
    public function send_notification($registratoin_ids, $message) {
    	$msg = array
    	(
    		'title'		=> 'Firebase Notification',
    		'message'	=> $message,
    		'type'		=> 'message'
    	);
    	$fields = array
    	(
    		'registration_ids' 	=> array($registratoin_ids) ,
    		'data'			=> $msg
    	);

    	$headers = array
    	(
    		'Authorization: key=' . API_ACCESS_KEY3,
    		'Content-Type: application/json'
    	);
    	$ch = curl_init();
    	curl_setopt( $ch,CURLOPT_URL, FIREBASE_SEND_URL );
    	curl_setopt( $ch,CURLOPT_POST, true );
    	curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    	curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    	curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    	curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
    	$result = curl_exec($ch );
		echo $result;
    	curl_close( $ch );
    }
}

class FirebaseNotificationClassUnity {
	function __construct() {

	}
    /**
     * Sending Push Notification
     */ 
    public function send_notification($registratoin_ids, $message) {
    	$notification = [
			'title' =>'Внимание !',
			'body' => $message,
			'icon' =>'myIcon', 
			'sound' => 'mySound'
		];
		$extraNotificationData = ["message" => $notification,"moredata" =>'dd'];
	
		$fcmNotification = [
			'to'        => $registratoin_ids,
			'notification' => $notification,
			'data' => $extraNotificationData
		];
	
		$headers = [
			'Authorization: key=' . API_ACCESS_KEY_UNITY,
			'Content-Type: application/json'
		];
	
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,FIREBASE_SEND_URL);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
		$result = curl_exec($ch);
		curl_close($ch);
	
		print_r("UNITY NOTIFICATION");
		echo $result;
    }
}

?>