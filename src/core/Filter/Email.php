<?php

namespace Thunderhawk\Filter;
use Thunderhawk\Filter\FilterHandlerInterface;
class Email implements FilterHandlerInterface {
	public function filter($value) {
		return filter_var($value,FILTER_SANITIZE_EMAIL);
	}
}