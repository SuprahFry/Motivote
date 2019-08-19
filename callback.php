<?php
require('init.php');
$auth = false;
$data = '';

$postdata = $_POST ?: $_GET;

if (count($postdata) > 0) {
	// scan get and post variables for our incentive string
	foreach ($postdata as $key => $value) {
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
		
		//print_r($auth);
		$res = mv_update_vote(intval($auth['id']), $_SERVER['REMOTE_ADDR'], json_encode($_GET).json_encode($_POST));
		
		if (!$res) {
			echo('Fail. Query issue.');
		}
		else {
			echo('Success!');
		}
	}
	else {
		echo('Invalid callback.');
	}
}

if (mv_setbool('log_callback')) {
	$vid = empty($auth) ? 0 : $auth['id'];
	$headers = array();

	foreach ($_SERVER as $k => $v) {
		if (strpos($k, 'HTTP_') !== false || strpos($k, 'CF_') !== false) {
			$headers[$k] = $v;
		}
	}

	mv_insert_cbdata($vid, json_encode($_GET), json_encode($_POST), json_encode($headers), json_encode($auth), $_SERVER['REMOTE_ADDR']);
}
/*if (mv_setbool('log_callback')) {
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
}*/
?>
