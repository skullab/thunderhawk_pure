<?php

namespace Thunderhawk\Http\Response;

use Thunderhawk\Http\Response\Cookies\CookiesInterface;

class Cookies implements CookiesInterface {
	protected $_urlEncoding = false;
	protected $_setcookie = 'setcookie';
	protected $_cookies = array ();
	public function useUrlEncoding($useUrlEncoding) {
		$this->_urlEncoding = ( bool ) $useUrlEncoding;
		if ($this->_urlEncoding) {
			$this->_setcookie = 'setrawcookie';
		} else {
			$this->_setcookie = 'setcookie';
		}
	}
	public function isUsingUrlEncoding() {
		return $this->_urlEncoding;
	}
	public function set($name, $value = "", $expire = 0, $path = "", $domain = "", $secure = false, $httpOnly = false) {
		$this->_cookies [$name] = array (
				'value' => $value,
				'expire' => $expire,
				'path' => $path,
				'domain' => $domain,
				'secure' => $secure,
				'httpOnly' => $httpOnly 
		);
	}
	public function get($name) {
		if ($this->has ( $name ))
			return $this->_cookies [$name];
	}
	public function has($name) {
		return isset ( $this->_cookies [$name] );
	}
	public function delete($name) {
		if ($this->has ( $name ))
			unset ( $this->_cookies [$name] );
	}
	public function send() {
		foreach ( $this->_cookies as $name => $cookie ) {
			$func = $this->_setcookie ;
			$func ( 
					$name,
					$cookie ['value'], 
					$cookie ['expire'], 
					$cookie ['path'], 
					$cookie ['domain'], 
					$cookie ['secure'], 
					$cookie ['httpOnly'] 
			);
		}
	}
	public function reset() {
		$this->_cookies = array ();
	}
	
	public function toArray(){
		return $this->_cookies ;
	}
	public function merge(CookiesInterface $cookies){
		$this->_cookies = array_merge($this->_cookies,$cookies->toArray());
	}
	public static function getClient($name){
		return $_COOKIE[$name] ;
	}
	public static function hasClient($name){
		return isset($_COOKIE[$name]);
	}
	public static function deleteClient($name){
		if(self::hasClient($name))unset($_COOKIE[$name]);
	}
	public static function resetClient(){
		foreach ($_COOKIE as $name => $value){
			unset($_COOKIE[$name]);
		}
	} 
}