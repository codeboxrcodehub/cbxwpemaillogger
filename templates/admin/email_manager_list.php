<?php
//phpcs:ignoreFile  WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
/**
 * Provide a dashboard view for the plugin
 * This file is used to markup the public-facing aspects of the plugin.
 * @link       https://codeboxr.com
 * @since      2.0.0
 * @package    cbxwpemaillogger
 * @subpackage cbxwpemaillogger/templates/admin
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="section_header row">
    <div class="col-12 section_header_l">
        <h2><?php esc_html_e( 'Email notifications', 'cbxwpemaillogger' ); ?></h2>
        <p><?php esc_html_e( 'Here are the list of available email notifications. Please note that, few notification may sent from background without any setting based on the type of not.', 'cbxwpemaillogger' ); ?></p>
    </div>
    <!--                        <div class="col-6 section_header_r"></div>-->
</div>
<div id="email_manager_listing_wrapper">
    <h3><?php esc_html_e( 'Notification list', 'cbxwpemaillogger' ); ?></h3>
    <table class="table table-bordered table-striped table-hover" id="cbxwpemaillogger_email_items">
        <thead>
        <tr>
            <th><?php esc_html_e( 'Title', 'cbxwpemaillogger' ); ?></th>
            <th><?php esc_html_e( 'Type', 'cbxwpemaillogger' ); ?></th>
            <th><?php esc_html_e( 'Recipient(s)', 'cbxwpemaillogger' ); ?></th>
            <th><?php esc_html_e( 'Actions', 'cbxwpemaillogger' ); ?></th>
        </tr>
        </thead>
        <tbody>
		<?php
		$admin_url = admin_url( 'admin.php?page=comfortsmtp-emails' );

		$enabled_svg    = comfortsmtp_esc_svg( comfortsmtp_load_svg( 'icon_enabled', 'app' ) );
		$disabled_svg   = comfortsmtp_esc_svg( comfortsmtp_load_svg( 'icon_disabled', 'app' ) );

		foreach ( $emails as $email ):
			$id = $email->id;
			$title      = $email->title;
			$type       = $email->type;
			$settings   = $email->settings;
			$user_email = $email->is_user_email();

			$manual = $email->is_manual();

			if ( ! is_array( $settings ) ) {
				$settings = [];
			}

			$enabled    = isset( $settings['enabled'] ) ? $settings['enabled'] : '';
			$email_type = isset( $settings['email_type'] ) ? $settings['email_type'] : 'html';

			$status_title = ( $enabled === 'yes' ) ? esc_attr__( 'Enabled', 'cbxwpemaillogger' ) : esc_attr__( 'Disabled', 'cbxwpemaillogger' );

			$button_status_class = ( $enabled === 'yes' ) ? 'cbxwpemaillogger_email_status_enabled' : 'cbxwpemaillogger_email_status_disabled';
            $status_svg = ( $enabled === 'yes' ) ? $enabled_svg : $disabled_svg;


			if ( $manual ) {
				$button_status_class = 'cbxwpemaillogger_email_status_manual';
				$status_title        = esc_attr__( 'Manually Triggered', 'cbxwpemaillogger' );
                $status_svg          = $disabled_svg;
			}

			$recipient = $email->get_recipient();

			$action_url = add_query_arg( [ 'edit' => $id ], $admin_url );
			?>
            <tr>
                <td>
                    <span aria-label="<?php echo esc_attr( $status_title ); ?>" data-balloon-pos="up" class="button cbxwpemaillogger_email_status <?php echo esc_attr( $button_status_class ); ?> outline secondary icon icon-only">
                        <i class="cbx-icon">
                            <?php echo $status_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            ?>
                        </i>
                    </span>
					<?php echo esc_html( $title ); ?>
                </td>
                <td><?php echo esc_html( $email->get_content_type() ); ?></td>
                <td><?php echo ( $user_email ) ? esc_html__( 'System User/Guest', 'cbxwpemaillogger' ) : esc_html( $recipient ); ?></td>
                <td>
                    <?php if($type == 1): ?>
                    <a class="button primary icon icon-inline small" href="<?php echo esc_url( $action_url ); ?>">
                        <i class="cbx-icon cbx-icon-edit-white"></i>
                        <span class="button-label"><?php esc_html_e( 'Edit', 'cbxwpemaillogger' ); ?></span>
                    </a>
                    <?php endif; ?>
                </td>
            </tr>
		<?php endforeach; ?>
        </tbody>
    </table>
</div>