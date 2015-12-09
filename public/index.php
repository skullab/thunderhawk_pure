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
use Thunderhawk\Events\Manager as EventsManager ;


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

class MyComponentListener {
	public function beforeTask($event,$component,$data){
		echo 'my-component-listener -> '.$event->getType().'<br>' ;
		echo 'and say : '.$data.'<br>' ;
		//$event->stop();
		return 'after task response';
	}
	public function afterTask($event,$component){
		echo 'my-component-listener -> '.$event->getType().'<br>' ;
	}
	public function finishTask($event,$component){
		echo 'my-component-listener -> '.$event->getType().'<br>' ;
	}
}
class MyComponent {
	protected $em ;
	protected $listener ;
	protected $func01,$func02 ;
	public function __construct(){
		$this->em = new EventsManager();
		$this->em->collectResponses(true);
		$this->listener = new MyComponentListener();
		$this->em->attach('my-component',$this->listener);
	}
	public function _internalListener($event){
		var_dump('internal listener '.$event->getType() );
	}
	public function task(){
		$event = $this->em->fire('my-component:beforeTask', $this,'hello world');
		if($event->isStopped())return;
		echo 'my-component -> task<br>';
		$event = $this->em->fire('my-component:afterTask',$this);
		if($event->isStopped())return;
		$event = $this->em->fire('my-component:finishTask',$this);
		if($event->isStopped())return;
		var_dump($this->em->getResponses());
	}
}

$m = new MyComponent();
$m->task();
