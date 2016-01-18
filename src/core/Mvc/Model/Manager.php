<?php

namespace Thunderhawk\Mvc\Model;
use Thunderhawk\Di\InjectionInterface;
use Thunderhawk\Events\EventsAwareInterface;
use Thunderhawk\Di\ContainerInterface;
use Thunderhawk\Events\Manager\ManagerInterface;

class Manager implements InjectionInterface,EventsAwareInterface{
	
	protected $_models = array();
	protected $_initializedModels = array();
	protected $_criterias = array();
	protected $_queries = array();
	protected $_records = array();
	protected $_lastQuery ;
	protected $_di ;
	protected $_eventsManager ;
	
	public function isLoaded($modelName){
		return isset($this->_models[$modelName]);
	}
	public function load($modelName,$instance = null){
		if($this->isLoaded($modelName))return;
		var_dump('manager load '.$modelName);
		$this->_models[$modelName] = $instance ? $instance : new $modelName($this->getDi()) ;
		$this->_queries[$modelName] = array();
		$this->_records[$modelName] = $this->_models[$modelName]->toArray();
		$this->_initializedModels[$modelName] = false ;
	}
	public function initialize($modelName){
		if($this->isInitialized($modelName))return ;
		var_dump('manager initialize ',$modelName);
		$this->_models[$modelName]->initialize();
		$this->_initializedModels[$modelName] = true ;
	}
	public function isInitialized($modelName){
		if(!array_key_exists($modelName, $this->_initializedModels))return false;
		return $this->_initializedModels[$modelName] ;
	}
	public function criteriaExists($conditions){
		foreach ($this->_criterias as $criteria){
			if($criteria->getConditions() == $conditions)return true ;
		}
		return false ;
	}
	public function getLastCriteria(){
		return end($this->_criterias);
	}
	public function getLastModel(){
		return end($this->_models);
	}
	public function getLastQuery(){
		return $this->_lastQuery ;
	}
	public function getModel($modelName){
		if($this->isLoaded($modelName)){
			return $this->_models[$modelName] ;
		}
		return null ;	
	}
	public function saveModelCriteria($modelName,Criteria $criteria){
		$this->_criterias[$modelName] = $criteria ;
	}
	public function modelCriteriaExists($modelName,$conditions){
		if($this->isLoaded($modelName))return false ;
		$criteria = $this->getModelCriteria($modelName);
		if(is_null($criteria))return false;
		return $criteria->getConditions() == $conditions ;
	}
	public function getModelCriteria($modelName){
		if(!isset($this->_criterias[$modelName]))return null ;
		return $this->_criterias[$modelName] ;
	}
	public function getRecord($modelName){
		if(!$this->isLoaded($modelName))return null ;
		return $this->_records[$modelName] ;
	}
	public function saveRecord($modelName,array $record){
		if(!$this->isLoaded($modelName))return false ;
		$this->_records[$modelName] = $record ;
		return true ;
	}
	public function saveQuery($modelName,$query){
		if(!$this->isLoaded($modelName))return false ;
		$this->_lastQuery = $query ;
		if($this->queryExists($modelName, $query))return false;
		$this->_queries[$modelName][] = $query ;
		return true ;
	}
	public function getQueries($modelName){
		if(!$this->isLoaded($modelName))return null ;
		return $this->_queries[$modelName] ;
	}
	public function queryExists($modelName,$query){
		if(false === array_search($query, $this->_queries[$modelName]))return false;
		return true ;
	}
	public function setDi(ContainerInterface $di) {
		$this->_di = $di ;
	}

	
	public function getDi() {
		return $this->_di;
	}

	public function setEventsManager(ManagerInterface $eventsManager) {
		$this->_eventsManager = $eventsManager ;
	}

	public function getEventsManager() {
		return $this->_eventsManager ;
	}

}