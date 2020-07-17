<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Ale_Clinic_Import
 * @subpackage Ale_Clinic_Import/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ale_Clinic_Import
 * @subpackage Ale_Clinic_Import/admin
 * @author     Your Name <email@example.com>
 */
class Ale_Clinic_Import_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $ale_clinic_import    The ID of this plugin.
	 */
	private $ale_clinic_import;

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
	 * @param      string    $ale_clinic_import       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $ale_clinic_import, $version ) {

		$this->ale_clinic_import = $ale_clinic_import;
		$this->version = $version;
		$this->upload_page = ALE_CI;
		$this->view = new Ale_Clinic_Import_View();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->ale_clinic_import, plugin_dir_url( __FILE__ ) . 'css/ale-clinic-import-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->ale_clinic_import, plugin_dir_url( __FILE__ ) . 'js/ale-clinic-import-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function plugin_init() {
		add_action('admin_menu', [$this,'xls_order_menu']);
	
	}


	public function xls_order_menu() {
		add_menu_page( 
			__('Импорт клиник', ALE_CI),
			__('Импорт клиник', ALE_CI),  
			'manage_options', 
			$this->upload_page,
			[$this, 'xls_order_menu_page'], 
			'dashicons-products', 
			120
		);
		
	}

	public function xls_order_menu_page(){
		
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if ( ! function_exists( 'wp_handle_upload' ) ) 
				require_once( ABSPATH . 'wp-admin/includes/file.php' ); 

			$file = & $_FILES['file_upload'];

			$overrides = [ 'test_form' => false ];

			$uploadedFileArr = wp_handle_upload( $file, $overrides );

			if ( $uploadedFileArr && empty($uploadedFileArr['error']) ): ?> 
				<?php $p = $this->parce_xlsx($uploadedFileArr['file']);
				if($p['result']):  ?>
					<div class="notice notice-success is-dismissible">
						<p><?php _e('XLSX импортирован', ALE_CI); ?></p>
					</div>
				<?php else: ?>
					<div class="notice notice-error is-dismissible"> 
						<p>
							<?php _e('Ошибка парсинга, не правильный формат файла', ALE_CI) ?>
						</p>
						<p>
							<?php echo $p['error'] ?>
						</p>
					</div>
				<?php endif; ?>

			<?php else: ?>
				<div class="notice notice-error is-dismissible"> 
					<p><?php _e('Ошибка загрузки файла', ALE_CI) ?></p>
				</div>
			<?php endif; 
		}

		?>
			<div class="wrap">
				<h2> <?php _e('Импорт XLSX файла', ALE_CI) ?></h2>
				<form method="post" enctype="multipart/form-data" action="
				<?php echo admin_url( 'admin.php?page='.$this->upload_page )?>">
					<input name="file_upload" type="file" />
					<p class="submit">  
						<input type="submit" class="button-primary" value="<?php _e('Импортировать') ?>" />  
					</p>
				</form>
			</div>
			
		<?php

	}

	function parce_xlsx($file) {
		//$file = ALE_CLINIC_IMPORT_PATH . '/Адреса+клиник+Мск.xlsx' ;
		if ( $xlsx = SimpleXLSX::parse($file) ) {
			$rows = $xlsx->rows();
			if(!$rows) {
				return [
					'result' => false,
					'error' => ''
				];
			}

			global $wpdb;

			$wpdb->query("TRUNCATE TABLE ". ALE_CLINICS_TABLE);

			$clinics = [];
			$i = 0;
			$stationsArrAll =[];
			foreach($rows as $xlsRow ) {
				$i++; if($i <= 2) {continue;}
				if(!$xlsRow[1]) {continue;}
				if(!$xlsRow[5]) {continue;}
				$stationsArrRaw = explode(",", $xlsRow[5]);

				$oneClinicArray = [];
				$oneClinicArray['address'] = $xlsRow[3] .', '. $xlsRow[4];
				//$oneClinicArray['stations_str'] = $xlsRow[5];
				
				foreach($stationsArrRaw as $stationRaw ) {
					$parts = explode("-", $stationRaw);
					$station = $parts[0];
					//$oneClinicArray['time'] = $parts[1];

					if($parts[1]) {
						$oneClinicArray['time_formated'] = strval(round(floatval($parts[1])/100)) . ' мин.';
					} else {
						$oneClinicArray['time_formated'] = '';
					}
					
					$oneClinicArray['station'] = $station;

					//$stationsArrAll[$station][] = $oneClinicArray;
					$wpdb->insert( ALE_CLINICS_TABLE, $oneClinicArray ) ;
				}

			}
			return [
				'result' => true,
			];   
			//print_r($stationsArrAll);die;
			
		} else {
			return [
				'result' => false,
				'error' => SimpleXLSX::parseError()
			];	
		}
	
	}
}

