<?php

namespace Thunderhawk\Http\Response\Cookies;

interface CookiesInterface {
	public function useUrlEncoding($useUrlEncoding);
	public function isUsingUrlEncoding();
	public function set($name, $value, $expire, $path, $domain, $secure ,$httpOnly);
	public function get($name);
	public function has($name);
	public function delete($name);
	public function send();
	public function reset();
	public function toArray();
	public function merge(CookiesInterface $cookies);
}