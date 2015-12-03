<?php
namespace Thunderhawk\Filter;
use Thunderhawk\Filter\FilterHandlerInterface;
class Underscore implements FilterHandlerInterface{
	public function filter($value) {
		return implode ( '_', array_map ( 'strtolower', preg_split ( '/([A-Z]{1}[^A-Z]*)/', $value, - 1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY ) ) );
		
	}

}