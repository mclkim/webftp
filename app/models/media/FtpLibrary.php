<?php

namespace App\Models;

use App\Models\FtpLibraryItem;

class FtpLibrary {
	const SORT_BY_NAME = 'name';
	const SORT_BY_SIZE = 'size';
	const SORT_BY_MODIFIED = 'modified';
	protected $ftp = null;
	static $path = null;
	function __construct($ftp) {
		$this->ftp = $ftp;
	}
	private function parseScanLine($rawlistline) {
		$listline ['raw'] = $rawlistline;
		// -------------------------------------------------------------------------
		// Scanning:
		// 1. first scan with strict rules
		// 2. if that does not match, scan with less strict rules
		// 3. if that does not match, scan with rules for specific FTP servers (AS400)
		// 4. and if that does not match, return the raw line
		// -------------------------------------------------------------------------
		
		// ----------------------------------------------
		// 1. Strict rules
		// ----------------------------------------------
		if (preg_match ( "/([-dl])([rwxsStT-]{9})[ ]+([0-9]+)[ ]+([^ ]+)[ ]+(.+)[ ]+([0-9]+)[ ]+([a-zA-Z]+[ ]+[0-9]+)[ ]+([0-9:]+)[ ]+(.*)/", $rawlistline, $regs ) == true) {
			// permissions number owner group size month day year/hour filename
			$listline ["scanrule"] = "rule-1";
			$listline ["dirorfile"] = "$regs[1]"; // Directory ==> d, File ==> -
			$listline ["dirfilename"] = "$regs[9]"; // Filename
			$listline ["files_inside"] = "$regs[3]";
			$listline ["size"] = "$regs[6]"; // Size
			$listline ["owner"] = "$regs[4]"; // Owner
			$listline ["group"] = trim ( $regs [5] ); // Group
			$listline ["permissions"] = "$regs[2]"; // Permissions
			$listline ["mtime"] = "$regs[7] $regs[8]"; // Mtime -- format depends on what FTP server returns (year, month, day, hour, minutes... see above)
		}		

		// ----------------------------------------------
		// 2. Less strict rules
		// ----------------------------------------------
		elseif (preg_match ( "/([-dl])([rwxsStT-]{9})[ ]+(.*)[ ]+([a-zA-Z0-9 ]+)[ ]+([0-9:]+)[ ]+(.*)/", $rawlistline, $regs ) == true) {
			// permissions number/owner/group/size
			// month-day year/hour filename
			$listline ["scanrule"] = "rule-2";
			$listline ["dirorfile"] = "$regs[1]"; // Directory ==> d, File ==> -
			$listline ["dirfilename"] = "$regs[6]"; // Filename
			$listline ["size"] = "$regs[3]"; // Number/Owner/Group/Size
			$listline ["permissions"] = "$regs[2]"; // Permissions
			$listline ["mtime"] = "$regs[4] $regs[5]"; // Mtime -- format depends on what FTP server returns (year, month, day, hour, minutes... see above)
		}		

		// ----------------------------------------------
		// 3. Specific FTP server rules
		// ----------------------------------------------
		
		// ---------------
		// 3.1 Windows
		// ---------------
		elseif (preg_match ( "/([0-9\\/-]+)[ ]+([0-9:AMP]+)[ ]+([0-9]*|<DIR>)[ ]+(.*)/", $rawlistline, $regs ) == true) {
			// date time size filename
			
			$listline ["scanrule"] = "rule-3.1";
			if ($regs [3] == "<DIR>") {
				$listline ["size"] = "";
			} else {
				$listline ["size"] = "$regs[3]";
			} // Size
			$listline ["dirfilename"] = "$regs[4]"; // Filename
			$listline ["owner"] = ""; // Owner
			$listline ["group"] = ""; // Group
			$listline ["permissions"] = ""; // Permissions
			$listline ["mtime"] = "$regs[1] $regs[2]"; // Mtime -- format depends on what FTP server returns (year, month, day, hour, minutes... see above)
			
			if ($listline ["size"] != "") {
				$listline ["dirorfile"] = "-";
			} else {
				$listline ["dirorfile"] = "d";
			}
		}		

		// ---------------
		// 3.2 Netware
		// Thanks to Danny!
		// ---------------
		elseif (preg_match ( "/([-]|[d])[ ]+(.{10})[ ]+([^ ]+)[ ]+([0-9]*)[ ]+([a-zA-Z]*[ ]+[0-9]*)[ ]+([0-9:]*)[ ]+(.*)/", $rawlistline, $regs ) == true) {
			// dir/file perms owner size month day hour filename
			$listline ["scanrule"] = "rule-3.2";
			$listline ["dirorfile"] = "$regs[1]"; // Directory ==> d, File ==> -
			$listline ["dirfilename"] = "$regs[7]"; // Filename
			$listline ["size"] = "$regs[4]"; // Size
			$listline ["owner"] = "$regs[3]"; // Owner
			$listline ["group"] = ""; // Group
			$listline ["permissions"] = "$regs[2]"; // Permissions
			$listline ["mtime"] = "$regs[5] $regs[6]"; // Mtime -- format depends on what FTP server returns (year, month, day, hour, minutes... see above)
		}		

		// ---------------
		// 3.3 AS400
		// ---------------
		elseif (preg_match ( "/([a-zA-Z0-9_-]+)[ ]+([0-9]+)[ ]+([0-9\\/-]+)[ ]+([0-9:]+)[ ]+([a-zA-Z0-9_ -\*]+)[ \\/]+([^\\/]+)/", $rawlistline, $regs ) == true) {
			// owner size date time type filename
			
			if ($regs [5] != "*STMF") {
				$directory_or_file = "d";
			} elseif ($regs [5] == "*STMF") {
				$directory_or_file = "-";
			}
			
			$listline ["scanrule"] = "rule-3.3";
			$listline ["dirorfile"] = "$directory_or_file"; // Directory ==> d, File ==> -
			$listline ["dirfilename"] = "$regs[6]"; // Filename
			$listline ["size"] = "$regs[2]"; // Size
			$listline ["owner"] = "$regs[1]"; // Owner
			$listline ["group"] = ""; // Group
			$listline ["permissions"] = ""; // Permissions
			$listline ["mtime"] = "$regs[3] $regs[4]"; // Mtime -- format depends on what FTP server returns (year, month, day, hour, minutes... see above)
		}		

		// ---------------
		// 3.4 Titan
		// Owner, group are modified compared to rule 1
		// TODO: integrate this rule in rule 1 itself
		// ---------------
		elseif (preg_match ( "/([-dl])([rwxsStT-]{9})[ ]+([0-9]+)[ ]+([a-zA-Z0-9]+)[ ]+([a-zA-Z0-9]+)[ ]+([0-9]+)[ ]+([a-zA-Z]+[ ]+[0-9]+)[ ]+([0-9:]+)[ ](.*)/", $rawlistline, $regs ) == true) {
			// dir/file permissions number owner group size month date time file
			$listline ["scanrule"] = "rule-3.4";
			$listline ["dirorfile"] = "$regs[1]"; // Directory ==> d, File ==> -
			$listline ["dirfilename"] = "$regs[9]"; // Filename
			$listline ["size"] = "$regs[6]"; // Size
			$listline ["owner"] = "$regs[4]"; // Owner
			$listline ["group"] = "$regs[5]"; // Group
			$listline ["permissions"] = "$regs[2]"; // Permissions
			$listline ["mtime"] = "$regs[7] $regs[8]"; // Mtime -- format depends on what FTP server returns (year, month, day, hour, minutes... see above)
		} 		

		// ----------------------------------------------
		// 4. If nothing matchs, return the raw line
		// ----------------------------------------------
		else {
			$listline ["scanrule"] = "rule-4";
			$listline ["dirorfile"] = "u";
			$listline ["dirfilename"] = $rawlistline;
		}
		
		// -------------------------------------------------------------------------
		// Remove the . and .. entries
		// Remove the total line that some servers return
		// -------------------------------------------------------------------------
		if ($listline ["dirfilename"] == "." || $listline ["dirfilename"] == "..") {
			return "";
		} elseif (substr ( $rawlistline, 0, 5 ) == "total") {
			return "";
		}
		
		// -------------------------------------------------------------------------
		// And finally... return the nice list!
		// -------------------------------------------------------------------------
		return $listline;
	}
	protected function parseRawList($directory = null, $recursive = false) {
		try {
			$list = $this->ftp->rawlist ( $directory, $recursive );
		} catch ( Exception $e ) {
			throw new FtpException ( $e->getMessage () );
			return false;
		}
		if (! is_array ( $list )) {
			return array (
					'folders' => array (),
					'files' => array () 
			);
		}
		
		$folders_list = array ();
		$files_list = array ();
		
		foreach ( $list as $line ) {
			$listline = $this->parseScanLine ( $line );
			
			if ($listline ['raw'] == "") {
				continue;
			}
			
			$isdir = ($listline ['dirorfile'] === 'd' || $listline ['dirfilename'] === $listline ['raw']);
			$item = ($isdir) ? 'folder' : 'file';
			
			$path = rtrim ( $directory, '/' ); // TODO::경로를 다른 방법으로 처리
			$name = $listline ['dirfilename'];
			
			// TODO::시간변환작업을 해야 하나??
			$mtime = strtotime ( $listline ['mtime'] );
			$stamp = ($mtime > time ()) ? strtotime ( "-1 year", $mtime ) : $mtime;
			$ext = FtpLibraryItem::getInstance ()->getextension ( $name );
			$type = FtpLibraryItem::getInstance ()->getFileType ( $item, $ext );
			$icon = FtpLibraryItem::getInstance ()->itemTypeToIconClass ( $item, $type );
			$byte = FtpLibraryItem::getInstance ()->byteconvert ( $listline ['size'] );
			$publicUrl = 'http://mclkim.ipdisk.co.kr:8000/list';
			$urlencode = implode ( "/", array_map ( "rawurlencode", explode ( "/", $path . "/" . $name ) ) );
			
			if ($isdir) {
				$folders_list [] = array (
						'name' => $name,
						'path' => $path ? $path : self::$path,
						'item' => $item,
						'icon' => $icon,
						'type' => $type,
						'rights' => $listline ['permissions'],
						'files_inside' => $listline ['files_inside'],
						'owner' => $listline ['owner'],
						'group' => $listline ['group'],
						'size' => $listline ['size'],
						'byte' => '',
						'date' => date ( "Y-m-d h:i A", $stamp ),
						'modified' => $stamp,
						'raw' => $listline ['raw'] 
				);
			} else {
				$files_list [] = array (
						'name' => $name,
						'path' => $path ? $path : self::$path,
						'ext' => $ext,
						'item' => $item,
						'icon' => $icon,
						'type' => $type,
						'rights' => $listline ['permissions'],
						'owner' => $listline ['owner'],
						'group' => $listline ['group'],
						'size' => $listline ['size'],
						'byte' => $byte,
						'date' => date ( "Y-m-d h:i A", $stamp ),
						'modified' => $stamp,
						'raw' => $listline ['raw'],
						'publicUrl' => $publicUrl . $urlencode,
						'base64' => base64_encode ( $name ) 
				);
			}
		}
		
		return array (
				'folders' => (is_array ( $folders_list )) ? $folders_list : array (),
				'files' => (is_array ( $files_list )) ? $files_list : array () 
		);
	}
	protected function _lsDirs($path) {
		$list = $this->parseRawList ( $path );
		if ($list == false) {
			return false;
		}
		return $list ["folders"];
	}
	protected function _lsFiles($path) {
		$list = $this->parseRawList ( $path );
		if ($list == false) {
			return false;
		}
		return $list ["files"];
	}
	/**
	 * Returns a list of folders and files in a Library folder.
	 *
	 * @param string $folder
	 *        	Specifies the folder path relative the the Library root.
	 * @param string $sortBy
	 *        	Determines the sorting preference.
	 *        	Supported values are 'name', 'size', 'lastModified' (see SORT_BY_XXX class constants) and FALSE.
	 * @param string $filter
	 *        	Determines the document type filtering preference.
	 *        	Supported values are 'image', 'video', 'audio', 'document' (see FILE_TYPE_XXX constants of MediaLibraryItem class).
	 * @return array Returns an array of MediaLibraryItem objects.
	 */
	public function listFolderContents($folder = '/', $sortBy = 'name', $filter = null) {
		$folder = self::validatePath ( $folder );
		
		/*
		 * Try to load the contents from cache
		 */
		
		$script_tz = date_default_timezone_get ();
		date_default_timezone_set ( 'UTC' );
		
		$folderContents = $this->parseRawList ( $folder );
		
		date_default_timezone_set ( $script_tz );
		
		/**
		 * Sort the result and combine the file and folder lists
		 */
		
		if ($sortBy !== false) {
			$this->sortItemList ( $folderContents ['folders'], $sortBy );
			$this->sortItemList ( $folderContents ['files'], $sortBy );
		}
		
		$this->filterItemList ( $folderContents ['files'], $filter );
		
		$folderContents = array_merge ( $folderContents ['folders'], $folderContents ['files'] );
		
		return $folderContents;
	}
	public function findFiles($searchTerm, $sortBy = 'name', $filter = null) {
		$words = explode ( ' ', strtolower ( $searchTerm ) );
		$result = [ ];
		
		$findInFolder = function ($folder) use (&$findInFolder, $words, &$result, $sortBy, $filter) {
			$folderContents = $this->listFolderContents ( $folder, $sortBy, $filter );
			
			foreach ( $folderContents as $item ) {
				if ($item ['type'] == FtpLibraryItem::TYPE_FOLDER)
					$findInFolder ( $item ['path'] );
				else if ($this->pathMatchesSearch ( $item ['path'], $words ))
					$result [] = $item;
			}
		};
		
		$findInFolder ( '/' );
		
		$this->sortItemList ( $result, $sortBy );
		return $result;
	}
	public function deleteFiles($paths) {
		try {
			foreach ( $paths as $path ) {
				$path = self::validatePath ( $path );
				$this->ftp->delete ( $path );
			}
		} catch ( Exception $e ) {
			throw new FtpException ( $e->getMessage () );
			return false;
		}
		return true;
	}
	public function deleteFolder($path) {
		$path = self::validatePath ( $path );
		
		try {
			$this->ftp->deleteRecursive ( $path );
		} catch ( Exception $e ) {
			throw new FtpException ( $e->getMessage () );
			return false;
		}
		return true;
	}
	public function exists($path) {
		$path = self::validatePath ( $path );
		
		return $this->ftp->fileExists ( $path );
	}
	public function folderExists($path) {
		$path = self::validatePath ( $path );
		
		return $this->ftp->isDir ( $path );
	}
	public function listAllDirectories($exclude = []) {
		$list = $this->ftp->rawlist ( '/', true );
		$list = array_unique ( $list, SORT_LOCALE_STRING );
		
		$result = [ ];
		
		foreach ( $list as $line ) {
			if ($line == "")
				continue;
			
			if ($line [0] === '/' && $line [strlen ( $line ) - 1] === ':')
				$result [] = rtrim ( $line, ':' );
		}
		
		return $result;
	}
	public function get($path) {
		$path = self::validatePath ( $path );
		return $this->ftp->get ( $path );
	}
	public function put($path, $contents) {
		$path = self::validatePath ( $path );
		return $this->ftp->put ( $path, $contents );
	}
	public function moveFile($oldPath, $newPath, $isRename = false) {
		$oldPath = self::validatePath ( $oldPath );
		$newPath = self::validatePath ( $newPath );
		return $this->ftp->rename ( $oldPath, $newPath );
	}
	public function copyFolder($oldPath, $newPath) {
		$path = self::validatePath ( $path );
	}
	public function moveFolder($originalPath, $newPath) {
		return $this->moveFile ( $originalPath, $newPath );
	}
	public function makeFolder($path) {
		$path = self::validatePath ( $path );
		
		try {
			if ($this->ftp->isDir ( $path ) == false)
				$this->ftp->mkdir ( $path );
		} catch ( Exception $e ) {
			throw new FtpException ( $e->getMessage () );
			return false;
		}
		return true;
	}
	public function uploadFile($path = '/', $file, $mode = 'auto') {
		$path = self::validatePath ( $path );
		
		if ($mode === 'auto') {
			$extension = FtpLibraryItem::getInstance ()->getextension ( $file ['name'] );
			$mode = FtpLibraryItem::getInstance ()->getFileMode ( $extension );
		}
		
		try {
			$this->ftp->chdir ( $path );
			$mode = ($mode === 'ascii') ? FTP_ASCII : FTP_BINARY;
			$this->ftp->put ( $file ['name'], $file ['tmp_name'], $mode );
		} catch ( Exception $e ) {
			throw new FtpException ( $e->getMessage () );
			return false;
		}
		return true;
	}
	public function downloadFile($path = '/', $tempfile, $mode = 'auto') {
		$fullpath = self::validatePath ( $path );
		$path = dirname ( $fullpath );
		$file = FtpLibraryItem::getInstance ()->getbasename ( $fullpath );
		// Set the mode if not specified
		if ($mode === 'auto') {
			$extension = FtpLibraryItem::getInstance ()->getextension ( $file );
			$mode = FtpLibraryItem::getInstance ()->getFileMode ( $extension );
		}
		try {
			$this->ftp->chdir ( $path );
			$mode = ($mode === 'ascii') ? FTP_ASCII : FTP_BINARY;
			$this->ftp->get ( $tempfile, $file, $mode );
		} catch ( Exception $e ) {
			throw new FtpException ( $e->getMessage () );
			return false;
		}
		return true;
	}
	public function getLocalTempFilePath() {
		// TODO::임시 파일지정
		$tempfile = tempnam ( uniqid ( rand (), TRUE ), 'downl__' );
		if ($tempfile == false) {
			// unlink ( $tempfile );
			throw new FtpException ( "Unable to create the temporary file." );
		} // end if
		return $tempfile;
	}
	public function zip($folders, $files, $destination, $overwrite = true) {
		if (class_exists ( 'ZipArchive' ) == false) {
			exit ( 'PHP - ZipArchive is not enabled or not found' );
		}
		
		try {
			$zip = new \ZipArchive ();
			
			if ($zip->open ( $destination, $overwrite ? \ZIPARCHIVE::OVERWRITE : \ZIPARCHIVE::CREATE ) !== true) {
				// echo 'Error: Unable to create zip file';
				throw new FtpException ( 'Error: Unable to create zip file' );
				// return false;
			}
			
			// 선택한 폴더를 압축하기
			if (count ( $folders )) {
				foreach ( $folders as $path ) {
					$this->zipRecursive ( $zip, $path );
				}
			}
			
			// 선택한 파일을 압축하기
			if (count ( $files )) {
				foreach ( $files as $file ) {
					$this->zipAdd ( $zip, $file );
				}
			}
			
			$zip->close ();
			return file_exists ( $destination );
		} catch ( Exception $e ) {
			throw new FtpException ( $e->getMessage () );
		}
		return $zip;
	}
	private function zipAdd(&$zip, $file) {
		// Get the file
		$tempfile = $this->getLocalTempFilePath ();
		$this->downloadFile ( $file, $tempfile );
		
		// TODO::경로시작을 '/'으로 시작하면 탐색기 미리보기에서 압축파일목록이 나오지 않음.
		$zip->addFile ( $tempfile, $file );
	}
	private function zipRecursive(&$zip, $path) {
		if ($this->ftp->isDir ( $path ) && $this->ftp->chdir ( $path )) {
			// 파일압축하기
			$result = $this->_lsFiles ( $path );
			foreach ( $result as $file ) {
				$filename = rtrim ( $path, '/' ) . '/' . $file ['name'];
				$this->zipAdd ( $zip, $filename );
			}
			
			// 폴더 압축하기
			$result = $this->_lsDirs ( $path );
			foreach ( $result as $dir ) {
				$directory = rtrim ( $path, '/' ) . '/' . $dir ['name'];
				$this->zipRecursive ( $zip, $directory );
			}
		}
	}
	protected function findRecursive(&$found, $path = '/', $pattern) {
		if ($this->ftp->isDir ( $path ) && $this->ftp->chdir ( $path )) {
			
			// 파일찾기
			$result = $this->_lsFiles ( $path );
			foreach ( $result as $file ) {
				if (preg_match ( "/$pattern/i", $file ['name'] )) {
					$file ['path'] = $path;
					array_push ( $found, $file );
				}
			}
			
			// 폴더찾기
			$result = $this->_lsDirs ( $path );
			foreach ( $result as $dir ) {
				$directory = rtrim ( $path, '/' ) . '/' . $dir ['name'];
				$this->findRecursive ( $found, $directory, $pattern );
			}
		}
	}
	public function resetCache() {
	}
	public static function validatePath($path, $normalizeOnly = false) {
		$path = str_replace ( '\\', '/', $path );
		$path = '/' . trim ( $path, '/' );
		
		if ($normalizeOnly) {
			if (strpos ( $path, '..' ) !== false)
				throw new ApplicationException ( "지정된 잘못된 파일 경로: '$path'." );
		}
		
		if (strpos ( $path, './' ) !== false || strpos ( $path, '//' ) !== false)
			throw new ApplicationException ( "지정된 잘못된 파일 경로: '$path'." );
		
		return $path;
	}
	public static function url($file) {
	}
	public function getPathUrl($path) {
	}
	protected function getMediaPath($path) {
	}
	protected function getMediaRelativePath($path) {
		$path = self::validatePath ( $path, true );
	}
	protected function isVisible($path) {
	}
	protected function initLibraryItem($path, $itemType) {
	}
	protected function getFolderItemCount($path) {
	}
	protected function scanFolderContents($fullFolderPath) {
	}
	protected function sortItemList(&$itemList, $sortBy) {
		$files = [ ];
		$folders = [ ];
		
		usort ( $itemList, function ($a, $b) use ($sortBy) {
			switch ($sortBy) {
				case self::SORT_BY_NAME :
					return strcasecmp ( $a [$sortBy], $b [$sortBy] );
				case self::SORT_BY_SIZE :
				case self::SORT_BY_MODIFIED :
					if ($a [$sortBy] > $b [$sortBy])
						return - 1;
					
					return ($a [$sortBy] < $b [$sortBy]) ? 1 : 0;
					break;
			}
		} );
	}
	protected function filterItemList(&$itemList, $filter) {
		if (! $filter)
			return;
		
		$result = [ ];
		foreach ( $itemList as $item ) {
			if ($item ['type'] == $filter)
				$result [] = $item;
		}
		
		$itemList = $result;
	}
	protected function pathMatchesSearch($path, $words) {
	}
}
