<?php

class Zend_View_Helper_ImageGallery extends Zend_View_Helper_Abstract
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

	public function imageGallery($path, $images = array())
	{
		$i = 0;
		foreach($images as $image)
		{
			$ta = $image->getHashNames();
			$name = $ta[0];

			$pp1 = substr($name, 0, 2) . "/";
			$pp2 = substr($name, 2, 2) . "/";
			
			echo "    <span class=\"mediacontainer\">\n";
			echo "      <span class=\"media\">\n";
			echo "        <span class=\"mediathumb\"><a href=\"/media-details?media_name=".$name."\"><img src=\"".$path.'/'.$pp1.$pp2.$name."_thumb.png\" /></a></span>\n";
			echo "        <span class=\"mediadesc\">";
			echo "        </span>\n";
			echo "      </span>\n";
			echo "    </span>\n";
			$i = $i + 1;
			if(($i % 5) == 0)
			{
				echo "    <div style=\"clear: both;\"></div><br>\n";
			}
		}
	}
}
