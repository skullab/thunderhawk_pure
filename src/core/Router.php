<?php

namespace Thunderhawk;
  
use Thunderhawk\Router\RouterInterface;
use Thunderhawk\Router\Route;

class Router implements RouterInterface{
	
	const HTTP_METHOD_GET = 'GET' ;
	const HTTP_METHOD_POST = 'POST';
	const HTTP_METHOD_PUT = 'PUT' ;
	const HTTP_METHOD_PATCH = 'PATCH' ;
	const HTTP_METHOD_DELETE = 'DELETE' ;
	const HTTP_METHOD_OPTIONS = 'OPTIONS' ;
	const HTTP_METHOD_HEAD = 'HEAD' ;
	
	private $defaults = array();
	private $routes = array();
	private $matches ;
	private $macthed_route = null ;
	private $requested_uri ;
	
	public function setDefaultModule($moduleName) {
		$this->defaults['module'] = (string)$moduleName;
	}

	
	public function setDefaultController($controllerName) {
		$this->defaults['controller'] = (string)$controllerName;
	}

	
	public function setDefaultAction($actionName) {
		$this->defaults['action'] = (string)$actionName;
	}

	
	public function setDefaults(array $defaults) {
		$this->defaults = $defaults ;
	}

	
	public function handle($uri = null) {
		$this->requested_uri = $uri ? $uri : $_GET['_url'] ;
		if($this->wasMatched()){
			// call route handler 
		}
	}

	
	public function add($pattern, $handler = array(), $httpMethods = array()) {
		$route = $pattern instanceof Route ? $pattern : new Route($pattern,$handler,$httpMethods);
		$this->routes[$route->getRouteId()] = $route ;
		return $route ;
	}

	
	public function addGet($pattern, $handler) {
		return $this->add($pattern,$handler,array(self::HTTP_METHOD_GET));
	}

	
	public function addPost($pattern, $handler) {
		return $this->add($pattern,$handler,array(self::HTTP_METHOD_POST));
	}

	
	public function addPut($pattern, $handler) {
		return $this->add($pattern,$handler,array(self::HTTP_METHOD_PUT));
	}

	
	public function addPatch($pattern, $handler) {
		return $this->add($pattern,$handler,array(self::HTTP_METHOD_PATCH));
	}

	
	public function addDelete($pattern, $handler) {
		return $this->add($pattern,$handler,array(self::HTTP_METHOD_DELETE));
	}

	
	public function addOptions($pattern, $handler) {
		return $this->add($pattern,$handler,array(self::HTTP_METHOD_OPTIONS));
	}

	
	public function addHead($pattern, $handler) {
		return $this->add($pattern,$handler,array(self::HTTP_METHOD_HEAD));
	}

	
	public function mount($group) {
		// TODO: Auto-generated method stub

	}

	
	public function clear() {
		// TODO: Auto-generated method stub

	}

	
	public function getModuleName() {
		$handler = $this->macthed_route->getHandler();
		if(array_key_exists('module', $handler)){
			$name = is_int($handler['module']) ? $this->getMatches()[$handler['module']] : $handler['module'] ;
			return $name ;
		}
		return null ;
	}

	
	public function getNamespaceName() {
		// TODO: Auto-generated method stub

	}

	
	public function getControllerName() {
		$handler = $this->macthed_route->getHandler();
		if(array_key_exists('controller', $handler)){
			$name = is_int($handler['controller']) ? $this->getMatches()[$handler['controller']] : $handler['controller'] ;
			return $name ;
		}
		return null ;
	}

	
	public function getActionName() {
		$handler = $this->macthed_route->getHandler();
		if(array_key_exists('action', $handler)){
			$name = is_int($handler['action']) ? $this->getMatches()[$handler['action']] : $handler['action'] ;
			return $name ;
		}
		return null ;
	}

	
	public function getParams() {
		$handler = $this->macthed_route->getHandler();
		if(array_key_exists('params', $handler)){
			$params = is_int($handler['params']) ? $this->getMatches()[$handler['params']] : $handler['params'] ;
			if(is_array($params)){
				//todo resolve params
				return $params;
			}
			return array($params);
		}else{
			$params = explode('/',rtrim(ltrim(substr($this->requested_uri, strlen($this->matches[0])),'/'),'/'));
			return $params ;
		}
	}

	
	public function getMatchedRoute() {
		return $this->macthed_route ;
	}

	
	public function getMatches() {
		return $this->matches ;
	}

	
	public function wasMatched() {
		foreach ($this->routes as $route){
			$match = preg_match($route->getCompiledPattern(), $this->requested_uri,$this->matches);
			if(false !== $match && $match > 0){
				$this->macthed_route = $route ;
				return true ;
			}
		}
		return false ;
	}

	
	public function getRoutes() {
		return array_values($this->routes);
	}

	
	public function getRouteById($id) {
		if(array_key_exists($id, $this->routes)){
			$route = array_keys($this->routes,$id);
			return $route[0] ;
		}
		return null ;
	}

	
	public function getRouteByName($name) {
		foreach ($this->routes as $route){
			if($name == $route->getName){
				return $route ;
			}
		}
		return null ;
	}

}