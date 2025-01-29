<?php

namespace Comfort\Crm\Smtp\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Migrations extends Eloquent {

	protected $table = 'cbxmigrations';

	protected $guarded = [];

	public $timestamps = false;
}//end class Migrations