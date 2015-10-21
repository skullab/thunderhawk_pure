<?php

namespace Thunderhawk\Mvc\Model;
use Thunderhawk\Di\InjectionInterface;
use Thunderhawk\Di\Container;
use Thunderhawk\Mvc\Model;

class MetaData implements InjectionInterface{
	
	const NATIVE_TYPE = 'native_type' ;
	const PDO_TYPE = 'pdo_type' ;
	const BOTH_TYPE = 'both_type' ;
	const FLAGS = 'flags' ;
	const TABLE = 'table';
	const NAME = 'name';
	const LEN = 'len';
	const PRECISION = 'precision';
			
	protected $di ;
	protected $model ;
	protected $info = array() ;
	protected $column_count = 0 ;
	
	public function getColumns(){
		return $this->info ;
	}
	
	public function getTypes($mode = self::BOTH_TYPE){
		$types = array() ;
		for($i = 0 ; $i < $this->column_count ; $i++){
			$types[] = $this->getType($i,$mode);
		}
		return $types ;
	}
	public function getType($index,$mode = self::BOTH_TYPE){
		$type = array() ;
		$continue = false ;
		switch ($mode){
			case self::BOTH_TYPE:
				$continue = true ;
			case self::NATIVE_TYPE:
				$type[] = $this->info[(int)$index][self::NATIVE_TYPE];
				if(!$continue)break;
			case self::PDO_TYPE:
				$type[] = $this->info[(int)$index][self::PDO_TYPE];
				break;
		}
		return $type ;
	}
	public function getFlags(){
		$flags = array();
		for($i = 0 ; $i < $this->column_count ; $i++){
			$flags[] = $this->getFlag($i);
		}
		return $flags ;
	}
	public function getFlag($index){
		return $this->info[(int)$index][self::FLAGS];
	}
	public function getTable(){
		return $this->model->getTableName();
	}
	public function getNames(){
		$names = array();
		for($i = 0 ; $i < $this->column_count ; $i++){
			$names[] = $this->getName($i);
		}
		return $names ;
	}
	public function getName($index){
		return $this->info[$index][self::NAME];
	}
	public function getLens(){
		$lens = array();
		for($i = 0 ; $i < $this->column_count ; $i++){
			$lens[] = $this->getLen($i);
		}
		return $lens ;
	}
	public function getLen($index){
		return $this->info[$index][self::LEN];
	}
	public function getPrecisions(){
		$precisions = array();
		for($i = 0 ; $i < $this->column_count ; $i++){
			$precisions[] = $this->getPrecision($i);
		}
		return $precisions ;
	}
	public function getPrecision($index){
		return $this->info[$index][self::PRECISION];
	}
	
	private function __construct(Model $model){
		$this->model = $model ;
		$this->setDi($model->getDi());
		$this->retrieveInfo();
	}
	private function retrieveInfo(){
		$statement = $this->getDi()->db->query("SELECT * FROM ".$this->model->getTableName()." LIMIT 0");
		$this->column_count = $statement->columnCount();
		for($i = 0 ; $i < $this->column_count ; $i++){
			$this->info[] = $statement->getColumnMeta($i);
		}
	}
	public function setDi(Container $di) {
		$this->di = $di ;
	}
	
	public function getDi(){
		return $this->di ;
	}
	
	public static function extract(Model $model){
		return new MetaData($model);
	}
	
}