<?php
session_start();
if (file_exists('config.php') && empty($_SESSION['installstep'])) {
	include('config.php');
	
	if (isset($mvdbpass)) {
		die('Motivote Already Installed. Feel free to delete install.php from your server.');
		//header('Location: index.php');
	}
}
if ($_SESSION['installstep'] == 1) {
	if (file_exists('config.php')) {
		include('config.php');
		if (isset($mvdbpass)) {
			$_SESSION['installstep'] = 2;
		}
	}
}
if ($_SESSION['installstep'] >= 2) {
	require('config.php');
	require('class-dbi.php');
	$mvdb = new Database($mvdbuser, $mvdbpass, $mvdbhost, $mvdb);
}
if ((empty($_SESSION['installstep']) || $_SESSION['installstep'] == 3) && isset($_POST['svname'])) {
	if (empty($_POST['svname']) || empty($_POST['tactic']) || empty($_POST['adpass']) || empty($_POST['sechash']) || empty($_POST['datakey']) || empty($_POST['rsrvid'])) {
		$error[] = 'Please enter all fields marked with \'*\' and submit again.';
	}
	
	if (empty($error)) {
		$mvdb->escapedQuery("UPDATE `".DBPRE."preferences` SET `value` = '%1:s' WHERE `name` = '%2:s'", $_POST['svname'], 'server_name');
		$mvdb->escapedQuery("UPDATE `".DBPRE."preferences` SET `value` = '%1:s' WHERE `name` = '%2:s'", $_POST['tactic'], 'incentive_tactic');
		$mvdb->escapedQuery("UPDATE `".DBPRE."preferences` SET `value` = '%1:s' WHERE `name` = '%2:s'", md5($_POST['adpass']), 'admin_pass');
		$mvdb->escapedQuery("UPDATE `".DBPRE."preferences` SET `value` = '%1:s' WHERE `name` = '%2:s'", $_POST['sechash'], 'security_hash');
		$mvdb->escapedQuery("INSERT INTO `".DBPRE."preferences` (`id`, `name`, `value`) VALUES (NULL, 'datakey', '%1:s')", $_POST['datakey']);
		$mvdb->escapedQuery("UPDATE `".DBPRE."preferences` SET `value` = '%1:s' WHERE `name` = '%2:s'", isset($_POST['logcallback']) ? '1' : '0', 'log_callback');
		$mvdb->escapedQuery("UPDATE `".DBPRE."sites` SET `voteurlid` = '%1:s' WHERE `id` = '%2:s'", $_POST['rsrvid'], 1);
		//@unlink('install.php');
		$_SESSION['installstep'] = 4;
		//header('Location: admin/index.php');
	}
}
?>
<html>
	<head>
		<title>Install Motivote v1</title>
		<style>
			#center {
				margin: 0 auto;
				text-align: center;
				width: 500px;
			}
			table {
				display: inline-block;
			}
			.error {
				color: red;
				font-weight: bold;
			}
		</style>
	</head>
	<body>
		<div id="center">
			<form action="install.php" method="post">
			<?php
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);
			
			if ((empty($_SESSION['installstep']) || $_SESSION['installstep'] == 0) && !empty($_POST)) {
				// step 1 being submitted
				
				if (empty($_POST['sqname']) || empty($_POST['sqpass']) || empty($_POST['dbname']) || empty($_POST['dbhost'])) {
					$error[] = 'Please enter all fields marked with \'*\' and submit again.';
				}
				
				if (empty($error)) {
					if(@mysql_connect($_POST['dbhost'],$_POST['sqname'],$_POST['sqpass']) === false) {
						$error[] = 'Error connecting to MySQL: '.mysql_error();
					}
					
					if (empty($error)) {
						if (!@mysql_select_db($_POST['dbname'])) {
							$error[] = 'Connected but error with database: '.mysql_error();
						}
						
						if (empty($error)) {
							$_SESSION['installconfig'] = '<?php
define(\'DBPRE\', \''.$_POST['dbpre'].'\');
define(\'MVERNUM\', 1.2);
$mvdbuser = \''.$_POST['sqname'].'\';
$mvdbpass = \''.$_POST['sqpass'].'\';
$mvdb = \''.$_POST['dbname'].'\';
$mvdbhost = \''.$_POST['dbhost'].'\';
?>';
							$_SESSION['sqlinstall'] = $_POST;
							$_SESSION['installstep'] = 1;
						}
					}
				}
			}
			
			if (!empty($error)) {
				echo('<div class="error">');
				
				foreach ($error as $e) {
					echo($e.'<br />');
				}
				
				echo('</div>');
			}
			
			if (empty($_SESSION['installstep']) || $_SESSION['installstep'] == 0) {
			?>
			<h3>Motivote - Database Configuration</h3>
			<table>
				<tr>
					<td>MySQL User Name*</td>
					<td><input type="text" name="sqname" value="" /></td>
				</tr>
				<tr>
					<td>MySQL Password*</td>
					<td><input type="text" name="sqpass" value="" /></td>
				</tr>
				<tr>
					<td>MySQL Database Name*</td>
					<td><input type="text" name="dbname" value="" /></td>
				</tr>
				<tr>
					<td>MySQL Database Host*</td>
					<td><input type="text" name="dbhost" value="localhost" /></td>
				</tr>
				<tr>
					<td>MySQL Table Prefix</td>
					<td><input type="text" name="dbpre" value="mv_" /></td>
				</tr>
			</table><br />
			<small>* denotes a required field</small>
			<?php
			}
			else if ($_SESSION['installstep'] == 1) {
			?>
			<h3>Motivote - Write Configuration</h3>
			<?php
				if (!is_writable('config.php')) {
				?>
					<p>It appears that your config.php can not be written to. In this case, you must manually change your config.php to the contents listed below. After you have done so, click next.<br /><br />
					<textarea rows='5' cols='50' onclick='this.select();'><?php echo($_SESSION['installconfig']); ?></textarea>
				<?php
				}
				else {
					$fp = fopen('config.php', 'wb');
					fwrite($fp, $_SESSION['installconfig']);
					fclose($fp);
					if (!@chmod('config.php', 0666))
						echo('Could not change file permissions for config.php, you must CHMOD 0666 config.php manually.<br />');
					echo('Configuration written. Click next to continue.');
					$_SESSION['installstep'] = 2;
				}
			}
			else if ($_SESSION['installstep'] == 2) {
			?>
			<h3>Motivote - Creating database</h3>
			<?php
				$db[] = "CREATE TABLE IF NOT EXISTS `".$_SESSION['sqlinstall']['dbpre']."incentives` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `name` varchar(50) COLLATE latin1_general_ci NOT NULL,
						  `amount` int(11) NOT NULL,
						  `image` varchar(25) COLLATE latin1_general_ci NOT NULL,
						  `active` tinyint(1) NOT NULL,
						  PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=2 ;";
				$db[] = "INSERT INTO `".$_SESSION['sqlinstall']['dbpre']."incentives` (`id`, `name`, `amount`, `image`, `active`) VALUES
						(1, 'Gold', 1000000, 'Coins_10000.png', 1);";
				$db[] = "CREATE TABLE IF NOT EXISTS `".$_SESSION['sqlinstall']['dbpre']."phrases` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `name` varchar(255) COLLATE latin1_general_ci NOT NULL,
						  `value` text COLLATE latin1_general_ci NOT NULL,
						  PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=7 ;";
				$db[] = "INSERT INTO `".$_SESSION['sqlinstall']['dbpre']."phrases` (`id`, `name`, `value`) VALUES
						(1, 'header', '{1} Voting'),
						(2, 'section_header', 'Why should you vote for us?'),
						(3, 'section_body', 'If you enjoy this server, you should vote every time you can to help keep it alive. Voting comes with many rewards, and you can vote twice a day. Help support us and we''ll help support you!'),
						(4, 'title', '{1} - {2}'),
						(6, 'powered_by', 'Powered by Motivote v{1}');";
				$db[] = "CREATE TABLE IF NOT EXISTS `".$_SESSION['sqlinstall']['dbpre']."preferences` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `name` varchar(255) COLLATE latin1_general_ci NOT NULL,
						  `value` text COLLATE latin1_general_ci NOT NULL,
						  PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=7 ;";
				$db[] = "INSERT INTO `".$_SESSION['sqlinstall']['dbpre']."preferences` (`id`, `name`, `value`) VALUES
						(1, 'server_name', 'Motivote'),
						(2, 'selected_theme', 'default'),
						(3, 'incentive_tactic', 'reward'),
						(4, 'security_hash', 'ubersuperswag'),
						(5, 'log_callback', '1'),
						(6, 'admin_pass', '098f6bcd4621d373cade4e832627b4f6');";
				$db[] = "CREATE TABLE IF NOT EXISTS `".$_SESSION['sqlinstall']['dbpre']."rewards` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `user` varchar(25) COLLATE latin1_general_ci NOT NULL,
						  `ip` varchar(64) COLLATE latin1_general_ci NOT NULL,
						  `ready` tinyint(1) NOT NULL,
						  `fulfilled` tinyint(1) NOT NULL,
						  `submitted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
						  `incentive` int(11) DEFAULT NULL,
						  PRIMARY KEY (`id`),
						  KEY `incentive` (`incentive`)
						) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;";
				$db[] = "CREATE TABLE IF NOT EXISTS `".$_SESSION['sqlinstall']['dbpre']."sites` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `name` varchar(25) COLLATE latin1_general_ci NOT NULL,
						  `voteurl` text COLLATE latin1_general_ci NOT NULL,
						  `voteurlid` text COLLATE latin1_general_ci NOT NULL,
						  `waittime` int(11) NOT NULL DEFAULT '24',
						  `active` tinyint(1) NOT NULL,
						  PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=8 ;";
				$db[] = "INSERT INTO `".$_SESSION['sqlinstall']['dbpre']."sites` (`id`, `name`, `voteurl`, `voteurlid`, `waittime`, `active`) VALUES
						(1, 'RSPServer', 'http://www.rspserver.com/in.php?id={id}&incentive={incentive}', '1', 12, 1),
						(2, 'Rune-Server', 'http://rune-server.org/toplist.php?do=vote&sid={id}&incentive={incentive}', '0', 12, 1),
						(3, 'Rune-Script', 'http://rune-script.com/toplist.php?action=vote&id={id}&incentive={incentive}', '172', 12, 1),
						(4, 'Top100Arena', 'http://www.top100arena.com/in.asp?id={id}&incentive={incentive}', '86126', 24, 1),
						(5, 'TopG', 'http://topg.org/Runescape/in-{id}-{incentive}', '394863', 12, 1),
						(6, 'RuneToplist', 'http://www.runetoplist.com/servers/{id}/vote?i={incentive}', 'sysfly', 12, 1),
						(7, 'RuneLocus', 'http://www.runelocus.com/toplist/index.php?action=vote&id={id}&id2={incentive}', '40045', 12, 1);";
				$db[] = "CREATE TABLE IF NOT EXISTS `".$_SESSION['sqlinstall']['dbpre']."votes` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `site` int(11) NOT NULL,
						  `user` varchar(25) COLLATE latin1_general_ci NOT NULL,
						  `ip` varchar(60) COLLATE latin1_general_ci NOT NULL,
						  `opendate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
						  `callbackdate` timestamp NULL DEFAULT NULL,
						  `callbackip` varchar(64) COLLATE latin1_general_ci NOT NULL,
						  `callbackdata` text COLLATE latin1_general_ci NOT NULL,
						  `ready` tinyint(1) NOT NULL DEFAULT '0',
						  `fulfilled` tinyint(1) NOT NULL DEFAULT '0',
						  PRIMARY KEY (`id`),
						  KEY `site` (`site`)
						) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;";
				$db[] = "ALTER TABLE `mv_rewards` ADD CONSTRAINT `mv_rewards_ibfk_1` FOREIGN KEY (`incentive`) REFERENCES `mv_incentives` (`id`) ON UPDATE CASCADE;";
				$db[] = "ALTER TABLE `mv_votes` ADD CONSTRAINT `mv_votes_ibfk_1` FOREIGN KEY (`site`) REFERENCES `mv_sites` (`id`) ON UPDATE CASCADE;";
				
				//var_dump($mvdb);
				echo($mvdb->lastError());
				$e = 0;
				foreach ($db as $d) {
					$mvdb->escapedQuery($d);
					echo($mvdb->lastError().'<br />');
					$e++;
				}
				
				echo($e.' queries run, click next to continue.');
				$_SESSION['installstep'] = 3;
			}
			else if ($_SESSION['installstep'] == 3) {
			?>
			<h3>Motivote - Preferences</h3>
			<table>
				<tr>
					<td>Server Name*</td>
					<td><input type="text" name="svname" value="Motivote" /></td>
				</tr>
				<tr>
					<td>Incentive Tactic*</td>
					<td>
						<select name="tactic">
							<option value="reward" selected>Reward for all votes</option>
							<option value="vote">Reward for each vote</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Log Callbacks to File</td>
					<td>
						<input type="checkbox" name="logcallback" checked="checked" />
					</td>
				</tr>
				<tr>
					<td>Security Hash (best left random)*</td>
					<td><input type="text" name="sechash" value="<?php echo(substr(md5(rand()), 0, 10)); ?>" /></td>
				</tr>
				<tr>
					<td>Server Data Callback Key (best left random)*</td>
					<td><input type="text" name="datakey" value="<?php echo(substr(md5(rand()), 0, 10)); ?>" /><br />
					<span style="color: red; font-weight: bold;">SAVE THIS FOR SERVER-SIDE SETUP</span></td>
				</tr>
				<tr>
					<td>Admin Password (MAKE SURE YOU COPY THIS!)*</td>
					<td><input type="text" name="adpass" value="<?php echo(substr(md5(rand()), 0, 6)); ?>" /><br />
					<span style="color: red; font-weight: bold;">SAVE THIS FOR WEBSITE-SIDE SETUP</span></td>
				</tr>
				<tr>
					<td style="vertical-align: top;"><a href="http://rspserver.com/">RSPServer.com</a> Site ID*</td>
					<td><input type="text" name="rsrvid" /><br />
					View your server's info page and your ID is in the URL.<br />Example: <em>http://rspserver.com/info.php?id=<strong>1</strong></e><br /><u>JUST THE NUMBER.</u></td>
				</tr>
			</table><br />
			<small>* denotes a required field</small>
			<?php
			}
			else if ($_SESSION['installstep'] == 4) {
				if (!is_writable('callback.log')) {
					if (!@chmod('callback.log', 0777))
						echo('Could not make callback.log writeable. You must "CHMOD 0777 callback.log" manually.');
				}
			?>
			<h3>Motivote - Install complete!</h3>
			<p>Data shown below should be saved for your records.</p>
			<table>
				<tr>
					<td>Server Data Callback Key</td>
					<td><?php echo($_POST['datakey']); ?><br />
					<span style="color: red; font-weight: bold;">SAVE THIS FOR SERVER-SIDE SETUP</span></td>
				</tr>
				<tr>
					<td>Admin Control Panel Password</td>
					<td><?php echo($_POST['adpass']); ?><br />
					<span style="color: red; font-weight: bold;">SAVE THIS FOR WEBSITE-SIDE SETUP</span></td>
				</tr>
			</table><br /><br />
			<a href="admin/index.php" target="_blank">CLICK HERE TO GO TO ADMIN CONTROL PANEL</a>
			<?php
				session_destroy();
			}
			if (empty($_SESSION['installstep']) || $_SESSION['installstep'] != 4) {
			?>
			<br /><br /><input type="submit" value="Next" />
			<?php
			}
			?>
			</form>
		</div>
	</body>
</html>