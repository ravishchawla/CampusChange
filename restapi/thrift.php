<?php
require_once('thriftapi.php');

if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
	$_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

try {
	$API = new ThriftAPI($_REQUEST, $_SERVER['HTTP_ORIGIN']);
	echo $API->processApi();
}

catch (Exception $e) {
	echo json_encode(Array('error' => $e->getMessage()));
}