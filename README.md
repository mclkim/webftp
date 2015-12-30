# webftp
webftp

```
git clone git@github.com:mclkim/webftp.git
```

composer update
```
cd webftp
composer update
```

git clone jstree & plupload 
```
git submodule update --init --recursive
```

config/config.php
```
<?php
return [
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
		
		// plupload
		'plupload' => [ 
				'chunk_size' => '1mb',
				'max_file_size' => '2gb',
				'max_file_count' => 20,
				'default_expire' => 365,
				'default_size' => 100 
		]
		
];
```