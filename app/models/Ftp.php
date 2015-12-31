<?php

namespace App\Models;

use Kaiser\Manager\FtpManager;

class Ftp extends FtpManager {
	function __construct($ftp) {
		parent::__construct ( $ftp );
		
		// Login with username and password
		if (! empty ( $_SESSION ['user'] ['username'] ) && ! empty ( $_SESSION ['user'] ['password'] )) {
			$ftp->login ( $_SESSION ['user'] ['username'], $_SESSION ['user'] ['password'] );
		}
	}
	function password_verify($username, $password) {
		if (empty ( $username ) || empty ( $password ))
			return false;
		
		try {
			$ret = $this->FTP->login ( $username, $password );
			return isset ( $ret ) ? $ret : false;
		} catch ( \Exception $e ) {
			return false;
		}
	}
	private function id($path) {
		$path = str_replace ( DIRECTORY_SEPARATOR, '/', $path );
		$path = trim ( $path, '/' );
		return ($path) ? $path : '/';
	}
	function getFolder($path = '/', $sortby = 'name', $sortorder = 'ASC') {
		return $this->queryDirToMapList ( $path, $sortby, $sortorder );
	}
	function getFiles($path = '/', $sortby = 'name', $sortorder = 'ASC') {
		return $this->queryFileToMapList ( $path, $sortby, $sortorder );
	}
	function getFolderTree($path = '/', $with_root = false) {
		$result = $this->_lsDirs ( $path );
		if (! $result) {
			throw new Exception ( 'Could not list path: ' . $path );
		}
		
		$res = array ();
		foreach ( $result as $entry ) {
			// Expected format of the node (there are no required fields)
			$res [] = array (
					'text' => $entry ['name'],
					'children' => $entry ['files_inside'] > 2,
					'id' => $this->id ( $path . DIRECTORY_SEPARATOR . $entry ['name'] ),
					'icon' => '',
					'state' => array (
							'opened' => false,
							'disabled' => false 
					) 
			);
		}
		
		if ($with_root && $this->id ( $path ) === '/') {
			$res = array (
					array (
							'text' => '/',
							'children' => $res,
							'id' => '/',
							'icon' => '',
							'state' => array (
									'opened' => true,
									'disabled' => false 
							) 
					) 
			);
		}
		
		return $res;
	}
	function upload($path = '/', $file, $mode = 'auto') {
		// Set the mode if not specified
		if ($mode === 'auto') {
			// Get the file extension so we can set the upload type
			$mode = $this->_settype ( $file ['ext'] );
		}
		
		try {
			$this->FTP->chdir ( $path );
			$mode = ($mode === 'ascii') ? FTP_ASCII : FTP_BINARY;
			$this->FTP->put ( $file ['name'], $file ['tmp_name'], $mode );
		} catch ( Exception $e ) {
			throw new FtpException ( $e->getMessage () );
			return false;
		}
		return true;
	}
	private function _getext($filename) {
		if (FALSE === strpos ( $filename, '.' )) {
			return false;
		}
		$x = explode ( '.', $filename );
		return ( string ) end ( $x );
	}
	private function _settype($ext) {
		$text_types = array (
				'txt',
				'text',
				'php',
				'phps',
				'php4',
				'js',
				'css',
				'htm',
				'html',
				'phtml',
				'shtml',
				'log',
				'xml' 
		);
		return ( bool ) (in_array ( $ext, $text_types )) ? 'ascii' : 'binary';
	}
	function download($path = '/', $entry, $mode = 'auto') {
		// Temporary filename
		// TODO::임시 파일지정
		$tempfile = tempnam ( uniqid ( rand (), TRUE ), 'downl__' );
		if ($tempfile == false) {
			// unlink ( $tempfile );
			// $this->err ( "Unable to create the temporary file." );
			throw new FtpException ( "Unable to create the temporary file." );
			// return false;
		} // end if
		  
		// $this->debug ( $tempfile );
		  
		// Set the mode if not specified
		if ($mode === 'auto') {
			// Get the file extension so we can set the upload type
			$ext = $this->_getext ( $entry );
			$mode = $this->_settype ( $ext );
		}
		
		try {
			$this->FTP->chdir ( $path );
			$mode = ($mode === 'ascii') ? FTP_ASCII : FTP_BINARY;
			// Get the file
			$this->FTP->get ( $tempfile, $entry, $mode );
		} catch ( Exception $e ) {
			throw new FtpException ( $e->getMessage () );
			return false;
		}
		return $tempfile;
	}
	function mkdir($path = '/', $directory) {
		try {
			$this->FTP->chdir ( $path );
			if ($this->FTP->isDir ( $directory ) == false)
				$this->FTP->mkdir ( $directory );
		} catch ( Exception $e ) {
			throw new FtpException ( $e->getMessage () );
			return false;
		}
		return true;
	}
	function rename($path = '/', $folder, $file, $rename) {
		$_Folder = array_filter ( explode ( ',', $folder ) );
		$_File = array_filter ( explode ( ',', $file ) );
		
		try {
			// 폴더
			if (is_array ( $_Folder )) {
				foreach ( $_Folder as $k => $v ) {
					$oldname = rtrim ( $path, '/' ) . '/' . $v;
					$newname = rtrim ( $path, '/' ) . '/' . $rename;
					$this->FTP->rename ( $oldname, $newname );
				}
			}
			
			// 파일
			if (is_array ( $_File )) {
				foreach ( $_File as $k => $v ) {
					$v = base64_decode ( $v );
					$oldname = rtrim ( $path, '/' ) . '/' . $v;
					$newname = rtrim ( $path, '/' ) . '/' . $rename;
					$this->FTP->rename ( $oldname, $newname );
				}
			}
		} catch ( Exception $e ) {
			throw new FtpException ( $e->getMessage () );
			return false;
		}
		return true;
	}
	function move($path = '/', $folder, $file, $target = '/') {
		$_Folder = array_filter ( explode ( ',', $folder ) );
		$_File = array_filter ( explode ( ',', $file ) );
		
		try {
			// 폴더
			if (is_array ( $_Folder )) {
				foreach ( $_Folder as $k => $v ) {
					$oldname = rtrim ( $path, '/' ) . '/' . $v;
					$newname = rtrim ( $target, '/' ) . '/' . $v;
					$this->FTP->rename ( $oldname, $newname );
				}
			}
			
			// 파일
			if (is_array ( $_File )) {
				foreach ( $_File as $k => $v ) {
					$v = base64_decode ( $v );
					$oldname = rtrim ( $path, '/' ) . '/' . $v;
					$newname = rtrim ( $target, '/' ) . '/' . $v;
					$this->FTP->rename ( $oldname, $newname );
				}
			}
		} catch ( Exception $e ) {
			throw new FtpException ( $e->getMessage () );
			return false;
		}
		return true;
	}
	function delete($path = '/', $folder, $file) {
		$_Folder = array_filter ( explode ( ',', $folder ) );
		$_File = array_filter ( explode ( ',', $file ) );
		
		try {
			// 폴더
			if (is_array ( $_Folder )) {
				foreach ( $_Folder as $k => $v ) {
					// $this->FTP->deleteRecursive ( rtrim ( $path, '/' ) . '/' . $v );
					$this->deleteRecursive ( rtrim ( $path, '/' ) . '/' . $v );
				}
			}
			
			// 파일
			if (is_array ( $_File )) {
				foreach ( $_File as $k => $v ) {
					$v = base64_decode ( $v );
					$this->FTP->delete ( rtrim ( $path, '/' ) . '/' . $v );
				}
			}
		} catch ( Exception $e ) {
			throw new FtpException ( $e->getMessage () );
			return false;
		}
		return true;
	}
	function deleteRecursive($path) {
		if ($this->FTP->isDir ( $path ) && $this->FTP->chdir ( $path )) {
			$result = $this->_lsFiles ( $path );
			foreach ( $result as $file ) {
				$this->FTP->delete ( $path . '/' . $file ['name'] );
			}
			$result = $this->_lsDirs ( $path );
			foreach ( $result as $dir ) {
				$directory = rtrim ( $path, '/' ) . '/' . $dir ['name'];
				$this->deleteRecursive ( $directory );
			}
		}
		if ($this->FTP->isDir ( $path ) == true)
			$this->FTP->rmdir ( $path );
	}
	function zip($path, $folder, $file, $destination, $overwrite = true) {
		if (class_exists ( 'ZipArchive' ) == false) {
			exit ( 'PHP - ZipArchive is not enabled or not found' );
		}
		
		$_Folder = array_filter ( explode ( ',', $folder ) );
		$_File = array_filter ( explode ( ',', $file ) );
		
		try {
			$zip = new \ZipArchive ();
			
			if ($zip->open ( $destination, $overwrite ? \ZIPARCHIVE::OVERWRITE : \ZIPARCHIVE::CREATE ) !== true) {
				// echo 'Error: Unable to create zip file';
				throw new FtpException ( 'Error: Unable to create zip file' );
				// return false;
			}
			
			// 선택한 폴더를 압축하기
			if (count ( $_Folder )) {
				foreach ( $_Folder as $k => $v ) {
					$this->zipRecursive ( $zip, rtrim ( $path, '/' ) . '/' . $v );
				}
			}
			
			// 선택한 파일을 압축하기
			if (count ( $_File )) {
				foreach ( $_File as $k => $v ) {
					$v = base64_decode ( $v );
					$this->zipAdd ( $zip, $path, $v );
				}
			}
			
			$zip->close ();
			return file_exists ( $destination );
		} catch ( Exception $e ) {
			throw new FtpException ( $e->getMessage () );
		}
		return $zip;
	}
	private function zipAdd(&$zip, $path, $file) {
		// Get the file
		$tempfile = $this->download ( $path, $file );
		
		// Read temporary file
		// $content = file_get_contents ( $tempfile );
		// unlink ( $tempfile );
		
		// TODO::경로시작을 '/'으로 시작하면 탐색기 미리보기에서 압축파일목록이 나오지 않음.
		$filename = ($path == '/') ? $file : trim ( $path, '/' ) . '/' . $file;
		$zip->addFile ( $tempfile, iconv ( "utf-8", "euc-kr", $filename ) );
		// Logger::debug ( $filename );
	}
	private function zipRecursive(&$zip, $path) {
		if ($this->FTP->isDir ( $path ) && $this->FTP->chdir ( $path )) {
			// Logger::debug ( $path );
			$result = $this->_lsFiles ( $path );
			// Logger::debug ( $result );
			foreach ( $result as $file ) {
				$this->zipAdd ( $zip, $path, $file ['name'] );
			}
			$result = $this->_lsDirs ( $path );
			// Logger::debug ( $result );
			foreach ( $result as $dir ) {
				$directory = rtrim ( $path, '/' ) . '/' . $dir ['name'];
				$this->zipRecursive ( $zip, $directory );
			}
		}
	}
	function findRecursive(&$found, $path = '/', $pattern) {
		if ($this->FTP->isDir ( $path ) && $this->FTP->chdir ( $path )) {
			// Logger::debug ( $path );
			$result = $this->_lsFiles ( $path );
			// Logger::debug ( $result );
			foreach ( $result as $file ) {
				if (preg_match ( "/$pattern/i", $file ['name'] )) {
					$file ['path'] = $path;
					array_push ( $found, $file );
				}
			}
			$result = $this->_lsDirs ( $path );
			// Logger::debug ( $result );
			foreach ( $result as $dir ) {
				$directory = rtrim ( $path, '/' ) . '/' . $dir ['name'];
				$this->findRecursive ( $found, $directory, $pattern );
			}
		}
	}
}
