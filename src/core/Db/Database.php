<?php

namespace Thunderhawk\Db;

use Thunderhawk\Di\InjectionInterface;
use Thunderhawk\Db\PDO\Driver\Bridge;
use Thunderhawk\Db\PDO\Dsn;

class Database {
	const ERR_NONE = \PDO::ERR_NONE ;
	const ERRMODE_SILENT = \PDO::ERRMODE_SILENT ;
	const ERRMODE_WARNING = \PDO::ERRMODE_WARNING ;
	const ERRMODE_EXCEPTION = \PDO::ERRMODE_EXCEPTION ;
	private $bridge = null;
	private $parameters = array ();
	private $options = array ();
	private $dsn = null;
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
	public function __construct($parameters, $options = array()) {
		if(is_array($parameters)){
			$this->parameters = $parameters;
			$this->dsn = Dsn::createByArray ( $this->parameters );
		}else if($parameters instanceof Dsn){
			$this->dsn = $parameters ;
		}
		
		$this->options = $options;
		$this->open ();
	}
	public function isOpen(){
		return ($this->bridge !== null);
	}
	public function open() {
		$this->bridge = Bridge::connect ( $this->dsn, $this->options );
		$this->options = $this->getDriverOptions();
	}
	public function close() {
		$this->bridge = Bridge::disconnect();
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
		return (in_array ( $this->dsn->prefix, $this->bridge->getAvailableDrivers () ));
	}
	public function getDriverOption($attribute) {
		return $this->bridge->getAttribute ( $attribute );
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
		return $this->bridge->setAttribute ( $attribute, $value );
	}
	public function debug($errmode){
		return $this->setDriverOption(\PDO::ATTR_ERRMODE, $errmode);
	}
	public function getErrorCode() {
		return $this->bridge->errorCode ();
	}
	public function getErrorMessages() {
		return $this->bridge->errorInfo ();
	}
	public function execute($sql) {
		return $this->bridge->exec ( $sql );
	}
	public function query($sql) {
		return $this->bridge->query ( $sql );
	}
	public function prepare($sql,$options = array()){
		return $this->bridge->prepare($sql,$options);
	}
}