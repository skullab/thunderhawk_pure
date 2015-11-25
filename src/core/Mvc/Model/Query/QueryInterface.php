<?php

namespace Thunderhawk\Mvc\Model\Query;

interface QueryInterface {
	public function create($sql);
	public function execute(array $bindParams = array(),array $bindTypes = array());
}