<?php
/**
 +------------------------------------------------------------------------------
 * 1. 프로젝트명 : qhard
 * 2. 패키지명(또는 디렉토리 경로) : package_name
 * 3. 파일명 : qupload.php
 * 4. 작성일 : 2014. 9. 2. 오전 9:36:31
 * 5. 작성자 : Administrator
 * 6. 설명 :
 * 
 * 1.파일업로드
 * Created on 2008. 07. 28
 * Modify on 2011. 10. 28
 * Modify on 2011. 11. 1
 * Modify on 2014. 04. 1
 * --------------------------------------------------------
 *  큰 사이즈의 파일을 업로드 하실 경우 php.ini 에서 업로드 제한 용량을 수정하셔야 합니다
 *  아래 예제는 1G 업로드 허용시 설정 방법 입니다.
 *
 *  file_uploads = On
 *  upload_max_filesize = 1024M
 *  post_max_size = 1024M
 *  max_execution_time = 600
 *  memory_limit = 1024M
 * --------------------------------------------------------
 * Plupload v2.1.1 사용
 * [출처]. http://plupload.org/
 * [데모]. http://plupload.org/examples/queue
 *
 * 타이틀이 나타나지 않도록 (CSS)파일을 찾아 다음과 같이 설정해야 한다.
 * .plupload_header_content {display: none;}
 * .plupload_filelist_footer {border-top: 1px solid #FFF; height: 40px; line-height: 20px; vertical-align: middle;}
 +------------------------------------------------------------------------------
 **/
use \Kaiser\Controller;
class qupload extends Controller {
	function execute() {
		$config = $this->container->get ( 'config' );
		$tpl = $this->container->get ( 'template' );
		
		$dir = $this->getParameter ( 'dir', '/' );
		$parent = $this->getParameter ( 'path', '/' );
		$current = rtrim ( $parent, '/' ) . '/' . ltrim ( $dir, '/' );
		
		$this->debug ( array (
				'dir' => $dir,
				'parent' => $parent,
				'current' => $current 
		) );
		
		$tpl->assign ( array (
				'directory' => $dir,
				'path' => $parent,
				'domain' => $this->router ()->getBaseUrl (),
				'chunk_size' => $config->get ( 'plupload.chunk_size' ),
				'max_file_size' => $config->get ( 'plupload.max_file_size' ),
				'max_file_count' => $config->get ( 'plupload.max_file_count' ) 
		) );
		$tpl->define ( 'upload', 'upload.tpl.html' );
		$tpl->print_ ( 'upload' );
	}
	function ok() {
	}
	function upload() {
		$config = $this->container->get ( 'config' );
		$ftp = $this->container->get ( 'ftp' );
		
		$dir = $this->getParameter ( 'dir', '/' );
		$parent = $this->getParameter ( 'path', '/' );
		$current = rtrim ( $parent, '/' ) . '/' . ltrim ( $dir, '/' );
		
		$this->debug ( array (
				'dir' => $dir,
				'parent' => $parent,
				'current' => $current 
		) );
		
		$this->debug ( $_FILES ['file'] );
		
		$upload = new \Kaiser\Plupload ( $config->get ( 'plupload' ) );
		
		$upload->no_cache_headers ();
		$upload->cors_headers ();
		
		if (($file = $upload->getFiles ()) !== false) {
			$this->debug ( $file );
			
			$model = new \App\Models\Ftp ( $ftp );
			
			$model->upload ( $current, $file );
		} else {
			$this->err ( $upload->get_error_message () );
		}
	}
}
