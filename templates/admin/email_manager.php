<?php
/**
 * Provide a dashboard view for the plugin
 * This file is used to markup the public-facing aspects of the plugin.
 * @link       https://codeboxr.com
 * @since      2.0.0
 * @package    cbxwpemaillogger
 * @subpackage cbxwpemaillogger/templates/admin
 */

use Comfort\Crm\Smtp\Helpers\ComfortSmtpHelpers;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$cbxwpemaillogger_more_v_svg = comfortsmtp_esc_svg( comfortsmtp_load_svg( 'icon_more_v' ) );
?>
<div class="wrap cbx-chota cbxwpemaillogger-page-wrapper cbxwpemaillogger-email-manager-wrapper"
     id="cbxwpemaillogger-email-manager">
    <div class="container">
        <div class="row">
            <div class="col-12 mb-20">
                <h2></h2>
				<?php settings_errors(); ?>
				<?php do_action( 'cbxwpemaillogger_wpheading_wrap_before', 'email_manager' ); ?>
                <div class="wp-heading-wrap">
                    <div class="wp-heading-wrap-left pull-left">
						<?php do_action( 'cbxwpemaillogger_wpheading_wrap_left_before', 'email_manager' ); ?>
                        <h1 class="wp-heading-inline wp-heading-inline-cbxwpemaillogger">
							<?php esc_html_e( 'Comfort Smtp : Email Manager', 'cbxwpemaillogger' ); ?>
                        </h1>
						<?php do_action( 'cbxwpemaillogger_wpheading_wrap_left_before', 'email_manager' ); ?>
                    </div>
                    <div class="wp-heading-wrap-right pull-right">
						<?php do_action( 'cbxwpemaillogger_wpheading_wrap_right_before', 'email_manager' ); ?>
						<?php
						$cbxwpemaillogger_menus = ComfortSmtpHelpers::dashboard_menus();
						if ( sizeof( $cbxwpemaillogger_menus ) ):
							?>
                            <div class="button_actions button_actions-global-menu">
                                <details class="dropdown dropdown-menu ml-10">
                                    <summary class="button outline primary icon icon-only">
                                        <i class="cbx-icon">
											<?php echo $cbxwpemaillogger_more_v_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											?>
                                        </i>
                                        <span class="sr-only"><?php esc_html_e( 'Dashboard Menu', 'cbxwpemaillogger' ); ?></span>
                                    </summary>
                                    <div class="card card-menu card-menu-right">
                                        <ul id="dashboard_menus">
											<?php foreach ( $cbxwpemaillogger_menus as $cbxwpemaillogger_slug => $menu ): ?>
												<?php
												$title = $menu['title-attr'];
												$cbxwpemaillogger_label = $menu['title'];
												$cbxwpemaillogger_url   = $menu['url'];

												echo '<li><a class="button outline dashboard_menu dashboard_menu_' . esc_attr( $cbxwpemaillogger_slug ) . '" role="button" title="' . esc_attr( $title ) . '" href="' . esc_url( $cbxwpemaillogger_url ) . '">' . esc_html( $cbxwpemaillogger_label ) . '</a></li>';

												?>
											<?php endforeach; ?>
                                        </ul>
                                    </div>
                                </details>
                            </div>
						<?php endif; ?>
						<?php do_action( 'cbxwpemaillogger_wpheading_wrap_right_after', 'email_manager' ); ?>
                    </div>
                </div>
				<?php do_action( 'cbxwpemaillogger_wpheading_wrap_after', 'email_manager' ); ?>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-12">
				<?php do_action( 'cbxwpemaillogger_email_manager_before' ); ?>
                <div id="email_manager_wrapper">
					<?php do_action( 'cbxwpemaillogger_email_manager_start', 'email_manager' ); ?>
					<?php
					$cbxwpemaillogger_template_data = [ 'settings' => $settings ];
					if ( $edit ):
						$cbxwpemaillogger_template_data['email'] = $emails[ $id ];
						$cbxwpemaillogger_template_data['id']    = $id;

						echo comfortsmtp_get_template_html( 'admin/email_manager_edit.php', $cbxwpemaillogger_template_data );//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					else:
						$cbxwpemaillogger_template_data = [ 'emails' => $emails ];

						echo comfortsmtp_get_template_html( 'admin/email_manager_list.php', $cbxwpemaillogger_template_data );//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					endif;
					?>
					<?php do_action( 'cbxwpemaillogger_email_manager_end', 'email_manager' ); ?>
                </div>
				<?php do_action( 'cbxwpemaillogger_email_manager_after', 'email_manager' ); ?>
            </div>
        </div>
    </div>
</div>