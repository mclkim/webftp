<?php
use \Kaiser\Controller;
class qtrash extends Controller {
	function execute() {
	}
	function trash() {
		$ftp = $this->container->get ( 'ftp' );
		
		// 휴지통 경로(환경변수로 설정할까?)
		$trash = '/.trash';
		
		$model = new \App\Models\Ftp ( $ftp );
		$model->deleteRecursive ( $trash );
		$model->mkdir ( '/', $trash );
		
		echo json_encode ( array (
				'code' => 1,
				'message' => '휴지통에 있는 항목을 모두 삭제했습니다.!!!' 
		) );
	}
}
?>
