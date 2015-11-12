<?php

namespace Thunderhawk\Mvc\Model;
use Thunderhawk\Di\InjectionInterface;
use Thunderhawk\Di\ContainerInterface;
use Thunderhawk\Mvc\Model;
use Thunderhawk\Mvc\Model\ModelInterface;

class MetaData implements InjectionInterface{
	//s
	const NATIVE_TYPE = 'native_type' ;
	const PDO_TYPE = 'pdo_type' ;
	const BOTH_TYPE = 'both_type' ;
	const FLAGS = 'flags' ;
	const TABLE = 'table';
	const NAME = 'name';
	const LEN = 'len';
	const PRECISION = 'precision';
	//
	const PRIMARY_KEY = 'primary_key';
	const UNIQUE_KEY = 'unique_key';
	const MULTIPLE_KEY = 'multiple_key';
	//	
	protected $di ;
	protected $model ;
	protected $info = array() ;
	protected $column_count = 0;
	protected $keys = array(self::PRIMARY_KEY,self::UNIQUE_KEY,self::MULTIPLE_KEY);
	
	public function __construct(ModelInterface $model){
		$this->model = $model ;
		$this->setDi($model->getDi());
		$this->retrieveInfo();
	}
	public function getColumnsCount(){
		return $this->column_count ;
	}
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
	public function getConciseInfo($type = self::NATIVE_TYPE){
		$concise = array() ;
		foreach ($this->getNames() as $index => $name){
			$concise[$name] = array() ;
			$concise[$name]['type'] = $this->getType($index,$type)[0] ;
			$concise[$name]['len'] = $this->getLen($index);
			$concise[$name]['flags'] = $this->getFlag($index);
		}
		return $concise ;
	}
	public function retrieveKeys(){
		$keys = array();
		foreach ($this->getConciseInfo() as $name => $column){
			foreach ($column['flags'] as $flag){
				if(in_array($flag,$this->keys)){
					$keys[$flag][] = $name ;
				}
			}
		}
		return $keys ;
	}
	public function getPrimaryKeyName(){
		if(array_key_exists(self::PRIMARY_KEY,$this->retrieveKeys())){
			return $this->retrieveKeys()[self::PRIMARY_KEY][0] ;
		}
		return false ;
	}
	public function getUniqueKeysName(){
		if(array_key_exists(self::UNIQUE_KEY, $this->retrieveKeys())){
			return $this->retrieveKeys()[self::UNIQUE_KEY];
		}
		return false ;
	}
	public function getMultipleKeysName(){
		if(array_key_exists(self::MULTIPLE_KEY, $this->retrieveKeys())){
			return $this->retrieveKeys()[self::MULTIPLE_KEY];
		}
		return false ;
	}
	private function retrieveInfo(){
		$sql = "SELECT * FROM `".$this->model->getTableName()."` LIMIT 0" ;
		//var_dump($sql);
		$statement = $this->getDi()->db->query($sql);
		if(!$statement)return false ;
		$this->column_count = $statement->columnCount();
		for($i = 0 ; $i < $this->column_count ; $i++){
			$this->info[] = $statement->getColumnMeta($i);
		}
	}
	public function setDi(ContainerInterface $di) {
		$this->di = $di ;
	}
	
	public function getDi(){
		return $this->di ;
	}
}