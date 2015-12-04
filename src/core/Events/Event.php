<?php

namespace Thunderhawk\Events;

class Event implements EventInterface{
	protected $_type ;
	protected $_source ;
	protected $_data ;
	protected $_cancelable ;
	protected $_stopped = false  ;
	public function __construct($type,$source,$data = null,$cancelable = true){
		$this->setType($type);
		$this->_source = is_object($source) ? $source : null ;
		$this->setData($data);
		$this->_cancelable = (bool)$cancelable ;
	}
	/**
	 * {@inheritDoc}
	 * @see \Thunderhawk\Events\EventInterface::setType()
	 */
	public function setType($type) {
		$this->_type = (string)$type ;
	}

	/**
	 * {@inheritDoc}
	 * @see \Thunderhawk\Events\EventInterface::getType()
	 */
	public function getType() {
		return $this->_type ;
	}

	/**
	 * {@inheritDoc}
	 * @see \Thunderhawk\Events\EventInterface::getSource()
	 */
	public function getSource() {
		return $this->_source ;
	}

	/**
	 * {@inheritDoc}
	 * @see \Thunderhawk\Events\EventInterface::setData()
	 */
	public function setData($data) {
		$this->_data = $data ;
	}

	/**
	 * {@inheritDoc}
	 * @see \Thunderhawk\Events\EventInterface::getData()
	 */
	public function getData() {
		return $this->_data ;
	}

	/**
	 * {@inheritDoc}
	 * @see \Thunderhawk\Events\EventInterface::isCancelable()
	 */
	public function isCancelable() {
		return $this->_cancelable ;
	}

	/**
	 * {@inheritDoc}
	 * @see \Thunderhawk\Events\EventInterface::stop()
	 */
	public function stop() {
		if($this->isCancelable())$this->_stopped = true ;
	}

	/**
	 * {@inheritDoc}
	 * @see \Thunderhawk\Events\EventInterface::isStopped()
	 */
	public function isStopped() {
		return $this->_stopped ;
	}

}
