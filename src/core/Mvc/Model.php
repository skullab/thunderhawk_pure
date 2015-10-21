<?php

namespace Thunderhawk\Mvc;

use Thunderhawk\Di\Container;
use Thunderhawk\Di\InjectionInterface;
use Thunderhawk\Mvc\Model\MetaData;

abstract class Model implements InjectionInterface {
	
	protected $table_name;
	protected $di;
	public $metadata ;
	
	public function __construct() {
		global $di;
		$this->setTableName ( strtolower ( basename ( get_class ( $this ) ) ) );
		$this->setDi ( $di );
		$this->metadata = MetaData::extract($this);
	}
	public function setDi(Container $di) {
		$this->di = $di;
	}
	public function getDi() {
		return $this->di;
	}
	public function update(){
		if(property_exists($this, 'id') && (int)$this->id > 0){
			$sql = "UPDATE ".$this->getTableName()." SET " ;
			foreach ($this->metadata->getNames() as $name){
				$sql .= $name."='".$this->{$name}."'," ;
			}
			$sql = rtrim($sql,',');
			$sql .= " WHERE id = ".(int)$this->id ;
			var_dump($sql);
			return $this->getDi()->db->exec($sql);
			if(false === $count){
				
			}
		}else{
			var_dump('impossible update ');
		}
	}
	public function insert(){
		
	}
	public function delete(){
		
	}
	public function save(){
		
	}
	public static function find($parameters = array()) {
		$class_name = get_called_class ();
		$class = new $class_name ();
		$di = $class->getDi ();
		
		$sql = "SELECT * FROM " . $class->getTableName () . PHP_EOL;
		if (! empty ( $parameters )) {
			switch ($parameters) {
				case is_numeric ( $parameters ) :
					$sql .= "WHERE id = " . ( int ) $parameters;
					break;
				case is_string ( $parameters ) :
					$sql .= "WHERE " . $parameters;
					break;
				case is_array ( $parameters ) :
					
					break;
			}
		}
		$statement = $di->db->query ( $sql );
		
		$return = array ();
		/*$col = $statement->columnCount();
		for($i = 0 ; $i < $col ; $i++){
			var_dump($statement->getColumnMeta($i));
		}*/
		while ( false !== $o = $statement->fetchObject ( $class_name ) ) {
			$return [] = $o;
		}
		return $return;
	}
	protected function setTableName($name) {
		$this->table_name = ( string ) $name;
	}
	public function getTableName() {
		return $this->table_name;
	}
}