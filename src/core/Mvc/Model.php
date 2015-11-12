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
	public $primary_key = null ;
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
			$this->{$property} = $value ;
		}
	}
	
	public function __isset($property){
		return in_array($property, $this->_metadata->getNames()) ;
	}
	
	public function __call($method,$args){
		return false ;
	}
	public function getPrimaryKey(){
		if(false !== $key = $this->_metadata->getPrimaryKeyName()){
			return $this->{$key};
		}
		return null ;
	}
	public function computeRecordDifference(){
		$compute = array() ;
		foreach ($this->_metadata->getNames() as $name){
			$compute[$name] = $this->{$name} ;
		}
		return array_diff($compute, $this->_record);
	}
	protected function initialize() {
	}
	protected function onCreate() {
	}
	protected function onInsert() {
	}
	protected function onUpdate() {
	}
	protected function onDelete() {
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
				//WHERE CLAUSOLE
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
		$statement->execute();
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
	public static function count($parameters) {
		// TODO: Auto-generated method stub
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
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\ModelInterface::save()
	 */
	public function save($data, $whiteList) {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\ModelInterface::create()
	 */
	public function create($data, $whiteList) {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\ModelInterface::update()
	 */
	public function update($data = null, $whiteList = null) {
		$recordDiff = $this->computeRecordDifference();
		$info = $this->_metadata->getConciseInfo();
		$sql = 'UPDATE `'.$this->getTableName().'` SET'.PHP_EOL ;
		foreach ($recordDiff as $column => $value){
			$sql .= '`'.$column . '` = ' ;
			
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
		return $response ;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\ModelInterface::delete()
	 */
	public function delete() {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\ModelInterface::refresh()
	 */
	public function refresh() {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\ModelInterface::reset()
	 */
	public function reset() {
		foreach ($this->_metadata->getNames() as $name){
			$this->_record[$name] = $this->{$name} ;
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
}