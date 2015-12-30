<?php
/**
 * 로그아웃
 * Created on 2011. 11. 1
 *
 */
use \Kaiser\Controller;
class Logout extends Controller {
	protected function requireLogin() {
		return false;
	}
	function execute() {
		$this->logout();
	}
}
?>