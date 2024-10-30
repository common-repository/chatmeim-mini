<?php
class Mini extends ChatMe {
        
    public function __construct() {
	add_action( 'plugins_loaded', array( $this, 'mini_init' ) );
        $this->resource             = $_SERVER['SERVER_NAME'];
    }
	
    function mini_init() {
        add_action('wp_enqueue_scripts',    array( $this, 'chatme_mini_wp_head') );
		add_filter('wp_resource_hints', 	array( $this, 'add_resource_hints'), 10, 2 );
        add_action('admin_menu',    		array( $this, 'chatme_mini_admin_menu') );
        add_action('admin_init',    		array( $this, 'chatme_mini_admin_init') );
    }
	
      	function chatme_mini_add_help_tab () {
          	$screen = get_current_screen();

          	$screen->add_help_tab( array(
              	      	'id'		=> 'chatme_mini_help_tab_1',
              	      	'title'		=> esc_html__('anonymous server', 'chatmeim-mini'),
              	      	'content'	=> '<p>' . esc_html__( 'The anonymous server of your XMPP service, default: anonymous.chatme.im', 'chatmeim-mini' ) . '</p>',
          	      	) );

          	$screen->add_help_tab( array(
              	      	'id'		=> 'chatme_mini_help_tab_2',
              	      	'title'		=> esc_html__('Chat rooms password', 'chatmeim-mini'),
              	      	'content'	=> '<p>' . esc_html__( 'This is the password of chat room where the user enter when click the chat button, remeber that', 'chatmeim-mini') . ' <b>' . esc_html__( 'password is visble in HTML code', 'chatmeim-mini') . '</b> ' . esc_html__( 'of page and', 'chatmeim-mini') . ' <b>' . esc_html__( 'it is not possible to hide it.', 'chatmeim-mini') . '</b></p>',
          	      	) );

          	$screen->set_help_sidebar(
                              '<p><strong>' . esc_html__('Other Resources', 'chatmeim-mini') . '</strong></p><p><a href="https://jappix.org/" target="_blank">' . esc_html__('Jappix Official Site', 'chatmeim-mini') . '</a></p><p><a href="https://github.com/jappix/jappix/wiki" target="_blank">' . esc_html__('Jappix Official Documentation', 'chatmeim-mini') . '</a></p><p><a href="http://xmpp.net" target="_blank">' . esc_html__('XMPP.net', 'chatmeim-mini') . '</a></p><p><a href="http://chatme.im" target="_blank">' . esc_html__('ChatMe Site', 'chatmeim-mini') . '</a></p>'
                             );
      	      	}

	function add_resource_hints($hints, $relation_type) {
		if ( 'dns-prefetch' === $relation_type ) {

			$bind_mini = (get_option('bind_mini') == '') ? parent::$default['bind_mini'] : get_option('bind_mini');

        		array_push($hints, $bind_mini);
		}
    		return $hints;
	}

    function chatme_mini_wp_head() {
	
        $current_user = wp_get_current_user();
		
		$setting = array(
				'jappix_url' 			=> esc_url(get_option('custom')),
				'anonymous'			=> esc_html(get_option('custom-server')),
				'adminjid'			=> esc_html(get_option('admin_site')),
				'dlng' 				=> esc_html(get_option('language')),
				'auto_login' 			=> esc_html(get_option('auto_login')),
	    			'animate' 			=> esc_html(get_option('animate')),
	    			'auto_show' 			=> esc_html(get_option('auto_show')),
				'default_room' 			=> esc_html(get_option('join_groupchats')),
				'nickname'			=> $current_user->display_name,	
				'resource'			=> $this->resource,
				'loggedonly'			=> esc_html(get_option('all')),		
				'style'				=> wp_kses(get_option('style'), ''),	
				'icon' 				=> esc_url(get_option('icon')),	
				'mini_disable_mobile' 		=> esc_html(get_option('mini_disable_mobile')),	
				'priority'			=> esc_html(get_option('priority')),
				'open_passwords'		=> wp_kses(get_option('open_passwords'),''),
				'bind_mini' 			=> wp_kses(get_option('bind_mini'),''),
						);
						
		foreach( $setting as $k => $settings )
			if( false == $settings )
				unset( $setting[$k]);
						
		$actual = apply_filters( 'chat_actual', wp_parse_args( $setting, parent::$default ) );
		
		if (!$actual['loggedonly'] || is_user_logged_in()) {

			wp_register_style( 'chatmemini', plugins_url( '/mini/stylesheets/mini.css', __FILE__ ), array(), parent::$default['version'] );
			wp_enqueue_style( 'chatmemini' );

			wp_register_script( 'chatmemini', plugins_url( '/mini/javascripts/mini.js', __FILE__ ), array('jquery'), parent::$default['version'], true );
			wp_enqueue_script( 'chatmemini' );

			$chat_css = '
				' . $actual['style'] . '
    			#jappix_mini .jm_images_animate { background-image: url(\'' . $actual['icon'] . '\') !important; background-repeat: no-repeat;}
			';

			$chat_html = '
				jQuery(document).ready(function() {
					HOST_BOSH_MINI = "' . $actual['bind_mini'] . '";
        			ANONYMOUS = "on";

        			JappixMini.launch({
            			connection: {
                			domain: "' . $actual['anonymous'] . '",
							resource: "' . $actual['resource'] . '",
							priority: ' . $actual['priority'] . ',
           				},

            			application: {
                			network: {
                    			autoconnect: ' . $actual['auto_login'] . ',
                		},

                		interface: {
                    		showpane: true,
                    		animate: ' . $actual['animate'] . ',
                    		no_mobile: ' . $actual['mini_disable_mobile'] . ',
                    		error_link: "' . $actual['mini_error_link'] . '",
                		},

                		user: {
                    		random_nickname: false,
                    		nickname: "' . $actual['nickname'] . '",
                		},

                		chat: {
                    		suggest: ["' . $actual['adminjid'] . '"],
                		},

                		groupchat: {
                    		open: ["' . $actual['default_room'] . '"],
		    				open_passwords: ["' . $actual['open_passwords'] . '"],
                    		suggest: ["piazza@conference.chatme.im","support@conference.chatme.im"],
                		},
            		},
        		});
    		});
			';

			$inline_chat = apply_filters( 'chat_html', $chat_html );
			$inline_chat_css = apply_filters( 'chat_html_css', $chat_css );

			wp_add_inline_script( 'chatmemini', $inline_chat );
			wp_add_inline_style( 'chatmemini', $inline_chat_css );

		}

	}

    function chatme_mini_admin_menu() {
        $my_admin_page = add_options_page( esc_html__('ChatMe Mini Options', 'chatmeim-mini'), esc_html__('ChatMe Mini', 'chatmeim-mini'), 'manage_options', parent::$default['plugin_options_key'], array($this, 'chatme_mini_options') );
        add_action('load-'.$my_admin_page, array( $this, 'chatme_mini_add_help_tab') );
    }

    function chatme_mini_admin_init() {
		//register our settings
		register_setting('mini_chat', 'custom');
		register_setting('mini_chat', 'custom-server');
		register_setting('mini_chat', 'language');
		register_setting('mini_chat', 'auto_login');
		register_setting('mini_chat', 'auto_show');
		register_setting('mini_chat', 'animate');
		register_setting('mini_chat', 'join_groupchats');
		register_setting('mini_chat', 'admin_site');
       		register_setting('mini_chat', 'all');
        	register_setting('mini_chat', 'style');
        	register_setting('mini_chat', 'icon'); 
       		register_setting('mini_chat', 'mini_disable_mobile');     
       		register_setting('mini_chat', 'priority');   
        	register_setting('mini_chat', 'open_passwords');   
        	register_setting('mini_chat', 'bind_mini');
    }

    function chatme_mini_options() {
        if (!current_user_can('manage_options'))  {
        wp_die( esc_html__('You do not have sufficient permissions to access this page.', 'chatmeim-mini') );
    }
?>
 <div class="wrap">
<h1>ChatMe Mini</h1>
<p><?php esc_html_e('For more information visit', 'chatmeim-mini'); ?> <a href='http://www.chatme.im' target='_blank'><?php esc_html_e('www.chatme.im', 'chatmeim-mini'); ?></a> - <a href="https://webchat.chatme.im/?r=support" target="_blank"><?php esc_html_e('Support Chat Room', 'chatmeim-mini'); ?></a> - <a href="http://chatme.im/prodotto/servizio-im-proprio-dominio/" target="_blank"><?php esc_html_e('Enable Chat service in your domain', 'chatmeim-mini'); ?></a></p>
<p><?php esc_html_e('For subscribe your account visit', 'chatmeim-mini'); ?> <a href='http://chatme.im/servizi/domini-disponibili/' target='_blank'><?php esc_html_e('http://chatme.im/servizi/domini-disponibili/', 'chatmeim-mini'); ?></a></p>

<form method="post" action="options.php">
    <?php settings_fields( 'mini_chat' ); ?>
    <table class="form-table">

		<!-- <tr valign="top">
        <th scope="row"><label for="custom"><?php esc_html_e('Insert a custom Jappix Installation url', 'chatmeim-mini'); ?></label></th>
        <td><input class="regular-text" aria-describedby="custom-description" type="url" size="50" id="custom" name="custom" placeholder="<?php esc_html_e('https://webchat.chatme.im', 'chatmeim-mini'); ?>" value="<?php echo get_option('custom'); ?>" /> /server/get.php...<p class="description" id="custom-description"><?php esc_html_e('Insert your Jappix installation URL', 'chatmeim-mini'); ?></p></td>
        </tr> -->

		<tr valign="top">
        <th scope="row"><label for="bind_mini"><?php esc_html_e('Insert a custom Bind Server URL', 'chatmeim-mini'); ?></label></th>
        <td><input class="regular-text" aria-describedby="bind_mini-description" type="text" size="50" id="bind_mini" name="bind_mini" placeholder="<?php esc_html_e('https://bind.chatme.im', 'chatmeim-mini'); ?>" value="<?php echo get_option('bind_mini'); ?>" /><p class="description" id="bind_mini-description"><?php esc_html_e('If you use a local XMPP server or a XMPP without SRV record in DNS you must enter a custom bind server', 'chatmeim-mini'); ?></p></td>
        </tr>

		<tr valign="top">
        <th scope="row"><label for="custom-server"><?php esc_html_e('Insert your custom anonymous server', 'chatmeim-mini'); ?></label></th>
        <td><input class="regular-text" type="text" id="custom-server" name="custom-server" placeholder="<?php esc_html_e('anonymous.chatme.im', 'chatmeim-mini'); ?>" value="<?php echo get_option('custom-server'); ?>" /></td>
        </tr>
            
        <tr valign="top">
        <th scope="row"><label for="auto_login"><?php esc_html_e('Auto login to the account', 'chatmeim-mini'); ?></label></th>
        <td><input type="checkbox" id="auto_login" name="auto_login" value="true" <?php checked('true', get_option('auto_login')); ?> /></td>
        </tr>
		
		<tr valign="top">
        <th scope="row"><label for="auto_show"><?php esc_html_e('Auto show the opened chat', 'chatmeim-mini'); ?></label></th>
        <td><input type="checkbox" id="auto_show" name="auto_show" value="true" <?php checked('true', get_option('auto_show')); ?> /></td>
        </tr>

		<tr valign="top">
        <th scope="row"><label for="animate"><?php esc_html_e('Display an animated image when the user is not connected', 'chatmeim-mini'); ?></label></th>
        <td><input type="checkbox" id="animate" name="animate" value="true" <?php checked('true', get_option('animate')); ?> /><br />
	<input class="regular-text" aria-describedby="animate-description" type="url" size="50" name="icon" placeholder="<?php esc_html_e('Custom Icon URL', 'chatmeim-mini'); ?>" value="<?php echo get_option('icon'); ?>" /><p class="description" id="animate-description"><?php esc_html_e('Insert your custom icon url, default: /wp-content/plugins/chatmeim-mini/images/chat-mini.png size: 80x74 px', 'chatmeim-mini'); ?></p>
	</td>
        </tr>
		
		<tr valign="top">
        <th scope="row"><label for="join_groupchats"><?php esc_html_e('Chat rooms to join (if any)', 'chatmeim-mini'); ?></label></th>
        <td><input aria-describedby="join_groupchats-description" class="regular-text" type="text" id="join_groupchats" name="join_groupchats" placeholder="<?php esc_html_e('piazza@conference.chatme.im', 'chatmeim-mini'); ?>" value="<?php echo get_option('join_groupchats'); ?>" /><p class="description" id="join_groupchats-description"><?php esc_html_e('For create a Chat Room use Desktop', 'chatmeim-mini'); ?> <a href="http://chatme.im/elenco-client/" target="_blank"><?php esc_html_e('Client', 'chatmeim-mini'); ?></a> <?php esc_html_e('or go to', 'chatmeim-mini'); ?> <a href="https://conference.chatme.im/chat.php" target="_blank"><?php esc_html_e('Here.', 'chatmeim-mini'); ?></a></p></td>
        </tr>

		<tr valign="top">
        <th scope="row"><label for="open_passwords"><?php esc_html_e('Chat rooms password', 'chatmeim-mini'); ?></label></th>
        <td><input aria-describedby="open_passwords-description" class="regular-text" type="password" id="open_passwords" name="open_passwords" placeholder="<?php esc_html_e('Chat Room Password', 'chatmeim-mini'); ?>" value="<?php echo wp_kses(get_option('open_passwords'),''); ?>" /><p class="description" id="open_passwords-description"><?php esc_html_e('The password of Chat Room, please attention the password is visible in HTML code ', 'chatmeim-mini'); ?></p></td>
        </tr>
        
        <tr valign="top">
	    <th scope="row"><label for="admin_site"><?php esc_html_e('Chat with site admin', 'chatmeim-mini'); ?></label></th>
	    <td><input class="regular-text" type="text" id="admin_site" name="admin_site" placeholder="<?php esc_html_e('admin', 'chatmeim-mini'); ?><?php echo parent::$default['chat']; ?>" value="<?php echo get_option('admin_site'); ?>" /> </td>
	    </tr>        

		<tr valign="top">
        <th scope="row"><label for="all"><?php esc_html_e('Available only for logged users', 'chatmeim-mini'); ?></label></th>
        <td><input type="checkbox" id="all" name="all" value="true" <?php checked('true', get_option('all')) ?> /></td>
        </tr>

		<tr valign="top">
        <th scope="row"><label for="mini_disable_mobile"><?php esc_html_e('Hide for mobile user', 'chatmeim-mini'); ?></label></th>
        <td><input type="checkbox" id="mini_disable_mobile" name="mini_disable_mobile" value="true" <?php checked('true', get_option('mini_disable_mobile')) ?> /></td>
        </tr>

        <tr valign="top">
        <th scope="row"><label for="priority"><?php esc_html_e('Priority', 'chatmeim-mini'); ?></label></th>
        <td>
        	<select id="priority" name="priority">
        		<option value="1" <?php selected('1', get_option('priority')); ?>><?php esc_html_e('Low', 'chatmeim-mini'); ?></option>
        		<option value="10" <?php selected('10', get_option('priority')); ?>><?php esc_html_e('Medium', 'chatmeim-mini'); ?></option>
        		<option value="100" <?php selected('100', get_option('priority')); ?>><?php esc_html_e('Height', 'chatmeim-mini'); ?></option>
       		</select>
        </td>
        </tr>

        <!-- <tr valign="top">
        <th scope="row"><label for="language"><?php esc_html_e('Mini Jappix language', 'chatmeim-mini'); ?></label></th>
        <td>
        <select id="language" name="language">
        <?php
        $auto_lng = (in_array(substr(get_locale(), 0, 2), parent::$default['lngarray'])) ? substr(get_locale(), 0, 2) : parent::$default['dlng'];
        ?>
        <option value="<?php echo $auto_lng ?>"><?php esc_html_e('Some of Site', 'chatmeim-mini'); ?></option>
        <option value="de" <?php selected('de', get_option('language')); ?>><?php esc_html_e('Deutsch', 'chatmeim-mini'); ?></option>
        <option value="en" <?php selected('en', get_option('language')); ?>><?php esc_html_e('English', 'chatmeim-mini'); ?></option>
        <option value="eo" <?php selected('eo', get_option('language')); ?>><?php esc_html_e('Esperanto', 'chatmeim-mini'); ?></option>
        <option value="es" <?php selected('es', get_option('language')); ?>><?php esc_html_e('Espa&ntilde;ol', 'chatmeim-mini'); ?></option>
        <option value="fr" <?php selected('fr', get_option('language')); ?>><?php esc_html_e('Fran&ccedil;ais', 'chatmeim-mini'); ?></option>
        <option value="it" <?php selected('it', get_option('language')); ?>><?php esc_html_e('Italiano', 'chatmeim-mini'); ?></option>
        <option value="ja" <?php selected('ja', get_option('language')); ?>><?php esc_html_e('Japan', 'chatmeim-mini'); ?></option>
        <option value="nl" <?php selected('nl', get_option('language')); ?>><?php esc_html_e('Nederlands', 'chatmeim-mini'); ?></option>
        <option value="pl" <?php selected('pl', get_option('language')); ?>><?php esc_html_e('Polski', 'chatmeim-mini'); ?></option>
        <option value="ru" <?php selected('ru', get_option('language')); ?>><?php esc_html_e('Russian', 'chatmeim-mini'); ?></option>
        <option value="sv" <?php selected('sv', get_option('language')); ?>><?php esc_html_e('Svenska', 'chatmeim-mini'); ?></option>
        <option value="hu" <?php selected('hu', get_option('language')); ?>><?php esc_html_e('Hungarian', 'chatmeim-mini'); ?></option>
        </select>
        </td>
        </tr> -->

	<tr valign="top">
        	<th scope="row"><label for="style"><?php esc_html_e('Custom Style', 'chatmeim-mini'); ?></label></th>
        	<td><textarea class="large-text code" aria-describedby="style-description" id="style" name="style" rows="4" cols="50"><?php echo wp_kses(get_option('style'),''); ?></textarea><br /> <p class="description" id="style-description"><?php esc_html_e('For Advance use try chat_html and chat_html_css hook', 'chatmeim-mini') ?></p></td>
        </tr>

    </table>
    <?php submit_button(); ?>
    </form>
    
        <p><?php esc_html_e('For Ever request you can use our', 'chatmeim-mini') ?> <a href="http://chatme.im/forums" target="_blank"><?php esc_html_e('forum', 'chatmeim-mini') ?></a></p>

<h3 class="title"><?php esc_html_e('Donation', 'chatmeim-mini') ?></h3>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="8CTUY8YDK5SEL">
<input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal -  The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypalobjects.com/it_IT/i/scr/pixel.gif" width="1" height="1">
</form>
</div>
<?php 
    }
} 
$Mini = new Mini();
?>