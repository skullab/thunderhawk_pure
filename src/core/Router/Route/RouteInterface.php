<?php

namespace Thunderhawk\Router\Route;

interface RouteInterface {
	public function __construct($pattern, $handler = array(), $httpMethods = array());
	public function compilePattern($pattern);
	public function via($httpMethods);
	public function reConfigure($pattern, $handler = array());
	public function getName();
	public function setName($name);
	public function setHttpMethods($httpMethods = array());
	public function getRouteId();
	public function getPattern();
	public function getCompiledPattern();
	public function getHandler();
	public function getReversedHandler();
	public function getHttpMethods();
	public function reset();
}