<?php
require('../config.php');
require('../init.php');

if (!isset($_SESSION['admin']) || $_SESSION['admin'] != $mvadminpass) {
	unset($_SESSION['admin']);
	die('Not logged in.');
}

if ($mvrewardtac) {
	if (empty($_POST['name']) || empty($_POST['reward']) || empty($_POST['ip'])) {
		//var_dump($_POST);
		die('All input is required.');
	}
	
	$insert = mv_insert_reward($_POST['name'], $_POST['ip']);
	echo(mv_update_reward($_POST['reward'], $insert));
}
else {
	if (empty($_POST['name']) || empty($_POST['site']) || empty($_POST['ip'])) {
		//var_dump($_POST);
		die('All input is required.');
	}
	
	$insid = mv_insert_vote($_POST['site'], $_POST['name'], $_POST['ip']);
	echo(mv_update_vote($insid, '127.0.0.1', json_encode($_GET).json_encode($_POST)));
}
?>