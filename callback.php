<?php
require('config.php');
require('init.php');
$auth = false;
$data = '';

if (count($_GET) > 0) {
	// scan get and post variables for our incentive string
	foreach ($_GET as $key => $value) {
		$auth = mv_incentive_array($value);
		
		if (is_array($auth)) {
			break;
		}
	}
	
	foreach ($_POST as $key => $value) {
		$auth = mv_incentive_array($value);
		
		if (is_array($auth)) {
			break;
		}
	}
	
	// if we found it in our get/post then go ahead and process it.
	if (is_array($auth)) {
		if ($auth['hash'] != $mvsecurityhash) {
			die('Invalid hash.');
		}
		
		print_r($auth);
		$mvdb->escapedQuery("UPDATE `".DBPRE."votes` SET
										`callbackdate` = UTC_TIMESTAMP(),
										`ready` = true,
										`callbackip` = '%1:s',
										`callbackdata` = '%2:s'
									WHERE `id` = %3:d AND `ready` != true",
								$_SERVER['REMOTE_ADDR'], json_encode($_GET).json_encode($_POST), intval($auth['id']));
	}
	else {
		echo('Invalid callback.');
	}
}

if (mv_setbool('log_callback')) {
	$f = 'callback.log';
	
	if (is_writable($f)) {
		$fh = fopen($f, 'a') or die('Can not open file. Make sure it exists and is CHMOD 0777');
		$data .= 'DATE: '.date('m-d-y Y h:i:s A e')."\n";
		$data .= 'IP:   '.$_SERVER['REMOTE_ADDR']."\n";
		$data .= 'AUTH: '.json_encode($auth)."\n";
		$data .= 'GET:  '.json_encode($_GET)."\n";
		$data .= 'POST: '.json_encode($_POST)."\n";
		$data .= "********************************************\n";
		fwrite($fh, $data);
		fclose($fh);
	}
	else {
		echo('Can\'t write to log file. CHMOD 0777 log file to fix.');
	}
}
?>