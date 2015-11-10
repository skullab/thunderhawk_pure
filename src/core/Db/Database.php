<?php

namespace Thunderhawk\Db;
use Thunderhawk\Db\PDO\Dsn;
use Thunderhawk\Db\PDO\Connection\Connector;
use Thunderhawk\Di\ContainerInjection;
use Thunderhawk\Db\PDO\Dsn\DsnInterface;
use Thunderhawk\Db\PDO\Connection\Connector\ConnectorInterface;
class Database extends ContainerInjection {
	//
	const ERR_NONE = \PDO::ERR_NONE ;
	const ERRMODE_SILENT = \PDO::ERRMODE_SILENT ;
	const ERRMODE_WARNING = \PDO::ERRMODE_WARNING ;
	const ERRMODE_EXCEPTION = \PDO::ERRMODE_EXCEPTION ;
	//
	const FETCH_LAZY = \PDO::FETCH_LAZY ;
	const FETCH_ASSOC = \PDO::FETCH_ASSOC ;
	const FETCH_NAMED = \PDO::FETCH_NAMED ;
	const FETCH_NUM = \PDO::FETCH_NUM ;
	const FETCH_BOTH = \PDO::FETCH_BOTH ;
	const FETCH_OBJ = \PDO::FETCH_OBJ ;
	const FETCH_BOUND = \PDO::FETCH_BOUND ;
	const FETCH_COLUMN = \PDO::FETCH_COLUMN ;
	const FETCH_CLASS = \PDO::FETCH_CLASS ;
	const FETCH_INTO = \PDO::FETCH_INTO ;
	const FETCH_FUNC = \PDO::FETCH_FUNC ;
	const FETCH_GROUP = \PDO::FETCH_GROUP ;
	const FETCH_UNIQUE = \PDO::FETCH_UNIQUE ;
	const FETCH_KEY_PAIR = \PDO::FETCH_KEY_PAIR ;
	const FETCH_CLASSTYPE = \PDO::FETCH_CLASSTYPE ;
	const FETCH_SERIALIZE = \PDO::FETCH_SERIALIZE ;
	const FETCH_PROPS_LATE = \PDO::FETCH_PROPS_LATE ;
	//
	private $connection = null;
	private $connector = null;
	private $query_fetch = null ;
	//
	private $options_name = array (
			"AUTOCOMMIT",
			"ERRMODE",
			"CASE",
			"CLIENT_VERSION",
			"CONNECTION_STATUS",
			"DRIVER_NAME",
			"ORACLE_NULLS",
			"PERSISTENT",
			"PREFETCH",
			"SERVER_INFO",
			"SERVER_VERSION",
			"TIMEOUT" 
	);
	public function __construct($method = null) {
		if(is_array($method)){
			$this->setConnector(new Connector(new Dsn($method)));
			//$this->setPDO($this->getConnector()->connect());
		}else if($method instanceof DsnInterface){
			$this->setConnector(new Connector($method));
			//$this->setPDO($this->getConnector()->connect());
		//}else if($method instanceof \PDO){
		//	$this->setPDO($method);
		}else if($method instanceof Connector){
			$this->setConnector($method);
			
		}
		if($method != null)$this->open();
	}
	public function open(){
		$this->setPDO($this->getConnector()->connect());
	}
	public function close(){
		if($this->connector){
			$this->connector->disconnect();
		}
	}
	public function reset(){
		$this->setPDO($this->getConnector()->reset());
	}
	public function getPDO(){
		return $this->connection ;
	}
	public function setPDO(\PDO $connection){
		$this->connection = $connection ;
	}
	public function getConnector(){
		return $this->connector ;
	}
	public function setConnector(ConnectorInterface $connector){
		$this->connector = $connector ;
	}
	public function getOption($name){
		if(in_array($name, $this->options_name)){
			return $this->connection->getAttribute(constant("PDO::ATTR_$name"));
		}
	}
	public function getOptions(){
		$options = array();
		foreach ($this->options_name as $name){
			$options[$name] = $this->connection->getAttribute(constant("PDO::ATTR_$name"));
		}
		return $options ;
	}
	public static function getAvalaibleDrivers(){
		return \PDO::getAvailableDrivers();
	}
	public function execute($statement){
		return $this->getPDO()->exec($statement);
	}
	public function prepare($statement,array $driver_options = array()){
		return $this->getPDO()->prepare($statement,$driver_options);
	}
	public function setQueryFetch($mode){
		if(is_int($mode)){
			$this->query_fetch = $mode ;
		}
	}
	public function getQueryFetch(){
		return $this->query_fetch ;
	}
	public function query($statement){
		return $this->getPDO()->query($statement,$this->getQueryFetch()); 
	}
}