<?php
require('config.php');
require('init.php');

if (isset($_GET['site'])) {
	$user = $_SESSION['user'];
	$site = mv_site($_GET['site']);
	$res = $mvdb->escapedArrayAssoc("SELECT *,
											SEC_TO_TIME(TIMESTAMPDIFF(SECOND, (UTC_TIMESTAMP() - INTERVAL %1:d HOUR), `callbackdate`)) AS `nextvote`
										FROM `".DBPRE."votes`
										WHERE (`callbackdate` IS NULL
												OR (`callbackdate` > UTC_TIMESTAMP() - INTERVAL %2:d HOUR))
											AND `site` = %3:d
											AND (`user` = '%4:s' OR `ip` = '%5:s')
										ORDER BY `id` DESC LIMIT 0,1",
										$site['waittime'], $site['waittime'], $site['id'], $user, $_SERVER['REMOTE_ADDR']);
	$insert = -1;
	
	if (count($res) > 0) {
		$insert = $res['id'];
		
		if ($res['ready'] == '1') {
			die('Vote has received callback within last '.$site['waittime'].' hours. Try again in '.$res['nextvote'].'.');
		}
	}
	else {
		$mvdb->escapedQuery("INSERT INTO `".DBPRE."votes`
									(`site`, `user`, `ip`, `opendate`, `callbackdate`, `ready`, `fulfilled`)
									VALUES (%1:d, '%2:s', '%3:s', UTC_TIMESTAMP(), null, false, false)",
									$site['id'], $user, $_SERVER['REMOTE_ADDR']);
		$insert = $mvdb->lastInsertID();
		//echo('inserted');
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