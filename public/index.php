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
		'dbname'	=> 'thunderhawk_0_0_1',
		'host'		=> 'localhost',
		'user'		=>	'root',
		'password'	=> '',
		'options'	=> array(PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT)
);

$info2 = array(
		'tag'		=> 'slave',
		'prefix'	=> 'mysql',
		'dbname'	=> 'thunderhawk_0_0_1',
		'host'		=> '127.0.0.1',
		'user'		=>	'root',
		'password'	=> '',
		'options'	=> array(PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT)
);

$dsn = new Dsn($info);
$dsn2 = new Dsn($info2);

$connection = new Connector($dsn);
$connection2 = new Connector($dsn2);

$map = new Map(array($connection,$connection2));

$pool = new Pool($map);

$db = $pool->getConnection('slave');
$db2 = $pool->getConnection('slave');

var_dump($db);
var_dump($db2);








