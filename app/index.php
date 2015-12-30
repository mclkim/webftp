<?php
use \Kaiser\Controller;
class index extends Controller {
	protected function requireLogin() {
		return false;
	}
	function execute() {
		header ( "cache-control: no-cache" );
		header ( "cache-control: must-revalidate" );
		header ( "pragma: no-cache" );
		
		$this->router ()->redirect ( '?qfiles' );
	}
}