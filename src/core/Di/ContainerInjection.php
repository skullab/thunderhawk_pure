<?php

namespace Thunderhawk\Di;

abstract class ContainerInjection implements InjectionInterface {
	protected $_di;
	public function setDi(ContainerInterface $di) {
		$this->_di = $di;
	}
	public function getDi() {
		return $this->_di;
	}
	public function __get($name){
		return $this->_di->get($name);
	}
}