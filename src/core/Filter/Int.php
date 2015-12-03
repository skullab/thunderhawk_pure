<?php

namespace Thunderhawk\Filter;
use Thunderhawk\Filter\FilterHandlerInterface;
class Int implements FilterHandlerInterface {
	protected $cast = false ;
	public function __construct($cast = false){
		$this->cast = (bool)$cast;
	}
	public function enableCast($enable){
		$this->cast = (bool)$enable;
	}
	public function filter($value) {
		return $this->cast ? (int)$value : filter_var($value,FILTER_SANITIZE_NUMBER_INT);
	}
}