<?php

namespace Thunderhawk\Db\PDO;

class Dsn_old {
	const PREFIX_MYSQL = 'mysql';
	const PREFIX_SQLITE = 'sqlite';
	const PREFIX_SQLITE_2 = 'sqlite2';
	const PREFIX_ORACLE = 'oci';
	const PREFIX_POSTGRESQL = 'pgsql';
	protected $by_prefix;
	protected $is_generic;
	protected $tag;
	protected $options;
	// dsn declaration
	protected $prefix;
	protected $host;
	protected $port;
	protected $dbname;
	protected $unix_socket;
	// PHP 5.3.6 only
	protected $charset;
	// PostGreSQL only
	protected $user;
	protected $password;
	// SQLite only
	protected $sqlite_param;
	// ODBC Generic db
	protected $generic_param;
	private function __construct($prefix, $parameters = array(), $by_prefix = false, $is_generic = false) {
		$this->by_prefix = $by_prefix;
		$this->is_generic = $is_generic;
		$this->prefix = ( string ) $prefix;
		if ($this->is_generic)
			$this->generic_param = $parameters;
		if ($this->prefix == self::PREFIX_SQLITE || $this->prefix == self::PREFIX_SQLITE_2) {
			foreach ( $parameters as $key => $value ) {
				if (! is_numeric ( $key )) {
					$this->sqlite_param = ( string ) $key;
				} else {
					$this->sqlite_param = ( string ) $value;
				}
			}
			return;
		}
		foreach ( $parameters as $key => $value ) {
			$this->{$key} = $value;
		}
	}
	public function resolve() {
		$dsn = $this->prefix . ':';
		$dsn_oci = '';
		$skip = false;
		if ($this->is_generic) {
			foreach ( $this->generic_param as $key => $value ) {
				$dsn .= $key . '=' . $value . ';';
			}
			return rtrim ( rtrim ( rtrim ( $dsn, ';' ), ':' ), '=' );
		}
		foreach ( get_object_vars ( $this ) as $name => $value ) {
			if ($skip)
				break;
			if (! is_null ( $value ) && $name != 'tag' && $name != 'options' && $name != 'prefix' && $name != 'by_prefix' && $name != 'is_generic' && $name != 'generic_param') {
				switch ($this->prefix) {
					case self::PREFIX_ORACLE :
						if (! ($name == 'user' || $name == 'password')) {
							switch ($name) {
								case 'port' :
									$dsn_oci .= ':' . $value;
									break;
								case 'charset' :
									$dsn_oci .= ';' . $name . '=' . $value;
									break;
								case 'host' :
									$dsn_oci .= '//' . $value;
									break;
								default :
									$dsn_oci .= '/' . $value;
							}
							$dsn = str_replace ( '///', '//', $this->prefix . ':dbname=' . $dsn_oci );
						}
						break;
					
					case self::PREFIX_SQLITE_2 :
					case self::PREFIX_SQLITE :
						if (($name == 'dbname' || $name == 'sqlite_param')) {
							$dsn .= $value . ';';
							$skip = true;
						}
						break;
					default :
						if (($this->prefix == self::PREFIX_POSTGRESQL || ! ($name == 'user' || $name == 'password')) && $name != 'sqlite_param')
							$dsn .= $name . '=' . $value . ';';
				}
			}
		}
		return rtrim ( rtrim ( rtrim ( $dsn, ';' ), ':' ), '=' );
	}
	public function __get($name) {
		return (isset ( $this->{$name} ) ? $this->{$name} : null);
	}
	public function __set($name, $value) {
		if (property_exists ( __CLASS__, $name ) && $this->by_prefix && $name != 'prefix')
			$this->{$name} = $value;
	}
	public static function create($definition) {
		$parameters = array ();
		$definition = ( string ) $definition;
		$definition = explode ( ':', $definition, 2 );
		if (isset ( $definition [1] )) {
			$definition [1] = explode ( ';', $definition [1] );
			foreach ( $definition [1] as $def ) {
				$def = explode ( '=', $def );
				$parameters [$def [0]] = @$def [1];
			}
		}
		
		$is_generic = in_array ( $definition [0], self::getPrefixes () ) ? false : true;
		return new Dsn_old ( $definition [0], $parameters, false, $is_generic );
	}
	public static function createByPrefix($prefix) {
		if (in_array ( $prefix, self::getPrefixes () )) {
			return new Dsn_old ( ( string ) $prefix, array (), true );
		}
	}
	public static function createByArray(array $parameters) {
		if (! isset ( $parameters ['prefix'] ))
			return;
		$prefix = $parameters ['prefix'];
		unset ( $parameters ['prefix'] );
		$is_generic = in_array ( $prefix, self::getPrefixes () ) ? false : true;
		return new Dsn_old ( $prefix, $parameters, false, $is_generic );
	}
	public static function getPrefixes() {
		$o = new \ReflectionClass ( __CLASS__ );
		return $o->getConstants ();
	}
}