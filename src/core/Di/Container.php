<?php

namespace Thunderhawk\Di;

class Container implements ContainerInterface {
	private $dependencies = array ();
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\Di\ContainerInterface::set()
	 */
	public function set($name, callable $service, $shared = false, $override = false) {
		if ($this->serviceExist ( $name ) && !$this->isOverridable($name)) {
			$message = sprintf ( 'The service "%s" already exist : You CAN\'T override this service', $name );
			throw new \InvalidArgumentException ( $message );
		}
		
		$service = $shared ? (function ($container) use($service) {
			static $object;
			if (is_null ( $object )) {
				$object = $service ( $container );
			}
			return $object;
		}) : $service;
		
		$this->dependencies [$name] = array (
				"service" => $service,
				"shared" => ( bool ) $shared,
				"overridable" => ( bool ) $override 
		);
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\Di\ContainerInterface::get()
	 */
	public function get($name) {
		if (! $this->serviceExist ( $name )) {
			$message = sprintf ( 'The service "%s" does not exist', $name );
			throw new \InvalidArgumentException ( $message );
		}
		return $this->dependencies [$name] ['service'] ( $this );
	}
	public function isOverridable($name) {
		if (isset ( $this->dependencies [$name] )) {
			return $this->dependencies[$name]['overridable'] ;
		}
	}
	public function isShared($name) {
		if (isset ( $this->dependencies [$name] ))
			return $this->dependencies [$name] ['shared'];
	}
	public function serviceExist($name) {
		return isset ( $this->dependencies [$name] );
	}
	public function __set($name, $service) {
		$this->set ( $name, $service );
	}
	public function __get($name) {
		return $this->get ( $name );
	}
}