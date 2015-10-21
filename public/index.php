<?php
echo 'start<br>' ;
use Thunderhawk\Autoloader;
use Thunderhawk\Di\Container;
use Thunderhawk\Plugin\Test ;
use Thunderhawk\Parser\Ini;
use Thunderhawk\Parser\Configuration;
use Thunderhawk\Db\PDO\Mysql;
use Thunderhawk\Db\PDO\Sqlite;
use Thunderhawk\Di\Thunderhawk\Di;
use Thunderhawk\Mvc\Model;
use Thunderhawk\Plugin\Thunderhawk\Plugin;
use Thunderhawk\Mvc\Model\MetaData;


require '../src/core/Autoloader.php';


$loader = new Autoloader('../src/');

$loader->setPriorities(array(
		Autoloader::NAMESPACES => Autoloader::PRIORITY_HIGH
));
$loader->registerNamespaces(array(
		'Thunderhawk'		=> 'core/',
		'vendor'			=> 'path/to/lib/',
		'vendor\package'	=> 'path/to/lib/sub/'
));
$loader->registerNamespaces(array(
	'Thunderhawk\Plugin'	=> 'plugins/'	
),true);
$loader->registerPrefixes(array(
		'vendor_' 			=> 'path/to/lib/',
		'vendor_package'	=> 'path/to/lib/sub/'
));
$loader->registerDirs(array(
		'path/to/lib/',
		'path/to/lib/sub/',
		'path/to/lib/prefixClass_'
));
$loader->registerClasses(array(
	'MyClass'				=> 'path/to/lib/',
	'MyNamespace\MyClass'	=> 'path/to/lib/sub/'
));

$loader->register();

$di = new Container();
$di->set('test',function(){
	return new Test();
},true);


$data = array(
		'test' => true,
		'section' => array(
				'value01' => 1e16,
				'value02' => 'string',
				'value03' => false,
				'value04' => null,
				'value05' => 11.0E+18
		),
		'db'	=> array(
				'user'		=> 'johndoe',
				'password'	=> '1254$87ABC',
				'host'		=> 'localhost'
		)
		
);
//var_dump($data);

$db_config = new Configuration(array(
		'db' => array(
				'host' 		=> 'localhost',
				'dbname'	=> 'thunderhawk',
				'username' 	=> 'root',
				'password' 	=> ''
		)
));

$db_config_2 = new Configuration('../src/config/test.ini');
//var_dump((array)$db_config_2->options);
//$db = new Mysql($db_config_2->db);

$di = new Container();
$di->set('db',function() use($db_config_2){
	return new Mysql($db_config_2->db);
},true);

/*Model::find();
$stm = $di->db->query('SELECT * from users') ;
$stm->setFetchMode(PDO::FETCH_CLASS,'Thunderhawk\Plugin\Test',array('test'=>'prova'));
foreach ($stm as $row){
	var_dump($row);
}*/

class Users extends Model{
	protected $id ;
	public $name ;
	public $password ;
}

/*$users = Users::find(1);
foreach ($users as $user){
	$user->name = 'other' ;	
	var_dump($user->update());
}*/
$user = new Users();
$user->name = 'ciao' ;
$user->password = '999' ;
$user->update();





