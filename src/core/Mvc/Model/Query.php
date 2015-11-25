<?php
namespace Thunderhawk\Mvc\Model;
use Thunderhawk\Mvc\Model\Criteria;
use Thunderhawk\Mvc\Model\Query\QueryInterface;
use Thunderhawk\Di\InjectionInterface;

class Query extends Criteria implements QueryInterface{
		const INSERT_INTO = "INSERT INTO ";
	const VALUES = " VALUES ";
	const UPDATE = "UPDATE ";
	const SET = " SET ";
	const DELETE_FROM = "DELETE FROM ";
	
	protected $_query ;
	
	public function resolveQuery() {
		return $this->_query.$this->getWhere() ;
	}

	public function create($sql) {
		$this->_query = (string)$sql ;
		return $this ;
	}
	
	public function insert(array $columns,array $values = array()){
		$this->_query = self::INSERT_INTO.$this->getModelName() ;
	}
	public function update(array $columns,array $values = array()){
		$this->_query = self::UPDATE.$this->getModelName().self::SET ;
	}
	public function delete(){}
	
	public function execute(array $bindParams = array(), array $bindTypes = array()) {
		if(!empty($bindParams))$this->bind($bindParams);
		if(!empty($bindTypes))$this->bindTypes($bindTypes);
		$response = parent::execute();
		if($response instanceof Resultset)return true;
		return false;
	}

}