<?php
class Zend_View_Helper_SearchForm extends Zend_View_Helper_Abstract 
{
	public function searchForm ()
	{
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$controller = $request->getControllerName();
		$action = $request->getActionName();

		if($controller == 'index') 
		{
		$search = <<<EOT
			<form class="searchform" name="searchform">
				<input class="searchfield" id="target" name="search" type="text" value="" />
				<input class="searchbutton" type="button" value="Go" />
			</form>
EOT;
		}
		else
			$search = "";

		return $search;
	}
}
