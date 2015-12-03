<?php

namespace Thunderhawk\Filter;
use Thunderhawk\Filter\FilterHandlerInterface;
class Upper implements FilterHandlerInterface {
	public function filter($value) {
		return strtoupper ( $value );
	}
}