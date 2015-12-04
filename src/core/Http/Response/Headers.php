<?php

namespace Thunderhawk\Http\Response;

use Thunderhawk\Http\Response\Headers\HeadersInterface;

class Headers implements HeadersInterface {	protected $_headers = array();

	public function set($name, $value,$replace = true ,$statusCode = null) {
		$this->_headers[$name] = $value ;
		$this->setRaw("$name: $value",$replace,$statusCode);
	}

	
	public function get($name) {
		return (isset($this->_headers[$name]) && !is_array($this->_headers[$name])) ? $this->_headers[$name] : null ;
	}

	
	public function setRaw($header,$replace = true ,$statusCode = null) {
		$statusCode = is_null($statusCode) ? http_response_code() : (int)$statusCode ;
		$this->_headers[] = array(
				'value' => $header,
				'replace' => $replace,
				'statusCode' => $statusCode
		);
	}

	protected function sendHeader(array $header){
		header($header['value'],$header['replace'],$header['statusCode']);
	}
	
	public function send() {
		foreach ($this->_headers as $key => $header){
			if(is_int($key) && is_array($header))$this->sendHeader($header);
		}

	}

	public function reset() {
		$this->_headers = array();
	}

}