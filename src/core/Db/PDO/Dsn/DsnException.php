<?php

namespace Thunderhawk\Db\PDO\Dsn;

class DsnException extends \Exception {
	
	public function __construct($message = null, $code = null, $previous = null) {
		parent::__construct($message,$code,$previous);
	}
	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}

}