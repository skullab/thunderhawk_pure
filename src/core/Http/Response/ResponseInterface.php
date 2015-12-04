<?php

namespace Thunderhawk\Http\Response;

interface ResponseInterface {
	public function setStatusCode($code, $message);
	public function getStatusCode();
	public function getHeaders();
	public function setHeader($name, $value,$replace,$statusCode);
	public function setRawHeader($header,$replace,$statusCode);
	public function resetHeaders();
	public function setExpires($datetime);
	public function setNotModified();
	public function setContentType($contentType, $charset);
	public function redirect($location, $externalRedirect, $statusCode);
	public function setContent($content);
	public function setJsonContent($content);
	public function appendContent($content);
	public function getContent();
	public function sendHeaders();
	public function sendCookies();
	public function send();
	public function setFileToSend($filePath, $attachmentName);
}