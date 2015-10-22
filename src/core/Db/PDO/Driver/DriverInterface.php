<?php

namespace Thunderhawk\Db\PDO\Driver;
use Thunderhawk\Db\PDO\Dsn;

interface DriverInterface {
	public static function connect(Dsn $dsn,$options = array());
	public static function disconnect();
}