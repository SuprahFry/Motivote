<?php
require('../config.php');
require('../init.php');

if (isset($_POST['pass'])) {
	if (md5($_POST['pass']) == $mvadminpass) {
		$_SESSION['admin'] = $mvadminpass;
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo(mv_phrase('admin_title', mv_setting('server_name'))); ?></title>
		<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<style>
			html, body {
				height: 100%;
				width: 100%;
				padding: 0;
				margin: 0;
			}
			a:visited {
				color: blue;
			}
			#nav {
				padding: 0;
				margin: 0;
				list-style: none;
				display: inline-block;
				float: left;
				border-right: 1px solid #000000;
				border-bottom: 1px solid #000000;
			}
			#nav li {
				padding: 7px 10px;
			}
			#content {
				display: inline-block;
				padding: 7px 10px;
			}
		</style>
	</head>
	<body>
		<?php
		if (!isset($_SESSION['admin']) || $_SESSION['admin'] != $mvadminpass):
			unset($_SESSION['admin']);
			?>
			<form action="index.php" method="post">
				Password: <input type="password" name="pass" /> <input type="submit" value="Log In" />
			</form>
			<?php
		else:
		?>
		<ul id="nav">
			<li><a href="#">Dashboard</a></li>
			<li><a href="#">Themes</a></li>
			<li><a href="#">Sites</a></li>
			<li><a href="#">Incentives</a></li>
			<li><a href="#">Preferences</a></li>
			<li><a href="#">Phrases</a></li>
		</ul>
		<div id="content">
			
		</div>
		<?php endif; ?>
	</body>
</html>