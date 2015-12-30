<?php
use \Kaiser\Controller;
class login extends Controller {
	protected function requireLogin() {
		return false;
	}
	function execute() {
		$settings = $this->container->get ( 'settings' );
		$router = $this->container->get ( 'router' );
		$tpl = $this->container->get ( 'template' );
		
		// var_dump($_SESSION);
		$returnURI = $this->getParameter ( 'returnURI', $this->_defaultPage );
		
		$tpl->assign ( array (
				'jquery' => $settings ['jquery'],
				'type-interior' => false,
				'bootstrap' => $settings ['bootstrap'],
				'custom' => array (
						'css' => '_template/bootstrap/signin.css' 
				),
				'domain' => $router->getBaseUrl (),
				'loginOnClick' => 'return true;',
				'returnURI' => $returnURI,
				'userField' => 'user_id',
				'passField' => 'pass_wd' 
		) );
		
		$tpl->define ( array (
				"index" => "layout.tpl.html",
				"header" => "header.tpl.html",
				"footer" => "footer.tpl.html",
				"content" => "login.tpl.html" 
		) );
		
		$tpl->print_ ( 'index' );
	}
	function auth() {
		$ftp = $this->container->get ( 'ftp' );
		$returnURI = $this->getParameter ( 'returnURI', $this->_defaultPage );
		
		if (($username = $this->getParameter ( 'user_id' )) == false) {
			throw new \Kaiser\Exception\EchoException ( '아이디를 입력해 주세요.' );
		}
		if (($password = $this->getParameter ( 'pass_wd' )) == false) {
			throw new \Kaiser\Exception\EchoException ( '비밀번호를 입력해 주세요.' );
		}
		
		$model = new \App\Models\Ftp ( $ftp );

		// 비밀번호 확인
		if ($model->password_verify ( $username, $password ) == false) {
			// throw new \Kaiser\Exception\LoginException ();
			throw new \Kaiser\Exception\EchoException ( '아이디 또는 비밀번호가 일치하지 않습니다.' );
		}
		
		$this->debug($username);
		$this->debug($password);
		$this->debug($model->password_verify ( $username, $password ));
		
		$_SESSION ['auth'] = true;
		$_SESSION ['user'] = array (
				'username' => $username,
				'password' => $password 
		);
		
		$ret ['code'] = 1;
		$ret ['value'] = rtrim ( $this->router ()->getBaseUrl ( true ), '/' ) . $returnURI;
		echo json_encode ( $ret );
	}
}