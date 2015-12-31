<?php
/**
 * -------------------------------------------------------------------
 * 로그파일과 에러내용을 화면에 출력하는 설정을 할수 있습니다.
 * -------------------------------------------------------------------
 * development : 화면에 에러 출력
 * testing : 화면에 에러 출력하지 않고 에러로그에 출력
 * production : 화면에도 에러로그에도 출력하지 않는다.
 * -------------------------------------------------------------------
 */
define ( 'ENVIRONMENT', 'production' );

/**
 * -------------------------------------------------------------------
 * 주 경로 상수를 설정, 디렉토리 구분자 설정
 * -------------------------------------------------------------------
 */
define ( 'ROOT_PATH', dirname ( __FILE__ ) );
define ( 'BASE_PATH', dirname ( ROOT_PATH ) );

/**
 * ---------------------------------------------------------------
 * ini_set('date.timezone', 'Asia/Seoul'); //한국시간(timezone)설정
 * ---------------------------------------------------------------
 */
date_default_timezone_set ( 'Asia/Seoul' ); // 한국시간(timezone)설정

/**
 * -------------------------------------------------------------------
 * ClassLoader implements a PSR-0, PSR-4 and classmap class loader.
 * -------------------------------------------------------------------
 */
$loader = require_once BASE_PATH . '/vendor/autoload.php';
// $loader->add ( 'Kaiser', BASE_PATH . '/vendor/mclkim/kaiser/kaiser' ); // Kaiser framework
$loader->addPsr4 ( 'App\\', BASE_PATH . '/app' ); // Application Controller
$loader->addClassMap ( [ 
		'Template_' => BASE_PATH . '/vendor/mclkim/kaiser/kaiser/Template_/Template_.class.php', // 언더바 템플릿 경로
		'PluploadHandler' => BASE_PATH . '/vendor/mclkim/kaiser/kaiser/Plupload/PluploadHandler.php' 
] // Plupload 경로
 );

/**
 * -------------------------------------------------------------------
 * Set up dependencies
 * -------------------------------------------------------------------
 */
require_once BASE_PATH . '/app/dependencies.php';

/**
 * -------------------------------------------------------------------
 * Instantiate the app
 * -------------------------------------------------------------------
 */
$app = new \Kaiser\App ( $container );
$app->setAppDir ( [
		BASE_PATH . '/app'
] );

/**
 * -------------------------------------------------------------------
 * Run app
 * -------------------------------------------------------------------
 */
$app->run ();

