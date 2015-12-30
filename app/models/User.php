<?php

namespace App\Models;

use Kaiser\Manager\DBPageManager;

class User extends DBPageManager {
	function __construct($pdo) {
		parent::__construct ( $pdo );
	}
	function all() {
		$sql = 'select * from mysql.user';
		return $this->executePreparedQueryToPageMapList ( $sql );
	}
}