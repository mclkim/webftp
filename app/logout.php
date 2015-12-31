<?php
use \Kaiser\Controller;
class Logout extends Controller {
	protected function requireLogin() {
		return false;
	}
	function execute() {
		$this->logout ();
	}
}
