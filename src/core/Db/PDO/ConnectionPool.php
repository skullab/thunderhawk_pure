<?php

namespace Thunderhawk\Db\PDO;

use Thunderhawk\Db\PDO\Dsn;

class ConnectionPool {
	private static $configurations = array (
			'untagged' => array () 
	);
	private static $connections = array() ;
	
	public static function resolveConnection(Dsn $dsn, $stictly = false) {
		if ($dsn->tag != null) {
			$tag = $dsn->tag;
			if (!isset ( self::$configurations [$tag] ) || ! in_array ( $dsn, self::$configurations [$tag] )) {
				self::$configurations [$tag] [] = $dsn;
			}else{
				
			}
		} else {
			if (! in_array ( $dsn, self::$configurations ['untagged'] )) {
				self::$configurations ['untagged'] [] = $dsn;
			} else {
				
			}
		}
		var_dump ( self::$configurations );
	}
	
	public static function getConnection(Dsn $dsn,$strictly = false){
		
	}
	
	public static function getConnectionByTag($tag){
		
	}
}