<?php

namespace Thunderhawk\Db\PDO\Connection;
use Thunderhawk\Db\PDO\Connection\Connector\ConnectorInterface;
use Thunderhawk\Db\PDO\Dsn\DsnInterface;

class Connector implements ConnectorInterface{
	
	private $dsn = null ;
	private $connection = null ;
	
	public function __construct(DsnInterface $dsn){
		$this->dsn = $dsn ;
	}
	public function __destruct(){
		$this->connection = null ;
		$this->dsn = null ;
	}
	public function connect(){
		if($this->connection == null){
			try{
			//var_dump('call new PDO connection');
			$this->connection = new \PDO($this->dsn->resolve(),
					$this->dsn->getUser(),
					$this->dsn->getPassword(),
					$this->dsn->getOptions());
			
			}catch (\PDOException $e){
				$message = sprintf('Unable to connect to "%s"',$this->dsn->getHost()).PHP_EOL;
				throw new ConnectionException($message,$e->getCode(),$e);
			}
			
		}
		return $this->connection ;
	}
	public function isConnected(){
		return $this->connection != null ;
	}
	public function disconnect(){
		return $this->connection = null ;
	}
	public function reset(){
		$this->disconnect();
		return $this->connect();
	}
	public function getDsn(){
		return $this->dsn ;
	}
}