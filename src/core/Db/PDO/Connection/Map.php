<?php

namespace Thunderhawk\Db\PDO\Connection;
use Thunderhawk\Db\PDO\Connection\Connector ;

class Map extends \ArrayObject{
	
	const TAG_MASTER = 'master' ;
	const TAG_SLAVE = 'slave' ;
	const TAG_ANONYMOUS = 'anonymous' ;
	
	public function __construct(array $connections = array()){
		parent::__construct(array(
				self::TAG_MASTER => array(),
				self::TAG_SLAVE => array(),
				self::TAG_ANONYMOUS => array()
		));
		foreach ($connections as $connection){
			$this->add($connection);
		}
	}
	public function append($value){}
	
	public function add(Connector $connection){
		$tag = $connection->getDsn()->getTag() ;
		$tag = $tag ? $tag : self::TAG_ANONYMOUS ;
		if(!$this->offsetExists($tag)){
			$this->offsetSet($tag, array());
		}
		$map = $this->offsetGet($tag);
		if(!in_array($connection, $map)){
			$index = $tag .'_'.count($map);
			$map[$index] = $connection ;
		}
		$this->offsetSet($tag, $map);
	}
	
	public function __get($tag){
		if($this->offsetExists($tag)){
			return array_values($this->offsetGet($tag));
		}
	}
	public function getConnections(){
		return $this->getArrayCopy();
	}
	public function getConnectionsByTag($tag){
		if($this->offsetExists($tag))return $this->offsetGet($tag);
		return null ;
	}
	public function getConnection($tag,$index = 0){
		$connections = $this->__get($tag);
		if($connections)return $connections[$index] ;
		return null ;
	}
	public function getFirstAvailable(){
		foreach (array_values($this->getArrayCopy()) as $map){
			if(!empty($map)){
				$connection = array_values($map) ;
				return $connection[0] ;
			}
		}
		return null ;
	}
	public function count(){
		$n = 0 ;
		foreach ($this->getArrayCopy() as $map){
			$n += count($map);
		}
		return $n ;
	}
}