<?php class TwitterWidget extends WP_Widget {

	function TwitterWidget() {
		$widget_ops = array('classname' => 'widget_latest_tweets', 'description' => __( 'Displays a list of twitter feeds', TEMPLATENAME ) );
		$this->WP_Widget(false, __('Twitter +', TEMPLATENAME), $widget_ops);

		if ( is_active_widget(false, false, $this->id_base) ){
			add_action( 'wp_print_scripts', array(&$this, 'add_tweet_script') );
		}

	}

	function add_tweet_script(){
		wp_enqueue_script('js_twitter');
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters('widget_title', empty($instance['title']) ? __('Recent Tweets', TEMPLATENAME) : $instance['title'], $instance, $this->id_base);
		$username= $instance['username'];
		$count = (int)$instance['count'];

		if($count < 1){
			$count = 1;
		}

		if ( !empty( $username ) ) {
			echo $before_widget;
			if ( $title)
				echo $before_title . $title . $after_title;

		$id = rand(1,1000);
		?>

		<script type="text/javascript">
				jQuery(document).ready(function($) {
					 $(".jstwitter_<?php echo $id;?>").tweet({
						username: "<?php echo $username;?>",
						count: <?php echo $count;?>,
						seconds_ago_text: '<?php _e('about %d seconds ago',TEMPLATENAME);?>',
						a_minutes_ago_text: '<?php _e('about a minute ago',TEMPLATENAME);?>',
						minutes_ago_text: '<?php _e('about %d minutes ago',TEMPLATENAME);?>',
						a_hours_ago_text: '<?php _e('about an hour ago',TEMPLATENAME);?>',
						hours_ago_text: '<?php _e('about %d hours ago',TEMPLATENAME);?>',
						a_day_ago_text: '<?php _e('about a day ago',TEMPLATENAME);?>',
						days_ago_text: '<?php _e('about %d days ago',TEMPLATENAME);?>',
						view_text: '<?php _e('view tweet on twitter',TEMPLATENAME);?>'
					 });
				});
		</script>
		<div class="jstwitter_<?php echo $id;?>"></div>

		<?php
			echo $after_widget;
		}
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['username'] = strip_tags($new_instance['username']);
		$instance['count'] = (int) $new_instance['count'];

		return $instance;
	}

	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$username = isset($instance['username']) ? esc_attr($instance['username']) : '';
		$count = isset($instance['count']) ? absint($instance['count']) : 3;
		$display = isset( $instance['display'] ) ? $instance['display'] : 'latest';
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', TEMPLATENAME); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('username'); ?>"><?php _e('Username:', TEMPLATENAME); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" type="text" value="<?php echo $username; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('How many tweets to display?', TEMPLATENAME); ?></label>
		<input id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo $count; ?>" size="3" /></p>

<?php
	}
}

function TwitterWidgetInit() {
  register_widget('TwitterWidget');
}

add_action('widgets_init', 'TwitterWidgetInit');

?>