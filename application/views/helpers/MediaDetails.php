<?php

class Zend_View_Helper_MediaDetails extends Zend_View_Helper_Abstract
{
	protected $_baseurl = null;
	protected $_images = array();

	public function __construct()
	{
		$url = Zend_Controller_Front::getInstance()->getRequest()->getBaseUrl();
		$root = "/" . trim($url, '/');
		if('/' == $root)
		{
			$root = '';
		}
		$this->_baseurl = $root . '/';
	}

	public function mediaDetailsMediaDescription()
	{
		
	}

	public function mediaDetailsMediaPresentation($mediahash)
	{	
		
	}

	public function mediaDetailsMediaTechDetails()
	{
	}
}
