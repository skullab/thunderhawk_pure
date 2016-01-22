<?php

namespace Thunderhawk\Acl;

interface RoleInterface {
	public function getName ();
	public function getDescription ();
	public function __toString();
}