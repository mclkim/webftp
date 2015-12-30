<?php
use \Kaiser\Controller;
class QRename extends Controller {
	function execute() {
		$tpl = $this->container->get ( 'template' );
		
		$dir = $this->getParameter ( 'dir', '/' );
		$parent = $this->getParameter ( 'path', '/' );
		
		$tpl->assign ( array (
				'dir' => $dir,
				'path' => $parent 
		) );
		
		$tpl->define ( 'index', 'rename.tpl.html' );
		$tpl->print_ ( 'index' );
	}
	function rename() {
		$ftp = $this->container->get ( 'ftp' );
		
		$dir = $this->getParameter ( 'dir', '/' );
		$parent = $this->getParameter ( 'path', '/' );
		$folder = $this->getParameter ( 'folder' );
		$file = $this->getParameter ( 'file' );
		$rename = $this->getParameter ( 'rename' );
		
		$current = rtrim ( $parent, '/' ) . '/' . ltrim ( $dir, '/' );
		
		$this->debug ( array (
				'dir' => $dir,
				'parent' => $parent,
				'current' => $current,
				'folder' => $folder,
				'file' => $file,
				'rename' => $rename 
		) );
		
		$model = new \App\Models\Ftp ( $ftp );
		$model->rename ( $current, $folder, $file, $rename );
	}
}
?>
