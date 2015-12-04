<?php

namespace Thunderhawk\Mvc;

use Thunderhawk\Di\InjectionInterface;
use Thunderhawk\Di\ContainerInterface;
use Thunderhawk\Mvc\Dispatcher\DispatcherInterface;
use Thunderhawk\Filter\String;

class Dispatcher implements InjectionInterface,DispatcherInterface{
	protected $_controllerSuffix ;
	protected $_actionSuffix ;
	protected $_defaultController ;
	protected $_defaultAction ;
	protected $_defaultNamespace ;
	protected $_di ;
	protected $_controllers ;
	protected $_toBeDispatched = array() ;
	
	public function __construct(ContainerInterface $di){
		$this->setDi($di);
		$this->setControllerSuffix('Controller');
		$this->setActionSuffix('Action');
		$this->setDefaultController('index');
		$this->setDefaultAction('index');
		$this->_controllers = new \ArrayObject();
	}
	public function setDi(ContainerInterface $di) {
		$this->_di = $di ;
	}

	
	public function getDi() {
		return $this->_di ;
	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::setControllerSuffix()
	 */
	public function setControllerSuffix($controllerSuffix) {
		$this->_controllerSuffix = (string)$controllerSuffix;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::setDefaultController()
	 */
	public function setDefaultController($controllerName) {
		$this->_defaultController = (string)$controllerName;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::setControllerName()
	 */
	public function setControllerName($controllerName) {
		$this->_toBeDispatched['name'] = (string)$controllerName;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::getControllerName()
	 */
	public function getControllerName() {
		return $this->_toBeDispatched['name'] ;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::getLastController()
	 */
	public function getLastController() {
		// TODO: Auto-generated method stub

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::getActiveController()
	 */
	public function getActiveController() {
		return $this->_controllers->getIterator()->current();

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::setActionSuffix()
	 */
	public function setActionSuffix($actionSuffix) {
		$this->_actionSuffix = (string)$actionSuffix;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::setDefaultNamespace()
	 */
	public function setDefaultNamespace($defaultNamespace) {
		$this->_defaultNamespace = (string)$defaultNamespace;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::setDefaultAction()
	 */
	public function setDefaultAction($actionName) {
		$this->_defaultAction = (string)$actionName;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::setNamespaceName()
	 */
	public function setNamespaceName($namespaceName) {
		$this->_toBeDispatched['namespace'] = (string)$namespaceName;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::setModuleName()
	 */
	public function setModuleName($moduleName) {
		$this->_toBeDispatched['module'] = (string)$moduleName;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::setActionName()
	 */
	public function setActionName($actionName) {
		$this->_toBeDispatched['action'] = (string)$actionName;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::getActionName()
	 */
	public function getActionName() {
		return $this->_toBeDispatched['action'] ;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::setParams()
	 */
	public function setParams($params) {
		$this->_toBeDispatched['params'] = $params ;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::getParams()
	 */
	public function getParams() {
		return $this->_toBeDispatched['params'];

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::setParam()
	 */
	public function setParam($param, $value) {
		$this->_toBeDispatched['params'][$param] = $value ;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::getParam()
	 */
	public function getParam($param, $filters = null) {
		if(isset($this->_toBeDispatched['params'][$param])){
			return is_null($filters) ? 
			$this->_toBeDispatched['params'][$param] : 
			($this->getDi()->serviceExist('filter') ? 
			$this->getDi()->filter->sanitize($this->_toBeDispatched['params'][$param],$filters) :
			$this->_toBeDispatched['params'][$param]
			) ;
		
		}
		return null ;
	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::isFinished()
	 */
	public function isFinished() {
		// TODO: Auto-generated method stub

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::getReturnedValue()
	 */
	public function getReturnedValue() {
		// TODO: Auto-generated method stub

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::dispatch()
	 */
	public function dispatch() {
		
	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::forward()
	 */
	public function forward($forward) {
		// TODO: Auto-generated method stub

	}

} 