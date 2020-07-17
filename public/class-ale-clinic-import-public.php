<?php


class Ale_Clinic_Import_Public {

	private $ale_clinic_import;
	private $version;

	public function __construct( $ale_clinic_import, $version ) {
		$this->view = new Ale_Clinic_Import_View();
		$this->ale_clinic_import = $ale_clinic_import;
		$this->version = $version;
		$this->flash = new Ale_Clinic_Import_Flash();
		
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		//wp_enqueue_style( $this->ale_clinic_import, plugin_dir_url( __FILE__ ) . 'css/ale-clinic-import-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		
		//wp_enqueue_script( $this->ale_clinic_import, plugin_dir_url( __FILE__ ) . 
		//	'js/ale-clinic-import-public.js', array( 'jquery' ), $this->version, false );
	}

	public function plugin_init() {
			
	}

	public function init() {
		
	}

	public function wp() {
		
	}

}
