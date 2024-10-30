<?php
/*
Plugin Name: ChatMe Mini
Plugin URI: http://www.chatme.im/
Description: This plugin add the javascript code for ChatMe Mini a Jabber/XMPP group chat for your WordPress. Also add ChatMe Shortcode and Widget for more chat integrations.
Version: 5.6.1
Author: camaran
Author URI: http://www.chatme.im
Text Domain: chatmeim-mini
Domain Path: /languages/
*/

require 'includes/class.chatmini.php';
require 'includes/class.shortcode.php';
require 'includes/class.login_widget.php';
require 'includes/class.status_widget.php';
require 'includes/chatmeapi.php';

class ChatMe {

        static protected $default = array(
    		'chatme_cache' 				=> 'true',
    		'version' 					=> '5.6.1',
    		'jappix_url' 				=> 'https://oldwebchat.chatme.im',
		'bind_mini'					=> 'https://bind.chatme.im',
		'chat' 						=> '@chatme.im',
		'anonymous'					=> 'anonymous.chatme.im',
		'default_room' 				=> 'piazza@conference.chatme.im',
		'adminjid'					=> 'admin@chatme.im',
		'dlng' 						=> 'en',
		'language_dir'				=> '/languages/',
		'style'						=> '#jappix_popup { z-index:99999 !important }',
		'auto_login' 				=> 'false',
	    	'animate' 					=> 'false',
	    	'auto_show' 				=> 'false',
			'nickname'	    			=> '',
			'loggedonly'				=> false,
			'icon'						=> '/wp-content/plugins/chatmeim-mini/images/chat-mini.png',
			'plugin_options_key'    	=> 'chatme-mini',
			'plugin_options_short'		=> 'chatme-shortcode',
			'mini_error_link' 	    	=> 'https://chatme.im/forums/?chatmeim-mini',
			'mini_disable_mobile' 		=> 'false',
			'priority'					=> 1,
			'open_passwords'			=> '',
			'chat_domains' 		        => 'https://webchat.domains',
			'muc_url' 		            => 'https://conference.chatme.im',
			'conference_domain' 	    => '@conference.chatme.im',	
			'room' 					    => '<option value="piazza@conference.chatme.im">Piazza</option>
									        <option value="support@conference.chatme.im">Support</option>
									        <option value="rosolina@conference.chatme.im">Rosolina</option>
									        <option value="politica@conference.chatme.im">Politica</option>',
    		'chat_powered' 			    => '<div><small>Chat powered by <a href="https://chatme.im" target="_blank">ChatMe</a></small></div>',  
            //Default Variables
            //userStatus
        	'userStatus_user'           => 'admin@chatme.im',
        	'userStatus_hosted'    	    => false,
        	'userStatus_link'      	    => false,   
            //chatRoom    
            'chatRoom_anon' 		    => true,
            //chatRoomIframe
            'chatRoomIframe_room'       => 'piazza',
            'chatRoomIframe_width'	    => '100%',
            'chatRoomIframe_height'     => '100%',
            'chatRoomIframe_hosted' 	=> false,
            'chatRoomIframe_powered'    => true,
            //languages array
            'lngarray'                  => array('de', 'en', 'eo', 'es', 'fr', 'it', 'ja', 'nl', 'pl', 'ru', 'su', 'hu'),

			);
			
    public function __construct() {
	add_action( 'plugins_loaded', array( $this, 'chatme_mini_init' ) );
	}
	
    function chatme_mini_init() {
	add_filter('plugin_action_links_' . plugin_basename(__FILE__), 	array( $this, 'add_action_chatme_mini_links') );
        load_plugin_textdomain( 'chatmeim-mini');
    }

    function add_action_chatme_mini_links ( $links ) {
    	$mylinks = array( '<a href="' . admin_url( 'options-general.php?page=' . self::$default['plugin_options_key'] ) . '">' . esc_html__( 'Settings', 'chatmeim-mini' ) . '</a>', '<a href="' . admin_url( 'options-general.php?page=' . self::$default['plugin_options_short'] ) . '">' . esc_html__( 'Shortcode', 'chatmeim-mini' ) . '</a>', );
    	return array_merge( $links, $mylinks );
    }
}
$ChatMe = new ChatMe();
?>