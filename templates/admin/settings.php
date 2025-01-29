<?php

use Comfort\Crm\Smtp\Helpers\ComfortSmtpHelpers;

/**
 * Provide a dashboard view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://codeboxr.com
 * @since      1.0.0
 *
 * @package    cbxwpemaillogger
 * @subpackage cbxwpemaillogger/templates/admin
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>
<?php
$plugin_url = ComfortSmtpHelpers::url_utmy( 'https://codeboxr.com/product/cbx-email-logger-for-wordpress/' );
$doc_url    = ComfortSmtpHelpers::url_utmy( 'https://codeboxr.com/product/cbx-email-logger-for-wordpress/' );
$more_v_svg = comfortsmtp_esc_svg( comfortsmtp_load_svg( 'icon_more_v' ) );
$save_svg   = comfortsmtp_esc_svg( comfortsmtp_load_svg( 'icon_save' ) );
?>
<div class="wrap cbx-chota cbxchota-setting-common cbx-page-wrapper comfortsmtp-page-wrapper comfortsmtp-setting-wrapper"
     id="comfortsmtp-setting">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2></h2>
				<?php
				settings_errors();
				?>
				<?php do_action( 'comfortsmtp_wpheading_wrap_before', 'settings' ); ?>
                <div class="wp-heading-wrap">
                    <div class="wp-heading-wrap-left pull-left">
						<?php do_action( 'comfortsmtp_wpheading_wrap_left_before', 'settings' ); ?>
                        <h1 class="wp-heading-inline wp-heading-inline-comfortsmtp">
							<?php esc_html_e( 'Comfort SMTP: Global Settings', 'cbxwpemaillogger' ); ?>
                        </h1>
						<?php do_action( 'comfortsmtp_wpheading_wrap_left_before', 'settings' ); ?>
                    </div>
                    <div class="wp-heading-wrap-right  pull-right">
						<?php do_action( 'comfortsmtp_wpheading_wrap_right_before', 'settings' ); ?>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=comfortsmtp_support' ) ); ?>"
                           class="button outline primary">
							<?php esc_html_e( 'Support & Docs', 'cbxwpemaillogger' ); ?>
                        </a>
                        <a href="#" id="save_settings"
                           class="button primary icon icon-inline icon-right mr-5">
                            <i class="cbx-icon">
								<?php echo $save_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>
                            </i>
                            <span class="button-label"><?php esc_html_e( 'Save Settings', 'cbxwpemaillogger' ); ?></span>
                        </a>
                        <div class="button_actions button_actions-global-menu">
                            <details class="dropdown dropdown-menu ml-10">
                                <summary class="button icon icon-only outline primary icon-inline"><i
                                            class="cbx-icon"><?php echo $more_v_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></i>
                                </summary>
                                <div class="card card-menu card-menu-right">
									<?php
									$menus = ComfortSmtpHelpers::dashboard_menus();
									?>
                                    <ul>
										<?php
										foreach ( $menus as $menu ) { ?>
                                            <li>
                                                <a href="<?php echo esc_url( $menu['url'] ); ?>" class="button outline"
                                                   role="button"
                                                   title="<?php echo esc_attr( $menu['title-attr'] ); ?>"><?php echo esc_attr( $menu['title'] ); ?>
                                                </a>
                                            </li>
											<?php
										}
										?>
                                    </ul>
                                </div>
                            </details>
                        </div>
						<?php do_action( 'comfortsmtp_wpheading_wrap_right_after', 'settings' ); ?>
                    </div>
                </div>
				<?php do_action( 'comfortsmtp_wpheading_wrap_after', 'settings' ); ?>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-12">
				<?php do_action( 'comfortsmtp_settings_form_before', 'settings' ); ?>
                <div class="postbox">
                    <div class="clear clearfix"></div>
                    <div class="inside setting-form-wrap">
						<?php do_action( 'comfortsmtp_settings_form_start', 'settings' ); ?>
						<?php
						$settings->show_navigation();
						$settings->show_forms();
						?>
						<?php do_action( 'comfortsmtp_settings_form_end', 'settings' ); ?>
                    </div>
                    <div class="clear clearfix"></div>
                </div>
				<?php do_action( 'comfortsmtp_settings_form_after', 'settings' ); ?>
            </div>
        </div>
    </div>
</div>