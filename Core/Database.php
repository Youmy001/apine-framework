<?php
/**
 * Database access tool
 * This script contains an helper to enahnce communication with the database
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
namespace Apine\Core;

use Apine\Application\Application;
use Apine\Exception\DatabaseException;
use Apine\Exception\GenericException;

/**
 * Database Access Tools
 *
 * Binding for PDO classes. Support select, insert, update and delete
 * statements, execute queries, prepared queries, transactions
 * and singleton.
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Core
 */
final class Database {

	/**
	 * Default PDO connection instance
	 * 
	 * @static
	 * @var \PDO
	 */
	private static $apine_instance;
	
	/**
	 * Custom PDO connection instane
	 * 
	 * @var \PDO
	 */
	private $instance;

	/**
	 * PDO Statement to execute
	 * 
	 * @var \PDOStatement[]
	 */
	public $Execute = array();

	/**
	 * Is a PDOStatement is pending execution
	 * 
	 * @var boolean
	 */
	private $_isExecute;

	/**
	 * Database class' constructor
	 *
     * @param string $db_type
     * @param string $db_host
     * @param string $db_name
     * @param string $db_port
     * @param string $db_user
     * @param string $db_password
     * @param string $db_charset
	 * @throws DatabaseException If cannot connect to database server
	 */
	public function __construct ($db_type = null, $db_host = null, $db_name = null, $db_port = '3306', $db_user = 'root', $db_password = '', $db_charset = 'utf8') {

		try {
			if (!is_null($db_type) && !is_null($db_host) && !is_null($db_name)) {
				$this->instance = new \PDO($db_type . ':host=' . $db_host . ';dbname=' . $db_name . ';port=' . $db_port . ';charset=' . $db_charset, $db_user, $db_password);
				$this->instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
				$this->instance->exec('SET time_zone = "+00:00";');
			} else { 
				if (!isset(self::$apine_instance)) {
					try {
						$config = Application::get_instance()->get_config();
						self::$apine_instance = new \PDO($config->get('database', 'type').':host='.$config->get('database', 'host').';dbname='.$config->get('database', 'dbname').';port=' . $config->get('database', 'port') . ';charset='.$config->get('database', 'charset'), $config->get('database', 'username'), $config->get('database', 'password'));
						self::$apine_instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
						self::$apine_instance->exec('SET time_zone = "+00:00";');
					} catch (\PDOException $e) {
						throw new DatabaseException($e->getMessage(), $e->getCode(), $e);
					}
				}
			}
			
			if (is_null($this->instance)) {
				$this->instance = &self::$apine_instance;
			}
		} catch (\PDOException $e) {
			throw new DatabaseException($e->getMessage(), $e->getCode(), $e);
		}
	
	}

	/**
	 * Fetch table rows from database through the PDO handler with a
	 * MySQL query
	 * 
	 * @param string $query
	 *        Query of a SELECT type to execute
	 * @throws DatabaseException If unable to execute query
	 * @return array Matching rows
	 */
	public function select ($query) {

		$arResult = array();
		
		try {
			$result = $this->instance->query($query);
			
			if ($result) {
				
				$arResult = $result->fetchAll(\PDO::FETCH_ASSOC);
				
				$result->closeCursor();
			}
			
			return $arResult;
		} catch (\PDOException $e) {
			throw new DatabaseException($e->getMessage(), $e->getCode(), $e);
		}
	
	}

	/**
	 * Insert a new table row into the database through the PDO
	 * handler
	 * 
	 * @param string $table_name
	 *        Name of the table in which insert the row
	 * @param string[] $ar_values
	 *        Field names and values to include in the row
	 * @throws \Apine\Exception\DatabaseException If cannot execute insertion query
	 * @return string Id of the newly inserted row
	 */
	public function insert ($table_name, $ar_values) {
		
		$fields = array_keys($ar_values);
		$values = array_values($ar_values);
		$new_values = array();
		
		// Quote string values
		foreach ($values as $val) {
			
			if (is_string($val)) {
				$val = $this->quote($val);
			} else if (is_null($val)) {
				$val = 'NULL';
			} else if (is_bool($val)) {
				if ($val === true) {
					$val = 'TRUE';
				} else {
					$val = 'FALSE';
				}
			}
			
			$new_values[] = $val;
		}
		
		// Create query
		$query = "INSERT into $table_name (";
		$query .= join(',', $fields);
		$query .= ') values (';
		$query .= join(',', $new_values) . ')';
		
		//print $query;
		try {
			$success = $this->instance->exec($query);
			
			if ($success == 0) {
				throw new \PDOException('Cannot insert row');
			}
			
			return $this->last_insert_id();
		} catch (\PDOException $e) {
			throw new DatabaseException($e->getMessage(), $e->getCode(), $e);
		}
	
	}

	/**
	 * Update one or many table rows from the database through the PDO
	 * handler
	 * 
	 * @param string $table_name
	 *        Name of the table in which modify rows
	 * @param string[] $ar_values
	 *        Field names and values to modify on rows
	 * @param string[] $ar_conditions
	 *        Field names and values to match desired rows - Used to
	 *        define the "WHERE" SQL statement
	 * @throws DatabaseException If cannot execute update query
     * @throws GenericException
	 */
	public function update ($table_name, $ar_values, $ar_conditions) {
		
		$new_values = array();
		$ar_where = array();
		
		// Quote string values
		foreach ($ar_values as $field=>$val) {
			
			if (is_string($val)) {
				$val = $this->quote($val);
			} else if (is_null($val)) {
				$val = 'NULL';
			} else if (is_bool($val)) {
				if ($val === true) {
					$val = 'TRUE';
				} else {
					$val = 'FALSE';
				}
			}
			
			$new_values[] = "$field = $val";
		}
		
		// Quote Conditions values
		foreach ($ar_conditions as $field=>$val) {
			
			if (is_string($val) && !is_numeric($val)) {
				$val = $this->quote($val);
			}
			
			$ar_where[] = "$field = $val";
		}
		
		// Create query
		$query = "UPDATE $table_name SET ";
		$query .= join(' , ', $new_values);
		$query .= ' WHERE ' . join(' AND ', $ar_where);
		
		//print $query;
		try {
			
			if (count($ar_values) > 0) {
				$this->instance->exec($query);
			} else {
				throw new GenericException('Missing Values', 500);
			}
		} catch (\PDOException $e) {
			throw new DatabaseException($e->getMessage(), $e->getCode(), $e);
		}
	
	}

	/**
	 * Delete one or many table rows from the database through the PDO
	 * handler
	 * 
	 * @param string $table_name
	 *        Name of the table in which delete rows
	 * @param string[] $ar_conditions
	 *        Field names and values to match desired rows - Used to
	 *        define the "WHERE" SQL statement
	 * @throws \Apine\Exception\DatabaseException If cannot execute delete query
	 * @return boolean
	 */
	public function delete ($table_name, $ar_conditions) {
		
		$ar_where = array();
		
		// Quote Conditions values
		foreach($ar_conditions as $field=>$val){
			
			if (is_string($val)) {
				$val = $this->quote($val);
			}
			
			$ar_where[] = "$field = $val";
		}
		
		// Create query
		$query = "DELETE FROM $table_name WHERE " . join(' AND ', $ar_where);
		
		try {
			$success = $this->instance->exec($query);
			
			if ($success == 0) {
				return false;
			}
			
			return true;
		} catch (\PDOException $e) {
			throw new DatabaseException($e->getMessage(), $e->getCode(), $e);
		}
	
	}

	/**
	 * Execute operation onto database through the PDO handler with a
	 * MySQL query
	 * 
	 * @param string $query
	 *        Query of any type to execute
	 * @throws DatabaseException If cannot execute the query
	 * @return integer
	 */
	public function exec ($query) {

		try {
			$result = $this->instance->exec($query);
			return $result;
		} catch (\PDOException $e) {
			throw new DatabaseException($e->getMessage(), $e->getCode(), $e);
		}
	
	}

	/**
	 * Prepare a statement for later execution
	 * 
	 * @param string $statement
	 *        MySQL query statement
	 * @param array $driver_options
	 *        Attributes as defined on http://php.net/manual/en/pdo.prepare.php
	 * @return integer
	 */
	public function prepare ($statement, $driver_options = array()) {
		
		// Returns statement's index for later access
		$this->_isExecute = true;
		$this->Execute[] = $this->instance->prepare($statement, $driver_options);
		end($this->Execute);
		return key($this->Execute);
	
	}

	/**
	 * Execute a previously prepared statement
	 * 
	 * @param array $input_parameters
	 *        Values to replace markers in statement
	 * @param integer $index
	 *        Id of the statement to execute
	 * @throws \Apine\Exception\DatabaseException If cannot execute statement
	 * @return mixed
	 */
	public function execute ($input_parameters = array(), $index = null) {
		
		// When no index is passed, executes the oldest statement
		if ($this->_isExecute) {
			$arResult = array();
			
			if ($index == null) {
				reset($this->Execute);
				$index = key($this->Execute);
			}
			
			if (array_key_exists($index, $this->Execute) == true) {
				$result = $this->Execute[$index];
			}
			
			try {
			    if (!isset($result)) {
			        throw new DatabaseException('Non-existent PDO Statement', 500);
                }

				$result->execute($input_parameters);
				
				if ($result->columnCount() == 0) {
					$arResult = (bool) $result->rowCount();
				} else {
					while ($data = $result->fetch()) {
						$arResult[] = $data;
					}
				}
				
				$result->closeCursor();
				return $arResult;
			} catch (\PDOException $e) {
				throw new DatabaseException($e->getMessage(), 500, $e);
			}
		}else{
			throw new DatabaseException('Trying to fetch on non-existent PDO Statement.', 500);
		}
	
	}

	/**
	 * Close a previously prepared PDO statement
	 * 
	 * @param integer $index
	 *        Identifier of the PDO tatement
	 */
	public function close_cursor ($index = null) {
		
		// If not index is passed, deletes the oldest statement
		if ($index == null) {
			reset($this->Execute);
			$index = key($this->Execute);
		}
		
		/*if (array_key_exists($index, $this->Execute) == true) {
			$result = $this->Execute[$index];
		}*/
		
		if (count($this->Execute) > 0) {
			unset($this->Execute[$index]);
		}
		
		if (count($this->Execute) == 0) {
			$this->_isExecute = false;
		}
	
	}
	
	/**
	 * Return the Id of the last inserted row
	 * 
	 * @param string $name
	 *        Name of the sequence object from which the ID should be
	 *        returned.
	 * @return integer
	 * @see PDO::lastInsertID()
	 */
	public function last_insert_id ($name = null) {

		return $this->instance->lastInsertId($name);
	
	}

	/**
	 * Quotes a string for use in a query.
	 * 
	 * @param string $string
	 *        String to quote following database server's settings
	 * @param integer $parameter_type
	 *        Provides a data type hint for drivers that have
	 *        alternate quoting styles.
	 * @return string
	 */
	public function quote ($string, $parameter_type = \PDO::PARAM_STR) {

		return $this->instance->quote($string, $parameter_type);
	
	}

}
