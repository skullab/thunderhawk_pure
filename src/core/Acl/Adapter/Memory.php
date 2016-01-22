<?php

namespace Thunderhawk\Acl\Adapter;

use Thunderhawk\Acl\Adapter;
use Thunderhawk\Acl\Role;
use Thunderhawk\Acl\RoleInterface;

class Memory extends Adapter {
	protected $_rolesNames;
	protected $_roles;
	protected $_resourcesNames;
	protected $_resources;
	protected $_access;
	protected $_roleInherits;
	protected $_accessList;
	public function __construct() {
		$this->_resourcesNames ["*"] = true;
		$this->_accessList ["*!*"] = true;
	}
	public function addRole($role, $accessInherits = null) {
		$roleName = '';
		$roleObject = null;
		
		if ($role instanceof RoleInterface) {
			$roleName = $role->getName ();
			$roleObject = $role;
		} else {
			$roleName = $role;
			$roleObject = new Role ( $role );
		}
		
		if (isset ( $this->_rolesNames [$roleName] )) {
			return false;
		}
		
		$this->_roles [] = $roleObject;
		$this->_rolesNames [$roleName] = true;
		$this->_access [$roleName . "!*!*"] = $this->_defaultAccess;
		
		if ($accessInherits != null) {
			return $this->addInherit ( $roleName, $accessInherits );
		}
		
		return true;
	}
	public function addInherit($roleName, $roleToInherit) {
	}
}