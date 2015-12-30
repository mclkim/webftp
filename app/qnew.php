<?php
use Kaiser\Controller;
class QNew extends Controller {
	function execute() {
		$tpl = $this->container->get ( 'template' );
		
		$dir = $this->getParameter ( 'dir', '/' );
		$parent = $this->getParameter ( 'path', '/' );
		
		$tpl->assign ( array (
				'dir' => $dir,
				'path' => $parent 
		) );
		
		$tpl->define ( 'index', 'new.tpl.html' );
		$tpl->print_ ( 'index' );
	}
	function mkdir() {
		$ftp = $this->container->get ( 'ftp' );
		
		$dir = $this->getParameter ( 'dir', '/' );
		$parent = $this->getParameter ( 'path', '/' );
		$newname = $this->getParameter ( 'newname', 'New Folder' );
		
		$current = rtrim ( $parent, '/' ) . '/' . ltrim ( $dir, '/' );
		
		$this->debug ( array (
				'dir' => $dir,
				'parent' => $parent,
				'current' => $current,
				'newname' => $newname 
		) );
		
		$model = new \App\Models\Ftp ( $ftp );
		$model->mkdir ( $current, $newname );
	}
}
?>
