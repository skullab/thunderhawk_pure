<?php

namespace Thunderhawk\Acl;

class Resource implements ResourceInterface {
	protected $_name;
	protected $_description;
	public function __construct($name, $description = null) {
		if ($name == '*') {
			throw new \Exception ( "Resource name cannot be '*'" );
		}
		$this->_name = ( string ) $name;
		if ($description) {
			$this->_description = ( string ) $description;
		}
	}
	public function getName() {
		return $this->_name;
	}
	public function getDescription() {
		return $this->_description;
	}
	public function __toString() {
		return $this->_name;
	}
}