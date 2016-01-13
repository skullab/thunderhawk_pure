<?php

namespace Thunderhawk\Mvc;
use Thunderhawk\Di\InjectionInterface;
use Thunderhawk\Di\ContainerInterface;

class View implements InjectionInterface{	
	protected $_di ;
	protected $_vars = array() ;
	
	public function setDi(ContainerInterface $di) {
		$this->_di = $di ;

	}

	public function getDi() {
		return $this->_di ;
	}

	public function __get($var){
		if(isset($this->_vars[$var])){
			return $this->_vars[$var] ;
		}
	}
	
	public function __set($var,$value){
		$this->_vars[$var] = $value ;
	}
}