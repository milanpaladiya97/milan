=== Popup anything on click ===
Contributors: wponlinesupport, anoopranawat, pratik-jain
Tags:  modal popup, popup, modal, full screen popup, html popup, image popup, popup on click, modal popup on click, full screen popup on click, on click popup, 
Requires at least: 4.0
Tested up to: 5.3.2
Stable tag: trunk
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Display a modal popup by clicking on a link, image or button. Also work with Gutenberg shortcode block. 

== Description ==
Popup anything by **Clicking on a** 

* Link, 
* Image or 
* Button 

Manage powerful modal popups for your WordPress blog or website. You can add unlimited popups with your own configurations.

Check [Demo and Features](https://demo.wponlinesupport.com/popup-anything-on-click-demo/) for additional information.

Popup anything on click is a modal popup plugin for WordPress website that allows you to add highly customizable popup windows. Set Popup position ie center, top left, top right, bottom left, bottom right, center left and center right.

This plugin enable awesome popup in your WordPress website using short codes. With Popup anything on click plugin you can insert any type of content into your Popup (HTML, Image, Shortcodes etc). Insert your popup shortcode into any page or a post, easily and fast.

**Also work with Gutenberg shortcode block.** 

= Shortcode Example =

<code>[popup_anything id="XX"]</code>

Where XX is popup id.

You can also display popup in template/php file:

<code><?php echo do_shortcode('[popup_anything id="XX"]'); ?></code>

= Compatible with the major form plugins that submit the form with help of ajax =
* Contact form 7
* Ninja Forms
* Gravity Forms
* Any generic form that submit the form with help of ajax.

= Features =
* Link click popup.
* Html popup.
* Image popup.
* Set Popup position ie center, top left, top right, bottom left, bottom right, center left and center right.
* Create and manage as many popups as you want.
* Customize the look and feel of the popup.
* Work well with contact form plugins.
* Display shortcode output in the popup.
* Set custom animation effects (Fadein, Slide, Fall, Flip, Blur, Rotate etc)
* Customize popup animation effect.
* Set popup location on the screen.
* Full screen popup.
* Responsive popup.

= How to install (Video) : =
[youtube https://www.youtube.com/watch?v=Df94DWdmCik] 

= PRO Features Include : =
> <strong>Premium Version</strong><br>
>
> * 17 Effect.
> * Popup background color and font color.
> * Popup background image.
> * Customize popup width.
> * Customize popup overlay color.
> * Customize popup overlay opacity.
> * Customize popup loader color.
> * Set loader speed as you want
> * Set Background Image and color.
>
> View [PRO DEMO and Features](https://www.wponlinesupport.com/wp-plugin/popup-anything-click/) for additional information.
>

= Privacy & Policy =
* We have also opt-in e-mail selection , once you download the plugin , so that we can inform you and nurture you about products and its features.

== Installation ==

1. Upload the 'popup-anything-on-click' folder to the '/wp-content/plugins/' directory.
2. Activate the "popup-anything-on-click" list plugin through the 'Plugins' menu in WordPress.
3. Check the Popup Anything Menu button and start adding popups.

== Frequently Asked Questions ==

= How to enable/disable Gutenberg editor support for Popup Anything? =

Just add this code in your theme function.php file to enable/disable Gutenberg editor support for  Popup Anything :

<code>
function prefix_gutenberg_editor_support($popupaoc_args){
 $popupaoc_args['show_in_rest'] = true; 
  return $popupaoc_args;	
}
add_filter( 'popupaoc_registered_post_type_args', 'prefix_gutenberg_editor_support' );
</code>


== Screenshots ==

1. How to add create a popup and add content and shortcode
2. Settings
3. Popup Effects
4. Also work with Gutenberg shortcode block.


== Changelog ==

= 1.6 (06, Jan 2020) =
* [*] Due to Uncaught Error: only one instance of babel-polyfill is allowed reporetd by some users we have added jquery- prefix with custombox.legacy.min.js and custombox.min.js to avoid the conflict with custombox jQuery library already Registred in a theme or in any third-party plugin.
* [*] Renamed : We have Renamed popupaoc-popup.min.js file to custombox.min.js.
* [*] Important Note : Please clear your website cache (if you are using) after updating the plugin.  

= 1.5.1 (06, Jan 2020) =
* [*] Fixed z-index issue with divi theme.

= 1.5 (26, Dec 2019) =
* [*] Tested : Tested with Gutenberg blocks.
* [*] Updated features list.
* [*] Taken care for custombox.legacy.js jQuery library if already Registred in a theme or in any third-party plugin. Handle name is : jquery-custombox-legacy
* [*] If Popup anything plugin is not working with TablePress plugin then please use TablePress shortcode parameter cache_table_output="0" with TablePress shortcode [table id=ID]. So here is complete shortcode : [table id=ID cache_table_output="0" /]

= 1.4.3 (21, Aug 2019) =
* [*] Tested : Tested with Gutenberg blocks.
* [*] To enable/disable Gutenberg editor support for popup anything plugin, please check "FAQ" section of the plugin under "Details" Tab : How to enable/disable Gutenberg editor support for Popup Anything?

= 1.4.2 (12, Feb 2019) =
* [*] Minor change in Opt-in flow.

= 1.4.1 (25, Dec 2018) =
* [*] Update Opt-in flow.

= 1.4 (06, Dec 2018) =
* [*] Tested with WordPress 5.0 and Gutenberg.
* [*] Tested with Twenty Nineteen theme.
* [*] Fixed some CSS issues.

= 1.3 (24, Sep 2018) =
* [+] Added alt tag for Link type image.
* [*] Fixed issue when uploaded a large size image for Link type image.

= 1.2.2 (07, June 2018) =
* [*] Follow some WordPress Detailed Plugin Guidelines.

= 1.2.1 (08-05-2018) =
* Fixed : Fixed some css issues.

= 1.2 (10-3-2018) =
* Fixed : Fixed some css issues

= 1.1.6 (28-2-2018) =
* Fixed : Popup display issue if large content added in the popup.
* Fixed : Aligning the button or simple text to appear next to a word or sentence, rather than it appearing on the next line.
* Changed : Change close button image
* Fixed : Some issues has been fixed
* Fixed : Work with any third party plugin shortcode.

= 1.1.5 (24-1-2018) =
* Thanks to @amugereki for showing us all the bug. Fixed popup display issue if large content added in the popup (Updated)

= 1.1.4 (22-1-2018) =
* Fixed popup display issue if large content added in the popup

= 1.1.3 (12-7-2017) =
* Fixed popup overlay and loader TRUE and FALSE issue

= 1.1.2 (12-7-2017) =
* Fixed close button issue

= 1.1.1 =
* Fixed popup close issue on full screen.

= 1.0 =
* Initial release.