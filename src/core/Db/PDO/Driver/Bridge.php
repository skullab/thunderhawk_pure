<?php

namespace Thunderhawk\Db\PDO\Driver;

use Thunderhawk\Db\PDO\Dsn;

abstract class Bridge implements DriverInterface {
	
	private static $_instance = null;
	private static $dsn = null ;
	private static $options = array() ;
	
	public static function connect(Dsn $dsn, $options = array()) {
		if (self::$_instance == null) {
			self::$dsn = $dsn ;
			self::$options = $options;
			self::$_instance = new \PDO ( $dsn->resolve (), $dsn->user, $dsn->password, $options );
		}
		return self::$_instance;
	}
	public static function disconnect() {
		return self::$_instance = null;
	}
}