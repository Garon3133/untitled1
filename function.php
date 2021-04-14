<?php

function load_config() {
	
	$line = [];
	$file_handle = fopen("config.ini", "r");
	while (!feof($file_handle)) {
		$file_line = explode("=", fgets($file_handle));
		array_push($line, $file_line);
	}
	fclose($file_handle);

	return $line;
	
}

function retrieveJsonPostData() {
    $rawData = file_get_contents("php://input");
    return json_decode($rawData);
}

function object_to_array($data) {
	
    if (is_array($data) || is_object($data))
    {
        $result = array();
        foreach ($data as $key => $value)
        {
            $result[$key] = object_to_array($value);
        }
        return $result;
    }
    return $data;
	
}

function post_login($postData, $Url) {
	
	$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $Url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__).'/cookie.txt'); // сохранять куки в файл
	curl_setopt($ch, CURLOPT_COOKIEFILE,  dirname(__FILE__).'/cookie.txt');
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}

function login($post_data, $url) {
	$headers = array(
		'cache-control: max-age=0',
		'upgrade-insecure-requests: 1',
		'user-agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 Safari/537.36',
		'sec-fetch-user: ?1',
		'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
		'x-compress: null',
		'sec-fetch-site: none',
		'sec-fetch-mode: navigate',
		'accept-encoding: deflate, br',
		'accept-language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
	);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url ); // отправляем на
	curl_setopt($ch, CURLOPT_HEADER, 1); // пустые заголовки
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // возвратить то что вернул сервер
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // следовать за редиректами
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);// таймаут4
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// просто отключаем проверку сертификата
	curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__).'/cookie.txt'); // сохранять куки в файл
	curl_setopt($ch, CURLOPT_COOKIEFILE,  dirname(__FILE__).'/cookie.txt');
	curl_setopt($ch, CURLOPT_POST, 1); // использовать данные в post
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}

function commutation($url) {
	$headers = array(
		'cache-control: max-age=0',
		'upgrade-insecure-requests: 1',
		'user-agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 Safari/537.36',
		'sec-fetch-user: ?1',
		'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
		'x-compress: null',
		'sec-fetch-site: none',
		'sec-fetch-mode: navigate',
		'accept-encoding: deflate, br',
		'accept-language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
	);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url ); // отправляем на
	curl_setopt($ch, CURLOPT_HEADER, 0); // пустые заголовки
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // возвратить то что вернул сервер
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // следовать за редиректами
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);// таймаут4
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// просто отключаем проверку сертификата
	curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__).'/cookie.txt'); // сохранять куки в файл
	curl_setopt($ch, CURLOPT_COOKIEFILE,  dirname(__FILE__).'/cookie.txt');
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}

function httpGetPrev($Url) {

    $ch = curl_init();  
    curl_setopt($ch,CURLOPT_URL, $Url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__).'/cookie.txt'); // сохранять куки в файл
	curl_setopt($ch, CURLOPT_COOKIEFILE,  dirname(__FILE__).'/cookie.txt');
    $output=curl_exec($ch);
    curl_close($ch);
    return $output;
}

function httpGet($postData, $Url) {

    $ch = curl_init();  
    curl_setopt($ch,CURLOPT_URL, $Url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIEFILE,  dirname(__FILE__).'/cookie.txt');
    $output=curl_exec($ch);
    curl_close($ch);
    return $output;
}

function httpGetNext($Url) {

    $ch = curl_init();  
    curl_setopt($ch,CURLOPT_URL, $Url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIEFILE,  dirname(__FILE__).'/cookie.txt');
    $output=curl_exec($ch);
    curl_close($ch);
    return $output;
}

function create_ticket($postData, $usersideURL) {
	
	$ch = curl_init($usersideURL.'/api.php');
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$userside = curl_exec($ch);
	curl_close($ch);
	
	return $userside;
}

function myscandir($dir, $sort=0)
{
	$list = scandir($dir, $sort);
	
	if (!$list) return false;
	
	if ($sort == 0) unset($list[0],$list[1]);
	else unset($list[count($list)-1], $list[count($list)-1]);
	return $list;
}

function myscandirnew($dir)
{
	$list = scandir($dir);
	unset($list[0],$list[1]);
	return array_values($list);
}

function clear_dir($dir)
{
	$list = myscandirnew($dir);
	
	foreach ($list as $file)
	{
		if (is_dir($dir.$file))
		{
			clear_dir($dir.$file.'/');
			rmdir($dir.$file);
		}
		else
		{
			unlink($dir.$file);
		}
	}
}

function load_imei($imei, $tel) {
	
	$file_line = 0;
	$file_handle = fopen("imei.txt", "r");
	while (!feof($file_handle)) {
		$file_imei = explode("=", fgets($file_handle));
		if (strcasecmp($imei, $file_imei[0]) == 0) {
			$file_line = str_replace(array("\r","\n"), "", $file_imei[2]);
		}
	}
	fclose($file_handle);
	
	if ($file_line == 1) {
		save_imei($imei, $tel);
	}
	
	return $file_line;
	
}

function save_imei ($imei, $tel) {
	
	$filename = 'imei.txt';
	$s = fopen($filename, "a+") or die("не удалось открыть файл");
	$str_num = 1;
	$con = 0;
	$str_num_is = 0;
	while (!feof($s)) {
		$str_line = str_replace(array("\r","\n"), "", fgets($s));
		$str_arr = explode("=", $str_line);
		if (strcasecmp($imei, $str_arr[0]) == 0) {
			$con = 1;
			$str_tel = $str_arr[1];
			$str_num_is = $str_num;
		}
		$str_num++;
	}
	
	if ($con == 1) {
		if (strcasecmp($tel, $str_tel) != 0) {
			$new_replacement = $imei."=".$tel."=1";
 
			$file_replacement = file($filename);
			$file_replacement[$str_num_is-1] = $new_replacement.PHP_EOL;
			file_put_contents($filename, join('', $file_replacement));
			fclose($s);
			return ("IMEI перезаписан");
		} else {
			fclose($s);
			return ("Такие данные уже есть");
		}
	} else {
		fwrite($s, $imei."=".$tel."=0\r\n");
		fclose($s);
		return ("IMEI записан");
	}

}

function save_update_control ($opis) {
	
	$s = fopen("CONTROL.ini","w");
	fwrite($s,$opis."\r\n");
	fclose($s);
	
}

function search_key($searchKey, array $arr, array &$result) {
    // Если в массиве есть элемент с ключем $searchKey, то ложим в результат
    if (isset($arr[$searchKey])) {
        $result[] = $arr[$searchKey];
    }
    // Обходим все элементы массива в цикле
    foreach ($arr as $key => $param) {
        // Если эллемент массива есть массив, то вызываем рекурсивно эту функцию
        if (is_array($param)) {
            search_key($searchKey, $param, $result);
        }
    }
}