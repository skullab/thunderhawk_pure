<?php
echo 'start<br>';
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
use Thunderhawk\Db\Thunderhawk\Db;
use Thunderhawk\Mvc\Model\Criteria;
use Thunderhawk\Mvc\Model\Query;
use Thunderhawk\Mvc\Model\Message;

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
class Users extends Model {
	private $id;
	private $username;
	private $password;
	public static function validate($username, $password) {
	}
	protected function setUsername($value) {
		var_dump ( 'call me', $value );
		$this->username = 'mister ' . $value;
	}
	public function getUsername() {
		return $this->username;
	}
	protected function getPassword() {
		return $this->password;
	}
	protected function setPassword($value) {
		$this->password = crypt ( $value );
	}
	public function getDi() {
		global $di;
		return $di;
	}
	protected function onCreate($record) {
		foreach ( $record as $key => $value ) {
			if(is_null($value) && $key != 'id'){
				$record[$key] = 'default' ;
			}
		}
		return $record ;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model::onCreateFails()
	 */
	protected function onCreateFails($record, $query) {
		$message = new Message ( 'User creation fails !' );
		$message->setErrorInfo ( $query->getErrorMessages () );
		$this->appendMessage ( $message );
	}
	protected function onCreateSucces() {
		$this->appendMessage ( new Message ( 'User creates successful !' ) );
	}
	protected function onUpdate($recordDiff) {
		var_dump ( $recordDiff );
	}
	protected function onDelete($record) {
		var_dump ( 'deleting -> ' . $record ['id'] );
		// return false ;
	}
}

class NoCode extends Model{
	
	protected function getPrimaryKeyName(){
		return 'abc' ;
	}
	public function getDi() {
		global $di;
		return $di;
	}
}

$user = new Users();
var_dump($user->save(array('username'=>'testing user')));
var_dump($user);

