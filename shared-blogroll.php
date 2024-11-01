<?php

/*
Plugin Name: Shared Blogroll
Plugin URI: http://teleogistic.net/code/wordpresswordpress-mu/shared-blogroll/
Description: Adds a widget that displays a link category from any blog on the same WPMU installation
Version: 1.0
Author: Boone Gorges
Author URI: http://teleogistic.net
*/


function shared_blogroll_init() {
	register_widget('WP_Widget_Shared_Blogroll');
}
add_action('widgets_init', 'shared_blogroll_init');


class WP_Widget_Shared_Blogroll extends WP_Widget {

	function WP_Widget_Shared_Blogroll() {
		$widget_ops = array('description' => __( "Your blogroll" ) );
		$this->WP_Widget('shared_blogroll', __('Shared Blogroll'), $widget_ops);
	}

	function widget( $args, $instance ) {
		global $blog_id;

		extract($args, EXTR_SKIP);

		$show_description = isset($instance['description']) ? $instance['description'] : false;
		$show_name = isset($instance['name']) ? $instance['name'] : false;
		$show_rating = isset($instance['rating']) ? $instance['rating'] : false;
		$show_images = isset($instance['images']) ? $instance['images'] : true;
		$category = isset($instance['category']) ? $instance['category'] : false;
		$source_blog_id = isset($instance['blog_id']) ? $instance['blog_id'] : $blog_id;

		if ( is_admin() && !$category ) {
			// Display All Links widget as such in the widgets screen
			echo $before_widget . $before_title. __('All Links') . $after_title . $after_widget;
			return;
		}

		if ( function_exists( 'switch_to_blog' ) && $source_blog_id != $blog_id )
			switch_to_blog( $source_blog_id );

		$before_widget = preg_replace('/id="[^"]*"/','id="%id"', $before_widget);
		wp_list_bookmarks(apply_filters('widget_links_args', array(
			'title_before' => $before_title, 'title_after' => $after_title,
			'category_before' => $before_widget, 'category_after' => $after_widget,
			'show_images' => $show_images, 'show_description' => $show_description,
			'show_name' => $show_name, 'show_rating' => $show_rating,
			'category' => $category, 'class' => 'linkcat widget'
		)));
		
		if ( function_exists( 'restore_current_blog' ) )
			restore_current_blog();
	}

	function update( $new_instance, $old_instance ) {
		$new_instance = (array) $new_instance;
		$instance = array( 'images' => 0, 'name' => 0, 'description' => 0, 'rating' => 0);
		foreach ( $instance as $field => $val ) {
			if ( isset($new_instance[$field]) )
				$instance[$field] = 1;
		}
		$instance['blog_id'] = intval($new_instance['blog_id']);
		$instance['category'] = intval($new_instance['category']);

		return $instance;
	}

	function form( $instance ) {
		global $blog_id;

		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'images' => true, 'name' => true, 'description' => false, 'rating' => false, 'category' => false ) );
		$source_blog_id = $instance['blog_id'];
		$blog_details = get_blog_details($source_blog_id);
?>
		

		<p><label for="<?php echo $this->get_field_id('blog_id'); ?>"><?php _e('Source blog ID:'); ?></label> <input class="shared_blogroll_blog_id" name="<?php echo $this->get_field_name('blog_id'); ?>" type="text" value="<?php echo esc_attr($source_blog_id); ?>" style="width: 20%;"/></p>
		
		<div id="<?php echo $this->get_field_id('blog_name'); ?>" class="shared-blogroll-blog-name">
			<?php 	$blogname = $blog_details->blogname;
						_e("<p>Include the following link categories from the blog \"$blogname\":</p>", 'shared-blogroll'); 
				?>
		</div>

		<div class="shared-blogroll-slider">

		<p class="cat-par">
		<label for="<?php echo $this->get_field_id('category'); ?>" class="screen-reader-text"><?php _e('Select Link Category'); ?></label>
		<select class="widefat cat-drop" id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>">
		<option value=""><?php _e('All Links'); ?></option>
		<?php
		if ( function_exists( 'switch_to_blog' ) && $source_blog_id != $blog_id ) {
			switch_to_blog( $source_blog_id );
			}
		
		$link_cats = get_terms( 'link_category');
		
		foreach ( $link_cats as $link_cat ) {
			echo '<option value="' . intval($link_cat->term_id) . '"'
				. ( $link_cat->term_id == $instance['category'] ? ' selected="selected"' : '' )
				. '>' . $link_cat->name . "</option>\n";
		}
		
		if ( function_exists( 'restore_current_blog' ) )
			restore_current_blog();
		?>
		</select></p>
		<p class="cat-opt">
		<input class="checkbox" type="checkbox" <?php checked($instance['images'], true) ?> id="<?php echo $this->get_field_id('images'); ?>" name="<?php echo $this->get_field_name('images'); ?>" />
		<label for="<?php echo $this->get_field_id('images'); ?>"><?php _e('Show Link Image'); ?></label><br />
		<input class="checkbox" type="checkbox" <?php checked($instance['name'], true) ?> id="<?php echo $this->get_field_id('name'); ?>" name="<?php echo $this->get_field_name('name'); ?>" />
		<label for="<?php echo $this->get_field_id('name'); ?>"><?php _e('Show Link Name'); ?></label><br />
		<input class="checkbox" type="checkbox" <?php checked($instance['description'], true) ?> id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>" />
		<label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Show Link Description'); ?></label><br />
		<input class="checkbox" type="checkbox" <?php checked($instance['rating'], true) ?> id="<?php echo $this->get_field_id('rating'); ?>" name="<?php echo $this->get_field_name('rating'); ?>" />
		<label for="<?php echo $this->get_field_id('rating'); ?>"><?php _e('Show Link Rating'); ?></label>
		</p>
		
		</div>
<?php
	}
}


function shared_blogroll_javascript() {
	$nonce = wp_create_nonce( 'shared_blogroll' );
	?>
	<script type="text/javascript">
	jQuery(document).ready(function() {
   		jQuery("input.shared_blogroll_blog_id").blur(function() {
   			var wc = jQuery(this).parents('.widget-content');
   			var sl = jQuery(wc).children('.shared-blogroll-slider');
   			var cp = jQuery(sl).children('.cat-par');
   			var dropdown = jQuery(cp).children('.cat-drop');
   			var bn = jQuery(wc).children('.shared-blogroll-blog-name');
   			
   			var	blog_id = jQuery(this).val();
   			var data = {
				action: 'shared_blogroll_ajax',
				blog_id: blog_id,
				_ajax_nonce: '<?php echo $nonce; ?>'
			};
			
			jQuery.post(ajaxurl, data, function(response) {
				a = response.split('&message=');
				if ( a[0] != '' ) {
					if ( jQuery(cp).is(":invisible") ) {
						jQuery(sl).slideDown("fast", function() {
							jQuery(dropdown).html(a[0]);
						});
					} else {
						jQuery(dropdown).html(a[0]);
					}					
				} else {
					jQuery(sl).slideUp("fast");
				}
				
				if ( jQuery(bn).html() != a[1] ) {
					jQuery(bn).fadeOut("fast", function() {
						jQuery(bn).html(a[1]);
						jQuery(bn).fadeIn("fast");									
					});
				}
			});
	});
 });
	</script>
<?php
}
add_action('admin_head', 'shared_blogroll_javascript');


function shared_blogroll_ajax_callback() {
	global $wpdb; // this is how you get access to the database
	
	check_ajax_referer( "shared_blogroll" );		
	
	if ( $_POST[ 'blog_id' ] )
		$blog_id = $_POST[ 'blog_id' ];
	
	if ( function_exists( 'switch_to_blog' ) && $blog_id )
		switch_to_blog( $blog_id );
	
	$m = '&message=';
	
	$blog_details = get_blog_details($blog_id);
	$blogname = $blog_details->blogname;
	
	if ( $blogname == '' ) {
		$contents = $m . __('<p>That blog doesn\'t exist!</p>', 'shared-blogroll');
		echo $contents;
		die();
	}
	
	$link_cats = get_terms( 'link_category' );
	
	if ( $link_cats ) {
		$contents = '<option value="">' . __('All Links') . '</option>';
		
		foreach ( $link_cats as $link_cat ) {
			$link = '<option value="' . intval($link_cat->term_id) . '"' . ( $link_cat->term_id == $instance['category'] ? ' selected="selected"' : '' )
				. '>' . $link_cat->name . "</option>\n";
			$contents .= $link;
		}
		
		$contents .= $m . __("<p>Include the following link categories from the blog \"$blogname\":</p>", 'shared-blogroll');
		
	} else {
		$contents .= $m . __("Sorry, $blogname does not have any links.", 'shared-blogroll');
	}
	
	if ( function_exists( 'restore_current_blog' ) )
			restore_current_blog();
	
    echo $contents;

	die();
}
add_action('wp_ajax_shared_blogroll_ajax', 'shared_blogroll_ajax_callback');

?>