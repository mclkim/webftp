<?php
use Kaiser\Controller;
class Qremove extends Controller {
	function execute() {
		$ftp = $this->container->get ( 'ftp' );
		
		$dir = $this->getParameter ( 'dir', '/' );
		$parent = $this->getParameter ( 'path', '/' );
		$folder = $this->getParameter ( 'folder' );
		$file = $this->getParameter ( 'file' );
		
		$current = rtrim ( $parent, '/' ) . '/' . ltrim ( $dir, '/' );
		
		$this->debug ( array (
				'dir' => $dir,
				'parent' => $parent,
				'current' => $current,
				'folder' => $folder,
				'file' => $file 
		) );
		
		// 휴지통 경로(환경변수로 설정할까?)
		$trash = '/.trash';
		
		$model = new \App\Models\Ftp ( $ftp );
		
		// 휴지통 생성하기
		$model->mkdir ( '/', $trash );
		
		// 만일 휴지통에서 삭제하면..
		if ($current == $trash) {
			$model->delete ( $current, $folder, $file );
		} else {
			$model->move ( $current, $folder, $file, $trash );
		}
	}
}
?>
