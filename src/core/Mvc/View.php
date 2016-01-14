<?php

namespace Thunderhawk\Mvc;
use Thunderhawk\Di\InjectionInterface;
use Thunderhawk\Di\ContainerInterface;
use Thunderhawk\Mvc\View\ViewInterface;
use Thunderhawk\Events\EventsAwareInterface;
use Thunderhawk\Events\Manager\ManagerInterface;

class View implements InjectionInterface,ViewInterface,EventsAwareInterface{
		const LEVEL_NO_RENDER = 0 ;
	const LEVEL_MAIN_LAYOUT = 1 ;
	const LEVEL_BEFORE_TEMPLATE = 2 ;
	const LEVEL_LAYOUT = 3 ;
	const LEVEL_ACTION_VIEW = 4 ;
	const LEVEL_AFTER_TEMPLATE = 5;
	
	protected $_di ;
	
	protected $_levels = array(
			self::LEVEL_NO_RENDER => false,
			self::LEVEL_MAIN_LAYOUT => true,
			self::LEVEL_BEFORE_TEMPLATE => true,
			self::LEVEL_LAYOUT => true,
			self::LEVEL_ACTION_VIEW => true,
			self::LEVEL_AFTER_TEMPLATE => true,
	);
	
	protected $_viewsDir ;
	protected $_layoutDir ;
	protected $_partialsDir ;
	protected $_basePath ;
	protected $_renderLevel ;
	protected $_mainView ;
	protected $_layout ;
	protected $_templateBefore = array() ;
	protected $_templateAfter = array() ;
	protected $_content ;
	protected $_engines = array();
	protected $_engineEntities = array();
	protected $_eventsManager = null ;
	protected $_controllerName ;
	protected $_actionName ;
	protected $_params = array() ;
	protected $_activeRenderLevel ;
	
	public function __construct(){
		
	}
	public function setDi(ContainerInterface $di) {
		$this->_di = $di ;

	}

	public function getDi() {
		return $this->_di ;
	}

	public function __get($var){
		if(isset($this->_params[$var])){
			return $this->_params[$var] ;
		}
	}
	
	public function __set($var,$value){
		$this->_params[$var] = $value ;
	}
	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::setLayoutsDir()
	 */
	public function setLayoutsDir($layoutsDir) {
		$this->_layoutDir = $layoutsDir ;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::getLayoutsDir()
	 */
	public function getLayoutsDir() {
		return $this->_layoutDir ;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::setPartialsDir()
	 */
	public function setPartialsDir($partialsDir) {
		$this->_partialsDir = $partialsDir ;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::getPartialsDir()
	 */
	public function getPartialsDir() {
		return $this->_partialsDir ;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::setBasePath()
	 */
	public function setBasePath($basePath) {
		$this->_basePath = $basePath ;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::getBasePath()
	 */
	public function getBasePath() {
		return $this->_basePath ;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::setRenderLevel()
	 */
	public function setRenderLevel($level) {
		$this->_renderLevel = $level ;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::setMainView()
	 */
	public function setMainView($viewPath) {
		$this->_mainView = $viewPath ;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::getMainView()
	 */
	public function getMainView() {
		return $this->_mainView ;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::setLayout()
	 */
	public function setLayout($layout) {
		$this->_layout = $layout ;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::getLayout()
	 */
	public function getLayout() {
		return $this->_layout ;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::setTemplateBefore()
	 */
	public function setTemplateBefore($templateBefore) {
		if(is_array($templateBefore)){
			$this->_templateBefore = $templateBefore ;
		}else{
			$this->_templateBefore[] = (string)$templateBefore ;
		}

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::cleanTemplateBefore()
	 */
	public function cleanTemplateBefore() {
		$this->_templateBefore = array();

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::setTemplateAfter()
	 */
	public function setTemplateAfter($templateAfter) {
		if(is_array($templateAfter)){
			$this->_templateAfter = $templateAfter ;
		}else{
			$this->_templateAfter[] = (string)$templateAfter ;
		}

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::cleanTemplateAfter()
	 */
	public function cleanTemplateAfter() {
		$this->_templateAfter = array();

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::getControllerName()
	 */
	public function getControllerName() {
		return $this->_controllerName ;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::getActionName()
	 */
	public function getActionName() {
		return $this->_actionName ;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::getParams()
	 */
	public function getParams() {
		return $this->_params ;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::start()
	 */
	public function start() {
		$this->loadTemplateEngines();
		ob_start();
		return $this ;
	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::registerEngines()
	 */
	public function registerEngines($engines) {
		foreach ($engines as $ext => $engine){
			$this->_engines[$ext] = $engine ;
		}
	}
	public function getRegisteredEngines(){
		return $this->_engines ;
	}
	
	protected function loadTemplateEngines(){
		foreach ($this->getRegisteredEngines() as $ext => $engineClass){
			$this->_engineEntities[$engineClass] = new $engineClass($this,$this->getDi());
		}
	}
	
	protected function engineRender($engines,$viewPath){
		foreach ($engines as $ext => $engineClass){
			var_dump($viewPath.$ext);
			if($this->exists($viewPath.$ext)){
				if($this->fireEvent('view:beforeRenderView',$this->_activeRenderLevel))return;
				
				$this->_engineEntities[$engineClass]->render($viewPath.$ext,$this->getParams());
				$this->setContent($this->_engineEntities[$engineClass]->getContent());
				return;
			}
		}
		$this->fireEvent('view:notFoundView',$this->_activeRenderLevel,false);
	}
	
	public function exists($view){
		return file_exists($view);
	}
	
	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::render()
	 */
	public function render($controllerName, $actionName, $params = null) {
		$this->_controllerName = $controllerName ;
		$this->_actionName = $actionName ;
		$this->_params = $params != null ? $params : $this->_params ;
		if($this->fireEvent('view::beforeRender'))return;
		if(!$this->isDisabled()){
			//render process
			for($this->_activeRenderLevel = 1 ; $this->_activeRenderLevel < count($this->_levels) ; $this->_activeRenderLevel++){
				$activeView = $this->getActiveRenderPath();
				$this->engineRender($this->getRegisteredEngines(), $activeView);
				$this->fireEvent('view:afterRenderView',$this->_activeRenderLevel,false);
			}
			
		}
		$this->fireEvent('view:afterRender',null,false);
		return $this ;
	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::pick()
	 */
	public function pick($renderView) {
		// TODO: Auto-generated method stub

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::finish()
	 */
	public function finish() {
		ob_end_clean();
	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::getActiveRenderPath()
	 */
	public function getActiveRenderPath() {
		$path = $this->getBasePath().$this->getViewsDir() ;
		switch ($this->_activeRenderLevel){
			case self::LEVEL_MAIN_LAYOUT :
				return $path.$this->getMainView();
				break;
			case self::LEVEL_BEFORE_TEMPLATE:
				return $path.$this->getLayoutsDir().'/'.current($this->_templateBefore);
				break;
			case self::LEVEL_LAYOUT:
				$path .= $this->getLayoutsDir() ;
				$view = $this->getLayout() ? $this->getLayout() : $this->getControllerName() ;
				return $path.$view ;
				break;
			case self::LEVEL_ACTION_VIEW:
				return $path.$this->getControllerName().'/'.$this->getActionName() ;
				break;
			case self::LEVEL_AFTER_TEMPLATE:
				return $path.$this->getLayoutsDir().'/'.current($this->_templateAfter);
				break;
		}
	}

	public function disableLevel($level){
		if(is_int($level) && array_key_exists($level, $this->_levels)){
			$this->_levels[$level] = false ;
		}else if(is_array($level)){
			foreach ($level as $key){
				if(array_key_exists($key, $this->_levels)){
					$this->_levels[$key] = false ;
				}
			}
		}
	}
	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::disable()
	 */
	public function disable() {
		$this->_levels[self::LEVEL_NO_RENDER] = true ;
	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::enable()
	 */
	public function enable() {
		$this->_levels[self::LEVEL_NO_RENDER] = false ;
	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::reset()
	 */
	public function reset() {
		// TODO: Auto-generated method stub

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::isDisabled()
	 */
	public function isDisabled() {
		return $this->_levels[self::LEVEL_NO_RENDER] ;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::setViewsDir()
	 */
	public function setViewsDir($viewsDir) {
		$this->_viewsDir = $viewsDir ;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::getViewsDir()
	 */
	public function getViewsDir() {
		return $this->_viewsDir ;

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::setParamToView()
	 */
	public function setParamToView($key, $value) {
		return $this->setVar($key, $value);
	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::setVar()
	 */
	public function setVar($key, $value) {
		return $this->__set($key, $value);
	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::getParamsToView()
	 */
	public function getParamsToView() {
		return $this->_params ;
	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::getCache()
	 */
	public function getCache() {
		// TODO: Auto-generated method stub

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::cache()
	 */
	public function cache($options) {
		// TODO: Auto-generated method stub

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::setContent()
	 */
	public function setContent($content) {
		$this->_content = $content ;
	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::getContent()
	 */
	public function getContent() {
		return $this->_content ;
	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\ViewInterface::partial()
	 */
	public function partial($partialPath, $params) {
		// TODO: Auto-generated method stub

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