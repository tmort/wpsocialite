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
// split style from scripts
if ( !defined('WPSOCIALITE_LOADSTYLES') )
	define( 'WPSOCIALITE_LOADSTYLES', true );


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
                        
                        // localizes the buttons depending on the get_locale().
                        add_action( 'wp_footer', array( &$this, 'wpsocialite_localize_script'),20);

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
			load_plugin_textdomain('wpsocialite', false, dirname(plugin_basename(__FILE__)).'/lang/');
                    
			if( WPSOCIALITE_LOADSCRIPTS ){
				$this->wpsocialite_enqueue_scripts();
			}
			if( WPSOCIALITE_LOADSTYLES ){
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

				wp_enqueue_script('socialite-lib', WPSOCIALITE_URL_SOCIALITE.'/socialite.min.js', array('jquery'), '2.0', true);

				wp_enqueue_script('wpsocialite', WPSOCIALITE_URL.'wpsocialite.js', array('socialite-lib'), '1.0', true);

				$scripts = WPSocialite_Options::wpsocialite_list_network_options(null, null, null, null);

				$value = get_option('wpsocialite_networkoptions');

				foreach ($scripts as $script){
					if( isset($value[$script['slug']]) && $script['external_file'] !== false )
						wp_enqueue_script('socialite-'.$script['slug'].'', WPSOCIALITE_URL_SOCIALITE.'/extensions/'.$script['external_file'].'', array('jquery'), '1.0', true);
				}

			}// if is admin

		} // wpsocialite_enqueue_scripts()

                function wpsocialite_localize_script()
                {
                    // overrides Socialite setup with valid locales
                    
                    $locale = get_locale();
                    $c5 = $locale;
                    $c2 = substr($c5, 0, 2);
                    
                    $fb_locales = array('af_ZA','ar_AR','az_AZ','be_BY','bg_BG','bn_IN','bs_BA','ca_ES','cs_CZ','cy_GB','da_DK','de_DE','el_GR','en_GB','en_US','eo_EO','es_ES','es_LA','et_EE','eu_ES','fa_IR','fi_FI','fo_FO','fr_CA','fr_FR','fy_NL','ga_IE','gl_ES','he_IL','hi_IN','hr_HR','hu_HU','hy_AM','id_ID','is_IS','it_IT','ja_JP','ka_GE','km_KH','ko_KR','ku_TR','la_VA','lt_LT','lv_LV','mk_MK','ml_IN','ms_MY','nb_NO','ne_NP','nl_NL','nn_NO','pa_IN','pl_PL','ps_AF','pt_BR','pt_PT','ro_RO','ru_RU','sk_SK','sl_SI','sq_AL','sr_RS','sv_SE','sw_KE','ta_IN','te_IN','th_TH','tl_PH','tr_TR','uk_UA','vi_VN','zh_CN','zh_HK','zh_TW');
                    $tw_locales = array('en','fr','de','it','es','ko','ja');
                    $gp_locales = array('af','am','ar','eu','bn','bg','ca','zh-HK','zh-CN','zh-TW','hr','cs','da','nl','en-GB','en-US','et','fil','fi','fr','fr-CA','gl','de','el','gu','iw','hi','hu','is','id','it','ja','kn','ko','lb','lt','ms','ml','mr','no','fa','pl','pt-BR','pt-PT','ro','ru','sr','sk','sl','es','es-419','sw','sv','ta','te','th','tr','uk','ur','vi','zu');
                    
                    $fb_locale = (in_array($c5,$fb_locales))? $c5 : 'en_US';
                    $tw_locale = (in_array($c2,$tw_locales))? $c2 : 'en';
                    $gp_locale = (in_array($c5,$gp_locales))? str_replace('_', '-', $c5) : (in_array($c2,$gp_locales))? $c2 : 'en';
                    
                    
                    echo "<script type=\"text/javascript\">Socialite.setup({facebook:{lang:'$fb_locale',appId:null},twitter:{lang:'$tw_locale'},googleplus:{lang:'$gp_locale'}});</script>";
                    
                }
                
		function wpsocialite_enqueue_styles()
		{

			if(!is_admin()){

				wp_enqueue_style('socialite-css', WPSOCIALITE_URL_LIB.'/wpsocialite.css');

			}// if is admin


		} // wpsocialite_enqueue_scripts


		function wpsocialite_markup($args = array())
		{
			// use the wp_parse_arg paradigm to permit easy addition of parameters in the future.
			$default_args = array(
				'size'=>get_option('wpsocialite_style')
			);
			extract(wp_parse_args($args,$default_args),EXTR_SKIP);
			
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
                        // added single and post type filters
                    
                    
			$single = get_option('wpsocialite_single');

                        $position = get_option('wpsocialite_position');

                        $post_types = get_option('wpsocialite_post_types',array());
                        $pt = get_post_type();
                        
                        if ($single && !is_single())
                                return $content;
                        
                        if(!in_array($pt,$post_types))
                                return $content;
                        
			if(is_feed())
				return $content; //do not include social markup in feed

			switch($position){

				case 'manual':
					//nothing
				break;

				case 'before':

					$content = $this->wpsocialite_markup() . $content;

				break;

				case 'after':

					$content .= $this->wpsocialite_markup();

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
			add_settings_section( 
                                $id = 'wpsocialite', 
                                $title = __('WPSocialite','wpsocialite'), 
                                $callback = array(&$this,'wpsocialite_section'), 
                                $page = 'discussion'
                                ); 
                        
                        
                        add_settings_field(
				$id = 'wpsocialite_mode',
				$title = __('Mode','wpsocialite'),
				$callback = array( &$this, 'wpsocialite_mode' ),
				$page = 'discussion',
                                $section = 'wpsocialite'
				);
			register_setting( $option_group = 'discussion', $option_name = 'wpsocialite_mode' );

			add_settings_field(
				$id = 'wpsocialite_excerpt',
				$title = __('Apply to Excerpt','wpsocialite'),
				$callback = array( &$this, 'wpsocialite_excerpt' ),
				$page = 'discussion',
                                $section = 'wpsocialite'
				);
			register_setting( $option_group = 'discussion', $option_name = 'wpsocialite_excerpt' );

			add_settings_field(
				$id = 'wpsocialite_single',
				$title = __('Apply to Single only','wpsocialite'),
				$callback = array( &$this, 'wpsocialite_single' ),
				$page = 'discussion',
                                $section = 'wpsocialite'
				);
			register_setting( $option_group = 'discussion', $option_name = 'wpsocialite_single' );

                        add_settings_field(
				$id = 'wpsocialite_style',
				$title = __('Style','wpsocialite'),
				$callback = array( &$this, 'wpsocialite_style' ),
				$page = 'discussion',
                                $section = 'wpsocialite'
				);
			register_setting( $option_group = 'discussion', $option_name = 'wpsocialite_style' );

			add_settings_field(
				$id = 'wpsocialite_position',
				$title = __('Position','wpsocialite'),
				$callback = array( &$this, 'wpsocialite_position' ),
				$page = 'discussion',
                                $section = 'wpsocialite'
				);
			register_setting( $option_group = 'discussion', $option_name = 'wpsocialite_position' );

			add_settings_field(
				$id = 'wpsocialite_post_types',
				$title = __('Post Types','wpsocialite'),
				$callback = array( &$this, 'wpsocialite_post_types' ),
				$page = 'discussion',
                                $section = 'wpsocialite'
				);
			register_setting( $option_group = 'discussion', $option_name = 'wpsocialite_post_types' );

			add_settings_field(
				$id = 'wpsocialite_networkoptions',
				$title = __('Network Options','wpsocialite'),
				$callback = array( &$this, 'wpsocialite_networkoptions' ),
				$page = 'discussion',
                                $section = 'wpsocialite'
				);
			register_setting( $option_group = 'discussion', $option_name = 'wpsocialite_networkoptions' );


		} // function

                function wpsocialite_section(){
                        _e('The configuration of the WP Socialite Plugin.','wpsocialite');
                }
                
		function wpsocialite_mode()
		{
			$value = get_option('wpsocialite_mode');
			# echo your form fields here containing the value received from get_option

			// I replaced your if/else logic with the selected() function. Since Scroll is your default value, I've put it at the beginning (auto selection).		
			
			echo '<label for="wpsocialite_mode">
					<select name="wpsocialite_mode" id="wpsocialite_mode">
						<option value="scroll" '.selected($value,'scroll',false).'>'.__('Scroll','wpsocialite').'</option>
						<option value="hover" '.selected($value,'hover',false).'>'.__('Hover','wpsocialite').'</option>
					</select>
					'.__('Choose the event to which Socialite will activate.','wpsocialite').'
				</label>';

		} // function

		function wpsocialite_excerpt()
		{
			$value = get_option('wpsocialite_excerpt');
			# echo your form fields here containing the value received from get_option

			// I replaced your if/else logic with the checked() function.		
			
			echo '<label for="wpsocialite_excerpt">
					<input name="wpsocialite_excerpt" type="checkbox" id="wpsocialite_excerpt" value="1" '.checked($value,1,false).'>
					'.__('Display WPSocialite sharing buttons in the excerpt of your posts.','wpsocialite').'
				</label>';

		} // function

		function wpsocialite_single()
		{
			$value = get_option('wpsocialite_single');
			# echo your form fields here containing the value received from get_option

			// I replaced your if/else logic with the checked() function.		
			
			echo '<label for="wpsocialite_single">
					<input name="wpsocialite_single" type="checkbox" id="wpsocialite_single" value="1" '.checked($value,1,false).'>
					'.__('Display WPSocialite sharing buttons only on single posts.','wpsocialite').'
				</label>';

		} // function

                function wpsocialite_position()
		{
			$value = get_option('wpsocialite_position');
			# echo your form fields here containing the value received from get_option

			// I replaced your if/else logic with the selected() function. Since Before is your default value, I've put it at the beginning (auto selection).		

			echo '<label for="wpsocialite_position">
					<select name="wpsocialite_position" id="wpsocialite_position">
						<option value="before" '.selected($value,'before',false).'>'.__('Before','wpsocialite').'</option>
						<option value="after" '.selected($value,'after',false).'>'.__('After','wpsocialite').'</option>
						<option value="manual" '.selected($value,'manual',false).'>'.__('Manual','wpsocialite').'</option>
					</select>
					'.sprintf(__('Choose where you would like the social icons to appear, before or after the main content. If set to <strong>Manual</strong>, you can use this code to place your Social links anywhere you like in your templates files: %s','wpsocialite'),'<pre>&lt;?php wpsocialite_markup(); ?&gt;</pre>').'
				</label>';



		} // function

		function wpsocialite_style()
		{
			$value = get_option('wpsocialite_style');
			# echo your form fields here containing the value received from get_option

			echo '<label for="wpsocialite_style">
					<select name="wpsocialite_style" id="wpsocialite_style">
						<option value="large" '.selected($value,'large',false).'>'.__('Large','wpsocialite').'</option>
						<option value="small" '.selected($value,'small',false).'>'.__('Small','wpsocialite').'</option>
					</select>
					'.__('Choose the type of socialite style you would like to use.','wpsocialite').'
				</label>';



		} // function
                
                // this addition makes it possible to specify to which post type to show the buttons
                function wpsocialite_post_types(){
                	$value = get_option('wpsocialite_post_types',array());
                        $post_types = get_post_types(array('public'=>true),'objects');

                        foreach($post_types as $pt=>$ptobj){
                            $checked = (in_array($pt, $value))?' checked="CHECKED"': '';
                            echo '<label for="wpsocialite_post_type_'.$pt.'">'
					.'<input name="wpsocialite_post_types[]" type="checkbox" id="wpsocialite_post_type_'.$pt.'" value="'.$pt.'" '.$checked.' > '
                                        .$ptobj->label;
                            echo '</label><br />';
                        }
                    
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

				$output .= '
				<label for="wpsocialite_networkoptions['.$button['slug'].']">
					<input name="wpsocialite_networkoptions['.$button['slug'].']" type="checkbox" id="wpsocialite_networkoptions['.$button['slug'].']" value="1" '.checked($buttonvalue,1,false).'>
					'.$button['name'].'
				</label><br />';
			}

			echo __('Select the social networks to display.','wpsocialite').'<br />
				'.$output.'
			';

		}

		function wpsocialite_list_network_options($link = null, $title = null, $size = null, $image = null) {
			if( $image == '') { $image = null; } //link post featured image with Pinterest, if available
                        $locale = get_locale();
            $buttons = array(
                'facebook' => array(
                    'name' => 'Facebook',
                    'slug' => 'facebook',
                    'markup_large' => '<a href="http://www.facebook.com/sharer.php?u='.$link.'&amp;locale='.$locale.'&amp;t='.$title.'" class="socialite facebook-like" data-lang="'.$locale.'" data-href="'.$link.'" data-send="false" data-layout="box_count" data-width="60" data-show-faces="false" rel="nofollow" target="_blank"><span class="vhidden">'.__('Share on Facebook','wpsocialite').'</span></a>',
                    'markup_small' => '<a href="http://www.facebook.com/sharer.php?u='.$link.'&amp;locale='.$locale.'&amp;t='.$title.'" class="socialite facebook-like" data-lang="'.$locale.'" data-href="'.$link.'" data-send="false" data-layout="button_count" data-width="60" data-show-faces="false" rel="nofollow" target="_blank"><span class="vhidden">'.__('Share on Facebook','wpsocialite').'</span></a>',
                    'external_file' => false
                ),
                'twitter' => array(
                    'name' => 'Twitter',
                    'slug' => 'twitter',
                    'markup_large' => '<a href="http://twitter.com/share" class="socialite twitter-share" data-text="'.$title.'" data-url="'.$link.'" data-count="vertical" data-lang="'.$locale.'" rel="nofollow" target="_blank"><span class="vhidden">'.__('Share on Twitter','wpsocialite').'</span></a>',
                    'markup_small' => '<a href="http://twitter.com/share" class="socialite twitter-share" data-text="'.$title.'" data-url="'.$link.'" data-count="horizontal" data-lang="'.$locale.'" data-via="" rel="nofollow" target="_blank"><span class="vhidden">'.__('Share on Twitter','wpsocialite').'</span></a>',
                    'external_file' => false
                ),
                'gplus' => array(
                    'name' => 'Google Plus',
                    'slug' => 'gplus',
                    'markup_large' => '<a href="https://plus.google.com/share?url='.$link.'" class="socialite googleplus-one" data-size="tall" data-href="'.$link.'" rel="nofollow" target="_blank"><span class="vhidden">'.__('Share on Google+','wpsocialite').'</span></a>',
                    'markup_small' => '<a href="https://plus.google.com/share?url='.$link.'" class="socialite googleplus-one" data-size="medium" data-href="'.$link.'" rel="nofollow" target="_blank"><span class="vhidden">'.__('Share on Google+','wpsocialite').'</span></a>',
                    'external_file' => false
                ),
                'linkedin' => array(
                    'name' => 'Linkedin',
                    'slug' => 'linkedin',
                    'markup_large' => '<a href="http://www.linkedin.com/shareArticle?mini=true&amp;url='.$link.'&amp;title='.$title.'" class="socialite linkedin-share" data-url="'.$link.'" data-counter="top" rel="nofollow" target="_blank"><span class="vhidden">'.__('Share on LinkedIn','wpsocialite').'</span></a>',
                    'markup_small' => '<a href="http://www.linkedin.com/shareArticle?mini=true&amp;url='.$link.'&amp;title='.$title.'" class="socialite linkedin-share" data-url="'.$link.'" data-counter="right" rel="nofollow" target="_blank"><span class="vhidden">'.__('Share on LinkedIn','wpsocialite').'</span></a>',
                    'external_file' => false
                ),
                'pinterest' => array(
                    'name' => 'Pinterest',
                    'slug' => 'pinterest',
                    'markup_large' => '<a href="http://pinterest.com/pin/create/button/?url='.$link.'&amp;media=' . $image . '&amp;description='.$title.'" class="socialite pinterest-pinit" data-count-layout="vertical"><span class="vhidden">'.__('Pin It!','wpsocialite').'</span></a>',
                    'markup_small' => '<a href="http://pinterest.com/pin/create/button/?url='.$link.'&amp;media=' . $image . '&amp;description='.$title.'" class="socialite pinterest-pinit" data-count-layout="horizontal"><span class="vhidden">'.__('Pin It!','wpsocialite').'</span></a>',
                    'external_file' => 'socialite.pinterest.js'
                ),
            );

			return $buttons;
		}


		function wpsocialite_settings_link($links, $file) {
			static $this_plugin;
			if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);

			if ($file == $this_plugin){
				$settings_link = '<a href="options-discussion.php#wpsocialite_mode">'.__('Settings', 'wpsocialite').'</a>';
				array_unshift($links, $settings_link);
			}
			return $links;
		}

	} // class

} //End if class exists


$wpsocialite = new wpsocialite;



/* template function
 *
 */
function wpsocialite_markup($args = array()){
	
	wpsocialite::wpsocialite_markup($args);

}
