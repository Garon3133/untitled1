<?php
 $server = 'inetmail.ru';
 $port = 80;
 $status = 'unavailable';
 $timeout = 10;
 $fp = @fsockopen ($server, $port, $errno, $errstr, $timeout);
 if ($fp) {
  $status = 'alive, not responding';
  @fwrite ($fp, "HEAD / HTTP/1.0\r\nHost: $server:$port\r\n\r\n");
  if (strlen(@fread($fp,1024))>0) $status = 'alive, responding';
  fclose ($fp);
 }
 echo "$server status is $status"; 
?>