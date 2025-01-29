<?php

namespace Comfort\Crm\Smtp\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class SmtpLog extends Eloquent {

	protected $table = 'cbxwpemaillogger_log';

	protected $appends = [ 'formatted_created_at' ];

	protected $fillable = [
		'email_type',
		'subject',
		'date_created',
		'email_data',
		'ip_address',
		'status',
		'error_message',
		'src_tracked',
		'mailer',
		'mailer_id',
		'mailer_api',
		'api_status'
	];

	public $timestamps = false;

	/**
	 * delete the form
	 *
	 * @return bool|null
	 */
	public function delete() {
		$form = $this->toArray();
		do_action( 'comfortsmtp_log_delete_before', $this->id, $form );

		$delete = parent::delete();
		if ( $delete ) {
			do_action( 'comfortsmtp_log_delete_after', $this->id, $form );
		} else {
			do_action( 'comfortsmtp_log_delete_failed', $this->id, $form );
		}

		return $delete;
	}

	/**
	 * Get misc data
	 *
	 * @return array
	 */
	public function getEmailDataAttribute() {
		if ( isset( $this->attributes['email_data'] ) && ! is_null( $this->attributes['email_data'] ) ) {
			return unserialize( $this->attributes['email_data'] );
		} else {
			return [];
		}
	}//end method getEmailDataAttribute

	/**
	 * get formatted create date
	 *
	 * @return string
	 */
	public function getFormattedCreatedAtAttribute() {
		if ( ! isset( $this->attributes['id'] ) ) {
			return '';
		}
		if ( ! isset( $this->attributes['date_created'] ) ) {
			return '';
		}

		$format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );

		return date_i18n( $format, strtotime( $this->attributes['date_created'] ) );
	}//end method getFormattedCreateDateAttribute
}//end class SmtpLog