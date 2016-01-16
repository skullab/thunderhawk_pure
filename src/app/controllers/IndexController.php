<?php

namespace MyApp\Controllers;
use Thunderhawk\Mvc\Controller;
use MyApp\Models\Users;

class IndexController extends Controller{
	
	public function indexAction(){
		$this->view->myValue = 'test' ;
	}
	
	public function showAction($year,$month,$title){
		echo "you wanna see the post of $year/$month with title '$title'";
	}
}