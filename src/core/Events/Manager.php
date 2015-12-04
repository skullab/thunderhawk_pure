<?php

namespace Thunderhawk\Events;

use Thunderhawk\Events\Manager\ManagerInterface;

class Manager implements ManagerInterface {
	
	protected $_prefixDelimiter = ":";
	protected $_prioritiesEnabled = false;
	protected $_collectResponses = false ;
	protected $_responses = array();
	protected $_events = array ();
	
	public function collectResponses($enable = null){
		if(!is_null($enable) && is_bool($enable))$this->_collectResponses = $enable ; 
		return $this->_collectResponses ;
	}
	public function getResponses(){
		return $this->_responses ;
	}
	public function enablePriorities($enable) {
		$this->_prioritiesEnabled = ( bool ) $enable;
	}
	public function arePrioritiesEnabled() {
		return $this->_prioritiesEnabled;
	}
	public function hasEvents($eventType) {
		return isset ( $this->_events [$eventType] );
	}
	protected function getEvents($eventType) {
		if (! $this->hasEvents ( $eventType ))
			$this->_events[$eventType] = array();
		if ($this->arePrioritiesEnabled ())
			krsort ( $this->_events [$eventType] );
		return $this->_events [$eventType];
	}
	public function hasListeners($type) {
	}
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\Events\Manager\ManagerInterface::attach()
	 */
	public function attach($eventType, $handler, $priority = null) {
		if ($this->arePrioritiesEnabled () && ! is_null ( $priority )) {
			//$this->getEvents($eventType)[(int)$priority] = $handler;
			$this->_events[$eventType][(int)$priority] = $handler ;
		} else {
			//$this->getEvents($eventType)[] = $handler;
			$this->_events[$eventType][] = $handler;
		}
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\Events\Manager\ManagerInterface::detach()
	 */
	public function detach($eventType, $handler) {
		if (false !== $key = array_search ( $handler, $this->getEvents ( $eventType ) )) {
			unset($this->_events[$key]);
			$this->detach ( $eventType, $handler );
		}
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\Events\Manager\ManagerInterface::detachAll()
	 */
	public function detachAll($type) {
		$this->_events [$type] = array ();
	}
	
	protected function fireEvent($handler,Event &$event){
		$response = null ;
		if(is_callable($handler)){
			$response = $handler($event,$event->getSource(),$event->getData());
		}else{
			if(is_object($handler) && !is_null($event->getSource())){
				$args = array($event,$event->getSource(),$event->getData());
				$response = call_user_func_array(
						array($handler,$event->getType()), $args);
			}
		}
		if($this->collectResponses()){
			var_dump($response);
			$this->_responses[] = $response ;
		}
		return $event ;
	}
	public function fireQueue(array $queue,Event $event){
		foreach ($queue as $handler){
			$this->fireEvent($handler, $event);
			//var_dump($event);
			if($event->isStopped())break;
		}
	}
	public function fire($eventType, $source, $data = null, $cancelable = true) {
		$prefix = $this->hasPrefix ( $eventType );
		$_eventType = $prefix !== false ? $prefix [0] : $eventType;
		$type = $prefix !== false ? $prefix [1] : $eventType;
		
		if (! $this->hasEvents ( $_eventType ) && ! $this->hasEvents ( $eventType ))
			return;
		if(!is_null($source) && !is_object($source)){
			// exception ?
			return ;
		}
		$event = new Event($type, $source,$data,$cancelable);
		$this->fireQueue($this->getEvents($_eventType), $event);
		
		if ($_eventType != $eventType && $this->hasEvents($eventType)) {
			$this->fireQueue($this->getEvents($eventType), $event);
		}
	}
	protected function hasPrefix($eventType) {
		$e = explode ( $this->_prefixDelimiter, $eventType, 2 );
		if (count ( $e ) > 1)
			return $e;
		return false;
	}
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\Events\Manager\ManagerInterface::getListeners()
	 */
	public function getListeners($type) {
		// TODO: Auto-generated method stub
	}
}