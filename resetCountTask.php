<?php
	function Zapros($zapros){
		$link = mysqli_connect("localhost", "intronex", "5Q3d0E1f", "android_pril_server1");
		if ($link == false){
				print_r("Не удалось подключиться к БД." . mysqli_connect_error());
		}else{
			$result = mysqli_query($link, $zapros) or die("Ошибка запроса." . mysqli_error($link));
		}
		mysqli_close($link);
	}
	$zapros = "UPDATE oc_customer_connecteddevices SET count_task = '0'";
	$result = Zapros($zapros);
	print_r("Все таски у работников, которые были в БД, обнулились ".date("r"));
?>