<?php

namespace Thunderhawk\Db\PDO;

use Thunderhawk\Db\PDO;

class Sqlite extends PDO {
	const PREFIX = 'sqlite' ;
	protected function prepareConnection($settings = null) {
		if($this->is_connected)return;
		$this->settings = is_null($settings) ? $this->settings : (array)$settings ;
		if(is_null($this->settings)){
			$message = 'Sqlite - Impossible to connect : no settings available' ;
			throw new \PDOException($message);
		}
		$this->dsn = self::PREFIX . ':' . $this->settings['dbname'] ;
	}
}