<?php
// DIC configuration
$container = $app->getContainer ();

// -----------------------------------------------------------------------------
// Service providers & factories
// -----------------------------------------------------------------------------
if (! file_exists ( $container ['settings'] ['configuration'] )) {
	exit ( 'The configuration file does not exist.' );
}

// https://github.com/hassankhan/config
$container ['config'] = function ($c) {
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

// session
$container ['session'] = function ($c) {
	$session = new Kaiser\Session\FileSession ( __DIR__ . '/../tmp' );
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
