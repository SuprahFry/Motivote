<?php
if (isset($_POST['s'])) {
	// do install
	$qdb = new PDO('sqlite:mvq.db');
	$queries = null;
	$res = null;
	
	if ($runtype == 2) {
		$res = $qdb->query('SELECT * FROM `queries` WHERE `version`');
	}
	else {
		$res = $qdb->query('SELECT * FROM `queries` WHERE `version` >= '.MVERNUM);
	}
	
	$queries = $res->fetchAll();
	
	if ($queries == null) {
		$error[] = 'Could not load mvq.db, please make sure it is in your install directory.';
		$failedstep = true;
	}
	else {
		foreach ($queries as $q) {
			$req = str_replace('mv_', DBPRE, $q['query']);
			$db->query($req);
		}
		
		$curstep = $_SESSION['instep'] = $curstep + 1; // increment and set session
	}
}
?>