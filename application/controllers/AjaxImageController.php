<?php

class AjaxImageController extends Zend_Controller_Action
{

	public function init()
	{
        /* Initialize action controller here */
	}

	public function indexAction()
	{
		$having = "";
		$str = "";

		// Disable layout for ajax request
		$this->_helper->layout->disableLayout();

		// Create ajax mapper and generate arguments 

		// Do we want latest images or tagged images
		if(array_key_exists("latestgalleryimages", $_GET))
		{
			$ajaximage = new Application_Model_AjaxMapper();
			$this->view->entries = $ajaximage->getLatestImages($_GET['latestgalleryimages']);
		}
		else
		{		
			if(array_key_exists("having", $_GET) and sizeof($_GET) == 1)
			{
				$size = 0;
			}
			else
			{
				$str = "(";
				foreach($_GET as $key=>$value)
				{
					if($key == 'having' and $value != "")
						continue;

					$str = $str . "'" . $key ."'";

					if($key != array_pop(array_keys($_GET)))
						$str = $str . ", ";
				}

				// Set size to size of GET array
				$size = sizeof($_GET);

				// Last word is partial
				if(array_pop(array_keys($_GET)) == 'having')
				{
					$str = substr($str, 0, -2);		
					$size = $size - 1;
				}	

				// Last character must be )
				$str = $str . ")";
			}

			// Do we have partial word
			if(array_key_exists("having", $_GET))
			{
				$having = $_GET['having'];
				$size = $size + 1;
			}

			// Get all images from database and move those to views entries
			$ajaximage = new Application_Model_AjaxMapper();
			$this->view->entries = $ajaximage->getTaggedImages($str, $having, $size);
		}
	}
}

