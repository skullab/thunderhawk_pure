<?php

namespace Thunderhawk\Acl;
use Thunderhawk\Events\EventsAwareInterface;
use Thunderhawk\Events\Manager\ManagerInterface;

abstract class Adapter implements AdapterInterface, EventsAwareInterface{
	
	protected $_eventsManager ;
	protected $_defaultAccess = true ;
	protected $_accessGranted = false ;
	protected $_activeRole ;
	protected $_activeResource ;
	protected $_activeAccess ;
	
	public function setEventsManager(ManagerInterface $eventsManager) {
		$this->_eventsManager = $eventsManager ;
	}

	public function getEventsManager() {
		return $this->_eventsManager ;
	}
	
	
	public function setDefaultAction($defaultAccess) {
		$this->_defaultAccess = (int)$defaultAccess ;
	}

	public function getDefaultAction() {
		return (int)$this->_defaultAccess ;
	}


}