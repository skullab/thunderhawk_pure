<?php

namespace Thunderhawk\Mvc\Model\Query;

interface QueryInterface {
	public function create($sql);
	public function insert(array $columns,array $values = array());
	public function update(array $columns,array $values = array());
	public function delete();
	public function execute(array $bindParams = array(),array $bindTypes = array());
}