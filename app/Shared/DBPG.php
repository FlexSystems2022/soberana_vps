<?php

namespace App\Shared;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Connection;

class DBPG
{
	/**
	 * Initialize Connection
	 * 
	 * @return \Illuminate\Database\Connection
	 */
	public static function initialize(): Connection
	{
		return DB::connection('pgsql');
	}
}