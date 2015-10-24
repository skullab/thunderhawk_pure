<?php

namespace Thunderhawk\Db\PDO\Connection;
use Thunderhawk\Db\PDO\Connection\Map;
use Thunderhawk\Db\PDO\Dsn;
class Pool {
	private $map;
	
	public function __construct(Map $map = null) {
		$this->map = $map ? $map : new Map ();
	}
	
	/**
	 *
	 * @param string $which        	
	 * @param number $index        	
	 */
	public function getConnection($which = null, $index = 0) {
		$connector = null;
		if ($which instanceof Dsn) {
			var_dump('dsn');
			$found = false;
			foreach ( $this->map->getConnections () as $connections ) {
				foreach ( $connections as $connection ) {
					if ($connection->getDsn ()->equal ( $which )) {
						$connector = $connection;
						$found = true;
						break;
					}
				}
				if ($found)
					break;
			}
		} else if (is_string ( $which )) {
			var_dump ( 'ops' );
			$connector = $this->map->getConnection ( $which, $index );
		} else {
			var_dump('first');
			$connector = $this->map->getFirstAvailable ();
		}
		
		return $connector->connect() ;
	}
		
}