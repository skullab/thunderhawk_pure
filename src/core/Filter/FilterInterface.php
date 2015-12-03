<?php

namespace Thunderhawk\Filter;

interface FilterInterface {
	public function add($name, $handler);
	public function sanitize($value, $filters);
	public function getFilters();
}