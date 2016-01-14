<?php

namespace Thunderhawk\Mvc\View\Engine;

interface EngineInterface {
	public function render($viewPath,$params);
	public function getContent();
	public function partial($partialPath,$params);
}