<?php

namespace Thunderhawk\Http;

use Thunderhawk\Di\InjectionInterface;
use Thunderhawk\Http\Request\RequestInterface;
use Thunderhawk\Di\ContainerInterface;
use Thunderhawk\Filter;

class Request implements InjectionInterface, RequestInterface {
	/**
	 * Contents of the Accept: header from the current request, if there is one.
	 */
	const HTTP_ACCEPT = 'Accept';
	/**
	 * Contents of the Accept-Charset: header from the current request, if there is one.
	 * Example: 'iso-8859-1,*,utf-8'.
	 */
	const HTTP_ACCEPT_CHARSET = 'Accept-Charset';
	/**
	 * Contents of the Accept-Encoding: header from the current request, if there is one.
	 * Example: 'gzip'.
	 */
	const HTTP_ACCEPT_ENCODING = 'Accept-Encoding';
	/**
	 * Contents of the Accept-Language: header from the current request, if there is one.
	 * Example: 'en'.
	 */
	const HTTP_ACCEPT_LANGUAGE = 'Accept-Language';
	/**
	 * Contents of the Connection: header from the current request, if there is one.
	 * Example: 'Keep-Alive'.
	 */
	const HTTP_CONNECTION = 'Connection';
	const HTTP_CACHE_CONTROL = 'Cache-Control';
	const HTTP_UPGRADE_INSECURE_REQUESTS = 'Upgrade-Insecure-Requests';
	/**
	 * Contents of the Host: header from the current request, if there is one.
	 */
	const HTTP_HOST = 'Host';
	/**
	 * The address of the page (if any) which referred the user agent to the current page.
	 *
	 * This is set by the user agent. Not all user agents will set this,
	 * and some provide the ability to modify HTTP_REFERER as a feature.
	 * In short, it cannot really be trusted.
	 */
	const HTTP_REFERER = 'Referer';
	/**
	 * Contents of the User-Agent: header from the current request, if there is one.
	 *
	 * This is a string denoting the user agent being which is accessing the page.
	 * A typical example is: Mozilla/4.5 [en] (X11; U; Linux 2.2.9 i586).
	 * Among other things, you can use this value with get_browser() to tailor your page's output
	 * to the capabilities of the user agent.
	 */
	const HTTP_USER_AGENT = 'User-Agent';
	const GET = 'GET';
	const POST = 'POST';
	const PUT = 'PUT';
	const PATCH = 'PATCH';
	const HEAD = 'HEAD';
	const DELETE = 'DELETE';
	const OPTIONS = 'OPTIONS';
	const REQUEST = 'REQUEST';
	protected $_di;
	protected $_filter;
	public function __construct(ContainerInterface $di = null) {
		if (! is_null ( $di )) {
			$this->setDi ( $di );
		} else {
			// GET FROM GLOBAL APP
		}
		$this->_filter = $this->getDi ()->serviceExist ( 'filter' ) ? $this->getDi ()->filter : new Filter ();
	}
	public function setDi(ContainerInterface $di) {
		$this->_di = $di;
	}
	public function getDi() {
		return $this->_di;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::get()
	 */
	public function get($name, $filters = null, $defaultValue = null) {
		return $this->getHelper ( self::REQUEST, $name, $filters, $defaultValue );
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getPost()
	 */
	public function getPost($name, $filters = null, $defaultValue = null) {
		return $this->getHelper ( self::POST, $name, $filters, $defaultValue );
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getQuery()
	 */
	public function getQuery($name, $filters = null, $defaultValue = null) {
		return $this->getHelper ( self::GET, $name, $filters, $defaultValue );
	}
	public function getHelper($source, $name, $filters = null, $defaultValue = null) {
		$value = null;
		switch ($source) {
			case self::GET :
				$value = isset ( $_GET [$name] ) ? $_GET [$name] : (is_null ( $defaultValue ) ? null : $defaultValue);
				break;
			case self::POST :
				$value = isset ( $_POST [$name] ) ? $_POST [$name] : (is_null ( $defaultValue ) ? null : $defaultValue);
				break;
			case self::PUT :
				// TODO
				break;
			default :
				$value = isset ( $_REQUEST [$name] ) ? $_REQUEST [$name] : (is_null ( $defaultValue ) ? null : $defaultValue);
		}
		return is_null ( $filters ) ? $value : $this->_filter->sanitize ( $value, $filters );
	}
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getServer()
	 */
	public function getServer($name) {
		return $_SERVER [$name];
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::has()
	 */
	public function has($name) {
		return isset ( $_REQUEST [$name] );
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::hasPost()
	 */
	public function hasPost($name) {
		return isset ( $_POST [$name] );
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::hasPut()
	 */
	public function hasPut($name) {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::hasQuery()
	 */
	public function hasQuery($name) {
		return isset ( $_GET [$name] );
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::hasServer()
	 */
	public function hasServer($name) {
		return isset ( $_SERVER [$name] );
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getHeader()
	 */
	public function getHeader($header) {
		$headers = getallheaders ();
		return isset ( $headers [$header] ) ? $headers [$header] : null;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getScheme()
	 */
	public function getScheme() {
		return isset ( $_SERVER ['REQUEST_SCHEME'] ) ? $_SERVER ['REQUEST_SCHEME'] : null;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::isAjax()
	 */
	public function isAjax() {
		return (! empty ( $_SERVER ['HTTP_X_REQUESTED_WITH'] ) && strtolower ( $_SERVER ['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest');
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::isSoapRequested()
	 */
	public function isSoapRequested() {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::isSecureRequest()
	 */
	public function isSecureRequest() {
		return (isset ( $_SERVER ['HTTPS'] ) && ! empty ( $_SERVER ['HTTPS'] ) && $_SERVER ['HTTPS'] !== 'off');
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getRawBody()
	 */
	public function getRawBody() {
		$body = '';
		foreach ( getallheaders () as $name => $value ) {
			$body .= "$name : $value \n";
		}
		return $body;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getServerAddress()
	 */
	public function getServerAddress() {
		return $_SERVER ['SERVER_ADDR'];
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getServerName()
	 */
	public function getServerName() {
		return $_SERVER ['SERVER_NAME'];
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getHttpHost()
	 */
	public function getHttpHost() {
		return $_SERVER ['HTTP_HOST'];
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getClientAddress()
	 */
	public function getClientAddress($trustForwardedHeader = false) {
		return ($trustForwardedHeader ? (isset ( $_SERVER ['HTTP_X_FORWARDED_FOR'] ) ? $_SERVER ['HTTP_X_FORWARDED_FOR'] : $_SERVER ['REMOTE_ADDR']) : $_SERVER ['REMOTE_ADDR']);
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getMethod()
	 */
	public function getMethod() {
		return $_SERVER ['REQUEST_METHOD'];
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getUserAgent()
	 */
	public function getUserAgent() {
		return $_SERVER ['HTTP_USER_AGENT'];
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::isMethod()
	 */
	public function isMethod($methods, $strict = false) {
		return ($strict ? ($this->getMethod () === $methods) : ($this->getMethod () == $methods));
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::isPost()
	 */
	public function isPost() {
		return $this->isMethod ( self::POST, true );
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::isGet()
	 */
	public function isGet() {
		return $this->isMethod ( self::GET, true );
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::isPut()
	 */
	public function isPut() {
		return $this->isMethod ( self::PUT, true );
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::isHead()
	 */
	public function isHead() {
		return $this->isMethod ( self::HEAD, true );
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::isDelete()
	 */
	public function isDelete() {
		return $this->isMethod ( self::DELETE, true );
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::isOptions()
	 */
	public function isOptions() {
		return $this->isMethod ( self::OPTIONS, true );
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::hasFiles()
	 */
	public function hasFiles($onlySuccessful = false) {
		if (empty ( $_FILES ))
			return false;
		if (! $onlySuccessful)
			return true;
		foreach ( $_FILES as $file ) {
			if ($file ['error'] != UPLOAD_ERR_OK)
				return false;
		}
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getUploadedFiles()
	 */
	public function getUploadedFiles($onlySuccessful = false) {
		$files = array ();
		foreach ( $_FILES as $file ) {
			$file = $onlySuccessful ? ($file ['error'] == UPLOAD_ERR_OK ? $file : null) : $file;
			if (! is_null ( $file ))
				$files [] = $file;
		}
		return $files;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getHTTPReferer()
	 */
	public function getHTTPReferer() {
		return $_SERVER ['HTTP_REFERER'];
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getAcceptableContent()
	 */
	public function getContent($target) {
		var_dump ( $target );
		$result = array ();
		$pattern = '/;q=[0-9].[0-9],?/';
		$count = preg_match_all ( $pattern, $target, $quality );
		if ($count > 0) {
			$names = preg_split ( $pattern, $target );
			
			for($i = 0; $i < $count; $i ++) {
				$_names = explode ( ',', $names [$i] );
				foreach ( $_names as $n ) {
					$result [$n] = ( float ) str_replace ( ';q=', '', $quality [0] [$i] );
				}
			}
		}else{
			$keys = explode(',', str_replace(' ','', $target)) ;
			foreach ($keys as $key){
				$result[$key] = (float)1 ;
			}
			
		}
		return $result;
	}
	public function getAcceptableContent() {
		return $this->getContent($_SERVER['HTTP_ACCEPT']);
	}
	
	protected function getBestContent(array $content){
		arsort($content,SORT_NUMERIC);
		reset($content);
		return key($content);
	}
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getBestAccept()
	 */
	public function getBestAccept() {
		return $this->getBestContent($this->getAcceptableContent());
		
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getClientCharsets()
	 */
	public function getClientCharsets() {
		return isset($_SERVER['HTTP_ACCEPT_CHARSET']) ? $this->getContent($_SERVER['HTTP_ACCEPT_CHARSET']) : array() ;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getBestCharset()
	 */
	public function getBestCharset() {
		return $this->getBestContent($this->getClientCharsets());
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getLanguages()
	 */
	public function getLanguages() {
		return $this->getContent($_SERVER['HTTP_ACCEPT_LANGUAGE']);
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getBestLanguage()
	 */
	public function getBestLanguage() {
		return $this->getBestContent($this->getLanguages());
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getBasicAuth()
	 */
	public function getBasicAuth() {
		if (!isset($_SERVER['PHP_AUTH_USER'])) return null ;
		return array(
				'PHP_AUTH_USER' => $_SERVER['PHP_AUTH_USER'],
				'PHP_AUTH_PW' => $_SERVER['PHP_AUTH_PW'] ) ;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getDigestAuth()
	 */
	public function getDigestAuth() {
		if(!isset($_SERVER['PHP_AUTH_DIGEST']))return null ;
		// protect against missing data
		$needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
		$data = array();
		$keys = implode('|', array_keys($needed_parts));
		
		preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $_SERVER['PHP_AUTH_DIGEST'], $matches, PREG_SET_ORDER);
		
		foreach ($matches as $m) {
			$data[$m[1]] = $m[3] ? $m[3] : $m[4];
			unset($needed_parts[$m[1]]);
		}
		
		return $needed_parts ? null : $data;
	}
}