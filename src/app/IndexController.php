<?php
namespace MyApp\Controllers;
use Thunderhawk\Mvc\Controller;

class IndexController extends Controller{
	
	public function indexAction(){
		$this->view->myValue = 'ciao' ;
		echo 'from controller' ;
	}
	
	public function testAction(){
		echo 'test action' ;
	}
	public function initialize(){
		var_dump('index controller initialize');
	}
}