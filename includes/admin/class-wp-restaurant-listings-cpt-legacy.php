<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Handles legacy actions and filters specific to the custom post type for Restaurant Listings.
 *
 * @package RestaurantListings
 * @since 1.0.0
 */
class WP_Restaurant_Listings_CPT_Legacy extends WP_Restaurant_Listings_CPT {
	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since 1.0.0
	 */
	private static $_instance = null;

	/**
	 * Allows for accessing single instance of class. Class should only be constructed once per call.
	 *
	 * @since 1.0.0
	 * @static
	 * @return self Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'admin_footer-edit.php', array( $this, 'add_bulk_actions_legacy' ) );
		add_action( 'load-edit.php', array( $this, 'do_bulk_actions_legacy' ) );
		remove_action( 'bulk_actions-edit-restaurant_listings', array( $this, 'add_bulk_actions' ) );
	}

	/**
	 * Adds bulk actions to drop downs on Restaurant Listings admin page.
	 */
	public function add_bulk_actions_legacy() {
		global $post_type, $wp_post_types;

		if ( $post_type === 'restaurant_listings' ) {
			?>
			<script type="text/javascript">
				jQuery(document).ready(function() {
				    <?php
					foreach( $this->get_bulk_actions() as $key => $bulk_action ) {
						if ( isset( $bulk_action[ 'label' ] ) ) {
							echo 'jQuery(\'<option>\').val(\'' . $key . '\').text(\'' . addslashes( sprintf( $bulk_action[ 'label' ], $wp_post_types[ 'restaurant_listings' ]->labels->name ) ) . '\').appendTo("select[name=\'action\']");';
							echo 'jQuery(\'<option>\').val(\'' . $key . '\').text(\'' . addslashes( sprintf( $bulk_action[ 'label' ], $wp_post_types[ 'restaurant_listings' ]->labels->name ) ) . '\').appendTo("select[name=\'action2\']");';
						}
					}
					?>
				});
			</script>
			<?php
		}
	}

	/**
	 * Performs bulk actions on Restaurant Listings admin page.
	 */
	public function do_bulk_actions_legacy() {
		$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
		$action        = $wp_list_table->current_action();
		$actions_handled = $this->get_bulk_actions();
		if ( isset ( $actions_handled[ $action ] ) && isset ( $actions_handled[ $action ]['handler'] ) ) {
			check_admin_referer( 'bulk-posts' );
			$post_ids     = array_map( 'absint', array_filter( (array) $_GET['post'] ) );
			if ( ! empty( $post_ids ) ) {
				$this->do_bulk_actions( admin_url( 'edit.php?post_type=restaurant_listings' ), $action, $post_ids );
			}
		}
	}
}
