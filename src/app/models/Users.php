<?php

namespace MyApp\Models;
use Thunderhawk\Mvc\Model;

class Users extends Model{
	public $id;
	public $username;
	public $password;
	
	public function initialize(){
		var_dump('model users init');
	}
}