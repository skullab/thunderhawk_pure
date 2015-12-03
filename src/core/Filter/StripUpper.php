<?php

namespace Thunderhawk\Filter;
use Thunderhawk\Filter\FilterHandlerInterface;
class StripUpper implements FilterHandlerInterface{
	public function filter($value) {
		return preg_replace ( '/[A-Z]/', '', $value );
	}

}