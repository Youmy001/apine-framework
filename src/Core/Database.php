<?php
/**
 * Database access tool
 * This script contains an helper to enhance communication with the database
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */
declare(strict_types=1);

namespace Apine\Core;

use Apine\Core\Error\DatabaseException;

/**
 * Database Access Tools
 * Binding for PDO classes. Support select, insert, update and delete
 * statements, execute queries, prepared queries, transactions
 * and singleton.
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Core
 */
final class Database
{
    /**
     * Default PDO connection instance
     *
     * @static
     * @var \PDO
     */
    private static $apine_instance;
    
    /**
     * Custom PDO connection instance
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
    private $isExecute = false;
    
    /**
     * Database class' constructor
     * We strongly discourage you from overriding the default database.
     * This could cause unforeseen issues with entities depending on
     * the default database instance..
     *
     * @param string  $db_type
     * @param string  $db_host
     * @param string  $db_name
     * @param string  $db_user
     * @param string  $db_password
     * @param string  $db_charset
     * @param boolean $db_override Replace the default database
     *
     * @throws DatabaseException If cannot connect to database server
     * @throws \Exception If cannot load the config
     */
    public function __construct(
        string $db_type = null,
        string $db_host = null,
        string $db_name = null,
        string $db_user = 'root',
        string $db_password = '',
        string $db_charset = 'utf8',
        bool $db_override = false
    ) {
        try {
            if ((!is_null($db_type) && !is_null($db_host) && !is_null($db_name)) || !isset(self::$apine_instance)) {
                $config = new Config('config/database.json');
                $db_port = '3306';
                
                if (!(!is_null($db_type) && !is_null($db_host) && !is_null($db_name))) {
                    $db_host = $config->host;
                }
                
                // Split Host string to extract the port
                $port_pos = strrpos($db_host, ':');
                
                if ($port_pos) {
                    $str_port = substr($db_host, $port_pos + 1);
                    
                    if (is_numeric($str_port)) {
                        $db_port = (int)$str_port;
                    }
                }
                
                if (!is_null($db_type) && !is_null($db_host) && !is_null($db_name)) {
                    $db_dns = $db_type . ':host=' . $db_host . ';dbname=' . $db_name . ';port=' . $db_port . ';charset=' . $db_charset;
                } else {
                    $db_dns = $config->type . ':host=' . $db_host . ';dbname=' . $config->dbname . ';port=' . $db_port . ';charset=' . $config->charset;
                    $db_user = $config->username;
                    $db_password = $config->password;
                }
                
                
                $this->instance = new \PDO($db_dns, $db_user, $db_password);
                $this->instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $this->instance->exec('SET time_zone = "+00:00";');
                
                if ((!(!is_null($db_type) && !is_null($db_host) && !is_null($db_name)) && !isset(self::$apine_instance)) || $db_override === true) {
                    self::$apine_instance = $this->instance;
                }
            } else {
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
     *
     * @throws DatabaseException If unable to execute query
     * @return array Matching rows
     */
    public function select(string $query) : array
    {
        $arResult = array();
        
        try {
            $result = $this->instance->query($query);
            
            if ($result !== false) {
                $arResult = $result->fetchAll(\PDO::FETCH_ASSOC);
                $result->closeCursor();
                $result = null;
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
     * @param string   $table_name
     *        Name of the table in which insert the row
     * @param array<string,mixed> $ar_values
     *        Field names and values to include in the row
     *
     * @throws \Apine\Exception\DatabaseException If cannot execute insertion query
     * @return int Id of the newly inserted row
     */
    public function insert(string $table_name, array $ar_values) : int
    {
        $fields = array_keys($ar_values);
        $values = array_values($ar_values);
        $new_values = array();
        
        // Quote string values
        foreach ($values as $value) {
            
            /*if (is_string($val)) {
                $val = $this->quote($val);
            } else {
                if (is_null($val)) {
                    $val = 'NULL';
                } else {
                    if (is_bool($val)) {
                        if ($val === true) {
                            $val = 'TRUE';
                        } else {
                            $val = 'FALSE';
                        }
                    }
                }
            }*/
    
            if (is_string($value) && !is_numeric($value)) {
                $value = $this->quote($value);
            } else if (is_bool($value)) {
                $value = ($value === true) ? 'TRUE' : 'FALSE';
            } else if (is_null($value)) {
                $value = 'NULL';
            }
            
            $new_values[] = $value;
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
            throw new DatabaseException($e->getMessage(), 500, $e);
        }
    }
    
    /**
     * Update one or many table rows from the database through the PDO
     * handler
     *
     * @param string   $table_name
     *        Name of the table in which modify rows
     * @param array<string,mixed> $ar_values
     *        Field names and values to modify on rows
     * @param array<string,mixed> $ar_conditions
     *        Field names and values to match desired rows - Used to
     *        define the "WHERE" SQL statement
     *
     * @throws DatabaseException If cannot execute update query
     * @throws \Exception
     */
    public function update(string $table_name, array $ar_values, array $ar_conditions) : void
    {
        $new_values = array();
        $ar_where = array();
        
        // Quote string values
        foreach ($ar_values as $field => $value) {
            
            /*if (is_string($val)) {
                $val = $this->quote($val);
            } else {
                if (is_null($val)) {
                    $val = 'NULL';
                } else {
                    if (is_bool($val)) {
                        if ($val === true) {
                            $val = 'TRUE';
                        } else {
                            $val = 'FALSE';
                        }
                    }
                }
            }*/
    
            if (is_string($value) && !is_numeric($value)) {
                $value = $this->quote($value);
            } else if (is_bool($value)) {
                $value = ($value === true) ? 'TRUE' : 'FALSE';
            } else if (is_null($value)) {
                $value = 'NULL';
            }
            
            $new_values[] = "$field = $value";
        }
        
        // Quote Conditions values
        foreach ($ar_conditions as $field => $value) {
            
            if (is_string($value) && !is_numeric($value)) {
                $value = $this->quote($value);
            }
            
            $ar_where[] = "$field = $value";
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
                throw new \Exception('Missing Values', 500);
            }
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage(), 500, $e);
        }
    }
    
    /**
     * Delete one or many table rows from the database through the PDO
     * handler
     *
     * @param string   $table_name
     *        Name of the table in which delete rows
     * @param array<string,mixed> $ar_conditions
     *        Field names and values to match desired rows - Used to
     *        define the "WHERE" SQL statement
     *
     * @throws \Apine\Exception\DatabaseException If cannot execute delete query
     * @return boolean
     */
    public function delete($table_name, $ar_conditions)
    {
        $ar_where = array();
        
        // Quote Conditions values
        foreach ($ar_conditions as $field => $val) {
            
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
            throw new DatabaseException($e->getMessage(), 500, $e);
        }
    }
    
    /**
     * Execute operation onto database through the PDO handler with a
     * MySQL query
     *
     * @param string $query
     *        Query of any type to execute
     *
     * @throws DatabaseException If cannot execute the query
     * @return integer
     */
    public function exec($query)
    {
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
     * @param array  $driver_options
     *        Attributes as defined on http://php.net/manual/en/pdo.prepare.php
     *
     * @return integer
     */
    public function prepare($statement, $driver_options = array())
    {
        // Returns statement's index for later access
        $this->isExecute = true;
        $this->Execute[] = $this->instance->prepare($statement, $driver_options);
        end($this->Execute);
        
        return key($this->Execute);
    }
    
    /**
     * Execute a previously prepared statement
     *
     * @param array   $input_parameters
     *        Values to replace markers in statement
     * @param integer $index
     *        Id of the statement to execute
     *
     * @throws \Apine\Exception\DatabaseException If cannot execute statement
     * @return mixed
     */
    public function execute($input_parameters = array(), $index = null)
    {
        // When no index is passed, executes the oldest statement
        if ($this->isExecute) {
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
                    $arResult = (bool)$result->rowCount();
                } else {
                    while ($data = $result->fetch(\PDO::FETCH_ASSOC)) {
                        $arResult[] = $data;
                    }
                }
                
                $result->closeCursor();
                
                return $arResult;
            } catch (\PDOException $e) {
                throw new DatabaseException($e->getMessage(), 500, $e);
            }
        } else {
            throw new DatabaseException('Trying to fetch on non-existent PDO Statement.', 500);
        }
    }
    
    /**
     * Close a previously prepared PDO statement
     *
     * @param integer $index
     *        Identifier of the PDO statement
     */
    public function closeCursor($index = null) : void
    {
        // If not index is passed, deletes the oldest statement
        if ($index == null) {
            reset($this->Execute);
            $index = key($this->Execute);
        }
        
        /*if (array_key_exists($index, $this->Execute) == true) {
            $result = $this->Execute[$index];
        }*/
        
        if (count($this->Execute) > 0) {
            //$this->Execute[$index] = null;
            unset($this->Execute[$index]);
        }
        
        if (count($this->Execute) == 0) {
            $this->isExecute = false;
        }
    }
    
    /**
     * Return the Id of the last inserted row
     *
     * @param string $name
     *        Name of the sequence object from which the ID should be
     *        returned.
     *
     * @return mixed
     * @see PDO::lastInsertID()
     */
    public function last_insert_id($name = null)
    {
        return (null !== $name) ? $this->instance->lastInsertId($name) : $this->instance->lastInsertId();
    }
    
    /**
     * Quotes a string for use in a query.
     *
     * @param string  $string
     *        String to quote following database server's settings
     * @param integer $parameter_type
     *        Provides a data type hint for drivers that have
     *        alternate quoting styles.
     *
     * @return string
     */
    public function quote($string, $parameter_type = \PDO::PARAM_STR)
    {
        return $this->instance->quote($string, $parameter_type);
    }
    
    public function __destruct()
    {
        if (count($this->Execute) > 0) {
            foreach ($this->Execute as $id => $statement) {
                //$this->Execute[$id] = null;
                unset($this->Execute[$id]);
            }
        }
        
        if ($this->instance !== self::$apine_instance) {
            unset($this->instance);
        }
    }
}
