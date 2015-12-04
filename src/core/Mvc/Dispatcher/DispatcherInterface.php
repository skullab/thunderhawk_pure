<?php

namespace Thunderhawk\Mvc\Dispatcher;

interface DispatcherInterface {
	public function setControllerSuffix($controllerSuffix);
	public function setDefaultController($controllerName);
	public function setControllerName($controllerName);
	public function getControllerName();
	public function getLastController();
	public function getActiveController();
	public function setActionSuffix($actionSuffix); 
	public function setDefaultNamespace($defaultNamespace); 
	public function setDefaultAction($actionName); 
	public function setNamespaceName($namespaceName); 
	public function setModuleName($moduleName); 
	public function setActionName($actionName); 
	public function getActionName(); 
	public function setParams(array $params); 
	public function getParams(); 
	public function setParam($param, $value); 
	public function getParam($param, $filters); 
	public function isFinished(); 
	public function getReturnedValue(); 
	public function dispatch(); 
	public function forward(array $forward); 
}