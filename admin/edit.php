<?php
require('../config.php');
require('../init.php');

if (!isset($_SESSION['admin']) || $_SESSION['admin'] != $mvadminpass) {
	unset($_SESSION['admin']);
	die('Not logged in.');
}

if (isset($_GET['action'])) {
	$action = $_GET['action'];
	$type = $_GET['target'];
	
	if ($action == 'update') {
		if ($type == 'settings') {
			//var_dump($_POST);
			
			foreach ($_POST as $k => $v) {
				$set = mv_setting_full($k);
				
				if ($set['values'] == 'md5') {
					if (empty($v)) {
						continue;
					}
					
					$v = md5($v);
				}
				
				mv_update_setting($k, $v);
			}
			
			echo('Update successful!');
		}
		else if ($type == 'phrases') {
			mv_update_phrase($_GET['name'], $_GET['value']);
			echo('Update successful!');
		}
		else if ($type == 'sites') {
			$active = false;
			
			if (isset($_POST['active']) && $_POST['active'] == 'on') {
				$active = true;
			}
			
			if (intval($_POST['id']) != 1) {
				mv_update_site($_POST['id'], $_POST['name'], $_POST['voteurl'], $_POST['voteurlid'], $_POST['waittime'], $active);
				echo('Update successful!');
			}
			else {
				if (strpos($_POST['voteurl'], 'rspserver.com') !== false) {
					mv_update_site2($_POST['id'], $_POST['voteurl'], $_POST['voteurlid'], $_POST['waittime']);
					echo('Update required site successful!');
				}
				else {
					echo('Invalid URL for site.');
				}
			}
		}
		else if ($type == 'incentives') {
			$active = false;
			
			if (isset($_POST['active']) && $_POST['active'] == 'on') {
				$active = true;
			}
			
			$mvdb->escapedQuery("UPDATE `".DBPRE."incentives`
									SET `name` = '%1:s',
										`amount` = %2:d,
										`image` = '%3:s',
										`active` = %4:d
									WHERE `id` = %5:d",
										$_POST['name'],
										intval($_POST['amount']),
										$_POST['image'],
										$active,
										intval($_POST['id']));
			echo('Update successful!');
		}
		else {
			print_r($_GET);
			print_r($_POST);
		}
	}
	else if ($action == 'create') {
		if ($type == 'sites') {
			if (empty($_POST['name']) || empty($_POST['voteurl'])
					|| empty($_POST['voteurlid']) || empty($_POST['waittime'])) {
				echo('Please enter all fields and try again.');
			}
			else {
				$active = false;
				
				if (isset($_POST['active']) && $_POST['active'] == 'on') {
					$active = true;
				}
				
				$mvdb->escapedQuery("INSERT INTO `".DBPRE."sites`
										(`name`, `voteurl`, `voteurlid`, `waittime`, `active`)
									VALUES ('%1s', '%2:s', '%3:s', %4:d, %5:d)",
										$_POST['name'], $_POST['voteurl'], $_POST['voteurlid'], intval($_POST['waittime']), $active);
				echo('Insert successful!');
			}
		}
		else if ($type == 'incentives') {
			if (empty($_POST['name']) || empty($_POST['amount']) || empty($_POST['image'])) {
				echo('Please enter all fields and try again.');
			}
			else {
				$active = false;
				
				if (isset($_POST['active']) && $_POST['active'] == 'on') {
					$active = true;
				}
				
				$mvdb->escapedQuery("INSERT INTO `".DBPRE."incentives`
										(`name`, `amount`, `image`, `active`)
									VALUES ('%1s', %2:d, '%3:s', %4:d)",
										$_POST['name'], intval($_POST['amount']), $_POST['image'], $active);
				echo('Insert successful!');
			}
		}
		else {
			print_r($_GET);
			print_r($_POST);
		}
	}
	else if ($action == 'delete') {
		if ($type == 'sites') {
			if (intval($_POST['id']) != 1) {
				$mvdb->escapedQuery("DELETE FROM `".DBPRE."sites` WHERE `id` = %1:d", intval($_POST['id']));
				echo('Site deleted!');
			}
			else {
				echo('Can not delete site.');
			}
		}
		else if ($type == 'incentives') {
			$mvdb->escapedQuery("DELETE FROM `".DBPRE."incentives` WHERE `id` = %1:d", intval($_POST['id']));
			echo('Incentive deleted!');
		}
	}
}
?>