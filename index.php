<?php

/*
Plugin Name: Cyklodev WP Notify
Plugin URI: http://www.cyklodev.com/wordpress-notify/
Description: Cyklodev WP Notify
Author: Zephilou
Version: 1.2.1
Author URI: http://www.cyklodev.com
*/

defined('ABSPATH') or die("Cannot access pages directly.");  

/*
 * Clean string
 */

if(!function_exists(ckd_esc)){
    function ckd_esc($string) {
        $result = preg_replace( '/[^a-zA-Z\ 0-9_\-\[\]\(\)\,\.\!\?]/', '', $string );
        return $result;
    }
}

/*
 * Load text files
 */

function cyklodev_notify_load_text_domain() {
    
 if(get_bloginfo('language') == 'fr_FR'){
     $ckd_lang = 'fr';
 } else {
     $ckd_lang = 'en';
 }
    
    
 $path = dirname( plugin_basename( __FILE__ ) ) . '/languages-'.$ckd_lang.'/';
 load_plugin_textdomain( 'cyklodev', null, $path );
 
}
add_action( 'init', 'cyklodev_notify_load_text_domain' );

/*
 * Add link menu 
 */

function com_cyklodev_wordpress_notify(){
  add_menu_page('Cyklodev Notify', 'Cyklodev Notify', 'manage_options', 'cyklodev_notify', 'cyklodev_notify');
  add_submenu_page('cyklodev_notify',"Twitter","Twitter", 'manage_options' , 'cyklodev_notify_twitter', 'cyklodev_notify_twitter');
}
add_action('admin_menu', 'com_cyklodev_wordpress_notify');

/*
 * Include view
 */

function cyklodev_notify(){
    echo '<div class="wrap"><h2>'.__('Cyklodev Notification','cyklodev').'</h2>';
    include_once 'views/notify.php';
}

function cyklodev_notify_twitter(){
    echo '<div class="wrap"><h2>'.__('Cyklodev Notification Twitter','cyklodev').'</h2>';
    include_once 'views/twitter.php';
}


function cyklodev_notify_add_link ( $actions, $post ){
    if ( get_post_status( $post ) == 'publish' )
    {
        $nonce = wp_create_nonce( 'quick-publish-action' ); 
        $link = admin_url( "admin.php?page=cyklodev_notify&update_id={$post->ID}" );
        $actions['share'] = "<a href='$link'>".__('Notifier','cyklodev')."</a>";
        
        $options_list = array (
            'cyklodev_notify_twitter_consumer_secret'          => 'Twitter consumer secret',
            'cyklodev_notify_twitter_consumer_key'             => 'Twitter consumer key',
            'cyklodev_notify_twitter_access_token'             => 'Twitter access token',
            'cyklodev_notify_twitter_access_token_secret'      => 'Twitter access token secret'
        );

        $twitter_settings_complete = 1;
        foreach ($options_list as $k => $v) {
            if(get_option($k) == ''){
                $twitter_settings_complete = 0;
            }
        }

        if($twitter_settings_complete == 1){
            $link = admin_url( "admin.php?page=cyklodev_notify&update_id={$post->ID}&twitter=twitting" );
            $actions['tweet'] = "<a href='$link'>".__('Twitter','cyklodev')."</a>";
        }
    }
    return $actions;
}
add_filter( 'post_row_actions',cyklodev_notify_add_link, 10, 2 ) ;

/*
 * Add metabox
 */

function cyklodev_redirect_notify(){
    if(is_numeric($_GET['post'])){
        if(get_post_status($_GET['post']) == 'publish'){
            echo '<center><a href="admin.php?page=cyklodev_notify&update_id='.$_GET['post'].'" class="button">'.__('Notifier','cyklodev').'</a></center>';
            $options_list = array (
                'cyklodev_notify_twitter_consumer_secret'          => 'Twitter consumer secret',
                'cyklodev_notify_twitter_consumer_key'             => 'Twitter consumer key',
                'cyklodev_notify_twitter_access_token'             => 'Twitter access token',
                'cyklodev_notify_twitter_access_token_secret'      => 'Twitter access token secret'
            );

            $twitter_settings_complete = 1;
            foreach ($options_list as $k => $v) {
                if(get_option($k) == ''){
                    $twitter_settings_complete = 0;
                }
            }

            if($twitter_settings_complete == 1){
                echo '<br /><center><a href="admin.php?page=cyklodev_notify&update_id='.$_GET['post'].'&twitter=twitting" class="button">'.__('Tweet it','cyklodev').'</a></center>';
            }
        } else {
            _e("Publiez d'abord votre article ;)",'cyklodev');
        }
    } else {
        _e("Publiez d'abord votre article ;)",'cyklodev');
        return false;
    }

    
}

function cyklodev_add_post_meta_boxes() {

	add_meta_box(
		'cyklodev-post-class',			
		esc_html__( 'Cyklodev notify', 'cyklodev' ),
		'cyklodev_redirect_notify',		
		'post',					
		'side',					
		'high'					
	);
}
add_action( 'add_meta_boxes', 'cyklodev_add_post_meta_boxes' );

?>