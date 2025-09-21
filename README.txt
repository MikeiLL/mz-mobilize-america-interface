=== MZ Mobilize America Interface ===
Contributors: mikeill
Donate link: http://mzoo.org/
Tags: comments, spam
Requires at least: 5.3
Tested up to: 6.8.2
Stable tag: 1.0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simple interface for displaying Mobilize America Events from their API via Shortcode.

== Description ==

Simple interface for displaying events from Mobilize America's API via shortcode. Customize the display using template files in your theme.

 * This is _only an interface_ between your WP install and Mobilize America, provided free, with no guarantees.
 * Visit [Mobilize America](https://join.mobilize.us) more info on this galvanization tool.
 * Check Mobilize America's [Terms and Conditions](https://join.mobilize.us/terms-of-service) here.

== Installation ==

1. Upload `mz-mobilize-america.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Visit Admin->Settings->MZ Mobilize America for Shortcodes

== Frequently Asked Questions ==

= Why did you make this plugin? =

Because I like Mobilize America and there wasn't one.

== Changelog ==

= v1.0.4 =
* Update version and tested up to number.
* BUGFIX: bad data and non options set.

= v1.0.3 =
* Update version and tested up to number.

= v1.0.2 =
* Use wp_timezone() function requiring WP 5.3+

= v1.0.1 =
* Bugfix: Stop enqueueing unnecessary files (which were also wrongly named)
* Bugfix: Correct name of enqueued js file.
* Bugfix: To include associated organization events, still need _some_ query variables.
* Pull current time from WP settings instead of default timezone.

= v1.0 =
* Initial Release

== Upgrade Notice ==

= v1.0 =
* Initial Release
