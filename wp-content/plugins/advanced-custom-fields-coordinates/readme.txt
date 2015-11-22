=== Advanced Custom Fields: Coordinates ===

Contributors: stupid_studio
Requires at least: 3.4.0
Tags: admin, advanced custom field, cusstom field, acf, google maps, maps, gmap, map
Tested up to: 3.6.1
Stable tag: 1.0.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html


== Description ==

This add-on to [Advanced Custom Fields (ACF)](http://www.advancedcustomfields.com/ "Advanced Custom Fields")
makes it easy to add coordinates to your posts by choosing the location on a
visual map or by searching for an address.

This software is licensed under the GNU General Public License version 3. See
gpl.txt included with this software for more detail.

The plugin relies on the Google Maps API. It does not use an API-key and is
therefore operating under the [restrictions of the free Google Maps API](https://developers.google.com/maps/faq#usage_pricing),
which should be plenty for most backend usage.


== Installation ==

Install this plugin by downloading [the source](https://github.com/StupidStudio/acf-coordinates/archive/master.zip)
and unzipping it into the plugin folder in your WordPress installation. Make
sure to also have ACF installed.


== Usage ==

When you create a new custom field with ACF, set the field type to
**Coordinates map**. Now the coordinates chooser should show up when you edit
a post with your custom fields.

To get the coordinates data in your frontend, simply request the field value
and in return you get the latitude, longitude and the address.

    <?php
    $values = get_field('*****FIELD_NAME*****');
    $lat = $values['lat'];
    $lng = $values['lng'];
    $address = $values['address'];

Address is not the exact, correct name of the location. Instead it is the 
term you wrote when searching for the coordinates.


== Frequently Asked Questions ==

= How do I get the plugin to show a map on the website? =

By implementing a map on your own. We do not provide a frontend-implementation - this is up to you.


== Screenshots ==

1. The plugin in action in the backend. It is shown as a stand-alone field, as well as inside a repeater.

== Changelog ==

= 1.0.0 =

* First stable release.