<?php

namespace Thunderhawk\Db\PDO;

class Dsn {
	const PREFIX_MYSQL = 'mysql';
	const PREFIX_SQLITE = 'sqlite';
	const PREFIX_SQLITE_2 = 'sqlite2';
	const PREFIX_ORACLE = 'oci';
	const PREFIX_POSTGRESQL = 'pgsql';
	private $by_driver;
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
	private function __construct($prefix, $parameters = array(), $by_driver = false) {
		$this->by_driver = $by_driver;
		$this->prefix = ( string ) $prefix;
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
			$this->{$key} = ( string ) $value;
		}
	}
	public function resolve() {
		$dsn = $this->prefix . ':';
		$dsn_oci = '';
		foreach ( get_object_vars ( $this ) as $name => $value ) {
			if (! is_null ( $value ) && $name != 'prefix' && $name != 'by_driver') {
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
									$dsn_oci .= '//'.$value;
									break;
								default :
									$dsn_oci .= '/'.$value;
							}
							$dsn = str_replace('///', '//', $this->prefix . ':dbname=' . $dsn_oci);
						}
						break;
					default :
						if ($this->prefix == self::PREFIX_POSTGRESQL || ! ($name == 'user' || $name == 'password'))
							$dsn .= $name != 'sqlite_param' ? $name . '=' . $value . ';' : $value;
				}
			}
		}
		return rtrim ( rtrim ( $dsn, ';' ), ':' );
	}
	public function __get($name) {
		return (isset ( $this->{$name} ) ? $this->{$name} : null);
	}
	public function __set($name, $value) {
		if (property_exists ( __CLASS__, $name ) && $this->by_driver)
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
		// var_dump($definition[0],$parameters);
		return new Dsn ( $definition [0], $parameters );
	}
	public static function createByDriver($prefix) {
		if (in_array ( $prefix, self::getPrefixes () )) {
			return new Dsn ( ( string ) $prefix, array (), true );
		}
	}
	public static function getPrefixes() {
		$o = new \ReflectionClass ( __CLASS__ );
		return $o->getConstants ();
	}
}