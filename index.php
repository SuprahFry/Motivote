<?php
require('init.php');
$mvthemedir = 'templates/'.mv_setting('selected_theme');

if (isset($_GET['ajax'])) {
	$path = realpath($mvthemedir.'/'.str_replace('../', '', $_GET['ajax']).'.php');
	
	if ($_GET['ajax'] == 'times' && isset($_SESSION['user'])) {
		if (empty($_SESSION['user'])) {
			die('Session invalid.');
		}
		
		echo(json_encode(mv_vote_times()));
	}
	else if ($_GET['ajax'] == 'step3continue' && isset($_SESSION['user'])) {
		if (empty($_SESSION['user'])) {
			die('Session invalid.');
		}
		
		$rewardvotes = mv_reward_votes();
		$rewardentry = mv_reward();
		//var_dump($rewardentry);
		
		if (empty($rewardentry)) {
			echo(mv_site_count() - count($rewardvotes));
		}
		else {
			echo(0);
		}
	}
	else if ($path !== false) {
		include($path);
		/*if (strpos($path, $mvthemedir.'/') !== false) {
			require($path);
		}
		else {
			header('HTTP/1.1 404 Not Found');
			echo('The requested file could not be found');
		}*/
	}
	else {
		die('Invalid request.');
	}
	
	die();
}

require($mvthemedir.'/index.php');
?>