<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Comfort\Crm\Smtp\Helpers\ComfortSmtpHelpers;
use Pelago\Emogrifier\CssInliner;
use Pelago\Emogrifier\HtmlProcessor\CssToAttributeConverter;
use Pelago\Emogrifier\HtmlProcessor\HtmlPruner;

if ( class_exists( 'ComfortSmtpEmail', false ) ) {
	return;
}

/**
 * Email alert parent class
 */
class ComfortSmtpEmail {
	/**
	 * Email method ID.
	 *
	 * @var String
	 */
	public $id;

	/**
	 * Email method title.
	 *
	 * @var string
	 */
	public $title;

	/**
	 * 'yes' if the method is enabled.
	 *
	 * @var string yes, no
	 */
	public $enabled;

	/**
	 * True when the email notification is sent manually only. So, this type email are always active but can be configured
	 *
	 * @var bool
	 */
	protected $manual = false;

	/**
	 * If Dynamic Fields which means settings fields are dynamic $type = 2, If Static/editable Fields $type = 1
	 *
	 * @var int
	 */
	public $type = 1;


	/**
	 * True when the email notification is sent to customers.
	 *
	 * @var bool
	 */
	protected $user_email = false;

	/**
	 * Description for the email.
	 *
	 * @var string
	 */
	public $description;

	/**
	 * Default heading.
	 *
	 *
	 * @var string
	 */
	public $heading = '';

	/**
	 * Default subject.
	 *
	 * Supported for backwards compatibility but we recommend overloading the
	 * get_default_x methods instead so localization can be done when needed.
	 *
	 * @var string
	 */
	public $subject = '';

	/**
	 * Validation errors.
	 *
	 * @var array of strings
	 */
	public $errors = [];

	/**
	 * Setting values.
	 *
	 * @var array
	 */
	public $settings = [];

	/**
	 * Recipients for the email.
	 *
	 * @var string
	 */
	//public $recipient;

	/**
	 * Form option fields.
	 *
	 * @var array
	 */
	public $form_fields = [];

	/**
	 * Object this email is for, for example a customer, product, or email.
	 *
	 * @var object|bool
	 */
	public $object;

	/**
	 * The posted settings data. When empty, $_POST data will be used.
	 *
	 * @var array
	 */
	protected $data = [];

	/**
	 * Strings to find/replace in subjects/headings/contents.
	 *
	 * @var array
	 */
	protected $placeholders = [];

	/**
	 * Strings to find in subjects/headings.
	 *
	 * @deprecated 3.2.0 in favour of placeholders
	 * @var array
	 */
	//public $find = array();

	/**
	 * Strings to replace in subjects/headings.
	 *
	 * @deprecated 3.2.0 in favour of placeholders
	 * @var array
	 */
	//public $replace = array();

	/**
	 * E-mail type: plain, html or multipart.
	 *
	 * @var string
	 */
	public $email_type;

	/**
	 *  List of preg* regular expression patterns to search for,
	 *  used in conjunction with $plain_replace.
	 *  https://raw.github.com/ushahidi/wp-silcc/master/class.html2text.inc
	 *
	 * @var array $plain_search
	 * @see $plain_replace
	 */
	public $plain_search = [
		"/\r/",                                                  // Non-legal carriage return.
		'/&(nbsp|#0*160);/i',                                    // Non-breaking space.
		'/&(quot|rdquo|ldquo|#0*8220|#0*8221|#0*147|#0*148);/i', // Double quotes.
		'/&(apos|rsquo|lsquo|#0*8216|#0*8217);/i',               // Single quotes.
		'/&gt;/i',                                               // Greater-than.
		'/&lt;/i',                                               // Less-than.
		'/&#0*38;/i',                                            // Ampersand.
		'/&amp;/i',                                              // Ampersand.
		'/&(copy|#0*169);/i',                                    // Copyright.
		'/&(trade|#0*8482|#0*153);/i',                           // Trademark.
		'/&(reg|#0*174);/i',                                     // Registered.
		'/&(mdash|#0*151|#0*8212);/i',                           // mdash.
		'/&(ndash|minus|#0*8211|#0*8722);/i',                    // ndash.
		'/&(bull|#0*149|#0*8226);/i',                            // Bullet.
		'/&(pound|#0*163);/i',                                   // Pound sign.
		'/&(euro|#0*8364);/i',                                   // Euro sign.
		'/&(dollar|#0*36);/i',                                   // Dollar sign.
		'/&[^&\s;]+;/i',                                         // Unknown/unhandled entities.
		'/[ ]{2,}/',                                             // Runs of spaces, post-handling.
	];

	/**
	 *  List of pattern replacements corresponding to patterns searched.
	 *
	 * @var array $plain_replace
	 * @see $plain_search
	 */
	public $plain_replace = [
		'',                                              // Non-legal carriage return.
		' ',                                             // Non-breaking space.
		'"',                                             // Double quotes.
		"'",                                             // Single quotes.
		'>',                                             // Greater-than.
		'<',                                             // Less-than.
		'&',                                             // Ampersand.
		'&',                                             // Ampersand.
		'(c)',                                           // Copyright.
		'(tm)',                                          // Trademark.
		'(R)',                                           // Registered.
		'--',                                            // mdash.
		'-',                                             // ndash.
		'*',                                             // Bullet.
		'£',                                             // Pound sign.
		'EUR',                                           // Euro sign. € ?.
		'$',                                             // Dollar sign.
		'',                                              // Unknown/unhandled entities.
		' ',                                             // Runs of spaces, post-handling.
	];

	/**
	 * HTML template path.
	 *
	 * @var string
	 */
	public $template_html;

	/**
	 * Recipients for the email.
	 *
	 * @var string
	 */
	public $recipient;

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Find/replace.
		$this->placeholders = array_merge(
			array(
				'{site_title}'   => $this->get_blogname(),
				'{site_url}'     => wp_parse_url( home_url(), PHP_URL_HOST ),
				'{site_link}'     => '<a href="'.wp_parse_url( home_url(), PHP_URL_HOST ).'">'.$this->get_blogname().'</a>',
			),
			$this->placeholders
		);

		// Init settings.
		$this->init_form_fields();
		$this->init_settings();

		// Default template base if not declared in child constructor.

		$this->email_type = $this->get_option( 'email_type' );
		$this->enabled    = $this->get_option( 'enabled' );

		add_action( 'phpmailer_init', [ $this, 'handle_multipart' ] );
		add_filter( 'comfortsmtp_email_footer_text', [ $this, 'footer_text_format' ] );
	}//end constructor


	/**
	 * Return the email's title
	 *
	 * @return string
	 */
	public function get_title() {
		return apply_filters( 'cbxwpemaillogger_email_title', $this->title, $this );
	}//end method get_title

	/**
	 * Return the email's description
	 *
	 * @return string
	 */
	public function get_description() {
		return apply_filters( 'cbxwpemaillogger_email_description', $this->description, $this );
	}//end method get_description

	/**
	 * Checks if this email is enabled and will be sent.
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return apply_filters( 'cbxwpemaillogger_email_enabled_' . $this->id, 'yes' === $this->enabled, $this->object, $this );
	}//end method is_enabled

	/**
	 * Get email subject.
	 *
	 * @return string
	 * @since  3.1.0
	 */
	public function get_default_subject() {
		return $this->subject;
	}//end method get_default_subject

	/**
	 * Get default email heading.
	 *
	 * @return string
	 * @since  2.0.0
	 */
	public function get_default_heading() {
		return $this->heading;
	}//end method get_default_heading

	public function get_default_recipient() {
		return get_option( 'admin_email' );
	}//end method get_default_recipient

	/**
	 * Get email subject.
	 *
	 * @return string
	 */
	public function get_subject() {
		return apply_filters( 'cbxwpemaillogger_email_subject_' . $this->id, $this->format_string( $this->get_option( 'subject', $this->get_default_subject() ) ), $this->object, $this );
	}//end method get_subject

	/**
	 * Get email heading.
	 *
	 * @return string
	 */
	public function get_heading() {
		return apply_filters( 'cbxwpemaillogger_email_heading_' . $this->id, $this->format_string( $this->get_option( 'heading', $this->get_default_heading() ) ), $this->object, $this );
	}//end method get_heading

	/**
	 * Get valid recipient(s).
	 *
	 * @return string
	 */
	public function get_recipient() {
		//$recipient  = apply_filters( 'cbxwpemaillogger_email_recipient_' . $this->id, $this->get_option( 'recipient', $this->get_default_recipient() ), $this->object, $this );

		$recipient = apply_filters( 'cbxwpemaillogger_email_recipient_' . $this->id, $this->recipient, $this->object, $this );
		if ( $recipient !== null ) {
			$recipients = array_map( 'trim', explode( ',', $recipient ) );
			$recipients = array_filter( $recipients, 'is_email' );

			return implode( ', ', $recipients );
		}

		return $recipient;
	}//end method get_recipient

	/**
	 * Get email headers.
	 *
	 * @return string
	 */
	public function get_headers() {
		$header = 'Content-Type: ' . $this->get_content_type() . "\r\n";

		//handle css
		if ( $this->get_cc() ) {
			$cc       = $this->get_cc();
			$cc_items = explode( ',', $cc );
			foreach ( $cc_items as $cc_item ) {
				$header .= 'CC: ' . $cc_item . "\r\n";
			}
		}

		//handle bcc
		if ( $this->get_bcc() ) {
			$bcc       = $this->get_bcc();
			$bcc_items = explode( ',', $bcc );
			foreach ( $bcc_items as $bcc_item ) {
				$header .= 'BCC: ' . $bcc_item . "\r\n";
			}
		}

		if ( $this->get_replyto() ) {
			$header .= 'Reply-to: ' . $this->get_replyto() . " \r\n";
		} elseif ( $this->get_from_email() && $this->get_from_name() ) {
			$header .= 'Reply-to: ' . $this->get_from_name() . ' <' . $this->get_from_email() . ">\r\n";
		}

		return apply_filters( 'comfortsmtp_email_headers', $header, $this->id, $this->object, $this );
	}//end method get_headers

	/**
	 * Get valid cc(s).
	 *
	 * @return string
	 */
	public function get_cc() {
		$cc = apply_filters( 'cbxwpemaillogger_email_cc_' . $this->id, $this->get_option( 'cc', '' ), $this->object, $this );
		$cc = array_map( 'trim', explode( ',', $cc ) );
		$cc = array_filter( $cc, 'is_email' );

		return implode( ', ', $cc );
	}//end method get_cc

	/**
	 * Get valid bcc(s).
	 *
	 * @return string
	 */
	public function get_bcc() {
		$bcc = apply_filters( 'cbxwpemaillogger_email_bcc_' . $this->id, $this->get_option( 'bcc', '' ), $this->object, $this );
		$bcc = array_map( 'trim', explode( ',', $bcc ) );
		$bcc = array_filter( $bcc, 'is_email' );

		return implode( ', ', $bcc );
	}//end method get_bcc

	/**
	 * Get valid replyto
	 *
	 * @return string
	 */
	public function get_replyto() {
		$reply_to = apply_filters( 'cbxwpemaillogger_email_replyto_' . $this->id, $this->get_option( 'replyto', '' ), $this->object, $this );

		return trim( $reply_to );
	}//end method get_replyto

	/**
	 * Get from name
	 *
	 * @return string
	 */
	public function get_from_name( $from_name = '' ) {
		$from_name = apply_filters( 'cbxwpemaillogger_email_from_name_' . $this->id, trim( $this->get_option( 'from_name', '' ) ), $this->object, $this, $from_name );

		return wp_specialchars_decode( esc_html( $from_name ), ENT_QUOTES );
	}//end method get_from_name

	/**
	 * Get valid from email
	 *
	 * @return string
	 */
	public function get_from_email( $from_email = '' ) {
		$from_email = apply_filters( 'cbxwpemaillogger_email_from_email_' . $this->id, $this->get_option( 'from_email', '' ), $this->object, $this, $from_email );
		$from_email = trim( $from_email );

		return is_email( $from_email ) ? sanitize_email( $from_email ) : '';
	}//end method get_from_email

	/**
	 * Send an email.
	 *
	 * @param  string  $to  Email to.
	 * @param  string  $subject  Email subject.
	 * @param  string  $message  Email message.
	 * @param  string  $headers  Email headers.
	 * @param  array  $attachments  Email attachments.
	 *
	 * @return bool success
	 */
	public function send( $to, $subject, $message, $headers, $attachments ) {
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
		 * @since 5.6.0
		 */
		do_action( 'cbxwpemaillogger_email_sent', $return, $this->id, $this );

		return $return;
	}//end function send

	/**
	 * Clears the PhpMailer AltBody field, to prevent that content from leaking across emails.
	 */
	private function clear_alt_body_field(): void {
		global $phpmailer;

		if ( $phpmailer instanceof PHPMailer\PHPMailer\PHPMailer ) {
			$phpmailer->AltBody = ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		}
	}//end method clear_alt_body_field


	/**
	 * Checks if this email is customer focussed.
	 *
	 * @return bool
	 */
	public function is_user_email() {
		return $this->user_email;
	}//end method is_user_email

	/**
	 * Get WordPress blog name.
	 *
	 * @return string
	 */
	public function get_blogname() {
		return wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}//end method get_blogname


	/**
	 * Default content to show below main email content.
	 *
	 * @return string
	 * @since 2.1.0
	 */
	public function get_default_additional_content() {
		return '';
	}//end method get_default_additional_content

	/**
	 * Initialise Settings Form Fields - these are generic email options most will use.
	 */
	public function init_form_fields() {
		/* translators: %s: list of placeholders */
		$placeholder_text  = sprintf( __( 'Available placeholders: %s', 'cbxwpemaillogger' ), '<code>' . esc_html( implode( '</code>, <code>', array_keys( $this->placeholders ) ) ) . '</code>' );
		$this->form_fields = [
			'enabled'            => [
				'title'   => esc_html__( 'Enable/Disable', 'cbxwpemaillogger' ),
				'type'    => 'checkbox',
				'label'   => esc_html__( 'Enable this email notification', 'cbxwpemaillogger' ),
				'default' => 'yes'
			],
			'subject'            => [
				'title'       => esc_html__( 'Subject', 'cbxwpemaillogger' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => $placeholder_text,
				'placeholder' => esc_html__( 'Email subject here', 'cbxwpemaillogger' ),
				'default'     => $this->get_default_subject()
			],
			'heading'            => [
				'title'       => esc_html__( 'Email heading', 'cbxwpemaillogger' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => $placeholder_text,
				'placeholder' => esc_html__( 'Email heading here', 'cbxwpemaillogger' ),
				'default'     => $this->get_default_heading()
			],
			'additional_content' => [
				'title'       => esc_html__( 'Additional content', 'cbxwpemaillogger' ),
				'description' => esc_html__( 'Text to appear below the main email content.', 'cbxwpemaillogger' ) . ' ' . $placeholder_text,
				'css'         => 'width:400px; height: 75px;',
				'placeholder' => esc_html__( 'N/A', 'cbxwpemaillogger' ),
				'type'        => 'textarea',
				'default'     => $this->get_default_additional_content(),
				'desc_tip'    => true
			],
			'email_type'         => [
				'title'       => esc_html__( 'Email type', 'cbxwpemaillogger' ),
				'type'        => 'select',
				'description' => esc_html__( 'Choose which format of email to send.', 'cbxwpemaillogger' ),
				'default'     => 'html',
				'class'       => 'email_type wc-enhanced-select',
				'options'     => $this->get_email_type_options(),
				'desc_tip'    => true
			],
			'from_name'          => [
				'title'       => esc_html__( 'From Name', 'cbxwpemaillogger' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => esc_html__( 'Email sent from name. Put empty to set this from WordPress core or via any smtp plugin.', 'cbxwpemaillogger' ),
				'placeholder' => esc_html__( 'From name', 'cbxwpemaillogger' ),
				'default'     => ''
			],
			'from_email'         => [
				'title'       => esc_html__( 'From Email', 'cbxwpemaillogger' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => esc_html__( 'Email sent from name. Put empty to set this from WordPress core or via any smtp plugin.', 'cbxwpemaillogger' ),
				'placeholder' => esc_html__( 'From Email', 'cbxwpemaillogger' ),
				'default'     => ''
			],
			'cc'                 => [
				'title'       => esc_html__( 'CC', 'cbxwpemaillogger' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => esc_html__( 'Email Recipient(s) as CC. Put multiple as comma.', 'cbxwpemaillogger' ),
				'placeholder' => esc_html__( 'Email', 'cbxwpemaillogger' ),
				'default'     => ''
			],
			'bcc'                => [
				'title'       => esc_html__( 'BCC', 'cbxwpemaillogger' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => esc_html__( 'Email Recipient(s) as BCC. Put multiple as comma.', 'cbxwpemaillogger' ),
				'placeholder' => esc_html__( 'Email', 'cbxwpemaillogger' ),
				'default'     => ''
			],
			'replyto'            => [
				'title'       => esc_html__( 'Reply To', 'cbxwpemaillogger' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => wp_kses( __( 'Email reply to. If empty then will be used from <strong>From Email</strong>.', 'cbxwpemaillogger' ), ComfortSmtpHelpers::allowedHtmlTags() ),
				'placeholder' => esc_html__( 'Reply to email', 'cbxwpemaillogger' ),
				'default'     => ''
			],
		];		
	}//end method init_form_fields

	/**
	 * Get the form fields after they are initialized.
	 *
	 * @return array of options
	 */
	public function get_form_fields() {
		return apply_filters( 'cbxwpemaillogger_email_form_fields_' . $this->id, array_map( [
			$this,
			'set_defaults'
		], $this->form_fields ) );
	}//end method get_form_fields

	/**
	 * Set default required properties for each field.
	 *
	 * @param  array  $field  Setting field array.
	 *
	 * @return array
	 */
	protected function set_defaults( $field ) {
		if ( ! isset( $field['default'] ) ) {
			$field['default'] = '';
		}

		return $field;
	}//end method set_defaults

	/**
	 * Init the email class settings
	 *
	 * @return void
	 */
	public function init_settings() {
		$email_settings = get_option( 'comfortsmtp_emails', [] );


		$this->settings = isset( $email_settings[ $this->id ] ) ? $email_settings[ $this->id ] : [];


		// If there are no settings defined, use defaults.
		if ( ! is_array( $this->settings ) || ( is_array( $this->settings ) && sizeof( $this->settings ) == 0 ) ) {
			$form_fields    = $this->get_form_fields();
			$this->settings = array_merge( array_fill_keys( array_keys( $form_fields ), '' ), wp_list_pluck( $form_fields, 'default' ) );
		}
	}//end method init_settings

	public function get_option( $key = '', $default = '' ) {
		if ( $key == '' ) {
			return '';
		}

		$settings = $this->settings;

		return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
	}//end method get_option


	/**
	 * Get email attachments.
	 *
	 * @return array
	 */
	public function get_attachments() {
		return apply_filters( 'cbxwpemaillogger_email_attachments', [], $this->id, $this->object, $this );
	}//end method get_attachments

	/**
	 * Get the email content in plain text format.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		return '';
	}//end method get_content_plain

	/**
	 * Get the email content in HTML format.
	 *
	 * @return string
	 */
	public function get_content_html() {
		return '';
	}//end method get_content_html

	/**
	 * Email type options.
	 *
	 * @return array
	 */
	public function get_email_type_options() {
		$types = [ 'plain' => esc_html__( 'Plain text', 'cbxwpemaillogger' ) ];

		if ( class_exists( 'DOMDocument' ) ) {
			$types['html']      = esc_html__( 'HTML', 'cbxwpemaillogger' );
			$types['multipart'] = esc_html__( 'Multipart', 'cbxwpemaillogger' );
		}

		return $types;
	}//end method get_email_type_options

	/**
	 * Return email type.
	 *
	 * @return string
	 */
	public function get_email_type() {
		return $this->email_type && class_exists( 'DOMDocument' ) ? $this->email_type : 'plain';
	}//end method get_email_type

	/**
	 * Get email content type.
	 *
	 * @param  string  $default_content_type  Default wp_mail() content type.
	 *
	 * @return string
	 */
	public function get_content_type( $default_content_type = '' ) {
		switch ( $this->get_email_type() ) {
			case 'html':
				$content_type = 'text/html';
				break;
			case 'multipart':
				$content_type = 'multipart/alternative';
				break;
			default:
				$content_type = 'text/plain';
				break;
		}

		return apply_filters( 'cbxwpemaillogger_email_content_type', $content_type, $this, $default_content_type );
	}//end method get_content_type

	/**
	 * Format email string.
	 *
	 * @param  mixed  $string  Text to replace placeholders in.
	 *
	 * @return string
	 */
	public function format_string( $string ) {
		$find    = array_keys( $this->placeholders );
		$replace = array_values( $this->placeholders );


		// Take care of blogname which is no longer defined as a valid placeholder.
		$find[]    = '{blogname}';
		$replace[] = $this->get_blogname();

		/**
		 * Filter for main find/replace.
		 *
		 * @since 3.2.0
		 */
		return apply_filters( 'cbxwpemaillogger_email_format_string', str_replace( $find, $replace, $string ), $this );
	}//end method format_string

	/**
	 * Return if emogrifier library is supported.
	 *
	 * @return bool
	 * @since 3.5.0
	 * @version 4.0.0
	 */
	protected function supports_emogrifier() {
		return class_exists( 'DOMDocument' );
	}//end method supports_emogrifier

	/**
	 * Returns CSS styles that should be included with all HTML e-mails, regardless of theme specific customizations.
	 *
	 * @return string
	 * @since 9.1.0
	 *
	 */
	protected function get_must_use_css_styles(): string {
		// phpcs:ignore Squiz.PHP.Heredoc.NotAllowed
		$css = <<<'EOF'

		/*
		* Temporary measure until e-mail clients more properly support the correct styles.
		* See https://github.com/woocommerce/woocommerce/pull/47738.
		*/
		.screen-reader-text {
			display: none;
		}

		EOF;

		return $css;
	}//end method get_must_use_css_styles

	/**
	 * Apply inline styles to dynamic content.
	 *
	 * We only inline CSS for html emails, and to do so we use Emogrifier library (if supported).
	 *
	 * @param  string|null  $content  Content that will receive inline styles.
	 *
	 * @return string
	 * @version 4.0.0
	 */
	public function style_inline( $content ) {
		if ( in_array( $this->get_content_type(), [ 'text/html', 'multipart/alternative' ], true ) ) {

			$css = $this->get_must_use_css_styles();
			$css .= "\n";

			ob_start();

			$tpl_settings = get_option( 'comfortsmtp_email_tpl', [] );
			$sel_template = $tpl_settings['selected_template'] ?? 'tpl-general';
			echo comfortsmtp_get_template_html( 'email_templates/'. esc_attr($sel_template) .'/email-styles.php' );  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			$css .= ob_get_clean();

			/**
			 * Provides an opportunity to filter the CSS styles included in e-mails.
			 *
			 * @param  string  $css  CSS code.
			 * @param  \ComfortSmtpEmail  $email  E-mail instance.
			 *
			 * @since 2.3.0
			 *
			 */
			$css = apply_filters( 'cbxwpemaillogger_email_styles', $css, $this );

			$css_inliner_class = CssInliner::class;

			if ( $this->supports_emogrifier() && class_exists( $css_inliner_class ) ) {
				try {
					$css_inliner = CssInliner::fromHtml( $content )->inlineCss( $css );

					do_action( 'cbxwpemaillogger_emogrifier', $css_inliner, $this );

					$dom_document = $css_inliner->getDomDocument();

					HtmlPruner::fromDomDocument( $dom_document )->removeElementsWithDisplayNone();
					$content = CssToAttributeConverter::fromDomDocument( $dom_document )
					                                  ->convertCssToVisualAttributes()
					                                  ->render();
				} catch ( Exception $e ) {
					//$logger = wc_get_logger();
					//$logger->error( $e->getMessage(), array( 'source' => 'emogrifier' ) );
				}
			} else {
				$content = '<style>' . $css . '</style>' . $content;
			}
		}

		return $content;
	}//end method style_inline

	/**
	 * Return content from the additional_content field.
	 *
	 * Displayed above the footer.
	 *
	 * @return string
	 * @since 2.1.0
	 */
	public function get_additional_content() {
		/**
		 * Provides an opportunity to inspect and modify additional content for the email.
		 *
		 * @param  string  $additional_content  Additional content to be added to the email.
		 * @param  object|bool  $object  The object (ie, product or order) this email relates to, if any.
		 * @param  ComfortSmtpEmail  $email  ComfortSmtpEmail instance managing the email.
		 *
		 * @since 2.1.0
		 *
		 */
		return apply_filters( 'cbxwpemaillogger_email_additional_content_' . $this->id, $this->format_string( $this->get_option( 'additional_content' ) ), $this->object, $this );
	}//end method get_additional_content

	/**
	 * Handle multipart mail.
	 *
	 * @param  PHPMailer  $mailer  PHPMailer object.
	 *
	 * @return PHPMailer
	 */
	public function handle_multipart( $mailer ) {
		/*if ( ! $this->sending ) {
			return $mailer;
		}*/

		if ( 'multipart' === $this->get_email_type() ) {
			$mailer->AltBody = wordwrap( // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				preg_replace( $this->plain_search, $this->plain_replace, wp_strip_all_tags( $this->get_content_plain() ) )
			);
		} else {
			$mailer->AltBody = ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		}

		//$this->sending = false;
		return $mailer;
	}//end method handle_multipart

	public function footer_text_format( $footer_text ) {
		return $this->format_string( $footer_text );
	}//end function footer_text_format

	/**
	 * Return if manual or not
	 *
	 * @return bool
	 * @since 2.0.0
	 */
	public function is_manual() {
		return $this->manual;
	}//end is_manual
}//end class ComfortSmtpEmail