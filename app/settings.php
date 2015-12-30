<?php
return [ 
		'settings' => [ 
				'displayErrorDetails' => true,
				
				'configuration' => __DIR__ . '/../config/config.php',
				
				'log' => [ 
						'path' => __DIR__ . '/../logs',
						'level' => \Psr\Log\LogLevel::DEBUG 
				],
				
				// http://getbootstrap.com/
				'bootstrap' => array (
						'js' => "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.js",
						'css' => "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.css",
						'theme' => "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.css",
						'min' => array (
								'js' => "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js",
								'css' => "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css",
								'theme' => "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" 
						),
						'fonts' => array () 
				),
				
				// http://code.jquery.com/
				'jquery' => array (
						'js' => 'http://code.jquery.com/jquery-1.11.3.js',
						'min' => 'http://code.jquery.com/jquery-1.11.3.min.js',
						'mobile' => array (
								'js' => 'http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.js',
								'css' => 'http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css',
								'min' => 'http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js' 
						),
						'ui' => array (
								'js' => 'http://code.jquery.com/ui/1.11.4/jquery-ui.js',
								'min' => 'http://code.jquery.com/ui/1.11.4/jquery-ui.min.js' 
						),
						'git' => array (
								'js' => 'http://code.jquery.com/mobile/git/jquery.mobile-git.js',
								'css' => 'http://code.jquery.com/mobile/git/jquery.mobile-git.css',
								'min' => 'http://code.jquery.com/mobile/git/jquery.mobile-git.min.js' 
						) 
				),
				
				// https://www.jstree.com/
				'jstree' => array (
						'jquery' => "//cdnjs.cloudflare.com/ajax/libs/jquery/1.11.1/jquery.min.js",
						'css' => "//cdnjs.cloudflare.com/ajax/libs/jstree/3.0.9/themes/default/style.min.css",
						'js' => "//cdnjs.cloudflare.com/ajax/libs/jstree/3.0.9/jstree.min.js" 
				) 
		] 
];

