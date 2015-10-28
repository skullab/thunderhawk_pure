<?php

namespace Thunderhawk\Db\PDO\Connection;

use Thunderhawk\Db\PDO\Connection\Connector;

class Map extends \ArrayObject {
	const TAG_MASTER = 'master';
	const TAG_SLAVE = 'slave';
	const TAG_ANONYMOUS = 'anonymous';
	public function __construct(array $connectors = array()) {
		parent::__construct ( array (
				self::TAG_MASTER => array (),
				self::TAG_SLAVE => array (),
				self::TAG_ANONYMOUS => array () 
		) );
		foreach ( $connectors as $connector ) {
			$this->add ( $connector );
		}
	}
	public function append($value) {
	}
	public function add(Connector $connector) {
		$tag = $connector->getDsn ()->getTag ();
		$tag = $tag ? $tag : self::TAG_ANONYMOUS;
		if (! $this->offsetExists ( $tag )) {
			$this->offsetSet ( $tag, array () );
		}
		$map = $this->offsetGet ( $tag );
		if (! in_array ( $connector, $map )) {
			$index = $tag . '_' . count ( $map );
			$map [$index] = $connector;
		}
		$this->offsetSet ( $tag, $map );
	}
	public function __get($tag) {
		if ($this->offsetExists ( $tag )) {
			return array_values ( $this->offsetGet ( $tag ) );
		}
	}
	public function getTags() {
		return array_keys ( $this->getArrayCopy () );
	}
	public function getConnectors() {
		return $this->getArrayCopy ();
	}
	public function getConnectorsByTag($tag) {
		if ($this->offsetExists ( $tag ))
			return $this->offsetGet ( $tag );
		return null;
	}
	public function getConnector($tag, $index = 0) {
		$connectors = $this->__get ( $tag );
		if ($connectors)
			return $connectors [$index];
		return null;
	}
	public function getFirstAvailable() {
		foreach ( array_values ( $this->getArrayCopy () ) as $map ) {
			if (! empty ( $map )) {
				$connector = array_values ( $map );
				return $connector [0];
			}
		}
		return null;
	}
	public function count($tag = null) {
		if (! $tag) {
			$n = 0;
			foreach ( $this->getArrayCopy () as $map ) {
				$n += count ( $map );
			}
			return $n;
		}else{
			return count($this->getConnectorsByTag($tag));
		}
	}
}