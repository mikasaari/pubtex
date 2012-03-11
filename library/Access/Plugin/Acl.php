<?php

class Access_Plugin_Acl extends Zend_Controller_Plugin_Abstract {
	private $_acl = null;
 
	public function __construct(Zend_Acl $acl) 
	{
		$this->_acl = $acl;
	}
 
	public function preDispatch(Zend_Controller_Request_Abstract $request) 
	{
		//As in the earlier example, authed users will have the role user
		$role = (Zend_Auth::getInstance()->hasIdentity())
			? 'user'
			: 'guest';
 
		//For this example, we will use the controller as the resource:
		$resource = $request->getControllerName();
 
		if(!$this->_acl->isAllowed($role, $resource, 'view')) 
		{
			//If the user has no access we send him elsewhere by changing the request
			$request->setModuleName('auth')
				->setControllerName('auth')
				->setActionName('login');
		}
	}
}
