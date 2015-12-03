<?php

namespace Thunderhawk\Filter;
use Thunderhawk\Filter\FilterHandlerInterface;
class URL implements FilterHandlerInterface {
	public function filter($value) {
		return filter_var($value,FILTER_SANITIZE_URL);
	}
}