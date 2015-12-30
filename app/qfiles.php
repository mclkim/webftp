<?php
use \Kaiser\Controller;
class qfiles extends Controller {
	function execute() {
		$request = $this->container->get ( 'request' );
		$settings = $this->container->get ( 'settings' );
		$tpl = $this->container->get ( 'template' );
		$ftp = $this->container->get ( 'ftp' );
		
// 		var_dump($_SESSION);;
		$dir = $this->getParameter ( 'dir', '/' );
		$parent = $this->getParameter ( 'path', '/' );
		$search = $this->getParameter ( 'search', '' );
		
		// TODO::좋은 방법이 있을 텐데..
		if ($dir == '..') {
			$current = str_replace ( '\\', '/', dirname ( $parent ) );
			$parent = str_replace ( '\\', '/', dirname ( $current ) );
		} else if ($dir == '.trash') {
			$current = '/.trash';
			$parent = '';
		} else {
			$current = rtrim ( $parent, '/' ) . '/' . ltrim ( $dir, '/' );
		}
		
		$this->debug ( array (
				'dir' => $dir,
				'parent' => $parent,
				'current' => $current,
				'search' => $search 
		) );

		$sortby = $request->cookie ( 'sortby', 'name' );
		$sortorder = $request->cookie ( 'sortorder', 'ASC' );
		/**		
		$this->debug ( array (
				'sortby' => $sortby,
				'sortorder' => $sortorder 
		) );
		*/
		$model = new \App\Models\Ftp ( $ftp );
		$p_folder = $model->getFolder ( $parent, $sortby, $sortorder );
		
		if ($search == '') {
			$folder = $model->getFolder ( $current, $sortby, $sortorder );
			$files = $model->getFiles ( $current, $sortby, $sortorder );
		} else {
			$folder = $files = array ();
			$model->findRecursive ( $files, $current, $search );
		}
		
		$tpl->assign ( array (
				'jquery' => $settings ['jquery'],
				
				'type-interior' => true,
				'bootstrap' => $settings ['bootstrap'],
				'custom' => array (
						'css' => '_template/bootstrap/dashboard.css' 
				),
				
				'breadcrumb' => $this->breadcrumb ( $current ),
				'current' => $current,
				'domain' => $this->router ()->getBaseUrl (),
				'ExtPath' => 'images/exts',
				
				'path' => $parent,
				'directory' => $dir,
				'full_path' => $current,
				
				'p_folder' => $p_folder,
				'folder' => $folder,
				'files' => $files 
		) );
		
		$tpl->define ( array (
				"index" => "layout.tpl.html",
				"header" => "header.tpl.html",
				"footer" => "footer.tpl.html",
				"content" => "files.tpl.html" 
		) );
		
		$tpl->print_ ( 'index' );
	}
	private function breadcrumb($current) {
		$breadcrumb = explode ( "/", rtrim ( $current, '/' ) );
		$parent = '';
		foreach ( $breadcrumb as $dir ) {
			$chunks [] = array (
					'dir' => $dir,
					'path' => $parent,
					'content' => $dir == '' ? 'Home' : $dir 
			);
			$parent = rtrim ( $parent, '/' ) . '/' . ltrim ( $dir, '/' );
		}
		return $chunks;
	}
}
?>
