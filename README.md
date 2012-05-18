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