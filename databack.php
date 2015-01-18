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
		$out['votes'] = mv_pending_votes();
		$out['rewards'] = mv_pending_rewards();
		$out['tactic'] = mv_setting('incentive_tactic');
		$out['reward'] = $out['tactic'] == 'reward';
	}
	else if ($_GET['do'] == 'finalize' && !empty($_GET['ids']) && !empty($_GET['type'])) {
		$ids = explode(',', $_GET['ids']);
		$type = $_GET['type'] == 'rewards' ? 'rewards' : 'votes';
		
		if ($type == 'rewards') {
			foreach ($ids as $id) {
				mv_finalize_reward(intval($id));
			}
		}
		else {
			foreach ($ids as $id) {
				mv_finalize_vote(intval($id));
			}
		}
		
		die('success');
	}
}

echo(json_encode($out));
?>