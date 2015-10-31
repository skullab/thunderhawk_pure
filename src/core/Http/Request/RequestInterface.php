<?php

namespace Thunderhawk\Http\Request;

interface RequestInterface {
	public function get($name, $filters, $defaultValue);
	public function getPost($name, $filters, $defaultValue);
	public function getQuery($name, $filters, $defaultValue);
	public function getServer($name);
	public function has($name);
	public function hasPost($name);
	public function hasPut($name);
	public function hasQuery($name);
	public function hasServer($name);
	public function getHeader($header);
	public function getScheme();
	public function isAjax();
	public function isSoapRequested();
	public function isSecureRequest();
	public function getRawBody();
	public function getServerAddress();
	public function getServerName();
	public function getHttpHost();
	public function getClientAddress($trustForwardedHeader);
	public function getMethod();
	public function getUserAgent();
	public function isMethod($methods, $strict);
	public function isPost();
	public function isGet();
	public function isPut();
	public function isHead();
	public function isDelete();
	public function isOptions();
	public function hasFiles($onlySuccessful);
	public function getUploadedFiles($onlySuccessful);
	public function getHTTPReferer();
	public function getAcceptableContent();
	public function getBestAccept();
	public function getClientCharsets();
	public function getBestCharset();
	public function getLanguages();
	public function getBestLanguage();
	public function getBasicAuth();
	public function getDigestAuth();
}