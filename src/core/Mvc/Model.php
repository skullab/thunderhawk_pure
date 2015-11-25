<?php

namespace Thunderhawk\Mvc;

use Thunderhawk\Mvc\Model\ModelInterface;
use Thunderhawk\Di\InjectionInterface;
use Thunderhawk\Di\ContainerInterface;
use Thunderhawk\Utils;
use Thunderhawk\Mvc\Model\MetaData;
use Thunderhawk\Db\Database;
use Thunderhawk\Mvc\Model\Resultset;
use Thunderhawk\Mvc\Model\Criteria;

class Model implements InjectionInterface, ModelInterface, \Serializable {
	//
	const CON_GLOBAL = 'global';
	const CON_READ = 'read';
	const CON_WRITE = 'write';
	//
	const TYPE_BOOL = \PDO::PARAM_BOOL;
	const TYPE_INT = \PDO::PARAM_INT;
	const TYPE_NULL = \PDO::PARAM_NULL;
	const TYPE_STRING = \PDO::PARAM_STR;
	const TYPE_LOB = \PDO::PARAM_LOB;
	const TYPE_STMT = \PDO::PARAM_STMT;
	const TYPE_INPUT_OUTPUT = \PDO::PARAM_INPUT_OUTPUT;
	//
	protected $_di;
	protected $_data;
	protected $_table;
	protected $_schema;
	protected $_connections = array (
			self::CON_GLOBAL => null,
			self::CON_WRITE => null,
			self::CON_READ => null 
	);
	protected $_metadata = null ;
	protected $_record = array() ;
	protected $_primary_key = null ;
	//
	final public function __construct(ContainerInterface $di = null) {
		$this->setTableName ( Utils::underscore ( basename ( get_class ( $this ) ) ) );
		if($di){
			$this->setDi($di);
		}else{
			//get from GLOBAL APP
		}
		$this->setConnectionService($this->getDi()->db);
		
		$this->_metadata = new MetaData($this);
		
		foreach ($this->_metadata->getNames() as $name){
			$this->_record[$name] = null ;
		}
		
		$this->initialize();
	}
	
	protected function readAttribute($name){
		try{
			$reflect = new \ReflectionProperty(get_class($this), $name);
		}catch(\ReflectionException $e){
			return null ;
		}
		
		if(!$reflect->isPublic()){
			$reflect->setAccessible(true);
		}
		$get = $reflect->getValue($this);
		$reflect = null ;
		return $get ;
	}
	protected function writeAttribute($name,$value){
		try{
			$reflect = new \ReflectionProperty(get_class($this), $name);
		}catch(\ReflectionException $e){
			return ;
		}
		
		if(!$reflect->isPublic()){
			$reflect->setAccessible(true);
		}
		$value = is_int($value) ? (int)$value : $value ;
		$reflect->setValue($this, $value);
		$reflect = null ;
	}
	public function __get($property){
		if(isset($property)){
			var_dump('get meta and obj '.$property);
			//try to call getter
			$response = call_user_func(array($this,'get'.ucfirst($property)));
			//get the property ;
			if($response === false){
				return $this->{$property} ;
			}else{
				return $response ;
			}
			
		}
		
	}
	public function __set($property,$value){
		if(isset($property)){
			var_dump('set meta and obj '.$value);
			//try to call setter
			$response = call_user_func_array(array($this,'set'.ucfirst($property)),array($value));
			if($response === false){
				$this->{$property} = $value ;
			}else{
				return $response ;
			}
			
		}
	}
	
	public function __isset($property){
		return in_array($property, $this->_metadata->getNames()) ;
	}
	
	public function __call($method,$args){
		return false ;
	}
	
	public static function __callStatic($method,$args){
		if((false !== ($pos = strpos($method,'findBy')) && $pos == 0)){
			$property = lcfirst(str_replace('findBy','',$method));
			return self::find("$property = '$args[0]'");
		}
		if((false !== ($pos = strpos($method,'findFirstBy')) && $pos == 0)){
			$property = lcfirst(str_replace('findFirstBy','',$method));
			return self::findFirst("$property = '$args[0]'");
		}
	}
	protected function isPrimaryKey($property){
		return $this->_metadata->getPrimaryKeyName() == $property ;
	}
	protected function getPrimaryKey(){
		if(!is_null($this->_primary_key))return $this->_primary_key ;
		if(false !== $key = $this->_metadata->getPrimaryKeyName()){
			return $this->readAttribute($key);
		}
		return null ;
	}
	protected function setPrimaryKey($value){
		if(false !== $key = $this->_metadata->getPrimaryKeyName()){
			$this->writeAttribute($key, (int)$value);
			$this->_primary_key = (int)$value ;
			return true ;
		}
		return false ;
	}
	protected function computeRecordDifference(array $data = null){
		$compute = array() ;
		foreach ($this->_metadata->getNames() as $name){
			if($this->isPrimaryKey($name)){
				$compute[$name] = $this->getPrimaryKey() ;
			}else{
				$compute[$name] = $this->{$name} ;
			}
		}
		
		$record = $data ? $data : $compute ;
		return array_diff($record, $this->_record);
	}
	protected function initialize() {
	}
	protected function onCreate($record) {
	}
	protected function onInsert() {
	}
	protected function onUpdate($recordDiff) {
	}
	protected function onDelete($record) {
	}
	protected function onSave() {
	}
	public function getTableName() {
		return $this->_table;
	}
	protected function setTableName($name) {
		$this->_table = ( string ) $name;
	}
	public function getSchema() {
		return $this->_schema;
	}
	protected function setSchema($name) {
		$this->_schema = $name;
	}
	public function getModelMetaData(){
		return $this->_metadata ;
	}
	public function setConnectionService($connectionService) {
		$this->_connections [self::CON_GLOBAL] = $connectionService;
	}
	public function setWriteConnectionService($connectionService) {
		$this->_connections [self::CON_WRITE] = $connectionService;
	}
	public function setReadConnectionService($connectionService) {
		$this->_connections [self::CON_READ] = $connectionService;
	}
	public function getConnectionService() {
		return $this->_connections [self::CON_GLOBAL];
	}
	public function getReadConnectionService() {
		return $this->resolveConnectionService(self::CON_READ);
	}
	public function getWriteConnectionService() {
		return $this->resolveConnectionService(self::CON_WRITE);
	}
	protected function resolveConnectionService($type) {
		switch ($type){
			case self::CON_READ:
				$con = !is_null($this->_connections[self::CON_READ]) ? $this->_connections[self::CON_READ] : $this->getConnectionService() ;
				//$con = $this->getReadConnectionService() ? $this->getReadConnectionService() : $con ;
				break;
			case self::CON_WRITE:
				$con = !is_null($this->_connections[self::CON_WRITE]) ? $this->_connections[self::CON_WRITE] : $this->getConnectionService() ;
				//$con = $this->getWriteConnectionService() ? $this->getWriteConnectionService() : $con ;
				break;
			default:
				$con = $this->getConnectionService ();
		}
		return $con ;
	}
	
	private static function getCalledModel(){
		$model_name = get_called_class();
		$model = new $model_name();
		return $model ;
	}
	
	private static function __prepareFind($parameters = false,$first = false){
		
		
	}
	private static function _prepareFind($parameters = null,$first = false){
		$class_name = get_called_class ();
		$class = new $class_name ();
		
		$sql = "SELECT * FROM `" . $class->getTableName () ."`". PHP_EOL;
		if($parameters){
			if(is_numeric($parameters)){
				//BY PRIMARY KEY
				if(false !== $key = $class->getModelMetaData()->getPrimaryKeyName()){
					$sql .= "WHERE `$key` = ".$parameters.PHP_EOL ;
				}
			}else
				if(is_string($parameters)){
				$sql .= 'WHERE '.$parameters.PHP_EOL ;
			}else
				if(is_array($parameters)){
				//COMPLEX WHERE CLAUSOLE
			}
		}
		if($first){
			$sql .= 'LIMIT 1' ;
		}
		var_dump($sql);
		$statement = $class->resolveConnectionService(self::CON_READ)->prepare($sql);
		$statement->setFetchMode(Database::FETCH_CLASS | Database::FETCH_PROPS_LATE,$class_name);
		
		$response = $statement->execute();
		
		if($response === false)return null ;
		
		$resultset = new Resultset();
		while($obj = $statement->fetch()){
			$obj->reset();
			$resultset[] = $obj ;
		}
		$statement->closeCursor();
		return $resultset ;
	}
	public static function find($parameters = null) {
		return self::_prepareFind($parameters);
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\ModelInterface::findFirst()
	 */
	public static function findFirst($parameters = null) {
		$resultset = self::_prepareFind($parameters,true);
		if(!$resultset)return null ;
		return $resultset[0] ;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\ModelInterface::query()
	 */
	public static function query($dependencyInjector = false) {
		return new Criteria(self::getCalledModel());
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\ModelInterface::count()
	 */
	public static function count($parameters = null) {
		$class_name = get_called_class ();
		$class = new $class_name ();
		$sql = 'SELECT COUNT(*) FROM '.$class->getTableName().PHP_EOL ;
		if($parameters){
			$sql .= 'WHERE '.$parameters.PHP_EOL ;
		}
		$statement = $class->resolveConnectionService(self::CON_READ)->query($sql);
		$count = (int)$statement->fetchColumn();
		$statement->closeCursor();
		return $count ;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\ModelInterface::sum()
	 */
	public static function sum($parameters) {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\ModelInterface::maximum()
	 */
	public static function maximum($parameters) {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\ModelInterface::minimum()
	 */
	public static function minimum($parameters) {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\ModelInterface::average()
	 */
	public static function average($parameters) {
		// TODO: Auto-generated method stub
	}
	
	public function save(array $data = null, array $whiteList = null) {
		if($this->getPrimaryKey())return $this->update($data,$whiteList);
		return $this->create($data,$whiteList);
	}
	
	public function create(array $data = null, array $whiteList = null) {
		
		$record = $data ? $data : $this->_record ;
		$interrupt = $this->onCreate($record);
		if($interrupt === false)return false;
		if(is_array($interrupt))$record = $interrupt ;
		
		$names = $whiteList ? $whiteList : array_keys($record) ;
		$bindCount = count($names) ;
		
		$sql = "INSERT INTO " .Database::encapsulateProperty($this->getTableName()).
		"(".implode(',',Database::encapsulateProperty($names)).
		") VALUES (".Database::implodeBindValues($bindCount).")" ;
		
		var_dump($sql);
		
		$statement = $this->resolveConnectionService(self::CON_WRITE)->prepare($sql);
		
		$n = 1 ;
		foreach ($record as $i => $value){
			//var_dump($n,$this->_record[$i],$this->_metadata->getType($i,MetaData::PDO_TYPE)[0]);
			$this->{$i} = $value ;
			$assign = $this->readAttribute($i);
			$index = array_search($i, $this->_metadata->getNames());
			if($index){
				$type = $this->_metadata->getType($index,MetaData::PDO_TYPE);
			}else{
				$type = array(\PDO::PARAM_NULL) ;
			}
			var_dump($n.') '.$i.' -> '.$assign);
			$statement->bindValue($n,$assign,$type[0]);
			if($n >= $bindCount)break;
			$n++ ;
		}
		
		$response = $statement->execute();
		$statement->closeCursor();
		if($response !== false){
			$response = $this->setPrimaryKey($this->resolveConnectionService(self::CON_WRITE)->lastId());
			if($response !== false){
				$this->reset();
			}
		}else{
			$this->undo();
		}
		return $response ;
	}
	
	public function update(array $data = null, array $whiteList = null) {
	
		//$record = $data ? $data : $this->_record ;
		//$names = $whiteList ? $whiteList : array_keys($record);
		
		$recordDiff = $this->computeRecordDifference($data);
		if($whiteList){
			$recordDiff = array_intersect_key($recordDiff, array_flip($whiteList));
		}
		if(empty($recordDiff)){
			//nothing to update
			var_dump('nothing to update');
			return false ;
		}
		
		$interrupt = $this->onUpdate($recordDiff);
		if($interrupt === false)return false ;
		
		$info = $this->_metadata->getConciseInfo();
		$sql = 'UPDATE `'.$this->getTableName().'` SET'.PHP_EOL ;
		
		$values = array();
		
		foreach ($recordDiff as $column => $value){
			
			if(!$this->isPrimaryKey($column)){
				$sql .= '`'.$column . '` = ? ,' ;
				$values[$column] = $value ;
			}
		}
		
		$sql = rtrim($sql,',').PHP_EOL;
		$sql .= 'WHERE `'.$this->_metadata->getPrimaryKeyName().'` = '.(int)$this->getPrimaryKey();
		var_dump($sql);
		$statement = $this->resolveConnectionService(self::CON_WRITE)->prepare($sql);
		$n = 1 ;
		var_dump($values);
		foreach ($values as $column => $value){
			$this->{$column} = $value ;
			$assign = $this->readAttribute($column);
			$index = array_search($column, $this->_metadata->getNames());
			if($index){
				$type = $this->_metadata->getType($index,MetaData::PDO_TYPE);
			}else{
				$type = array(\PDO::PARAM_NULL) ;
			}
			$statement->bindValue($n,$assign,$type[0]);
			$n++ ;
		}
		$response = $statement->execute();
		$statement->closeCursor();
		if($response !== false){
			$this->reset();
		}
		return $response ;
	}
	
	public function delete() {
		$sql = "DELETE FROM ".$this->getTableName().
		" WHERE ".$this->_metadata->getPrimaryKeyName()." = ".$this->getPrimaryKey() ;
		$this->reset();
		$interrupt = $this->onDelete($this->_record);
		if($interrupt === false)return false ;
		$response = $this->resolveConnectionService(self::CON_WRITE)->execute($sql);
		return $response ;
	}
	
	/**
	 * Reassign internal parameters by Database stored data
	 * and reset the internal record
	 * @see \Thunderhawk\Mvc\Model\ModelInterface::refresh()
	 */
	public function refresh() {
		$key = $this->getPrimaryKey();
		if(!is_null($key)){
			$refreshed = self::findFirst($key);
			if($refreshed){
				foreach ($refreshed->toArray() as $parameter => $value){
					$this->writeAttribute($parameter, $value);
				}
				$refreshed = null ;
			}
			$this->reset();
			return true ;
		}
		return false ;
	}
	
	/**
	 * Re-set the internal record
	 * @see \Thunderhawk\Mvc\Model\ModelInterface::reset()
	 */
	public function reset() {
		foreach ($this->_metadata->getNames() as $name){
			if($this->isPrimaryKey($name)){
				$this->_record[$name] = $this->getPrimaryKey() ;
			}else{
				$this->_record[$name] = $this->{$name} ;
			}
		}
	}
	/**
	 * Rewrite internal parameters using the previously record (or chached)
	 */
	protected function undo(array $cached = null){
		$record = $cached ? $cached : $this->_record ;
		foreach ($record as $parameter => $value){
			$this->writeAttribute($parameter, $value);
		}
	}
	
	public function setDi(ContainerInterface $di) {
		$this->_di = $di;
	}
	public function getDi() {
		return $this->_di;
	}
	public function serialize() {
		return serialize($this->toArray());
	}
	public function unserialize($data) {
		$this->_record = unserialize($data);
		$this->undo();
	}
	public function toArray(array $columns =  array()) {
		$this->reset();
		$ret = array() ;
		$columns = empty($columns) ? $this->_metadata->getNames() : $columns ;
		foreach ($columns as $column){
			if(in_array($column, $this->_metadata->getNames())){
				$ret[$column] = $this->_record[$column] ;
			}
		}
		return $ret ;
	}

}