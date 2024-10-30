<?php

class chatme_login_Widget extends WP_Widget {

	function __construct() {

		$widget_ops = array('classname' => 'widget_chatme_login', 'description' => esc_html__('Chatme Login Widget', 'chatmeim-mini') );
		
		parent::__construct('logn-form-widget', esc_html__('ChatMe Login Form', 'chatmeim-mini'), $widget_ops);
		
	}
	
	function widget($args,$instance) {
	
		extract($args);

		$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
			
		echo $before_widget;
		if (!empty( $title )) { 
			echo $before_title . esc_html__('ChatMe.im Login', 'chatmeim-mini') . $after_title; 
		};
		echo '<ul style="list-style:none;margin-left:0px;">';
		
		echo '  <form method="get" action="https://oldwebchat.chatme.im" target="_blank">';
			echo '  <li>' . esc_html__('Username', 'chatmeim-mini') . '<input type="email" name="u" placeholder="' . esc_html__('user@host', 'chatmeim-mini') . '" required="" /></li>';
			echo '  <li>' . esc_html__('Password', 'chatmeim-mini') . '<input type="password" name="q" required="" placeholder="' . esc_html__('Password', 'chatmeim-mini') . '" /><input type="hidden" name="h" value="1"></li>';
			echo '  <li><button type="submit" formtarget="_blank">' . esc_html__('Login to Chat', 'chatmeim-mini') . '</button></li>';
		echo '  </form>';
		
		echo '</ul>';
		echo $after_widget;

	}
	
	function update($new_instance, $old_instance) {
		
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);

		return $instance;
		
	}
	
	function form($instance) {

		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = strip_tags($instance['title']);
		
		echo '<p>' . esc_html__('Not more option for this widget', 'chatmeim-mini') . '</p>';
		
		
	}

}

add_action( 'widgets_init', function() {
     register_widget( 'chatme_login_Widget' );
});
?>