<?php
namespace Thunderhawk\Mvc\Model;
use Thunderhawk\Mvc\Model\Criteria;
use Thunderhawk\Mvc\Model\Query\QueryInterface;
use Thunderhawk\Di\InjectionInterface;

class Query extends Criteria implements QueryInterface{
	const INSERT = " INSERT ";	const INSERT_INTO = " INSERT INTO ";
	const LOW_PRIORITY = " LOW PRIORITY ";
	const DELAYED = " DELAYED ";
	const HIGH_PRIORITY = " HIGH PRIORITY ";
	const IGNORE = " IGNORE ";
	const PARTITION = " PARTITION ";
	const VALUES = " VALUES ";
	const VALUE = " VALUE ";
	const ON_DUPLICATE_KEY_UPDATE = " ON DUPLICATE KEY UPDATE ";
	const UPDATE = " UPDATE ";
	const SET = " SET ";
	const DELETE = " DELETE ";
	const DELETE_FROM = " DELETE FROM ";
	
	protected $_query ;
	
	public function resolveQuery() {
		return $this->stripString($this->_query.$this->getWhere());
	}

	public function create($sql) {
		$this->_query = (string)$sql ;
		return $this ;
	}
	public function lastId(){
		$this->_lastConnection = $this->_lastConnection ? $this->_lastConnection : $this->getDi()->db ;
		return $this->_lastConnection->lastId();
	}
	public function insert(array $columns,array $values = array()){
		$this->_query = self::INSERT_INTO.$this->getModelName() ;
		
		if(!empty($values)){
			$columns = array_combine(array_keys($columns), $values);
		}
		
		$this->_query .= " (" ;
		foreach ($columns as $column => $value){
			$this->_query .= "$column," ;
		}
		$this->_query = rtrim($this->_query,",").")".self::VALUES."(";
		foreach ($columns as $column => $value){
			$value = is_null($value) ? 'NULL' : $value ;
			$this->_query .= "$value,";
		}
		$this->_query = rtrim($this->_query,",").")";
		
		return $this ;
	}
	public function update(array $columns,array $values = array()){
		$this->_query = self::UPDATE.$this->getModelName().self::SET ;
		if(!empty($values)){
			$columns = array_combine(array_keys($columns), $values);
		}
		foreach ($columns as $column => $value){
			$value = is_null($value) ? 'NULL' : $value ;
			$this->_query .= "$column=$value,";
		}
		$this->_query = rtrim($this->_query,",");
		return $this ;
	}
	public function delete(){
		$this->_query = self::DELETE_FROM.$this->getModelName();
		return $this ;
	}
	
	public function execute(array $bindParams = array(), array $bindTypes = array()) {
		if(!empty($bindParams))$this->bind($bindParams);
		if(!empty($bindTypes))$this->bindTypes($bindTypes);
		$response = parent::execute(true);
		if($response instanceof Resultset){
			return ($response->count() == 0 ? true : $response);
		}
		return false;
	}

}