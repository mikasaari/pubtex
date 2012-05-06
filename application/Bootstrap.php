<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initRegisterControllerPlugins()
	{
		$this->bootstrap('FrontController');
		$front = $this->getResource('FrontController');
		$acl = new Access_Acl();
		$aplg = new Access_Plugin_Acl($acl);
		$front->registerPlugin($aplg);
	}

	protected function _initJquery()
	{
		$this->bootstrap('view');
		$view = $this->getResource('view');
		$view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");
		$view->jQuery()->enable()
			->setVersion('1.5')
			->setUiVersion('1.8')
			->addStylesheet('/css/jquery-ui.css')
			->uiEnable();
	}
}

