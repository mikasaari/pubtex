<?php

class AjaxTagsController extends Zend_Controller_Action
{

	public function init()
	{
		/* Initialize action controller here */
	}

	public function indexAction()
	{
		// Disable layout for ajax request
		$this->_helper->layout->disableLayout();

		// Do we want latest images or tagged images
		if(array_key_exists("q", $_GET))
		{
			$ajaximage = new Application_Model_AjaxMapper();
			$this->view->entries = $ajaximage->getTagsWithName($_GET['q']);
		}
		else
			$this->view->entries = array();
	}
}

