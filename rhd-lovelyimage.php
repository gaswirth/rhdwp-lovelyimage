<?php
/**
 * Plugin Name: LovelyImage
 * Description: A simple image widget with CSS-stylable captions and optional image linkage.
 * Author: Roundhouse Designs
 * Author URI: http://roundhouse-designs.com
 * Version: 1.0.1
**/

define( 'RHD_LI_DIR', plugin_dir_url(__FILE__) );

class RHD_LovelyImage extends WP_Widget {
	function __construct() {
		parent::__construct(
			'rhd_lovelyimage', // Base ID
			__('LovelyImage', 'rhd'), // Name
			array( 'description' => __( 'A simple image widget with CSS-stylable captions and optional image linkage.', 'rhd' ), ) // Args
		);

		wp_enqueue_media();

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'display_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'display_styles' ) );
	}

    public function admin_scripts() {
		// wp_enqueue_media();
		wp_enqueue_script( 'upload_media_widget', RHD_LI_DIR . 'js/upload-media.js', array( 'jquery' ) );
		wp_enqueue_script( 'preview-image', RHD_LI_DIR . 'js/preview-image.js', array( 'jquery' ) );
	}

	public function admin_styles() {
		wp_enqueue_style( 'rhd-lovelyimage-admin', RHD_LI_DIR . 'css/rhd-lovelyimage-admin.css' );
	}

	public function display_scripts() {
		wp_enqueue_script( 'rhd-lovelyimage', RHD_LI_DIR . 'js/rhd-lovelyimage.js' );
	}

	public function display_styles() {
		wp_enqueue_style( 'rhd-lovelyimage', RHD_LI_DIR . 'css/rhd-lovelyimage.css' );
	}


	public function update($new_instance, $old_instance) {
		// processes widget options to be saved
		return $new_instance;
	}

	public function widget($args, $instance) {
		// outputs the content of the widget
		extract( $args );

		$caption = apply_filters('widget_title', $instance['title']);
		$link = $instance['link'];
		$img_url = $instance['image'];
		$cap_color = $instance['cap_color'];
		$cap_opacity[0] = $instance['opacity_base'];
		$cap_opacity[1] = $instance['opacity_hover'];

		echo $before_widget;
		?>

		<figure class="rhd_lovelyimage_container">
			<figcaption><?php echo $caption; ?></figcaption>
			<img src="<?php echo $img_url; ?>" alt="<?php echo $caption; ?>">
		</figure>

		<?php echo $after_widget;
	}

	public function form( $instance ) {
		// outputs the options form on admin
		$args['title'] = esc_attr( $instance['title'] );
		$args['link'] = esc_url( $instance['link'] );
		$args['image'] = esc_url( $instance['image'] );
		$args['cap_color'] = sanitize_hex_color( $instance['cap_color'] );
		$args['opacity_base'] = esc_attr( $instance['opacity_base'] );
		$args['opacity_hover'] = esc_attr( $instance['opacity_hover'] );
	?>

		<h3><?php _e( 'Caption Options:' ); ?></h3>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Widget Title/Caption:' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $args['title']; ?>" >
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'cap_color' ); ?>"><?php _e( 'Background Hex Color (include "#"):' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'cap_color' ); ?>" name="<?php echo $this->get_field_name( 'cap_color' ); ?>" type="text" value="<?php echo $args['cap_color']; ?>" placeholder="#ffffff">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'opacity_base' ); ?>"><?php _e( 'Background Base Opacity (0-1):' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'opacity_base' ); ?>" name="<?php echo $this->get_field_name( 'opacity_base' ); ?>" type="number" value="<?php echo $args['opacity_base']; ?>" placeholder="0.5">

			<label for="<?php echo $this->get_field_id( 'opacity_hover' ); ?>"><?php _e( 'Background Hover Opacity (0-1):' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'opacity_hover' ); ?>" name="<?php echo $this->get_field_name( 'opacity_hover' ); ?>" type="text" value="<?php echo $args['opacity_hover']; ?>" placeholder="0.8">
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