<?php

namespace App\Models;

use Kaiser\Singleton;

/**
 * Represents a file or folder in the Media Library.
 *
 * @package october\cms
 * @author Alexey Bobkov, Samuel Georges
 */
class FtpLibraryItem extends Singleton {
	const TYPE_FILE = 'file';
	const TYPE_FOLDER = 'folder';
	const FILE_TYPE_AUDIO = 'audio';
	const FILE_TYPE_COMPRESSED = 'compressed';
	const FILE_TYPE_DOCUMENT = 'document';
	const FILE_TYPE_EXECUTABLE = 'executable';
	const FILE_TYPE_FLODER = 'floder';
	const FILE_TYPE_IMAGE = 'image';
	const FILE_TYPE_VIDEO = 'video';
	
	/**
	 *
	 * @var string Specifies the item path relative to the Library root.
	 */
	public $path;
	
	/**
	 *
	 * @var integer Specifies the item size.
	 *      For files the item size is measured in bytes. For folders it
	 *      contains the number of files in the folder.
	 */
	public $size;
	
	/**
	 *
	 * @var integer Contains the last modification time (Unix timestamp).
	 */
	public $lastModified;
	
	/**
	 *
	 * @var string Specifies the item type.
	 */
	public $type;
	
	/**
	 *
	 * @var string Specifies the public URL of the item.
	 */
	public $publicUrl;
	
	/**
	 *
	 * @var array Contains a default list of files and directories to ignore.
	 *      The list can be customized with the following configuration options:
	 *      - cms.storage.media.image_extensions
	 *      - cms.storage.media.video_extensions
	 *      - cms.storage.media.audo_extensions
	 */
	protected static $defaultTypeExtensions = [ 
			'audio' => [ 
					'mp3',
					'wav',
					'wma',
					'm4a',
					'ogg' 
			],
			'compressed' => [ 
					'zip',
					'tar',
					'gz' 
			],
			'image' => [ 
					'gif',
					'png',
					'jpg',
					'jpeg',
					'bmp' 
			],
			'video' => [ 
					'mp4',
					'avi',
					'mov',
					'mpg',
					'mpeg',
					'mkv',
					'webm' 
			] 
	];
	protected static $imageExtensions;
	protected static $videoExtensions;
	protected static $audioExtensions;
	protected static $compressedExtensions;
	
	/**
	 *
	 * @param string $path        	
	 * @param int $size        	
	 * @param int $lastModified        	
	 * @param string $type        	
	 * @param string $publicUrl        	
	 */
	// public function __construct($path, $size, $lastModified, $type, $publicUrl) {
	// $this->path = $path;
	// $this->size = $size;
	// $this->lastModified = $lastModified;
	// $this->type = $type;
	// $this->publicUrl = $publicUrl;
	// }
	
	/**
	 *
	 * @return bool
	 */
	public function isFile() {
		return $this->type == self::TYPE_FILE;
	}
	
	/**
	 * Returns the file type by its name.
	 * The known file types are: image, video, audio, document
	 *
	 * @return string Returns the file type or NULL if the item is a folder.
	 */
	public function getFileType($item, $extension) {
		// if (! $this->isFile ()) {
		// return null;
		// }
		if ($item == self::TYPE_FOLDER)
			return null;
		
		self::$imageExtensions = self::$defaultTypeExtensions ['image'];
		self::$videoExtensions = self::$defaultTypeExtensions ['video'];
		self::$audioExtensions = self::$defaultTypeExtensions ['audio'];
		self::$compressedExtensions = self::$defaultTypeExtensions ['compressed'];
		
		// $extension = pathinfo ( $this->path, PATHINFO_EXTENSION );
		$extension = strtolower ( $extension );
		if (! strlen ( $extension )) {
			return self::FILE_TYPE_DOCUMENT;
		}
		
		if (in_array ( $extension, self::$imageExtensions )) {
			return self::FILE_TYPE_IMAGE;
		}
		
		if (in_array ( $extension, self::$videoExtensions )) {
			return self::FILE_TYPE_VIDEO;
		}
		
		if (in_array ( $extension, self::$audioExtensions )) {
			return self::FILE_TYPE_AUDIO;
		}
		
		if (in_array ( $extension, self::$compressedExtensions )) {
			return self::FILE_TYPE_COMPRESSED;
		}
		
		return self::FILE_TYPE_DOCUMENT;
	}
	
	/**
	 * Returns the item size as string.
	 * For file-type items the size is the number of bytes. For folder-type items
	 * the size is the number of items contained by the item.
	 *
	 * @return string Returns the size as string.
	 */
	public function sizeToString() {
		return $this->type == self::TYPE_FILE ? File::sizeToString ( $this->size ) : $this->size . ' ' . trans ( 'cms::lang.media.folder_size_items' );
	}
	
	/**
	 * Returns the item last modification date as string.
	 *
	 * @return string Returns the item's last modification date as string.
	 */
	public function lastModifiedAsString() {
		if (! ($date = $this->lastModified)) {
			return null;
		}
		
		return Carbon::createFromTimestamp ( $date )->toFormattedDateString ();
	}
	public function itemTypeToIconClass($item, $itemType) {
		if ($item == self::TYPE_FOLDER)
			return 'icon-folder';
		
		switch ($itemType) {
			case self::FILE_TYPE_IMAGE :
				return "icon-picture-o";
			case self::FILE_TYPE_VIDEO :
				return "icon-video-camera";
			case self::FILE_TYPE_AUDIO :
				return "icon-volume-up";
			default :
				return "icon-file";
		}
	}
	public function getFileMode($ext) {
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
	public function byteconvert($bytes) {
		if (empty ( $bytes ) || $bytes < 0)
			return '0 B';
		
		$symbol = array (
				'B',
				'KB',
				'MB',
				'GB',
				'TB',
				'PB',
				'EB',
				'ZB',
				'YB' 
		);
		$exp = floor ( log ( $bytes ) / log ( 1024 ) );
		return sprintf ( '%.2f ' . $symbol [$exp], ($bytes / pow ( 1024, floor ( $exp ) )) );
	}
	public function chmodnum($chmod) {
		$trans = array (
				'-' => '0',
				'r' => '4',
				'w' => '2',
				'x' => '1' 
		);
		$chmod = substr ( strtr ( $chmod, $trans ), 1 );
		$array = str_split ( $chmod, 3 );
		return array_sum ( str_split ( $array [0] ) ) . array_sum ( str_split ( $array [1] ) ) . array_sum ( str_split ( $array [2] ) );
	}
	/* getbasename function : 한글파일이 잘려서 나타나는 문제 (ex> 안녕.ppt --> .ppt) 모든 basename() 함수를 getbasename()으로 대체 */
	/* 출처 : http://dev.tattertools.com/changeset/7 */
	public function getbasename($path) {
		$pattern = (strncasecmp ( PHP_OS, 'WIN', 3 ) ? '/([^\/]+)[\/]*$/' : '/([^\/\\\\]+)[\/\\\\]*$/');
		if (preg_match ( $pattern, $path, $matches ))
			return $matches [1];
		return '';
	}
	public function getextension($file) {
		// *** Get extension
		$extension = strtolower ( pathinfo ( $file, PATHINFO_EXTENSION ) );
		// $extension = strtolower ( strrchr ( $file, '.' ) );
		// $extension = strtolower ( substr ( $file, strrpos ( $file, "." ) + 1 ) );
		return $extension;
	}
}
