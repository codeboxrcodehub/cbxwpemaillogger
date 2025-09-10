=== Comfort Email SMTP, Logger & Email Api ===
Contributors: codeboxr, manchumahara
Tags: wordpress smtp,wordpress email log,smtp
Requires at least: 5.3
Requires PHP: 8.2
Tested up to: 6.8
Stable tag: 2.0.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin helps to send email using SMTP and Email Api. It helps to log email and displays in admin panel and more.

== Description ==

Sending email to user's inbox is a great challenge now days as if you don't take extra measure your email may go to spam folder. Comfort Email SMTP, Logger & Email Api plugin comes to help on this and fix your email sending problem. This plugin helps to send email using SMTP and log any email sent from WordPress.


### Comfort Email SMTP, Logger & Email Api by [Codeboxr](https://codeboxr.com/product/cbx-email-logger-for-wordpress)

>📺 [Live Demo](https://www.youtube.com/watch?v=mx3Aw0-UVew) | 🌟 [Upgrade to PRO](https://codeboxr.com/product/cbx-email-logger-for-wordpress/) | 📋 [Documentation](https://codeboxr.com/product/cbx-email-logger-for-wordpress/) | 👨‍💻 [Free Support](https://wordpress.org/plugins/cbxwpemaillogger/) | 🤴 [Pro Support](https://codeboxr.com/contact-us) | 📱 [Contact](https://codeboxr.com/contact-us/)

[youtube https://www.youtube.com/watch?v=mx3Aw0-UVew]


### 🛄 Core Plugin Features ###

-  Email Log Manager
-  Email SMTP Manager
-  Email SMTP API(Pro) - New in V2.0.5


**📧 Email Log Features:**

* Default enabled on plugin activation
* Logs every email sent
* Logs email send success or fail(Bullet proof way to detect email send or not)
* Delete all email logs or single
* View Email Log
* View Email Preview
* ReSend email from the list window
* Delete X Days old logs from Log listing
* Auto delete X Days old logs using wordpress native event schedule
* Custom Setting panel
* Delete custom options created by this plugin and email logs on uninstall(it's not deactivate, uninstall means delete plugin)
* Save email attachments if enabled, default disabled
* Email sending error tracking - (New in version 1.0.4)
* Track email sent by popular plugin(started with Contact form 7 support) - (New in version 1.0.4)


**📤 Email SMTP Features:**

* Fresh New feature For SMTP (New in version 1.0.4)
* Default disabled on plugin activation
* Enable/disable override from Name
* Enable/disable override from Email
* Override wordpress default email to send via SMTP
* Full SMTP feature implementations
* SMTP config store and choose as need


**📤 Email SMTP Api(pro):**

* Mailgun from V2.0.5
* Mailjet from V2.0.5
* MailTrap from V2.0.5
* Postmark from V2.0.5
* Sendgrid from V2.0.5
* Brevo(Sendinblue) from V2.0.5


### 🛄 Comfort Email SMTP, Logger & Email Api Pro Addon Features ###

**📤 General Extended Features :**

* Unlimited SMTP server option

**📤 Popular Plugin(s) Tracking :**

* Track WPForms Email sending
* Track WooCommerce Email sending
* Track Easy Digital Downloads email sending
* More coming soon, contact us for integration for your plugin.

👉 Get the [pro addon](https://codeboxr.com/product/cbx-email-logger-for-wordpress/)


### 📋 Documentation and 🦸‍♂️Support ###

- For documentation and tutorials go to our [Documentation](https://codeboxr.com/product/cbx-email-logger-for-wordpress/)
- If you have any more questions, visit our [support](https://codeboxr.com/contact-us/)
- For more information about features, FAQs and documentation, check out our website at [Codeboxr](https://codeboxr.com)

### 👍 Liked Codeboxr? ###

- Join our [Facebook Page](https://www.facebook.com/codeboxr//)
- Learn from our tutorials on [Youtube Channel](https://www.youtube.com/user/codeboxr)
- Or [rate us](https://wordpress.org/support/plugin/cbxwpemaillogger/reviews/#new-post) on WordPress


== Installation ==

This section describes how to install the plugin and get it working.

> this plugins add an extra header to email to tracking email sent success or not. The custom header added in email is in format
  'x-cbxwpemaillogger-id: $log_id'

e.g.

1. Upload `cbxwpemaillogger` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. You can search from wordpress plugin manager by keyword "cbxwpemaillogger" and install from live

== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==
= 2.0.9 =
* [fixed] Fixed method callback for 'custom_robots_txt'

= 2.0.8 =
* [updated] PHP version compatibility check
* [new] Pro addon V1.0.6 released

= 2.0.7 =
* [updated] WordPress Core V6.8 compatible
* [new] Pro addon V1.0.5 released

= 2.0.6 =
* [updated] Miscellaneous changes and improvement(+ Some methods moved to helper class)
* [new] Pro addon V1.0.4 released
* [updated] WordPress Core V6.7.2 compatible
* [updated] Language file is now loaded using 'init'  hook

= 2.0.5 =
* [updated] Miscellaneous changes and improvement
* [new] Pro addon V1.0.3 released (Mail api support added)

= 2.0.4 =
* [improvement] Optimization to create the installable size smaller
* [fixed] On log delete attachment delete fixed
* [improvement] Style improvement, color picker css file missing fixed

= 2.0.3 =
* [improvement] Upgrade system improved
* [fixed] Fixed corrupted svg image
* [fixed] Error fixed if tables were created using old method dbdelta or non migration way

= 2.0.2 =
* [improvement] Latest WordPress 6.7.1 compatible
* [improvement] PHP 8.2 support
* [improvement] Improved branding
* [improvement] More robust dashboard
* [new] Plugin update checking added

= 2.0.1 =
* [improvement] Latest WordPress 6.3.1 compatible
* [improvement] PHP 8.x support
* [improvement] Setting page first time render fixed
* [improvement] Restructure of hooks loading

= 2.0.0 =
* [new] Test email sending now has attachment option to test attachment sending
* [improvement] Attachments displays as icon in log listing

= 1.0.11 =
* [improvement] Email sending now more easy with default email, subject and body message
* [improvement] Setting page ui improvements
* [updated] Help & setting page header issue fixed (on scroll)

= 1.0.10 =

* [improvement] Plugin setting and other pages ui improvements (New Look)
* [new] Add new menu helps & update
* [updated] Plugin's compatibility with latest wp version


= 1.0.9 =

* [new] Add email source filter in log listing
* [new] How to video tutorial added

= 1.0.8 =

* [updated] Minor improvement

= 1.0.7 =

* [new] Multi range date picker in email log listing
* [fixed] Wrong admin url for log listing from some screens
* [improved] Some improvement in admin ui


= 1.0.6 =

* [security] Dashboard widget is now hidden from non admin users

= 1.0.5 =

* [Improvement] More polished plugin interface
* [New] Dashboard widget to see recent email logs
* [New] Test Email Sending with all possible parameters

= 1.0.4 =

* [New] Plugin started as logger but now it also helps to send email using smtp
* [New] Custom multiple SMTP server config
* [New] Track email send failure, logs error message

= 1.0.3 =

* [New] Custom SMTP
* [New] Email attachment store/save
* [Fix] Email resend now maintain same email content type
* [New] Track Email source of very popular common plugin, Now supports only Contact form 7

= 1.0.2 =

* Added option panel
* Delete X Days old logs from Log listing
* Auto delete X Days old logs using wordpress native event schedule
* Custom Setting panel
* Delete custom options created by this plugin and email logs on uninstall(it's not deactivate, uninstall means delete plugin)

= 1.0.1 =

* View Email Log
* View Email Template in Popup
* View Email log template in single view display
* Single click resend email

= 1.0.0 =

* First public release