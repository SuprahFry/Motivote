<?php
session_start();
define('BASE_DIR', str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname(__FILE__)));
require('class-dbi.php');
$mvdb = new Database($mvdbuser, $mvdbpass, $mvdbhost, $mvdb);
$mvsetcache = array();
$mvphrcache = array();

$mvsecurityhash = mv_setting('security_hash');
$mvadminpass = mv_setting('admin_pass');

function mv_site($id) {
	global $mvdb;
	return $mvdb->escapedArrayAssoc("SELECT * FROM `".DBPRE."sites` WHERE `id` = %1:d", intval($id));
}

function mv_incentives() {
	global $mvdb;
	return $mvdb->escapedAllResultsAssoc("SELECT * FROM `".DBPRE."incentives` WHERE `active` = 1");
}

function mv_base_url() {
	return 'http://'.$_SERVER['HTTP_HOST'].BASE_DIR;
}

function mv_callback_url() {
	return mv_base_url().'/callback.php?auth=';
}

function mv_reward_votes() {
	global $mvdb;
	$data = $mvdb->escapedAllResultsAssoc("SELECT `".DBPRE."votes`.`id`,
											`".DBPRE."votes`.`site`,
											`".DBPRE."votes`.`callbackdate`
										FROM `".DBPRE."votes`
										INNER JOIN
											(SELECT max(`id`) AS `id`, `site` FROM `".DBPRE."votes`
												WHERE (`user` = '%1:s' OR `ip` = '%2:s')
												GROUP BY `site` ORDER BY `callbackdate` ASC) `dest`
										ON `dest`.`id` = `".DBPRE."votes`.`id`
										WHERE `callbackdate` IS NOT NULL AND `fulfilled` = 0
											AND (`user` = '%3:s' OR `ip` = '%4:s')",
										$_SESSION['user'], $_SERVER['REMOTE_ADDR'], $_SESSION['user'], $_SERVER['REMOTE_ADDR']);
	return $data;
}

function mv_reward() {
	global $mvdb;
	$rewardentry = $mvdb->escapedArrayAssoc("SELECT * FROM `".DBPRE."rewards`
											WHERE `ready` = 0 AND (`user` = '%1:s' OR `ip` = '%2:s')
											LIMIT 0, 1", $_SESSION['user'], $_SERVER['REMOTE_ADDR']);
	return $rewardentry;
}

function mv_active_site_count() {
	global $mvdb;
	return $mvdb->escapedCountQuery("SELECT COUNT(*) FROM `".DBPRE."sites` WHERE `active` = 1");
}

function mv_vote_times() {
	global $mvdb;
	// WHERE (`user` = '%1:s' OR `ip` = '%2:s')
	$data = $mvdb->escapedAllResultsAssoc("SELECT `".DBPRE."votes`.`id`,
										`".DBPRE."votes`.`site`,
										`".DBPRE."sites`.`waittime`,
										`".DBPRE."votes`.`callbackdate`,
										`".DBPRE."votes`.`fulfilled`,
										(`callbackdate` > UTC_TIMESTAMP() - INTERVAL `waittime` HOUR) `outoftime`
									FROM `".DBPRE."votes`
									INNER JOIN
										(SELECT max(`id`) AS `id`, `site` FROM `".DBPRE."votes`
											WHERE (`user` = '%1:s' OR `ip` = '%2:s')
											GROUP BY `site` ORDER BY `callbackdate` DESC) `dest`
										ON `dest`.`id` = `".DBPRE."votes`.`id`
									INNER JOIN `".DBPRE."sites` ON `".DBPRE."sites`.`id` = `".DBPRE."votes`.`site`
									WHERE `callbackdate` IS NOT NULL AND ((`callbackdate` > UTC_TIMESTAMP() - INTERVAL `waittime` HOUR) OR `fulfilled` = 0) AND (`user` = '%3:s' OR `ip` = '%4:s')",
									$_SESSION['user'], $_SERVER['REMOTE_ADDR'], $_SESSION['user'], $_SERVER['REMOTE_ADDR']);
	return $data;
}

function mv_incentive_string($incentive, $id) {
	global $mvsecurityhash;
	return 'mv_'.$mvsecurityhash.'_'.$id;
}

function mv_incentive_array($string) {
	$result = preg_match("/^.*(?P<auth>mv)_(?P<hash>.*)_(?P<id>\d+).*$/", $string, $data);
	
	if ($result === false || $result == 0) {
		return false;
	}
	
	return $data;
}

function mv_phrase($name) {
	global $mvdb, $mvphrcache;
	$valueCount = func_num_args();
	$arguments = array();
	
	if ($valueCount > 1) {
		$arguments = func_get_args();
		unset($arguments[0]); // unset name
	}
	
	$value = '';
	
	if (array_key_exists($name, $mvphrcache)) {
		$value = $mvphrcache[$name];
		$mvphrcache[$name] = $value; // cache phrase in case we want it later, to save on queries
	}
	else {
		$result = $mvdb->escapedArrayAssoc("SELECT * FROM `".DBPRE."phrases` WHERE `name` = '%1:s'", $name);
		$value = $result['value'];
	}
	
	$index = 1;
	
	foreach ($arguments as $argval) {
		$value = str_replace('{'.($index++).'}', $argval, $value);
	}
	
	return $value;
}

function mv_phrases() {
	global $mvdb;
	$result = $mvdb->escapedAllResultsAssoc("SELECT * FROM `".DBPRE."phrases`");
	return $result;
}

function mv_setbool($name) {
	return mv_setting($name) == '1';
}

function mv_setting($name) {
	global $mvdb, $mvsetcache;
	
	if (array_key_exists($name, $mvsetcache) && !empty($mvsetcache[$name])) {
		return $mvsetcache[$name];
	}
	
	$result = $mvdb->escapedArrayAssoc("SELECT * FROM `".DBPRE."preferences` WHERE `name` = '%1:s'", $name);
	$mvsetcache[$name] = $result['value'];
	return $result['value'];
}

function mv_settings() {
	global $mvdb;
	$result = $mvdb->escapedAllResultsAssoc("SELECT * FROM `".DBPRE."preferences`");
	return $result;
}

function createCall($function, $prependArguments, $arguments)
{
	$args = array_merge((array)$prependArguments, (array)$arguments);
	return @call_user_func_array(array($this, $function), $args);
}
?>