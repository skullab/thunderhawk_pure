<?php

namespace Thunderhawk\Filter;
use Thunderhawk\Filter\FilterHandlerInterface;
class StripLower implements FilterHandlerInterface {
	public function filter($value) {
		return preg_replace ( '/[a-z]/', '', $value );
	}
}