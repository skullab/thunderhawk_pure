<?php

namespace Thunderhawk\Http;

use Thunderhawk\Di\InjectionInterface;
use Thunderhawk\Http\Response\ResponseInterface;
use Thunderhawk\Http\Response\Cookies\CookiesInterface;
use Thunderhawk\Di\ContainerInterface;
use Thunderhawk\Http\Response\Headers;

class Response implements InjectionInterface, ResponseInterface {
	
	protected $_di;
	protected $_serverProtocol = null ;
	protected $_headers = null ;
	protected $_body = null;
	protected $_fileTosend = false ;
	protected $_cookies = null ;
	
	public function __construct($content = null ,$code = null,$message = null){
		$this->_headers = new Headers();
		$this->_serverProtocol = $_SERVER ["SERVER_PROTOCOL"] ;
		$this->setContent($content);
		$code = is_null($code) ? 200 : (int)$code;
		$message = is_null($message) ? "OK" : (string) $message ;
		$this->setStatusCode($code, $message);
	}
	public function setDi(ContainerInterface $di) {
		$this->_di = $di;
	}
	public function getDi() {
		return $this->_di;
	}
	public function setStatusCode($code, $message) {
		$this->_headers->set($this->_serverProtocol,"$code $message",true,$code);
	}
	public function getStatusCode(){
		return $this->_headers->get($this->_serverProtocol);
	}
	public function getHeaders() {
		return $this->_headers ;
	}
	
	public function setHeader($name, $value,$replace = true,$statusCode = null) {
		$this->_headers->set($name, $value,$replace,$statusCode);
	}
	
	public function setRawHeader($header,$replace = true,$statusCode = null) {
		$this->_headers->setRaw($header,$replace,$statusCode);
	}
	
	public function resetHeaders() {
		$this->_headers->reset();
	}
	public function setCache($control,$mustRevalidate = false){
		$control = $mustRevalidate ? $control.", must-revalidate" : $control ;
		$this->setHeader('Cache-Control', $control);
	}
	public function setEtag($eTag){
		$this->setHeader('ETag', $eTag);
	}
	public function setExpires($datetime) {
		$this->setHeader ( 'Expires', $datetime );
	}
	
	public function setNotModified() {
		$this->setStatusCode ( 304, 'Not Modified' );
	}
	
	public function setContentType($contentType, $charset = null) {
		$contentType = is_null ( $charset ) ? $contentType : "$contentType; charset=$charset";
		$this->setHeader ( 'Content-Type', $contentType );
	}
	
	public function redirect($location, $externalRedirect = false , $statusCode = 302) {
		if($externalRedirect){
			if (preg_match("#https?://#", $location) === 0) {
				$location = "http://$location";
			}
			$this->setHeader('Location',$location,true,$statusCode);
			$this->send();
		}else{
			//internal redirect
		}
	}
	
	public function setCookies(CookiesInterface $cookies){
		$this->_cookies = $cookies ;
	}
	public function getCookies(){
		return $this->_cookies ;
	}
	public function setContent($content) {
		$this->_body = ( string ) $content;
	}
	public function setJsonContent($content) {
		$this->_body = json_encode ( $content );
	}
	public function appendContent($content) {
		$this->_body .= ( string ) $content;
	}
	public function getContent() {
		return $this->_body;
	}
	public function sendHeaders() {
		$this->_headers->send();
	}
	public function sendCookies() {
		if(!is_null($this->getCookies()))$this->getCookies()->send();
	}
	
	public function send() {
		$this->sendHeaders ();
		if($this->_fileTosend !== false){
			readfile($this->_fileTosend);
			exit;
		}
		$this->sendCookies();
		if (! is_null ( $this->getContent() ))echo ($this->getContent());
		//die ();
	}
	public function setFileToSend($filePath, $attachmentName = 'attachment') {
		if (file_exists ( $filePath )) {
			$this->_fileTosend = $filePath ;
			$this->setRawHeader ( 'Content-Description: File Transfer' );
			$this->setRawHeader ( 'Content-Type: application/octet-stream' );
			$this->setRawHeader ( 'Content-Disposition: '.$attachmentName.'; filename="' . basename ( $filePath ) . '"' );
			$this->setRawHeader ( 'Expires: 0' );
			$this->setRawHeader ( 'Cache-Control: must-revalidate' );
			$this->setRawHeader ( 'Pragma: public' );
			$this->setRawHeader ( 'Content-Length: ' . filesize ( $filePath ) );
		}
	}
}