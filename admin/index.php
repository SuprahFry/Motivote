<?php
require('../config.php');
require('../init.php');

$board = null;
$baseboard = 'board/';
$validboards = array('dashboard', 'settings', 'incentives', 'phrases', 'sites', 'system', 'themes', 'emulate');

if (isset($_GET['board'])) {
	$board = realpath($baseboard.$_GET['board'].'.php');
	
	if (!in_array($_GET['board'], $validboards)) {
		header('HTTP/1.1 404 Not Found');
		die('The requested file could not be found');
	}
	
	/*if (strpos($board, $baseboard) === false) {
		header('HTTP/1.1 404 Not Found');
		die('The requested file could not be found');
	}*/
}

if (!isset($_SESSION['admin']) || $_SESSION['admin'] != $mvadminpass || (isset($_GET['do']) && $_GET['do'] == 'logout')) {
	unset($_SESSION['admin']);
	setcookie('loginflow', '', 0);
	
	if ($board != null) {
		echo('<meta http-equiv="refresh" content="0; url=login.php" />');
	}
	else {
		header('Location: login.php');
	}
	
	die();
}
else if ($board != null) {
	include($board);
	die();
}

include('includes/header.php');
?>
<div id="ajaxcontent">&nbsp;</div>
<?php
include('includes/footer.php');
?>