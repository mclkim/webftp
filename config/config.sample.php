<?php
return [
		// db settings
		'db' => [ 
				'driver' => 'mysql', // Db driver
				'host' => 'localhost',
				'port' => 3306,
				'database' => 'dbname',
				'username' => 'username',
				'password' => 'password',
				'charset' => 'utf8', // Optional
				'collation' => 'utf8_unicode_ci', // Optional
		], 
		   
		// ftp settings
		'ftp' => [ 
				'scheme' => 'ftp',
				'host' => 'localhost',
				'port' => 21,
				'user' => '',
				'pass' => '',
				'path' => '/',
				'timeout' => 90,
				'passive' => true 
		],
		
		// plupload[http://plupload.org/]
		'plupload' => [ 
				'file_data_name' => 'file',
				'chunk_size' => '1mb',
				'max_file_size' => '2gb',
				'max_file_count' => 20,
				'default_expire' => 365,
				'default_size' => 100 
		]
		
];