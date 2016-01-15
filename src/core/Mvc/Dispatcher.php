<?php

namespace Thunderhawk\Mvc;

use Thunderhawk\Di\InjectionInterface;
use Thunderhawk\Di\ContainerInterface;
use Thunderhawk\Mvc\Dispatcher\DispatcherInterface;
use Thunderhawk\Mvc\Controller\ControllerInterface;
use Thunderhawk\Events\EventsAwareInterface;
use Thunderhawk\Events\Manager\ManagerInterface;

class Dispatcher implements InjectionInterface, DispatcherInterface, EventsAwareInterface {
	protected $_controllerSuffix;
	protected $_actionSuffix;
	protected $_defaultController;
	protected $_defaultAction;
	protected $_defaultNamespace;
	protected $_di;
	protected $_controllerName;
	protected $_actionName;
	protected $_actionValue;
	protected $_moduleName;
	protected $_namespaceName;
	protected $_params = array ();
	protected $_isFinished = false;
	protected $_wasForwarded = false;
	protected $_lastController = null;
	protected $_eventsManager = null;
	public function __construct(ContainerInterface $di) {
		$this->setDi ( $di );
		$this->setControllerSuffix ( 'Controller' );
		$this->setActionSuffix ( 'Action' );
		$this->setDefaultController ( 'index' );
		$this->setDefaultAction ( 'index' );
	}
	public function setDi(ContainerInterface $di) {
		$this->_di = $di;
	}
	public function getDi() {
		return $this->_di;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::setControllerSuffix()
	 */
	public function setControllerSuffix($controllerSuffix) {
		$this->_controllerSuffix = ( string ) $controllerSuffix;
	}
	public function getControllerSuffix() {
		return $this->_controllerSuffix;
	}
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::setDefaultController()
	 */
	public function setDefaultController($controllerName) {
		$this->_defaultController = ( string ) $controllerName;
	}
	public function getDefaultController() {
		return $this->_defaultController;
	}
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::setControllerName()
	 */
	public function setControllerName($controllerName) {
		$this->_controllerName = ( string ) $controllerName;
	}
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::getControllerName()
	 */
	public function getControllerName() {
		return $this->_controllerName;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::getLastController()
	 */
	public function getLastController() {
		return $this->_lastController;
	}
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::getActiveController()
	 */
	public function getActiveController() {
		$active = ucfirst ( is_null ( $this->getControllerName () ) ? $this->getDefaultController () : $this->getControllerName () );
		$active .= $this->getControllerSuffix ();
		return $active;
	}
	public function getActiveMethod() {
		$active = is_null ( $this->getActionName () ) ? $this->getDefaultAction () : $this->getActionName ();
		$active .= $this->getActionSuffix ();
		return $active;
	}
	public function getActiveNamespace() {
		$active = is_null ( $this->getNamespaceName () ) ? $this->getDefaultNamespace () : $this->getNamespaceName ();
		return $active;
	}
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::setActionSuffix()
	 */
	public function setActionSuffix($actionSuffix) {
		$this->_actionSuffix = ( string ) $actionSuffix;
	}
	public function getActionSuffix() {
		return $this->_actionSuffix;
	}
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::setDefaultNamespace()
	 */
	public function setDefaultNamespace($defaultNamespace) {
		$this->_defaultNamespace = ( string ) $defaultNamespace;
	}
	public function getDefaultNamespace() {
		return $this->_defaultNamespace;
	}
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::setDefaultAction()
	 */
	public function setDefaultAction($actionName) {
		$this->_defaultAction = ( string ) $actionName;
	}
	public function getDefaultAction() {
		return $this->_defaultAction;
	}
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::setNamespaceName()
	 */
	public function setNamespaceName($namespaceName) {
		$this->_namespaceName = ( string ) $namespaceName;
	}
	public function getNamespaceName() {
		return $this->_namespaceName;
	}
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::setModuleName()
	 */
	public function setModuleName($moduleName) {
		$this->_moduleName = ( string ) $moduleName;
	}
	public function getModuleName() {
		return $this->_moduleName;
	}
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::setActionName()
	 */
	public function setActionName($actionName) {
		$this->_actionName = ( string ) $actionName;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::getActionName()
	 */
	public function getActionName() {
		return $this->_actionName;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::setParams()
	 */
	public function setParams(array $params) {
		$this->_params = $params;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::getParams()
	 */
	public function getParams() {
		return $this->_params;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::setParam()
	 */
	public function setParam($param, $value) {
		$this->_params [$param] = $value;
	}
	public function hasParam($param) {
		return isset ( $this->_params [$param] );
	}
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::getParam()
	 */
	public function getParam($param, $filters = null) {
		if (! $this->hasParam ( $param ))
			return null;
		if (! is_null ( $filters ) && $this->getDi ()->serviceExists ( 'filter' )) {
			return $this->getDi ()->filter->sanitize ( $this->_params [$param], $filters );
		}
		return $this->_params [$param];
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::isFinished()
	 */
	public function isFinished() {
		return $this->_isFinished;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::getReturnedValue()
	 */
	public function getReturnedValue() {
		return $this->_actionValue;
	}
	public function setReturnedValue($value) {
		$this->_actionValue = $value;
	}
	public function getHandlerClass() {
		return is_null ( $this->getActiveNamespace () ) ? $this->getActiveController () : $this->getActiveNamespace () . "\\" . $this->getActiveController ();
	}
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::dispatch()
	 */
	public function dispatch() {
		if ($this->fireEvent ( 'dispatch:beforeDispatchLoop' ))
			return;
		while ( ! $this->isFinished () ) {
			try {
				if ($this->fireEvent ( 'dispatch:beforeDispatch' ))
					break;
				$controllerClass = $this->getHandlerClass ();
				if ((get_class ( $this->_lastController ) !== $controllerClass)) {
					if (! (class_exists ( $controllerClass ))) {
						var_dump ( "controller $controllerClass doesn't exists" );
						throw new \Exception ( "controller $controllerClass doesn't exists" );
					}
					$controller = new $controllerClass ();
					if (! ($controller instanceof ControllerInterface)) {
						// throw exception
						var_dump ( 'controller is not instance of ControllerInterface' );
					}
					$this->fireEvent ( 'dispatch:initialize', null, false );
					$controller->initialize ();
					$controller->setDi ( $this->getDi () );
					$this->_lastController = $controller;
				}
				if (method_exists ( $controller, $this->getActiveMethod () )) {
					if ($this->fireEvent ( 'dispatch:beforeExecuteRoute' ))
						break;
					call_user_func_array ( array (
							$controller,
							$this->getActiveMethod () 
					), $this->getParams () );
					$this->fireEvent ( 'dispatch:afterExecuteRoute', null, false );
				} else {
					if ($this->fireEvent ( 'dispatch:beforeNotFoundAction' ))
						break;
					$action = $this->getActiveMethod ();
					var_dump("action $action doesn't exists");
					throw new \Exception ( "action $action doesn't exists" );
				}
				
				$this->_isFinished = ! $this->wasForwarded ();
				$this->_wasForwarded = false;
				$this->fireEvent ( 'dispatch:afterDispatch', null, false );
			} catch ( \Exception $e ) {
				if ($this->fireEvent ( 'dispatch:beforeException', $e ))
					break;
				throw $e;
			}
		}
		$this->fireEvent ( 'dispatch:afterDispatchLoop', null, false );
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::forward()
	 */
	public function forward(array $forward) {
		$this->_wasForwarded = true;
		if (isset ( $forward ['namespace'] ))
			$this->setNamespaceName ( $forward ['namespace'] );
		if (isset ( $forward ['module'] ))
			$this->setModuleName ( $forward ['module'] );
		if (isset ( $forward ['controller'] ))
			$this->setControllerName ( $forward ['controller'] );
		if (isset ( $forward ['action'] ))
			$this->setActionName ( $forward ['action'] );
		if (isset ( $forward ['params'] ))
			$this->setParams ( $forward ['params'] );
	}
	public function wasForwarded() {
		return $this->_wasForwarded;
	}
	protected function fireEvent($eventType, $data = null, $cancelable = true) {
		if ($this->getEventsManager () != null) {
			$event = $this->_eventsManager->fire ( $eventType, $this, $data, $cancelable );
			return $event->isStopped ();
		}
		return false;
	}
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Events\EventsAwareInterface::setEventsManager()
	 */
	public function setEventsManager(ManagerInterface $eventsManager) {
		$this->_eventsManager = $eventsManager;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Events\EventsAwareInterface::getEventsManager()
	 */
	public function getEventsManager() {
		return $this->_eventsManager;
	}
} 