<?php

namespace Thunderhawk\Filter;
use Thunderhawk\Filter\FilterHandlerInterface;
class Lower implements FilterHandlerInterface {
	public function filter($value) {
		return strtolower($value);
	}
}