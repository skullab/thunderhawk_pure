<?php
namespace Thunderhawk\Di ;
interface ContainerInterface {
	public function set($name,callable $service,$shared = false,$override = false);
	public function get($name);
}