<?php
/**
 * Email Styles
 *
 * This template can be overridden by copying it to yourtheme/cbxwpemaillogger/email_templates/tpl-general/email-styles.php.
 *
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
            background-color: #f5f5f5;
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
            background-color: #f5f5f5;
            padding: 40px 0;
            margin-top: 50px;
            margin-bottom: 50px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e7eb;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px 40px;
        }
        .header-table {
            width: 100%;
        }
        .logo-text {
            font-size: 28px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.5px;
            text-align: left;
        }
        .logo-image {
            width: 50px;
            height: 50px;
            border-radius: 0;
            font-size: 20px;
            color: #000000;
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
            font-size: 24px;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0 0 20px 0;
            line-height: 1.3;
        }
        .message {
            font-size: 16px;
            line-height: 1.6;
            color: #4a5568;
            margin: 0 0 30px 0;
        }
        .button-center {
            text-align: center;
            padding: 10px 0;
        }
        .button {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        .button-secondary {
            background: transparent;
            border: 2px solid #667eea;
            color: #667eea !important;
            margin-left: 10px;
            box-shadow: none;
        }
        .quote-section {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 25px 30px;
            margin: 30px 0;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        .quote-text {
            font-size: 18px;
            font-style: italic;
            color: #2d3748;
            margin: 0 0 10px 0;
            line-height: 1.6;
        }
        .quote-author {
            font-size: 14px;
            color: #667eea;
            font-weight: 600;
            margin: 0;
        }
        .data-table {
            width: 100%;
            margin: 30px 0;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            border-collapse: collapse;
        }
        .table-header {
            background-color: #667eea;
            color: #ffffff;
            font-weight: 600;
            padding: 15px;
            text-align: left;
            font-size: 14px;
            border: 1px solid #667eea;
        }
        .table-cell {
            padding: 15px;
            border: 1px solid #e5e7eb;
            font-size: 14px;
            color: #4a5568;
            background-color: #ffffff;
        }
        .table-row-alt {
            background-color: #f9fafb;
        }
        .table-row-alt .table-cell {
            background-color: #f9fafb;
        }
        .text-center-section {
            text-align: center;
            margin: 40px 0;
            padding: 30px;
            background-color: #f8f9fa;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }
        .centered-heading {
            font-size: 26px;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0 0 15px 0;
            line-height: 1.3;
        }
        .centered-text {
            font-size: 16px;
            line-height: 1.6;
            color: #4a5568;
            margin: 0;
        }
        .form-summary-section {
            background-color: #f0f4ff;
            border: 2px solid #667eea;
            border-radius: 12px;
            padding: 30px;
            margin: 30px 0;
        }
        .form-summary-heading {
            font-size: 20px;
            font-weight: 600;
            color: #667eea;
            margin: 0 0 20px 0;
            text-align: center;
        }
        .form-summary-table {
            width: 100%;
            margin: 20px 0;
        }
        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #4a5568;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 0;
        }
        .form-value {
            font-size: 15px;
            color: #1a1a1a;
            padding: 10px 0;
            border-bottom: 1px solid #d1d5db;
        }
        .form-summary-footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #d1d5db;
            font-size: 13px;
            color: #6b7280;
        }
        .footer {
            padding: 30px 40px;
            background-color: #fafafa;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer-text {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.5;
            margin: 0;
        }
        .footer-link {
            color: #667eea;
            text-decoration: none;
        }
        .preheader {
            display: none;
            font-size: 1px;
            color: #f5f5f5;
            line-height: 1px;
            max-height: 0px;
            max-width: 0px;
            opacity: 0;
            overflow: hidden;
        }

        /* Dark mode styles */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #0f172a !important;
            }
            .wrapper {
                background-color: #0f172a !important;
            }
            .container {
                background-color: #1e293b !important;
                border: 1px solid rgba(255, 255, 255, 0.1) !important;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3) !important;
            }
            .content {
                background-color: #1e293b !important;
            }
            .heading, .centered-heading {
                color: #f1f5f9 !important;
            }
            .message, .centered-text {
                color: #cbd5e1 !important;
            }
            .button-secondary {
                color: #8b9aee !important;
            }
            .quote-section {
                background-color: rgba(102, 126, 234, 0.1) !important;
                border: 1px solid rgba(102, 126, 234, 0.2) !important;
            }
            .quote-text {
                color: #e2e8f0 !important;
            }
            .quote-author {
                color: #8b9aee !important;
            }
            .data-table {
                border: 1px solid rgba(255, 255, 255, 0.1) !important;
            }
            .table-cell {
                color: #cbd5e1 !important;
                background-color: #1e293b !important;
                border: 1px solid rgba(255, 255, 255, 0.1) !important;
            }
            .table-row-alt .table-cell {
                background-color: #2d3748 !important;
            }
            .text-center-section {
                background-color: rgba(102, 126, 234, 0.05) !important;
                border: 1px solid rgba(102, 126, 234, 0.15) !important;
            }
            .form-summary-section {
                background-color: rgba(102, 126, 234, 0.08) !important;
                border: 2px solid rgba(102, 126, 234, 0.3) !important;
            }
            .form-summary-heading {
                color: #8b9aee !important;
            }
            .form-label {
                color: #94a3b8 !important;
            }
            .form-value {
                color: #e2e8f0 !important;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
            }
            .form-summary-footer {
                border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
                color: #94a3b8 !important;
            }
            .footer {
                background-color: #0f172a !important;
                border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
            }
            .footer-text {
                color: #64748b !important;
            }
            .footer-link {
                color: #8b9aee !important;
            }
            .preheader {
                color: #0f172a !important;
            }
        }

        @media only screen and (max-width: 600px) {
            .container {
                border-radius: 0;
            }
            .content {
                padding: 30px 20px;
            }
            .heading {
                font-size: 20px;
            }
            .logo-text {
                font-size: 22px;
            }
            .button {
                display: block;
                margin: 10px 0;
            }
            .button-secondary {
                margin-left: 0;
            }
            .table-header, .table-cell {
                padding: 10px;
                font-size: 13px;
            }
        }

<?php
