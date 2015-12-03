<?php

namespace Thunderhawk\Filter;
use Thunderhawk\Filter\FilterHandlerInterface;
class Alphanum implements FilterHandlerInterface {
	public function filter($value) {
		return preg_replace("/[^a-zA-Z0-9]+/", "", $value);
	}
}