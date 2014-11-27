<?php
/**
 * @package WordPress
 * @subpackage BuddyPress
 * @sub-subpackage Mapology
 * @author Boris Glumpler
 * @copyright 2010, ShabuShabu Webdesign
 * @link http://shabushabu.eu
 * @license http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

class MAPO_Widget extends WP_Widget
{
	function mapo_widget()
	{
		$widget_ops = array( 'classname' => 'widget_routes', 'description' => __( 'Display Mapology Routes', 'mapo' ) );
		$this->WP_Widget( 'routes', __( 'Routes Widget', 'mapo' ), $widget_ops );
	}

	function widget( $args, $instance )
	{
		global $bp, $wpdb;
		
		extract( $args );
			
		$title = ( empty( $instance['title'] ) ) ? __( 'Routes', 'mapo' ) : $instance['title'];
		$group = ( empty( $instance['group'] ) ) ? false : $instance['group'];
		$max = ( empty( $instance['max'] ) ) ? 3 : $instance['max'];
		$user = ( empty( $instance['user'] ) ) ? false : $instance['user'];
		$ids = ( empty( $instance['ids'] ) ) ? false : $wpdb->escape( $instance['ids'] );
		
		if( mapo_has_routes( array( 'user_id' => $user_id, 'group_id' => $group, 'max' => $max, 'search_terms' => false, 'ids' => $ids ) ) ) :
		
			echo $before_widget;
			
			if ( $title)
				echo $before_title . $title . $create .  $after_title;
			?>
			<ul id="widget-routes-list" class="item-list">
			
			<?php while( mapo_routes() ) : mapo_the_route(); ?>
		
                <li id="route-<?php mapo_routes_id() ?>" class="widget-route">
                    
                    <div class="item">
                        <div class="item-title"><a href="<?php mapo_routes_link( false, 'routes', true ) ?>"><?php mapo_routes_name() ?></a></div>
                    </div>
        
        			<?php mapo_routes_avatar( false, 180, 180 ) ?>
                </li>
		
			<?php endwhile; ?>
			
			</ul>
		
			<?php
            echo $after_widget;
		endif;
	}

	function update( $new_instance, $old_instance )
	{
		$instance = $old_instance;
		
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['group'] = (int)$new_instance['group'];
		$instance['user'] = (int)$new_instance['user'];
		$instance['max'] = (int)$new_instance['max'];
		$instance['ids'] = $new_instance['ids'];

		return $instance;
	}

	function form( $instance )
	{
		global $wpdb;

		$instance = wp_parse_args( (array) $instance, array( 'title' => 'Routes', 'group' => false, 'max' => 3, 'ids' => false, 'user' => false ) );
		$title  = esc_attr( $instance['title'] );
		$group  = esc_attr( $instance['group'] );
		$user  = esc_attr( $instance['user'] );
		$max  = esc_attr( $instance['max'] );
		$ids  = esc_attr( $instance['ids'] );
		?>
		<p>
        	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
		<p>
        	<label for="<?php echo $this->get_field_id( 'group' ); ?>"><?php _e( 'Group ID:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'group' ); ?>" name="<?php echo $this->get_field_name( 'group' ); ?>" type="text" value="<?php echo $group; ?>" />
        </p>
		<p>
        	<label for="<?php echo $this->get_field_id( 'user' ); ?>"><?php _e( 'User ID:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'user' ); ?>" name="<?php echo $this->get_field_name( 'user' ); ?>" type="text" value="<?php echo $user; ?>" />
        </p>
		<p>
        	<label for="<?php echo $this->get_field_id( 'max' ); ?>"><?php _e( 'Max:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'max' ); ?>" name="<?php echo $this->get_field_name( 'max' ); ?>" type="text" value="<?php echo $max; ?>" />
        </p>
		<p>
        	<label for="<?php echo $this->get_field_id( 'ids' ); ?>"><?php _e( 'Comma separated event ids:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'ids' ); ?>" name="<?php echo $this->get_field_name( 'ids' ); ?>" type="text" value="<?php echo $ids; ?>" />
        </p>
		<?php	
	}
}
add_action( 'widgets_init', create_function( '', 'return register_widget("MAPO_Widget");' ) );
?>