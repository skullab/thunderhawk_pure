<?php

namespace Thunderhawk\Db\PDO\Connection\Connector;

interface ConnectorInterface {
	public function connect();
	public function disconnect(); 
}