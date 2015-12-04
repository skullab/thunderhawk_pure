<?php

namespace Thunderhawk\Events\Manager;

interface ManagerInterface {
	public function attach($eventType, $handler);
	public function detach($eventType, $handler);
	public function detachAll($type);
	public function fire($eventType, $source, $data);
	public function getListeners($type);
}