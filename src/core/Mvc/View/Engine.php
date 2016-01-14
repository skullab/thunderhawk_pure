<?php

namespace Thunderhawk\Mvc\View;
use Thunderhawk\Events\EventsAwareInterface;
use Thunderhawk\Di\ContainerInjection;
use Thunderhawk\Mvc\View\Engine\EngineInterface;
use Thunderhawk\Events\Manager\ManagerInterface;

abstract class Engine extends ContainerInjection implements EventsAwareInterface,EngineInterface{
	
	protected $_view ;
	protected $_eventsManager ;
	
	public function __construct($view,$di = null){
		$this->_view = $view ;
		$this->setDi($di);
	}
	
	public function getContent(){
		return ob_get_contents() ;
	}
	
	public function setEventsManager(ManagerInterface $eventsManager){
		$this->_eventsManager = $eventsManager ;
	}
	
	public function getEventsManager() {
		return $this->_eventsManager ;
	}
	
	public function partial($partialPath, $params) {
		
	}


}