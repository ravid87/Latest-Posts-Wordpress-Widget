<?php

/*
Plugin Name: Latest Posts Widget
Description: List the lastest posts from a category to pull posts from and with the possibility to display an excerpt and/or date
Version: 1.0
Author: Ravidhu Dissanayake
Author URI: http://ravidhu.com
License: GPL
Copyright: Ravidhu Dissanayake
*/

add_action( 'widgets_init', 'raw_lp_register_widget' );
function raw_lp_register_widget() {
	register_widget( 'raw_lp_widget' );
}

class raw_lp_widget extends WP_Widget {

	public function raw_lp_widget() {
	
		$widget_ops = array(

			'classname'   => 'raw_lp_widget widget_recent_entries',
			'description' => 'Display last blog posts from a specific category'
		
		);
		
		$this->WP_Widget( 'raw_lp_widget', __( 'Last Posts widget' ), $widget_ops );
	
	}
    
	public function form( $instance ) {
	
		$defaults  = array( 'title' => '', 'category' => '', 'number' => 5, 'show_date' => '' );
		$instance  = wp_parse_args( ( array ) $instance, $defaults );
		$title     = $instance['title'];
		$category  = $instance['category'];
		$number    = $instance['number'];
		$show_date = $instance['show_date'];
        $show_excerpt = $instance['show_excerpt'];
		$link_title = $instance['link_title'];
        
		?>
		
		<p>
			<label for="raw_lp_widget_title"><?php _e( 'Title' ); ?>:</label>
			<input type="text" class="widefat" id="raw_lp_widget_title" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<p>
			<label for="raw_lp_widget_posts_category"><?php _e( 'Category' ); ?>:</label>				
			
			<?php

			wp_dropdown_categories( array(
                'show_option_none'   => 'Tous',
				'orderby'            => 'title',
				'hide_empty'         => false,
				'name'               => $this->get_field_name( 'category' ),
				'id'                 => 'raw_lp_widget_cat_select',
				'class'              => 'widefat',
				'selected'           => $category

			) );

			?>

		</p>
		
		<p>
			<label for="raw_lp_widget_posts_number"><?php _e( 'Number of posts to show' ); ?>: </label>
			<input type="text" id="raw_lp_widget_posts_number" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo esc_attr( $number ); ?>" size="3" />
		</p>

		<p>
			<input type="checkbox" id="raw_lp_widget_posts_show_date" class="checkbox" name="<?php echo $this->get_field_name( 'show_date' ); ?>" <?php checked( $show_date, 1 ); ?> />
			<label for="raw_lp_widget_posts_show_date"><?php _e( 'Display post date ?' ); ?></label>
		</p>
        
        <p>
			<input type="checkbox" id="raw_lp_widget_posts_show_excerpt" class="checkbox" name="<?php echo $this->get_field_name( 'show_excerpt' ); ?>" <?php checked( $show_excerpt, 1 ); ?> />
			<label for="raw_lp_widget_posts_show_excerpt"><?php _e( 'Display an Excerpt ?' ); ?></label>
		</p>
        
        <p>
			<input type="checkbox" id="raw_lp_widget_posts_link_title" class="checkbox" name="<?php echo $this->get_field_name( 'link_title' ); ?>" <?php checked( $link_title, 1 ); ?> />
			<label for="raw_lp_widget_posts_link_title"><?php _e( 'Link in title ?' ); ?></label>
		</p>
        
		
		<?php
	
	}
    
	public function update( $new_instance, $old_instance ) {

		$instance                 = $old_instance;
		$instance['title']        = wp_strip_all_tags( $new_instance['title'] );
		$instance['category']     = wp_strip_all_tags( $new_instance['category'] );
		$instance['number']       = is_numeric( $new_instance['number'] ) ? intval( $new_instance['number'] ) : 5;
		$instance['show_date']    = isset( $new_instance['show_date'] ) ? 1 : 0;
        $instance['show_excerpt'] = isset( $new_instance['show_excerpt'] ) ? 1 : 0;
        $instance['link_title']   = isset( $new_instance['link_title'] ) ? 1 : 0;

		return $instance;

	}

	public function widget( $args, $instance ) {

		extract( $args );

		echo $before_widget;

		$title        = $title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$category     = $instance['category'];
		$number       = $instance['number'];
		$show_date    = ( $instance['show_date'] === 1 ) ? true : false;
        $show_excerpt = ( $instance['show_excerpt'] === 1 ) ? true : false;
        $link_title = ( $instance['link_title'] === 1 ) ? true : false;

		if ( !empty( $title ) ) echo $before_title . $title . $after_title;

		$cat_recent_posts = new WP_Query( array( 

			'post_type'      => 'post',
			'posts_per_page' => $number,
			'cat'            => $category

		) );

		if ( $cat_recent_posts->have_posts() ) {

			echo '<ul>';

			while ( $cat_recent_posts->have_posts() ) {

				$cat_recent_posts->the_post();

				echo '<li>';
				echo '<h3 class="raw_lp_title">';
                if ( $link_title ){
                    echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
                }else{
                    echo get_the_title();
                }
                echo '</h3>';
                if ( $show_date || $show_excerpt) echo '<p class="raw_lp_row">';
				if ( $show_date ) echo '<i class="raw_lp_date">' . get_the_time( get_option( 'date_format' ) ) . '</i>';
                if ( $show_excerpt ) echo '<br /><span class="raw_lp-excertp">' . get_the_excerpt() . '</span>';
                if ( $show_date || $show_excerpt) echo '</p>';
				echo '</li>';

			}

			echo '</ul>';

		} else {

			echo '';

		}

		wp_reset_postdata();

		echo $after_widget;

	}

}

?>