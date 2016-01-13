<?php

namespace Thunderhawk;

use Thunderhawk\Router\RouterInterface;
use Thunderhawk\Router\Route\RouteInterface;
use Thunderhawk\Router\Route;
use Thunderhawk\Di\InjectionInterface;
use Thunderhawk\Di\ContainerInterface;
use Thunderhawk\Events\EventsAwareInterface;
use Thunderhawk\Events\Manager\ManagerInterface;

class Router implements RouterInterface,InjectionInterface,EventsAwareInterface {
	const SOURCE_MODE_GET_URL = 100;
	const SOURCE_MODE_SERVER_REQUEST_URI = 200;
	const HTTP_METHOD_GET = 'GET';
	const HTTP_METHOD_POST = 'POST';
	const HTTP_METHOD_PUT = 'PUT';
	const HTTP_METHOD_PATCH = 'PATCH';
	const HTTP_METHOD_DELETE = 'DELETE';
	const HTTP_METHOD_OPTIONS = 'OPTIONS';
	const HTTP_METHOD_HEAD = 'HEAD';
	private $defaults = array (
			'module' => null,
			'namespace' => null,
			'controller' => null,
			'action' => null 
	);
	private $routes = array ();
	private $matches;
	private $macthed_route = null;
	private $requested_uri;
	private $source_mode = self::SOURCE_MODE_GET_URL;
	protected $_di ;
	protected $_eventsManager ;
	
	public function __construct($setDefault = true) {
		if ($setDefault) {
			$this->setDefaultAction ( 'index' );
			$this->setDefaultController ( 'index' );
			// $this->setDefaultModule('');
			// $this->setDefaultNamespace('');
			$this->add ('/');
			$this->add('/:controller',array('controller'=>1,'action'=>'index'));
			$this->add('/:controller/:action',array('controller'=>1,'action'=>2));
		}
	}
	public function setDefaultModule($moduleName) {
		$this->defaults ['module'] = ( string ) $moduleName;
	}
	public function setDefaultNamespace($namespaceName){
		$this->defaults['namespace'] = (string)$namespaceName;
	}
	public function setDefaultController($controllerName) {
		$this->defaults ['controller'] = ( string ) $controllerName;
	}
	public function setDefaultAction($actionName) {
		$this->defaults ['action'] = ( string ) $actionName;
	}
	public function setDefaults(array $defaults) {
		$this->defaults = $defaults;
	}
	public function setSourceMode($mode) {
		if (is_int ( $mode )) {
			$this->source_mode = $mode;
		}
	}
	public function handle($uri = null) {
		if($this->fireEvent('router:beforeHandleRoute'))return;
		switch ($this->source_mode) {
			case self::SOURCE_MODE_SERVER_REQUEST_URI :
				$url = $_SERVER ['REQUEST_URI'];
				break;
			default :
				$url = isset ( $_GET ['_url'] ) ? $_GET ['_url'] : '/';
		}
		
		$this->requested_uri = $uri ? $uri : $url;
		
		if ($this->wasMatched ()) {
			
			if (! in_array ( $_SERVER ['REQUEST_METHOD'], $this->getMatchedRoute ()->getHttpMethods () )) {
				if($this->fireEvent('router:beforeWrongMethod'))return;
				//throw exception
				var_dump ( 'route matched but wrong http method' );
			}
			
			$module = $this->getModuleName () ? $this->getModuleName () : $this->defaults ['module'];
			$namespace = $this->getNamespaceName() ? $this->getNamespaceName() : $this->defaults['namespace'];
			$controller = $this->getControllerName () ? $this->getControllerName () : $this->defaults ['controller'];
			$action = $this->getActionName () ? $this->getActionName () : $this->defaults ['action'];
			$params = $this->getParams ();
			var_dump($this->getMatches());
			var_dump ( $module, $namespace,$controller, $action, $params );
		} else {
			if($this->fireEvent('router:beforeNotFound',$this->requested_uri))return;
			//throw exception
			var_dump ( 'no matched route' );
		}
		if($this->fireEvent('router:afterHandleRoute'))return;
	}
	public function add($pattern, $handler = array(), $httpMethods = array()) {
		$route = new Route ( $pattern, $handler, $httpMethods );
		return $this->addRoute($route);
	}
	public function addRoute(RouteInterface $route){
		array_unshift($this->routes, $route);
		return $route ;
	}
	public function addGet($pattern, $handler) {
		return $this->add ( $pattern, $handler, array (
				self::HTTP_METHOD_GET 
		) );
	}
	public function addPost($pattern, $handler) {
		return $this->add ( $pattern, $handler, array (
				self::HTTP_METHOD_POST 
		) );
	}
	public function addPut($pattern, $handler) {
		return $this->add ( $pattern, $handler, array (
				self::HTTP_METHOD_PUT 
		) );
	}
	public function addPatch($pattern, $handler) {
		return $this->add ( $pattern, $handler, array (
				self::HTTP_METHOD_PATCH 
		) );
	}
	public function addDelete($pattern, $handler) {
		return $this->add ( $pattern, $handler, array (
				self::HTTP_METHOD_DELETE 
		) );
	}
	public function addOptions($pattern, $handler) {
		return $this->add ( $pattern, $handler, array (
				self::HTTP_METHOD_OPTIONS 
		) );
	}
	public function addHead($pattern, $handler) {
		return $this->add ( $pattern, $handler, array (
				self::HTTP_METHOD_HEAD 
		) );
	}
	public function mount($group) {
		// TODO: Auto-generated method stub
	}
	public function clear() {
		$this->routes = array();
	}
	public function getModuleName() {
		if (! $this->macthed_route)return null;
		$handler = $this->macthed_route->getHandler ();
		if (array_key_exists ( 'module', $handler )) {
			$name = is_int($handler['module']) ? $this->getMatches()[$handler['module']] : $handler ['module'];
			return $name;
		}
		return $this->defaults['module'];
	}
	public function getNamespaceName() {
		if(! $this->macthed_route) return null ;
		$handler = $this->macthed_route->getHandler();
		if(array_key_exists('namespace', $handler)){
			$name = is_int($handler['namespace']) ? $this->getMatches()[$handler['namespace']] : $handler['namespace'] ;
			return $name ;
		}
		return $this->defaults['namespace'] ;
	}
	public function getControllerName() {
		if (! $this->macthed_route) return null ;
		$handler = $this->macthed_route->getHandler ();
		if (array_key_exists ( 'controller', $handler )) {
			$name = is_int($handler['controller']) ? $this->getMatches()[$handler['controller']] : $handler ['controller'];
			// $name = ucfirst($name).'Controller';
			return $name;
		}
		return $this->defaults['controller'];
	}
	public function getActionName() {
		if (! $this->macthed_route) return null ;
		$handler = $this->macthed_route->getHandler ();
		if (array_key_exists ( 'action', $handler )) {
			$name = is_int($handler['action']) ? $this->getMatches()[$handler['action']] : $handler ['action'];
			// $name .= 'Action' ;
			return $name;
		}
		return $this->defaults['action'];
	}
	public function getParams() {
		if (! $this->macthed_route)
			return null;
		$handler = $this->macthed_route->getHandler ();
		if (array_key_exists ( 'params', $handler )) {
			$params = is_int($handler['params']) ? $this->getMatches()[$handler['params']] : $handler ['params'];
			if (is_array ( $params )) {
				foreach ( $params as $key => $param ) {
					if (is_int ( $param )) {
						$params[$key] = @$this->getMatches()[$param] ;
					}
				}
				return $params;
			}
			return explode('/',trim($params,'/'));
		} else {
			//var_dump('get params from url');
			$path = trim(substr($this->requested_uri,strlen($this->matches[0])),'/');
			//var_dump($path);
			if($path !== false && trim($path) != false){
				$params = explode('/',$path);
				return $params ;
			}else return array();
		}
	}
	public function getAttribute($attribute){
		if (! $this->macthed_route)
			return null;
		$handler = $this->macthed_route->getHandler ();
		if(array_key_exists($attribute, $handler)){
			return (is_int($handler[$attribute]) ? $this->getMatches()[$handler[$attribute]] : $handler[$attribute]) ;
		}
		return null ;
		
	}
	public function getMatchedRoute() {
		return $this->macthed_route;
	}
	public function getMatches() {
		return $this->matches;
	}
	public function wasMatched() {
		foreach ( $this->routes as $route ) {
			$match = preg_match ( $route->getCompiledPattern (), $this->requested_uri, $this->matches );
			if (false !== $match && $match > 0) {
				$this->macthed_route = $route;
				return true;
			}
		}
		return false;
	}
	public function getRoutes() {
		return array_values ( $this->routes );
	}
	public function getRouteById($id) {
		foreach ( $this->routes as $route ) {
			if ($id == $route->getRouteId()) {
				return $route;
			}
		}
		return null;
		/*if (array_key_exists ( $id, $this->routes )) {
			$route = array_keys ( $this->routes, $id );
			return $route [0];
		}
		return null;*/
	}
	public function getRouteByName($name) {
		foreach ( $this->routes as $route ) {
			if ($name == $route->getName) {
				return $route;
			}
		}
		return null;
	}
	/* (non-PHPdoc)
	 * @see \Thunderhawk\Di\InjectionInterface::setDi()
	 */
	public function setDi(ContainerInterface $di) {
		$this->_di = $di ;
	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Di\InjectionInterface::getDi()
	 */
	public function getDi() {
		return $this->_di ;
	}
	
	protected function fireEvent($eventType, $data = null, $cancelable = true) {
		if ($this->getEventsManager () != null) {
			$event = $this->_eventsManager->fire ( $eventType, $this, $data, $cancelable );
			return $event->isStopped ();
		}
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see \Thunderhawk\Events\EventsAwareInterface::setEventsManager()
	 */
	public function setEventsManager(ManagerInterface $eventsManager) {
		$this->_eventsManager = $eventsManager ;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Events\EventsAwareInterface::getEventsManager()
	 */
	public function getEventsManager() {
		return $this->_eventsManager ;

	}

}