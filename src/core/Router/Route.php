<?php

namespace Thunderhawk\Router;

use Thunderhawk\Router\Route\RouteInterface;
use Thunderhawk\Router\Route\Rules;
use Thunderhawk\Router\Route\RouteException;

class Route implements RouteInterface {
	private $pattern;
	private $compiled_pattern;
	private $handler = array ();
	private $httpMethods = array ();
	private $id;
	private $name;
	public function __construct($pattern, $handler = array(), $httpMethods = array()) {
		$this->pattern = ( string ) $pattern;
		$this->handler = ( array ) $handler;
		$this->httpMethods = ( array ) $httpMethods;
		$this->id = spl_object_hash ( $this );
		$this->compilePattern ( $this->pattern );
	}
	public function compilePattern($pattern) {
		if(!is_string($pattern))throw new RouteException('No valid pattern');
		$this->compiled_pattern = '/' . str_replace ( '/', '\/', preg_replace ( Rules::getPlaceholders (), Rules::getReplacements (), $pattern ) ) . '/';
	}
	public function via($httpMethods) {
		if (is_string ( $httpMethods )) {
			$this->httpMethods = array (
					$httpMethods 
			);
		} else if (is_array ( $httpMethods )) {
			$this->httpMethods = $httpMethods;
		}
	}
	public function reConfigure($pattern, $handler = array()) {
		$this->pattern = ( string ) $pattern;
		$this->handler = ( array ) $handler;
		$this->compilePattern($this->pattern);
	}
	public function getName() {
		return $this->name;
	}
	public function setName($name) {
		$this->name = ( string ) $name;
	}
	public function setHttpMethods($httpMethods = array()) {
		$this->httpMethods = ( array ) $httpMethods;
	}
	public function getRouteId() {
		return $this->id;
	}
	public function getPattern() {
		return $this->pattern;
	}
	public function getCompiledPattern() {
		return $this->compiled_pattern;
	}
	public function getHandler() {
		return $this->handler;
	}
	public function getReversedHandler() {
		return array_flip ( $this->handler );
	}
	public function getHttpMethods() {
		return $this->httpMethods;
	}
	public function reset() {
		$this->id = spl_object_hash ( $this );
	}
}