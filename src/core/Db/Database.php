<?php

namespace Thunderhawk\Db;
use Thunderhawk\Db\PDO\Dsn;
use Thunderhawk\Db\PDO\Connection\Connector;
class Database {
	//
	const ERR_NONE = \PDO::ERR_NONE ;
	const ERRMODE_SILENT = \PDO::ERRMODE_SILENT ;
	const ERRMODE_WARNING = \PDO::ERRMODE_WARNING ;
	const ERRMODE_EXCEPTION = \PDO::ERRMODE_EXCEPTION ;
	//
	private $connection = null;
	private $connector = null;
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
	public function __construct($method) {
		if(is_array($method)){
			$this->connector = new Connector(new Dsn($method));
		}
		
	}
	public function isOpen(){
		return ($this->connection !== null);
	}
	public function open() {
		$this->connection = connection::connect ( $this->dsn, $this->options );
		$this->options = $this->getDriverOptions();
	}
	public function close() {
		$this->connection = connection::disconnect();
	}
	public function reset() {
		$this->close ();
		$this->options = array();
		$this->open ();
	}
	public function getDriverPrefix() {
		return $this->dsn->prefix;
	}
	public function isDriverSupported() {
		return (in_array ( $this->dsn->prefix, $this->connection->getAvailableDrivers () ));
	}
	public function getDriverOption($attribute) {
		return $this->connection->getAttribute ( $attribute );
	}
	public function getDriverOptions() {
		$options = array();
		$pdo = 'PDO::ATTR_';
		foreach ($this->options_name as $name){
			$options[constant($pdo.$name)] = @$this->getDriverOption(constant($pdo.$name));
		}
		return $options ;
	}
	public function getReadableDriverOptions(){
		$options = array();
		$pdo = 'PDO::ATTR_';
		foreach ($this->options_name as $name){
			$options[$pdo.$name] = @$this->getDriverOption(constant($pdo.$name));
		}
		return $options ;
	}
	public function setDriverOption($attribute, $value) {
		$this->options [$attribute] = $value;
		return $this->connection->setAttribute ( $attribute, $value );
	}
	public function debug($errmode){
		return $this->setDriverOption(\PDO::ATTR_ERRMODE, $errmode);
	}
	public function getErrorCode() {
		return $this->connection->errorCode ();
	}
	public function getErrorMessages() {
		return $this->connection->errorInfo ();
	}
	public function execute($sql) {
		return $this->connection->exec ( $sql );
	}
	public function query($sql) {
		return $this->connection->query ( $sql );
	}
	public function prepare($sql,$options = array()){
		return $this->connection->prepare($sql,$options);
	}
}