<?php

namespace Thunderhawk\Mvc\View\Engine;
use Thunderhawk\Mvc\View\Engine;

class Php extends Engine{
	public function __construct($view, $di = null) {
		parent::__construct($view,$di);
	
	}
	public function render($viewPath, $params){
		extract($params);
		require ''.$viewPath ;
	}
}