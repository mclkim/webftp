<?php
// DIC configuration
$container = $app->getContainer ();

// -----------------------------------------------------------------------------
// Service providers & factories
// -----------------------------------------------------------------------------

// https://github.com/hassankhan/config
$container ['config'] = function ($c) {
	if (! file_exists ( $c ['settings'] ['configuration'] )) {
		exit ( 'The configuration file does not exist.' );
	}
	return new \Noodlehaus\Config ( [ 
			$c ['settings'] ['configuration'] 
	] );
};

// www.xtac.net
$container ['template'] = function ($c) {
	$tpl = new \Template_ ();
	$tpl->skin = 'bootstrap';
	return $tpl;
};

// https://github.com/usmanhalalit/pixie
$container ['Pixie'] = function ($c) {
	return new \Pixie\Connection ( 'mysql', $c ['config']->get ( 'db' ), 'QB' );
};

// KLogger: Simple Logging for PHP
// https://github.com/katzgrau/KLogger
// $container ['logger'] = function ($c) {
// 	$logger = new Kaiser\Manager\LogManager ( __DIR__ . '/../logs' );
// 	return $logger;
// };

// session
$container ['session'] = function ($c) {
	$session = new Kaiser\Session\FileSession ( __DIR__ . '/../tmp' );
	// http://sir.co.kr/cm_free/756649
	// $session = new Kaiser\Session\DBSession ( $c ['Pixie'] );
	$session->start_session ();
	return $session;
};

// ftp
$container ['ftp'] = function ($c) {
	$ftp = new \Ftp ();
	
	// Opens an FTP connection to the specified host
	$ftp->connect ( $c ['config']->get ( 'ftp.host' ) );
	
	if (! empty ( $c ['config']->get ( 'ftp.user' ) ) && ! empty ( $c ['config']->get ( 'ftp.pass' ) )) {
		// Login with username and password
		$ftp->login ( $c ['config']->get ( 'ftp.user' ), $c ['config']->get ( 'ftp.pass' ) );
	}
	
	$ftp->pasv ( $c ['config']->get ( 'ftp.passive' ) );
	return $ftp;
};

