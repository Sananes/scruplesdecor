=== Drop Shadow Boxes ===
Contributors: stevehenty
Donate link: http://www.stevenhenty.com/products/wordpress-plugins/donate
Tags: drop shadow,box shadow,perspective,raised,curl,lifted
Requires at least: 3.0
Tested up to: 4.3.1
Stable tag: 1.5.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Highlight important content on your posts and pages inside a box with a drop shadow.

== Description ==

Drop Shadow Boxes highlight important content on your posts, pages and widget areas. Personalise the box with drop shadow effects like raised, lifted and perspective and choose whether the box has an inside shadow, outside shadow and rounded corners. The plugin includes a widget and shortcode builder with a preview so you can test your box before adding it. The shadows will display correctly on most browsers - some older versions of Internet Explorer may not display the shadows - but they will display the box with the content so nothing will be missing on the page.

See the [examples of drop shadow boxes](http://www.stevenhenty.com/products/wordpress-plugins/drop-shadow-boxes/examples/) to see how the plugin performs on your browser:

I'm offering this plugin free of charge. If you use it and like it [please consider giving it a rating](http://wordpress.org/plugins/drop-shadow-boxes/).

Stay in touch by following me on facebook: [Steven Henty](https://www.facebook.com/hentydevelopment)

= Instructions =

The plugin itself doesn't require any configuration. There isn't a setting page.

You can access the widget from the Widgets dashboard page - drag, drop and configure as you would any other widget.

The shortcode builder allows you to add Drop Shadow Boxes to posts and pages. You can access from the media toolbar while you're editing the post/page by clicking on the box icon next to the upload/insert media button.

= Shortcode Reference =

If you prefer not to use the shortcode builder, or if you'd like to modify an existing drop shadow box here's the shortcode reference guide.

Example usage:

[dropshadowbox]your content[/dropshadowbox]

[dropshadowbox align="left"]your content[/dropshadowbox]

[dropshadowbox effect="raised"]your content[/dropshadowbox]

[dropshadowbox effect="horizontal-curve-bottom" rounded_corners="false"]your content[/dropshadowbox]

Shortcode Attributes:

align = [left/right/center/none] default: "none"

width = [width plus units e.g. "250px" or "50%"] default: not set

height = [width plus units e.g. "250px"] default: "auto"

background_color = [colour code or name e.g. "#A8A8A8" or "blue"] default:"#ffffff"

border_width = [width in pixels] default "2"

border_color = [colour code or name e.g. "#A8A8A8" or "blue"] default:"#dddddd"

rounded_corners = [true/false] default: "true"

inside_shadow = [true/false] default: "true"

outside_shadow = [true/false] default: "true"

effect_shadow_color = [red/green/blue/yellow/white] default: gray (known issue: this color option will not worrk for the effect "raised")

effect = [name of the effect] default: "lifted-both"

Possible values for the effect attribute:
* none
* lifted-left
* lifted-right
* lifted-both
* curled
* perspective-left
* perspective-right
* raised
* vertical-curve-left
* vertical-curve-both
* horizontal-curve-bottom
* horizontal-curve-both

inline_styles = [true/false] default: "false" (only for use inside third party widgets and only works when allow_url_fopen is enabled in php.ini)

padding = [width plus units e.g. "250px" or "50%"] Defines the space between the box border and the box content. e.g. 20px. Default:10px;

margin = [width plus units e.g. "250px" or "50%"]  Defines the space around the box. e.g. 20px. Default:not set;

max_width = [width plus units e.g. "250px" or "50%"] Defines the maximum width for the box e.g. 300px

min_width = [width plus units e.g. "250px" or "50%"] Defines the minimum width of the box e.g. 200px


= Language Versions =

Drop Shadow Boxes is currently available in English, Spanish (es_ES), German and Serbian.

Many thanks to Fabio Vogt for the translation into German.
http://www.fabiolous.de

And to Ogi Djuraskovic, First Site Guide for the Serbian translation
http://firstsiteguide.com

The shortcode builder will automatically switch to the language configured in wp-config.php.

If you'd like to contribute other languages please get in touch with me here:
http://www.stevenhenty.com/contact/
You'll find the .po file in the plugin root. I'd be happy to link to your website but requests with spammy links will be ignored.

= Support =
If you find any that needs fixing, or if you have any ideas for improvements, please get in touch:
http://www.stevenhenty.com/contact/

Please also get in touch if you're using the latest version of your browser but the shadows are not displaying.


== Installation ==

1.  Download the zipped file.
1.  Extract and upload the contents of the folder to /wp-contents/plugins/ folder
1.  Go to the Plugin management page of WordPress admin section and enable the 'Drop Shadow Boxes' plugin

== Frequently Asked Questions ==

= How do I make the boxes 'responsive' =
A true responsive design ought to be handled more at the page level but you'll probably find what you're looking for if you change the width attribute to "auto".

= Will the shadows work in all browser? =
It will work on the latest versions of all browsers. Some older browsers may not display the shadows - but they will display the box with the content. Please see the following page for examples to see how it performs on your browser:
http://www.stevenhenty.com/products/wordpress-plugins/drop-shadow-boxes/examples/

= How do I open the shortcode builder? =
While you're editing a post or page you can open the shortcode builder by clicking on the box icon next to the upload media button above the toolbar.

= How do I add links and other formatting inside the box? =
Once you've added the shortcode to the page/post you can edit the contents just like any other content.

= How do I get two or more boxes lined up side by side? =
Try experimenting with the alignment of the boxes. To get a few boxes lined up in a row you'll probably need to align all the boxes left.

= Will it work on a dark background? =
Yes, you'll just need to set the effect_shadow_color shortcode attribute to "white".

= Can I edit the shadow effect? =
The shortcode offers quite a few options. If you need further customisation you'll need to override the css classes in your theme (usually style.css).

= Will the css file be loaded on all pages or only when it's needed? =
The css file will only be loaded when it's needed - when there's a [dropshadowbox] shortcode on the page or post.

= Are images used to display the shadows? =
No. It uses CSS3 only.

= How do I get the drop shadow effects to work in widgets? =
Please try to use the dedicated Drop Shadow Box widget. If you need to use the shortcode inside a different widget, first, make sure the widget allows shortcodes (this is not always the case). If you already have a Drop Shadow Box somewhere on the page/post then the styles will be loaded and it'll look ok. If you don't, then there's a pretty high chance that the styles won't be loaded. In this case, you may like to force the output of the styles by using the "inline_styles" shortcode attribute.
e.g.
[dropshadowbox inline_styles="true"]your content[/dropshadowbox]

= Can I get my content to fill the entire box? =
Yes, set the padding attribute to 0. i.e. padding="0"

= How do I add space around the box? =
Set the margin attribute e.g. 20px


== Screenshots ==

Please see the following page for examples to see how it performs on your browser:
http://www.stevenhenty.com/products/wordpress-plugins/drop-shadow-boxes/examples/

1. Example boxes
2. Shortcode builder
3. Widget options


== ChangeLog ==

= 1.5.4 =
1. Updated text domain
1. Updated default width to auto

= 1.5.3 =
1. Added the Serbian translation

= 1.5.2 =
1. Added support for WordPress 4.3

= 1.5 =
1. Added the margin, max_width and min_width attributes
1. Added "none" as an option for the "effect" attribute
1. Updated the default width option to not set

= 1.4.9 =
1. Added the padding attribute

= 1.4.8 =
1. Fixed more strict notices in PHP 5.4+

= 1.4.7 =
1. Fixed strict notices in PHP 5.4+

= 1.4.6 =
1. added inline_styles shortcode attribute
1. added German language translation

= 1.4.4 =
1. fixed PHP warning messages

= 1.4.3 =
1. fixed media button compatibility with other plugins
1. fixed localisation

= 1.4.2 =
1. changed icon for the new WordPress UI
1. fixed issue with multisite WordPress where icon wasn't appearing

= 1.4 =
1. added shortcode attribute effect_shadow_color
1. fixed alignment to allow boxes to sit side by side
1. fixed plugin URI to point directly to the plugin page

= 1.3 =
1. added compatibility with the Ultimatum theme
1. fixed the rendering of the perspective-left effect
1. fixed the colour picker on the widget which failed to open on page load

= 1.2.3 =
Fixed an issue that affected the display of the widget in some themes

= 1.2.2 =
Fixed an issue that affected the creation of new pages and posts

= 1.2.1 =
Fixed an issue that affected the editing of pages and posts

= 1.2 =
1. added height attribute and options in the widget and shortcode builder
1. added background color attribute and options in the widget and shortcode builder
1. added color pickers

= 1.1 =
added height attibute

= 1.0 =
Version 1.0 release

= 0.3 =
Added a widget

= 0.2 =
* A couple of new effects, some fixes and now also available in Spanish.

= 0.1 =
* Initial beta release.

== Upgrade Notice ==

= 1.5.4 =
1. Updated text domain
1. Updated default width to auto

= 1.5.3 =
1. Added the Serbian translation

= 1.5.2 =
1. Added support for WordPress 4.3

= 1.4.9 =
1. Added the padding attribute

= 1.4.8 =
1. Fixed more strict notices in PHP 5.4+

= 1.4.7 =
1. Fixed strict notices in PHP 5.4+

= 1.4.6 =
1. added inline_styles shortcode attribute
1. added German language translation

= 1.4.4 =
1. fixed PHP warning messages

= 1.4.3 =
1. fixed media button compatibility with other plugins
1. fixed localisation

= 1.4.2 =
1. changed icon for the new WordPress UI
1. fixed issue with multisite WordPress where icon wasn't appearing

= 1.4 =
1. added shortcode attribute effect_color
1. fixed alignment to allow boxes to sit side by side

= 1.3 =
1. added compatibility with the Ultimatum theme
1. fixed the rendering of the perspective-left effect
1. fixed the colour picker on the widget which failed to open on page load

= 1.2.3 =
Fixed an issue that affected the display of the widget in some themes

= 1.2.2 =
Fixed an issue that affected the creation of new pages and posts

= 1.2.1 =
Fixed an issue that affected the editing of pages and posts

= 1.2 =
1. added height attribute and options in the widget and shortcode builder
1. added background color attribute and options in the widget and shortcode builder
1. added color pickers

= 1.1 =
added height attibute

= 1.0 =
Various bug fixes

= 0.3 =
Added a widget

= 0.2 =
A couple of new effects, some fixes and now also available in Spanish.