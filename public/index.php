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
use Thunderhawk\Mvc\View;
use Thunderhawk\Mvc\View\Engine;
use Thunderhawk\Mvc\Thunderhawk\Mvc;



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

$di->set('dispatcher',function($di){
	$dispatcher = new Dispatcher($di);
	return $dispatcher ;
},true);

$di->set('router',function($di){
	$router = new Router();
	$router->setDi($di);
	$router->setDefaultNamespace('MyApp\Controllers');
	return $router ;
},true);

$di->set('view',function($di){
	$view = new View();
	$view->setDi($di);
	$view->setBasePath('../src/');
	$view->setViewsDir('app/views/');
	return $view ;
},true);


$di->router->handle();
$di->dispatcher->setNamespaceName($di->router->getNamespaceName());
$di->dispatcher->setControllerName($di->router->getControllerName());
$di->dispatcher->setActionName($di->router->getActionName());
$di->dispatcher->setParams($di->router->getParams());

$di->view->start();
try {
$di->dispatcher->dispatch();
}catch (\Exception $e){
	//throw $e ;
}
$di->view->render($di->router->getControllerName(),$di->router->getActionName(),$di->router->getParams());
$di->view->finish();

echo $di->view->getContent() ;


