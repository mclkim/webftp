<?php
use Kaiser\Controller;
use Apfelbox\FileDownload\FileDownload;
class QDownload extends Controller {
	function execute() {
		$ftp = $this->container->get ( 'ftp' );
		
		$dir = $this->getParameter ( 'dir', '/' );
		$parent = $this->getParameter ( 'path', '/' );
		$entry = $this->getParameter ( 'entry', '' );
		
		$current = rtrim ( $parent, '/' ) . '/' . ltrim ( $dir, '/' );
		
		$this->debug ( array (
				'dir' => $dir,
				'parent' => $parent,
				'current' => $current,
				'entry' => $entry 
		) );
		
		$entry = base64_decode ( $entry );
		// TODO::한글변환을 해야 한다.
		$filename = iconv ( 'utf-8', 'euc-kr', $entry );
		
		$this->debug ( $entry );
		
		$model = new \App\Models\Ftp ( $ftp );
		$res = $model->download ( $current, $entry );
		
		$fileDownload = FileDownload::createFromFilePath ( $res );
		$fileDownload->sendDownload ( $filename );
	}
	/**
	 * 여러 파일 압축해서 다운로드 기능(2009.12.07)
	 */
	function zip() {
		$ftp = $this->container->get ( 'ftp' );
		
		$dir = $this->getParameter ( 'dir', '/' );
		$parent = $this->getParameter ( 'path', '/' );
		$folder = $this->getParameter ( 'folder', '' );
		$file = $this->getParameter ( 'file', '' );
		
		$current = rtrim ( $parent, '/' ) . '/' . ltrim ( $dir, '/' );
		
		$this->debug ( array (
				'dir' => $dir,
				'parent' => $parent,
				'current' => $current,
				'folder' => $folder,
				'file' => $file 
		) );
		
		// TODO::임시파일이름 지정
		$temp_file = tempnam ( sys_get_temp_dir (), 'Zip' );
		$model = new \App\Models\Ftp ( $ftp );
		$zip = $model->zip ( $current, $folder, $file, $temp_file );
		
		$this->debug ( $temp_file );
		
		$zipfile = $_SESSION ['data'] ['username'] . '-' . time () . '.zip';
		$fileDownload = FileDownload::createFromFilePath ( $temp_file );
		$fileDownload->sendDownload ( $zipfile );
	}
}
?>
