<?php
namespace MyApp\Controllers;
use Thunderhawk\Mvc\Controller;

class IndexController extends Controller{
	public function indexAction(){
		var_dump('index action');
		var_dump($this->dispatcher->getParam('language'));
	}
	
	public function initialize(){
		var_dump('index controller initialize');
	}
}