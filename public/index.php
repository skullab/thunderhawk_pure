<?php
echo 'start<br>';
use Thunderhawk\Autoloader;
use Thunderhawk\Db\PDO\Dsn;
use Thunderhawk\Db\PDO\Connection\Connector;
use Thunderhawk\Db\PDO\Connection\Map;
use Thunderhawk\Db\PDO\Connection\ConnectionException;
use Thunderhawk\Db\PDO\Connection\Pool;

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
//$db2 = $pool->getConnection('custom');
//var_dump($db);

$base_uri = 'thunderhawk/';
$uri = substr($_SERVER['REQUEST_URI'],strlen($base_uri));

var_dump($uri);









