<?php

namespace Thunderhawk\Mvc\Url;

interface UrlInterface {
	public function setBaseUri($baseUri);
	public function getBaseUri();
	public function setBasePath($basePath);
	public function getBasePath();
	public function get($uri, $args, $local);
	public function path($path);
}