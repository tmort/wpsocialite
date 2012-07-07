=== Plugin Name ===
Contributors: TM3909
Donate link: http://twmorton.com/plugins/?donate=true
Tags: social networking, sharing links, tm3909
Requires at least: 3.0
Tested up to: 3.0
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Long page loads aren't fun for anyone. Use WPSocialite to take control of heavy social sharing links and load them how you want!

== Description ==

No one likes long load times, but we all want to be able to share our content via Facebook, Twitter, and other social networks. These take a long time to load. Paradox? Not anymore! With WPSocialite (utilizing David Bushnell's amazing SocialiteJS plugin [http://www.socialitejs.com/]) we can manage the loading process of our social sharing links. Load them on hover, on page scroll, and more!


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


== Screenshots ==

1. WPSocialite shown before a post, before load.
2. WPSocialite shown before a post, after hover.
3. Plugin settings, located in Settings->Discussion

== Changelog ==

= 1.1 =
* Updated to latest version of SocialiteJS

= 1.0 =
* First version, here goes nothing!


== Upgrade Notice ==

= 1.0 =
None as of yet.


