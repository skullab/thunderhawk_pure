<?php

namespace Thunderhawk\Mvc\Model;
use Thunderhawk\Mvc\Model\Message\MessageInterface;
class Message implements MessageInterface{
	
	const TYPE_NO_ERROR = 0 ;
	/** Generated when a field with a non-null attribute on the database is trying to insert/update a null value */	const TYPE_PRESENCE_OF = 100 ;
	/** Generated when a field part of a virtual foreign key is trying to insert/update a value that doesn’t exist in the referenced model */
	const TYPE_CONSTRAINT_VIOLATION = 200 ;
	/** Generated when a validator failed because of an invalid value */
	const TYPE_INVALID_VALUE = 300 ;
	/** Produced when a record is attempted to be created but it already exists */
	const TYPE_INVALID_CREATE_ATTEMPT = 400 ;
	/** Produced when a record is attempted to be updated but it doesn’t exist */
	const TYPE_INVALID_UPDATE_ATTEMPT = 500 ;
	 
	protected $_text,$_field,$_type ;
	protected $_driverText = '',$_driverError = 0,$_sqlstate = '00000';
	
	public function __construct($message, $field = false, $type = false) {
		$this->setMessage($message);
		if($field)$this->setField($field);
		if($type)$this->setType($type);
	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Message\MessageInterface::setType()
	 */
	public function setType($type) {
		$this->_type = (int)$type;
	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Message\MessageInterface::getType()
	 */
	public function getType() {
		return $this->_type ;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Message\MessageInterface::setMessage()
	 */
	public function setMessage($message) {
		$this->_text = (string)$message;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Message\MessageInterface::getMessage()
	 */
	public function getMessage() {
		return $this->_text ;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Message\MessageInterface::setField()
	 */
	public function setField($field) {
		$this->_field = (string)$field;
	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Message\MessageInterface::getField()
	 */
	public function getField() {
		return $this->_field ;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Message\MessageInterface::__toString()
	 */
	public function __toString() {
		return $this->getMessage()." ".$this->getDriverMessage() ;
	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Message\MessageInterface::setSQLSTATE()
	 */
	public function setSQLSTATE($state) {
		$this->_sqlstate = $state ;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Message\MessageInterface::getSQLSTATE()
	 */
	public function getSQLSTATE() {
		return $this->_sqlstate ;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Message\MessageInterface::setDriverError()
	 */
	public function setDriverError($error) {
		$this->_driverError = (int)$error;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Message\MessageInterface::getDriverError()
	 */
	public function getDriverError() {
		return $this->_driverError ;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Message\MessageInterface::setDriverMessage()
	 */
	public function setDriverMessage($message) {
		$this->_driverText = (string)$message;
	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Message\MessageInterface::getDriverMessage()
	 */
	public function getDriverMessage() {
		return $this->_driverText ;
	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Message\MessageInterface::setErrorInfo()
	 */
	public function setErrorInfo(array $info) {
		$this->setSQLSTATE($info[0]);
		$this->setDriverError($info[1]);
		$this->setDriverMessage($info[2]);
	}

}