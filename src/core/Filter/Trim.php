<?php

namespace Thunderhawk\Filter;
use Thunderhawk\Filter\FilterHandlerInterface;
class Trim implements FilterHandlerInterface{
	public function filter($value) {
		return trim($value);
	}

}