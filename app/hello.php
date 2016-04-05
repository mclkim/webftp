<?php
use \Kaiser\Controller;
class hello extends Controller {
	protected function requireLogin() {
		return false;
	}
	function execute() {
		// echo 'hello world';
		$tpl = $this->container->get ( 'template' );
		$tpl->assign ( array (
				'filename' => '/webftp/public/?qdownload&dir=%EA%B9%80%EB%AA%85%EC%B2%A0&path=%2FHDD1%2F%EA%B9%80%EB%AF%BC%EC%84%A0&entry=RFNDMDAyODAuSlBH' 
		) );
		$tpl->define ( "index", "aaa.html" );
		$tpl->print_ ( 'index' );
	}
}