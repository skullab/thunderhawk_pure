<?php

namespace Thunderhawk\Router\Route;

class RouteException extends \Exception {
	public function __construct($message = null, $code = null, $previous = null) {
		parent::__construct($message,$code,$previous);
	}
	
	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
}