<?php
require('config.php');
require('init.php');

$out = null;

if (empty($_GET['do'])) {
	$out['error'][] = 'No action specified.';
}

if (empty($_GET['key']) || mv_setting('datakey') != $_GET['key']) {
	$out['error'][] = 'Invalid security key';
}

if (empty($out['error'])) {
	if ($_GET['do'] == 'pending') {
		$out['votes'] = $mvdb->escapedAllResultsAssoc("SELECT `id`, `site`, `user`, `ip` FROM `".DBPRE."votes` WHERE `ready` = 1 AND `fulfilled` = 0");
		$out['rewards'] = $mvdb->escapedAllResultsAssoc("SELECT `r`.`id`, `r`.`incentive`, `r`.`user`, `r`.`ip`, `i`.`name`, `i`.`amount` FROM `".DBPRE."rewards` `r`
														INNER JOIN
															(SELECT * FROM `".DBPRE."incentives`) `i`
															ON `r`.`incentive` = `i`.`id`
														WHERE `ready` = 1 AND `fulfilled` = 0");
		$out['tactic'] = mv_setting('incentive_tactic');
		$out['reward'] = $out['tactic'] == 'reward';
	}
	else if ($_GET['do'] == 'finalize' && !empty($_GET['ids']) && !empty($_GET['type'])) {
		$ids = explode(',', $_GET['ids']);
		$type = $_GET['type'] == 'rewards' ? 'rewards' : 'votes';
		
		if ($type == 'rewards') {
			foreach ($ids as $id) {
				$mvdb->escapedQuery("UPDATE `".DBPRE."rewards` SET `fulfilled` = '1' WHERE `id` = %1:d", intval($id));
			}
		}
		else {
			foreach ($ids as $id) {
				$mvdb->escapedQuery("UPDATE `".DBPRE."votes` SET `fulfilled` = '1' WHERE `id` = %1:d", intval($id));
			}
		}
		
		die('success');
	}
}

echo(json_encode($out));
?>