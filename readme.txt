=== Protocol Enforcer ===
Contributors: KCPT Fastmover jirrodglynn
Donate link: http://KCPT.org/
Tags: http, https
Requires at least: 4.1
Tested up to: 4.5.2
Stable tag: 0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin allows a user to specify a post to only be viewed over HTTP or HTTPS.

== Description ==

A simple drop down selection meta box on a post edit page will allow you to specify default, HTTP or HTTPS.  Default will allow both HTTP and HTTPS protocols.

== Installation ==

1. Unzip files.
2. Upload the folder into your plugins directory.
3. Activate the plugin.

== Screenshots ==

1. Picture of the widget just after it was added.  It is empty and not filled out yet.
2. The widget filled out.

== Changelog ==

= 0.1 =
* Added metabox with dropdown to select: default, http, https
* Added logic in template_redirect hook to check this meta field for intended protocol to be used and redirect if needed.
