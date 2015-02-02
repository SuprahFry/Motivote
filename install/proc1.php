<?php
if (!empty($_POST['mvdbhost']) && !empty($_POST['mvdbuser'])
		&& !empty($_POST['mvdbpass']) && !empty($_POST['mvdb'])) {
	try {
		$db = new PDO('mysql:host='.$_POST['mvdbhost'].';dbname='.$_POST['mvdb'], $_POST['mvdbuser'], $_POST['mvdbpass']);
		
		if ($db === false) {
			$failedstep = true;
			$error[] = 'Failed to connect to database using supplied information. Double check information and try again.';
		}
	}
	catch (PDOException $e) {
		$failedstep = true;
		$error[] = 'Failed to connect to database using supplied information. Double check information and try again.';
	}
}
else if (isset($_POST['s'])) {
	$failedstep = true;
	$error[] = 'Please enter all information to proceed.';
}
else {
	$failedstep = true;
}

if (!$failedstep) {
	$curstep = $_SESSION['instep'] = $curstep + 1; // increment and set session
	
	$installcode = '<?php
define(\'DBPRE\', \''.$_POST['dbpre'].'\');
$mvdbuser = \''.$_POST['mvdbuser'].'\';
$mvdbpass = \''.$_POST['mvdbpass'].'\';
$mvdb = \''.$_POST['mvdb'].'\';
$mvdbhost = \''.$_POST['mvdbhost'].'\';
?>';
	$fp = fopen('../config.php', 'wb');
	fwrite($fp, $installcode);
	fclose($fp);
}
?>