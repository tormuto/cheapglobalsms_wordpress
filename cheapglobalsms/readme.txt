=== CheapGlobalSMS ===
Contributors: Tormuto
Tags: sms, two factor, security, mobile, texting, cheap sms, sms gateway,sms api
Requires at least: 4.6
Tested up to: 5.7.0
Stable tag: 1.0.0
Version: 1.0.0
License: MIT
License URI: https://opensource.org/licenses/MIT

Send SMS'es and enable SMS-based two-factor security.

== Description ==

This plugin enables you to send SMS'es straight from the WordPress back-end or via the programmers API.

Also included is free and really easy to use two-factor security, which really hardens the security of your site.

All you need, is the plugin and a free [CheapGlobalSMS.com](https://cheapglobalsms.com)-account.

Main features:

* **📱 Send SMS / text messages**
  * Import recipient lists from CSV/Excel.
  * Group recipients.
  * Bulk-sending.
  * Easy programmers API.

* **🔐 Two-factor security**
  * Easy on your users: No apps needed!
  * Easy on the admin: Tick a checkbox and it just works!
  * Military grade security!
  * Pick roles to enable mandatory two-factor.
  * Re-authorize at each login or remember devices for up to 30 days.


**Easy to get started:**

- Live chat support and mail support from CheapGlobalSMS.com.

**Backed by high quality, lowest pricing SMS-gateway:**

If you would prefer to disable the UI-features and do all the sending from code, then that's possible as well. For this purpose you can use the method `cgsms_send_sms` which accepts arguments for message, recipient(s), sender-text and type of SMS.


== Installation ==

This section describes how to install the plugin and get it working.

1. If you haven't already, then go to [CheapGlobalSMS.com](https://cheapglobalsms.com) and create a free account.
1. Install and activate the plugin.
1. Go to "Settings » CheapGlobalSMS Settings" and add a sub-account details from your CheapGlobalSMS.com account.
1. (Optional) Enable the sending UI and then go to "CheapGlobalSMS » Send SMS" and try to send an SMS to yourself, verifying that all is setup correctly.


== Frequently Asked Questions ==

= How well does this plugin handle 10.000+'s of recipients =

It works really well; simply send in batches of 3,000 recipients per request. 

= HELP! I'm administrator and I'm locked out of the two-factor system! =

If you don't have a backup of the "Emergency bypass URL" from the setup-screen, then you need to dig into the database to disable the two-factor system. Your host probably has a phpMyAdmin that you can use to access it.

Then find the `options`-table, by default `wp_options`. Search for the row where the `option_name` is `cgsms_security_enable`. Simply delete the row.


== How to use ==

= Most users: User Guide =

The user interfaces (which internally loads the cheapglobalsms widget) are quite intuitive and straightforward.

[See the widget demo and parameters](https://cheapglobalsms.com/widget)


= Advanced: Programmers API =

Send an SMS to one or multiple recipients by calling `cgsms_send_sms` with the following arguments

- $message (string) A string containing the message to be sent.
- $recipients (array|string) A single recipient or a list of recipients.
- $sender (string, *optional*) Sender text (11 chars or 15 digits)
- $destaddr (string, *optional*) Type of SMS - Can be MOBILE (regular SMS) or DISPLAY (shown immediately on phone and usually not stored, also knows as a Flash SMS)

Returns the CheapGlobalSMS.com message-ID on success and a WP_Error on failure.

The recipients-argument may consist of either:

- An integer or string, containing a phone number MSISDN (CC + number, digits only).

Multiple recipient's phone numbers can be separated by comma (,) e.g.
+2348091234567,447828383732,+14472829929
or separate by space, e.g:
2348091234567 447828383732 14472829929
