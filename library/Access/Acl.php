<?php
class Access_Acl extends Zend_Acl 
{
	public function __construct() 
	{
		//Add a new role called "guest"
		$this->addRole(new Zend_Acl_Role('guest'));

		//Add a role called user, which inherits from guest
		$this->addRole(new Zend_Acl_Role('user'), 'guest');

		//Add a resource called page
		$this->add(new Zend_Acl_Resource('index'));
		$this->add(new Zend_Acl_Resource('error'));
		$this->add(new Zend_Acl_Resource('auth'));
		//$this->add(new Zend_Acl_Resource('details'));
		$this->add(new Zend_Acl_Resource('ajax-media'));
		//$this->add(new Zend_Acl_Resource('ajax-file'));
		//$this->add(new Zend_Acl_Resource('media-details'));
		$this->add(new Zend_Acl_Resource('file-download'));

		//Add a resource called news, which inherits page
		//$this->add(new Zend_Acl_Resource('rating'), 'media');

		//Finally, we want to allow guests to view pages
		$this->allow('guest', 'index', 'view');
		$this->allow('guest', 'error', 'view');
		$this->allow('guest', 'auth', 'view');
		//$this->allow('guest', 'details', 'view');
		$this->allow('guest', 'ajax-media', 'view');
		//$this->allow('guest', 'ajax-file', 'view');
		//$this->allow('guest', 'media-details', 'view');
		$this->allow('guest', 'file-download', 'view');

		//and users can comment news
		//$this->allow('user', 'rating', 'rate');
	}
}


