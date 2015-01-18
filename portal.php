<?php
require('config.php');
require('init.php');

if (!isset($_SESSION['user'])) {
	header('Location: index.php');
	die('Session must have expired, try again.');
}

if (isset($_GET['site'])) {
	$user = $_SESSION['user'];
	$site = mv_site($_GET['site']);
	$res = mv_voted_within($site['waittime'], $site['id'], $user);
	$insert = -1;
	//var_dump($res);
	
	if (!empty($res) && count($res) > 0) {
		$insert = $res['id'];
		
		if ($res['ready'] == '1') {
			die('Vote has received callback within last '.$site['waittime'].' hours. Try again in '.$res['nextvote'].'.');
		}
	}
	else {
		$insert = mv_insert_vote($site['id'], $user);
		//var_dump($insert);
	}
	
	//print_r($res);
	//echo('<br />rows: '.count($res).'<br />user: '.$incentive.'<br />vote: '.$insert);
	//$voteurl = getVoteURL($source, $incentive, $insert);
	$siteurl = $site['voteurl'];
	$siteurl = str_replace('{id}', $site['voteurlid'], $siteurl);
	$siteurl = str_replace('{incentive}', mv_incentive_string($site['name'], $insert), $siteurl);
	//echo($siteurl);
	header('Location: '.$siteurl);
	die();
}
?>