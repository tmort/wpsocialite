# WPSocialite

This plugin adds the SocialiteJS Functionailty (Developed by David Bushell[http://dbushell.com/]) to Wordpress.

Author: Tom Morton [http://twmorton.com](http://twmorton.com/) [@tmort](http://twitter.com/tmort/)


You can download the plugin in the Wordpress Plugin Repo here: [http://wordpress.org/extend/plugins/wpsocialite/](http://wordpress.org/extend/plugins/wpsocialite/)

Original SocialJS Author: David Bushell [http://dbushell.com](http://dbushell.com/) [@dbushell](http://twitter.com/dbushell/)


## Setup

Download the plugin, place into your wp-content/plugins folder, activate it under your wordpress plugins panel, and you're all set! Configure settings via the Options->Discussion Settings page.

## Manual Usage

Use the "manual" setting under the plugin settings (Settings->Discussion) and then use the following template tags in your template to display the links however you please.

The first template tag is to echo out the markup and display WPSocialite:

<code><?php wpsocialite_markup('large'); ?></code>

The second template tag is to get WPSocialite's mark up and place it in an object, if needed:

<code><?php
    $wpsocialite =  get_wpsocialite_markup('small');
    echo $wpsocialite;
?></code>

When using this method, be sure to include "large" or "small" inside the function (as seen above) to define which style WPSocialite will use to display your social links.

You can also use the shortcode [wpsocialite size="large"] or [wpsocialite size="small"] in a post or page and it will display the social sharing buttons anywhere you like.

## Disable Script Loading

By dropping the following code into your wp-config.php file you will tell the plugin to not load its CSS and Javascript and give you the ability to add it manually.

<code>define('WPSOCIALITE_LOADSCRIPTS', false);</code>

Setting this to false tells the plugin to not load any Javascript. If you want the plugin to automatically load it again, simply set this to true or remove it completely.

To stop the plugin from automatically loading its CSS, you would use the following line in the same way:

<code>define( 'WPSOCIALITE_LOADSTYLES', false);</code>

Please note, when using this method if you are loading any social networks with an external file (Pinterest, for example), you will also have to load the javascript file associated with the network (wpsocialite/Socialite/extensions/socialite.pinterest.js).

=======
# Socialite

### Because if you're selling your soul, you may as well do it asynchronously.

Socialite provides a very easy way to implement and activate a plethora of social sharing buttons — any time you wish. On document load, on article hover, on any event!

[For a demo and documentation visit: **socialitejs.com**](http://www.socialitejs.com/)

Author: David Bushell [http://dbushell.com](http://dbushell.com/) [@dbushell](http://twitter.com/dbushell/)

Copyright © 2012

## Features and Benefits

* No more tedious copy/paste!
* No dependencies.
* Loads external resources only when needed.
* Less than 2kb when minified and compressed.
* More accessible and styleable defaults/fallbacks.
* Built in support for Twitter, Google+, Facebook and LinkedIn.
* Easily extendable with other social networks.

## Functions

	<a href="http://twitter.com/share" class="socialite twitter" data-text="Socialite.js" data-url="http://socialitejs.com" data-count="vertical" data-via="dbushell" rel="nofollow" target="_blank">
		Share on Twitter
	</a>

### Load

	Socialite.load();

`load` will search the document for elements with the class `socialite` and magically transform them into sharing buttons (based on a network class and data-* attributes).

	Socialite.load(context);

Be kind! Provide an element to search within using `context` rather than the whole document.

### Activate

	Socialite.activate(element, 'network');

`activate` replaces a single element (or an array of) with the specific social network button. The following are built in by default: `twitter`, `plusone`, `facebook`, `linkedin`.

### Extend

	Socialite.extend('network', function);


With `extend` you can add more social networks! The `function` is called by `Socialite.load` and `Socialite.activate` to replace the default element with the shiny sharing button.
