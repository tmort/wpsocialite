<?php
/*
Plugin Name: WPSocialite
Plugin URI: http://wordpress.org/extend/plugins/wpsocialite/
Description: No one likes long load times! Yet we all want to be able to share our content via Facebook, Twitter, and all other social networks. These take a long time to load. Paradox? Not anymore! With WPSocialite (utilizing David Bushnell's amazing SocialiteJS plugin [http://www.socialitejs.com/]) we can manage the loading process of our social sharing links. Load them on hover, on page scroll, and more!
Author: Tom Morton
Version: 1.4.2
Author URI: http://twmorton.com/

This plugin uses the Socialitejs library created by David Bushell. The author of this plugin does not wish to claim this tool as his own but ensure that David gets proper credit for his work. I've simply wrapped his fantastic tool into a Wordpress plugin for us all to use. Please be sure to check him out: @dbushell or '.$postlink.'

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

define( 'WPSOCIALITE_URL_SOCIALITE', plugin_dir_url(__FILE__).'Socialite' );
define( 'WPSOCIALITE_URL_IMG', plugin_dir_url(__FILE__).'Socialite/demo/images' );

if ( !defined('WPSOCIALITE_LOADSCRIPTS') )
	define( 'WPSOCIALITE_LOADSCRIPTS', true );

if (!class_exists("wpsocialite")) {

	class wpsocialite {

		function WPSocialite()
		{
			$this->__construct();
		} // function

		function __construct()
		{
			new WPSocialite_Options;

			add_action( 'init', array( &$this, 'init' ) );

			add_filter( 'body_class', array( &$this, 'wpsocialite_body_class' ) );

			add_filter( 'the_content', array( &$this, 'wpsocialite_add_to_content' ) );

			if( get_option('wpsocialite_excerpt') == 1 ){
				add_filter( 'the_excerpt', array( &$this, 'wpsocialite_add_to_content' ) );
			}

		} // __construct

		function admin_init()
		{

		} // admin_init

		function init()
		{
			if( WPSOCIALITE_LOADSCRIPTS ){
				$this->wpsocialite_enqueue_scripts();
				$this->wpsocialite_enqueue_styles();
			}
		} // init

		function wpsocialite_body_class($classes)
		{
			$value = get_option('wpsocialite_mode');

			if(!is_admin() && $value == 'scroll' ){

				$classes[] = 'wpsocialite-scroll';

			}

			return $classes;

		}
		function wpsocialite_enqueue_scripts()
		{
			if(!is_admin()){

				wp_enqueue_script('socialite-lib', WPSOCIALITE_URL_SOCIALITE.'/socialite.min.js', array('jquery'), '1.0', true);

				wp_enqueue_script('wpsocialite', WPSOCIALITE_URL.'wpsocialite.js', array('jquery'), '1.0', true);

				$scripts = WPSocialite_Options::wpsocialite_list_network_options(null, null, null, null);

				$value = get_option('wpsocialite_networkoptions');

				foreach ($scripts as $script){
					if( isset($value[$script['slug']]) && $script['external_file'] !== false )
						wp_enqueue_script('socialite-'.$script['slug'].'', WPSOCIALITE_URL_SOCIALITE.'/extensions/'.$script['external_file'].'', array('jquery'), '1.0', true);
				}

			}// if is admin

		} // wpsocialite_enqueue_scripts()

		function wpsocialite_enqueue_styles()
		{

			if(!is_admin()){

				wp_enqueue_style('socialite-css', WPSOCIALITE_URL_LIB.'/wpsocialite.css');

			}// if is admin


		} // wpsocialite_enqueue_scripts


		function wpsocialite_markup($size = null)
		{
			global $wp_query;
			$post = $wp_query->post; //get post content
			$id = $post->ID; //get post id
			$postlink = get_permalink($id); //get post link
			$imagelink = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'full' ); //get the featured image url
			$title = trim($post->post_title); // get post title

			$value = get_option('wpsocialite_networkoptions');
			$buttons = WPSocialite_Options::wpsocialite_list_network_options($postlink, $title, $size, $imagelink[0]);

			$return = '';
			$return .= '<ul class="wpsocialite social-buttons '.$size.'">';

			foreach ($buttons as $button){
				if(isset($value[$button['slug']])) :
					$markup = 'markup_'.$size;
				else :
					continue;
				endif;


				$return .= '<li>'.$button[$markup].'</li>';
			}

			$return .= '</ul>';

			return $return;

		}

		function wpsocialite_add_to_content( $content )
		{
			global $wp_current_filter;
  			if(in_array('get_the_excerpt', $wp_current_filter)) return $content;

			$position = get_option('wpsocialite_position');

			$size = get_option('wpsocialite_style');

			if(is_feed() || is_page())
				return $content; //do not include social markup in feed and page

			switch($position){

				case 'manual':
					//nothing
				break;

				case 'before':

					$content = $this->wpsocialite_markup($size) . $content;

				break;

				case 'after':

					$content .= $this->wpsocialite_markup($size);

				break;
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
				$id = 'wpsocialite_excerpt',
				$title = "WPSocialite Excerpt",
				$callback = array( &$this, 'wpsocialite_excerpt' ),
				$page = 'discussion'
				);
			register_setting( $option_group = 'discussion', $option_name = 'wpsocialite_excerpt' );

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

			add_settings_field(
				$id = 'wpsocialite_position',
				$title = "WPSocialite Position",
				$callback = array( &$this, 'wpsocialite_position' ),
				$page = 'discussion'
				);
			register_setting( $option_group = 'discussion', $option_name = 'wpsocialite_position' );

			add_settings_field(
				$id = 'wpsocialite_networkoptions',
				$title = "WPSocialite Options",
				$callback = array( &$this, 'wpsocialite_networkoptions' ),
				$page = 'discussion'
				);
			register_setting( $option_group = 'discussion', $option_name = 'wpsocialite_networkoptions' );


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

		function wpsocialite_excerpt()
		{
			$value = get_option('wpsocialite_excerpt');
			# echo your form fields here containing the value received from get_option
			if($value == 1) :
				$checked = 'checked';
			else :
				$checked = '';
			endif;

			echo '<label for="wpsocialite_excerpt">
					<input name="wpsocialite_excerpt" type="checkbox" id="wpsocialite_excerpt" value="1" '.$checked.'>
					Display WPSocialite sharing options in the excerpt of your posts.
				</label>';

		} // function

		function wpsocialite_position()
		{
			$value = get_option('wpsocialite_position');
			# echo your form fields here containing the value received from get_option

			if($value == 'before'){
			$options = '<option value="before" selected="selected">Before</option>
						<option value="after">After</option>
						<option value="manual">Manual</option>';
			} elseif($value == 'after'){
			$options = '<option value="before">Before</option>
						<option value="after" selected="selected">After</option>
						<option value="manual">Manual</option>';
			} elseif($value == 'manual'){
			$options = '<option value="before">Before</option>
						<option value="after">After</option>
						<option value="manual" selected="selected">Manual</option>';
			} else {
			$options = '<option value="before" selected="selected">Before</option>
						<option value="after">After</option>
						<option value="manual">Manual</option>';
			}

			echo '<label for="wpsocialite_position">
					<select name="wpsocialite_position" id="wpsocialite_position">
						'.$options.'
					</select>
					Choose where you would like the social icons to appear, before or after the main content. If set to <strong>Manual</strong>, you can use this code to place your Social links anywhere you like: <pre>&lt;?php echo wpsocialite::wpsocialite_markup("large"); ?&gt;</pre>
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

		function wpsocialite_networkoptions(){

			$value = get_option('wpsocialite_networkoptions');
			$buttons = $this->wpsocialite_list_network_options();
			$output = '';
			foreach ($buttons as $button){
				if(isset($value[$button['slug']])) :
					$buttonvalue = $value[$button['slug']];
				else :
					$buttonvalue = 0;
				endif;

				if($buttonvalue == 1) :
					$checked = 'checked';
				else :
					$checked = '';
				endif;

				$output .= '
				<label for="wpsocialite_networkoptions['.$button['slug'].']">
					<input name="wpsocialite_networkoptions['.$button['slug'].']" type="checkbox" id="wpsocialite_networkoptions['.$button['slug'].']" value="1" '.$checked.'>
					'.$button['name'].'
				</label><br />';
			}

			echo 'Select the social networks to display.<br />
				'.$output.'
			';

		}

		function wpsocialite_list_network_options($link = null, $title = null, $size = null, $image = null) {
			if( $image == '') { $image = null; } //link post featured image with Pinterest, if available

            $buttons = array(
                'facebook' => array(
                    'name' => 'Facebook',
                    'slug' => 'facebook',
                    'markup_large' => '<a href="http://www.facebook.com/sharer.php?u='.$link.'&amp;t='.$title.'" class="socialite facebook-like" data-href="'.$link.'" data-send="false" data-layout="box_count" data-width="60" data-show-faces="false" rel="nofollow" target="_blank"><span class="vhidden">Share on Facebook</span></a>',
                    'markup_small' => '<a href="http://www.facebook.com/sharer.php?u='.$link.'&amp;t='.$title.'" class="socialite facebook-like" data-href="'.$link.'" data-send="false" data-layout="button_count" data-width="60" data-show-faces="false" rel="nofollow" target="_blank"><span class="vhidden">Share on Facebook</span></a>',
                    'external_file' => false
                ),
                'twitter' => array(
                    'name' => 'Twitter',
                    'slug' => 'twitter',
                    'markup_large' => '<a href="http://twitter.com/share" class="socialite twitter-share" data-text="'.$title.'" data-url="'.$link.'" data-count="vertical" rel="nofollow" target="_blank"><span class="vhidden">Share on Twitter</span></a>',
                    'markup_small' => '<a href="http://twitter.com/share" class="socialite twitter-share" data-text="'.$title.'" data-url="'.$link.'" data-count="horizontal" data-via="" rel="nofollow" target="_blank"><span class="vhidden">Share on Twitter</span></a>',
                    'external_file' => false
                ),
                'gplus' => array(
                    'name' => 'Google Plus',
                    'slug' => 'gplus',
                    'markup_large' => '<a href="https://plus.google.com/share?url='.$link.'" class="socialite googleplus-one" data-size="tall" data-href="'.$link.'" rel="nofollow" target="_blank"><span class="vhidden">Share on Google+</span></a>',
                    'markup_small' => '<a href="https://plus.google.com/share?url='.$link.'" class="socialite googleplus-one" data-size="medium" data-href="'.$link.'" rel="nofollow" target="_blank"><span class="vhidden">Share on Google+</span></a>',
                    'external_file' => false
                ),
                'linkedin' => array(
                    'name' => 'Linkedin',
                    'slug' => 'linkedin',
                    'markup_large' => '<a href="http://www.linkedin.com/shareArticle?mini=true&amp;url='.$link.'&amp;title='.$title.'" class="socialite linkedin-share" data-url="'.$link.'" data-counter="top" rel="nofollow" target="_blank"><span class="vhidden">Share on LinkedIn</span></a>',
                    'markup_small' => '<a href="http://www.linkedin.com/shareArticle?mini=true&amp;url='.$link.'&amp;title='.$title.'" class="socialite linkedin-share" data-url="'.$link.'" data-counter="right" rel="nofollow" target="_blank"><span class="vhidden">Share on LinkedIn</span></a>',
                    'external_file' => false
                ),
                'pinterest' => array(
                    'name' => 'Pinterest',
                    'slug' => 'pinterest',
                    'markup_large' => '<a href="http://pinterest.com/pin/create/button/?url='.$link.'&amp;media=' . $image . '&amp;description='.$title.'" class="socialite pinterest-pinit" data-count-layout="vertical"><span class="vhidden">Pin It!</span></a>',
                    'markup_small' => '<a href="http://pinterest.com/pin/create/button/?url='.$link.'&amp;media=' . $image . '&amp;description='.$title.'" class="socialite pinterest-pinit" data-count-layout="horizontal"><span class="vhidden">Pin It!</span></a>',
                    'external_file' => 'socialite.pinterest.js'
                ),
            );

			return $buttons;
		}


		function wpsocialite_settings_link($links, $file) {
			static $this_plugin;
			if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);

			if ($file == $this_plugin){
				$settings_link = '<a href="options-discussion.php#wpsocialite_mode">'.__("Settings", "wpsocialite").'</a>';
				array_unshift($links, $settings_link);
			}
			return $links;
		}

	} // class

} //End if class exists


$wpsocialite = new wpsocialite;