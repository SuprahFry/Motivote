<?php
if (isset($_POST['failedstep'])) {
	$failedstep = $_POST['failedstep'] == 1;
	
	if (!$failedstep) {
		$curstep = $_SESSION['instep'] = $curstep + 1; // increment and set session
	}
}
?>