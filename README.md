<<<<<<< HEAD
# WPSocialite

This plugin adds the SocialiteJS Functionailty (Developed by David Bushell[http://dbushell.com/]) to Wordpress. 

Author: Tom Morton [http://twmorton.com](http://twmorton.com/) [@tmort](http://twitter.com/tmort/)

For a demo, please visit http://twmorton.com/hello-world/


Original SocialJS Author: David Bushell [http://dbushell.com](http://dbushell.com/) [@dbushell](http://twitter.com/dbushell/)

Copyright © 2012

## Setup

Download the plugin, place into your wp-content/plugins folder, activate it under your wordpress plugins panel, and you're all set! Configure settings via the Options->Discussion Settings page. 

## Manual Usage

A big thanks to [@shmula](http://twitter.com/shmula/) for contributing his thoughts and needs! I've added manual usage so you can insert WPSocialite wherever you like within your Wordpress template. To do so, navigate to the settings page and set the position to "manual". Then, place the following code wherever you would like the social icons to appear. 

<code><?php echo $wpsocialite->wpsocialite_markup('large'); ?> </code> 

You can set the size/style of your social icons by setting 'large' or 'small' inside of the function. 

## Contribute

Don't hesitate to send issues or feature requests, or even improvements. The plugin will go live on the Wordpress Plugin repo shortly. 
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
>>>>>>> conflictissue
