<?php

namespace Thunderhawk\Mvc;

use Thunderhawk\Mvc\Model\ModelInterface;
use Thunderhawk\Di\InjectionInterface;
use Thunderhawk\Di\ContainerInterface;
use Thunderhawk\Utils;

class Model implements InjectionInterface, ModelInterface, \Serializable {
	//
	const CON_GLOBAL = 'global' ;
	const CON_READ = 'read' ;
	const CON_WRITE = 'write' ;
	//
	const TYPE_BOOL = \PDO::PARAM_BOOL ;
	const TYPE_INT = \PDO::PARAM_INT ;
	const TYPE_NULL = \PDO::PARAM_NULL ;
	const TYPE_STRING = \PDO::PARAM_STR ;
	const TYPE_LOB = \PDO::PARAM_LOB ;
	const TYPE_STMT = \PDO::PARAM_STMT ;
	const TYPE_INPUT_OUTPUT = \PDO::PARAM_INPUT_OUTPUT ;
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
	//
	final public function __construct(ContainerInterface $di = null) {
		$this->setTableName(Utils::underscore(basename(get_class($this))));
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
	public function setConnectionService($connectionService) {
		$this->_connections ['global'] = $connectionService;
	}
	public function setWriteConnectionService($connectionService) {
		$this->_connections ['write'] = $connectionService;
	}
	public function setReadConnectionService($connectionService) {
		$this->_connections ['read'] = $connectionService;
	}
	public function getConnectionService() {
		return $this->_connections ['global'];
	}
	public function getReadConnectionService() {
		return $this->_connections ['read'];
	}
	public function getWriteConnectionService() {
		return $this->_connections ['write'];
	}
	protected function resolveConnectionService($type){
		
	}
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\ModelInterface::find()
	 */
	public static function find($parameters) {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\ModelInterface::findFirst()
	 */
	public static function findFirst($parameters) {
		// TODO: Auto-generated method stub
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
	public function update($data, $whiteList) {
		// TODO: Auto-generated method stub
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
		// TODO: Auto-generated method stub
	}
	public function setDi(ContainerInterface $di) {
		$this->_di = $di;
	}
	public function getDi() {
		return $this->_di;
	}
	public function serialize() {
	}
	public function unserialize($data) {
	}
}