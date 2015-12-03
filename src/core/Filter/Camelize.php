<?php
namespace Thunderhawk\Filter;
use Thunderhawk\Filter\FilterHandlerInterface;
class Camelize implements FilterHandlerInterface{
	public function filter($value) {
		$explode = explode ( '_', $value );
		if(count($explode)<=1)return $value;
		return ( implode ( '', array_map ( 'ucfirst', array_map ( 'strtolower', $explode ) ) ) );
	}

}