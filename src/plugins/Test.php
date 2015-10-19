<?php
namespace Thunderhawk\Plugin;


class Test{
	private $value = null ;
	public function set($value){
		$this->value = $value ;
	}
	public function get(){
		return $this->value ;
	}
}