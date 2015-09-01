<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.champ.ninja/
 * @since      1.0.0
 *
 * @package    Aumd
 * @subpackage Aumd/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Aumd
 * @subpackage Aumd/admin
 * @author     Champ Camba <heychampsupertramp@gmail.com>
 */
class Aumd_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Aumd_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Aumd_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/aumd-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Aumd_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Aumd_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/aumd-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function audm_metabox_ajaxify_settings( $post_type, $post){

		 add_meta_box( 'aumd-ajax-settings', __( 'Ajax Settings' ), array($this,'audm_options_ajaxify_settings'),'um_directory','normal','default');
	}

	public function audm_options_ajaxify_settings(){

		$metabox = new UM_Admin_Metabox();
		?>
		<div class="um-admin-metabox">

			<div class="">
				
				<p>
					<label class="um-admin-half"><?php _e('Enable Search with Ajax','ultimatemember'); ?> <?php $metabox->tooltip('If turned on, users will be able to search members in this directory without reloading the page.'); ?></label>
					<span class="um-admin-half">
					
						<?php $metabox->ui_on_off('_um_ajax_settings', 0, true, 1, 'enable-ajax', 'xxx'); ?>
						
					</span>
				</p>
			</div>
		</div>
		<div class="clear clearfix"></div>


		<?php
	}

}
