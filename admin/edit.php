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
			$mvdb->escapedQuery("UPDATE `".DBPRE."preferences` SET `value` = '%1:s' WHERE `name` = '%2:s'", $_GET['value'], $_GET['name']);
			echo('Update successful!');
		}
		else if ($type == 'phrases') {
			$mvdb->escapedQuery("UPDATE `".DBPRE."phrases` SET `value` = '%1:s' WHERE `name` = '%2:s'", $_GET['value'], $_GET['name']);
			echo('Update successful!');
		}
		else if ($type == 'sites') {
			$active = false;
			
			if (isset($_POST['active']) && $_POST['active'] == 'on') {
				$active = true;
			}
			
			if (intval($_POST['id']) != 1) {
				$mvdb->escapedQuery("UPDATE `".DBPRE."sites`
										SET `name` = '%1:s',
											`voteurl` = '%2:s',
											`voteurlid` = '%3:s',
											`waittime` = %4:d,
											`active` = %5:d
										WHERE `id` = %6:d",
											$_POST['name'],
											$_POST['voteurl'],
											$_POST['voteurlid'],
											intval($_POST['waittime']),
											$active,
											intval($_POST['id']));
				echo('Update successful!');
			}
			else {
				if (strpos($_POST['voteurl'], 'rspserver.com') !== false) {
					$mvdb->escapedQuery("UPDATE `".DBPRE."sites`
											SET `voteurl` = '%1:s',
												`voteurlid` = '%2:s',
												`waittime` = %3:d,
												`active` = true
											WHERE `id` = %4:d",
												$_POST['voteurl'],
												$_POST['voteurlid'],
												intval($_POST['waittime']),
												intval($_POST['id']));
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