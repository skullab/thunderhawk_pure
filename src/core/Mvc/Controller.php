<?php

namespace Thunderhawk\Mvc;
use Thunderhawk\Di\ContainerInjection;
use Thunderhawk\Mvc\Controller\ControllerInterface;
abstract class Controller extends ContainerInjection implements ControllerInterface{
	
	protected $_controllerName ;
	
	public function __construct(){
		$this->_controllerName = get_class($this) ;
		$this->onConstruct();
	}
	
	public function initialize(){}
	public function onConstruct(){}
	
	
}