<?php

namespace Comfort\Crm\Smtp\Models;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

use ComfortSmtpScoped\Illuminate\Database\Eloquent\Model as Eloquent;

class Migrations extends Eloquent {

	protected $table = 'cbxmigrations';

	protected $guarded = [];

	public $timestamps = false;
}//end class Migrations