<?php

namespace Thunderhawk\Db\PDO\Connection;

use Thunderhawk\Db\PDO\Connection\Map;
use Thunderhawk\Db\PDO\Dsn;
use Thunderhawk\Db\PDO\Connection\Pool\PoolException;

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
			var_dump ( 'dsn' );
			$found = false;
			foreach ( $this->map->getConnectors() as $connectors ) {
				foreach ( $connectors as $c ) {
					if ($c->getDsn ()->equal ( $which )) {
						$connector = $c;
						$found = true;
						break;
					}
				}
				if ($found)
					break;
			}
		} else if (is_string ( $which )) {
			var_dump ( 'string' );
			$connector = $this->map->getConnector( $which, $index );
		} else {
			var_dump ( 'first' );
			$connector = $this->map->getFirstAvailable ();
		}
		if ($connector) {
			return $connector->connect ();
		}else{
			throw new PoolException('No connections available');
		}
	}
	
	public function getRandomConnection($tag = null){
		$tags = $this->map->getTags();
		$tag = $tag ? $tag : $tags[mt_rand(0,count($tags)-1)] ;
		$index = mt_rand(0,$this->map->count($tag)-1);
		return $this->map->getConnector($tag,$index);
	}
}