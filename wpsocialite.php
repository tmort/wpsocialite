<?php
/*
Plugin Name: WPSocialite
Plugin URI: http://wordpress.org/extend/plugins/wpsocialite/
Description: No one likes long load times! Yet we all want to be able to share our content via Facebook, Twitter, and all other social networks. These take a long time to load. Paradox? Not anymore! With WPSocialite (utilizing David Bushnell's amazing SocialiteJS plugin [http://www.socialitejs.com/]) we can manage the loading process of our social sharing links. Load them on hover, on page scroll, and more!
Author: Tom Morton
Version: 2.4.1
Author URI: http://twmorton.com/

=================================================================

This plugin uses the Socialitejs library created by David Bushell. The author of this plugin does not wish to claim this tool as his own but ensure that David gets proper credit for his work. I've simply wrapped his fantastic tool into a Wordpress plugin for us all to use. Please be sure to check him out: @dbushell or http://socialitejs.com

Copyright 2013 Tom Morton

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

//Lets Go!

if ( !defined('WPSOCIALITE_LOADSCRIPTS') )
    define( 'WPSOCIALITE_LOADSCRIPTS', true );
// split style from scripts
if ( !defined('WPSOCIALITE_LOADSTYLES') )
    define( 'WPSOCIALITE_LOADSTYLES', true );

if (!class_exists("wpsocialite")) {

    class wpsocialite {
        public static $instance;
        private $options;

        public function WPSocialite() {
            $this->__construct();
        }

        function __construct() {
            self::$instance = $this;

            add_action(     'init',                     array( $this, 'init'                            ) );
            add_action(     'wp_footer',                array( $this, 'wpsocialite_localize_script'     ), 20);

            add_action(     'admin_init',               array( $this, 'admin_init'                      ) );
            add_action(     'admin_footer',             array( $this, 'admin_footer'                    ), 20);

            add_filter(     'body_class',               array( $this, 'wpsocialite_body_class'          ) );
            add_filter(     'the_content',              array( $this, 'wpsocialite_filter_content'      ) );
            add_filter(     'mce_external_plugins',     array( $this, 'wpsocialite_shortcode_plugin'    ) );
            add_filter(     'mce_buttons',              array( $this, 'wpsocialite_shortcode_button'    ) );
            add_filter(     'plugin_action_links',      array( $this, 'wpsocialite_settings_link'       ), 10, 2 );
            add_shortcode(  'wpsocialite',              array( $this, 'wpsocialite_shortcode'           ) );

            if( get_option( 'wpsocialite_excerpt' ) == 1 ){
                add_filter( 'the_excerpt',              array( $this, 'wpsocialite_filter_content'      ) );
            }

        } // __construct

        public function init() {

            load_plugin_textdomain('wpsocialite', false, dirname( plugin_basename(__FILE__) ).'/lang/');

            if( WPSOCIALITE_LOADSCRIPTS && !is_admin() ) {

                wp_enqueue_script('socialite-lib',  plugin_dir_url(__FILE__).'Socialite/socialite.min.js',  array('jquery'),        '2.0', true);
                wp_enqueue_script('wpsocialite',    plugin_dir_url(__FILE__).'wpsocialite.js',              array('socialite-lib'), '1.0', true);

                $scripts = self::wpsocialite_list_network_options(null, null, null, null);

                $value = get_option('wpsocialite_networkoptions');

                foreach ($scripts as $script){
                    if( isset($value[$script['slug']]) && $script['external_file'] !== false )
                        wp_enqueue_script('socialite-'.$script['slug'].'', $script['external_file'], array('jquery'), '1.0', true);
                }

            }

            if( WPSOCIALITE_LOADSTYLES && !is_admin()){
                wp_enqueue_style('socialite-css', plugin_dir_url(__FILE__).'lib/wpsocialite.css');
            }

        }

        public function wpsocialite_body_class( $classes ) {
            $value = get_option('wpsocialite_mode');
            if(!is_admin() && $value == 'scroll' ){
                $classes[] = 'wpsocialite-scroll';
            }
            return $classes;
        }

        public function wpsocialite_localize_script() {
            // overrides Socialite setup with valid locales

            $locale = get_locale();
            $c5     = $locale;
            $c2     = substr($c5, 0, 2);

            $fb_locales = array('af_ZA','ar_AR','az_AZ','be_BY','bg_BG','bn_IN','bs_BA','ca_ES','cs_CZ','cy_GB','da_DK','de_DE','el_GR','en_GB','en_US','eo_EO','es_ES','es_LA','et_EE','eu_ES','fa_IR','fi_FI','fo_FO','fr_CA','fr_FR','fy_NL','ga_IE','gl_ES','he_IL','hi_IN','hr_HR','hu_HU','hy_AM','id_ID','is_IS','it_IT','ja_JP','ka_GE','km_KH','ko_KR','ku_TR','la_VA','lt_LT','lv_LV','mk_MK','ml_IN','ms_MY','nb_NO','ne_NP','nl_NL','nn_NO','pa_IN','pl_PL','ps_AF','pt_BR','pt_PT','ro_RO','ru_RU','sk_SK','sl_SI','sq_AL','sr_RS','sv_SE','sw_KE','ta_IN','te_IN','th_TH','tl_PH','tr_TR','uk_UA','vi_VN','zh_CN','zh_HK','zh_TW');
            $tw_locales = array('en','fr','de','it','es','ko','ja');
            $gp_locales = array('af','am','ar','eu','bn','bg','ca','zh-HK','zh-CN','zh-TW','hr','cs','da','nl','en-GB','en-US','et','fil','fi','fr','fr-CA','gl','de','el','gu','iw','hi','hu','is','id','it','ja','kn','ko','lb','lt','ms','ml','mr','no','fa','pl','pt-BR','pt-PT','ro','ru','sr','sk','sl','es','es-419','sw','sv','ta','te','th','tr','uk','ur','vi','zu');

            $fb_locale = (in_array($c5,$fb_locales))? $c5 : 'en_US';
            $tw_locale = (in_array($c2,$tw_locales))? $c2 : 'en';
            $gp_locale = (in_array($c5,$gp_locales))? str_replace('_', '-', $c5) : (in_array($c2,$gp_locales))? $c2 : 'en';

            if( WPSOCIALITE_LOADSCRIPTS && !is_admin() ) {
                echo "<script type=\"text/javascript\">Socialite.setup({facebook:{lang:'$fb_locale',appId:null},twitter:{lang:'$tw_locale'},googleplus:{lang:'$gp_locale'},vkontakte:{apiId:'".get_option('wpsocialite_vkontakte_apiId')."'}});</script>";
            }
        }

        public function wpsocialite_shortcode($atts) {
            extract( shortcode_atts( array(
                'size' => 'small',
            ), $atts ) );
            return get_wpsocialite_markup($atts);
        }

        public function wpsocialite_shortcode_button($buttons) {
            array_push($buttons, "wpsocialite");
            return $buttons;
        }

        public function wpsocialite_shortcode_plugin($plugin_array) {
            $plugin_array['wpsocialite'] = plugin_dir_url(__FILE__).'lib/wpsocialite-shortcode.js';
            return $plugin_array;
        }

        public function wpsocialite_markup( $args = array() ){

            $default_args = array(
                'size'              => get_option('wpsocialite_style'),
                'url'               => null,
                'button_override'   => 'facebook,twitter-share,gplus,linkedin,pinterest,twitter-follow,stumbleupon,vkontakte-like',
            );
            extract( wp_parse_args($args,$default_args), EXTR_SKIP );
            $button_override = str_replace(' ', '', $button_override);
            $button_override = explode(',', esc_attr($button_override));

            global $wp_query;
            $post       = $wp_query->post;
            $id         = $post->ID;
            $imagelink  = self::wpsocialite_get_image( $id );
            $title      = trim($post->post_title);

            if( $url ){
                $postlink   = $url;
            } else {
                $postlink   = get_permalink($id);
            }

            $value      = get_option('wpsocialite_networkoptions');
            $buttons    = self::wpsocialite_list_network_options($postlink, $title, $size, $imagelink);

            $return = '<ul class="wpsocialite social-buttons '.$size.'">';

                foreach ( $buttons as $button ) {

                    if(in_array($button['slug'], $button_override)){

                        if(isset($value[$button['slug']])) :
                            $markup = 'markup_'.$size;
                        else :
                            continue;
                        endif;
                        $return .= '<li>'.$button[$markup].'</li>';

                    }
                }

            $return     .= '</ul>';

            return $return;

        }

        public function wpsocialite_get_image( $postID ) {
            //try the featured image first
            if(has_post_thumbnail()){
                $imageattachment = wp_get_attachment_image_src( get_post_thumbnail_id( $postID ), 'full' );
                $imagelink = $imageattachment[0];
            } else {
            //No featured image? Try for an attachment.
                $args = array(
                    'order'          => 'ASC',
                    'post_parent'    => $postID,
                    'post_type'      => 'attachment',
                    'post_mime_type' => 'image',
                    'post_status'    => null,
                    'showposts'      => '1',
                );
                $attachments = get_posts($args);
                if ($attachments) {
                    foreach ($attachments as $attachment) {
                        $imagelink = wp_get_attachment_url($attachment->ID, 'full', false, false);
                    }
                } else{
                    $imagelink = null; //if there are no attachments set $imagelink to null
                }
            }
            return $imagelink;
        }

        public function wpsocialite_filter_content( $content ){
            global $wp_current_filter;

            $single     = get_option('wpsocialite_single');
            $position   = get_option('wpsocialite_position');
            $post_types = get_option('wpsocialite_post_types',array());
            $pt         = get_post_type();

            if ( $single && !is_singular() ){
                return $content;
            }

            if( $post_types && !in_array($pt,$post_types)){
                return $content;
            }

            if( is_feed() ) {
                return $content;
            }

            if( in_array('get_the_excerpt', $wp_current_filter) ) {
                return $content;
            }

            switch($position){
                case 'manual':
                break;

                case 'both':
                    $content = $this->wpsocialite_markup() . $content . $this->wpsocialite_markup();
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

        public function admin_init() {

            add_settings_section(
                $id         = 'wpsocialite',
                $title      = __('WPSocialite','wpsocialite'),
                $callback   = array($this,'wpsocialite_section'),
                $page       = 'discussion'
            );

            add_settings_field(
                $id         = 'wpsocialite_mode',
                $title      = __('Mode','wpsocialite'),
                $callback   = array( $this, 'wpsocialite_select' ),
                $page       = 'discussion',
                $section    = 'wpsocialite',
                $args       = array(
                    'name'        => 'wpsocialite_mode',
                    'description' => __('Choose the event to which Socialite will activate.','wpsocialite'),
                    'options'     => array(
                        'hover'     => __('Hover','wpsocialite'),
                        'scroll'    => __('Scroll','wpsocialite'),
                    ),
                )
            );
            register_setting( $option_group = 'discussion', $option_name = 'wpsocialite_mode' );

            add_settings_field(
                $id         = 'wpsocialite_excerpt',
                $title      = __('Apply to Excerpt','wpsocialite'),
                $callback   = array( $this, 'wpsocialite_checkbox' ),
                $page       = 'discussion',
                $section    = 'wpsocialite',
                $args       = array(
                    'name'        => 'wpsocialite_excerpt',
                    'description' => 'Display WPSocialite sharing buttons in the excerpt of your posts.',
                    'options'     => array(
                        '1' => __('Display WPSocialite sharing buttons in the excerpt of your posts.','wpsocialite'),
                    ),
                )
            );
            register_setting( $option_group = 'discussion', $option_name = 'wpsocialite_excerpt' );

            add_settings_field(
                $id         = 'wpsocialite_single',
                $title      = __('Apply to Single only','wpsocialite'),
                $callback   = array( $this, 'wpsocialite_checkbox' ),
                $page       = 'discussion',
                $section    = 'wpsocialite',
                $args       = array(
                    'name'        => 'wpsocialite_single',
                    'description' => '',
                    'options'     => array(
                        '1' => __('Display WPSocialite sharing buttons only on single posts.','wpsocialite'),
                    ),
                )
            );
            register_setting( $option_group = 'discussion', $option_name = 'wpsocialite_single' );

            add_settings_field(
                $id         = 'wpsocialite_style',
                $title      = __('Style','wpsocialite'),
                $callback   = array( $this, 'wpsocialite_select' ),
                $page       = 'discussion',
                $section    = 'wpsocialite',
                $args       = array(
                    'name'        => 'wpsocialite_style',
                    'description' => __('Choose the type of socialite style you would like to use.','wpsocialite'),
                    'options'     => array(
                        'large' => __('Large','wpsocialite'),
                        'small' => __('Small','wpsocialite'),
                    ),
                )
            );
            register_setting( $option_group = 'discussion', $option_name = 'wpsocialite_style' );

            add_settings_field(
                $id         = 'wpsocialite_position',
                $title      = __('Position','wpsocialite'),
                $callback   = array( $this, 'wpsocialite_select' ),
                $page       = 'discussion',
                $section    = 'wpsocialite',
                $args       = array(
                    'name'        => 'wpsocialite_position',
                    'description' => sprintf(__('Choose where you would like the social icons to appear, before or after the main content. If set to <strong>Manual</strong>, you can use this code to place your Social links anywhere you like in your templates files: %s','wpsocialite'),'<pre>&lt;?php wpsocialite_markup(); ?&gt;</pre>'),
                    'options'     => array(
                        'before'    => __('Top','wpsocialite'),
                        'after'     => __('Bottom','wpsocialite'),
                        'both'      => __('Top and Bottom','wpsocialite'),
                        'manual'    => __('Manual','wpsocialite'),
                    ),
                )
            );
            register_setting( $option_group = 'discussion', $option_name = 'wpsocialite_position' );

            add_settings_field(
                $id         = 'wpsocialite_post_types',
                $title      = __('Post Types','wpsocialite'),
                $callback   = array( $this, 'wpsocialite_post_types' ),
                $page       = 'discussion',
                $section    = 'wpsocialite'
            );
            register_setting( $option_group = 'discussion', $option_name = 'wpsocialite_post_types' );

            add_settings_field(
                $id         = 'wpsocialite_networkoptions',
                $title      = __('Network Options','wpsocialite'),
                $callback   = array( $this, 'wpsocialite_networkoptions' ),
                $page       = 'discussion',
                $section    = 'wpsocialite'
            );
            register_setting( $option_group = 'discussion', $option_name = 'wpsocialite_networkoptions' );

            add_settings_field(
                $id         = 'wpsocialite_twitter_username',
                $title      = __('Twitter Username','wpsocialite'),
                $callback   = array( $this, 'wpsocialite_text_input' ),
                $page       = 'discussion',
                $section    = 'wpsocialite',
                $args       = array(
                    'name'        => 'wpsocialite_twitter_username',
                    'description' => 'Enter your twitter username to enable the twitter follow button.',
                    'options'     => array(
                        'twitter_username'  => __(''),
                    ),
                )
            );
            register_setting( $option_group = 'discussion', $option_name = 'wpsocialite_twitter_username' );

        }

        public function admin_footer() {
            echo '<script type="text/javascript">
                jQuery(document).ready(function($) {

                    var vkontakte_appid_input = $("#wpsocialite_vkontakte_apiId").closest("tr");
                    var vkontaktelike = $("input:checkbox[name=\'wpsocialite_networkoptions[vkontakte-like]\']");
                    vkontakte_appid_input.hide();

                    if( vkontaktelike.is(":checked") ){
                        vkontakte_appid_input.show();
                    }
                    vkontaktelike.on(\'change\', function() {
                        if(vkontaktelike.is(":checked")){
                            vkontakte_appid_input.show();
                        } else {
                            vkontakte_appid_input.hide();
                        }
                    });
                });
            </script>';
        }

        public function wpsocialite_section() {
            _e('The configuration of the WPSocialite Plugin.','wpsocialite');
        }

        public function wpsocialite_select( $args ) {

            if ( empty( $args['name'] ) || ! is_array( $args['options'] ) )
                return false;

            $selected = ( isset( $args['name'] ) ) ? get_option($args['name']) : '';
            echo '<select name="' . esc_attr( $args['name'] ) . '">';
                foreach ( (array) $args['options'] as $value => $label ){
                    echo '<option value="' . esc_attr( $value ) . '"' . selected( $value, $selected, false ) . '>' . $label . '</option>';
                }
            echo '</select>';
            if ( ! empty( $args['description'] ) )
                echo ' <p class="description">' . $args['description'] . '</p>';
        }

        public function wpsocialite_checkbox( $args ){

            if ( empty( $args['name'] ) || ! is_array( $args['options'] ) )
                return false;

            $checked = ( isset( $args['name'] ) ) ? get_option($args['name']) : '';
            echo '<label for="' . esc_attr( $args['name'] ) . '">';
                foreach ( (array) $args['options'] as $value => $label ){
                    echo '<input name="' . esc_attr( $args['name'] ) . '" type="checkbox" id="' . esc_attr( $args['name'] ) . '" value="1"  '.checked($checked, 1, false).'> ' . $label;
                }
            echo '</label>';

        }

        public function wpsocialite_text_input( $args ){

            if ( empty( $args['name'] ) || ! is_array( $args['options'] ) )
                return false;

            $option_value = ( isset( $args['name'] ) ) ? get_option($args['name']) : '';

            echo '<label for="' . esc_attr( $args['name'] ) . '">';
                foreach ( (array) $args['options'] as $value => $label ){
                    echo '<input name="' . esc_attr( $args['name'] ) . '" type="text" id="' . esc_attr( $args['name'] ) . '" value="'.esc_attr($option_value).'" > ' . $label;
                }
            echo '</label>';
            if ( ! empty( $args['description'] ) )
                echo ' <p class="description">' . $args['description'] . '</p>';

        }

        public function wpsocialite_post_types() {

            $value = get_option('wpsocialite_post_types',array());
            if($value === ''){
                $value = array();
            }

            $post_types = get_post_types(array('public'=>true),'objects');

            foreach($post_types as $pt=>$ptobj){
                $checked = (in_array($pt, $value))?' checked="CHECKED"': '';
                echo '<label for="wpsocialite_post_type_'.$pt.'">
                        <input name="wpsocialite_post_types[]" type="checkbox" id="wpsocialite_post_type_'.$pt.'" value="'.$pt.'" '.$checked.' > '
                        .$ptobj->label.
                    '</label><br />';
            }

        }

        public function wpsocialite_networkoptions() {

            $value      = get_option('wpsocialite_networkoptions');
            $buttons    = $this->wpsocialite_list_network_options();
            $output     = '';

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
                '.$output;

        }

        public function wpsocialite_list_network_options( $link = null, $title = null, $size = null, $image = null ) {
            if( $image == '') { $image = null; } //link post featured image with Pinterest, if available
            $locale = get_locale();

            $twitter_title = trim($title);
            if (strlen($twitter_title) > 110){
                $twitter_title = substr($twitter_title, 0, 107)."...";
            }

            $twitter_username = get_option('wpsocialite_twitter_username');

            $buttons = array(
                'facebook' => array(
                    'name' => __('Facebook','wpsocialite'),
                    'slug' => 'facebook',
                    'markup_large' => '<a href="http://www.facebook.com/sharer.php?u='.$link.'&amp;locale='.$locale.'&amp;t='.$title.'" class="socialite facebook-like" data-lang="'.$locale.'" data-href="'.$link.'" data-send="false" data-layout="box_count" data-width="60" data-show-faces="false" rel="nofollow" target="_blank"><span class="vhidden">'.apply_filters('wpsocialite_share_facebook_label',__('Share on Facebook.','wpsocialite')).'</span></a>',
                    'markup_small' => '<a href="http://www.facebook.com/sharer.php?u='.$link.'&amp;locale='.$locale.'&amp;t='.$title.'" class="socialite facebook-like" data-lang="'.$locale.'" data-href="'.$link.'" data-send="false" data-layout="button_count" data-width="60" data-show-faces="false" rel="nofollow" target="_blank"><span class="vhidden">'.apply_filters('wpsocialite_share_facebook_label',__('Share on Facebook.','wpsocialite')).'</span></a>',
                    'external_file' => false
                ),
                'twitter-share' => array(
                    'name' => __('Twitter Share','wpsocialite'),
                    'slug' => 'twitter-share',
                    'markup_large' => '<a href="http://twitter.com/share" class="socialite twitter-share" data-text="'.$twitter_title.'" data-url="'.$link.'" data-count="vertical" data-lang="'.$locale.'" data-via="'.$twitter_username.'" rel="nofollow" target="_blank"><span class="vhidden">'.apply_filters('wpsocialite_share_twitter_label',__('Share on Twitter.','wpsocialite')).'</span></a>',
                    'markup_small' => '<a href="http://twitter.com/share" class="socialite twitter-share" data-text="'.$twitter_title.'" data-url="'.$link.'" data-count="horizontal" data-lang="'.$locale.'" data-via="'.$twitter_username.'" rel="nofollow" target="_blank"><span class="vhidden">'.apply_filters('wpsocialite_share_twitter_label',__('Share on Twitter.','wpsocialite')).'</span></a>',
                    'external_file' => false
                ),
                'gplus' => array(
                    'name' => __('Google Plus','wpsocialite'),
                    'slug' => 'gplus',
                    'markup_large' => '<a href="https://plus.google.com/share?url='.$link.'" class="socialite googleplus-one" data-size="tall" data-href="'.$link.'" rel="nofollow" target="_blank"><span class="vhidden">'.apply_filters('wpsocialite_share_googleplus_label',__('Share on Google+','wpsocialite')).'</span></a>',
                    'markup_small' => '<a href="https://plus.google.com/share?url='.$link.'" class="socialite googleplus-one" data-size="medium" data-href="'.$link.'" rel="nofollow" target="_blank"><span class="vhidden">'.apply_filters('wpsocialite_share_googleplus_label',__('Share on Google+','wpsocialite')).'</span></a>',
                    'external_file' => false
                ),
                'linkedin' => array(
                    'name' => __('Linkedin','wpsocialite'),
                    'slug' => 'linkedin',
                    'markup_large' => '<a href="http://www.linkedin.com/shareArticle?mini=true&amp;url='.$link.'&amp;title='.$title.'" class="socialite linkedin-share" data-url="'.$link.'" data-counter="top" rel="nofollow" target="_blank"><span class="vhidden">'.apply_filters('wpsocialite_share_linkedin_label',__('Share on LinkedIn','wpsocialite')).'</span></a>',
                    'markup_small' => '<a href="http://www.linkedin.com/shareArticle?mini=true&amp;url='.$link.'&amp;title='.$title.'" class="socialite linkedin-share" data-url="'.$link.'" data-counter="right" rel="nofollow" target="_blank"><span class="vhidden">'.apply_filters('wpsocialite_share_linkedin_label',__('Share on LinkedIn','wpsocialite')).'</span></a>',
                    'external_file' => false
                ),
                'pinterest' => array(
                    'name' => __('Pinterest','wpsocialite'),
                    'slug' => 'pinterest',
                    'markup_large' => '<a href="http://pinterest.com/pin/create/button/?url='.$link.'&amp;media=' . $image . '&amp;description='.$title.'" class="socialite pinterest-pinit" data-count-layout="vertical"><span class="vhidden">'.apply_filters('wpsocialite_share_pinterest_label',__('Pin It!','wpsocialite')).'</span></a>',
                    'markup_small' => '<a href="http://pinterest.com/pin/create/button/?url='.$link.'&amp;media=' . $image . '&amp;description='.$title.'" class="socialite pinterest-pinit" data-count-layout="horizontal"><span class="vhidden">'.apply_filters('wpsocialite_share_pinterest_label',__('Pin It!','wpsocialite')).'</span></a>',
                    'external_file' => plugin_dir_url(__FILE__).'Socialite/extensions/socialite.pinterest.js',

                ),
                'stumbleupon' => array(
                    'name' => __('StumbleUpon','wpsocialite'),
                    'slug' => 'stumbleupon',
                    'markup_large' => '<a href="http://www.stumbleupon.com/submit?url='.$link.'&amp;title='.$title.'" class="socialite stumbleupon-share" data-url="'.$link.'" data-title="'.$title.'" data-layout="5" rel="nofollow"><span class="vhidden">'.apply_filters('wpsocialite_share_stumbleupon_label',__('Share on StumbleUpon','wpsocialite')).'</span></a>',
                    'markup_small' => '<a href="http://www.stumbleupon.com/submit?url='.$link.'&amp;title='.$title.'" class="socialite stumbleupon-share" data-url="'.$link.'" data-title="'.$title.'" data-layout="1" rel="nofollow"><span class="vhidden">'.apply_filters('wpsocialite_share_stumbleupon_label',__('Share on StumbleUpon','wpsocialite')).'</span></a>',
                    'external_file' => plugin_dir_url(__FILE__).'Socialite/extensions/socialite.stumbleupon.js',
                ),
                'twitter-follow' => array(
                    'name' => __('Twitter Follow','wpsocialite'),
                    'slug' => 'twitter-follow',
                    'markup_large' => '<a href="http://twitter.com/'.$twitter_username.'" class="socialite twitter-follow" data-text="'.$twitter_title.'" data-url="'.$link.'" data-size="large" data-width="" data-lang="'.$locale.'" rel="nofollow" target="_blank"><span class="vhidden">'.apply_filters('wpsocialite_share_twitter_label',__('Share on Twitter.','wpsocialite')).'</span></a>',
                    'markup_small' => '<a href="http://twitter.com/'.$twitter_username.'" class="socialite twitter-follow" data-text="'.$twitter_title.'" data-url="'.$link.'" data-size="small" data-lang="'.$locale.'" data-via="" rel="nofollow" target="_blank"><span class="vhidden">'.apply_filters('wpsocialite_share_twitter_label',__('Share on Twitter.','wpsocialite')).'</span></a>',
                    'external_file' => false
                )
            );

            return $buttons;
        }

        public function wpsocialite_settings_link( $links, $file ) {
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

new wpsocialite;



/* Template Tags
=================================================================
*/
function get_wpsocialite_markup($args = array()){
    $wpsocialite = wpsocialite::wpsocialite_markup($args);
    return $wpsocialite;
}
function wpsocialite_markup($args = array()){
    $wpsocialite = get_wpsocialite_markup($args);
    echo $wpsocialite;
}