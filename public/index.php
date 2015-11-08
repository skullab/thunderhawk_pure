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
		'options'	=> array(PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT)
);

$info2 = array(
		'tag'		=> 'master',
		'prefix'	=> 'mysql',
		'dbname'	=> 'thunderhawk',
		'host'		=> '127.0.0.1',
		'user'		=>	'root',
		'password'	=> '',
		'options'	=> array(PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT)
);

$dsn = new Dsn($info);
$dsn2 = new Dsn($info2);

$connector = new Connector($dsn);
$connector2 = new Connector($dsn2);

$map = new Map(array($connector,$connector2));

$pool = new Pool($map);

$db = $pool->getRandomConnection('master');
var_dump($db->getOptions());
$route = new Route('/blog/post/:int/:mixed',array(
		'module'=>'blogmodule',
		'controller'=>'post',
		'action'=>'index',
		'params' => array(
				'id' => 1,
				'title' => 2
		)
),array('POST')); 

$router = new Router();
$router->setDefaultModule('frontend');
$router->setDefaultNamespace('Thunderhawk\Plugin');
$router->addRoute($route);
$router->handle();
var_dump($router->getMatchedRoute());
var_dump($_SERVER);










