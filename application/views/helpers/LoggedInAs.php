<?php
class Zend_View_Helper_LoggedInAs extends Zend_View_Helper_Abstract 
{
	public function loggedInAs ()
	{
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) 
		{
			$username = $auth->getIdentity()->username;
			$logoutUrl = $this->view->url(array('controller'=>'auth',	'action'=>'logout'), null, true);
			$login = <<<EOT
			<div id="logged">
				<div class="inup">
					<div class="right">
						<div class="loggedin"><img src="/images/loggedin.png" /></div>
						<div class="loggedas">$username</div>
					</div>
				</div>

				<div class="inlo">
					<div class="logout"><a href="$logoutUrl"><img src="/images/logout.png" /></a></div>
				</div>
			</div>
EOT;
			return $login;
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
			<div class="label"><img src="/images/login.png" /></div>
	                <div id="popup" style="display:none;">
        	                <form method="post" action="http://pubtexi.local/auth">
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
