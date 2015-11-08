<?php

namespace Thunderhawk\Db\PDO\Dsn;

interface DsnInterface {
	public function resolve();
	public function getTag();
	public function getUser();
	public function getPassword();
	public function getOptions();
}