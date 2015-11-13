<?php

namespace Thunderhawk\Mvc\Model;

interface ResultsetInterface {
	public function getFirst();
	public function getLast();
	public function toArray(array $columns = array());
	public function filter($callable);
}