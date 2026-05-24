<?php
//phpcs:ignoreFile  WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
/**
 * Email Footer
 *
 * This template can be overridden by copying it to yourtheme/cbxwpemaillogger/email_templates/tpl-clean/email-footer.php.
 *
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

					</td>
                </tr>

                <tr>
                    <td class="footer">
                        <p class="footer-text">
							<?php
							$footer_text = isset( $template_settings['footertext'] ) ? $template_settings['footertext'] : '';

								echo wp_kses_post(
									wpautop(
										wptexturize(
										/**
										 * Provides control over the email footer text used for most order emails.
										 *
										 * @param  string  $email_footer_text
										 *
										 * @since 4.0.0
										 *
										 */
											apply_filters( 'comfortsmtp_email_footer_text', $footer_text )
										)
									)
								);
								?>
                            <!-- <strong>YourBrand Inc.</strong><br>
                            123 Business Street, Suite 100<br>
                            City, State 12345, Country -->
                        </p>
<!--                        <p class="footer-text" style="margin-top: 20px;">
                            <a href="https://yoursite.com/unsubscribe" class="footer-link">Unsubscribe</a> ·
                            <a href="https://yoursite.com/privacy" class="footer-link">Privacy Policy</a> ·
                            <a href="https://yoursite.com/contact" class="footer-link">Contact Us</a>
                        </p>
                        <p class="footer-text" style="margin-top: 15px;">
                            © 2026 YourBrand. All rights reserved.
                        </p>-->
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>