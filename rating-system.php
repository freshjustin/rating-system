<?php
/**
* Plugin Name: Rating System
* Plugin URI: http://github.com/VortexThemes/rating-system
* Description: The simple way to add like or dislike buttons.
* Version: 1.0
* Author: VortexThemes
* Author URI: https://github.com/VortexThemes
* License: GPL2
* Text Domain: vortex_system_ld
* Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) exit;//exit if accessed directly

//activation hook
require_once(plugin_dir_path( __FILE__ ).'activation.php');
//tgmpa
require_once(plugin_dir_path( __FILE__).'tgmpa/class-tgm-plugin-activation.php');
add_action( 'tgmpa_register', 'vortex_register_plugin' );
function vortex_register_plugin() {

	$plugins = array(
		array(
			'name'      => 'Redux Framework',
			'slug'      => 'redux-framework',
			'required'  => true,
		),
	);

	$config = array(
		'id'           => 'vortex-tgmpa',                 // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => 'vortex-install-plugins', // Menu slug.
		'parent_slug'  => 'plugins.php',            // Parent menu slug.
		'capability'   => 'edit_plugins',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => false,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => __('Fix this for Rating System to work','vortex_system_ld'), // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => false,                   // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.
	);

	tgmpa( $plugins, $config );
}

//require all usefull stuffs
function vortex_systen_main_function(){
	if(function_exists('is_plugin_active')){
		if ( is_plugin_active( 'redux-framework/redux-framework.php' ) ) {
			load_plugin_textdomain( 'vortex_system_ld', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );	
			
			if ( !class_exists( 'ReduxFramework' ) && file_exists( dirname(dirname(__FILE__)).'/redux-framework/ReduxCore/framework.php' ) ) {
				require_once( dirname(dirname(__FILE__)).'/redux-framework/ReduxCore/framework.php' );
			};
			if ( !isset( $vortex_like_dislike ) && file_exists( dirname( __FILE__ ) . '/admin/vortex-like-dislike.php' ) ) {
				require_once( plugin_dir_path( __FILE__ ) . '/admin/vortex-like-dislike.php' );
			};
			//donation button
			function vortex_system_donation_button(){
				echo '<form style="width:260px;margin:0 auto;" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="VVGFFVJSFVZ7S">
					<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
					<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
					</form>
					';
			}
			add_action('redux/vortex_like_dislike/panel/before','vortex_system_donation_button');
			add_action('redux/vortex_like_dislike/panel/after','vortex_system_donation_button');
				
			Redux::init('vortex_like_dislike');
			
			global $vortex_like_dislike;
			require_once( plugin_dir_path( __FILE__ ).'/widget/widget.php' );
			require_once( plugin_dir_path( __FILE__ ).'/widget/dashboard-widget.php' );
			
			if($vortex_like_dislike['v-switch-posts'] && isset($vortex_like_dislike['v-switch-posts'])){
				require_once(plugin_dir_path( __FILE__ ).'posts-pages.php');
			}
			
			if($vortex_like_dislike['v-switch-comments'] && isset($vortex_like_dislike['v-switch-comments'])){
				require_once(plugin_dir_path( __FILE__ ).'comments.php');
			}
			
			//add custom fields when comment is submited
			add_action('comment_post', 'vortex_system_add_likes_dislikes_comments');
			function vortex_system_add_likes_dislikes_comments($comment_ID) {
				global $vortex_like_dislike;
				$likes = 0;
				$dislikes = 0;
				
				if(isset($vortex_like_dislike['v_start_comment_like'])){
					$likes = $vortex_like_dislike['v_start_comment_like'];
				}
				
				if(isset($vortex_like_dislike['v_start_comment_dislike'])){
					$dislikes = $vortex_like_dislike['v_start_comment_dislike'];
				}
				add_comment_meta($comment_ID, 'vortex_system_likes', $likes, true);
				add_comment_meta($comment_ID, 'vortex_system_dislikes', $dislikes, true);
			}

			//add custom fields when post is published
			add_action( 'publish_post', 'post_published_notification' );
			function post_published_notification( $ID ) {
				global $vortex_like_dislike;
				$likes = 0;
				$dislikes = 0;
				
				if(isset($vortex_like_dislike['v_start_post_like'])){
					$likes = $vortex_like_dislike['v_start_post_like'];
				}
				
				if(isset($vortex_like_dislike['v_start_post_dislike'])){
					$dislikes = $vortex_like_dislike['v_start_post_dislike'];
				}
				add_post_meta($ID, 'vortex_system_likes', $likes, true);
				add_post_meta($ID, 'vortex_system_dislikes', $dislikes, true);
			}
		}
	}
}

add_action('plugins_loaded','vortex_systen_main_function');