<?php

namespace Thunderhawk\Http;

use Thunderhawk\Di\InjectionInterface;
use Thunderhawk\Http\Request\RequestInterface;
use Thunderhawk\Di\ContainerInterface;
use Thunderhawk\Filter;

class Request implements InjectionInterface, RequestInterface {
	
	const GET = 'GET';
	const POST = 'POST';
	const PUT = 'PUT';
	const PATCH = 'PATCH';
	const HEAD = 'HEAD';
	const DELETE = 'DELETE';
	const OPTIONS = 'OPTIONS';
	const REQUEST = 'REQUEST' ;
	protected $_di;
	protected $_filter ;
	
	public function __construct(){
		$this->_filter = $this->getDi()->serviceExist('filter') ? $this->getDi()->filter : new Filter() ;
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
		return $this->getHelper(self::REQUEST, $name,$filters,$defaultValue);
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getPost()
	 */
	public function getPost($name, $filters = null, $defaultValue = null) {
		return $this->getHelper(self::POST, $name,$filters,$defaultValue);
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getQuery()
	 */
	public function getQuery($name, $filters = null, $defaultValue = null) {
		return $this->getHelper(self::GET, $name,$filters,$defaultValue);
	}
	public function getHelper($source, $name, $filters = null, $defaultValue = null) {
		$value = null ;
		switch ($source) {
			case self::GET :
				$value = isset($_GET[$name]) ? $_GET[$name] : (is_null($defaultValue) ? null : $defaultValue);
				break;
			case self::POST :
				$value = isset($_POST[$name]) ? $_POST[$name] : (is_null($defaultValue) ? null : $defaultValue);
				break;
			case self::PUT :
				//TODO
				break;
			default :
				$value = isset($_REQUEST[$name]) ? $_REQUEST[$name] : (is_null($defaultValue) ? null : $defaultValue);
		}
		return is_null($filters) ? $value : $this->_filter->sanitize($value, $filters);
	}
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getServer()
	 */
	public function getServer($name) {
		return $_SERVER[$name];
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::has()
	 */
	public function has($name) {
		return isset($_REQUEST[$name]);
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::hasPost()
	 */
	public function hasPost($name) {
		return isset($_POST[$name]);
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
		return isset($_GET[$name]);
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::hasServer()
	 */
	public function hasServer($name) {
		return isset($_SERVER[$name]);
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getHeader()
	 */
	public function getHeader($header) {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getScheme()
	 */
	public function getScheme() {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::isAjax()
	 */
	public function isAjax() {
		// TODO: Auto-generated method stub
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
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getRawBody()
	 */
	public function getRawBody() {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getServerAddress()
	 */
	public function getServerAddress() {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getServerName()
	 */
	public function getServerName() {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getHttpHost()
	 */
	public function getHttpHost() {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getClientAddress()
	 */
	public function getClientAddress($trustForwardedHeader = false) {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getMethod()
	 */
	public function getMethod() {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getUserAgent()
	 */
	public function getUserAgent() {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::isMethod()
	 */
	public function isMethod($methods, $strict) {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::isPost()
	 */
	public function isPost() {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::isGet()
	 */
	public function isGet() {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::isPut()
	 */
	public function isPut() {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::isHead()
	 */
	public function isHead() {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::isDelete()
	 */
	public function isDelete() {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::isOptions()
	 */
	public function isOptions() {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::hasFiles()
	 */
	public function hasFiles($onlySuccessful) {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getUploadedFiles()
	 */
	public function getUploadedFiles($onlySuccessful) {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getHTTPReferer()
	 */
	public function getHTTPReferer() {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getAcceptableContent()
	 */
	public function getAcceptableContent() {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getBestAccept()
	 */
	public function getBestAccept() {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getClientCharsets()
	 */
	public function getClientCharsets() {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getBestCharset()
	 */
	public function getBestCharset() {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getLanguages()
	 */
	public function getLanguages() {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getBestLanguage()
	 */
	public function getBestLanguage() {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getBasicAuth()
	 */
	public function getBasicAuth() {
		// TODO: Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc)
	 * @see \Thunderhawk\Http\Request\RequestInterface::getDigestAuth()
	 */
	public function getDigestAuth() {
		// TODO: Auto-generated method stub
	}
}