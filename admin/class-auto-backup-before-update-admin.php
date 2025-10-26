<?php
/**
 * This file contains the definition of the Auto_Backup_Before_Update_Admin class, which
 * is used to load the plugin's admin-specific functionality.
 *
 * @package       Auto_Backup_Before_Update
 * @subpackage    Auto_Backup_Before_Update/admin
 * @author        Sajjad Hossain Sagor <sagorh672@gmail.com>
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version and other methods.
 *
 * @since    1.0.0
 */
class Auto_Backup_Before_Update_Admin {
	/**
	 * The ID of this plugin.
	 *
	 * @since     1.0.0
	 * @access    private
	 * @var       string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since     1.0.0
	 * @access    private
	 * @var       string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * The plugin options api wrapper object.
	 *
	 * @since     1.0.0
	 * @access    private
	 * @var       array $settings_api Holds the plugin options api wrapper class object.
	 */
	private $settings_api;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @param     string $plugin_name The name of this plugin.
	 * @param     string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name  = $plugin_name;
		$this->version      = $version;
		$this->settings_api = new Auto_Backup_Before_Update_Settings_API();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function enqueue_styles() {
		$current_screen = get_current_screen();

		// check if current page is plugin settings page.
		if ( 'abbu_page_auto-backup-before-update-plugins' === $current_screen->id || 'abbu_page_auto-backup-before-update-themes' === $current_screen->id ) {
			wp_enqueue_style( $this->plugin_name, ABBU_AUTO_BACKUP_BEFORE_UPDATE_PLUGIN_URL . 'admin/css/admin.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function enqueue_scripts() {
		$current_screen = get_current_screen();

		// check if current page is plugin settings page.
		if ( 'abbu_page_auto-backup-before-update-plugins' === $current_screen->id || 'abbu_page_auto-backup-before-update-themes' === $current_screen->id ) {
			wp_enqueue_script( $this->plugin_name, ABBU_AUTO_BACKUP_BEFORE_UPDATE_PLUGIN_URL . 'admin/js/admin.js', array( 'jquery' ), $this->version, false );

			wp_localize_script(
				$this->plugin_name,
				'AbbuAutoBackupBeforeUpdate',
				array(
					'ajaxurl'            => admin_url( 'admin-ajax.php' ),
					'_wpnonce'           => wp_create_nonce( 'auto_backup_action_nonce' ),
					'deleteConfirmMsg'   => __( 'Are you sure you want to delete the backup?', 'auto-backup-before-update' ),
					'restoreConfirmMsg'  => __( 'Are you sure you want to restore the backup?', 'auto-backup-before-update' ),
					'restoreConfirmMsg2' => __( 'THIS IS A DOUBLE CONFIRMATION. Are you sure you want to restore the backup? It will overwrite current files and not recoverable. Take action carefully.', 'auto-backup-before-update' ),
					'processingTxt'      => __( 'Processing....', 'auto-backup-before-update' ),
					'dismissNoticeTxt'   => __( 'Dismiss this notice.', 'auto-backup-before-update' ),
					'requestFailedTxt'   => __( 'Request failed. Please try again.', 'auto-backup-before-update' ),
				)
			);
		}
	}

	/**
	 * Adds a settings link to the plugin's action links on the plugin list table.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @param     array $links The existing array of plugin action links.
	 * @return    array $links The updated array of plugin action links, including the settings link.
	 */
	public function add_plugin_action_links( $links ) {
		$links[] = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'admin.php?page=auto-backup-before-update' ) ), __( 'Settings', 'auto-backup-before-update' ) );

		return $links;
	}

	/**
	 * Adds the plugin settings page to the WordPress dashboard menu.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function admin_menu() {
		add_menu_page(
			__( 'Auto Backup Before Update', 'auto-backup-before-update' ),
			__( 'ABBU', 'auto-backup-before-update' ),
			'manage_options',
			'auto-backup-before-update',
			array( $this, 'menu_page' ),
			'dashicons-backup',
		);

		add_submenu_page(
			'auto-backup-before-update',
			__( 'Auto Backup Before Update', 'auto-backup-before-update' ),
			__( 'Settings', 'auto-backup-before-update' ),
			'manage_options',
			'auto-backup-before-update',
			array( $this, 'menu_page' ),
		);

		add_submenu_page(
			'auto-backup-before-update',
			__( 'Auto Backup Before Update - Plugins', 'auto-backup-before-update' ),
			__( 'Plugin Backups', 'auto-backup-before-update' ),
			'manage_options',
			'auto-backup-before-update-plugins',
			array( $this, 'submenu_plugins_page' ),
		);

		add_submenu_page(
			'auto-backup-before-update',
			__( 'Auto Backup Before Update - Themes', 'auto-backup-before-update' ),
			__( 'Theme Backups', 'auto-backup-before-update' ),
			'manage_options',
			'auto-backup-before-update-themes',
			array( $this, 'submenu_themes_page' ),
		);
	}

	/**
	 * Renders the plugin menu page content.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function menu_page() {
		$this->settings_api->show_forms();
	}

	/**
	 * Renders the plugin menu page content.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function submenu_plugins_page() {
		$backup_storage_dir = self::get_backup_storage_dir();
		$plugins_list_table = new Auto_Backup_Before_Update_Backup_Table( $backup_storage_dir . 'plugins' );
		$plugins_list_table->prepare_items();

		echo '<div class="wrap abbu_backups abbu_plugin_backups">';
			$plugins_list_table->display();
		echo '</div>';
	}

	/**
	 * Renders the plugin menu page content.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function submenu_themes_page() {
		$backup_storage_dir = self::get_backup_storage_dir();
		$themes_list_table  = new Auto_Backup_Before_Update_Backup_Table( $backup_storage_dir . 'themes' );
		$themes_list_table->prepare_items();

		echo '<div class="wrap abbu_backups abbu_theme_backups">';
			$themes_list_table->display();
		echo '</div>';
	}

	/**
	 * Initializes admin-specific functionality.
	 *
	 * This function is hooked to the 'admin_init' action and is used to perform
	 * various administrative tasks, such as registering settings, enqueuing scripts,
	 * or adding admin notices.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @throws    Exception If failure occurs.
	 */
	public function admin_init() {
		// set the settings.
		$this->settings_api->set_sections( $this->get_settings_sections() );

		$this->settings_api->set_fields( $this->get_settings_fields() );

		// initialize settings.
		$this->settings_api->admin_init();
	}

	/**
	 * Initializes backup ajax functionality.
	 *
	 * This function is hooked to the 'wp_ajax_abbu_backup_manager' action and
	 * is used to perform backup related ajax tasks.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @throws    Exception If failure occurs.
	 */
	public function backup_manager() {
		check_ajax_referer( 'auto_backup_action_nonce', '_wpnonce', true );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Access denied. Administrator permissions are required.', 'auto-backup-before-update' ),
				)
			);
		}

		if (
			isset( $_POST['backup_action'], $_POST['version'], $_POST['slug'], $_POST['type'] )
			&& 'delete' === $_POST['backup_action']
		) {
			global $wp_filesystem;

			$slug    = sanitize_text_field( wp_unslash( $_POST['slug'] ) );
			$type    = sanitize_text_field( wp_unslash( $_POST['type'] ) );
			$version = sanitize_text_field( wp_unslash( $_POST['version'] ) );

			// Initialize filesystem.
			if ( ! function_exists( 'WP_Filesystem' ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}

			WP_Filesystem();

			// Define base directories for backups.
			$backup_storage_dir         = self::get_backup_storage_dir();
			$plugins_backup_storage_dir = $backup_storage_dir . 'plugins';
			$themes_backup_storage_dir  = $backup_storage_dir . 'themes';

			// Determine target directory based on type.
			if ( 'plugin' === $type ) {
				$target_dir = trailingslashit( $plugins_backup_storage_dir ) . $slug . '/' . $version;
			} elseif ( 'theme' === $type ) {
				$target_dir = trailingslashit( $themes_backup_storage_dir ) . $slug . '/' . $version;
			} else {
				wp_send_json_error(
					array(
						'message' => esc_html__( 'Invalid backup type provided.', 'auto-backup-before-update' ),
					)
				);
			}

			// Delete folder recursively if it exists.
			if ( $wp_filesystem->is_dir( $target_dir ) ) {
				self::delete_folders_recursively( $target_dir );
				wp_send_json_success(
					array(
						'message' => esc_html__( 'Backup sucessfully deleted!', 'auto-backup-before-update' ),
					)
				);
			} else {
				wp_send_json_error(
					array(
						'message' => esc_html__( 'Backup folder not found.', 'auto-backup-before-update' ),
					)
				);
			}
		}

		if (
			isset( $_POST['backup_action'], $_POST['version'], $_POST['slug'], $_POST['type'] )
			&& 'restore' === $_POST['backup_action']
		) {
			global $wp_filesystem;

			$slug    = sanitize_text_field( wp_unslash( $_POST['slug'] ) );
			$type    = sanitize_text_field( wp_unslash( $_POST['type'] ) );
			$version = sanitize_text_field( wp_unslash( $_POST['version'] ) );

			// Initialize filesystem.
			if ( ! function_exists( 'WP_Filesystem' ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}

			WP_Filesystem();

			// Define base directories for backups.
			$backup_storage_dir         = self::get_backup_storage_dir();
			$plugins_backup_storage_dir = $backup_storage_dir . 'plugins';
			$themes_backup_storage_dir  = $backup_storage_dir . 'themes';

			// Determine target directory based on type.
			if ( 'plugin' === $type ) {
				$target_dir = trailingslashit( $plugins_backup_storage_dir ) . $slug . '/' . $version;
				$live_dir   = trailingslashit( WP_PLUGIN_DIR ) . $slug;
			} elseif ( 'theme' === $type ) {
				$target_dir = trailingslashit( $themes_backup_storage_dir ) . $slug . '/' . $version;
				$live_dir   = trailingslashit( get_theme_root() ) . $slug;
			} else {
				wp_send_json_error(
					array(
						'message' => esc_html__( 'Invalid backup type provided.', 'auto-backup-before-update' ),
					)
				);
			}

			// Enable maintenance mode.
			self::enable_maintenance_mode();

			try {
				// Remove current live version if it exists.
				if ( $wp_filesystem->is_dir( $target_dir ) ) {
					self::delete_folders_recursively( $live_dir );

					// Find the backup zip file.
					$zip_files = glob( trailingslashit( $target_dir ) . '*.zip' );

					if ( empty( $zip_files ) ) {
						throw new Exception( 'Backup ZIP file not found for this version.' );
					}

					if ( ! empty( $zip_files ) ) {
						$backup_zip = $zip_files[0];

						// Unzip the backup into the live directory.
						if ( class_exists( 'ZipArchive' ) ) {
							$zip = new ZipArchive();
							if ( $zip->open( $backup_zip ) === true ) {
								$zip->extractTo( $live_dir );
								$zip->close();
							} else {
								throw new Exception( 'Unable to open the backup ZIP file.' );
							}
						} else {
							throw new Exception( 'Your PHP installation does not support ZIP extraction.' );
						}
					}
				} else {
					throw new Exception( 'Backup dir not found.' );
				}
			} catch ( Exception $e ) {
				wp_send_json_error(
					array(
						'message' => $e->get_message(),
					)
				);
			} finally {
				// Disable maintenance mode.
				self::disable_maintenance_mode();
			}

			wp_send_json_success(
				array(
					'message' => esc_html__( 'Backup sucessfully restored!', 'auto-backup-before-update' ),
				)
			);
		}
	}

	/**
	 * Enable maintenance mode.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @static
	 * @return    void
	 */
	public static function enable_maintenance_mode() {
		$maintenance_file = ABSPATH . '.maintenance';

		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		WP_Filesystem();

		if ( ! file_exists( $maintenance_file ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export
			$wp_filesystem->put_contents( $maintenance_file, '<?php $upgrading = ' . time() . ';' );
		}
	}

	/**
	 * Disable maintenance mode.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @static
	 * @return    void
	 */
	public static function disable_maintenance_mode() {
		$maintenance_file = ABSPATH . '.maintenance';

		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		WP_Filesystem();

		if ( file_exists( $maintenance_file ) ) {
			$wp_filesystem->delete( $maintenance_file );
		}
	}


	/**
	 * Returns the settings sections for the plugin settings page.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    array An array of settings sections, where each section is an array
	 *                  with 'id' and 'title' keys.
	 */
	public function get_settings_sections() {
		$settings_sections = array(
			array(
				'id'    => 'abbu_basic_settings',
				'title' => __( 'General', 'auto-backup-before-update' ),
			),
		);

		/**
		 * Filters the plugin settings sections.
		 *
		 * This filter allows you to modify the plugin settings sections.
		 * You can use this filter to add/remove/edit any settings sections.
		 *
		 * @since     1.0.0
		 * @param     array $settings_sections Default settings sections.
		 * @return    array $settings_sections Modified settings sections.
		 */
		return apply_filters( 'abbu_settings_sections', $settings_sections );
	}

	/**
	 * Returns all the settings fields for the plugin settings page.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    array An array of settings fields, organized by section ID.  Each
	 *                  section ID is a key in the array, and the value is an array
	 *                  of settings fields for that section. Each settings field is
	 *                  an array with 'name', 'label', 'type', 'desc', and other keys
	 *                  depending on the field type.
	 */
	public function get_settings_fields() {
		$backup_storage_dir = self::get_backup_storage_dir();

		$settings_fields = array(
			'abbu_basic_settings' => array(
				array(
					'name'  => 'enable',
					'label' => __( 'Enable/Disable', 'auto-backup-before-update' ),
					'desc'  => __( 'Enable automatic backups before updates.', 'auto-backup-before-update' ),
					'type'  => 'checkbox',
				),
				array(
					'name'  => 'backup_storage_dir',
					'label' => __( 'Backup Storage Path', 'auto-backup-before-update' ),
					'type'  => 'html',
					'desc'  => wp_kses_post( sprintf( '<code>%s</code>', $backup_storage_dir ) ),
				),
				array(
					'name'    => 'retention_limit',
					'label'   => __( 'Backup Retention Limit', 'auto-backup-before-update' ),
					'desc'    => __( 'Number of backup sets to keep per item (older ones will be deleted automatically).', 'auto-backup-before-update' ),
					'type'    => 'number',
					'default' => 7,
				),
			),
		);

		/**
		 * Filters the plugin settings fields.
		 *
		 * This filter allows you to modify the plugin settings fields.
		 * You can use this filter to add/remove/edit any settings field.
		 *
		 * @since     1.0.0
		 * @param     array $settings_fields Default settings fields.
		 * @return    array $settings_fields Modified settings fields.
		 */
		return apply_filters( 'abbu_settings_fields', $settings_fields );
	}

	/**
	 * Displays admin notices in the admin area.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function admin_notices() {
		// Check if backup storage directory is writable.
		if ( ! self::is_backup_storage_dir_writable() ) {
			wp_admin_notice(
				__( 'Upload Directory is not writable! Please make it writable to store backup files.', 'auto-backup-before-update' ),
				array(
					'type'        => 'error',
					'dismissible' => true,
				),
			);
		}
	}

	/**
	 * Pre-update backup handler: copy current plugin or theme to backup folder.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @param     mixed $response   Original response from upgrader.
	 * @param     array $hook_extra Hook extra data (contains plugin/theme info).
	 * @return    mixed
	 */
	public function on_upgrader_pre_install( $response, $hook_extra ) {
		$enabled = Auto_Backup_Before_Update::get_option( 'enable', 'abbu_basic_settings', 'off' );

		if ( 'on' !== $enabled ) {
			return $response;
		}

		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		WP_Filesystem();

		$backup_storage_dir         = self::get_backup_storage_dir();
		$plugins_backup_storage_dir = trailingslashit( $backup_storage_dir ) . 'plugins';
		$themes_backup_storage_dir  = trailingslashit( $backup_storage_dir ) . 'themes';

		$type          = '';
		$slug          = '';
		$version       = '';
		$name          = '';
		$src_path      = '';
		$dest_real_dir = '';
		$dest_dir      = '';
		$file_name     = '';

		if ( isset( $hook_extra['plugin'] ) ) {
			$type          = 'plugin';
			$plugin_file   = $hook_extra['plugin'];
			$plugin_data   = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_file, false, false );
			$file_name     = $plugin_file;
			$slug          = dirname( $plugin_file );
			$version       = $plugin_data['Version'] ?? 'unknown';
			$name          = $plugin_data['Name'] ?? $slug;
			$src_path      = trailingslashit( WP_PLUGIN_DIR ) . $slug;
			$dest_real_dir = trailingslashit( $plugins_backup_storage_dir ) . $slug;
			$dest_dir      = trailingslashit( $dest_real_dir ) . $version;
		} elseif ( isset( $hook_extra['theme'] ) ) {
			$type          = 'theme';
			$slug          = $hook_extra['theme'];
			$theme         = wp_get_theme( $slug );
			$file_name     = $slug;
			$version       = $theme->get( 'Version' ) ?? 'unknown';
			$name          = $theme->get( 'Name' ) ?? $slug;
			$src_path      = $theme->get_stylesheet_directory();
			$dest_real_dir = trailingslashit( $themes_backup_storage_dir ) . $slug;
			$dest_dir      = trailingslashit( $dest_real_dir ) . $version;
		}

		if ( empty( $slug ) || empty( $src_path ) || empty( $dest_real_dir ) || empty( $dest_dir ) ) {
			return $response;
		}

		// Create backup folder structure.
		self::create_folders_recursively( $dest_dir );

		// Paths to source files in the plugin.
		$files_to_copy = array(
			ABBU_AUTO_BACKUP_BEFORE_UPDATE_PLUGIN_PATH . 'admin/views/abbu-backups/index.html',
			ABBU_AUTO_BACKUP_BEFORE_UPDATE_PLUGIN_PATH . 'admin/views/abbu-backups/original.htaccess',
		);

		// Copy each file to the backup folder.
		foreach ( $files_to_copy as $file_to_copy ) {
			if ( $wp_filesystem->exists( $file_to_copy ) ) {
				$destination = trailingslashit( $dest_dir ) . basename( str_replace( 'original', '', $file_to_copy ) );

				if ( ! $wp_filesystem->exists( $destination ) ) {
					$wp_filesystem->copy( $file_to_copy, $destination, true, FS_CHMOD_FILE );
				}
			}
		}

		// Copy plugin/theme files.
		self::recursive_copy( $src_path, $dest_dir );

		// Create config.json with metadata.
		self::create_backup_config( $dest_dir, $type, $slug, $name, $version, $file_name );

		$retention_limit = Auto_Backup_Before_Update::get_option( 'retention_limit', 'abbu_basic_settings', 7 );
		self::enforce_backup_retention( $dest_real_dir, $retention_limit );

		return $response;
	}

	/**
	 * Creates a ZIP archive of the given source directory and stores it in the backup folder.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @static
	 * @param     string $src  Source directory.
	 * @param     string $dest Destination directory.
	 * @return    void
	 */
	private static function recursive_copy( $src, $dest ) {
		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		WP_Filesystem();

		if ( ! class_exists( 'ZipArchive' ) ) {
			return;
		}

		$zip          = new ZipArchive();
		$slug         = basename( untrailingslashit( $src ) );
		$zip_filename = sprintf( '%s.zip', sanitize_file_name( $slug ) );
		$zip_path     = trailingslashit( $dest ) . $zip_filename;

		if ( true === $zip->open( $zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE ) ) {
			$src_real = realpath( $src );

			$files = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator( $src_real, RecursiveDirectoryIterator::SKIP_DOTS ),
				RecursiveIteratorIterator::SELF_FIRST
			);

			foreach ( $files as $file ) {
				$file_path     = realpath( $file );
				$relative_path = str_replace( $src_real . DIRECTORY_SEPARATOR, '', $file_path );

				if ( is_dir( $file_path ) ) {
					$zip->addEmptyDir( $relative_path );
				} elseif ( is_file( $file_path ) ) {
					$zip->addFile( $file_path, $relative_path );
				}
			}

			$zip->close();

			// Ensure file permissions via WP_Filesystem.
			if ( file_exists( $zip_path ) ) {
				$wp_filesystem->chmod( $zip_path, FS_CHMOD_FILE );
			}
		}
	}

	/**
	 * Create a config.json metadata file for the backup.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @static
	 * @param     string $dest_dir  Full path to the backup folder (plugin/theme version folder).
	 * @param     string $type      'plugin' or 'theme'.
	 * @param     string $slug      Plugin or theme slug.
	 * @param     string $name      Name of the plugin/theme.
	 * @param     string $version   Plugin or theme version.
	 * @param     string $file_name Plugin or theme file name.
	 * @return    void
	 */
	private static function create_backup_config( $dest_dir, $type, $slug, $name, $version, $file_name ) {
		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		WP_Filesystem();

		$config_data = array(
			'type'      => $type,
			'slug'      => $slug,
			'name'      => $name,
			'version'   => $version,
			'file_name' => $file_name,
			'time'      => gmdate( 'Y-m-d H:i:s' ),
		);

		$config_file = trailingslashit( $dest_dir ) . 'config.json';
		$wp_filesystem->put_contents( $config_file, wp_json_encode( $config_data, JSON_PRETTY_PRINT ), FS_CHMOD_FILE );
	}

	/**
	 * Enforce retention limit on a backup folder.
	 *
	 * Deletes oldest backups if total exceeds $retention_limit.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @static
	 * @param     string $backup_folder   Full path to backup folder.
	 * @param     int    $retention_limit Maximum number of backups to keep.
	 * @return    void
	 */
	private static function enforce_backup_retention( $backup_folder, $retention_limit = 5 ) {
		global $wp_filesystem;

		if ( ! $wp_filesystem->exists( $backup_folder ) || ! $wp_filesystem->is_dir( $backup_folder ) ) {
			return;
		}

		// Get all backup files/folders sorted by modification time ascending (oldest first).
		$all_backups = array();

		foreach ( $wp_filesystem->dirlist( $backup_folder ) as $name => $info ) {
			$all_backups[] = array(
				'path' => trailingslashit( $backup_folder ) . $name,
				'time' => $info['lastmodunix'],
			);
		}

		// Sort oldest first.
		usort(
			$all_backups,
			function ( $a, $b ) {
				return $a['time'] <=> $b['time'];
			}
		);

		// Delete oldest if count exceeds retention limit.
		$total_backups = count( $all_backups );
		if ( $total_backups <= $retention_limit ) {
			return;
		}

		$to_delete = array_slice( $all_backups, 0, $total_backups - $retention_limit );

		foreach ( $to_delete as $backup ) {
			self::delete_folders_recursively( $backup['path'] );
		}
	}

	/**
	 * Retrieve all installed plugins.
	 *
	 * Returns an associative array of plugin slugs and names.
	 * The slug is derived from the directory name of the plugin file.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @static
	 * @return    array $plugins Associative array of plugin slugs as keys and plugin names as values.
	 */
	public static function get_installed_plugins() {
		// Ensure plugin.php is loaded for get_plugins().
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$_plugins = get_plugins();
		$plugins  = array();

		foreach ( $_plugins as $path => $plugin ) {
			$slug             = dirname( $path );
			$name             = isset( $plugin['Name'] ) ? $plugin['Name'] : $slug;
			$plugins[ $slug ] = $name;
		}

		return $plugins;
	}

	/**
	 * Retrieve all installed themes.
	 *
	 * Returns an associative array of theme slugs and names.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @static
	 * @return    array $themes Associative array of theme slugs as keys and theme names as values.
	 */
	public static function get_installed_themes() {
		$_themes = wp_get_themes();
		$themes  = array();

		foreach ( $_themes as $slug => $theme ) {
			$name            = $theme->get( 'Name' );
			$themes[ $slug ] = $name;
		}

		return $themes;
	}

	/**
	 * Retrieve the backup storage path for Auto Backup Before Update plugin.
	 *
	 * Returns the path as string.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @static
	 * @param     bool   $base_dir_only Optional. If true, returns only the base upload directory.
	 * @param     string $dir           Optional. The directory within the uploads array to use. Defaults to 'basedir'.
	 * @return    string $path Absolute path to the backup storage directory.
	 */
	public static function get_backup_storage_dir( $base_dir_only = false, $dir = 'basedir' ) {
		$upload     = wp_upload_dir();
		$upload_dir = $upload[ $dir ];

		if ( $base_dir_only ) {
			return $upload_dir;
		}

		return wp_normalize_path( trailingslashit( $upload_dir . '/abbu-backups' ) );
	}

	/**
	 * Deletes a folder and all its contents recursively.
	 *
	 * This function deletes a specified folder and all files and subfolders within it.
	 * It recursively traverses subfolders to ensure all content is removed.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @static
	 * @param     string $folder The path to the folder to be deleted.
	 * @return    bool           True if the folder and its contents were successfully deleted, false otherwise.
	 */
	public static function delete_folders_recursively( $folder ) {
		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		WP_Filesystem();

		// Ensure the folder exists and is a directory.
		if ( ! $wp_filesystem->is_dir( $folder ) ) {
			return false;
		}

		// Get the list of files and folders.
		$dir_list = $wp_filesystem->dirlist( $folder );
		if ( empty( $dir_list ) ) {
			// Empty folder, just delete it.
			return $wp_filesystem->delete( $folder, true );
		}

		foreach ( $dir_list as $file => $file_info ) {
			$path = trailingslashit( $folder ) . $file;

			if ( 'd' === $file_info['type'] ) {
				// Recursively delete subfolder.
				self::delete_folders_recursively( $path );
			}

			// Delete file or folder.
			$wp_filesystem->delete( $path, true );
		}

		// Finally, delete the main folder.
		return $wp_filesystem->delete( $folder, true );
	}

	/**
	 * Create a directory recursively using WP_Filesystem.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @static
	 * @param     string $path The directory path.
	 * @return    bool         True on success, false on failure.
	 */
	public static function create_folders_recursively( $path ) {
		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		WP_Filesystem();

		$path = wp_normalize_path( $path );

		// If the directory already exists, return true.
		if ( $wp_filesystem->is_dir( $path ) ) {
			return true;
		}

		// Get the parent directory.
		$parent_dir = dirname( $path );

		// Recursively create parent directories first.
		if ( ! $wp_filesystem->is_dir( $parent_dir ) ) {
			self::create_folders_recursively( $parent_dir );
		}

		// Create the directory.
		return $wp_filesystem->mkdir( $path, FS_CHMOD_DIR );
	}

	/**
	 * Creates the backup storage directory if it does not exist.
	 *
	 * This function checks if the backup storage directory, as determined by
	 * `self::get_backup_storage_dir()`, exists. If it does not, it creates the directory
	 * with permissions 0700 (owner read, write, execute).
	 *
	 * @since     1.0.0
	 * @access    public
	 * @static
	 * @return    void
	 */
	public static function create_backup_storage_dir() {
		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		WP_Filesystem();

		$backup_storage_dir         = self::get_backup_storage_dir();
		$plugins_backup_storage_dir = $backup_storage_dir . 'plugins';
		$themes_backup_storage_dir  = $backup_storage_dir . 'themes';

		self::create_folders_recursively( $plugins_backup_storage_dir );
		self::create_folders_recursively( $themes_backup_storage_dir );
	}

	/**
	 * Checks if the backup storage directory is writable.
	 *
	 * This function determines if the backup storage directory is writable by
	 * checking the directory returned by `self::get_backup_storage_dir()`.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @static
	 * @return    bool True if the upload directory is writable, false otherwise.
	 */
	public static function is_backup_storage_dir_writable() {
		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		WP_Filesystem();

		return $wp_filesystem->is_writable( self::get_backup_storage_dir( true ) );
	}
}
