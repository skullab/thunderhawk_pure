<?php
echo 'start<br>';
use Thunderhawk\Autoloader;
use Thunderhawk\Db\PDO\Dsn;
use Thunderhawk\Db\PDO\Driver\Bridge;
use Thunderhawk\Db\Database;
use Thunderhawk\Db\PDO\ConnectionPool;



require '../src/core/Autoloader.php';

$loader = new Autoloader ( '../src/' );

/*
 * $loader->setPriorities(array(
 * Autoloader::NAMESPACES => Autoloader::PRIORITY_HIGH
 * ));
 */

$loader->registerNamespaces ( array (
		'Thunderhawk' => 'core/',
		'Thunderhawk\Plugin' => 'plugins/',
		'Thunderhawk\Module' => 'modules/' 
) )->register();

/*$mysql_dsn = Dsn::create('mysql:host=localhost;port=3333;dbname=foo;user=test;password=123');
$sqlite_dsn = Dsn::create('sqlite:path/to/db');
$mysql_dsn->host = 'otherhost' ;
var_dump($sqlite_dsn->prefix);
var_dump($sqlite_dsn->sqlite_param);

var_dump($mysql_dsn->prefix,$mysql_dsn->host,$mysql_dsn->port,$mysql_dsn->user,$mysql_dsn->dbname,$mysql_dsn->option);

var_dump($sqlite_dsn->resolve());
var_dump($mysql_dsn->resolve());

$oracle_dsn = Dsn::createByDriver(Dsn::PREFIX_ORACLE);
$oracle_dsn->dbname = 'mydb' ;
$oracle_dsn->host = 'localhost' ;
$oracle_dsn->port = 3333 ;
$oracle_dsn->charset = 'foo' ;
var_dump($oracle_dsn->resolve());

$alias_dsn = Dsn::create('my_alias');
var_dump($alias_dsn->resolve());

$pgsql = Dsn::createByDriver(Dsn::PREFIX_POSTGRESQL);
$pgsql->user = 'foo' ;
$pgsql->password = '123' ;
$pgsql->host = '127.0.0.1' ;
$pgsql->dbname = 'mydb' ;
$pgsql->port = 3333 ;
var_dump($pgsql->resolve());

$mysql_dsn = Dsn::createByDriver(Dsn::PREFIX_MYSQL);
$mysql_dsn->host = 'localhost';
$mysql_dsn->user = 'foo' ;
$mysql_dsn->dbname = 'mydb' ;
$mysql_dsn->port = 3333 ;
var_dump($mysql_dsn->resolve());

$sqlite_dsn = Dsn::createByDriver(Dsn::PREFIX_SQLITE);
$sqlite_dsn->dbname = 'mydb.db' ;
$sqlite_dsn->user = 'foo' ;
var_dump($sqlite_dsn->resolve());

//Generic ODBC DSN
$odbc = Dsn::create('odbc:DRIVER={IBM DB2 ODBC DRIVER};HOSTNAME=localhost;PORT=50000;DATABASE=SAMPLE;PROTOCOL=TCPIP;UID=db2inst1;PWD=ibmdb2;');
$odbc = Dsn::create('odbc:Driver={Microsoft Access Driver (*.mdb)};Dbq=C:\\db.mdb;Uid=Admin');
var_dump($odbc->resolve());*/

$info = array(
		'tag'		=> 'master',
		'prefix'	=> 'mysql',
		'dbname'	=> 'thunderhawk_0_0_1',
		'host'		=> 'localhost',
		'user'		=>	'root',
		'password'	=> '',
		'options'	=> array(PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT)
);

$info2 = array_merge($info,array('host' => '127.0.0.1'));

$dsn = Dsn::createByArray($info);
$dsn2 = Dsn::createByArray($info);

ConnectionPool::resolveConnection($dsn);
ConnectionPool::resolveConnection($dsn2);








