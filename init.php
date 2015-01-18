<?php
ini_set('error_reporting', E_ALL);
ini_set("display_errors", 1);
error_reporting(E_ALL);
session_start();
define('BASE_DIR', str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname(__FILE__)));
$db = new PDO('mysql:host='.$mvdbhost.';dbname='.$mvdb, $mvdbuser, $mvdbpass);
$prep = array();
$prep['site'] = $db->prepare('SELECT * FROM `'.DBPRE.'sites` WHERE `id` = :id');
$prep['incentives'] = $db->prepare('SELECT * FROM `'.DBPRE.'incentives` WHERE `active` = :active');
$prep['rewvotes'] = $db->prepare('SELECT `'.DBPRE.'votes`.`id`,
											`'.DBPRE.'votes`.`site`,
											`'.DBPRE.'votes`.`callbackdate`
										FROM `'.DBPRE.'votes`
										INNER JOIN
											(SELECT max(`id`) AS `id`, `site` FROM `'.DBPRE.'votes`
												WHERE (`user` = :user OR `ip` = :ip)
												GROUP BY `site` ORDER BY `callbackdate` ASC) `dest`
										ON `dest`.`id` = `'.DBPRE.'votes`.`id`
										WHERE `callbackdate` IS NOT NULL AND `fulfilled` = 0
											AND (`user` = :user2 OR `ip` = :ip2)');
$prep['reward'] = $db->prepare('SELECT * FROM `'.DBPRE.'rewards`
										WHERE `ready` = 0 AND (`user` = :user OR `ip` = :ip)
										LIMIT 0, 1');
$prep['sitecount'] = $db->prepare('SELECT COUNT(*) FROM `'.DBPRE.'sites` WHERE `active` = :active');
$prep['votelast'] = $db->prepare('SELECT *, SEC_TO_TIME(TIMESTAMPDIFF(SECOND, (UTC_TIMESTAMP() - INTERVAL :waittime1 HOUR), `callbackdate`)) AS `nextvote`
										FROM `'.DBPRE.'votes`
										WHERE (`callbackdate` IS NULL
												OR (`callbackdate` > UTC_TIMESTAMP() - INTERVAL :waittime2 HOUR))
											AND `site` = :site
											AND (`user` = :user OR `ip` = :ip)
										ORDER BY `id` DESC LIMIT 0,1');
$prep['insvote'] = $db->prepare('INSERT INTO `'.DBPRE.'votes`
										   (`id`, `site`, `user`, `ip`, `opendate`, `callbackdate`, `callbackip`, `callbackdata`, `ready`, `fulfilled`)
									VALUES (NULL, :site, :user, :ip, UTC_TIMESTAMP(), null, \'\', \'\', 0, 0)');
$prep['votetimes'] = $db->prepare('SELECT `'.DBPRE.'votes`.`id`,
										`'.DBPRE.'votes`.`site`,
										`'.DBPRE.'sites`.`waittime`,
										`'.DBPRE.'votes`.`callbackdate`,
										`'.DBPRE.'votes`.`fulfilled`,
										(`callbackdate` > UTC_TIMESTAMP() - INTERVAL `waittime` HOUR) `outoftime`
									FROM `'.DBPRE.'votes`
									INNER JOIN
										(SELECT max(`id`) AS `id`, `site` FROM `'.DBPRE.'votes`
											WHERE (`user` = :user OR `ip` = :ip)
											GROUP BY `site` ORDER BY `callbackdate` DESC) `dest`
										ON `dest`.`id` = `'.DBPRE.'votes`.`id`
									INNER JOIN `'.DBPRE.'sites` ON `'.DBPRE.'sites`.`id` = `'.DBPRE.'votes`.`site`
									WHERE `callbackdate` IS NOT NULL AND ((`callbackdate` > UTC_TIMESTAMP() - INTERVAL `waittime` HOUR) OR `fulfilled` = 0) AND (`user` = :user2 OR `ip` = :ip2)');
$prep['setting'] = $db->prepare('SELECT * FROM `'.DBPRE.'preferences` WHERE `name` = :name');
$prep['usetting'] = $db->prepare('UPDATE `'.DBPRE.'preferences` SET `value` = :value WHERE `name` = :name');
$prep['phrase'] = $db->prepare('SELECT * FROM `'.DBPRE.'phrases` WHERE `name` = :name');
$prep['fireward'] = $db->prepare('UPDATE `'.DBPRE.'rewards` SET `fulfilled` = \'1\' WHERE `id` = :id');
$prep['fivote'] = $db->prepare('UPDATE `'.DBPRE.'votes` SET `fulfilled` = \'1\' WHERE `id` = :id');
$prep['rcvcall'] = $db->prepare('UPDATE `'.DBPRE.'votes` SET
										`callbackdate` = UTC_TIMESTAMP(),
										`ready` = true,
										`callbackip` = :cbip,
										`callbackdata` = :cbdata
									WHERE `id` = :id AND `ready` != 1');
$prep['uphrase'] = $db->prepare('UPDATE `'.DBPRE.'phrases` SET `value` = :value WHERE `name` = :name');
$prep['usite'] = $db->prepare('UPDATE `'.DBPRE.'sites`
										SET `name` = :name,
											`voteurl` = :url,
											`voteurlid` = :urlid,
											`waittime` = :wait,
											`active` = :active
										WHERE `id` = :id');
$prep['usite2'] = $db->prepare('UPDATE `'.DBPRE.'sites`
										SET `voteurl` = :url,
											`voteurlid` = :urlid,
											`waittime` = :wait,
											`active` = true
										WHERE `id` = :id');
$prep['insreward'] = $db->prepare('INSERT INTO `'.DBPRE.'rewards`
								(`user`, `ip`, `submitted`, `ready`, `fulfilled`, `incentive`)
								VALUES (:name, :ip, UTC_TIMESTAMP(), false, false, null)');
$prep['fulvote'] = $db->prepare('UPDATE `'.DBPRE.'votes` SET `fulfilled` = 1 WHERE `id` = :id');
$prep['upreward'] = $db->prepare('UPDATE `'.DBPRE.'rewards` SET `ready` = 1, `incentive` = :incentive WHERE `id` = :id');
$prep['updinc'] = $db->prepare('UPDATE `'.DBPRE.'incentives`
									SET `name` = :name,
										`amount` = :amount,
										`image` = :image,
										`active` = :active
									WHERE `id` = :id');
$prep['inssite'] = $db->prepare('INSERT INTO `'.DBPRE.'sites`
										(`name`, `voteurl`, `voteurlid`, `waittime`, `active`)
									VALUES (:name, :voteurl, :voteurlid, :waittime, :active)');
$prep['insinc'] = $db->prepare('INSERT INTO `'.DBPRE.'incentives`
										(`name`, `amount`, `image`, `active`)
									VALUES (:name, :amount, :image, :active)');
$prep['delsite'] = $db->prepare('DELETE FROM `'.DBPRE.'sites` WHERE `id` = :id');
$prep['delinc'] = $db->prepare('DELETE FROM `'.DBPRE.'incentives` WHERE `id` = :id');
$prep['inscaldata'] = $db->prepare('INSERT INTO `'.DBPRE.'callbacks`
											(`id`, `voteid`, `getdata`, `postdata`,
												`headers`, `auth`, `ip`, `date`)
									VALUES (null, :voteid, :getdata, :postdata,
												:headers, :auth, :ip, CURRENT_TIMESTAMP)');

if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
	// people can fake the header, but it's pointless, so let's just accept it
	$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
}

/*require('class-dbi.php');
$mvdb = new Database($mvdbuser, $mvdbpass, $mvdbhost, $mvdb);*/
$mvsetcache = array();
$mvphrcache = array();

define('MVERNUM', mv_setting('version'));
$mvsecurityhash = mv_setting('security_hash');
$mvadminpass = mv_setting('admin_pass');
$mvrewardtac = mv_setting('incentive_tactic') == 'reward';

function prep($name) {
	// let's save some lines of code.
	global $prep;
	return $prep[$name];
}

function mv_insert_cbdata($voteid, $getdata, $postdata, $headers, $auth, $ip) {
	$r = prep('inscaldata');
	return $r->execute(array(':voteid' => $voteid, ':getdata' => $getdata, ':postdata' => $postdata,
								':headers' => $headers, ':auth' => $auth, ':ip' => $ip));
}

function mv_delete_incentive($id) {
	$r = prep('delinc');
	return $r->execute(array(':id' => $id));
}

function mv_delete_site($id) {
	$r = prep('delsite');
	return $r->execute(array(':id' => $id));
}

function mv_insert_site($name, $voteurl, $voteurlid, $waittime, $active) {
	$r = prep('inssite');
	return $r->execute(array(':name' => $name, ':voteurl' => $voteurl, ':voteurlid' => $voteurlid, ':waittime' => $waittime, ':active' => $active));
}

function mv_insert_incentive($name, $amount, $image, $active) {
	$r = prep('insinc');
	return $r->execute(array(':name' => $name, ':amount' => $amount, ':image' => $image, ':active' => $active));
}

function mv_update_incentive($name, $amount, $image, $active, $id) {
	$r = prep('updinc');
	return $r->execute(array(':name' => $name, ':amount' => $amount, ':image' => $image, ':active' => $active, ':id' => $id));
}

function mv_unique_callbacks() {
	global $db;
	$r = $db->query('SELECT `callbackip`, COUNT(*) FROM `'.DBPRE.'votes` WHERE `callbackip` != \'\' GROUP BY `callbackip` ORDER BY COUNT(*) DESC');
	return $r->fetchAll();
}

function mv_update_reward($incentive, $id) {
	$st = prep('upreward');
	return $st->execute(array(':incentive' => $incentive, ':id' => $id));
}

function mv_insert_reward($name, $ip) {
	global $db;
	$st = prep('insreward');
	$st->execute(array(':name' => $name, ':ip' => $ip));
	return $db->lastInsertId();
}

function mv_fulfill_vote($id) {
	$st = prep('fulvote');
	return $st->execute(array(':id' => $id));
}

function mv_update_site($id, $name, $url, $urlid, $wait, $active) {
	global $db;
	$st = prep('usite');
	return $st->execute(array(':id' => $id, ':name' => $name, ':url' => $url, ':urlid' => $urlid, ':wait' => $wait, ':active' => $active));
}

function mv_update_site2($id, $url, $urlid, $wait) {
	global $db;
	$st = prep('usite2');
	return $st->execute(array(':id' => $id, ':url' => $url, ':urlid' => $urlid, ':wait' => $wait));
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

function mv_base_url() {
	return 'http://'.$_SERVER['HTTP_HOST'].BASE_DIR;
}

function mv_callback_url() {
	return mv_base_url().'/callback.php?auth=';
}

function mv_site($id) {
	$st = prep('site');
	$st->execute(array(':id' => $id));
	$val = $st->fetch();
	$st->closeCursor();
	return $val;
}

function mv_update_vote($id, $cbip, $cbdata) {
	global $db;
	$st = prep('rcvcall');
	return $st->execute(array(':id' => $id, ':cbip' => $cbip, ':cbdata' => $cbdata));
}

function mv_insert_vote($site, $user, $ip = '') {
	global $db;
	
	if ($ip == '') {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	
	$st = prep('insvote');
	$st->execute(array(':site' => $site, ':user' => $user, ':ip' => $ip));
	return $db->lastInsertId();
}

function mv_voted_within($time, $site, $user) {
	$st = prep('votelast');
	$st->execute(array(':waittime1' => $time, ':waittime2' => $time, ':site' => $site, ':user' => $user, ':ip' => $_SERVER['REMOTE_ADDR']));
	$val = $st->fetch();
	$st->closeCursor();
	return $val;
}

function mv_incentives($active = true) {
	$st = prep('incentives');
	$st->execute(array(':active' => $active));
	return $st->fetchAll();
}

function mv_reward_votes() {
	$st = prep('rewvotes');
	$st->execute(array(':user' => $_SESSION['user'], 'ip' => $_SERVER['REMOTE_ADDR'], ':user2' => $_SESSION['user'], 'ip2' => $_SERVER['REMOTE_ADDR']));
	return $st->fetchAll();
}

function mv_reward() {
	$st = prep('reward');
	$st->execute(array(':user' => $_SESSION['user'], 'ip' => $_SERVER['REMOTE_ADDR']));
	$val = $st->fetch();
	$st->closeCursor();
	return $val;
}

function mv_site_count($active = true) {
	$st = prep('sitecount');
	$st->execute(array(':active' => $active));
	$val = $st->fetchColumn();
	$st->closeCursor();
	return $val;
}

function mv_vote_times() {
	$st = prep('votetimes');
	$st->execute(array(':user' => $_SESSION['user'], 'ip' => $_SERVER['REMOTE_ADDR'],
						':user2' => $_SESSION['user'], 'ip2' => $_SERVER['REMOTE_ADDR']));
	return $st->fetchAll();
}

function mv_phrase($name) {
	global $mvphrcache;
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
		$st = prep('phrase');
		$st->execute(array(':name' => $name));
		$result = $st->fetch();
		$st->closeCursor();
		$value = $result['value'];
	}
	
	$index = 1;
	
	foreach ($arguments as $argval) {
		$value = str_replace('{'.($index++).'}', $argval, $value);
	}
	
	return $value;
}

function mv_phrases() {
	global $db;
	$st = $db->query('SELECT * FROM `'.DBPRE.'phrases`');
	return $st->fetchAll();
}

function mv_setbool($name) {
	return mv_setting($name) == '1';
}

function mv_update_setting($name, $value) {
	$st = prep('usetting');
	return $st->execute(array(':value' => $value, ':name' => $name));
}

function mv_update_phrase($name, $value) {
	$st = prep('uphrase');
	return $st->execute(array(':value' => $value, ':name' => $name));
}

function mv_vote_sites() {
	global $db;
	$st = $db->query('SELECT * FROM `'.DBPRE.'sites` WHERE `active` = 1 OR `id` = 1');
	return $st->fetchAll();
}

function mv_sites() {
	global $db;
	$st = $db->query('SELECT * FROM `'.DBPRE.'sites`');
	return $st->fetchAll();
}

function mv_setting($name) {
	global $mvsetcache;
	
	if (array_key_exists($name, $mvsetcache) && !empty($mvsetcache[$name])) {
		return $mvsetcache[$name];
	}
	
	$st = prep('setting');
	$st->execute(array(':name' => $name));
	$result = $st->fetch();
	$st->closeCursor();
	$mvsetcache[$name] = $result['value'];
	return $result['value'];
}

function mv_setting_full($name) {
	$st = prep('setting');
	$st->execute(array(':name' => $name));
	$result = $st->fetch();
	$st->closeCursor();
	return $result;
}

function mv_settings_visible() {
	global $db;
	$st = $db->query('SELECT * FROM `'.DBPRE.'preferences` WHERE `visible` = 1');
	return $st->fetchAll();
}

function mv_settings() {
	global $db;
	$st = $db->query('SELECT * FROM `'.DBPRE.'preferences`');
	return $st->fetchAll();
}

function mv_incentives_all() {
	global $db;
	$st = $db->query('SELECT * FROM `'.DBPRE.'incentives`');
	return $st->fetchAll();
}

function mv_finalize_vote($id) {
	$st = prep('fivote');
	return $st->execute(array(':id' => $id));
}

function mv_finalize_reward($id) {
	$st = prep('fireward');
	return $st->execute(array(':id' => $id));
}

function mv_pending_rewards() {
	global $db;
	$st = $db->query('SELECT `r`.`id`, `r`.`incentive`, `r`.`user`, `r`.`ip`, `i`.`name`, `i`.`amount` FROM `'.DBPRE.'rewards` `r`
						INNER JOIN
							(SELECT * FROM `'.DBPRE.'incentives`) `i`
							ON `r`.`incentive` = `i`.`id`
						WHERE `ready` = 1 AND `fulfilled` = 0');
	return $st->fetchAll();
}

function mv_pending_votes() {
	global $db;
	$st = $db->query('SELECT `id`, `site`, `user`, `ip` FROM `'.DBPRE.'votes` WHERE `ready` = 1 AND `fulfilled` = 0');
	return $st->fetchAll();
}

function createCall($function, $prependArguments, $arguments) {
	$args = array_merge((array)$prependArguments, (array)$arguments);
	return @call_user_func_array(array($this, $function), $args);
}
?>