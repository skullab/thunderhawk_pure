<?php

namespace Thunderhawk\Db\PDO;

class Connector {
	
	private $dsn = null ;
	private $connection = null ;
	
	public function __construct(Dsn $dsn){
		$this->dsn = $dsn ;
	}
	public function connect(){
		if($this->connection == null){
			$this->connection = new \PDO($this->dsn->resolve(),
					$this->dsn->user,
					$this->dsn->password,
					$this->dsn->options);
		}
		return $this->connection ;
	}
	public function disconnect(){
		return $this->connection = null ;
	}
	public function isDsnEqual(Dsn $dsn){
		return $this->dsn == $dsn ;
	}
}