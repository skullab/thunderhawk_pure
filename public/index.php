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
});

class Phtml extends Engine{
	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\View\Engine::__construct()
	 */
	public function __construct($view, $di = null) {
		parent::__construct($view,$di);

	}
	public function render($viewPath, $params){
		foreach ($params as $key => $value){
			$this->{$key} = $value ;
		}
		require ''.$viewPath ;
	}
}

$em = new EventsManager();
$em->attach('view',function($event,$component){
	var_dump($event->getType(),$event->getData());
});
$view = new View();
$view->setDi($di);
$view->setBasePath('../src/');
$view->setViewsDir('app/views/');
$view->registerEngines(array(
			'.phtml' => 'Phtml'
));
$view->setEventsManager($em);
$view->start()->render('blog', 'index',array('title' => 'My Blog Title'))->finish() ;
echo $view->getContent();








