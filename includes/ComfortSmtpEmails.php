<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ComfortSmtpEmails {
	/**
	 * The single instance of the class
	 *
	 * @var ComfortSmtpEmails
	 */
	private static $_instance = null;

	/**
	 * Array of email notification classes
	 *
	 * @var ComfortSmtpEmails[]
	 */
	public $emails = [];

	//public $mail_format;

	/**
	 * Main ComfortSmtpEmails Instance.
	 *
	 * Ensures only one instance of ComfortSmtpEmails is loaded or can be loaded.
	 *
	 * @return ComfortSmtpEmails Main instance
	 * @since 2.1
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}//end method instance

	/**
	 * Cloning is forbidden.
	 *
	 * @since 2.0.0
	 */
	public function __clone() {
		comfortsmtp_doing_it_wrong( __FUNCTION__, __( 'Cloning is forbidden.', 'cbxwpemaillogger' ), '2.0.0' );
	}//end method clone

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 2.0.0
	 */
	public function __wakeup() {
		comfortsmtp_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'cbxwpemaillogger' ), '2.0.0' );
	}//end method wakeup

	public function __construct() {
		$this->init();

		// Email Header, Footer and content hooks.
		add_action( 'comfortsmtp_email_header', [ $this, 'email_header' ] );
		add_action( 'comfortsmtp_email_footer', [ $this, 'email_footer' ] );

		// Let 3rd parties unhook the above via this hook.
		do_action( 'cbxwpemaillogger_email', $this );
	}//end constructor

	/**
	 * Init email classes.
	 */
	public function init() {
		// Include email classes.
		include_once __DIR__ . '/Emails/ComfortSmtpEmail.php';

		$this->emails['generic_email_user']  = include __DIR__ . '/Emails/ComfortSmtpGeneralUserEmail.php';
		$this->emails['generic_email_admin'] = include __DIR__ . '/Emails/ComfortSmtpGeneralAdminEmail.php';

		$this->emails = apply_filters( 'cbxwpemaillogger_email_classes', $this->emails );
	}//end method init

	/**
	 * Get the email header.
	 *
	 * @param  mixed  $email_heading  Heading for the email.
	 */
	public function email_header( $email_heading ) {
		$tpl_settings      = get_option( 'comfortsmtp_email_tpl', [] );
		$selected_template = isset( $tpl_settings['selected_template'] ) ? $tpl_settings['selected_template'] : 'tpl-general';

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo comfortsmtp_get_template_html( 'email_templates/' . $selected_template . '/email-header.php', [
			'email_heading'     => $email_heading,
			'template_settings' => $tpl_settings
		] );

	}//end method email_header

	/**
	 * Get the email footer.
	 */
	public function email_footer() {
		$tpl_settings = get_option( 'comfortsmtp_email_tpl', [] );
		$selected_tpl = isset( $tpl_settings['selected_template'] ) ? $tpl_settings['selected_template'] : 'tpl-general';

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo comfortsmtp_get_template_html( 'email_templates/' . esc_attr( $selected_tpl ) . '/email-footer.php', [
			'template_settings' => $tpl_settings
		] );

	}//end method email_footer

	/**
	 * Wraps a message in the cbxwpemaillogger mail template.
	 *
	 * @param  string  $email_heading  Heading text.
	 * @param  string  $message  Email message.
	 * @param  bool  $plain_text  Set true to send as plain text. Default to false.
	 *
	 * @return string
	 */
	public function wrap_message( $email_heading, $message, $plain_text = false ) {
		// Buffer.
		ob_start();

		do_action( 'comfortsmtp_email_header', $email_heading, null );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wp_kses_post( wpautop( wptexturize( $message ) ) ); // WPCS: XSS ok.

		do_action( 'comfortsmtp_email_footer', null );

		// Get contents.
		return ob_get_clean();
	}//end method wrap_message

	/**
	 * Send the email.
	 *
	 * @param  mixed  $to  Receiver.
	 * @param  mixed  $subject  Email subject.
	 * @param  mixed  $message  Message.
	 * @param  string  $headers  Email headers (default: "Content-Type: text/html\r\n").
	 * @param  string  $attachments  Attachments (default: "").
	 *
	 * @return bool
	 */
	public function send( $to, $subject, $message, $headers = "Content-Type: text/html\r\n", $attachments = '' ) {
		// Send.
		$email = new ComfortSmtpEmail();

		return $email->send( $to, $subject, $message, $headers, $attachments );
	}//end method send


	/**
	 * Get blog name formatted for emails.
	 *
	 * @return string
	 */
	private function get_blogname() {
		return wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}//end method get_blogname

	public function is_user_email() {
		return $this->user_email;
	}//end method is_user_email
}//end class ComfortSmtpEmails