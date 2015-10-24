<?php

namespace Thunderhawk\Db\PDO;

use Thunderhawk\Db\PDO\Dsn\DsnException;

class Dsn {
	//
	const PREFIX_MYSQL = 'mysql';
	const PREFIX_SQLITE = 'sqlite';
	const PREFIX_SQLITE_2 = 'sqlite2';
	const PREFIX_ORACLE = 'oci';
	const PREFIX_POSTGRESQL = 'pgsql';
	const PREFIX_CUBRID = 'cubrid';
	const PREFIX_SYBASE = 'sybase';
	const PREFIX_MSSQL = 'mssql';
	const PREFIX_DBLIB = 'dblib';
	const PREFIX_FIREBIRD = 'firebird';
	const PREFIX_IBM = 'ibm';
	const PREFIX_INFORMIX = 'informix';
	const PREFIX_SQLSRV = 'sqlsrv';
	const PREFIX_ODBC = 'odbc';
	const PREFIX_4D = '4D';
	//
	protected static $global_rules = array (
			self::PREFIX_CUBRID => array (),
			self::PREFIX_MSSQL => array (),
			self::PREFIX_SYBASE => array (),
			self::PREFIX_DBLIB => array (),
			self::PREFIX_SQLSRV => array (),
			self::PREFIX_FIREBIRD => array (),
			self::PREFIX_IBM => array (),
			self::PREFIX_INFORMIX => array (),
			self::PREFIX_MYSQL => array (),
			self::PREFIX_ORACLE => array (),
			self::PREFIX_ODBC => array (),
			self::PREFIX_POSTGRESQL => array (),
			self::PREFIX_SQLITE => array (),
			self::PREFIX_SQLITE_2 => array (),
			self::PREFIX_4D => array ()
	);
	//
	protected $allowed_parameters = array (
			self::PREFIX_CUBRID => array (
					'host',
					'dbname',
					'charset',
					'appname',
					'secure' 
			),
			self::PREFIX_MSSQL => array (
					'host',
					'dbname',
					'charset',
					'appname',
					'secure' 
			),
			self::PREFIX_SYBASE => array (
					'host',
					'dbname',
					'charset',
					'appname',
					'secure' 
			),
			self::PREFIX_DBLIB => array (
					'host',
					'dbname',
					'charset',
					'appname',
					'secure' 
			),
			self::PREFIX_SQLSRV => array (
					'APP',
					'ConnectionPooling',
					'Database',
					'Encrypt',
					'Failover_Partner',
					'LoginTimeout',
					'MultipleActiveResultSets',
					'QuoteId',
					'Server',
					'TraceFile',
					'TraceOn',
					'TransactionIsolation',
					'TrustServerCertificate',
					'WSID' 
			),
			self::PREFIX_FIREBIRD => array (
					'dbname',
					'charset',
					'role' 
			),
			self::PREFIX_IBM => array (
					'database',
					'hostname',
					'port',
					'username',
					'password',
					'protocol',
					'uid',
					'pwd' 
			),
			self::PREFIX_INFORMIX => array (
					'dsn',
					'host',
					'service',
					'database',
					'server',
					'protocol',
					'EnableScrollableCursors' 
			),
			self::PREFIX_MYSQL => array (
					'host',
					'port',
					'dbname',
					'unix_socket',
					'charset' 
			),
			self::PREFIX_ORACLE => array (
					'dbname',
					'charset' 
			),
			self::PREFIX_ODBC => array (
					'database',
					'hostname',
					'port',
					'username',
					'password',
					'protocol',
					'uid',
					'pwd' 
			),
			self::PREFIX_POSTGRESQL => array (
					'host',
					'port',
					'dbname',
					'user',
					'password' 
			),
			self::PREFIX_SQLITE => array (
					'path' 
			),
			self::PREFIX_SQLITE_2 => array (
					'path' 
			),
			self::PREFIX_4D => array (
					'host',
					'port',
					'user',
					'password',
					'dbname' 
			) 
	);
	//
	protected static $CREATED_BY_STRING = 'created_by_string';
	protected $createdByString = false;
	protected $definition = array ();
	protected $tag;
	protected $prefix;
	protected $user, $username;
	protected $password;
	protected $options;
	protected $rules = array();
	//
	public function __construct(array $definition) {
		// check for prefix
		if (! isset ( $definition ['prefix'] ))
			throw new DsnException ( 'No prefix in definition' );
		$this->prefix = $definition ['prefix'];
		unset ( $definition ['prefix'] );
		// created by string
		if (array_key_exists ( self::$CREATED_BY_STRING, $definition )) {
			$this->createdByString = true;
			// definition became a string
			$this->definition = $definition [self::$CREATED_BY_STRING];
			return;
		}
		// is valid prefix ?
		if (! $this->createdByString && ! self::isValidPrefix ( $this->prefix ))
			throw new DsnException ( 'No valid prefix !' );
			// created by array
		if (isset ( $definition ['tag'] )) {
			$this->tag = $definition ['tag'];
			unset ( $definition ['tag'] );
		}
		if (isset ( $definition ['user'] ))
			$this->user = $definition ['user'];
		if (isset ( $definition ['username'] ))
			$this->username = $definition ['username'];
		if (isset ( $definition ['password'] ))
			$this->password = $definition ['password'];
		if (isset ( $definition ['options'] )) {
			$this->options = $definition ['options'];
			unset ( $definition ['options'] );
		}
		$this->definition = $definition;
	}
	public function getTag() {
		return $this->tag;
	}
	public function getUser() {
		if ($this->user)
			return $this->user;
		return $this->username;
	}
	public function getPassword() {
		return $this->password;
	}
	public function getOptions() {
		return $this->options;
	}
	public function getHost(){
		if(is_array($this->definition) && isset($this->definition['host'])){
			return $this->definition['host'];
		}
		return 'unknown' ;
	}
	public function addRule(callable $callable){
		$this->rules[] = $callable ;
	}
	public function __set($name, $value) {
		if ($this->createdByString) {
			$this->{$name} = $value;
		}
	}
	public function resolve() {
		$dsn = $this->prefix . ':';
		if ($this->createdByString) {
			$dsn .= $this->definition;
		} else {
			//apply global rules
			foreach (self::$global_rules[$this->prefix] as $func){
				if (! array_walk ( $this->definition, $func )) {
					throw new DsnException ( 'Global Wrong rule...' );
				}
			}
			// apply the rules
			foreach ( $this->rules as $func ) {
				if (! array_walk ( $this->definition, $func )) {
					throw new DsnException ( 'Wrong rule...' );
				}
			}
			foreach ( $this->definition as $key => $value ) {
				if (in_array ( $key, $this->allowed_parameters [$this->prefix] ))
					$dsn .= $key . '=' . $value . ';';
			}
		}
		return rtrim ( rtrim ( rtrim ( $dsn, ';' ), '=' ), ':' );
	}
	public function equal(Dsn $dsn){
		return $this == $dsn ;
	}
	public static function byStrig($definition) {
		$definition = explode ( ':', $definition, 2 );
		$definition = @array (
				'prefix' => $definition [0],
				self::$CREATED_BY_STRING => $definition [1] 
		);
		return new Dsn ( $definition );
	}
	public static function getPrefixes() {
		$o = new \ReflectionClass ( __CLASS__ );
		return $o->getConstants ();
	}
	public static function isValidPrefix($prefix) {
		return in_array ( $prefix, self::getPrefixes () );
	}
}