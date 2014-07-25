<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
#
# This file is part of Clearbricks.
#
# Copyright (c) 2003-2008 Olivier Meunier and contributors
# Licensed under the GPL version 2.0 license.
# See LICENSE file or
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
#
# -- END LICENSE BLOCK ------------------------------------

/**
* Clearbricks DBLayer
*
* @package Clearbricks
* @subpackage DBLayer
*/

require dirname(__FILE__).'/class.cursor.php';

/**
* Clearbricks Database Abstraction Layer interface
*
* All methods in this interface should be implemented in your database driver.
*
* Database driver is a class that extends {@link dbLayer}, implements
* {@link i_dbLayer} and has a name of the form (driver name)Connection.
*
* @package Clearbricks
* @subpackage DBLayer
*/
interface i_dbLayer
{
	/**
	* Open connection
	*
	* This method should open a database connection and return a new resource
	* link.
	*
	* @param string	$host		Database server host
	* @param string	$user		Database user name
	* @param string	$password		Database password
	* @param string	$database		Database name
	* @return resource
	*/
	function db_connect($host,$user,$password,$database);
	
	/**
	* Open persistent connection
	*
	* This method should open a persistent database connection and return a new
	* resource link.
	*
	* @param string	$host		Database server host
	* @param string	$user		Database user name
	* @param string	$password		Database password
	* @param string	$database		Database name
	* @return resource
	*/
	function db_pconnect($host,$user,$password,$database);
	
	/**
	* Close connection
	*
	* This method should close resource link.
	*
	* @param resource	$handle		Resource link
	*/
	function db_close($handle);
	
	/**
	* Database version
	*
	* This method should return database version number.
	*
	* @param resource	$handle		Resource link
	* @return string
	*/
	function db_version($handle);
	
	/**
	* Database query
	*
	* This method should run an SQL query and return a resource result.
	*
	* @param resource	$handle		Resource link
	* @param string	$query		SQL query string
	* @return resource
	*/
	function db_query($handle,$query);
	
	/**
	* Database exec query
	*
	* This method should run an SQL query and return a resource result.
	*
	* @param resource	$handle		Resource link
	* @param string	$query		SQL query string
	* @return resource
	*/
	function db_exec($handle,$query);
	
	/**
	* Result columns count
	*
	* This method should return the number of fields in a result.
	*
	* @param resource	$res			Resource result
	* @return integer
	*/
	function db_num_fields($res);
	
	/**
	* Result rows count
	*
	* This method should return the number of rows in a result.
	*
	* @param resource	$res			Resource result
	* @return integer
	*/
	function db_num_rows($res);
	
	/**
	* Field name
	*
	* This method should return the name of the field at the given position
	* <var>$position</var>.
	*
	* @param resource	$res			Resource result
	* @param integer	$position		Field position
	* @return string
	*/
	function db_field_name($res,$position);
	
	/**
	* Field type
	*
	* This method should return the field type a the given position
	* <var>$position</var>.
	*
	* @param resource	$res			Resource result
	* @param integer	$position		Field position
	* @return string
	*/
	function db_field_type($res,$position);
	
	/**
	* Fetch result
	*
	* This method should fetch one line of result and return an associative array
	* with field name as key and field value as value.
	*
	* @param resource	$res			Resource result
	* @return array
	*/
	function db_fetch_assoc($res);
	
	/**
	* Move result cursor
	*
	* This method should move result cursor on given row position <var>$row</var>
	* and return true on success.
	*
	* @param resource	$res			Resource result
	* @param integer	$position		Row position
	* @return boolean
	*/
	function db_result_seek($res,$row);
	
	/**
	* Affected rows
	*
	* This method should return number of rows affected by INSERT, UPDATE or
	* DELETE queries.
	*
	* @param resource	$handle		Resource link
	* @param resource	$res			Resource result
	* @return integer
	*/
	function db_changes($handle,$res);
	
	/**
	* Last error
	*
	* This method should return the last error string for the current connection.
	*
	* @param resource	$handle		Resource link
	* @return string
	*/
	function db_last_error($handle);
	
	/**
	* Escape string
	*
	* This method should return an escaped string for the current connection.
	*
	* @param string	$str			String to escape
	* @param resource	$handle		Resource link
	* @return string
	*/
	function db_escape_string($str,$handle=null);
	
	/**
	* Acquiere Write lock
	*
	* This method should lock the given table in write access.
	*
	* @param string	$table		Table name
	*/
	function db_write_lock($table);
	
	/**
	* Release lock
	*
	* This method should releases an acquiered lock.
	*/
	function db_unlock();
}

/**
* Database Abstraction Layer class
*
* Base class for database abstraction. Each driver extends this class and
* implements {@link i_dbLayer} interface.
*
* @package Clearbricks
* @subpackage DBLayer
*/
class dbLayer
{
	/** @var string	Driver name */
	protected $__driver = null;
	
	/** @var string	Database version */
	protected $__version = null;
	
	/** @var resource	Database resource link */
	protected $__link;
	
	/** @var resource	Last result resource link */
	protected $__last_result;
	
	/**
	* Start connection
	*
	* Static function to use to init database layer. Returns a object extending
	* dbLayer.
	*
	* @param string	$driver		Driver name
	* @param string	$host		Database hostname
	* @param string	$database		Database name
	* @param string	$user		User ID
	* @param string	$password		Password
	* @param string	$persistent	Persistent connection
	* @return object
	*/
	public static function init($driver,$host,$database,$user='',$password='',$persistent=false)
	{
		if (file_exists(dirname(__FILE__).'/class.'.$driver.'.php')) {
			require_once dirname(__FILE__).'/class.'.$driver.'.php';
			$driver_class = $driver.'Connection';
		} else {
			trigger_error('Unable to load DB layer for '.$driver,E_USER_ERROR);
			exit(1);
		}
		
		return new $driver_class($host,$database,$user,$password,$persistent);
	}
	
	/**
	* @param string	$host		Database hostname
	* @param string	$database		Database name
	* @param string	$user		User ID
	* @param string	$password		Password
	* @param string	$persistent	Persistent connection
	*/
	public function __construct($host,$database,$user='',$password='',$persistent=false)
	{
		if ($persistent) {
			$this->__link = $this->db_pconnect($host,$user,$password,$database);
		} else {
			$this->__link = $this->db_connect($host,$user,$password,$database);
		}
		
		$this->__version = $this->db_version($this->__link);
		$this->__database = $database;
	}
	
	/**
	* Closes database connection.
	*/
	public function close()
	{
		$this->db_close($this->__link);
	}
	
	/**
	* Returns database driver name
	*
	* @return string
	*/
	public function driver()
	{
		return $this->__driver;
	}
	
	/**
	* Returns database driver version
	*
	* @return string
	*/
	public function version()
	{
		return $this->__version;
	}
	
	/**
	* Returns current database name
	*
	* @return string
	*/
	public function database()
	{
		return $this->__database;
	}
	
	/**
	* Returns link resource
	*
	* @return resource
	*/
	public function link()
	{
		return $this->__link;
	}
	
	/**
	* Run query and get results
	*
	* Executes a query and return a {@link record} object.
	* 
	* @param string	$sql			SQL query
	* @return record
	*/
	public function select($sql)
	{
		$result = $this->db_query($this->__link,$sql);
		
		$this->__last_result =& $result;
		
		$info = array();
		$info['con'] =& $this;
		$info['cols'] = $this->db_num_fields($result);
		$info['rows'] = $this->db_num_rows($result);
		$info['info'] = array();
		
		for ($i=0; $i<$info['cols']; $i++) {
			$info['info']['name'][] = $this->db_field_name($result,$i);
			$info['info']['type'][] = $this->db_field_type($result,$i);
		}
		
		return new record($result,$info);
	}
	
	/**
	* Run query
	*
	* Executes a query and return true if succeed
	* 
	* @param string	$sql			SQL query
	* @return true
	*/
	public function execute($sql)
	{
		$result = $this->db_exec($this->__link,$sql);
		
		$this->__last_result =& $result;
		
		return true;
	}
	
	/**
	* Begin transaction
	*
	* Begins a transaction. Transaction should be {@link commit() commited}
	* or {@link rollback() rollbacked}.
	*/
	public function begin()
	{
		$this->execute('BEGIN');
	}
	
	/**
	* Commit transaction
	*
	* Commits a previoulsy started transaction.
	*/
	public function commit()
	{
		$this->execute('COMMIT');
	}
	
	/**
	* Rollback transaction
	*
	* Rollbacks a previously started transaction.
	*/
	public function rollback()
	{
		$this->execute('ROLLBACK');
	}
	
	/**
	* Aquiere write lock
	*
	* This method lock the given table in write access.
	*
	* @param string	$table		Table name
	*/
	public function writeLock($table)
	{
		$this->db_write_lock($table);
	}
	
	/**
	* Release lock
	*
	* This method releases an acquiered lock.
	*/
	public function unlock()
	{
		$this->db_unlock();
	}
	
	/**
	* Vacuum the table given in argument.
	*
	* @param string	$table		Table name
	*/
	public function vacuum($table)
	{
	}
	
	/**
	* Changed rows
	*
	* Returns the number of lines affected by the last DELETE, INSERT or UPDATE
	* query.
	*
	* @return integer
	*/
	public function changes()
	{
		return $this->db_changes($this->__link,$this->__last_result);
	}
	
	/**
	* Last error
	*
	* Returns the last database error or false if no error.
	*
	* @return string|false
	*/
	public function error()
	{
		$err = $this->db_last_error($this->__link);
		
		if (!$err) {
			return false;
		}
		
		return $err;
	}

	/**
	* Date formatting
	*
	* Returns a query fragment with date formater.
	*
	* The following modifiers are accepted:
	*
	* - %d : Day of the month, numeric
	* - %H : Hour 24 (00..23)
	* - %M : Minute (00..59)
	* - %m : Month numeric (01..12)
	* - %S : Seconds (00..59)
	* - %Y : Year, numeric, four digits
	* 
	* @param string	$field			Field name
	* @param string	$pattern			Date format
	* @return string
	*/
	public function dateFormat($field,$pattern)
	{
		return
		'TO_CHAR('.$field.','."'".$this->escape($pattern)."') ";
	}
	
	/**
	* Query Limit
	*
	* Returns a LIMIT query fragment. <var>$arg1</var> could be an array of
	* offset and limit or an integer which is only limit. If <var>$arg2</var>
	* is given and <var>$arg1</var> is an integer, it would become limit.
	*
	* @param array|integer	$arg1		array or integer with limit intervals
	* @param array|null		$arg2		integer or null
	* @return string
	*/
	public function limit($arg1, $arg2=null)
	{
		if (is_array($arg1))
		{
			$arg1 = array_values($arg1);
			$arg2 = isset($arg1[1]) ? $arg1[1] : null;
			$arg1 = $arg1[0];
		}
		
		if ($arg2 === null) {
			$sql = ' LIMIT '.(integer) $arg1.' ';
		} else {
			$sql = ' LIMIT '.(integer) $arg2.' OFFSET '.$arg1.' ';
		}
		
		return $sql;
	}
	
	/**
	* IN fragment
	*
	* Returns a IN query fragment where $in could be an array, a string,
	* an integer or null
	*
	* @param array|string|integer|null		$in		"IN" values
	* @return string
	*/
	public function in($in)
	{
		if (is_null($in))
		{
			return ' IN (NULL) ';
		}
		elseif (is_string($in))
		{
			return " IN ('".$this->escape($in)."') ";
		}
		elseif (is_array($in))
		{
			foreach ($in as $i => $v) {
				if (is_null($v)) {
					$in[$i] = 'NULL';
				} elseif (is_string($v)) {
					$in[$i] = "'".$this->escape($v)."'";
				}
			}
			return ' IN ('.implode(',',$in).') ';
		}
		else
		{
			return ' IN ( '.(integer) $in.') ';
		}
	}
	
	/**
	* Concat strings
	*
	* Returns SQL concatenation of methods arguments. Theses arguments
	* should be properly escaped when needed.
	*
	* @return string
	*/
	public function concat()
	{
		$args = func_get_args();
		return implode(' || ',$args);
	}
	
	/**
	* Escape string
	*
	* Returns SQL protected string or array values.
	*
	* @param string|array	$i		String or array to protect
	* @return string|array
	*/
	public function escape($i)
	{
		if (is_array($i)) {
			foreach ($i as $k => $s) {
				$i[$k] = $this->db_escape_string($s,$this->__link);
			}
			return $i;
		}
		
		return $this->db_escape_string($i,$this->__link);
	}
	
	/**
	* System escape string
	*
	* Returns SQL system protected string.
	* 
	* @param string		$str		String to protect
	* @return string
	*/
	public function escapeSystem($str)
	{
		return '"'.$str.'"';
	}
	
	/**
	* Cursor object
	*
	* Returns a new instance of {@link cursor} class on <var>$table</var> for
	* the current connection.
	*
	* @param string		$table	Target table
	* @return cursor
	*/
	public function openCursor($table)
	{
		return new cursor($this,$table);
	}
}

/**
* Query Result Record Class
*
* This class acts as an iterator over database query result. It does not fetch
* all results on instantiation and thus, depending on database engine, should not
* fill PHP process memory.
*
* @package Clearbricks
* @subpackage DBLayer
*/
class record
{
	/** @var resource	Database resource link */
	protected $__link;
	
	/** @var resource	Query result resource */
	protected $__result;
	
	/** @var array		Result information array */
	protected $__info;
	
	/** @var array		List of static functions that extend record */
	protected $__extend = array();
	
	/** @var integer	Current result position */
	protected $__index = 0;
	
	/** @var array		Current result row content */
	protected $__row = false;
	
	private $__fetch = false;
	
	/**
	* Constructor
	*
	* Creates class instance from result link and some informations.
	* <var>$info</var> is an array with the following content:
	*
	* - con => database object instance
	* - cols => number of columns
	* - rows => number of rows
	* - info[name] => an array with columns names
	* - info[type] => an array with columns types
	*
	* @param resource	$result		Resource result
	* @param array		$info		Information array
	*/
	public function __construct($result,$info)
	{
		$this->__result = $result;
		$this->__info = $info;
		$this->__link = $info['con']->link();
		$this->index(0);
	}
	
	/**
	* To staticRecord
	*
	* Converts this record to a {@link staticRecord} instance.
	*/
	public function toStatic()
	{
		if ($this instanceof staticRecord) {
			return $this;
		}
		return new staticRecord($this->__result,$this->__info);
	}
	
	/**
	* Magic call
	*
	* Magic call function. Calls function added by {@link extend()} if exists, passing it
	* self object and arguments.
	*
	* @return mixed
	*/
	public function __call($f,$args)
	{
		if (isset($this->__extend[$f]))
		{
			array_unshift($args,$this);
			return call_user_func_array($this->__extend[$f],$args);
		}
		
		trigger_error('Call to undefined method record::'.$f.'()',E_USER_ERROR);
	}
	
	/**
	* Magic get
	*
	* Alias for {@link field()}.
	*
	* @param string|integer	$n		Field name
	* @return string
	*/
	public function __get($n)
	{
		return $this->field($n);
	}
	
	/**
	* Get field
	*
	* Alias for {@link field()}.
	*
	* @param string|integer	$n		Field name
	* @return string
	*/
	public function f($n)
	{
		return $this->field($n);
	}
	
	/**
	* Get field
	*
	* Retrieve field value by its name or column position.
	*
	* @param string|integer	$n		Field name
	* @return string
	*/
	public function field($n)
	{
		return $this->__row[$n];
	}
	
	/**
	* Field exists
	*
	* Returns true if a field exists.
	*
	* @param string		$n		Field name
	* @return string
	*/
	public function exists($n)
	{
		return isset($this->__row[$n]);
	}
	
	/**
	* Extend record
	*
	* Extends this instance capabilities by adding all public static methods of
	* <var>$class</var> to current instance. Class methods should take at least
	* this record as first parameter.
	*
	* @see __call()
	*
	* @param string	$class		Class name
	*/
	public function extend($class)
	{
		if (!class_exists($class)) {
			return;
		}
		
		$c = new ReflectionClass($class);
		foreach ($c->getMethods() as $m) {
			if ($m->isStatic() && $m->isPublic()) {
				$this->__extend[$m->name] = array($class,$m->name);
			}
		}
	}
	
	/**
	* Returns record extensions.
	* 
	* @return			<b>array</b>
	*/
	public function extensions()
	{
		return $this->__extend;
	}
	
	private function setRow()
	{
		$this->__row = $this->__info['con']->db_fetch_assoc($this->__result);
		
		if ($this->__row !== false)
		{
			foreach ($this->__row as $k => $v) {
				$this->__row[] =& $this->__row[$k];
			}
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	* Returns the current index position (0 is first) or move to <var>$row</var> if
	* specified.
	*
	* @param integer	$row			Row number to move
	* @return integer
	*/
	public function index($row=null)
	{
		if ($row === null) {
			return $this->__index === null ? 0 : $this->__index;
		}
		
		if ($row < 0 || $row+1 > $this->__info['rows']) {
			return false;
		}
		
		if ($this->__info['con']->db_result_seek($this->__result,(integer) $row))
		{
			$this->__index = $row;
			$this->setRow();
			$this->__info['con']->db_result_seek($this->__result,(integer) $row);
			return true;
		}
		return false;
	}
	
	/**
	* One step move index
	*
	* This method moves index forward and return true until index is not
	* the last one. You can use it to loop over record. Example:
	* <code>
	* <?php
	* while ($rs->fetch()) {
	* 	echo $rs->field1;
	* }
	* ?>
	* </code>
	*
	* @return boolean
	*/
	public function fetch()
	{
		if (!$this->__fetch) {
			$this->__fetch = true;
			$i = -1;
		} else {
			$i = $this->__index;
		}
		
		if (!$this->index($i+1)) {
			$this->__fetch = false;
			$this->__index = 0;
			return false;
		}
		
		return true;
	}
	
	/**
	* Moves index to first position.
	*
	* @return boolean
	*/
	public function moveStart()
	{
		return $this->index(0);
	}
	
	/**
	* Moves index to last position.
	*
	* @return boolean
	*/
	public function moveEnd()
	{
		return $this->index($this->__info['rows']-1);
	}
	
	/**
	* Moves index to next position.
	*
	* @return boolean
	*/
	public function moveNext()
	{
		return $this->index($this->__index+1);
	}
	
	/**
	* Moves index to previous position.
	*
	* @return boolean
	*/
	public function movePrev()
	{
		return $this->index($this->__index-1);
	}
	
	/**
	* @return boolean	true if index is at last position
	*/
	public function isEnd()
	{
		return $this->__index+1 == $this->count();
	}
	
	/**
	* @return boolean	true if index is at first position.
	*/
	public function isStart()
	{
		return $this->__index <= 0;
	}
	
	/**
	* @return boolean	true if record contains no result.
	*/
	public function isEmpty()
	{
		return $this->count() == 0;
	}
	
	/**
	* @return integer	number of rows in record
	*/
	public function count()
	{
		return $this->__info['rows'];
	}
	
	/**
	* @return array	array of columns, with name as key and type as value.
	*/
	public function columns()
	{
		return $this->__info['info']['name'];
	}
	
	/**
	* @return array	all rows in record.
	*/
	public function rows()
	{
		return $this->getData();
	}
	
	/**
	* All data
	*
	* Returns an array of all rows in record. This method is called by rows().
	*
	* @return array
	*/
	protected function getData()
	{
		$res = array();
		
		if ($this->count() == 0) {
			return $res;
		}
		
		$this->__info['con']->db_result_seek($this->__result,0);
		while (($r = $this->__info['con']->db_fetch_assoc($this->__result)) !== false) {
			foreach ($r as $k => $v) {
				$r[] =& $r[$k];
			}
			$res[] = $r;
		}
		$this->__info['con']->db_result_seek($this->__result,$this->__index);
		
		return $res;
	}
}

/**
* Query Result Static Record Class
*
* Unlike record class, this one contains all results in an associative array.
*
* @package Clearbricks
* @subpackage DBLayer
*/
class staticRecord extends record
{
	/** @var array		Data array */
	public $__data = array();
	
	private $__sortfield;
	private $__sortsign;
	
	/** @ignore */
	public function __construct($result,$info)
	{
		if (is_array($result))
		{
			$this->__info = $info;
			$this->__data = $result;
		}
		else
		{
			parent::__construct($result,$info);
			$this->__data = parent::getData();
		}
		
		unset($this->__link);
		unset($this->__result);
	}
	
	/**
	* Static record from array
	*
	* Returns a new instance of object from an associative array.
	*
	* @param array		$data		Data array
	* @return staticRecord
	*/
	public static function newFromArray($data)
	{
		if (!is_array($data)) {
			$data = array();
		}
		
		$data = array_values($data);
		
		if (empty($data) || !is_array($data[0])) {
			$cols = 0;
		} else {
			$cols = count($data[0]);
		}
		
		$info = array(
			'con' => null,
			'info' => null,
			'cols' => $cols,
			'rows' => count($data)
		);
		
		return new self($data,$info);
	}
	
	/** @ignore */
	public function field($n)
	{
		return $this->__data[$this->__index][$n];
	}
	
	/** @ignore */
	public function exists($n)
	{
		return isset($this->__data[$this->__index][$n]);
	}
	
	/** @ignore */
	public function index($row=null)
	{
		if ($row === null) {
			return $this->__index;
		}
		
		if ($row < 0 || $row+1 > $this->__info['rows']) {
			return false;
		}
		
		$this->__index = $row;
		return true;
	}
	
	/** @ignore */
	public function rows()
	{
		return $this->__data;
	}
	
	/**
	* Changes value of a given field in the current row.
	*
	* @param string	$n			Field name
	* @param string	$v			Field value
	*/
	public function set($n,$v)
	{
		if ($this->__index === null) {
			return false;
		}
		
		$this->__data[$this->__index][$n] = $v;
	}
	
	/**
	* Sorts values by a field in a given order.
	* 
	* @param string	$field		Field name
	* @param string	$order		Sort type (asc or desc)
	*/
	public function sort($field,$order='asc')
	{
		if (!isset($this->__data[0][$field])) {
			return false;
		}
		
		$this->__sortfield = $field;
		$this->__sortsign = strtolower($order) == 'asc' ? 1 : -1;
		
		usort($this->__data,array($this,'sortCallback'));
		
		$this->__sortfield = null;
		$this->__sortsign = null;
	}
	
	private function sortCallback($a,$b)
	{
		$a = $a[$this->__sortfield];
		$b = $b[$this->__sortfield];
		
		# Integer values
		if ($a == (string) (integer) $a && $b == (string) (integer) $b) {
			$a = (integer) $a;
			$b = (integer) $b;
			return ($a - $b) * $this->__sortsign;
		}
		
		return strcmp($a,$b) * $this->__sortsign;
	}
}
?>