<?php

namespace Thunderhawk\Events;

interface EventInterface {
	public function setType($type);
	public function getType();
	public function getSource();
	public function setData($data);
	public function getData();
	public function isCancelable();
	public function stop();
	public function isStopped();
}