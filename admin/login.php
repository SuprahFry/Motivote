<?php
require('../config.php');
require('../init.php');

if (isset($_POST['password'])) {
	if (md5($_POST['password']) == $mvadminpass) {
		$_SESSION['admin'] = $mvadminpass;
		header('Location: index.php');
		die();
	}
}

$nosidebar = true;
$nointeract = true;
include('includes/header.php');
?>
<div id="login">
	<form action="login.php" method="post">
		<div class="inputrow"><span class="left">Password:</span><input type="password" class="right" name="password" /></div>
		<br /><br /><br /><input type="submit" value="Log In" />
	</form>
</div>
<?php
include('includes/footer.php');
?>