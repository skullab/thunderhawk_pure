<?php

namespace Thunderhawk\Mvc;

use Thunderhawk\Mvc\Url\UrlInterface;
use Thunderhawk\Di\InjectionInterface;
use Thunderhawk\Di\ContainerInterface;

class Url implements UrlInterface, InjectionInterface {
	protected $_di;
	protected $_baseUri;
	protected $_basePath;
	protected $_staticBaseUri;
	public function __construct(ContainerInterface $di = null) {
		if (! is_null ( $di ))
			$this->setDi ( $di );
	}
	public function setBaseUri($baseUri) {
		$this->_baseUri = $baseUri;
	}
	public function getBaseUri() {
		if (! is_null ( $this->_baseUri ))
			return $this->_baseUri;
		$requested = isset ( $_GET ['_url'] ) ? $_GET ['_url'] : '';
		$url = str_replace ( $requested, '/', parse_url ( $_SERVER ['REQUEST_URI'] ) );
		return isset ( $url ['path'] ) ? $url ['path'] : '/';
	}
	public function setStaticBaseUri($staticBaseUri) {
		$this->_staticBaseUri = $staticBaseUri;
	}
	public function getStaticBaseUri() {
		return $this->_staticBaseUri;
	}
	public function setBasePath($basePath) {
		$this->_basePath = $basePath;
	}
	public function getBasePath() {
		return $this->_basePath;
	}
	public function get($uri, $args = array(), $local = true, $baseUri = null) {
		$baseUri = is_null ( $baseUri ) ? $this->getBaseUri () : $baseUri;
		if (is_string ( $uri )) {
			if (! $local) {
				if (preg_match ( "#https?://#", $uri ) === 0) {
					$uri = "http://$uri";
				}
			} else {
				$uri = $baseUri . $uri;
			}
			if (is_array ( $args ) && ! empty ( $args )) {
				$uri .= '?';
				foreach ( $args as $key => $value ) {
					$uri .= "$key=$value&";
				}
				$uri = rtrim ( $uri, '&' );
			}
			return $uri;
		}else if(is_array($uri)){
			$route = $this->getDi()->router->getRouteByName($uri['for']);
			unset($uri['for']);
			if(is_null($route))return $baseUri ;
			$pattern = ltrim($route->getPattern(),'/');
			foreach ($uri as $key => $value){
				$replace = '{'.$key.'}' ;
				$pattern = str_replace($replace, $value, $pattern);
			}
			return $baseUri.$pattern ;
		}
		
	}
	public function getStatic($uri) {
		return $this->getStaticBaseUri().$uri ;
	}
	public function path($path = '') {
		return $this->getBasePath().$path ;
	}
	public function setDi(ContainerInterface $di) {
		$this->_di = $di;
	}
	public function getDi() {
		return $this->_di;
	}
}