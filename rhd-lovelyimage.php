<?php
/**
 * Plugin Name: LovelyImage
 * Description: A simple image widget with CSS-stylable captions and optional image linkage.
 * Author: Roundhouse Designs
 * Author URI: http://roundhouse-designs.com
 * Version: 1.0.1
**/


/* ==========================================================================
	Plugin Setup
   ========================================================================== */


define( 'RHD_LI_DIR', plugin_dir_url(__FILE__) );

/**
 * rhd_register_image_sizes function.
 *
 * @access public
 * @return void
 */
function rhd_register_image_sizes() {
	add_image_size( 'lovelyimage-landscape', 230, 130, true );
	add_image_size( 'lovelyimage-square', 225, 225, true );
}
add_action( 'init', 'rhd_register_image_sizes' );


/**
 * rhd_get_image_id function.
 *
 * @access public
 * @param mixed $url
 * @return void
 */
function rhd_get_image_id( $url ) {
	global $wpdb;
	$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $url ));
	return $attachment[0];
}


/* ==========================================================================
	LovelyImage Widget
   ========================================================================== */


class RHD_LovelyImage extends WP_Widget {
	function __construct() {
		parent::__construct(
			'rhd_lovelyimage', // Base ID
			__('LovelyImage', 'rhd'), // Name
			array( 'description' => __( 'A simple image widget with CSS-stylable captions and optional image linkage.', 'rhd' ), ) // Args
		);

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'display_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'display_styles' ) );
	}

    public function admin_scripts( $hook ) {
		if ( 'widgets.php' != $hook ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_script( 'upload_media_widget', RHD_LI_DIR . 'js/upload-media.js', array( 'jquery' ) );
		wp_enqueue_script( 'preview-image', RHD_LI_DIR . 'js/preview-image.js', array( 'jquery' ) );
	}

	public function admin_styles() {
		wp_enqueue_style( 'rhd-lovelyimage-admin', RHD_LI_DIR . 'css/rhd-lovelyimage-admin.css' );
	}

	public function display_scripts() {
		wp_enqueue_script( 'rhd-lovelyimage-js', RHD_LI_DIR . 'js/rhd-lovelyimage.js', array( 'jquery', 'jquery-color' ) );
	}

	public function display_styles() {
		wp_enqueue_style( 'rhd-lovelyimage-css', RHD_LI_DIR . 'css/rhd-lovelyimage.css' );
	}

	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		return $new_instance;
	}

	public function widget( $args, $instance ) {
		// outputs the content of the widget

		extract( $args );

		$title = ( $instance['title'] ) ? apply_filters('widget_title', $instance['title']) : '';
		$caption = esc_textarea( $instance['caption'] );
		$link = $instance['link'];
		$img_url = $instance['image'];
		$animate = $instance['animate'];
		$cap_color = ( $instance['cap_color'] !== '' ) ? $instance['cap_color'] : '#fff';
		$cap_opacity[0] = ( $instance['opacity_base'] ) ? $instance['opacity_base'] : 0.6;
		$cap_opacity[1] = ( $instance['opacity_hover'] ) ? $instance['opacity_hover'] : 0.85;
		$style = ( $instance['style'] == 'rhd_lovely_style_landscape' ) ? 'landscape' : 'square';

		$image_id = rhd_get_image_id( $img_url );
		$image = wp_get_attachment_image( $image_id, 'lovelyimage-' . strtolower( $style ), false, array( 'class' => "attachment-lovelyimage-{$style} rhd_lovelyimage_thumb" ) );

		$caption_class = ( $style == 'landscape' || empty( $style ) ) ? 'caption-top' : 'caption-bottom';
		$caption_atts = "data-animate=\"{$animate}\" data-bg-color=\"{$cap_color}\" data-opacity-base=\"{$cap_opacity[0]}\" data-opacity-hover=\"{$cap_opacity[1]}\"";

		echo $before_widget;

		echo $title;
		?>

		<?php if ( $link ) echo "<a href=\"{$link}\" target=\"_blank\">\n"; ?>
		<figure class="rhd_lovelyimage_container <?php echo $style; ?>">
			<?php if ( $caption ) : ?>
				<figcaption class="rhd_lovelyimage_caption <?php echo $caption_class; ?>" <?php echo $caption_atts; ?>><?php echo $caption; ?></figcaption>
			<?php endif; ?>
			<?php echo $image; ?>
		</figure>
		<?php if ( $link ) echo "</a>"; ?>

		<?php echo $after_widget;
	}

	public function form( $instance ) {
		// outputs the options form on admin
		$args['title'] = esc_attr( $instance['title'] );
		$args['caption'] = esc_textarea( $instance['caption'] );
		$args['link'] = esc_url( $instance['link'] );
		$args['image'] = esc_url( $instance['image'] );
		$args['animate'] = $instance['animate'];
		$args['cap_color'] = esc_attr( $instance['cap_color'] );
		$args['opacity_base'] = esc_attr( $instance['opacity_base'] );
		$args['opacity_hover'] = esc_attr( $instance['opacity_hover'] );
		$args['style'] = esc_attr( $instance['style'] );
	?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Widget Title:' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $args['title']; ?>" >
		</p>

		<h3><?php _e( 'Caption Options:' ); ?></h3>
		<p>
			<label for="<?php echo $this->get_field_id( 'caption' ); ?>"><?php _e( 'Caption <em>(Optional)</em>:' ); ?></label>
			<textarea id="<?php echo $this->get_field_id( 'caption' ); ?>" name="<?php echo $this->get_field_name( 'caption' ); ?>" value="<?php echo $args['caption']; ?>" ></textarea>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'animate' ); ?>"><?php _e( 'Animate on hover?' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'animate' ); ?>" name="<?php echo $this->get_field_name( 'animate' ); ?>" type="checkbox" <?php checked( $instance['animate'], true ); ?> />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'cap_color' ); ?>"><?php _e( 'Background Hex Color <em>(Include \'#\')</em>:' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'cap_color' ); ?>" name="<?php echo $this->get_field_name( 'cap_color' ); ?>" type="text" size="8" value="<?php echo $args['cap_color']; ?>" placeholder="#ffffff">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'opacity_base' ); ?>"><?php _e( 'Background Base Opacity <em>(0-1)</em>:' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'opacity_base' ); ?>" name="<?php echo $this->get_field_name( 'opacity_base' ); ?>" type="number" size="5" min="0" max="1" step="any" value="<?php echo $args['opacity_base']; ?>" placeholder="0.6">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'opacity_hover' ); ?>"><?php _e( 'Background Hover Opacity <em>(0-1)</em>:' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'opacity_hover' ); ?>" name="<?php echo $this->get_field_name( 'opacity_hover' ); ?>" type="number" size="5" min="0" max="1" step="any" value="<?php echo $args['opacity_hover']; ?>" placeholder="0.85">
		</p>

		<h3><?php _e( 'Display Options:' ); ?></h3>
		<p>
			<p><?php _e( 'Image Style:' ); ?></p>
			<input id="<?php echo $this->get_field_id( 'style_landscape' ); ?>" class="rhd_li_style_landscape" name="<?php echo $this->get_field_name( 'style' ); ?>" type="radio" value="rhd_lovely_style_landscape" <?php checked( $instance['style'], true ); ?> ?> />
			<label for="<?php echo $this->get_field_id( 'style_landscape' ); ?>"><?php _e( 'Landscape (Caption: top)' ); ?></label>
			<br />
			<input id="<?php echo $this->get_field_id( 'style_square' ); ?>" class="rhd_li_style_square" name="<?php echo $this->get_field_name( 'style' ); ?>" type="radio" value="rhd_lovely_style_square" <?php checked( $instance['style'], true ); ?> />
			<label for="<?php echo $this->get_field_id( 'style_square' ); ?>"><?php _e( 'Square (Caption: bottom)'); ?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'link' ); ?>"><?php _e( 'Image Link URL:' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>" type="text" value="<?php echo $args['link']; ?>" >
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'image' ); ?>"><?php _e( 'Image:' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'image' ); ?>" name="<?php echo $this->get_field_name( 'image' ); ?>" class="image_input widefat" type="text" value="<?php echo $args['image']; ?>" >

			<input class="upload_image_button button button-primary" type="button" value="Choose or Upload Image" />

			<img class="rhd_lovelyimage_preview" src="<?php echo $args['image']; ?>">
		</p>

<?php
	}
}
// register Foo_Widget widget
function register_rhd_lovelyimage_widget() {
    register_widget( 'RHD_LovelyImage' );
}
add_action( 'widgets_init', 'register_rhd_lovelyimage_widget' );