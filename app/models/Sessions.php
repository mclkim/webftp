<?php

namespace App\Models;

use Kaiser\Manager\DBPageManager;

class Sessions extends DBPageManager {
	// function __construct($connection) {
	// parent::__construct ( $connection );
	// }
	function all() {
		$sql = 'select * from sessions where no=?';
		return $this->executePreparedQueryToPageMapList ( $sql, [ 
				99 
		] );
	}
}