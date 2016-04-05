<?php

namespace App\Models;

use \Kaiser\Exception\ApplicationException;
use \Kaiser\Exception\SystemException;
use abeautifulsite\SimpleImage;

class FtpManager extends FtpLibrary {
	const FOLDER_ROOT = '/';
	const VIEW_MODE_GRID = 'grid';
	const VIEW_MODE_LIST = 'list';
	const VIEW_MODE_TILES = 'tiles';
	const SELECTION_MODE_NORMAL = 'normal';
	const SELECTION_MODE_FIXED_RATIO = 'fixed-ratio';
	const SELECTION_MODE_FIXED_SIZE = 'fixed-size';
	const FILTER_EVERYTHING = 'everything';
	public function prepareVars() {
		$folder = $this->getCurrentFolder ();
		$viewMode = $this->getViewMode ();
		$filter = $this->getFilter ();
		$sortBy = $this->getSortBy ();
		$searchTerm = $this->getSearchTerm ();
		$searchMode = strlen ( $searchTerm ) > 0;
		
		if (! $searchMode)
			$items = $this->listFolderItems ( $folder, $filter, $sortBy );
		else
			$items = $this->findFiles ( $searchTerm, $filter, $sortBy );
		
		return array (
				'csrf_token' => app ()->getCsrfToken(),
				'session_key' => session_id (),
				'baseUrl' => app ( 'router' )->getBaseUrl (),
				'currentFolder' => $folder,
				'isRootFolder' => $folder == self::FOLDER_ROOT,
				'pathSegments' => $this->splitPathToSegments ( $folder ),
				'viewMode' => $viewMode,
				'thumbnailParams' => $this->getThumbnailParams ( $viewMode ),
				'currentFilter' => $filter,
				'sortBy' => $sortBy,
				'searchMode' => $searchMode,
				'searchTerm' => $searchTerm,
				'sidebarVisible' => $this->getSidebarVisible (),
				'items' => $items 
		);
	}
	protected function listFolderItems($folder, $filter, $sortBy) {
		$filter = $filter !== self::FILTER_EVERYTHING ? $filter : null;
		
		return $this->listFolderContents ( $folder, $sortBy, $filter );
	}
	protected function findItems($searchTerm, $filter, $sortBy) {
		$filter = $filter !== self::FILTER_EVERYTHING ? $filter : null;
		
		return $this->findFiles ( $searchTerm, $sortBy, $filter );
	}
	public function setCurrentFolder($folder) {
		$folder = self::validatePath ( $folder );
		
		$_SESSION ['current_folder'] = $folder;
	}
	protected function getCurrentFolder() {
		return if_exists ( $_SESSION, 'current_folder', self::FOLDER_ROOT );
	}
	public function setFilter($filter) {
		if (! in_array ( $filter, [ 
				self::FILTER_EVERYTHING,
				FtpLibraryItem::FILE_TYPE_IMAGE,
				FtpLibraryItem::FILE_TYPE_AUDIO,
				FtpLibraryItem::FILE_TYPE_DOCUMENT,
				FtpLibraryItem::FILE_TYPE_VIDEO 
		] )) {
			throw new ApplicationException ( 'Invalid input data' );
		}
		
		$_SESSION ['media_filter'] = $filter;
	}
	protected function getFilter() {
		return if_exists ( $_SESSION, 'media_filter', self::FILTER_EVERYTHING );
	}
	public function setSearchTerm($searchTerm) {
		$_SESSION ['search_term'] = trim ( $searchTerm );
	}
	protected function getSearchTerm() {
		return if_exists ( $_SESSION, 'search_term', null );
	}
	public function setSortBy($sortBy) {
		if (! in_array ( $sortBy, [ 
				self::SORT_BY_NAME,
				self::SORT_BY_SIZE,
				self::SORT_BY_MODIFIED 
		] )) {
			throw new ApplicationException ( 'Invalid input data' );
		}
		
		$_SESSION ['media_sort_by'] = $sortBy;
	}
	protected function getSortBy() {
		return if_exists ( $_SESSION, 'media_sort_by', self::SORT_BY_NAME );
	}
	protected function getSelectionParams() {
	}
	public function setSelectionParams($selectionMode, $selectionWidth, $selectionHeight) {
	}
	public function setSidebarVisible($visible) {
		$_SESSION ['sidebar_visible'] = ! ! $visible;
	}
	protected function getSidebarVisible() {
		return if_exists ( $_SESSION, 'sidebar_visible', true );
	}
	public function setViewMode($viewMode) {
		if (! in_array ( $viewMode, [ 
				self::VIEW_MODE_GRID,
				self::VIEW_MODE_LIST,
				self::VIEW_MODE_TILES 
		] ))
			throw new ApplicationException ( 'Invalid input data' );
		
		$_SESSION ['view_mode'] = $viewMode;
	}
	protected function getViewMode() {
		return if_exists ( $_SESSION, 'view_mode', self::VIEW_MODE_GRID );
	}
	protected function splitPathToSegments($path) {
		$path = self::validatePath ( $path, true );
		$path = explode ( '/', ltrim ( $path, '/' ) );
		
		$result = [ ];
		while ( count ( $path ) > 0 ) {
			$folder = array_pop ( $path );
			
			$result [$folder] = implode ( '/', $path ) . '/' . $folder;
			if (substr ( $result [$folder], 0, 1 ) != '/')
				$result [$folder] = '/' . $result [$folder];
		}
		
		return array_reverse ( $result );
	}
	protected function validateFileName($name) {
		if (! preg_match ( '/^[0-9a-z\.\s_\-]+$/i', $name )) {
			return false;
		}
		
		if (strpos ( $name, '..' ) !== false) {
			return false;
		}
		
		return true;
	}
	public function generateThumbnail($thumbnailInfo, $thumbnailParams = null) {
		$publicUrl = '_thumbnail/';
		$tempFilePath = null;
		$thumbnailPath = null;
		
		try {
			// Get and validate input data
			$path = $thumbnailInfo ['path'];
			$width = $thumbnailInfo ['width'];
			$height = $thumbnailInfo ['height'];
			$lastModified = $thumbnailInfo ['lastModified'];
			
			if (! is_numeric ( $width ) || ! is_numeric ( $height ) || ! is_numeric ( $lastModified )) {
				throw new ApplicationException ( 'Invalid input data' );
			}
			
			if (! $thumbnailParams) {
				$thumbnailParams = $this->getThumbnailParams ();
				$thumbnailParams ['width'] = $width;
				$thumbnailParams ['height'] = $height;
			}
			
			// If the thumbnail file exists - just return the thumbnail marup,
			// otherwise generate a new thumbnail.
			$thumbnailPath = $this->getThumbnailImagePath ( $thumbnailParams, $path, $lastModified );
			if ($this->thumbnailExists ( $thumbnailPath )) {
				return [ 
						'isError' => false,
						'imageUrl' => $publicUrl . FtpLibraryItem::getInstance ()->getbasename ( $thumbnailPath ) 
				];
			}
			
			// Save the file locally
			$tempFilePath = $this->getLocalTempFilePath ();
			if (! $this->downloadFile ( $path, $tempFilePath )) {
				throw new SystemException ( 'Error saving remote file to a temporary location' );
			}
			
			// Resize the thumbnail and save to the thumbnails directory
			$this->resizeImage ( $tempFilePath, $thumbnailParams, $thumbnailPath );
		} catch ( Exception $ex ) {
			// $this->err ( $ex->getMessage () );
			return [ 
					'isError' => true 
			];
		}
		return [ 
				'isError' => false,
				'imageUrl' => $publicUrl . FtpLibraryItem::getInstance ()->getbasename ( $thumbnailPath ) 
		];
	}
	protected function resizeImage($tempFilePath, $thumbnailParams, $thumbnailPath) {
		/**
		 * https://github.com/eventviva/php-image-resize
		 *
		 * PHP must be enabled:
		 * extension=php_mbstring.dll
		 * extension=php_exif.dll
		 *
		 * // $image = new ImageResize ( $tempFilePath );
		 * // $image->resizeToBestFit ( $thumbnailParams ['width'], $thumbnailParams ['height'] );
		 * // $image->save ( $thumbnailPath );
		 */
		try {
			$image = new SimpleImage ();
			$image->load ( $tempFilePath )->best_fit ( $thumbnailParams ['width'], $thumbnailParams ['height'] )->save ( $thumbnailPath );
		} catch ( Exception $e ) {
			throw new SystemException ( $e->getMessage () );
		}
	}
	protected function getThumbnailImagePath($thumbnailParams, $itemPath, $lastModified) {
		$itemSignature = md5 ( $itemPath ) . $lastModified;
		
		$thumbFile = 'thumb_' . $itemSignature . '_' . $thumbnailParams ['width'] . 'x' . $thumbnailParams ['height'] . '_' . $thumbnailParams ['mode'] . '.' . $thumbnailParams ['ext'];
		
		// $partition = implode ( '/', array_slice ( str_split ( $itemSignature, 3 ), 0, 3 ) ) . '/';
		$partition = '/';
		
		$result = $this->getThumbnailDirectory () . $partition . $thumbFile;
		
		return $result;
	}
	protected function absolutizePath($path) {
		$path = str_replace ( array (
				'/',
				'\\' 
		), DIRECTORY_SEPARATOR, $path );
		$parts = array_filter ( explode ( DIRECTORY_SEPARATOR, $path ), 'strlen' );
		$absolutes = array ();
		foreach ( $parts as $part ) {
			if ('.' == $part)
				continue;
			if ('..' == $part) {
				array_pop ( $absolutes );
			} else {
				$absolutes [] = $part;
			}
		}
		return implode ( DIRECTORY_SEPARATOR, $absolutes );
	}
	protected function getThumbnailDirectory() {
		$thumbnail = __DIR__ . '/../../public/_thumbnail';
		$path = $this->absolutizePath ( $thumbnail );
		
		if (! is_dir ( $path ))
			mkdir ( $path, 0755, true );
		
		return $path;
	}
	protected function thumbnailExists($fullPath) {
		return file_exists ( $fullPath ) ? $fullPath : false;
	}
	public function getThumbnailParams($viewMode = null) {
		$result = [ 
				'mode' => 'crop',
				'ext' => 'png' 
		];
		
		if ($viewMode) {
			if ($viewMode == self::VIEW_MODE_LIST) {
				$result ['width'] = 75;
				$result ['height'] = 75;
			} else {
				$result ['width'] = 165;
				$result ['height'] = 165;
			}
		}
		
		return $result;
	}
}
