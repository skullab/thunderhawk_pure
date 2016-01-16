<?php

namespace Thunderhawk\Router\Route;

abstract class Rules {
	
	private static $rules = array (
			//placeholders			//replacements
			'/:module/'			=> '([a-zA-Z0-9\_\-]+)',
			'/:controller/'		=> '([a-zA-Z0-9\_\-]+)',
			'/:action/'			=> '([a-zA-Z0-9\_]+)',
			'/:params/'			=> '(.*)/*',
			'/:namespace/'		=> '([a-zA-Z0-9\_\-]+)',
			'/:number/'			=> '([0-9]+)',
			'/:int/'			=> '([0-9]+)',
			'/:string/'			=> '([a-zA-Z\-\-]+)' ,
			'/:alphanumeric/'	=> '([a-zA-Z0-9\-\-]+)',
			'/:mixed/'			=> '([a-zA-Z0-9\-\-]+)',
			'/{([a-zA-Z0-9\-\-]+)}/' => '([a-zA-Z0-9\-\-]+)',
	);
	
	public static function add($placeholder,$replacement){
		self::$rules[(string)$placeholder] = (string)$replacement ;
	}
	
	public static function getPlaceholders(){
		return array_keys(self::$rules);
	}
	
	public static function getReplacements(){
		return array_values(self::$rules);
	}
}