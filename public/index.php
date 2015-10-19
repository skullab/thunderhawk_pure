<?php
echo 'start<br>' ;
use Thunderhawk\Autoloader;
use Thunderhawk\Di\Container;
use Thunderhawk\Plugin\Test ;

require '../src/core/Autoloader.php';


$loader = new Autoloader('../src/');
//$loader->demandLoader('Symfony\Component\ClassLoader\ClassLoader');

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
//$loader->addExtension('conf.php');
$loader->register();
//$test = new vendor\package\component\MyClass();
//$test = new vendor_package_component_MyClass();
//$test = new MyClass();*/

$di = new Container();
$di->set('test',function(){
	return new Test();
},true);

$di->test->set('foo');
var_dump($di->test->get());
$di->test->set('bar');
$value = $di->get('test')->get();
var_dump($value);

var_dump($di->isShared('test'));
var_dump($di->isOverridable('test'));