<?php

namespace Thunderhawk\Http\Response\Headers;

interface HeadersInterface {
	public function set($name, $value,$replace,$statusCode);
	public function get($name);
	public function setRaw($header,$replace,$statusCode);
	public function send();
	public function reset();
	// public function static __set_state ( $data);
}