<?php
class Zend_View_Helper_LoggedInAs extends Zend_View_Helper_Abstract 
{
	public function loggedInAs ()
	{
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) 
		{
			$username = $auth->getIdentity()->username;
			$logoutUrl = $this->view->url(array('controller'=>'auth',
				'action'=>'logout'), null, true);
			return '<div id="lnksign">'.$username .  ' <a href="'.$logoutUrl.'"><img src="/images/Logout.png" /></a></div>';
		} 

		$request = Zend_Controller_Front::getInstance()->getRequest();
		$controller = $request->getControllerName();
		$action = $request->getActionName();
		if($controller == 'auth' && $action == 'index') 
		{
			return '';
		}

		$loginUrl = $this->view->url(array('controller'=>'auth', 'action'=>'index'));

		$login = <<<EOT
		<div id="signin">
			<div id="signin_label"><img src="/images/Login.png" /></div>
	                <div id="signin_popup" style="display:none;">
        	                <form method="post" action="http://www.pubtex.org/auth">
                	                <label>UserName
                        	        <input type="text" name="username"/></label>
                                	<label>Password
	                                <input type='password' name="password"/></label>
        	                        <input type="submit" value="Login"/>
                	        </form>
                	</div>
        	</div>
EOT;

		return $login;
	}
}
