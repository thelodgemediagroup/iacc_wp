<?php class Popular_Posts extends WP_Widget
{

	function __construct()
	{
		$params = array(
			'description' => __('A popular posts widget that displays popular posts.', TEMPLATENAME),
			'name' => 'Popular Posts +'
		);

		parent::__construct('Popular_Posts', '', $params);
	}

	public function form($instance)
	{
		extract($instance);

		if ( empty($title) ) $title = __('Popular Posts', TEMPLATENAME);
		?>

		<!-- Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'organic') ?></label>
			<input
				type="text"
				class="widefat"
				id="<?php echo $this->get_field_id( 'title' ); ?>"
				name="<?php echo $this->get_field_name( 'title' ); ?>"
				value="<?php if( isset($title) ) echo esc_attr($title); ?>" />
		</p>

		<!-- Posts count: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'posts_count' ); ?>"><?php _e('Posts count:', TEMPLATENAME) ?></label>
			<input
				type="text"
				id="<?php echo $this->get_field_id( 'posts_count' ); ?>"
				name="<?php echo $this->get_field_name( 'posts_count' ); ?>"
				value="<?php if( isset($posts_count) ) echo esc_attr($posts_count); ?>" />
		</p>

		<?php
	}

	public function widget($args, $instance)
	{

		extract($args);
		extract($instance);

			$loop_options = array(
				'posts_per_page' => $posts_count,
				'post_type' => 'post',
				'orderby' => 'comment_count',
				'order' => 'DESC'
			);
			$loop = new WP_Query($loop_options);

		echo $before_widget;
			echo $before_title . $title . $after_title;
			
		if($loop->have_posts()) :

			echo '<ul>';
			
			while($loop->have_posts()) : $loop->the_post();

				echo '<li>';
					echo '<a href="'; echo the_permalink(); echo '"><h6>'; echo the_title(); echo '</h6></a>';
					echo '<span class="widget-date">'; echo get_the_date(); echo ', '; echo comments_number( 'No Comments', '1 Comment', '% Comments' ); echo '</span>';
				echo '</li>';

			endwhile;

			echo '</ul>';

		endif;

		echo $after_widget;
	}

}


/*
 * Register widget.
 */
add_action('widgets_init', 'Popular_PostsInit');
function Popular_PostsInit() {
	register_widget('Popular_Posts');
}
?>