<?php
/**
 * Email Styles
 *
 * This template can be overridden by copying it to yourtheme/cbxwpemaillogger/email_templates/tpl-clean/email-styles.php.
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// !important; is a gmail hack to prevent styles being stripped if it doesn't like something.
// body{padding: 0;} ensures proper scale/positioning of the email in the iOS native email app.
?>
    :root {
        color-scheme: light dark;
        supported-color-schemes: light dark;
    }

    body {
        margin: 0;
        padding: 0;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        background-color: #ffffff;
    }
    table {
        border-spacing: 0;
        border-collapse: collapse;
    }
    img {
        border: 0;
        display: block;
    }
    .wrapper {
        width: 100%;
        background-color: #ffffff;
        padding: 0;
        margin-top: 50px;
        margin-bottom: 50px;
    }
    .container {
        max-width: 600px;
        margin: 0 auto;
        background-color: #ffffff;
    }
    .header {
        padding: 40px 40px 30px 40px;
        border-bottom: 3px solid #000000;
    }
    .header-table {
        width: 100%;
    }
    .logo-text {
        font-size: 28px;
        font-weight: 700;
        color: #000000;
        letter-spacing: -0.5px;
        text-align: left;
    }
    .logo-image {
        width: 50px;
        height: 50px;
        background-color: #000000;
        border-radius: 0;
        font-size: 20px;
        color: #ffffff;
        font-weight: 700;
        text-align: center;
        line-height: 50px;
    }
    .content {
        padding: 50px 40px;
    }
    .content-section {
        margin-bottom: 40px;
    }
    .heading {
        font-size: 28px;
        font-weight: 700;
        color: #000000;
        margin: 0 0 20px 0;
        line-height: 1.3;
        letter-spacing: -0.5px;
    }
    .message {
        font-size: 16px;
        line-height: 1.8;
        color: #333333;
        margin: 0 0 20px 0;
    }
    .button-center {
        text-align: center;
        padding: 20px 0;
    }
    .button {
        display: inline-block;
        padding: 14px 40px;
        background-color: #000000;
        color: #ffffff;
        text-decoration: none;
        border-radius: 0;
        font-weight: 600;
        font-size: 14px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }
    .button-secondary {
        background-color: #ffffff;
        border: 2px solid #000000;
        color: #000000;
        margin-left: 15px;
    }
    .quote-section {
        background-color: #f8f8f8;
        border-left: 4px solid #000000;
        padding: 30px 35px;
        margin: 40px 0;
    }
    .quote-text {
        font-size: 18px;
        font-style: italic;
        color: #1a1a1a;
        margin: 0 0 12px 0;
        line-height: 1.7;
    }
    .quote-author {
        font-size: 14px;
        color: #666666;
        font-weight: 600;
        margin: 0;
        font-style: normal;
    }
    .divider {
        width: 80px;
        height: 3px;
        background-color: #000000;
        margin: 35px 0;
    }
    .data-table {
        width: 100%;
        margin: 30px 0;
        border: 2px solid #000000;
        border-collapse: collapse;
    }
    .table-header {
        background-color: #000000;
        color: #ffffff;
        font-weight: 700;
        padding: 16px;
        text-align: left;
        font-size: 13px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        border: 2px solid #000000;
    }
    .table-cell {
        padding: 16px;
        border: 1px solid #e0e0e0;
        font-size: 15px;
        color: #333333;
        background-color: #ffffff;
    }
    .table-row-alt .table-cell {
        background-color: #fafafa;
    }
    .text-center-section {
        text-align: center;
        margin: 50px 0;
        padding: 0;
    }
    .centered-heading {
        font-size: 24px;
        font-weight: 700;
        color: #000000;
        margin: 0 0 15px 0;
        line-height: 1.3;
        letter-spacing: -0.3px;
    }
    .centered-text {
        font-size: 16px;
        line-height: 1.8;
        color: #555555;
        margin: 0;
    }
    .form-summary-section {
        background-color: #fafafa;
        border: 2px solid #e0e0e0;
        padding: 35px;
        margin: 40px 0;
    }
    .form-summary-heading {
        font-size: 20px;
        font-weight: 700;
        color: #000000;
        margin: 0 0 25px 0;
        text-align: center;
        letter-spacing: -0.3px;
    }
    .form-summary-table {
        width: 100%;
        margin: 20px 0;
    }
    .form-label {
        font-size: 12px;
        font-weight: 700;
        color: #666666;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        padding: 12px 0;
    }
    .form-value {
        font-size: 15px;
        color: #000000;
        padding: 12px 0;
        border-bottom: 1px solid #e0e0e0;
    }
    .form-summary-footer {
        text-align: center;
        margin-top: 25px;
        padding-top: 25px;
        border-top: 2px solid #e0e0e0;
        font-size: 13px;
        color: #888888;
    }
    .footer {
        padding: 40px 40px 50px 40px;
        background-color: #fafafa;
        text-align: center;
        border-top: 3px solid #000000;
    }
    .footer-text {
        font-size: 13px;
        color: #666666;
        line-height: 1.8;
        margin: 0;
    }
    .footer-link {
        color: #000000;
        text-decoration: none;
        font-weight: 600;
    }
    .preheader {
        display: none;
        font-size: 1px;
        color: #ffffff;
        line-height: 1px;
        max-height: 0px;
        max-width: 0px;
        opacity: 0;
        overflow: hidden;
    }

    /* Dark Mode Styles */
    @media (prefers-color-scheme: dark) {
        body {
            background-color: #1a1a1a !important;
        }
        .wrapper {
            background-color: #1a1a1a !important;
        }
        .container {
            background-color: #1a1a1a !important;
        }
        .header {
            border-bottom-color: #ffffff !important;
        }
        .logo-text {
            color: #ffffff !important;
        }
        .logo-image {
            background-color: #ffffff !important;
            color: #000000 !important;
        }
        .heading {
            color: #ffffff !important;
        }
        .message {
            color: #cccccc !important;
        }
        .button {
            background-color: #ffffff !important;
            color: #000000 !important;
        }
        .button-secondary {
            background-color: #1a1a1a !important;
            border-color: #ffffff !important;
            color: #ffffff !important;
        }
        .quote-section {
            background-color: #2a2a2a !important;
            border-left-color: #ffffff !important;
        }
        .quote-text {
            color: #e0e0e0 !important;
        }
        .quote-author {
            color: #999999 !important;
        }
        .divider {
            background-color: #ffffff !important;
        }
        .data-table {
            border-color: #ffffff !important;
        }
        .table-header {
            background-color: #ffffff !important;
            color: #000000 !important;
            border-color: #ffffff !important;
        }
        .table-cell {
            border-color: #444444 !important;
            color: #cccccc !important;
            background-color: #1a1a1a !important;
        }
        .table-row-alt .table-cell {
            background-color: #252525 !important;
        }
        .centered-heading {
            color: #ffffff !important;
        }
        .centered-text {
            color: #aaaaaa !important;
        }
        .form-summary-section {
            background-color: #2a2a2a !important;
            border-color: #444444 !important;
        }
        .form-summary-heading {
            color: #ffffff !important;
        }
        .form-label {
            color: #999999 !important;
        }
        .form-value {
            color: #ffffff !important;
            border-bottom-color: #444444 !important;
        }
        .form-summary-footer {
            border-top-color: #444444 !important;
            color: #888888 !important;
        }
        .footer {
            background-color: #2a2a2a !important;
            border-top-color: #ffffff !important;
        }
        .footer-text {
            color: #999999 !important;
        }
        .footer-link {
            color: #ffffff !important;
        }
    }

    @media only screen and (max-width: 600px) {
        .content {
            padding: 30px 20px;
        }
        .header {
            padding: 30px 20px 20px 20px;
        }
        .footer {
            padding: 30px 20px 40px 20px;
        }
        .heading {
            font-size: 22px;
        }
        .logo-text {
            font-size: 22px;
        }
        .button {
            display: block;
            margin: 10px 0;
            text-align: center;
        }
        .button-secondary {
            margin-left: 0;
        }
        .table-header, .table-cell {
            padding: 12px;
            font-size: 13px;
        }
        .form-summary-section {
            padding: 25px 20px;
        }
    }


<?php
