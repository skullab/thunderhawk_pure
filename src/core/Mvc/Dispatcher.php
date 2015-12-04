<?php

namespace Thunderhawk\Mvc;

use Thunderhawk\Di\InjectionInterface;
use Thunderhawk\Di\ContainerInterface;
use Thunderhawk\Mvc\Dispatcher\DispatcherInterface;
use Thunderhawk\Mvc\Controller\ControllerInterface;

class Dispatcher implements InjectionInterface, DispatcherInterface {
	protected $_controllerSuffix;
	protected $_actionSuffix;
	protected $_defaultController;
	protected $_defaultAction;
	protected $_defaultNamespace;
	protected $_di;
	protected $_controllerName;
	protected $_actionName;
	protected $_actionValue ;
	protected $_moduleName;
	protected $_namespaceName;
	protected $_params = array ();
	protected $_isFinished = false ;
	protected $_wasForwarded = false ;
	protected $_lastController ;
	
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
	public function getControllerSuffix(){
		return $this->_controllerSuffix ;
	}
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::setDefaultController()
	 */
	public function setDefaultController($controllerName) {
		$this->_defaultController = ( string ) $controllerName;
	}
	public function getDefaultController(){
		return $this->_defaultController ;
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
		return $this->_lastController ;
	}
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::getActiveController()
	 */
	public function getActiveController() {
		$active = is_null($this->getControllerName()) ? $this->getDefaultController() : $this->getControllerName();
		$active .= $this->getControllerSuffix() ;
		return $active ;
	}
	public function getActiveMethod() {
		$active = is_null($this->getActionName()) ? $this->getDefaultAction() : $this->getActionName() ;
		$active .= $this->getActionSuffix() ;
		return $active ;
	}
	public function getActiveNamespace(){
		$active = is_null($this->getNamespaceName()) ? $this->getDefaultNamespace() : $this->getNamespaceName() ;
		return $active ;
	}
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::setActionSuffix()
	 */
	public function setActionSuffix($actionSuffix) {
		$this->_actionSuffix = ( string ) $actionSuffix;
	}
	public function getActionSuffix(){
		return $this->_actionSuffix ;
	}
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::setDefaultNamespace()
	 */
	public function setDefaultNamespace($defaultNamespace) {
		$this->_defaultNamespace = ( string ) $defaultNamespace;
	}
	public function getDefaultNamespace(){
		return $this->_defaultNamespace ;
	}
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::setDefaultAction()
	 */
	public function setDefaultAction($actionName) {
		$this->_defaultAction = ( string ) $actionName;
	}
	
	public function getDefaultAction(){
		return $this->_defaultAction ;
	}
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::setNamespaceName()
	 */
	public function setNamespaceName($namespaceName) {
		$this->_namespaceName = ( string ) $namespaceName;
	}
	
	public function getNamespaceName(){
		return $this->_namespaceName ;
	}
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::setModuleName()
	 */
	public function setModuleName($moduleName) {
		$this->_moduleName = ( string ) $moduleName;
	}
	
	public function getModuleName(){
		return $this->_moduleName ;
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
		return $this->_isFinished ;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::getReturnedValue()
	 */
	public function getReturnedValue() {
		return $this->_actionValue ;
	}
	
	public function setReturnedValue($value){
		$this->_actionValue = $value ;
	}
	public function getHandlerClass(){
		return is_null($this->getActiveNamespace()) ? $this->getActiveController() : 
		$this->getActiveNamespace()."\\".$this->getActiveController();
		
	}
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::dispatch()
	 */
	public function dispatch() {
		while (!$this->isFinished()){
			if(!$this->getDi()->serviceExists('router')){
				// throw exception
			}
			
			$controllerClass = $this->getHandlerClass();
			$controller = new $controllerClass();
			if(!($controller instanceof ControllerInterface)){
				//throw exception
			}
			$this->_lastController = $controller ;
			//check the route
			
			$controller->setDi($this->getDi());
			$controller->initialize();
			$this->_isFinished = true ;
		}
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Dispatcher\DispatcherInterface::forward()
	 */
	public function forward(array $forward) {
		$this->_wasForwarded = true ;
		$this->setControllerName($forward['controller']);
		$this->setActionName($forward['action']);
		
	}
	public function wasForwarded(){
		return $this->_wasForwarded ;
	}
} 