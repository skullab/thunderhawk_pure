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
	
	public function getRandomConnection($tag = null,$diff = null){
		$tags = $diff ? array_values($diff) : $this->map->getTags() ;
		$target = $tag ? $tag : $tags[mt_rand(0,count($tags)-1)] ;
		//var_dump($target);
		if(($c = $this->map->count($target)) <= 0){
			if($target == $tag)throw new PoolException('No connections available');
			$i = array_keys($tags,$target) ;
			//var_dump($i);
			unset($tags[$i[0]]);
			//var_dump($tags);
			return $this->getRandomConnection($tag,$tags);
		}
		$index = mt_rand(0,$c-1);
		return $this->map->getConnector($target,$index);
	}
}