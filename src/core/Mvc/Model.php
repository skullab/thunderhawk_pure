<?php

namespace Thunderhawk\Mvc;

use Thunderhawk\Mvc\Model\ModelInterface;
use Thunderhawk\Di\InjectionInterface;
use Thunderhawk\Di\ContainerInterface;
use Thunderhawk\Utils;
use Thunderhawk\Mvc\Model\MetaData;
use Thunderhawk\Db\Database;
use Thunderhawk\Mvc\Model\Resultset;

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
	protected $primary_key = null ;
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
		if(!is_null($this->primary_key))return $this->primary_key ;
		if(false !== $key = $this->_metadata->getPrimaryKeyName()){
			$reflect = new \ReflectionProperty(get_class($this), $key);
			if(!$reflect->isPublic()){
				$reflect->setAccessible(true);
			}
			return $reflect->getValue($this);
		}
		return null ;
	}
	protected function setPrimaryKey($value){
		if(false !== $key = $this->_metadata->getPrimaryKeyName()){
			$reflect = new \ReflectionProperty(get_class($this), $key);
			if(!$reflect->isPublic()){
				$reflect->setAccessible(true);
			}
			$reflect->setValue($this, $value);
			$this->primary_key = $value ;
			return true ;
		}
		return false ;
	}
	protected function computeRecordDifference(){
		$compute = array() ;
		
		foreach ($this->_metadata->getNames() as $name){
			if($this->isPrimaryKey($name)){
				$compute[$name] = $this->getPrimaryKey() ;
			}else{
				$compute[$name] = $this->{$name} ;
			}
		}
		
		return array_diff($compute, $this->_record);
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
	public function setTableName($name) {
		$this->_table = ( string ) $name;
	}
	public function getSchema() {
		return $this->_schema;
	}
	public function setSchema($name) {
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
		return $this->_connections [self::CON_READ];
	}
	public function getWriteConnectionService() {
		return $this->_connections [self::CON_WRITE];
	}
	protected function resolveConnectionService($type) {
		$con = $this->getConnectionService ();
		switch ($type){
			case self::CON_READ:
				$con = $this->getReadConnectionService() ? $this->getReadConnectionService() : $con ;
				break;
			case self::CON_WRITE:
				$con = $this->getWriteConnectionService() ? $this->getWriteConnectionService() : $con ;
				break;
		}
		return $con ;
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
		$statement = $class->resolveConnectionService(self::CON_READ)->prepare($sql);
		$statement->setFetchMode(Database::FETCH_CLASS | Database::FETCH_PROPS_LATE,$class_name);
		
		if(!$statement->execute())return null ;
		
		$resultset = new Resultset();
		while($obj = $statement->fetch()){
			$obj->reset();
			$resultset[] = $obj ;
		}
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
	public static function query($dependencyInjector) {
		// TODO: Auto-generated method stub
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
		return (int)$statement->fetchColumn() ;
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
	
	public function save($data = null, $whiteList = null) {
		if($this->getPrimaryKey())return $this->update($data,$whiteList);
		return $this->create($data,$whiteList);
	}
	
	public function create($data = null, $whiteList = null) {
		
		if($data){
			foreach ($data as $parameter => $value){
				$this->{$parameter} = $value ;
			}
		}
		
		$sql = "INSERT INTO " .Database::encapsulateProperty($this->getTableName()).
		"(".implode(',',Database::encapsulateProperty($this->_metadata->getNames())).
		") VALUES (".Database::implodeBindValues($this->_metadata->getColumnsCount()).")" ;
		
		$interrupt = $this->onCreate($this->_record);
		if($interrupt === false)return false;
		
		$this->reset();
		
		$statement = $this->resolveConnectionService(self::CON_WRITE)->prepare($sql);
		
		$n = 1 ;
		foreach ($this->_record as $i => $column){
			//var_dump($n,$this->_record[$i],$this->_metadata->getType($i,MetaData::PDO_TYPE)[0]);
			$statement->bindParam($n,$this->_record[$i],$this->_metadata->getType($i,MetaData::PDO_TYPE)[0]);
			$n++ ;
		}
		$response = $statement->execute();
		if($response !== false){
			$response = $this->setPrimaryKey($this->resolveConnectionService(self::CON_WRITE)->lastId());
			if($response !== false)$this->reset();
		}
		return $response ;
	}
	
	public function update($data = null, $whiteList = null) {
		
		if($data){
			foreach ($data as $parameter => $value){
				$this->{$parameter} = $value ;
			}
		}
		
		$recordDiff = $this->computeRecordDifference();
		if(empty($recordDiff)){
			//nothing to update
			var_dump('nothing to update');
			return false ;
		}
		
		$interrupt = $this->onUpdate($recordDiff);
		if($interrupt === false)return false ;
		
		$info = $this->_metadata->getConciseInfo();
		$sql = 'UPDATE `'.$this->getTableName().'` SET'.PHP_EOL ;
		foreach ($recordDiff as $column => $value){
			$sql .= '`'.$column . '` = ' ;
			if($this->isPrimaryKey($column)){
				$sql .= $this->getPrimaryKey() ;
			}else
			if($info[$column]['type'] == 'VAR_STRING'){
				$sql .= "'$value'" ;
			}else{
				$sql .= $value ;
			}
			$sql .= ',' ;
		}
		$sql = rtrim($sql,',').PHP_EOL;
		$sql .= 'WHERE `'.$this->_metadata->getPrimaryKeyName().'` = '.$this->getPrimaryKey();
		var_dump($sql);
		$response = $this->resolveConnectionService(self::CON_WRITE)->execute($sql);
		if($response !== false)$this->reset();
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
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\ModelInterface::refresh()
	 */
	public function refresh() {
		if(!is_null($this->getPrimaryKey())){
			$refreshed = $this->_prepareFind($this->getPrimaryKey(),true);
		}
	}
	
	/*
	 * (non-PHPdoc)
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
	public function undo(){
		foreach ($this->_record as $parameter => $value){
			$this->{$parameter} = $value ;
		}
	}
	
	public function setDi(ContainerInterface $di) {
		$this->_di = $di;
	}
	public function getDi() {
		return $this->_di;
	}
	public function serialize() {
		return serialize($this->_record);
	}
	public function unserialize($data) {
		$this->_record = unserialize($data);
	}
	public function toArray(array $columns =  array()) {
		$this->reset();
		$ret = array() ;
		$columns = empty($columns) ? $this->_metadata->getNames() : $columns ;
		foreach ($columns as $colum){
			if(in_array($colum, $this->_metadata->getNames())){
				$ret[$colum] = $this->_record[$colum] ;
			}
		}
		return $ret ;
	}

}