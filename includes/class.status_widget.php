<?php
class chatme_status_Widget extends WP_Widget {

	function __construct() {

		$widget_ops = array('classname' => 'widget_chatme_status', 'description' => esc_html__('Display the ChatMe User Status', 'chatmeim-mini') );
		parent::__construct('status-picture-widget', esc_html__('ChatMe Status Picture', 'chatmeim-mini'), $widget_ops);
		
	}

	function widget($args,$instance) {
	
		extract($args);

		$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);

		$dominio = explode('@',$title);
		$api= new ChatMeApi;
		$url_status = $api->getHost($dominio[1],'url-status');
			
		echo $before_widget;
		if (!empty( $title )) { 
			echo $before_title . esc_html__('ChatMe Status', 'chatmeim-mini') . $after_title; 
		};
		echo '<ul style="list-style:none;margin-left:0px;">';
 
				echo '  <li>'. $title .' <img src="' . $url_status .$title.'" alt="ChatMe Status" /></li>';
		
		echo '</ul>';
		echo $after_widget;
	}
	
	function update($new_instance, $old_instance) {
		
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);

		return $instance;
		
	}
	
	function form($instance) {

		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'hosted' => '0' ) );
		$title = strip_tags($instance['title']);

		?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php echo esc_html__('ChatMe Username with domain', 'chatmeim-mini'); ?>: <input placeholder="<?php echo esc_html__('user@host', 'chatmeim-mini'); ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="email" value="<?php echo esc_attr($title); ?>" /></label></p>

		<?php
		
	}

}

add_action( 'widgets_init', function() {
     register_widget( 'chatme_status_Widget' );
});
?>