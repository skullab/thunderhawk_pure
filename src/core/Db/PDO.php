<?php

namespace Thunderhawk\Db;

abstract class PDO extends \PDO {
	protected $settings, $prefix, $dsn, $username, $password, $options;
	protected $is_connected = false;
	public function __construct($settings, $options = array()) {
		$this->settings = ( array ) $settings;
		$this->options = ( array ) $options;
		$this->prepareConnection ( $this->settings );
		parent::__construct ( $this->dsn, $this->username, $this->password, $this->options );
		$this->is_connected = true;
	}
	protected abstract function prepareConnection($settings = null);
	public function setOptions(array $options) {
		$this->options = $options;
	}
	public function getOptions() {
		return $this->options;
	}
	public function isConnected() {
		return $this->is_connected;
	}
	protected function resolveDsn($prefix, array $config) {
		$this->prefix = $prefix;
		if (isset ( $config ['dsn'] )) {
			$this->dsn = $config ['dsn'];
		} else {
			$this->dsn = $this->prefix . ':';
			foreach ( $config as $key => $value ) {
				$this->dsn .= $key . '=' . $value . ';';
			}
			$this->dsn = rtrim ( $this->dsn, ';' );
		}
	}
}