<?php

class FileDownloadController extends Zend_Controller_Action
{
	public function init()
	{
		/* Initialize action controller here */
	}

	public function indexAction()
	{
		// Disable layout for ajax request
		$this->_helper->layout->disableLayout();

		// Create Media Mapper
		$filemapper = new Application_Model_MediaMapper();

		error_log("1.");
		// Download the file
		if(array_key_exists("df", $_GET))
		{
			error_log("2.");
			// Get latest Media from SQL and parse them to JSON format
			$downloadFile = $_GET['df'];

			// Get File details
			$fileprefix = $filemapper->getFilePrefix($downloadFile);
			error_log("FILE PREFIX: $fileprefix");
		
			// Get directory prefixes
			$dirf = substr($downloadFile, 0, 2);
			$dirs = substr($downloadFile, 2, 2);

			// Return file info array
			$path = realpath(APPLICATION_PATH . '/../data/images/' . $dirf .'/'. $dirs .'/'.$downloadFile);
			$this->view->entries = array('file' => $path, 'prefix' => $fileprefix);
		}
		else
		{
			// Something went wrong
			$this->view->entries = array();
		}
	}
}

