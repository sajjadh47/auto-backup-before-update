<?php
/**
 * WP_List_Table is not loaded automatically so we need to load it manually.
 *
 * @package    Auto_Backup_Before_Update
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Abstract base class for backups tables.
 *
 * This class extends WP_List_Table to provide a foundation for creating
 * tables that display backups information in the WordPress admin.
 *
 * @version       1.0.0
 * @package       Auto_Backup_Before_Update
 * @subpackage    Auto_Backup_Before_Update/includes
 */
class Auto_Backup_Before_Update_Backup_Table extends WP_List_Table {
	/**
	 * Backup storage folder path
	 *
	 * @var    string
	 */
	private $backup_dir;

	/**
	 * Constructor for the class.
	 *
	 * This constructor sets up the class properties, including the singular and
	 * plural labels for the list table.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @param     string $backup_dir Path to backups folder.
	 * @return    void
	 */
	public function __construct( $backup_dir ) {
		parent::__construct(
			array(
				'singular' => __( 'Backup', 'auto-backup-before-update' ),
				'plural'   => __( 'Backups', 'auto-backup-before-update' ),
				'ajax'     => false,
			)
		);

		$this->backup_dir = $backup_dir;
	}

	/**
	 * Get the columns for the table.
	 *
	 * This method returns an array defining the columns to be displayed in the table.
	 * The keys of the array are the column IDs, and the values are the column labels.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    array An associative array of column IDs and labels.
	 */
	public function get_columns() {
		return array(
			'name'     => __( 'Name', 'auto-backup-before-update' ),
			'slug'     => __( 'Slug', 'auto-backup-before-update' ),
			'current'  => __( 'Current Version', 'auto-backup-before-update' ),
			'hidden'   => '',
			'versions' => __( 'Backup Versions', 'auto-backup-before-update' ),
		);
	}

	/**
	 * Default column handler.
	 *
	 * This method handles the display of data for columns that don't have a
	 * specific column rendering method defined (e.g., column_title()).  It
	 * retrieves the value from the `$item` array using the column name.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @param     array  $item        The data for the current table row.
	 * @param     string $column_name The name of the column being rendered.
	 * @return    mixed               The data to display for the column, or an empty string if the column is not found.
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'name':
			case 'slug':
				return esc_html( $item[ $column_name ] ?? '' );

			case 'current':
				if ( 'plugin' === $item['type'] ) {
					$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $item['file_name'], false, false );
					$version     = $plugin_data['Version'] ?? 'unknown';
				} elseif ( 'theme' === $item['type'] ) {
					$theme   = wp_get_theme( $item['file_name'] );
					$version = $theme->get( 'Version' ) ?? 'unknown';
				}

				return esc_html( $version );

			case 'versions':
				$output  = '<table class="widefat striped versions_table">';
				$output .= '<thead><tr>';
				$output .= '<th>' . esc_html__( 'Version', 'auto-backup-before-update' ) . '</th>';
				$output .= '<th>' . esc_html__( 'Created At', 'auto-backup-before-update' ) . '</th>';
				$output .= '<th>' . esc_html__( 'Actions', 'auto-backup-before-update' ) . '</th>';
				$output .= '</tr></thead><tbody>';

				foreach ( $item['versions'] as $v ) {
					$output .= '<tr>';
					$output .= '<td>' . esc_html( $v['version'] ) . '</td>';
					$output .= '<td>' . esc_html( $v['time'] ) . '</td>';
					$output .= '<td class="backup_actions">';
					$output .= sprintf(
						'<button type="button" 
							class="button button-secondary button-small restore-backup"
							data-action="restore"
							data-version="%1$s" 
							data-slug="%2$s" 
							data-type="%3$s">%4$s</button>

						 <button type="button" 
							class="button button-link-delete button-small delete-backup"
							data-action="delete"
							data-version="%1$s" 
							data-slug="%2$s" 
							data-type="%3$s">%5$s</button>',
						esc_attr( $v['version'] ),
						esc_attr( $v['slug'] ),
						esc_attr( $v['type'] ),
						esc_html__( 'Restore', 'auto-backup-before-update' ),
						esc_html__( 'Delete', 'auto-backup-before-update' ),
					);
					$output .= '</td>';
					$output .= '</tr>';
				}

				$output .= '</tbody></table>';
				return $output;

			default:
				return '';
		}
	}

	/**
	 * Message to display when no items are found.
	 *
	 * This method outputs a user-friendly message when there are no items
	 * to display in the table.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    void
	 */
	public function no_items() {
		esc_html_e( 'No items are available.', 'auto-backup-before-update' );
	}

	/**
	 * Prepare the items for display.
	 *
	 * This method fetches the data for the table, sets up the column headers,
	 * and configures pagination.  It calls the abstract method {@see get_items()}
	 * to retrieve the actual data.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    void
	 */
	public function prepare_items() {
		$all_items             = $this->get_backup_items();
		$columns               = $this->get_columns();
		$per_page              = $this->get_items_per_page( 'logs_per_page', 10 );
		$current_page          = $this->get_pagenum();
		$total_items           = count( $all_items );
		$this->_column_headers = array( $columns, array(), array() );

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);

		$this->items = array_slice( $all_items, ( $current_page - 1 ) * $per_page, $per_page );
	}

	/**
	 * Gather backups, grouped by slug
	 *
	 * @since     1.0.0
	 * @access    private
	 * @return    array
	 */
	private function get_backup_items() {
		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		WP_Filesystem();

		$grouped = array();

		// Get plugin/theme folders.
		$folders = $wp_filesystem->dirlist( $this->backup_dir, true );
		if ( empty( $folders ) ) {
			return $grouped;
		}

		foreach ( $folders as $slug => $info ) {
			$slug_path = trailingslashit( $this->backup_dir ) . $slug;

			if ( $wp_filesystem->is_dir( $slug_path ) ) {
				$versions_list = array();

				$versions = $wp_filesystem->dirlist( $slug_path, true );
				foreach ( $versions as $version => $v_info ) {
					$version_path = trailingslashit( $slug_path ) . $version;
					$config_file  = trailingslashit( $version_path ) . 'config.json';

					if ( $wp_filesystem->exists( $config_file ) ) {
						$config_data = json_decode( $wp_filesystem->get_contents( $config_file ), true );
						if ( $config_data ) {
							$config_data['time'] = gmdate( 'F j, Y g:i A', strtotime( $config_data['time'] ) );
							$versions_list[]     = $config_data;
						}
					}
				}

				if ( ! empty( $versions_list ) ) {
					// Sort versions by time descending.
					usort(
						$versions_list,
						function ( $a, $b ) {
							return version_compare( $a['version'], $b['version'] ); // ascending.
						}
					);

					$grouped[] = array(
						'name'      => $versions_list[0]['name'] ?? $slug,
						'slug'      => $slug,
						'file_name' => $versions_list[0]['file_name'] ?? $slug,
						'type'      => $versions_list[0]['type'] ?? 'plugin',
						'versions'  => $versions_list,
					);
				}
			}
		}

		return $grouped;
	}
}
