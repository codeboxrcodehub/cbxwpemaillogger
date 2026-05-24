<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! class_exists( 'ComfortSmtpGeneralAdminEmail', false ) ) :

	/**
	 * Class ComfortSmtpGeneralAdminEmail file
	 *
	 * Sending email alert to admin
	 */
	class ComfortSmtpGeneralAdminEmail extends ComfortSmtpEmail {
		/**
		 * Constructor.
		 */

		protected $meta;
		protected $custom_from_email;
		protected $custom_from_name;

		public function __construct() {
			$this->id          = 'generic_email_admin';
			$this->type        = 2;
			$this->enabled     = 'yes';
			$this->user_email  = false; //alert for admin
			$this->title       = esc_html__( 'General notification for admin', 'cbxwpemaillogger' );
			$this->description = esc_html__( 'Sends notification to admin on general purpose.', 'cbxwpemaillogger' );

			$this->template_html = 'emails/generic_email_admin.php';

			// Triggers for this email.
			add_action( 'comfortsmtp_general_email_admin_trigger', [ $this, 'trigger' ], 10, 2 );

			$this->placeholders = [
				'{user_name}'       => '',
				'{user_email}'      => '',
				'{email_body}'      => '',
			];

			$this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );

			// Call parent constructor.
			parent::__construct();
            $this->manual = true;
		}//end method constructor

        /**
         * Trigger the sending of this email.
         *
         * @param $data
         * @param $attachments
         *
         * @return true|void
         */
		public function trigger( $data, $attachments ) {
            $this->data = $data;

			if( empty($data['email_body']) ){
				return;
			}

			$this->placeholders['{email_body}']  = $data['email_body'];

            $this->recipient = (!empty($data['to_email']) && is_email($data['to_email']))? sanitize_email($data['to_email']) : $this->recipient;


			$content = $this->get_content();

			$subject = isset($data['subject']) ? $data['subject'] : $this->get_subject();
			$heading = isset($data['heading']) ? $data['heading'] : $this->get_default_heading();

			$this->sendEmail( $this->get_recipient(), $subject, $content, $this->set_headers( $data ), $attachments, $data );

			return true;			
		}//end method trigger

		/**
		 * Send an email.
		 *
		 * @param  string  $to  Email to.
		 * @param  string  $subject  Email subject.
		 * @param  string  $message  Email message.
		 * @param  string  $headers  Email headers.
		 * @param  array  $attachments  Email attachments.
		 * @param  array  $data  Email data.
		 *
		 * @return bool success
		 */
		public function sendEmail( $to, $subject, $message, $headers, $attachments, $data ) {

			$this->custom_from_email = $data['from_email'] ?? '';
			$this->custom_from_name  = $data['from_name'] ?? '';

			add_filter( 'wp_mail_from', [ $this, 'get_from_email' ] );
			add_filter( 'wp_mail_from_name', [ $this, 'get_from_name' ] );

			add_filter( 'wp_mail_content_type', [ $this, 'get_content_type' ] );

			$message = $this->format_string( $message );

			$message              = apply_filters( 'cbxwpemaillogger_mail_content', $this->style_inline( $message ) );
			$mail_callback        = apply_filters( 'cbxwpemaillogger_mail_callback', 'wp_mail', $this );
			$mail_callback_params = apply_filters( 'cbxwpemaillogger_mail_callback_params', [
				$to,
				wp_specialchars_decode( $subject ),
				$message,
				$headers,
				$attachments
			], $this );
			$return               = $mail_callback( ...$mail_callback_params );

			remove_filter( 'wp_mail_from', [ $this, 'get_from_email' ] );
			remove_filter( 'wp_mail_from_name', [ $this, 'get_from_name' ] );
			remove_filter( 'wp_mail_content_type', [ $this, 'get_content_type' ] );

			// Clear the AltBody (if set) so that it does not leak across to different emails.
			$this->clear_alt_body_field();

			/**
			 * Action hook fired when an email is sent.
			 *
			 * @param  bool  $return  Whether the email was sent successfully.
			 * @param  int  $id  Email ID.
			 * @param  ComfortSmtpEmail  $this  ComfortSmtpEmail instance.
			 *
			 * @since 2.1.0
			 */
			do_action( 'cbxwpemaillogger_email_sent', $return, $this->id, $this );

			return $return;
		}//end function send

		/**
		 * Set email headers.
		 *
		 * @return string
		 */
		public function set_headers( $data ) {
			$header = 'Content-Type: ' . $this->get_content_type() . "\r\n";

			//handle css
			if ( isset($data['cc']) ) {
				$cc_items = explode( ',', $data['cc'] );
				foreach ( $cc_items as $cc_item ) {
					$header .= 'CC: ' . $cc_item . "\r\n";
				}
			}

			//handle bcc
			if ( isset($data['bcc']) ) {
				$bcc_items = explode( ',', $data['bcc'] );
				foreach ( $bcc_items as $bcc_item ) {
					$header .= 'BCC: ' . $bcc_item . "\r\n";
				}
			}

			if ( isset($data['reply_to']) && is_email($data['reply_to'])) {
				$header .= 'Reply-to: ' . $data['reply_to'] . " \r\n";
			}

			return $header;
		}//end method set_headers

		public function get_from_email( $from_email = '' ) {
			// If we passed a custom email in sendEmail, use it; otherwise, use the default logic
            $from_email = !empty($this->custom_from_email) ? $this->custom_from_email : $this->get_option( 'from_email', '' );
			
			$from_email = apply_filters( 'cbxwpemaillogger_email_from_email_' . $this->id, $from_email, $this->object, $this );
			$from_email = trim( $from_email );

			return is_email( $from_email ) ? sanitize_email( $from_email ) : $from_email;
		}//end method get_from_email

		public function get_from_name( $from_name = '' ) {
            $from_name = !empty($this->custom_from_name) ? $this->custom_from_name : $this->get_option( 'from_name', '' );
			
			$from_name = apply_filters( 'cbxwpemaillogger_email_from_name_' . $this->id, trim( $from_name ), $this->object, $this );

			return wp_specialchars_decode( esc_html( $from_name ), ENT_QUOTES );
		}//end method get_from_name

		/**
		 * Get email subject.
		 *
		 * @return string
		 * @since  2.1.0
		 */
		public function get_default_subject() {
			return esc_html__( 'Notification for admin', 'cbxwpemaillogger' );
		}//end method get_default_subject

		/**
		 * Get email heading.
		 *
		 * @return string
		 * @since  2.1.0
		 */
		public function get_default_heading() {
			return esc_html__( 'Notification for admin', 'cbxwpemaillogger' );
		}//end method get_default_heading

        /**
         * Default content to show below main email content.
         *
         * @return string
         * @since 2.1.0
         */
        public function get_additional_content() {
            return !empty($this->data['additional_content']) ? apply_filters( 'cbxwpemaillogger_email_additional_content_' . $this->id, $this->format_string($this->data['additional_content']), $this->object, $this) : '';
        }//end method get_additional_content

		/**
		 * Get email content.
		 *
		 * @return string
		 */
		public function get_content() {

			if ( 'plain' === $this->get_email_type() ) {
				$email_content = wordwrap( preg_replace( $this->plain_search, $this->plain_replace, wp_strip_all_tags( $this->get_content_plain() ) ), 70 );
			} else {
				$email_content = $this->get_content_html();
			}

			return $email_content;
		}//end method get_content

		/**
		 * Get content html.
		 *
		 * @return string
		 */
		public function get_content_html() {
			return comfortsmtp_get_template_html( $this->template_html, [
				'email_heading'      => $this->get_heading(),
				'additional_content' => $this->get_additional_content(),
				'email'              => $this,
			] );
		}//end method get_content_html

		/**
		 * Get content plain.
		 *
		 * @return string
		 */
		public function get_content_plain() {
			$message = $this->get_content_html();

			return \Soundasleep\Html2Text::convert( $message );
		}//end method get_content_plain

		/**
		 * Clears the PhpMailer AltBody field, to prevent that content from leaking across emails.
		 */
		private function clear_alt_body_field(): void {
			global $phpmailer;

			if ( $phpmailer instanceof PHPMailer\PHPMailer\PHPMailer ) {
				$phpmailer->AltBody = ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			}
		}//end method clear_alt_body_field
	}//end class ComfortSmtpGeneralAdminEmail
endif;

return new ComfortSmtpGeneralAdminEmail();