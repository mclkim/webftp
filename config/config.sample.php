<?php
return [
		// db settings
		'db' => [ 
				'mysql' => [ 
						'host' => '127.0.0.1',
						'port' => 3306,
						'dbname' => 'mysql',
						'username' => 'root',
						'password' => 'root1234' 
				]
		],
		
		// ftp settings
		'ftp' => [ 
				'type' => 'ftp',
				'host' => '127.0.0.1',
				'port' => 21,
				'username' => 'username',
				'password' => 'password',
				'timeout' => 90,
				'passive' => true 
		],
		
		// monolog settings
		'logger' => [ 
				'name' => 'app',
				'path' => __DIR__ . '/../log/app-' . date ( 'Y-m-d' ) . '.log' 
		] 
];