<?php

namespace Thunderhawk\Db\PDO;

use Thunderhawk\Db\PDO;

class Mysql extends PDO {
	
	const PREFIX = 'mysql' ;
	
	protected function prepareConnection($settings = null) {
		if($this->is_connected)return;
		$this->settings = is_null($settings) ? $this->settings : (array)$settings ;
		if(is_null($this->settings)){
			$message = 'Mysql - Impossible to connect : no settings available' ;
			throw new \PDOException($message);
		}
		
		$this->username = $this->settings['username'] ;
		unset($this->settings['username']);
		$this->password = $this->settings['password'] ;
		unset($this->settings['password']);
		
		$this->resolveDsn(self::PREFIX,$this->settings);
	}
	
}