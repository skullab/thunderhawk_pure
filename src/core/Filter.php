<?php

namespace Thunderhawk;

use Thunderhawk\Filter\FilterInterface;
use Thunderhawk\Filter\FilterHandlerInterface;
use Thunderhawk\Filter\ABS;
use Thunderhawk\Filter\Alphanum;
use Thunderhawk\Filter\Email;
use Thunderhawk\Filter\Float;
use Thunderhawk\Filter\Int;
use Thunderhawk\Filter\Lower;
use Thunderhawk\Filter\SpecialChars;
use Thunderhawk\Filter\String;
use Thunderhawk\Filter\StripTags;
use Thunderhawk\Filter\Trim;
use Thunderhawk\Filter\Upper;
use Thunderhawk\Filter\URL;
use Thunderhawk\Filter\StripLower;
use Thunderhawk\Filter\StripUpper;
use Thunderhawk\Filter\Camelize;
use Thunderhawk\Filter\Underscore;
use Thunderhawk\Filter\FilterException;

class Filter implements FilterInterface {
	
	const FILTER_EMAIL = "email";
	const FILTER_URL = "url";
	const FILTER_ABS = "abs";
	const FILTER_INT = "int";
	const FILTER_INT_CAST = "int_cast";
	const FILTER_STRING = "string";
	const FILTER_SPECIAL_CHARS = "special_chars";
	const FILTER_FLOAT = "float";
	const FILTER_FLOAT_CAST = "float_cast";
	const FILTER_ALPHANUM = "alphanum";
	const FILTER_TRIM = "trim";
	const FILTER_STRIPTAGS = "strip_tags";
	const FILTER_LOWER = "lower";
	const FILTER_UPPER = "upper";
	const FILTER_STRIP_LOWER = "strip_lower";
	const FILTER_STRIP_UPPER = "strip_upper";
	const FILTER_CAMELIZE = "camelize";
	const FILTER_UNDERSCORE = "underscore";
	protected $_handlers = array ();
	protected $_throwException = false ;
	
	public function __construct($empty = false, $throwException = false) {
		$this->throwException($throwException);
		if($empty)return;
		$this->add ( self::FILTER_ABS, new ABS () );
		$this->add ( self::FILTER_ALPHANUM, new Alphanum () );
		$this->add(self::FILTER_CAMELIZE, new Camelize());
		$this->add ( self::FILTER_EMAIL, new Email () );
		$this->add ( self::FILTER_FLOAT, new Float () );
		$this->add ( self::FILTER_FLOAT_CAST, new Float ( true ) );
		$this->add ( self::FILTER_INT, new Int () );
		$this->add ( self::FILTER_INT_CAST, new Int ( true ) );
		$this->add ( self::FILTER_LOWER, new Lower () );
		$this->add ( self::FILTER_SPECIAL_CHARS, new SpecialChars () );
		$this->add ( self::FILTER_STRING, new String () );
		$this->add ( self::FILTER_STRIPTAGS, new StripTags () );
		$this->add(self::FILTER_STRIP_LOWER,new StripLower());
		$this->add(self::FILTER_STRIP_UPPER, new StripUpper());
		$this->add ( self::FILTER_TRIM, new Trim () );
		$this->add(self::FILTER_UNDERSCORE,new Underscore());
		$this->add ( self::FILTER_UPPER, new Upper () );
		$this->add ( self::FILTER_URL, new URL () );
	}
	public function throwException($enable){
		$this->_throwException = (bool)$enable ;
	}
	public function add($name, $handler) {
		if (is_object ( $handler )) {
			$this->_handlers [( string ) $name] = $handler;
		}
	}
	protected function _sanitize($value, $filter) {
		if ($this->exists($filter)) {
			if (is_callable ( $this->_handlers [$filter] )) {
				return $this->_handlers [$filter] ( $value );
			} else if ($this->_handlers [$filter] instanceof FilterHandlerInterface) {
				return $this->_handlers [$filter]->filter ( $value );
			}
		}
		if($this->_throwException){
			$message = sprintf("The filter '%s' doesn't exists",$filter);
			throw new FilterException($message,100);
		}
		return $value ;
	}
	public function sanitize($value, $filters) {
		$filters = is_array($filters)? $filters : array((string)$filters);
		foreach ($filters as $filter){
			$value = $this->_sanitize($value, $filter);
		}
		return $value ;
	}
	public function getFilters() {
		return $this->_handlers;
	}
	public function getFilterByName($name) {
		if (isset ( $this->_handlers [$name] )) {
			return $this->_handlers [$name];
		}
		return null;
	}
	
	public function exists($filter) {
		return isset($this->_handlers[$filter]);
	}

}