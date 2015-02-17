<?php
/**
 * Plugin Name: LovelyImage
 * Description: A simple image widget with CSS-stylable captions and optional image linkage.
 * Author: Roundhouse Designs
 * Author URI: http://roundhouse-designs.com
 * Version: 0.1
**/

define( 'RHD_LI_DIR', plugin_dir_url(__FILE__) );

class RHD_LovelyImage extends WP_Widget {
	function __construct() {
		parent::__construct(
			'rhd_lovelyimage', // Base ID
			__('LovelyImage', 'rhd'), // Name
			array( 'description' => __( 'A simple image widget with CSS-stylable captions and optional image linkage.', 'rhd' ), ) // Args
		);
		add_action( 'admin_enqueue_scripts', array( $this, 'upload_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'display_styles' ) );
	}

	/**
     * Upload the Javascripts for the media uploader
     */
    public function upload_scripts() {
		wp_enqueue_media();
		wp_enqueue_script( 'upload_media_widget', RHD_LI_DIR . 'upload-media.js', array( 'jquery' ) );
	}

	/**
     * Add the styles for the upload media box
	 */
	public function display_styles() {
		wp_enqueue_style( 'rhd-lovelyimage', RHD_LI_DIR . 'rhd-lovelyimage.css' );
	}


	public function update($new_instance, $old_instance) {
		// processes widget options to be saved
		return $new_instance;
	}

	public function widget($args, $instance) {
		// outputs the content of the widget
		extract( $args );

		$caption = apply_filters('widget_title', $instance['title']);
		$imgUrl = $instance['image'];
		$link = $instance['link'];

		echo $before_widget;
		?>

		<figure class="rhd_lovelyimage_container">
			<figcaption><?php echo $caption; ?></figcaption>
			<img src="<?php echo $imgUrl; ?>" alt="<?php echo $caption; ?>">
		</figure>

		<?php echo $after_widget;
	}

	public function form( $instance ) {
		// outputs the options form on admin
		$args['title'] = esc_attr( $instance['title'] );
		$args['link'] = esc_url( $instance['link'] );
		$args['image'] = esc_url( $instance['image'] );
	?>
		<style type="text/css">
			.rhd_lovelyimage_preview {
				max-width: 90%;
				height: auto;
				display: block;
				margin: 1em auto;
			}
		</style>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Widget Title/Caption:' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $args['title']; ?>" >
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'link' ); ?>"><?php _e( 'Image Link URL:' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>" type="text" value="<?php echo $args['link']; ?>" >
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'image' ); ?>"><?php _e( 'Image:' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'image' ); ?>" name="<?php echo $this->get_field_name( 'image' ); ?>" class="image_input widefat" type="text" value="<?php echo $args['image']; ?>" >

			<input class="upload_image_button button button-primary" type="button" value="Upload Image" />

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