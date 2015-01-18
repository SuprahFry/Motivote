<?php
class Database
{
	private $_user, $_pass, $_host, $_database, $_link;
	
	public function __construct($user, $pass, $host, $database)
	{
		$this->_user = $user;
		$this->_pass = $pass;
		$this->_host = $host;
		$this->_database = $database;
		$this->connect();
	}
	
	public function connect()
	{
		$this->_link = mysqli_connect($this->_host, $this->_user, $this->_pass, $this->_database);
		//mysqli_select_db($this->_link, $this->_database);
	}
	
	public function disconnect()
	{
		@mysqli_close($this->_link); // who cares if this throws an error?
	}
	
	public function createCall($function, $prependArguments, $arguments)
	{
		$args = array_merge((array)$prependArguments, (array)$arguments);
		return @call_user_func_array(array($this, $function), $args);
	}
	
	public function beginTransaction() {
		return mysqli_begin_transaction($this->_link);
	}
	
	public function commit() {
		return mysqli_commit($this->_link);
	}
	
	public function escapedCountQuery($query)
	{
		$valueCount = func_num_args();
		
		if ($valueCount > 1)
		{
			$args = func_get_args(); // because the first parameter is the query
			unset($args[0]);
			$callQuery = $this->createCall('escapedArrayAssoc', $query, $args);
		}
		else
		{
			$callQuery = $this->escapedArrayAssoc($query);
		}
		
		//$_callQuery = $this->escapedArrayAssoc("SELECT COUNT(*) FROM `logged_in` WHERE `player_id` = '%1:d'", $this->_playerDatabaseID);
		$count = intval($callQuery['COUNT(*)']);
		
		if ($count > 0)
		{
			return $count;
		}
		
		return false;
	}

	public function escapedArrayAssoc($query)
	{
		$valueCount = func_num_args();
		
		if ($valueCount > 1)
		{
			$args = func_get_args(); // because the first parameter is the query
			unset($args[0]);
			return $this->createCall('escapedArray', array($query, MYSQLI_ASSOC), $args);
			//return $this->escapedArray($query, MYSQL_ASSOC, $args);
		}
		
		return $this->escapedArray($query, MYSQLI_ASSOC);
	}

	public function escapedArray($query, $result_type)
	{
		$valueCount = func_num_args();
		
		if ($valueCount > 2)
		{
			$args = func_get_args(); // because the first parameter is the query and second is the result type
			unset($args[0]);
			unset($args[1]);
			//$_query = $this->escapedQuery($query, $args);
			$_query = $this->createCall('escapedQuery', $query, $args);
		}
		else
		{
			$_query = $this->escapedQuery($query);
		}
		
		$_result = mysqli_fetch_array($_query, $result_type);
		
		while (mysqli_next_result($this->_link)) {
		  if (!mysqli_more_results($this->_link)) break;
		}
		
		@mysqli_free_result($_query);
		return $_result;
	}

	public function escapedAllResultsAssoc($query)
	{
		$valueCount = func_num_args();
		
		if ($valueCount > 1)
		{
			$args = func_get_args(); // because the first parameter is the query
			unset($args[0]);
			//return $this->escapedAllResults($query, MYSQL_ASSOC, $args);
			return $this->createCall('escapedAllResults', array($query, MYSQLI_ASSOC), $args);
		}
		
		return $this->escapedAllResults($query, MYSQLI_ASSOC);
	}

	public function escapedAllResults($query, $result_type)
	{
		$valueCount = func_num_args();
		
		if ($valueCount > 2)
		{
			$args = func_get_args(); // because the first parameter is the query and second is the result type
			unset($args[0]);
			unset($args[1]);
			//$_query = $this->escapedQuery($query, $args);
			$_query = $this->createCall('escapedQuery', $query, $args);
		}
		else
		{
			$_query = $this->escapedQuery($query);
		}
		
		//var_dump($query);
		$results = array();
		
		while ($result = mysqli_fetch_array($_query, $result_type))
		{
			$results[] = $result;
		}
		
		while (mysqli_next_result($this->_link)) {
		  if (!mysqli_more_results($this->_link)) break;
		}
		
		@mysqli_free_result($_query);
		return $results;
	}
	
	public function lastError() {
		return mysqli_error($this->_link);
	}
	
	public function lastInsertID()
	{
		return mysqli_insert_id($this->_link);
	}
	
	public function sanitize(&$in) {
		foreach ($in as $k => $v) {
			$in[$k] = $this->_link->real_escape_string($v);
		}
		
		return $in;
	}
	
	public function escapedQuery($query)
	{
		$valueCount = func_num_args();
		
		if ($valueCount > 1)
		{
			$args = func_get_args(); // because the first parameter is the query
			unset($args[0]);
			$query = vsprintf(preg_replace('/%([0-9]+):(d|s)/', '%$1$$2', $query), $this->sanitize($args));
		}
		
		echo($query);
		return mysqli_query($this->_link, $query);
	}

	public function escapedMultiQuery($query)
	{
		$valueCount = func_num_args();
		$rows = array();
		
		if ($valueCount > 1)
		{
			$args = func_get_args(); // because the first parameter is the query
			unset($args[0]);
			$query = vsprintf(preg_replace('/%([1-9]):(d|s)/', '%$1$$2', $query), $this->sanitize($args));
		}
		
		//echo($query);
		
		if (mysqli_multi_query($this->_link, $query))
		{
			while(mysqli_more_results($this->_link))
			{
				mysqli_next_result($this->_link);
				if ($result = mysqli_store_result($this->_link))
				{
					$rows[] = mysqli_fetch_array($result);
					mysqli_free_result($result);
				}
			}
			return $rows;
		}
		
		return null;
	}

	public function getLink()
	{
		return $this->_link;
	}

	public function getDatabase()
	{
		return $this->_database;
	}
	
	public function __destruct()
	{
		$this->disconnect();
	}
}
?>