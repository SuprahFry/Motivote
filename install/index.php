<?php
define('MVINUM', 1.4);
include('../init.php');

$requirements = array(
	1.3 => array(
		'version' => array(
			'PHP' => 5.3
		),
		'feature' => array(
			'short_open_tag' => true
		),
		'modules' => array(
			'session',
			'pdo_mysql'
		),
		'writable' => array(
			'config.php'
		)
	)
);
$steps = array('Server Requirements', 'Database Settings', 'Installation', 'Vote Settings', 'Completed');
$runtype = 0;
$error = array();
$curstep = empty($_SESSION['instep']) ? 0 : $_SESSION['instep'];
$cureq = $requirements[MVINUM];
$failedstep = false;
$m = 0;

if (defined('MVERNUM')) {
	$m = MVERNUM;
}

if (defined('MVERNUM') && !empty($m) && MVERNUM == MVINUM && !isset($_SESSION['instep'])) {
	header('Location: '.mv_base_url().'/index.php');
	die();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Motivote <?php echo(MVINUM); ?> Setup</title>
		<link rel="stylesheet" href="<?php echo(mv_base_url()); ?>/install/style.css" type="text/css" media="screen" charset="utf-8" />
		<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700|Cabin" type="text/css" />
	</head>
	<body>
		<div id="outercontainer">
			<div id="container">
				<div id="sethead">
					<img src="<?php echo(mv_base_url()); ?>/admin/images/logo.png" />
					<div id="setheadtext">
						<?php
						if ($mvconnect && defined('MVERNUM') && !empty($m) && MVINUM > MVERNUM) {
							$runtype = 1;
							echo('Updating Motivote from version '.MVERNUM.' to version '.MVINUM);
						}
						else if (defined('MVERNUM') && !empty($m)) {
							$runtype = 3;
							echo('Motivote '.MVERNUM.' is installed. This script can be deleted or ignored.');
						}
						else {
							$runtype = 2;
							echo('Installing Motivote version '.MVINUM);
						}
						?>
					</div>
				</div>
			</div>
			<div id="content">
				<ul id="steps">
					<?php
					include('proc'.$curstep.'.php');
					
					for ($i = 0; $i < count($steps); $i++) {
						$step = $steps[$i];
						$mod = 'next';
						
						if ($curstep == $i) {
							$mod = 'current';
						}
						else if ($curstep > $i) {
							$mod = 'completed';
						}
						
						if ($curstep == count($steps) - 1) {
							$mod = 'completed';
						}
						
						echo('<li class="'.$mod.'">'.$step.'</li>');
					}
					?>
				</ul>
				<div id="area">
					<form method="post" name="install">
						<p style="margin-top: 0;"><strong>Step <?php echo($curstep + 1) ?> out of <?php echo(count($steps)); ?></strong> - <?php echo($steps[$curstep]); ?></p>
						<?php
						foreach ($error as $e) {
							echo('<div class="fail">'.$e.'</div>');
						}
						
						include('step'.$curstep.'.php');
						
						if ($curstep < count($steps) - 1) {
						?>
						<div id="navi"><input type="submit" name="s" value="Next" /></div>
						<?php } ?>
					</form>
				</div>
				<div style="clear: both;"></div>
			</div>
		</div>
		<div id="footer">
			Motivote &copy; 2015 <a href="http://rspserver.com/">RSPServer.com</a> - Setup Script v<?php echo(MVINUM); ?>
		</div>
	</body>
</html>