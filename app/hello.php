<?php
use \Kaiser\Controller;
class hello extends Controller {
	protected function requireLogin() {
		return false;
	}
	function execute() {
		echo 'hello world';
	}
}