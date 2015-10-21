<?php

namespace Thunderhawk\Di;

interface InjectionInterface {
	public function setDi(Container $di);
	public function getDi();
}