<?php
//phpcs:ignoreFile  WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
/**
 * Email Header
 *
 * This template can be overridden by copying it to yourtheme/cbxwpemaillogger/email_templates/tpl-general/email-header.php.
 *
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="color-scheme" content="light dark"/>
    <meta name="supported-color-schemes" content="light dark"/>
    <title><?php echo get_bloginfo( 'name', 'display' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?></title>
</head>
<body>
    <!-- Preheader text for better inbox preview -->
    <div class="preheader">
        Important notification: Review your account summary and recent updates
    </div>

    <table role="presentation" class="wrapper" width="100%" cellpadding="0" cellspacing="0" >
        <tr>
            <td align="center">
                <table role="presentation" class="container" width="600" cellpadding="0" cellspacing="0">
                    <!-- Header with Logo and Brand Name -->
                    <tr>
                        <td class="header">
                            <table role="presentation" class="header-table" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="width: 70%; vertical-align: middle;">
                                        <div class="logo-text"><?php echo get_bloginfo( 'name', 'display' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?></div>
                                    </td>
                                    <td style="width: 30%; text-align: right; vertical-align: middle;">
                                        <?php
                                        $logo_img = isset( $template_settings['headerimage'] ) ? $template_settings['headerimage'] : '';

                                        if ( $logo_img ) {
                                            echo '<img style="display:inline-block; max-width: 50px; height: auto;" src="' . esc_url( $logo_img ) . '" alt="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" />'; // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
                                        }
                                        ?>
                                        <!-- Replace with: <img src="https://yoursite.com/logo.png" alt="YourBrand Logo" width="50" height="50" style="border-radius: 10px;"> -->
                                        <!-- <div class="logo-image">YB</div> -->
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Content Section 1 -->
                    <tr>
                        <td class="content">