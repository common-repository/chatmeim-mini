<?php
class ShortCodes extends ChatMe {
	
	function __construct() {
		self::register_shortcodes( $this->shortcodes_core() );
		add_action('admin_menu',  array( $this, 'chatme_shortcode_menu') );
	}

	private function shortcodes_core() {
		$core = array(
			'userStatus'			=>	array( 'function' => 'userStatus_short' ),
			'chatRoom'			    =>	array( 'function' => 'chatRoom_short' ),
			'chatRoomIframe'		=>	array( 'function' => 'chatRoomIframe_short' ),
			'swatchTime'			=>	array( 'function' => 'swatchTime_short' ),
			);
		return $core;
	}
	
	function chatme_shortcode_menu() {
  		$my_admin_page = add_options_page( esc_html__('ChatMe Shortocode Help', 'chatmeim-mini'), esc_html__('ChatMe Shortcode Help', 'chatmeim-mini'), 'manage_options', parent::$default['plugin_options_short'], array($this, 'mini_shortcode_help') );
	}
	
function mini_shortcode_help() {
  		if (!current_user_can('manage_options'))  {
    	wp_die( esc_html__('You do not have sufficient permissions to access this page.', 'chatmeim-mini-messenger') );
  		} 
	?>
 	<div class="wrap">
	<h1><?php esc_html_e('ChatMe Shortocode Help', 'chatmeim-mini'); ?></h1>
	<p><b>[userStatus user="users" link=1 hosted=0]</b><br/><?php esc_html_e('This code show user status (online/offline/etc):', 'chatmeim-mini'); ?><ul><li><b><?php esc_html_e('user', 'chatmeim-mini'); ?></b>: <?php esc_html_e('insert the user with the domain (example: user@chatme.im)', 'chatmeim-mini'); ?></li><li><b><?php esc_html_e('link', 'chatmeim-mini'); ?></b> <?php esc_html_e('(boolean): can be 0 (default) for not link and 1 for link to the user', 'chatmeim-mini'); ?></li></ul></p> 
	<p><b>[chatRoom anon=1]</b><br/><?php esc_html_e('This code show a list of default chat room.', 'chatmeim-mini'); ?><ul><li><b><?php esc_html_e('anon', 'chatmeim-mini'); ?></b> <?php esc_html_e('(boolean): can be 0 for not anonymous login (require username and password) or 1 (default) for chat only with nickname.', 'chatmeim-mini'); ?></li></ul></p> 
	<p><b>[chatRoomIframe room="room" width="width" height="height" hosted=0]</b><br/><?php esc_html_e('This shortcode show a chat room in your wordpress page:', 'chatmeim-mini'); ?><ul><li><b><?php esc_html_e('room', 'chatmeim-mini'); ?></b>: <?php esc_html_e('the name of the chat room (default: piazza@conference.chatme.im)', 'chatmeim-mini'); ?></li><li><b><?php esc_html_e('width', 'chatmeim-mini'); ?></b>: <?php esc_html_e('the frame width (default: 100%)', 'chatmeim-mini'); ?></li><li><b><?php esc_html_e('height', 'chatmeim-mini'); ?></b>: <?php esc_html_e('the height of frame (default: 100%)', 'chatmeim-mini'); ?></li><li><b><?php esc_html_e('hosted', 'chatmeim-mini'); ?></b> <?php esc_html_e('(boolean): can be 0 (default) for not hosted domain and 1 if you have a custom domain hosted in ChatMe XMPP server', 'chatmeim-mini'); ?></li></ul></p> 
	<p><b>[swatchTime]</b><br/><?php esc_html_e('This shortcode show Internet Swatch Time.', 'chatmeim-mini'); ?></p> 
	<p><?php esc_html_e('For more information visit our', 'chatmeim-mini'); ?> <a href="http://chatme.im/forums" target="_blank"><?php esc_html_e('forum', 'chatmeim-mini'); ?></a></p> 

	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
		<input type="hidden" name="cmd" value="_s-xclick">
		<input type="hidden" name="hosted_button_id" value="8CTUY8YDK5SEL">
		<input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal The safer, easier way to pay online.">
		<img alt="" border="0" src="https://www.paypalobjects.com/it_IT/i/scr/pixel.gif" width="1" height="1">
	</form>

	<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://chatme.im" data-text="Visita chatme.im" data-via="chatmeim" data-lang="it">Tweet</a>
	<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>

	</div>
<?php 
	}		

    //Stato utente [userStatus user="users" link="1"]
    function userStatus_short($atts)
	    {	
		    $defaults = array(
			    'user'      => parent::$default['userStatus_user'],
			    'link'      => parent::$default['userStatus_link'],
			    );
            $atts = shortcode_atts( $defaults, $atts );

            $dominio = explode('@',$atts['user']);
            $url_status = ChatMeApi::getHost($dominio[1],'url-status');    
                
            $link = ((bool)$atts['link']) ? ' <a href="xmpp:'. $atts['user'] . '" title="' . esc_html__('Chat with', 'chatmeim-mini') . ' ' . $atts['user'] . '">' . $atts['user'] . '</a>' : '';
            
            return '<img src="' . $url_status . $atts['user'] . '" alt="Status">' . $link;		

	    }	
	
    //Chat Room [chatRoom anon="1"]	
    function chatRoom_short($atts)
	    {
		    $defaults = array(
			    'anon' => parent::$default['chatRoom_anon'],
			    );
            $atts = shortcode_atts( $defaults, $atts );    
                
		    if (!(bool)$atts['anon'])  {	
                
		    return '<form method="get" action="' . parent::$default['muc_url'] . '" target="_blank" class="form-horizontal">
            	    <select name="room">
					    ' . parent::$default['room'] . '
				    </select>
                <button type="submit">' . esc_html__('Login to the room', 'chatmeim-mini') . '</button>
            </form> ';
		    } else {
                
		    return '<form method="get" action="' . parent::$default['jappix_url'] . '" target="_blank">
            	    <select name="r">
					    ' . parent::$default['room'] . '
				    </select>
    			    <input type="text" name="n" placeholder="' . esc_html__('Nickname', 'chatmeim-mini') . '" autocomplete="off">
        	    <button type="submit">' . esc_html__('Login to the room', 'chatmeim-mini') . '</button>
            </form> ';
		    }
	    }

    //Iframe Chat Room [chatRoomIframe room="room" width="width" height="height"]
    function chatRoomIframe_short($atts)
	    {	
		    $defaults = array(
			    'room' 		=> parent::$default['chatRoomIframe_room'],
			    'width' 	=> parent::$default['chatRoomIframe_width'],
			    'height' 	=> parent::$default['chatRoomIframe_height'],
			    'hosted' 	=> parent::$default['chatRoomIframe_hosted'],
				'powered' 	=> parent::$default['chatRoomIframe_powered'],
			    );
                $atts = shortcode_atts( $defaults, $atts );
                
				$chat_url = ((bool)$atts['hosted']) ? parent::$default['chat_domains'] : parent::$default['jappix_url'];
				$powered = ((bool)$atts['powered']) ? parent::$default['chat_powered'] : '';
				
				return '<div class="cm-iframe-room"><iframe src="' . $chat_url . '/?r='. $atts['room'] . parent::$default['conference_domain'] . '" width="' . $atts['width'] . '" height="' . $atts['height'] . '" border="0">' . esc_html__('Iframe not supported from browser', 'chatmeim-mini') . '</iframe>' . $powered . '</div>';		
	    }

    //Internet Swatch Time [swatchTime]
    function swatchTime_short()
	    {	
		return esc_html__('Internet Time Swatch', 'chatmeim-mini') . ' <strong>@' . date('B') . '</strong>';
	    }

    //Registro tutti gli shortcode della classe
	    private function register_shortcodes( $shortcodes ) {
		    foreach ( $shortcodes as $shortcode => $data ) {
			    add_shortcode( $shortcode, array( $this, $data['function']) );
		    }
	    }

}
	
new ShortCodes;		
?>