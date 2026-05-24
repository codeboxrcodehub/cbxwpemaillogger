<?php
/**
 * This template can be overridden by copying it to yourtheme/comfortsmtp/emails/generic_email_admin.php
 *
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'comfortsmtp_email_header', $email_heading, $email ); ?>
<div class="content">
    <?php echo wp_kses_post( '{email_body}' ); ?>
    <?php
    /**
     * Show user-defined additional content - this is set in each email's settings.
     */
    if ( $additional_content ) {
        echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
    }
    ?>
</div>
<?php
do_action( 'comfortsmtp_email_footer', $email );