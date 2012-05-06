<?php

class AjaxMediaController extends Zend_Controller_Action
{

	public function init()
	{
		/* Initialize action controller here */
	}

	public function indexAction()
	{
		$having = "";
		$str = "";
		$start = 0;

		// Disable layout for ajax request
		$this->_helper->layout->disableLayout();
		
		// Create Media Mapper
		$ajaximage = new Application_Model_MediaMapper();

		// Find latest images
		if(array_key_exists("n", $_GET))
		{
			// If there are start id (s) specified then we need to set start position
			if(array_key_exists("s", $_GET))
			{
				$start = $_GET['s'];
			}

			// Get latest Media from SQL and parse them to JSON format
			$entries = $ajaximage->getNewMedia($_GET['n'], $start);

			// Parse the Object array to be string array
			$mediarr = array();
			foreach(array_reverse($entries, true) as $key=>$values)
			{		
				$first_entry = array_shift(array_keys($values->getFiles()));
				$mediarr[] = array("id" => $values->getId(), "media_hash" => $values->getHashName(), "file_hash" => $first_entry );
			}

			// Set these entries to view entries
			$this->view->entries = $mediarr;
		}
	
		// Get details
		else if(array_key_exists("d", $_GET))
		{
			// Get details for specific media hash name
			$details = $ajaximage->getMediaDetails($_GET['d']);

			// Parse the object to string
			$mediadet = array();
			$mediadet['hash_name'] = $details->getHashName();
			$mediadet['type'] = $details->getType();
			$mediadet['id'] = $details->getId();
			$mediadet['user'] = $details->getUser();
			$mediadet['created'] = $details->getCreated();
			$mediadet['description'] = $details->getDescription();
			$tags = array();
			foreach($details->getTags() as $key => $value)
			{
				$tags[$key] = $value;
			}
			$mediadet['tags'] = $tags;
			$files = array();
			foreach($details->getFiles() as $key => $value)
			{
				$files[$key] = $value;
				error_log("KEY: $key VALUE: $value");
			}
			$mediadet['files'] = $details->getFiles();
		
			// Set one media object data string to view entries
			$this->view->entries = $mediadet;
		}

		// Search with tags
		else
		{	
			if(array_key_exists("ha", $_GET) and sizeof($_GET) == 1)
			{
				$size = 0;
			}
			else
			{
				$str = "(";
				foreach($_GET as $key=>$value)
				{
					if($key == 'ha' and $value != "")
						continue;

					$str = $str . "'" . $key ."'";

					if($key != array_pop(array_keys($_GET)))
						$str = $str . ", ";
				}

				// Set size to size of GET array
				$size = sizeof($_GET);

				// Last word is partial
				if(array_pop(array_keys($_GET)) == 'ha')
				{
					$str = substr($str, 0, -2);		
					$size = $size - 1;
				}	

				// Last character must be )
				$str = $str . ")";
			}

			// Do we have partial word
			if(array_key_exists("ha", $_GET))
			{
				$having = $_GET['ha'];
				$size = $size + 1;
			}

			// Get all images from database and move those to views entries
			$entries = $ajaximage->getTaggedImages($str, $having, $size);

			// Parse the object array to be string array for JSON
			$mediarr = array();
			foreach($entries as $key=>$values)
			{		
				$first_entry = array_shift(array_keys($values->getFiles()));
				$mediarr[] = array("file_hash" => $first_entry, "media_hash" => $values->getHashName());
			}

			// Set these entries to view entries
			$this->view->entries = $mediarr;
		}
	}

	public function test()
	{

	}
}

