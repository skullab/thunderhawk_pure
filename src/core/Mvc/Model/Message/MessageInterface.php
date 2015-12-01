<?php

namespace Thunderhawk\Mvc\Model\Message;

interface MessageInterface {
	public function __construct($message, $field = false, $type = false);
	public function setType($type);
	public function getType();
	public function setMessage($message);
	public function getMessage();
	public function setField($field);
	public function getField();
	public function setSQLSTATE($state);
	public function getSQLSTATE();
	public function setDriverError($error);
	public function getDriverError();
	public function setDriverMessage($message);
	public function getDriverMessage();
	public function setErrorInfo(array $info);
	public function __toString();
	//public function __set_state($message);
}