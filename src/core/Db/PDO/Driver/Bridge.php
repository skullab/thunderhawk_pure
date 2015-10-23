<?php

namespace Thunderhawk\Db\PDO\Driver;

use Thunderhawk\Db\PDO\Dsn;

abstract class Bridge implements DriverInterface {
	
	private static $_instance = null;
	private static $dsn = null ;
	private static $options = array() ;
	
	public static function connect(Dsn $dsn) {
		var_dump($dsn == self::$dsn);
		if (self::$_instance == null) {
			self::$dsn = $dsn ;
			//self::$options = $options;
			//var_dump('create connection');
			self::$_instance = new \PDO ( $dsn->resolve (), $dsn->user, $dsn->password, $dsn->options);
		}
		return self::$_instance;
	}
	public static function disconnect() {
		//var_dump('close connection');
		return self::$_instance = null;
	}
}