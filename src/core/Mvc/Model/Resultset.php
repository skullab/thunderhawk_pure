<?php

namespace Thunderhawk\Mvc\Model;

class Resultset implements ResultsetInterface,\SeekableIterator, \Countable, \ArrayAccess, \Serializable {
	protected $_row = 0 ;
	protected $_resultset = array();
	
	public function seek($row) {
		if (!isset($this->_resultset[$row])) {
			throw new OutOfBoundsException("invalid seek position ($row)");
		}
		
		$this->_row = $row;
	}
	public function current() {
		return $this->_resultset[$this->_row];
	}
	public function key() {
		return $this->_row ;
	}
	public function next() {
		++$this->_row ;
	}
	public function rewind() {
		$this->_row = 0 ;
	}
	public function valid() {
		return isset($this->_resultset[$this->_row]);
	}
	public function count() {
		return count($this->_resultset);
	}
	public function offsetExists($offset) {
		return isset($this->_resultset[$offset]);
	}
	public function offsetGet($offset) {
		return isset($this->_resultset[$offset]) ? $this->_resultset[$offset] : null ;
	}
	public function offsetSet($offset, $value) {
		if(is_null($offset)){
			$this->_resultset[] = $value ;
		}else{
			$this->_resultset[$offset] = $value ;
		}
	}
	public function offsetUnset($offset) {
		unset($this->_resultset[$offset]);
	}
	public function serialize() {
		return serialize($this->_resultset);
	}
	public function unserialize($serialized) {
		$this->_resultset = unserialize($serialized);
	}
}