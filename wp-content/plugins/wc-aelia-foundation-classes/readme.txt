=== Aelia Foundation Classes for WooCommerce ===
Contributors: daigo75, aelia
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=F8ND89AA8B8QJ
Tags: woocommerce, utility
Requires at least: 3.6
Tested up to: 4.2
Stable tag: 1.6.6.150825
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Adds a set of convenience classes that can simplify the development of other plugins for WooCommerce.

== Description ==
The Aelia Foundation Classes add several classes that can simplify the development of plugins for WooCommerce. Some of the available classes are listed below.

**Namespace `Aelia\WC`**

* `IP2Location`. Implements methods to determine visitor's country. Library relies on MaxMind GeoLite2 library.
* `Order`. An extended Order class, which includes methods to retrieve attributes of orders generated in multi-currency setups.
* `Settings`. Allows to manage the settings of a plugin. The class does not rely on WooCommerce Settings API.
* `Settings_Renderer`. Allows to render the settings interface for a plugin. The can automatically render a tabbed interface, using jQuery UI.
* `Logger`. A convenience Logger class.
* `Aelia_Plugin`. A base plugin class, which other plugins can extend. The class implements convenience methods to access plugin settings, WooCommerce settings, common paths and URLs, and automatically load CSS and JavaScript files when needed.
* `Semaphore`. Implements a simple semaphore logic, which can be used to prevent race conditions in operations that cannot run concurrently.

**Global namespace**

* Aelia_WC_RequirementsChecks. Implements the logic to use for requirement checking. When requirements are not met, a message is displayed to the site administrators and the plugin doesn't run. Everything is handled gracefully, and displayed messages are clear also to non-technical users.

This product includes GeoLite2 data created by MaxMind, available from
[http://www.maxmind.com](http://www.maxmind.com).

##Requirements
* WordPress 3.6 or later.
* PHP 5.3 or later.
* WooCommerce 2.0.20 or later

## Notes
* This plugin is provided as-is, and it's not automatically covered by free support. See FAQ for more details.

== Frequently Asked Questions ==

= What plugins used this library? =

Most of our free and premium plugins use this library. We released it to the public as many of our customers and collaborators expressed interest in using it.

= What is the support policy for this plugin? =

As indicated in the Description section, we offer this plugin **free of charge**, but we cannot afford to also provide free, direct support for it as we do for our paid products.
Should you encounter any difficulties with this plugin, and need support, you have several options:

1. **[Contact us](http://aelia.co/contact) to report the issue**, and we will look into it as soon as possible. This option is **free of charge**, and it's offered on a best effort basis. Please note that we won't be able to offer hands-on troubleshooting on issues related to a specific site, such as incompatibilities with a specific environment or 3rd party plugins.
2. **If you need urgent support**, you can avail of our standard paid support. As part of paid support, you will receive direct assistance from our team, who will troubleshoot your site and help you to make it work smoothly. We can also help you with installation, customisation and development of new features.

= I have a question unrelated to support, where can I ask it? =

Should you have any question about this product, please feel free to [contact us](http://aelia.co/contact). We will deal with each enquiry as soon as possible.
== Installation ==

1. Extract the zip file and drop the contents in the ``wp-content/plugins/`` directory of your WordPress installation.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Done. No further configuration is required.

For more information about installation and management of plugins, please refer to [WordPress documentation](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins).

== Changelog ==

= 1.6.6.150825 =
* Fixed text domain references in `IP2Location` class.

= 1.6.5.150822 =
* Fixed reference to logger class in IP2Location::update_database().

= 1.6.4.150820 =
* Improved performance of requirement checking class.

= 1.6.3.150815 =
* Added new Aelia_Plugin::is_frontend() method. This method will allow plugins to implement their custom logic to determine if they are working on a frontend page.

= 1.6.2.150731 =
* Improved geolocation features:
	* Added message explaining how the new geolocation system works.
	* Fixed bug in the installation of the Geolocation database.
* Improved requirement checking class.
* Added language files.

= 1.6.1.150728 =
* Improved geolocation:
	* Removed geolocation DB from plugin package.
	* Added automatic installation of geolocation database on activation.
	* Improved error checking, to ensure that issues encountered during automatic update of geolocation DB won't cause crashes.
	* Added caching of geolocation results.
	* Improved logging mechanism.
	* Added new method to retrieve visitor's State/county and city.
* Added new "Admin Messages" feature. This feature will allow plugins to display admin messages in a simple and straightforward way.

= 1.6.0.150724 =
* Improved geolocation:
	* Replaced MaxMind database with "City" one.
	* Added automatic update of GeoIP database.
	* Added functions to geolocate the city information.

= 1.5.19.150625 =
* Added new `Aelia_Plugin::editing_order()` method.
* Added new `Aelia_Plugin::doing_reports()` method.
* Improved rendering of setting pages. Now content posted by customer is escaped, to prevent issues due to HTML embedded in it.

= 1.5.18.150604 =
* Added new `aelia_wc_registered_order_types()` function. The function will provide a list of the registered order types even in WC2.1 and earlier.

= 1.5.17.150529 =
* Improved requirement checking. Now the plugin gracefully informs the user if an unsupported PHP version is installed.

= 1.5.16.150519 =
* Updated requirements. The AFC plugin now requires at least WooCommerce 2.1.9, as method `WC_Session_Handler::has_session()`, invoked by the plugin Session Manager, was introduced in that release.

= 1.5.15.150518 =
* Improved rendering of plugin settings page:
	* Added check to prevent raising a warning when no plugin settings are found.
	* Added CSS class to the plugin settings form, to simplify styling.

= 1.5.14.150514 =
* Improved performance. Moved call to updater and installer to the `admin_init` event.

= 1.5.13.150514 =
* Fixed bug in initialisation of WooCommerce session. the bug caused the session to be initialised when not needed.
* Added caching of session status, to improve performance.

= 1.5.12.150512 =
* Optimised Composer autoloader to improve performance.
* Updated GeoIP library and database.
* Refactored `get_value()` function. The function now uses `isset()` to determine the existence of a key. NOTE: this change makes the function behave differently from before. Now it will return the default value also if the key IS set, but it's "null".
* Refactored `Aelia_Plugin::path()` and `Aelia_Plugin::url()` to improve performance.
* Added `get_arr_value()` function.

= 1.5.11.150507 =
* Extended `Aelia_SessionManager class`. Added `set_cookie()` and `get_cookie()` methods.

= 1.5.10.150505 =
* Added `aelia_wc_version_is()` function. The function allows quickly compare the version of WooCommerce against an arbitrary version value.

= 1.5.9.150504 =
* Added support for CloudFlare. The IP2Location class can now use the country detect by CloudFlare and skips its internal detection logic.
* Added new *wc_aelia_ip2location_before_get_country_code* filter. This new filter will allow 3rd parties to set the country code as they wish, skipping the geolocation detection logic.

= 1.5.8.150429 =
* Improved check to prevent initialising a WooCommerce session when one was already started.
* Added new method to check if a WooCommerce session was started.
* Removed legacy code for WooCommerce 1.6.x.

= 1.5.7.150408 =
* Changed scope of `Aelia_Plugin::visitor_is_bot()` to static.
* Updated GeoIP database.

= 1.5.6.150402 =
* Optimised auto-update logic. The logic now keeps track of the last successful step and, in case of error, it resumes the updates starting from it, rather than from the beginning .
* Added logic to prevent automatic initialisation of sessions when bots visit the site.

= 1.5.5.150318 =
* Fixed bug in handling of error messages related to invalid widget classes.

= 1.5.4.150316 =
* Refactored `Aelia_WC_RequirementsChecks` class to fix incompatibility with wpMandrill plugin.
* Fixed bug in plugin activation logic. The bug caused the plugin activation to fail if the plugin requirements were not indicated as an array.

= 1.5.3.150312 =
* `Aelia_WC_RequirementsChecks` class - Fixed notice related to check for plugin url.

= 1.5.2.150309 =
* Fixed loading of text domain in `Aelia\WC\Aelia_Plugin` base class.

= 1.5.1.150305 =
* Improved `Aelia_WC_RequirementsChecks` class:
	* Fixed `Aelia_WC_RequirementsChecks::js_url()` method. The method now returns the full path to the JS Admin files, without requiring further
	* Improved messages displayed to the Admins.
	* Added `Aelia_WC_RequirementsChecks::$js_dir` property, to allow plugins to specify a custom JS directory.
	* Added `Aelia_WC_RequirementsChecks::$required_php_version` property, to allow plugins to specify the version of PHP.
	* Added logic to prevent enqueuing the `admin-install.js` script more than once.

= 1.5.0.150225 =
* Extended `Aelia_WC_RequirementsChecks` class. Added support for automatic installation and activation of required plugins.

= 1.4.11.150220 =
* Improved `Aelia_Install::add_column()` method. The method is now more powerful and flexible.

= 1.4.10.150209 =
* Changed scope of `Aelia\WC\Aelia_Plugin::log()` to `public`.

= 1.4.9.150111 =
* Added `Aelia_Install::column_exists()` method.
* Added `Aelia_Install::add_column()` method.

= 1.4.8.150109 =
* Added `attributes` argument to `Settings_Renderer::render_text_field()`.
* Added `attributes` argument to `Settings_Renderer::render_checkbox_field()`.

= 1.4.7.150107 =
* Improved requirement checker class. Replaced absolute plugin path with `WP_PLUGIN_DIR` constant.

= 1.4.6.150106 =
* Fixed bug in `Settings_Renderer::render_dropdown_field()`. The bug prevented the CSS class specified in field settings from being applied to the rendered element.

= 1.4.5.141230 =
* Added Httpful library.

= 1.4.4.141224 =
* Fixed bug in auto-update mechanism that prevented external plugin from being able to call it.

= 1.4.3.141223 =
* Refactored `Semaphore` class to use MySQL `GET_LOCK()`.
* Moved automatic updates to WordPress `init` event.

= 1.4.2.141222 =
* Updated GeoIP database.

= 1.4.1.141214 =
* Improved display of "missing requirements" messages.

= 1.4.0.141210 =
* Improved performance in Admin sections. Admin pages now run initialisation code only when they are requested.
* Improved rendering of checkboxes. The new logic ensures that a value is always posted for a checkbox, whether it's ticked or unticked.
* Added `Aelia\WC\Order::get_meta()` method.

= 1.3.0.141208 =
* Added base `Aelia\WC\ExchangeRatesModel` class.
* Added `Settings_Renderer::render_dropdown_field()` method.
* Improved semaphore logic used during auto-updates to reduce race conditions.
* Updated GeoIP database.

= 1.2.3.141129 =
* Added `WC\Aelia\Aelia_Plugin::log()` method.
* Added `Aelia\WC\Order::get_customer_vat_number()` method.
* Added possibility to show a description below the fields in plugin's settings page.
* Added `Aelia\WC\Settings_Renderer::render_text_field()` method.
* Added `Aelia\WC\Settings_Renderer::render_checkbox_field()` method.
* Added `AeliaSimpleXMLElement` class.
* Added support for database transactions to `Aelia_Install` class.
* Added `Model` class.
* Fixed bug in `Settings_Renderer::default_settings()` method.

= 1.2.2.141023 =
* Improved checks in `Aelia\WC\Settings::load()`. Method can now detect and ignore corrupt settings.

= 1.2.1.141017 =
* Added new `Aelia\WC\Aelia_Plugin::init_woocommerce_session()` method, which initialises the WooCommerce session.

= 1.2.0.141013 =
* Added `aelia_t()` function. The function integrates with WPML to translate dynamically generated text and plugin settings.

= 1.1.3.140910 =
* Updated reference to `plugin-update-checker` library.

= 1.1.2.140909 =
* Updated `readme.txt`.

= 1.1.1.140909 =
* Cleaned up build file.

= 1.1.0.140909 =
* Added automatic update mechanism.

= 1.0.12.140908 =
* Added `Aelia_SessionManager::session()` method, as a convenience to retrieve WC session

= 1.0.11.140825 =
* Fixed minor bug in `IP2Location` class that generated a notice message.

= 1.0.10.140819 =
* Fixed logic used to check and load plugin dependencies in `Aelia_WC_RequirementsChecks` class.

= 1.0.9.140717 =
* Refactored semaphore class:
	* Optimised logic used for auto-updates to improve performance.
	* Fixed logic to determine the lock ID for the semaphore.

= 1.0.8.140711 =
* Improved semaphore used for auto-updates:
	* Reduced timeout to forcibly release a lock to 180 seconds.
* Modified loading of several classes to work around quirks of Opcode Caching extensions, such as APC and XCache.

= 1.0.7.140626 =
* Added geolocation resolution for IPv6 addresses.
* Updated Geolite database.

= 1.0.6.140619 =
* Modified loading of Aelia_WC_RequirementsChecks class to work around quirks of Opcode Caching extensions, such as APC and XCache.

= 1.0.5.140611 =
* Corrected loading of plugin's text domain.

= 1.0.4.140607 =
* Modified logic used to load main class to allow dependent plugins to load AFC for unit testing.

= 1.0.3.140530 =
* Optimised auto-update logic to reduce the amount of queries.

= 1.0.2.140509 =
* Updated Composer dependencies.
* Removed unneeded code.
* Corrected reference to global WooCommerce instance in Aelia\WC\Aelia_SessionManager class.

= 1.0.1.140509 =
* First public release
