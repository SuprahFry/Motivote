<?php
if (!empty($_POST['server_name']) && !empty($_POST['incentive_tactic'])
		&& !empty($_POST['admin_pass'])) {
	mv_update_setting('server_name', $_POST['server_name']);
	mv_update_setting('incentive_tactic', $_POST['incentive_tactic']);
	mv_update_setting('admin_pass', md5($_POST['admin_pass']));
	mv_update_setting('security_hash', substr(md5(rand()), 0, 8));
	mv_update_setting('datakey', substr(md5(rand()), 0, 8));
	$_SESSION['admin'] = $_POST['admin_pass'];
}
else if (isset($_POST['s'])) {
	//mv_update_setting
	$failedstep = true;
	$error[] = 'Please enter all information to proceed.';
}
else {
	$failedstep = true;
}

if (!$failedstep) {
	$curstep = $_SESSION['instep'] = $curstep + 1; // increment and set session
}
?>