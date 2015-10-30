<?php

namespace Thunderhawk\Router;

interface RouterInterface {
	public function setDefaultModule($moduleName);
	public function setDefaultController($controllerName);
	public function setDefaultAction($actionName);
	public function setDefaults(array $defaults);
	public function handle($uri = null);
	public function add($pattern, $handler = array(), $httpMethods = array());
	public function addGet($pattern, $handler);
	public function addPost($pattern, $handler);
	public function addPut($pattern, $handler);
	public function addPatch($pattern, $handler);
	public function addDelete($pattern, $handler);
	public function addOptions($pattern, $handler);
	public function addHead($pattern, $handler);
	public function mount($group);
	public function clear();
	public function getModuleName();
	public function getNamespaceName();
	public function getControllerName();
	public function getActionName();
	public function getParams();
	public function getMatchedRoute();
	public function getMatches();
	public function wasMatched();
	public function getRoutes();
	public function getRouteById($id);
	public function getRouteByName($name);
}