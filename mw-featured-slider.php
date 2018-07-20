<?php
/**
 * @package MW_Featured_Slider
 * @version 1.5
 */
/*
Plugin Name: Makeworthy Featured Slider
Plugin URI: https://www.makeworthymedia.com/
Description: Displays rotating slides. Advanced Custom Fields plugin required to hyperlink slides. Uses Slick slider by Ken Wheeler http://kenwheeler.github.io/slick
Version: 1.5
Author: Makeworthy Media
Author URI: https://www.makeworthymedia.com/
License: GPL2
*/

/*  Copyright 2016 Jennette Fulda  (email : contact@makeworthymedia.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

// Set up Advanced Custom Fields fields if the plugin is active
if (function_exists('register_field_group')) {
	include_once('mw-featured-slider-acf.php');
}

// Add custom script to footer
add_action( 'wp_enqueue_scripts', 'mwfs_featured_enqueued_assets' );
function mwfs_featured_enqueued_assets() {
	wp_enqueue_script('slick-js', plugin_dir_url( __FILE__ ) . 'slick/slick.min.js', array(), '1.5.9', true );
	wp_enqueue_style( 'slick', plugin_dir_url( __FILE__ ) . 'slick/slick.css', '', '1.5.9' );
	wp_enqueue_style( 'slick-theme', plugin_dir_url( __FILE__ ) . 'slick/slick-theme.css', '', '1.5.9' );
}

//* Add stuff to <head>
//add_action ('wp_head', 'mwfs_featured_add_to_head');
function mwfs_featured_add_to_head() {
}

// Register Slide Post Type
function mwfs_slide_post_type() {

	$labels = array(
		'name'                  => 'Slides',
		'singular_name'         => 'Slide',
		'menu_name'             => 'Slides',
		'name_admin_bar'        => 'Slides',
		'archives'              => 'Slide Archives',
		'parent_item_colon'     => 'Parent Slide:',
		'all_items'             => 'All Slides',
		'add_new_item'          => 'Add New Slide',
		'add_new'               => 'Add New',
		'new_item'              => 'New Slide',
		'edit_item'             => 'Edit Slide',
		'update_item'           => 'Update Slide',
		'view_item'             => 'View Slide',
		'search_items'          => 'Search Slides',
		'not_found'             => 'Not found',
		'not_found_in_trash'    => 'Not found in Trash',
		'featured_image'        => 'Featured Image',
		'set_featured_image'    => 'Set featured image',
		'remove_featured_image' => 'Remove featured image',
		'use_featured_image'    => 'Use as featured image',
		'insert_into_item'      => 'Insert into Slide',
		'uploaded_to_this_item' => 'Uploaded to this Slide',
		'items_list'            => 'Slides list',
		'items_list_navigation' => 'Slides list navigation',
		'filter_items_list'     => 'Filter Slides list',
	);
	$args = array(
		'label'                 => 'Slide',
		'description'           => 'Slides for featured slider',
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'revisions', ),
		'taxonomies'            => array( 'category', 'post_tag' ),
		'hierarchical'          => true,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 25,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,		
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'slide', $args );

}
add_action( 'init', 'mwfs_slide_post_type', 0 );

// Creating the widget 
class mw_featured_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			// Base ID of your widget
			'mw_featured_widget', 

			// Widget name will appear in UI
			__('Featured Slider', 'mw_featured_widget_domain'), 

			// Widget description
			array( 'description' => __( 'Widget displays a featured slider', 'mw_featured_widget_domain' ), ) 
		);
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {
		// Initialize slider. Would prefer to do this in <head> but I don't know how within a widget.
		
		$adaptiveHeight = ( isset( $instance['adaptiveHeight'] ) ) ? $instance['adaptiveHeight'] : 'false';
		$autoplay = ( isset( $instance['autoplay'] ) ) ? $instance['autoplay'] : 'false';
		$autoplaySpeed = $instance['autoplaySpeed'] ? $instance['autoplaySpeed'] : '3000';
		$arrows = ( isset( $instance['arrows'] ) ) ? $instance['arrows'] : 'true';
		$dots = ( isset( $instance['dots'] ) ) ? $instance['dots'] : 'false';
		$infinite = ( isset( $instance['infinite'] ) ) ? $instance['infinite'] : 'true';
		$pauseOnHover = ( isset( $instance['pauseOnHover'] ) ) ? $instance['pauseOnHover'] : 'true';
		$slidesToShow = $instance['slidesToShow'] ? $instance['slidesToShow'] : '1';
		$slidesToScroll = $instance['slidesToScroll'] ? $instance['slidesToScroll'] : '1';
		$order = $instance['order'] ? $instance['order'] : 'ASC';
		$orderby = $instance['orderby'] ? $instance['orderby'] : 'menu_order';
		$customCode = $instance['customCode'] ? $instance['customCode'] : '';
		
	?>
<script>
jQuery( document ).ready(function( $ ) {
	$('#mw-featured-wrapper-<?php echo $this->id; ?>').slick({
		speed: 500,
		cssEase: 'linear',
		<?php if ($slidesToShow == 1) : ?>fade: true,<?php endif; ?>
		adaptiveHeight: <?php echo $adaptiveHeight; ?>,
		autoplay: <?php echo $autoplay; ?>,
		autoplaySpeed: <?php echo $autoplaySpeed; ?>,
		arrows: <?php echo $arrows; ?>,
		dots: <?php echo $dots; ?>,
		infinite: <?php echo $infinite; ?>,
		pauseOnHover: <?php echo $pauseOnHover; ?>,
		slidesToShow: <?php echo $slidesToShow; ?>,
		slidesToScroll: <?php echo $slidesToScroll; ?>,
		<?php echo $customCode;echo "\n"; ?>
	});
});
</script>
<?php 

		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		//echo $args['before_title'] . $title . $args['after_title'];
		$category = empty($instance['category']) ? '' : $instance['category'];

		// This is where you run the code and display the output
		$queryArgs = array(
			'post_type' => 'slide',
			'orderby' => $orderby,
			'order'   => $order,
			'cat' => $category,
		);
		$the_query = new WP_Query($queryArgs);
		if ($the_query->have_posts()) :
			$count = 1;
			
			echo '<div class="mw-featured-wrapper" id="mw-featured-wrapper-'. $this->id . '">';
			while ( $the_query->have_posts() ) : $the_query->the_post();
				$class = '';
			
				if (function_exists('get_field')) {
					if ($link = get_field('slide_link')) {
						echo '<a href="' . $link . '">';
					}
				} 
				if (function_exists('get_field')) {
					if (get_field('slide_class')) {
						$class = ' ' . get_field('slide_class');
					}
				} 				
				?>
				<div class="mw-featured">
				<?php if ( has_post_thumbnail() ) {
					echo '<div class="mw-image">';
					the_post_thumbnail();
					echo '</div>';
				} ?>
					<div class="mw-featured-content<?php echo $class; ?>"><?php echo the_content(); ?></div>
				</div>
				<?php
				$count++;
				if (function_exists('get_field')) {
					if ($link = get_field('slide_link')) {
						echo '</a>';
					}
				} 
				?>
			<?php endwhile;
			echo '</div><!-- end .mw-featured-wrapper -->';
		endif;
		
		echo $args['after_widget'];
		wp_reset_postdata();
	}
		
	// Widget Backend 
	public function form( $instance ) {

		// PART 1: Extract the data from the instance variable
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = $instance['title'];
		$category = $instance['category'];
		$adaptiveHeight = ( isset( $instance['adaptiveHeight'] ) ) ? $instance['adaptiveHeight'] : 'false';	// Default to false
		$autoplay = ( isset( $instance['autoplay'] ) ) ? $instance['autoplay'] : 'false';	// Default to false
		$autoplaySpeed = $instance['autoplaySpeed'] ? $instance['autoplaySpeed'] : '3000';	// Default to 3000
		$arrows = ( isset( $instance['arrows'] ) ) ? $instance['arrows'] : 'true';	// Default to true
		$dots = ( isset( $instance['dots'] ) ) ? $instance['dots'] : 'false';	// Default to true
		$infinite = ( isset( $instance['infinite'] ) ) ? $instance['infinite'] : 'true';	// Default to true
		$pauseOnHover = ( isset( $instance['pauseOnHover'] ) ) ? $instance['pauseOnHover'] : 'true';	// Default to true
		$slidesToShow = $instance['slidesToShow'] ? $instance['slidesToShow'] : '1';	// Default to 1
		$slidesToScroll = $instance['slidesToScroll'] ? $instance['slidesToScroll'] : '1';	// Default to 1
		$order = $instance['order'] ? $instance['order'] : 'ASC';	// Default to ASC
		$orderby = $instance['orderby'] ? $instance['orderby'] : 'menu_order';	// Default to menu_order
		$customCode = $instance['customCode'] ? $instance['customCode'] : '';	// Default to nothing

		// PART 2-3: Display the fields
     ?>
	<!-- Widget Title field START -->
	<p>
		<label for="<?php echo $this->get_field_id('title'); ?>">Title: 
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"  type="text"
			name="<?php echo $this->get_field_name('title'); ?>" 
			value="<?php echo esc_attr($title); ?>" />
		</label>
	</p>

     <!-- Widget Category field START -->
	<p>
		<label for="<?php echo $this->get_field_id('category'); ?>">Category: 
		<select class='widefat' id="<?php echo $this->get_field_id('category'); ?>"
		name="<?php echo $this->get_field_name('category'); ?>" type="text">
			<option value="">--All Categories--</option>
			<?php echo mwqs_create_tax_dropdown('category', $category); ?>
		</select>                
		</label>
	</p>
	
	<!-- Widget Adaptive Height field START -->
	<p>
		<label for="<?php echo $this->get_field_id('adaptiveHeight'); ?>">Adaptive Height: 
		<select class='widefat' id="<?php echo $this->get_field_id('adaptiveHeight'); ?>"
		name="<?php echo $this->get_field_name('adaptiveHeight'); ?>" type="text">
			<option value='false'<?php echo ($adaptiveHeight=='false')?'selected':''; ?>>Off</option>
			<option value='true'<?php echo ($adaptiveHeight=='true')?'selected':''; ?>>On</option> 
		</select>                
	</p>

	<!-- Widget autoplay field START -->
	<p>
		<label for="<?php echo $this->get_field_id('autoplay'); ?>">Autoplay: 
		<select class='widefat' id="<?php echo $this->get_field_id('autoplay'); ?>"
		name="<?php echo $this->get_field_name('autoplay'); ?>" type="text">
			<option value='false'<?php echo ($autoplay=='false')?'selected':''; ?>>Off</option>
			<option value='true'<?php echo ($autoplay=='true')?'selected':''; ?>>On</option> 
		</select>                
	</p>
	
	<!-- Widget autoplaySpeed field START -->
	<p>
		<label for="<?php echo $this->get_field_id('autoplaySpeed'); ?>">Autoplay Speed in milliseconds (default: 3000): 
		<input class="widefat" id="<?php echo $this->get_field_id('autoplaySpeed'); ?>"  type="text"
			name="<?php echo $this->get_field_name('autoplaySpeed'); ?>" 
			value="<?php echo esc_attr($autoplaySpeed); ?>" />
		</label>
	</p>

	<!-- Widget Arrows field START -->
	<p>
		<label for="<?php echo $this->get_field_id('arrows'); ?>">Arrows: 
		<select class='widefat' id="<?php echo $this->get_field_id('arrows'); ?>"
		name="<?php echo $this->get_field_name('arrows'); ?>" type="text">
			<option value='false'<?php echo ($arrows=='false')?'selected':''; ?>>Off</option>
			<option value='true'<?php echo ($arrows=='true')?'selected':''; ?>>On</option> 
		</select>                
	</p>
	
	<!-- Widget dots field START -->
	<p>
		<label for="<?php echo $this->get_field_id('dots'); ?>">Dots: 
		<select class='widefat' id="<?php echo $this->get_field_id('dots'); ?>"
		name="<?php echo $this->get_field_name('dots'); ?>" type="text">
			<option value='false'<?php echo ($dots=='false')?'selected':''; ?>>Off</option>
			<option value='true'<?php echo ($dots=='true')?'selected':''; ?>>On</option> 
		</select>                
	</p>

	
	<!-- Widget Infinite field START -->
	<p>
		<label for="<?php echo $this->get_field_id('infinite'); ?>">Infinite: 
		<select class='widefat' id="<?php echo $this->get_field_id('infinite'); ?>"
		name="<?php echo $this->get_field_name('infinite'); ?>" type="text">
			<option value='false'<?php echo ($infinite=='false')?'selected':''; ?>>Off</option>
			<option value='true'<?php echo ($infinite=='true')?'selected':''; ?>>On</option> 
		</select>                
	</p>
	
	<!-- Widget pauseOnHover field START -->
	<p>
		<label for="<?php echo $this->get_field_id('pauseOnHover'); ?>">Pause on hover: 
		<select class='widefat' id="<?php echo $this->get_field_id('pauseOnHover'); ?>"
		name="<?php echo $this->get_field_name('pauseOnHover'); ?>" type="text">
			<option value='false'<?php echo ($pauseOnHover=='false')?'selected':''; ?>>Off</option>
			<option value='true'<?php echo ($pauseOnHover=='true')?'selected':''; ?>>On</option> 
		</select>                
	</p>
	
	<!-- Widget slidesToShow field START -->
	<p>
		<label for="<?php echo $this->get_field_id('slidesToShow'); ?>">Slides to show (default: 1): 
		<input class="widefat" id="<?php echo $this->get_field_id('slidesToShow'); ?>"  type="text"
			name="<?php echo $this->get_field_name('slidesToShow'); ?>" 
			value="<?php echo esc_attr($slidesToShow); ?>" />
		</label>
	</p>
	
	<!-- Widget slidesToScroll field START -->
	<p>
		<label for="<?php echo $this->get_field_id('slidesToScroll'); ?>">Slides to scroll (default: 1): 
		<input class="widefat" id="<?php echo $this->get_field_id('slidesToScroll'); ?>"  type="text"
			name="<?php echo $this->get_field_name('slidesToScroll'); ?>" 
			value="<?php echo esc_attr($slidesToScroll); ?>" />
		</label>
	</p>
	
	<!-- Widget order field START -->
	<p>
		<label for="<?php echo $this->get_field_id('order'); ?>">Order: 
		<select class='widefat' id="<?php echo $this->get_field_id('order'); ?>"
		name="<?php echo $this->get_field_name('order'); ?>" type="text">
			<option value='ASC'<?php echo ($order=='ASC')?'selected':''; ?>>Ascending (1, 2, 3; a, b, c)</option>
			<option value='DESC'<?php echo ($order=='DESC')?'selected':''; ?>>Descending (3, 2, 1; c, b, a)</option> 
		</select>                
	</p>
	
	<!-- Widget orderby field START -->
	<p>
		<label for="<?php echo $this->get_field_id('orderby'); ?>">Order By: 
		<select class='widefat' id="<?php echo $this->get_field_id('orderby'); ?>"
		name="<?php echo $this->get_field_name('orderby'); ?>" type="text">
			<option value='menu_order'<?php echo ($orderby=='menu_order')?'selected':''; ?>>Menu Order</option>
			<option value='date'<?php echo ($orderby=='date')?'selected':''; ?>>Date</option> 
			<option value='title'<?php echo ($orderby=='title')?'selected':''; ?>>Title</option> 
		</select>                
	</p>
	
	<!-- Widget customCode field START -->
	<p>
		<label for="<?php echo $this->get_field_id('customCode'); ?>">Custom code: 
		<textarea class="widefat" id="<?php echo $this->get_field_id('customCode'); ?>"	name="<?php echo $this->get_field_name('customCode'); ?>" rows="6"><?php echo esc_attr($customCode); ?></textarea>
		</label>
	</p>

	<?php 
	}
	
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['category'] = ( ! empty( $new_instance['category'] ) ) ? strip_tags( $new_instance['category'] ) : '';
		$instance['adaptiveHeight'] = ( ! empty( $new_instance['adaptiveHeight'] ) ) ? strip_tags( $new_instance['adaptiveHeight'] ) : '';
		$instance['autoplay'] = ( ! empty( $new_instance['autoplay'] ) ) ? strip_tags( $new_instance['autoplay'] ) : '';
		$instance['autoplaySpeed'] = ( ! empty( $new_instance['autoplaySpeed'] ) ) ? strip_tags( $new_instance['autoplaySpeed'] ) : '';
		$instance['arrows'] = ( ! empty( $new_instance['arrows'] ) ) ? strip_tags( $new_instance['arrows'] ) : '';
		$instance['dots'] = ( ! empty( $new_instance['dots'] ) ) ? strip_tags( $new_instance['dots'] ) : '';
		$instance['infinite'] = ( ! empty( $new_instance['infinite'] ) ) ? strip_tags( $new_instance['infinite'] ) : '';
		$instance['pauseOnHover'] = ( ! empty( $new_instance['pauseOnHover'] ) ) ? strip_tags( $new_instance['pauseOnHover'] ) : '';
		$instance['slidesToShow'] = ( ! empty( $new_instance['slidesToShow'] ) ) ? strip_tags( $new_instance['slidesToShow'] ) : '';
		$instance['slidesToScroll'] = ( ! empty( $new_instance['slidesToScroll'] ) ) ? strip_tags( $new_instance['slidesToScroll'] ) : '';
		$instance['order'] = ( ! empty( $new_instance['order'] ) ) ? strip_tags( $new_instance['order'] ) : '';
		$instance['orderby'] = ( ! empty( $new_instance['orderby'] ) ) ? strip_tags( $new_instance['orderby'] ) : '';
		$instance['customCode'] = ( ! empty( $new_instance['customCode'] ) ) ? strip_tags( $new_instance['customCode'] ) : '';
		return $instance;
	}
} // Class mw_quote_widget ends here

// Register and load the widget
function mw_featured_load_widget() {
	register_widget( 'mw_featured_widget' );
}
add_action( 'widgets_init', 'mw_featured_load_widget' );

// Creates a dropdown menu with proper item selected when fed the taxonomy name and value to be selected
function mwqs_create_tax_dropdown($taxonomy, $value) {
	// Create the "taxonomy" dropdown
	$terms = get_terms( $taxonomy, array(
		'hide_empty' => 0,
		'orderby' => 'name',
	) );
	
	$html = '';
	
	if (count($terms) > 0) {
		foreach ($terms as $term) {
			$html .= sprintf(
				'<option value="%s" class="" %s >%s</option>',
				$term->term_id,
				selected( $term->term_id, $value ),
				$term->name
			);
		}
	}
	
	return $html;
}
