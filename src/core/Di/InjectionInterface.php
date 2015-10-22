<?php

namespace Thunderhawk\Di;

interface InjectionInterface {
	public function setDi(ContainerInterface $di);
	public function getDi();
}