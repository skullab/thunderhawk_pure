<?php

namespace Thunderhawk\Filter;
use Thunderhawk\Filter\FilterHandlerInterface;
class ABS implements FilterHandlerInterface{
	public function filter($value) {
		return abs($value);
	}

}