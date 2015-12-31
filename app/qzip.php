<?php
use Kaiser\Controller;
class qzip extends Controller {
	function execute() {
		$tpl = $this->container->get ( 'template' );
				
		$dir = $this->getParameter ( 'dir', '/' );
		$parent = $this->getParameter ( 'path', '/' );
		
		$tpl->assign ( array (
				'dir' => $dir,
				'path' => $parent 
		) );
		
		$tpl->define ( 'index', 'zip.tpl.html' );
		$tpl->print_ ( 'index' );
	}
	function zip() {
		$ftp = $this->container->get ( 'ftp' );
				
		$dir = $this->getParameter ( 'dir', '/' );
		$parent = $this->getParameter ( 'path', '/' );
		$folder = $this->getParameter ( 'folder', '' );
		$file = $this->getParameter ( 'file', '' );
		$save_filename = $this->getParameter ( 'save_filename', '' );
		
		$current = rtrim ( $parent, '/' ) . '/' . ltrim ( $dir, '/' );
		
		$this->debug ( array (
				'dir' => $dir,
				'parent' => $parent,
				'current' => $current,
				'folder' => $folder,
				'file' => $file,
				'save_filename' => $save_filename 
		) );
		
		// TODO::임시파일이름 지정
		$temp_file = tempnam ( sys_get_temp_dir (), 'Zip' );
		$model = new \App\Models\Ftp ( $ftp );
		$zip = $model->zip ( $current, $folder, $file, $temp_file );
		
		$this->debug ( $temp_file );
		
		$upfile ['tmp_name'] = $temp_file;
		$upfile ['name'] = rtrim ( $save_filename, '.zip' ) . '.zip';
		$model->upload ( $current, $upfile );
		
		$this->debug ( $upfile );
	}
}
