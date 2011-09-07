<?php

/**
 * Database
 * Provides an interface to a MySQL database.
 *
 * @author Travis Dent <tcdent@gmail.com>
 * @copyright (c) 2007-2008 Travis Dent. All rights reserved.
 * @package Framework
 * @version 0.3.3
 */

class Database {
	
	/**
	 * @var string Prefix to prepend to table names in all queries.
	 */
	public $prefix;
	
	/**
	 * @var array Holds SQL statements from all executed queries.
	 */
	public $queries = array();
	
	/**
	 * @var mixed MySQL connection instance.
	 */
	public $connection;
	
	/**
	 * @var mixed MySQL database instance.
	 */
	public $database;
	
	
	public function __construct($config=NULL){
		
		if($config == NULL){
			$config = $_ENV['CONFIG']['db'];
		}
		
		$this->connect($config['host'], $config['user'], $config['password'], $config['name']);
		if(array_key_exists('prefix', $config)) $this->prefix = $config['prefix'];
	}
	
	/**
	 * Open a connection.
	 * 
	 * @param string $host Database hostname.
	 * @param string $user Database username.
	 * @param string $password Database password.
	 * @param string $database Database name.
	 */
	public function connect($host, $user, $password, $database){
		
		if(!$this->connection = mysql_connect($host, $user, $password)){
			die("Could not connect to server: ".mysql_error());
		}
		elseif(!$this->database = mysql_select_db($database, $this->connection)){
			die("Could not select database: ".mysql_error());
		}
		
		return TRUE;
	}
	
	/**
	 * Report an error with a query.
	 * 
	 * @param string $query Query that caused an error.
	 * @global mixed $errors Instance of Error class.
	 */
	public function error($query){
		global $errors;
		
		$message  = "There was an error retrieving your request. The query has been printed below.<br />\n";
		$message .= "<p style=\"font-family:monospace;\">$query</p>";
			
		if($errors){
			$errors->warn($message);
		}
		else {
			print $message;
		}
	}
	
	/**
	 * Store and execute an SQL query.
	 * 
	 * @param string $query Query to execute.
	 * @return mixed Query result.
	 */
	public function query($query){
		
		//log_message("[Database] Query: $query");
		//$this->queries[] = $query;
		return mysql_query($query);
	}
	
	/**
	 * Run a fetch query with raw SQL.
	 * Optionally, return with an array key other than the 'id'.
	 * 
	 * @param string $query SQL query.
	 * @param string $index Return the array with this column as the array key.
	 * @return array Resulting rows.
	 */
	public function get($query, $index=NULL){
		
		$query_result = $this->query($query);
		$num_rows = @mysql_num_rows($query_result);
		if($num_rows > 0){
			while($row = mysql_fetch_array($query_result, MYSQL_ASSOC)){
				if(array_key_exists($index, $row)){
					foreach(array_keys($row) as $key){
						if(count(array_keys($row)) == 2){
							$results[$row[$index]] = $this->un_clean($row[$key]);
						}
						elseif($key != $index){
							$results[$row[$index]][$key] = $this->un_clean($row[$key]);
						}
					}
				}
				else{
					$results[] = $row;
				}
			}
			
			return $results;
		}
		elseif($num_rows == 0){
			return FALSE;
		}
		else {
			$this->error($query);
			return FALSE;
		}
	}
	
	/**
	 * Get all rows in a table. 
	 * Optionally, limit the result to a range, sort it, or return with an array key other than the 'id'.
	 * 
	 * @param string $table Table to SELECT from.
	 * @param string $start Entry to start with.
	 * @param string $limit Number of entries to return.
	 * @param string $order Order by a key using standard SQL syntax ex: "date ASC".
	 * @param string $index Return the array with this column as the array key.
	 * @return array Resulting rows.
	 */
	public function get_all($table, $start=NULL, $limit=NULL, $order=NULL, $index=NULL){
		
		$query = "SELECT * FROM ".$this->prefix($table);
		if($order != NULL){
			$query .= " ORDER BY $order";
		}
		if($start > 0 || $limit > 0){
			$query .= " LIMIT $start, $limit";
		}
		if(!$return = $this->get($query, $index)){
			return array();
		}
		return $return;
	}
	
	/**
	 * Get the first matching row in a table with raw SQL.
	 * 
	 * @param string $query SQL query.
	 * @return array Resulting rows or FALSE.
	 */
	public function get_first($query){
	
		$query_result = $this->query($query);
		$num_rows = @mysql_num_rows($query_result);
		if($num_rows > 0){
			$result = mysql_fetch_array($query_result, MYSQL_ASSOC);
			foreach($result as $key => $value){
				$return[$key] = $this->un_clean($value);
			}
			
			return $return;
		}
		elseif($num_rows == 0){
			return FALSE;
		}
		else {
			$this->error($query);
			return FALSE;
		}
	}
	
	/**
	 * Get the first row in a table with a $key matching $value.
	 * Optionally, return with an array key other than the 'id'.
	 * 
	 * @param string $table Table to SELECT from.
	 * @param string $index Return the array with this column as the array key.
	 * @return array Resulting rows.
	 */
	public function get_by_key($key, $value, $table){
		
		return $this->get_first(sprintf(
			"SELECT * FROM %s WHERE %s = '%s'", 
				$this->prefix($table), 
				$this->clean($key), 
				$this->clean($value)));
	}
	
	/**
	 * Get a row in the table by its 'id'.
	 * 
	 * @param string $id 'id' of the row to fetch.
	 * @param string $table Table to SELECT from.
	 * @return array Resulting rows.
	 */
	public function get_by_id($id, $table){
		
		 return $this->get_by_key('id', $id, $table);
	}
	
	/**
	 * Count the rows in a table. 
	 * Optionally, restrict with a WHERE statement.
	 * 
	 * @param string $table Table to count.
	 * @param string $where Optional WHERE statement.
	 * @return int Number of rows.
	 */
	public function count($table, $where=NULL){
		
		$query = "SELECT count(*) as count FROM ".$this->prefix($table);
		if($where) $query .= " WHERE $where";
		
		if($result = $this->get_first($query)){
			return $result['count'];
		}
		return 0;
	}
	
	/**
	 * UPDATE or INSERT a row. 
	 * If an index is passed the entry will be updated, otherwise it will be created.
	 * 
	 * @param array $entry Associative array containing the entry to be saved.
	 * @param string $table Table to update.
	 * @param string $index Use this as the index. Use caution if you change this as multiple entries can be updated at once.
	 * @return mixed 'id' of the inserted or updated entry or FALSE.
	 */
	public function save($entry, $table, $index='id'){
		
		$table = $this->prefix($table);
		$values = "";
		$sep = "";
		foreach($entry as $key => $value){
			$values .= $sep . "`" . $key . "` = '" . $this->clean($value) . "'";
			$sep = ", ";
		}
		
		if($this->get_by_key($index, $entry[$index], $table)){
			//Updating existing.
			$query = "UPDATE $table SET $values WHERE $index = '".$entry[$index]."'";

			if($this->query($query)){
				return $entry[$index];
			}
			else {
				$this->error($query);
				return FALSE;
			}
		}
		else {
			//Adding new.
			$query = "INSERT INTO $table SET $values";
			if($this->query($query)){
				return mysql_insert_id();
			}
			else {
				$this->error($query);
				return FALSE;
			}
		}
	}
	
	/**
	 * DELETE a row.
	 * 
	 * @param string $value Delete the row with this value for the $index.
	 * @param string $table Table to update.
	 * @param string $index Use this as the index. Use caution if you change this as multiple entries can be deleted at once.
	 * @return mixed Query result.
	 */
	public function delete($value, $table, $index='id'){
		
		return $this->query("DELETE FROM ".$this->prefix($table)." WHERE $index = '$value'");
	}
	
	public function delete_set($value, $table, $index='id'){
		return $this->query("DELETE FROM ".$this->prefix($table)."  WHERE $index IS NULL");
	}
	

	
	/**
	 * Prepend the prefix to the $table if one has been given.
	 * 
	 * @param string $table Table to prefix.
	 * @return string Prefixed table name.
	 */
	public function prefix($table){
		
		if(!empty($this->prefix)){
			return $this->prefix.$table;
		}
		return $table;
	}
	
	/**
	 * The current time in MySQL datetime format.
	 * 
	 * @param string $format Date format.
	 * @return string Formatted datetime.
	 */
	public function now($format='Y-m-d H:i:s'){
		
		return date($format, time());
	}
	
	/**
	 * Return a MySQL friendly string.
	 * 
	 * @param string $string Value to clean.
	 * @return string Cleaned value.
	 */
	public function clean($string){
		
		return mysql_real_escape_string($string);
	}
	
	/**
	 * Return the 'cleaned' value in its original state.
	 * 
	 * @param string $string Value to un-clean.
	 * @return string Un-cleaned value.
	 */
	public function un_clean($string){
		
		return stripslashes($string);
	}
}

?>