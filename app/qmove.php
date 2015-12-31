<?php
use \Kaiser\Controller;
class qmove extends Controller {
	function execute() {
		$settings = $this->container->get ( 'settings' );
		$tpl = $this->container->get ( 'template' );
		
		$dir = $this->getParameter ( 'dir', '/' );
		$parent = $this->getParameter ( 'path', '/' );
		
		$tpl->assign ( array (
				'jstree' => $settings ['jstree'],
				'directory' => $dir,
				'path' => $parent 
		) );
		
		$tpl->define ( 'index', 'move.tpl.html' );
		$tpl->print_ ( 'index' );
	}
	function json() {
		$ftp = $this->container->get ( 'ftp' );
		
		$id = $this->getParameter ( 'id' );
		
		$dir = isset ( $id ) && $id !== '#' ? $id : '/';
		
		// $this->debug ( $dir );
		
		$model = new \App\Models\Ftp ( $ftp );
		$folder = $model->getFolderTree ( $dir, (isset ( $id ) && $id == '#') );
		
		// $this->debug ( $folder );
		header ( 'Content-Type: application/json; charset=utf8' );
		echo json_encode ( $folder );
	}
	function move() {
		$ftp = $this->container->get ( 'ftp' );
		
		$dir = $this->getParameter ( 'dir', '/' );
		$parent = $this->getParameter ( 'path', '/' );
		$folder = $this->getParameter ( 'folder' );
		$file = $this->getParameter ( 'file' );
		$targetDir = $this->getParameter ( 'targetDir' );
		
		$current = rtrim ( $parent, '/' ) . '/' . ltrim ( $dir, '/' );
		$target = '/' . rtrim ( $targetDir, '/' );
		
		$this->debug ( array (
				'dir' => $dir,
				'parent' => $parent,
				'current' => $current,
				'folder' => $folder,
				'file' => $file,
				'target' => $target 
		) );
		
		$model = new \App\Models\Ftp ( $ftp );
		$model->move ( $current, $folder, $file, $target );
	}
}
