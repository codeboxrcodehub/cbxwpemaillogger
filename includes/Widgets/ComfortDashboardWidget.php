<?php

namespace Comfort\Crm\Smtp\Widgets;

use Comfort\Crm\Smtp\Helpers\ComfortSmtpHelpers;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Dashboard Widget Class
 */
class ComfortDashboardWidget {
	public function __construct() {
	}//end of construct

	public function dashboard_widget() {
		if ( ! current_user_can( 'comfortsmtp_settings_manage' ) ) {
			return;
		}

		$widget_option = get_option( 'comfortsmtp_dashboard_widget' );
		if ( ! is_array( $widget_option ) ) {
			$widget_option = [];
		}

		wp_add_dashboard_widget(
			'comfortsmtp_dashboard_widget',
			esc_html__( 'Comfort Email SMTP, Logger & Email Api: Latest Email Log', 'cbxwpemaillogger' ),
			[ $this, 'widget_display' ],
			[ $this, 'widget_configure' ]
		);

	}//end of dashboard_widget

	/**
	 * Widget display
	 */
	public function widget_display() {
		$options = get_option( 'comfortsmtp_dashboard_widget' );
		//$count   = isset( $options['count'] ) ? intval( $options['count'] ) : 20;
		$count = isset( $options['count'] ) ? intval( $options['count'] ) : 10;


		$logs = ComfortSmtpHelpers::getLogData( '', '', '', - 1, 'id', 'DESC', $count, 1 );
		?>
        <table class="widefat comfortsmtp_widefat">
            <thead>
            <tr>
                <th class="row-title"><?php esc_attr_e( 'Subject', 'cbxwpemaillogger' ); ?></th>
                <th><?php esc_attr_e( 'To', 'cbxwpemaillogger' ); ?></th>
                <th><?php esc_attr_e( 'Date', 'cbxwpemaillogger' ); ?></th>
            </tr>
            </thead>
            <tbody>
			<?php
			if ( sizeof( $logs ) > 0 ):
				$i = 0;
				foreach ( $logs as $log ) {
					$alternate_class = ( $i % 2 == 0 ) ? 'alternate' : '';
					$i ++;
					?>
                    <tr class="<?php echo esc_attr( $alternate_class ); ?>">
                        <td class="row-title"><label for="tablecell">
								<?php
								echo esc_attr( wp_unslash( $log['subject'] ) );
								?>
                            </label>
                        </td>
                        <td>
							<?php
							$email_data  = maybe_unserialize( $log['email_data'] );
							$headers_arr = isset( $email_data['headers_arr'] ) ? $email_data['headers_arr'] : [];
							$emails      = isset( $headers_arr['email_to'] ) ? $headers_arr['email_to'] : [];

							if ( is_array( $emails ) && sizeof( $emails ) > 0 ) {
								$formatted_emails = [];
								foreach ( $emails as $email ) {
									if ( $email['recipient_name'] != '' ) {
										$formatted_emails[] = $email['recipient_name'] . '(' . sanitize_email( $email['address'] ) . ')';
									} else {
										$formatted_emails[] = sanitize_email( $email['address'] );
									}
								}

								echo implode( ',', $formatted_emails ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}


							?>
                        </td>
                        <td><?php
							$date_created = '';
							if ( $log['date_created'] != '' ) {
								//$date_created = ComfortSmtpHelpers::DateReadableFormat( wp_unslash( $log['date_created'] ), 'M j, Y g:i a' );
								$date_created = '<a href="' . admin_url( 'admin.php?page=comfortsmtp_log#/log/' . absint($log['id']) ) . '">' . esc_html($log['formatted_created_at']) . '</a>';
							}

							echo $date_created; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
                        </td>
                    </tr>
					<?php
				}
			else:
				echo '<tr><td colspan="3" style="text-align: center; font-weight: bold; font-size: 14px;">' . esc_html__( 'No email log found', 'cbxwpemaillogger' ) . '</td></tr>';

			endif;

			?>


            </tbody>
            <tfoot>
            <tr>
                <th class="row-title"><?php esc_attr_e( 'Subject', 'cbxwpemaillogger' ); ?></th>
                <th><?php esc_attr_e( 'To', 'cbxwpemaillogger' ); ?></th>
                <th><?php esc_attr_e( 'Date', 'cbxwpemaillogger' ); ?></th>
            </tr>
            </tfoot>
        </table>
        <p class="comfortsmtp_dashboard_more">
            <a class="button button-primary button-small" style="float: left; display: inline-block;"
               href="<?php echo esc_url( admin_url( 'admin.php?page=comfortsmtp_log' ) ) ?>"><?php esc_html_e( 'View All', 'cbxwpemaillogger' ); ?></a>
			<?php
            if ( ! defined( 'COMFORTSMTPPROADDON_PLUGIN_NAME' ) ) {
				?>
                <a class="button button-secondary button-small" style="float: right; display: inline-block;"
                   target="_blank"
                   href="https://codeboxr.com/product/cbx-email-logger-for-wordpress/"><?php esc_html_e( 'Try Pro', 'cbxwpemaillogger' ); ?></a>
				<?php
			}
			?>
        </p>
        <div class="clear clearfix"></div>
        <style>
            #comfortsmtp_dashboard_widget {

            }

            #comfortsmtp_dashboard_widget div.inside {
                padding-left: 0;
                padding-right: 0;
            }

            .comfortsmtp_widefat {
                font-size: 12px;
                line-height: 1.2;
            }

            .comfortsmtp_dashboard_more {
                margin: 10px;
                text-align: right;
            }

            .comfortsmtp_dashboard_more a, #comfortsmtp_dashboard_widget a {
                color: #6648fe !important;
            }

            .comfortsmtp_widefat thead, .comfortsmtp_widefat tfoot {
                background-color: #6648fe !important;
                color: #fff !important;
            }

            .comfortsmtp_widefat thead tr th, .comfortsmtp_widefat thead tr td, .comfortsmtp_widefat tfoot tr th, .comfortsmtp_widefat tfoot tr td {
                font-size: 10px !important;
                line-height: 1.2;
                color: #fff !important;
            }

            .comfortsmtp_widefat td, .comfortsmtp_widefat td p, .comfortsmtp_widefat td ol, .comfortsmtp_widefat td ul {
                font-size: 10px !important;
                line-height: 1.2;
            }

            #comfortsmtp_dashboard_widget .button-primary {
                border: 1px solid #6648fe !important;
                color: #fff !important;
                background-color: #6648fe !important;
            }

            #comfortsmtp_dashboard_widget .button-secondary {
                border: 1px solid #6648fe !important;
                background: #fff !important;
                color: #6648fe !important;
            }
        </style>
		<?php
	}//end of method widget_display

	/**
	 * Configure form of widget
	 */
	public function widget_configure() {
		$options = get_option( 'comfortsmtp_dashboard_widget' );

		if ( ! is_array( $options ) ) {
			$options = [];
		}

        //phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['submit'] ) ) {
			$options['count'] = isset( $_POST['count'] ) ? absint( $_POST['count'] ) : 20;  //phpcs:ignore WordPress.Security.NonceVerification.Missing

			update_option( 'comfortsmtp_dashboard_widget', $options );
		}

		$count = isset( $options['count'] ) ? absint( $options['count'] ) : 20;

		?>
        <p>
            <label for="comfortsmtp_count"><?php echo esc_html__( 'Number of items:', 'cbxwpemaillogger' ) ?></label>
            <input type="text" autocomplete="off" class="comfortsmtp_count" name="count" id="comfortsmtp_count"
                   value="<?php echo intval( $count ); ?>">

        </p>
		<?php
	}// end of method  widget_configure
}//end class CBXWPEmailLoggerDashboardWidget