<?php

namespace Thunderhawk\Db\PDO\Driver;

use Thunderhawk\Db\PDO\Dsn;

abstract class Bridge implements DriverInterface {
	
	private static $_instance = null;
	private static $dsn = null ;

	public static function connect(Dsn $dsn) {
		var_dump($dsn == self::$dsn);
		if (self::$_instance == null) {
			self::$dsn = $dsn ;
			self::$_instance = new \PDO ( $dsn->resolve (), $dsn->getUser(), $dsn->getPassword(), $dsn->getOptions());
		}
		return self::$_instance;
	}
	public static function disconnect() {
		return self::$_instance = null;
	}
}