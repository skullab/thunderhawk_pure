<?php
// echo 'start<br>';
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
use Thunderhawk\Events\Manager as EventsManager;
use Thunderhawk\Events\Thunderhawk\Events;
use Thunderhawk\Thunderhawk;

require '../src/core/Autoloader.php';
$loader = new Autoloader ( '../src/' );
$loader->registerNamespaces ( array (
		'MyApp\Controllers' => 'app/',
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

$dispatcher = new Dispatcher ( $di );
$di->set ( 'dispatcher', function ($di) use($dispatcher) {
	return $dispatcher;
} );
$em = new EventsManager ();
$em->attach ( 'dispatch', function ($event, $component) {
	var_dump($event->getType());
} );
$em->attach('router', function($event,$component){
	var_dump($event->getType());
});
$dispatcher->setEventsManager ( $em );
//$dispatcher->setDefaultNamespace ( 'MyApp\Controllers' );

$router = new Router(false);
$di->set('router',function($di)use($router){
	return $router ;
});
$router->setEventsManager($em);

$router->setDefaultNamespace('MyApp\Controllers');
$router->setDefaultController('index');
$router->setDefaultAction('index');
$router->add('/',array(
		'params' => array(
				'language' => 'en'
		)
));
$router->add('/([a-z]{2})/:action',array(
		'controller' =>'index',
		'action' => 2,
		'language' => 1,
		/*'params' => array(
				'language' => 1
		)*/
),array('POST','GET'));

$router->add('/:action',array(
		'controller' =>'index',
		'action' => 1,
		'language' => 'en'
		/*'params' => array(
				'language' => 'en'
		)*/
));

$router->handle();
$dispatcher->setNamespaceName($router->getNamespaceName());
$dispatcher->setControllerName($router->getControllerName());
$dispatcher->setActionName($router->getActionName());
$dispatcher->setParams($router->getParams() ? $router->getParams() : array());

echo $router->getAttribute('language');

$dispatcher->dispatch();