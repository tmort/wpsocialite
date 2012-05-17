<?php
/*
Plugin Name: WPSocialite
Plugin URI: http://wordpress.org/extend/plugins/wpsocialte/
Description: No one likes long load times! Yet we all want to be able to share our content via Facebook, Twitter, and all other social networks. These take a long time to load. Paradox? Not anymore! With WPSocialite (utilizing David Bushnell's amazing SocialiteJS plugin [http://www.socialitejs.com/]) we can manage the loading process of our social sharing links. Load them on hover, on page scroll, and more!  
Author: Tom Morton
Version: 0.9
Author URI: http://twmorton.com/

This plugin uses the Socialitejs library created by David Bushell. The author of this plugin does not wish to claim this tool as his own but ensure that David gets proper credit for his work. I've simply wrapped his fantastic tool into a Wordpress plugin for us all to use. Please be sure to check him out: @dbushell or http://socialitejs.com

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

define( 'WPSOCIALITE_URL', plugin_dir_url(__FILE__) );
define( 'WPSOCIALITE_URL_LIB', plugin_dir_url(__FILE__).'lib' );
define( 'WPSOCIALITE_PATH', plugin_dir_path(__FILE__) );



if (!class_exists("wpsocialite")) {

	class wpsocialite {
	
		function WPSocialite()
		{
			$this->__construct();
		} // function
	
		function __construct()
		{
			new WPSocialite_Options;
			//add_action( 'admin_init', array( &$this, 'admin_init' ) );
			add_action( 'init', array( &$this, 'init' ) );
			
			add_action( 'wp_head', array( &$this, 'wpsocialite_vardefine_head' ) );
			
			add_filter( 'the_content', array( &$this, 'wpsocialite_add_to_content' ) );

		} // __construct
	
		function admin_init()
		{
			//$this->ban_check();
		} // admin_init
	
		function init()
		{
			$this->wpsocialite_enqueue_scripts();
			$this->wpsocialite_enqueue_styles();
		} // init
		function wpsocialite_vardefine_head()
		{
			global $wp;
			
			$value = get_option('wpsocialite_classes');
			
			if($value == ''){
				$value = 'article.post, .post, .page';
			}

			$script = "<script type=\"text/javascript\"> var thePostClasses = '$value'; </script>\n";
			
			echo $script;
		}
		function wpsocialite_enqueue_scripts()
		{
			if(!is_admin()){
			
				wp_enqueue_script('socialite-lib', WPSOCIALITE_URL_LIB.'/socialite.min.js', array('jquery'), '1.0', true);
				
				$value = get_option('wpsocialite_mode');
				if($value == 'scroll'){
					wp_enqueue_script('wpsocialite-scroll', WPSOCIALITE_URL_LIB.'/wpsocialite-scroll.js', array('jquery'), '1.0', true);				
				}else{
					wp_enqueue_script('wpsocialite-scroll', WPSOCIALITE_URL_LIB.'/wpsocialite-hover.js', array('jquery'), '1.0', true);
				}
				
			}// if is admin
			
		} // wpsocialite_enqueue_scripts

		function wpsocialite_enqueue_styles()
		{
			
			if(!is_admin()){
			
				wp_enqueue_style('socialite-css', WPSOCIALITE_URL_LIB.'/socialite.css');
			
			}// if is admin
			
			
		} // wpsocialite_enqueue_scripts
		
		
		function wpsocialite_markup() 
		{
			global $wp_query;
			$post = $wp_query->post; //get post content
			$id = $post->ID; //get post id
			$postlink = get_permalink($id); //get post link
			$title = trim($post->post_title); // get post title

			$return_social_large = '
			<ul id="socialite-large" class="social-buttons cf">
				<li><a href="http://twitter.com/share" class="socialite twitter" data-text="Socialite.js" data-url="'.$postlink.'" data-count="vertical" data-via="wpsocialite" rel="nofollow" target="_blank"><span>Share on Twitter</span></a></li>
				<li><a href="https://plus.google.com/share?url='.$postlink.'" class="socialite googleplus" data-size="tall" data-href="'.$postlink.'" rel="nofollow" target="_blank"><span>Share on Google+</span></a></li>
				<li><a href="http://www.facebook.com/sharer.php?u='.$postlink.'" class="socialite facebook" data-href="'.$postlink.'" data-send="false" data-layout="box_count" data-width="60" data-show-faces="false" rel="nofollow" target="_blank"><span>Share on Facebook</span></a></li>
				<li><a href="http://www.linkedin.com/shareArticle?mini=true&amp;url='.$postlink.'" class="socialite linkedin" data-url="'.$postlink.'" data-counter="top" rel="nofollow" target="_blank"><span>Share on LinkedIn</span></a></li>
			</ul>
			';
			
			$return_social_small = '
			<ul id="socialite-small" class="social-buttons cf">
				<li><a href="http://twitter.com/share" class="socialite twitter" data-text="Socialite.js" data-url="'.$postlink.'" data-count="horizontal" data-via="wpsocialite" rel="nofollow" target="_blank"><span>Share on Twitter</span></a></li>
				<li><a href="https://plus.google.com/share?url='.$postlink.'" class="socialite googleplus" data-size="medium" data-href="'.$postlink.'" rel="nofollow" target="_blank"><span>Share on Google+</span></a></li>
				<li><a href="http://www.facebook.com/sharer.php?u='.$postlink.'" class="socialite facebook" data-href="'.$postlink.'" data-send="false" data-layout="button_count" data-width="60" data-show-faces="false" rel="nofollow" target="_blank"><span>Share on Facebook</span></a></li>
				<li><a href="http://www.linkedin.com/shareArticle?mini=true&amp;url='.$postlink.'" class="socialite linkedin" data-url="'.$postlink.'" data-counter="right" rel="nofollow" target="_blank"><span>Share on LinkedIn</span></a></li>
			</ul>
			';

			$value = get_option('wpsocialite_style');

			if($value == 'small'){
				return $return_social_small;
			} else {
				return $return_social_large;
			}



		} 
		
		function wpsocialite_add_to_content( $content )
		{
			
			$position = get_option('wpsocialite_position');

			if($position == 'before') {
							
				$content = $this->wpsocialite_markup() . $content;
			
			} elseif($position = 'after'){

				$content .= $this->wpsocialite_markup();

			}

			return $content;


		}
		
	
	} // class

} //End if class exists

if (!class_exists("wpsocialite_options")) {
	class WPSocialite_Options {
	
		function WPSocialite_Options()
		{
			$this->__construct();
		} // function
	
		function __construct()
		{
			add_action( 'admin_init', array( &$this, 'admin_init' ) );
			add_filter('plugin_action_links', array(&$this, 'wpsocialite_settings_link'), 10, 2 );
		} // function
	
		function admin_init()
		{
			add_settings_field(
				$id = 'wpsocialite_mode',
				$title = "WPSocialite Mode",
				$callback = array( &$this, 'wpsocialite_mode' ),
				$page = 'discussion'
				);
			register_setting( $option_group = 'discussion', $option_name = 'wpsocialite_mode' );

			add_settings_field(
				$id = 'wpsocialite_classes',
				$title = "WPSocialite Classes",
				$callback = array( &$this, 'wpsocialite_classes' ),
				$page = 'discussion'
				);
			register_setting( $option_group = 'discussion', $option_name = 'wpsocialite_classes' );

			add_settings_field(
				$id = 'wpsocialite_style',
				$title = "WPSocialite Style",
				$callback = array( &$this, 'wpsocialite_style' ),
				$page = 'discussion'
				);
			register_setting( $option_group = 'discussion', $option_name = 'wpsocialite_style' );

			add_settings_field(
				$id = 'wpsocialite_position',
				$title = "WPSocialite Position",
				$callback = array( &$this, 'wpsocialite_position' ),
				$page = 'discussion'
				);
			register_setting( $option_group = 'discussion', $option_name = 'wpsocialite_position' );

		} // function
	
		function wpsocialite_mode()
		{
			$value = get_option('wpsocialite_mode');
			# echo your form fields here containing the value received from get_option
			
			if($value == 'hover'){
			$options = '<option value="hover" selected="selected">Hover</option>
						<option value="scroll">Scroll</option>';
			
			} elseif($value == 'scroll'){
			$options = '<option value="hover">Hover</option>
						<option value="scroll" selected="selected">Scroll</option>';
			} else{
			$options = '<option value="hover">Hover</option>
						<option value="scroll" selected="selected">Scroll</option>';
			}
			
			echo '<label for="wpsocialite_mode">
					<select name="wpsocialite_mode" id="wpsocialite_mode">
						'.$options.'
					</select>
					Choose the type of socialite style you would like to use.
				</label>';
			


		} // function

		function wpsocialite_position()
		{
			$value = get_option('wpsocialite_position');
			# echo your form fields here containing the value received from get_option
			
			if($value == 'before'){
			$options = '<option value="before" selected="selected">Before</option>
						<option value="after">After</option>';
			
			} elseif($value == 'after'){
			$options = '<option value="before">Before</option>
						<option value="after" selected="selected">After</option>';
			} else {
			$options = '<option value="before" selected="selected">Before</option>
						<option value="after">After</option>';			
			}
			
			echo '<label for="wpsocialite_position">
					<select name="wpsocialite_position" id="wpsocialite_position">
						'.$options.'
					</select>
					Choose where you would like the social icons to appear, before or after the main content.
				</label>';
			


		} // function

		function wpsocialite_style()
		{
			$value = get_option('wpsocialite_style');
			# echo your form fields here containing the value received from get_option
			
			if($value == 'small'){
			$options = '<option value="large">Large</option>
						<option value="small" selected="selected">Small</option>';
			} else {
			$options = '<option value="large" selected="selected">Large</option>
						<option value="small">Small</option>';
			}
			
			echo '<label for="wpsocialite_style">
					<select name="wpsocialite_style" id="wpsocialite_style">
						'.$options.'
					</select>
					Choose the type of socialite style you would like to use.
				</label>';
			


		} // function
		
		function wpsocialite_classes()
		{
			$value = get_option('wpsocialite_classes');
			if($value == ''){
				$value = 'article.post, .post, .page';
			}
			# echo your form fields here containing the value received from get_option
			
			echo '<label for="wpsocialite_classes">
					<input type="text" name="wpsocialite_classes" id="wpsocialite_classes" value="'.$value.'" class="large-text" >
					Define the class that your posts and pages are wrapped in. If you are unsure, leave it as is.
				</label>';
			
		} // function	

		function wpsocialite_settings_link($links, $file) {
			static $this_plugin;
			if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);

			if ($file == $this_plugin){
				$settings_link = '<a href="options-discussion.php#wpsocialite_mode">'.__("Settings", "photosmash-galleries").'</a>';
				array_unshift($links, $settings_link);
			}
			return $links;
		}
		 
	
	} // class 

} //End if class exists


new wpsocialite;
