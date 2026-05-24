<?php
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
<?php
$cbxwpemaillogger_list_url = admin_url( 'admin.php?page=comfortsmtp-emails' );
?>
<div class="section_header row">
    <div class="col-12 section_header_l">
        <h2>
			<?php esc_html_e( 'Edit notifications', 'cbxwpemaillogger' ); ?>
        </h2>
    </div>
    <!--                        <div class="col-6 section_header_r"></div>-->
</div>
<div id="email_manager_listing_wrapper">
	<?php
	$cbxwpemaillogger_settings    = $email->settings;
	$cbxwpemaillogger_form_fields = $email->form_fields;

	?>

    <div class="cbx-sub-heading-wrap mb-20" id="dashlisting_toolbar">
        <div class="cbx-sub-heading-l">
            <h2 class="cbx-sub-heading">
				<?php
				/* translators:translators: %s: Email title */
				echo sprintf( esc_html__( 'Notification Name: %s', 'cbxwpemaillogger' ), esc_html( $email->title ) );
				?>
            </h2>
        </div>
        <div class="cbx-sub-heading-r">
            <a class="button outline secondary"
               href="<?php echo esc_url( $cbxwpemaillogger_list_url ); ?>"><?php esc_html_e( 'Back to list', 'cbxwpemaillogger' ); ?></a>
            <a class="button primary" id="save_email"
               href="#"><?php esc_html_e( 'Save', 'cbxwpemaillogger' ); ?></a>
        </div>
    </div>
	<?php echo wpautop( wp_kses_post( $email->get_description() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

    <form class="global_setting_group" id="comfortsmtp_email_edit_form" method="post" action="" enctype="multipart/form-data">
        <input type="hidden" name="email_id" value="<?php echo esc_attr( $id ); ?>"/>
		<?php wp_nonce_field( 'comfortsmtp_email_edit_' . esc_attr( $id ) ); ?>
        <table class="table table-bordered table-striped table-hover">
            <thead>
            <tr>
                <th><?php esc_html_e( 'Label', 'cbxwpemaillogger' ); ?></th>
                <th><?php esc_html_e( 'Field', 'cbxwpemaillogger' ); ?></th>
            </tr>
            </thead>
            <tbody>
			<?php
			foreach ( $cbxwpemaillogger_form_fields as $cbxwpemaillogger_field_key => $cbxwpemaillogger_form_field ) {
				$type        = $cbxwpemaillogger_form_field['type'];
				$title       = $cbxwpemaillogger_form_field['title'];
				$cbxwpemaillogger_label       = isset( $cbxwpemaillogger_form_field['label'] ) ? $cbxwpemaillogger_form_field['label'] : '';
				$cbxwpemaillogger_default     = isset( $cbxwpemaillogger_form_field['default'] ) ? $cbxwpemaillogger_form_field['default'] : '';
				$cbxwpemaillogger_description = isset( $cbxwpemaillogger_form_field['description'] ) ? wp_specialchars_decode( $cbxwpemaillogger_form_field['description'], ENT_QUOTES ) : '';
				$cbxwpemaillogger_desc_tip    = isset( $cbxwpemaillogger_form_field['desc_tip'] ) ? absint( $cbxwpemaillogger_form_field['desc_tip'] ) : 0;
				$cbxwpemaillogger_placeholder = isset( $cbxwpemaillogger_form_field['placeholder'] ) ? $cbxwpemaillogger_form_field['placeholder'] : '';
				$cbxwpemaillogger_options     = isset( $cbxwpemaillogger_form_field['options'] ) ? $cbxwpemaillogger_form_field['options'] : [];
				$cbxwpemaillogger_class       = isset( $cbxwpemaillogger_form_field['class'] ) ? $cbxwpemaillogger_form_field['class'] : '';
				$cbxwpemaillogger_css         = isset( $cbxwpemaillogger_form_field['css'] ) ? $cbxwpemaillogger_form_field['css'] : '';
				$cbxwpemaillogger_value       = isset( $cbxwpemaillogger_settings[ $cbxwpemaillogger_field_key ] ) ? $cbxwpemaillogger_settings[ $cbxwpemaillogger_field_key ] : '';
				?>
                <tr>
                    <td><?php echo esc_html( $title ); ?></td>
                    <td>
						<?php
						if ( $type == 'checkbox' ) {
							echo '<div class="comfortsmtp_email_edit_field checkbox_field form-group d-flex">';
							echo '<input name="' . esc_attr( $cbxwpemaillogger_field_key ) . '" type="hidden" value="no" />';
							echo '<input name="' . esc_attr( $cbxwpemaillogger_field_key ) . '" class="magic-checkbox" id="comfortsmtp_email_edit_' . esc_attr( $cbxwpemaillogger_field_key ) . '" type="checkbox" ' . checked( 'yes', $cbxwpemaillogger_value, false ) . ' value="' . esc_attr( $cbxwpemaillogger_default ) . '" />';
							echo '<label for="comfortsmtp_email_edit_' . esc_attr( $cbxwpemaillogger_field_key ) . '">' . esc_html( $cbxwpemaillogger_label ) . '</label>';
							echo '<p class="description" >' . $cbxwpemaillogger_description . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo '</div>';
						} elseif ( $type == 'text' ) {
							echo '<div class="comfortsmtp_email_edit_field text_field form-group">';
							//echo '<label for="comfortsmtp_email_edit_' . esc_attr( $cbxwpemaillogger_field_key ) . '">' . esc_html( $title ) . '</label>';
							echo '<input placeholder="' . esc_attr( $cbxwpemaillogger_placeholder ) . '" name="' . esc_attr( $cbxwpemaillogger_field_key ) . '" class="" id="comfortsmtp_email_edit_' . esc_attr( $cbxwpemaillogger_field_key ) . '" type="text"  value="' . esc_attr( $cbxwpemaillogger_value ) . '" />';
							echo '<p class="description" >' . $cbxwpemaillogger_description . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo '</div>';
						} elseif ( $type == 'textarea' ) {
							echo '<div class="comfortsmtp_email_edit_field textarea_field form-group">';
							//echo '<label for="comfortsmtp_email_edit_' . esc_attr( $cbxwpemaillogger_field_key ) . '">' . esc_html( $title ) . '</label>';
							echo '<textarea placeholder="' . esc_attr( $cbxwpemaillogger_placeholder ) . '" name="' . esc_attr( $cbxwpemaillogger_field_key ) . '" class="" id="comfortsmtp_email_edit_' . esc_attr( $cbxwpemaillogger_field_key ) . '" >' . esc_html( $cbxwpemaillogger_value ) . '</textarea>';
							echo '<p class="description">' . $cbxwpemaillogger_description . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo '</div>';
						} elseif ( $type == 'select' ) {
							echo '<div class="comfortsmtp_email_edit_field select_field form-group">';
							//echo '<label for="comfortsmtp_email_edit_' . esc_attr( $cbxwpemaillogger_field_key ) . '">' . esc_html( $title ) . '</label>';
							echo '<select placeholder="' . esc_attr( $cbxwpemaillogger_placeholder ) . '" name="' . esc_attr( $cbxwpemaillogger_field_key ) . '" class="" id="comfortsmtp_email_edit_' . esc_attr( $cbxwpemaillogger_field_key ) . '">';
							foreach ( $cbxwpemaillogger_options as $cbxwpemaillogger_option_key => $cbxwpemaillogger_option_value ) {
								echo '<option ' . selected( $cbxwpemaillogger_option_key, $cbxwpemaillogger_value, false ) . ' value="' . esc_attr( $cbxwpemaillogger_option_key ) . '">' . esc_html( $cbxwpemaillogger_option_value ) . '</option>';
							}
							echo '</select>';
							echo '<p class="description">' . $cbxwpemaillogger_description . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo '</div>';
						}

						?>
                    </td>
                </tr>
			<?php } ?>
            </tbody>
        </table>
        <input type="hidden" name="comfortsmtp_email_edit" value="1" />
        <p class="button_actions">
            <button class="button primary" name="comfortsmtp_email_edit_submit"  type="submit"><?php esc_html_e( 'Save Changes', 'cbxwpemaillogger' ); ?></button>
        </p>
    </form>
</div>
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("save_email").addEventListener("click", function () {
            document.getElementById("comfortsmtp_email_edit_form").submit();
        });
    });
</script>