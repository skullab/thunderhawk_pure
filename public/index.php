<?php
//echo 'start<br>';
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
use Thunderhawk\Mvc\Model\Criteria;
use Thunderhawk\Mvc\Model\Query;
use Thunderhawk\Mvc\Model\Message;
use Thunderhawk\Filter\StripTags;
use Thunderhawk\Filter;
use Thunderhawk\Http\Request;
use Thunderhawk\Http\Response;
use Thunderhawk\Http\Response\Cookies;
use Thunderhawk\Filter\String;
use Thunderhawk\Mvc\Dispatcher;


require '../src/core/Autoloader.php';
$loader = new Autoloader ( '../src/' );
$loader->registerNamespaces ( array (
		'Thunderhawk' => 'core/',
		'Thunderhawk\Plugin' => 'plugins/',
		'Thunderhawk\Module' => 'modules/' 
) )->register ();
/**
 * *********************************************************************************
 */
/* TEST AREA */
/**
 * *********************************************************************************
 */
$info = array (
		'tag' => 'master',
		'prefix' => 'mysql',
		'dbname' => 'thunderhawk',
		'host' => 'localhost',
		'user' => 'root',
		'password' => '',
		'options' => array (
				Database::OPT_ERRMODE,
				Database::ERRMODE_SILENT 
		) 
);

$di = new Container ();
$di->set ( 'db', function ($di) use($info) {
	return new Database ( $info );
} );


//$dispatcher = new Dispatcher($di);
//$dispatcher->setControllerName('test');
$dispatcher = new Phalcon\Mvc\Dispatcher();
$dispatcher->setControllerName('test');
$dispatcher->forward(array());
var_dump($dispatcher);