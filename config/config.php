<?php
return [
		// db settings
		'db' => [ 
				'driver' => 'mysql', // Db driver
				'host' => 'localhost',
				'port' => 3306,
				'database' => 'sessionsDB',
				'username' => 'sec_user',
				'password' => 'eKcGZr59zAa2BEWU',
				'charset' => 'utf8', // Optional
				'collation' => 'utf8_unicode_ci', // Optional
// 				'prefix' => 'cb_' // Table prefix, optional
				'options'   => array( // PDO constructor options, optional
						PDO::ATTR_TIMEOUT => 5,
						PDO::ATTR_EMULATE_PREPARES => false,
				),
		], 
		   
		// ftp settings
		'ftp' => [ 
				'scheme' => 'ftp',
				'host' => 'cookingm.com',
				'port' => 21,
				'user' => '',
				'pass' => '',
				'path' => '/',
				'timeout' => 90,
				'passive' => true 
		],
		
		// plupload[http://plupload.org/]
		'plupload' => [ 
				'chunk_size' => '1mb',
				'max_file_size' => '2gb',
				'max_file_count' => 20,
				'default_expire' => 365,
				'default_size' => 100 
		],
		
		// KLogger settings
		'logger' => [ 
				'path' => __DIR__ . '/../logs' 
		] 
];