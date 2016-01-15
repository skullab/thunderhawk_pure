<?php
namespace MyApp\Controllers;
use Thunderhawk\Mvc\Controller;

class IndexController extends Controller{
	
	public function indexAction(){
		$this->view->pick('blog/index');
	}
	
	public function testAction(){
		echo 'test action' ;
	}
	public function initialize(){
		var_dump('index controller initialize');
	}
}