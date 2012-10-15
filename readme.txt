=== WPSocialite ===
Contributors: TM3909
Donate link:
Tags: social networking, sharing links, lazy loading, lazy loading social links, social links, tm3909
Requires at least: 3.0
Tested up to: 3.4.2
Stable tag: 1.4.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Long page loads aren't fun for anyone. Use WPSocialite to take control of heavy social sharing links and load them how you want!

== Description ==

No one likes long load times, but we all want to be able to share our content via Facebook, Twitter, and other social networks. These take a long time to load. Paradox? Not anymore! With WPSocialite (utilizing David Bushell's amazing SocialiteJS plugin [http://www.socialitejs.com/]) we can manage the loading process of our social sharing links. Load them on hover, on page scroll, and more!


== Installation ==

1. Download the wpsocialite folder and upload it to {your-wp-directory}/wp-content/plugins folder.
2. Visit Your-website.com/wp-admin/plugins.php and activate WPSocialite.
3. Head to Settings->Discussion and scroll to "WPSocialite" settings to configure the plugin.

Thats it!


== Frequently Asked Questions ==

= So what does this do? =

WPSocialite uses socialite.js and implements it into the Wordpress workflow, adding it automatically (or manually, if you choose) to your content.

Socialite.js allows us to define when we would like to load our social sharing links. For example, if we have a page with ten posts, each with their own set of Facebook Like, Google+ Share, and Twitter Share links, they could take some time to load. Using WPSocialite, you can load those individually when the user scrolls or hovers over a specific post.

= Can I add the social links myself instead of letting the plugin place them? =

Of course! Use the "manual" setting under the plugin settings (Settings->Discussion) and then use the following PHP in your template to display the links however you please.

`<?php echo $wpsocialite->wpsocialite_markup('large'); ?>`

When using this method, be sure to include "large" or "small" inside the function (as seen above) to define which style WPSocialite will use to display your social links.

= Can I disable the plugins script loading in order to manually add the CSS and Javascript myself? =

Yes! By dropping the following code into your wp-config.php file you will tell the plugin to not load its CSS and Javascript and give you the ability to add it manually.

`define('WPSOCIALITE_LOADSCRIPTS', false);`

Setting this to false tells the plugin to not load any Javascript or CSS. If you want the plugin to automatically load it again, simply set this to true or remove it completely.

Please note, when using this method if you are loading any social networks with an external file (Pinterest, for example), you will also have to load the javascript file associated with the network (wpsocialite/Socialite/extensions/socialite.pinterest.js).


== Screenshots ==

1. WPSocialite shown before a post, before load.
2. WPSocialite shown before a post, after hover.
3. Plugin settings, located in Settings->Discussion

== Changelog ==

= 1.4.1 =
* Quickfix for the issues with GIT and SVN Repo. Also added ability to disable autoloading CSS and JS. See FAQ for more information.

= 1.4 =
* Cleaned up CSS and added media call to pinterest button.

= 1.3 =
* Major Bugfix, CSS ID calls causing feed issues. Changed to classes.

= 1.2 =
* Added ability to enable/disable different social networks. Choose which to display!
* General repository and plugin cleanup.

= 1.1 =
* Updated to latest version of SocialiteJS

= 1.0 =
* First version, here goes nothing!


== Upgrade Notice ==

= 1.4.1 =
Cleaned up files and added ability to disable automatic loading of JS and CSS. Upgrade to ensure no compatability issues arise.

= 1.0 =
None as of yet.