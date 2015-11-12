<?php
echo 'start<br>';
use Thunderhawk\Autoloader;
use Thunderhawk\Db\PDO\Dsn;
use Thunderhawk\Db\PDO\Connection\Connector;
use Thunderhawk\Db\PDO\Connection\Map;
use Thunderhawk\Db\PDO\Connection\ConnectionException;
use Thunderhawk\Db\PDO\Connection\Pool;
use Thunderhawk\Router\Route;
use Thunderhawk\Router;
use Thunderhawk\Db\Database;
use Thunderhawk\Mvc\Model;
use Thunderhawk\Utils;
use Thunderhawk\Mvc\Model\Resultset;
use Thunderhawk\Mvc\Model\MetaData;
use Thunderhawk\Di\Container;
use Thunderhawk\Db\Thunderhawk\Db;

require '../src/core/Autoloader.php';
$loader = new Autoloader ( '../src/' );
$loader->registerNamespaces ( array (
		'Thunderhawk' => 'core/',
		'Thunderhawk\Plugin' => 'plugins/',
		'Thunderhawk\Module' => 'modules/' 
) )->register();
/************************************************************************************/
/*									TEST AREA 										*/
/************************************************************************************/
$info = array(
		'tag'		=> 'master',
		'prefix'	=> 'mysql',
		'dbname'	=> 'thunderhawk',
		'host'		=> 'localhost',
		'user'		=>	'root',
		'password'	=> '',
		'options'	=> array(Database::OPT_ERRMODE,Database::ERRMODE_SILENT)
);


$di = new Container();
$di->set('db', function($di) use($info){
	return new Database($info);
});

class Users extends Model {
	private $id ;
	//private $username ;
	//public $password ;
	
	/*public function getTableName(){
		return 'all' ;
	}*/
	public function getId(){
		return $this->id ;
	}
	public function getDi(){
		global $di ;
		return $di ;
	}
	protected function initialize(){
		 
	}

}

$user = new Users();
$user->username = 'testing' ;
$user->password = 'newpassword' ;
var_dump($user->create());

