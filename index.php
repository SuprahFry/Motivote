<?php
require('config.php');

if (!isset($mvdbpass)) {
	header('Location: install.php');
	die();
}

require('init.php');
$mvthemedir = 'templates/'.mv_setting('selected_theme');

if (isset($_GET['ajax'])) {
	$path = realpath($mvthemedir.'/'.$_GET['ajax'].'.php');
	
	if ($_GET['ajax'] == 'times' && isset($_SESSION['user'])) {
		echo(json_encode(mv_vote_times()));
	}
	else if ($_GET['ajax'] == 'step3continue' && isset($_SESSION['user'])) {
		$rewardvotes = mv_reward_votes();
		$rewardentry = mv_reward();
		
		if ($rewardentry === null || $rewardentry === false) {
			echo(mv_active_site_count() - count($rewardvotes));
		}
		else {
			echo(0);
		}
	}
	else if ($path !== false) {
		if (strpos($path, $mvthemedir.'/') !== false) {
			require($path);
		}
		else {
			header('HTTP/1.1 404 Not Found');
			echo('The requested file could not be found');
		}
	}
	
	die();
}

require($mvthemedir.'/index.php');
?>