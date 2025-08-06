<?php
/**
 * The page ordering functionality of the plugin.
 *
 * @link       https://nanatomedia.com
 * @since      1.0.6
 *
 * @package    Nanato_Addons
 * @subpackage Nanato_Addons/includes
 */

/**
 * The page ordering functionality of the plugin.
 *
 * Provides drag and drop page ordering functionality for WordPress pages
 * and custom post types that support page-attributes or are hierarchical.
 *
 * @package    Nanato_Addons
 * @subpackage Nanato_Addons/includes
 * @author     Nanato Media <gabrielac@nanatomedia.com>
 */
class Nanato_Addons_Page_Ordering {

	/**
	 * The plugin name.
	 *
	 * @since    1.0.6
	 * @access   private
	 * @var      string    $plugin_name    The plugin name.
	 */
	private $plugin_name;

	/**
	 * The plugin version.
	 *
	 * @since    1.0.6
	 * @access   private
	 * @var      string    $version    The plugin version.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.6
	 * @param    string $plugin_name The name of the plugin.
	 * @param    string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 *
	 * @since    1.0.6
	 */
	private function init_hooks() {
		add_action( 'load-edit.php', array( $this, 'load_edit_screen' ) );
		add_action( 'wp_ajax_nanato_page_ordering', array( $this, 'ajax_page_ordering' ) );
		add_action( 'wp_ajax_nanato_reset_page_ordering', array( $this, 'ajax_reset_page_ordering' ) );
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );

		// Custom edit page actions for hierarchical post types
		add_action( 'post_action_nanato-move-under-grandparent', array( $this, 'handle_move_under_grandparent' ) );
		add_action( 'post_action_nanato-move-under-sibling', array( $this, 'handle_move_under_sibling' ) );
	}

	/**
	 * Load edit screen functionality.
	 *
	 * @since    1.0.6
	 */
	public function load_edit_screen() {
		$screen = get_current_screen();
		if ( ! $screen ) {
			return;
		}

		$post_type = $screen->post_type;

		// Check if post type is sortable
		if ( ! $this->is_post_type_sortable( $post_type ) ) {
			return;
		}

		// Check user capabilities
		if ( ! $this->check_edit_others_caps( $post_type ) ) {
			return;
		}

		// Add hooks for this screen
		add_filter( 'views_' . $screen->id, array( $this, 'sort_by_order_link' ) );
		add_action( 'pre_get_posts', array( $this, 'filter_query' ) );
		add_action( 'wp', array( $this, 'wp_action' ) );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_filter( 'page_row_actions', array( $this, 'page_row_actions' ), 10, 2 );
		add_filter( 'post_row_actions', array( $this, 'page_row_actions' ), 10, 2 );
	}

	/**
	 * Filter query to enable pagination for ordering.
	 *
	 * @since    1.0.6
	 * @param    WP_Query $query The WP_Query instance.
	 */
	public function filter_query( $query ) {
		if ( ! $query->is_main_query() ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$is_page_ordering = isset( $_GET['id'] ) ? 'nanato-page-ordering' === $_GET['id'] : false;

		if ( ! $is_page_ordering ) {
			return;
		}

		$query->set( 'posts_per_page', -1 );
	}

	/**
	 * Initialize ordering scripts when sorting by menu order.
	 *
	 * @since    1.0.6
	 */
	public function wp_action() {
		$orderby = get_query_var( 'orderby' );
		$screen  = get_current_screen();
		
		if ( ! $screen ) {
			return;
		}

		$post_type = $screen->post_type ?? 'post';

		// Check if we're sorting by menu_order
		if ( ( is_string( $orderby ) && 0 === strpos( $orderby, 'menu_order' ) ) || 
			( isset( $orderby['menu_order'] ) && 'ASC' === $orderby['menu_order'] ) ) {

			// Enqueue scripts and styles
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 
				'nanato-page-ordering', 
				plugin_dir_url( __DIR__ ) . 'admin/js/nanato-page-ordering.js', 
				array( 'jquery', 'jquery-ui-sortable' ), 
				$this->version, 
				true 
			);

			wp_localize_script(
				'nanato-page-ordering',
				'nanato_page_ordering_data',
				array(
					'ajaxurl'           => admin_url( 'admin-ajax.php' ),
					'_wpnonce'          => wp_create_nonce( 'nanato-page-ordering-nonce' ),
					'reset_confirm_msg' => sprintf( 
						/* translators: %s: post type name */
						esc_html__( 'Are you sure you want to reset the ordering of the "%s" post type?', 'nanato-addons' ), 
						$post_type 
					),
				)
			);

			wp_enqueue_style( 
				'nanato-page-ordering', 
				plugin_dir_url( __DIR__ ) . 'admin/css/nanato-page-ordering.css', 
				array(), 
				$this->version 
			);
		}
	}

	/**
	 * Add help tab and admin head content.
	 *
	 * @since    1.0.6
	 */
	public function admin_head() {
		$screen = get_current_screen();
		if ( ! $screen ) {
			return;
		}

		$post_type = $screen->post_type ?? 'post';

		$screen->add_help_tab(
			array(
				'id'      => 'nanato_page_ordering_help_tab',
				'title'   => esc_html__( 'Nanato Page Ordering', 'nanato-addons' ),
				'content' => sprintf(
					'<p>%s</p><a href="#" id="nanato-page-ordering-reset" data-posttype="%s">%s</a>',
					esc_html__( 'To reposition an item, simply drag and drop the row by clicking and holding it anywhere (outside of the links and form controls) and moving it to its new position.', 'nanato-addons' ),
					esc_attr( $post_type ),
					/* translators: %s: post type name */
					sprintf( esc_html__( 'Reset %s order', 'nanato-addons' ), $post_type )
				),
			)
		);
	}

	/**
	 * Add sort by order link to views.
	 *
	 * @since    1.0.6
	 * @param    array $views Array of view links.
	 * @return   array Modified array of view links.
	 */
	public function sort_by_order_link( $views ) {
		$class        = ( get_query_var( 'orderby' ) === 'menu_order title' ) ? 'current' : '';
		$query_string = remove_query_arg( array( 'orderby', 'order' ) );
		
		if ( ! is_post_type_hierarchical( get_post_type() ) ) {
			$query_string = add_query_arg( 'orderby', 'menu_order title', $query_string );
			$query_string = add_query_arg( 'order', 'asc', $query_string );
			$query_string = add_query_arg( 'id', 'nanato-page-ordering', $query_string );
		}

		$views['byorder'] = sprintf( 
			'<a href="%s" class="%s">%s</a>', 
			esc_url( $query_string ), 
			$class, 
			__( 'Sort by Order', 'nanato-addons' ) 
		);

		return $views;
	}

	/**
	 * Add row actions for hierarchical post types.
	 *
	 * @since    1.0.6
	 * @param    array   $actions Array of row action links.
	 * @param    WP_Post $post    The post object.
	 * @return   array Modified array of row action links.
	 */
	public function page_row_actions( $actions, $post ) {
		$post = get_post( $post );
		if ( ! $post ) {
			return $actions;
		}

		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return $actions;
		}

		// Only add row actions for hierarchical post types
		if ( ! is_post_type_hierarchical( $post->post_type ) ) {
			return $actions;
		}

		list( 'top_level_pages' => $top_level_pages, 'children_pages' => $children_pages ) = $this->get_walked_pages( $post->post_type );

		$edit_link = get_edit_post_link( $post->ID, 'raw' );
		
		$move_under_grandparent_link = add_query_arg(
			array(
				'action'      => 'nanato-move-under-grandparent',
				'nanato_nonce' => wp_create_nonce( "nanato-page-ordering-nonce-move-{$post->ID}" ),
				'post_type'   => $post->post_type,
			),
			$edit_link
		);

		$move_under_sibling_link = add_query_arg(
			array(
				'action'      => 'nanato-move-under-sibling',
				'nanato_nonce' => wp_create_nonce( "nanato-page-ordering-nonce-move-{$post->ID}" ),
				'post_type'   => $post->post_type,
			),
			$edit_link
		);

		$parent_id = $post->post_parent;
		if ( $parent_id ) {
			$actions['nanato-move-under-grandparent'] = sprintf(
				'<a href="%s">%s</a>',
				esc_url( $move_under_grandparent_link ),
				sprintf(
					/* translators: %s: parent page/post title */
					__( 'Move out from under %s', 'nanato-addons' ),
					get_the_title( $parent_id )
				)
			);
		}

		// Get relevant siblings
		if ( 0 === $post->post_parent ) {
			$siblings = $top_level_pages;
		} else {
			$siblings = $children_pages[ $post->post_parent ] ?? array();
		}

		// Find previous sibling
		$sibling = 0;
		$filtered_siblings = wp_list_filter( $siblings, array( 'ID' => $post->ID ) );
		if ( ! empty( $filtered_siblings ) ) {
			$key = array_key_first( $filtered_siblings );
			if ( $key > 0 ) {
				$previous_page = $siblings[ $key - 1 ];
				$sibling       = $previous_page->ID;
			}
		}

		if ( $sibling ) {
			$actions['nanato-move-under-sibling'] = sprintf(
				'<a href="%s">%s</a>',
				esc_url( $move_under_sibling_link ),
				sprintf(
					/* translators: %s: sibling page/post title */
					__( 'Move under %s', 'nanato-addons' ),
					get_the_title( $sibling )
				)
			);
		}

		return $actions;
	}

	/**
	 * Handle AJAX page ordering.
	 *
	 * @since    1.0.6
	 */
	public function ajax_page_ordering() {
		// Verify nonce
		if ( ! wp_verify_nonce( $_POST['_wpnonce'] ?? '', 'nanato-page-ordering-nonce' ) ) {
			wp_die( -1 );
		}

		// Check required parameters
		if ( empty( $_POST['id'] ) || ( ! isset( $_POST['previd'] ) && ! isset( $_POST['nextid'] ) ) ) {
			wp_die( -1 );
		}

		$post_id  = (int) $_POST['id'];
		$previd   = empty( $_POST['previd'] ) ? false : (int) $_POST['previd'];
		$nextid   = empty( $_POST['nextid'] ) ? false : (int) $_POST['nextid'];
		$start    = empty( $_POST['start'] ) ? 1 : (int) $_POST['start'];
		$excluded = empty( $_POST['excluded'] ) ? array( $post_id ) : array_filter( (array) json_decode( $_POST['excluded'] ), 'intval' );

		// Validate post
		$post = get_post( $post_id );
		if ( ! $post ) {
			wp_die( -1 );
		}

		// Check user capabilities
		if ( ! $this->check_edit_others_caps( $post->post_type ) ) {
			wp_die( -1 );
		}

		$result = $this->page_ordering( $post_id, $previd, $nextid, $start, $excluded );

		if ( is_wp_error( $result ) ) {
			wp_die( -1 );
		}

		wp_die( wp_json_encode( $result ) );
	}

	/**
	 * Handle AJAX reset page ordering.
	 *
	 * @since    1.0.6
	 */
	public function ajax_reset_page_ordering() {
		global $wpdb;

		// Verify nonce
		if ( ! wp_verify_nonce( $_POST['_wpnonce'] ?? '', 'nanato-page-ordering-nonce' ) ) {
			wp_die( -1 );
		}

		$post_type = isset( $_POST['post_type'] ) ? sanitize_text_field( wp_unslash( $_POST['post_type'] ) ) : '';

		if ( empty( $post_type ) ) {
			wp_die( -1 );
		}

		// Check user capabilities
		if ( ! $this->check_edit_others_caps( $post_type ) ) {
			wp_die( -1 );
		}

		// Reset all menu_order values to 0 for this post type
		$wpdb->update( 
			$wpdb->posts, 
			array( 'menu_order' => 0 ), 
			array( 'post_type' => $post_type ), 
			array( '%d' ), 
			array( '%s' ) 
		);

		wp_die( 0 );
	}

	/**
	 * Handle moving post under grandparent.
	 *
	 * @since    1.0.6
	 * @param    int $post_id The post ID.
	 */
	public function handle_move_under_grandparent( $post_id ) {
		$post = get_post( $post_id );
		if ( ! $post ) {
			$this->redirect_to_referer();
		}

		check_admin_referer( "nanato-page-ordering-nonce-move-{$post->ID}", 'nanato_nonce' );

		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			wp_die( esc_html__( 'You are not allowed to edit this item.', 'nanato-addons' ) );
		}

		if ( 0 === $post->post_parent ) {
			// Already top level
			$this->redirect_to_referer();
		}

		$ancestors = get_post_ancestors( $post );

		// Set new parent - grandparent or top level if only one ancestor
		$parent_id = ( count( $ancestors ) === 1 ) ? 0 : $ancestors[1];

		wp_update_post(
			array(
				'ID'          => $post->ID,
				'post_parent' => $parent_id,
			)
		);

		$this->redirect_to_referer();
	}

	/**
	 * Handle moving post under sibling.
	 *
	 * @since    1.0.6
	 * @param    int $post_id The post ID.
	 */
	public function handle_move_under_sibling( $post_id ) {
		$post = get_post( $post_id );
		if ( ! $post ) {
			$this->redirect_to_referer();
		}

		check_admin_referer( "nanato-page-ordering-nonce-move-{$post->ID}", 'nanato_nonce' );

		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			wp_die( esc_html__( 'You are not allowed to edit this item.', 'nanato-addons' ) );
		}

		list( 'top_level_pages' => $top_level_pages, 'children_pages' => $children_pages ) = $this->get_walked_pages( $post->post_type );

		// Get relevant siblings
		if ( 0 === $post->post_parent ) {
			$siblings = $top_level_pages;
		} else {
			$siblings = $children_pages[ $post->post_parent ];
		}

		// Find previous sibling
		$filtered_siblings = wp_list_filter( $siblings, array( 'ID' => $post->ID ) );
		if ( empty( $filtered_siblings ) ) {
			$this->redirect_to_referer();
		}

		$key = array_key_first( $filtered_siblings );
		if ( 0 === $key ) {
			// First page, nothing to do
			$this->redirect_to_referer();
		}

		$previous_page    = $siblings[ $key - 1 ];
		$previous_page_id = $previous_page->ID;

		wp_update_post(
			array(
				'ID'          => $post->ID,
				'post_parent' => $previous_page_id,
			)
		);

		$this->redirect_to_referer();
	}

	/**
	 * Core page ordering function.
	 *
	 * @since    1.0.6
	 * @param    int   $post_id  The post ID.
	 * @param    int   $previd   The previous post ID.
	 * @param    int   $nextid   The next post ID.
	 * @param    int   $start    The start index.
	 * @param    array $excluded Array of excluded post IDs.
	 * @return   object|WP_Error|string The result object or 'children' string.
	 */
	public function page_ordering( $post_id, $previd, $nextid, $start, $excluded ) {
		// Get post
		$post = get_post( $post_id );
		if ( ! $post ) {
			return new WP_Error( 'invalid', __( 'Invalid post ID.', 'nanato-addons' ) );
		}

		// Disable error reporting for badly written plugins
		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
			error_reporting( 0 ); // phpcs:ignore
		}

		$previd   = empty( $previd ) ? false : (int) $previd;
		$nextid   = empty( $nextid ) ? false : (int) $nextid;
		$start    = empty( $start ) ? 1 : (int) $start;
		$excluded = empty( $excluded ) ? array( $post_id ) : array_filter( (array) $excluded, 'intval' );

		$new_pos     = array();
		$return_data = new stdClass();

		do_action( 'nanato_page_ordering_pre_order_posts', $post, $start );

		// Determine parent ID
		$parent_id        = $post->post_parent;
		$next_post_parent = $nextid ? wp_get_post_parent_id( $nextid ) : false;

		if ( $previd === $next_post_parent ) {
			$parent_id = $next_post_parent;
		} elseif ( $next_post_parent !== $parent_id ) {
			$prev_post_parent = $previd ? wp_get_post_parent_id( $previd ) : false;
			if ( $prev_post_parent !== $parent_id ) {
				$parent_id = ( false !== $prev_post_parent ) ? $prev_post_parent : $next_post_parent;
			}
		}

		if ( $next_post_parent !== $parent_id ) {
			$nextid = false;
		}

		// Get sorting limit
		$page_ordering_options = get_option( 'nanato_addons_page_ordering_options', array() );
		$max_sortable_posts = isset( $page_ordering_options['batch_size'] ) ? (int) $page_ordering_options['batch_size'] : 50;
		$max_sortable_posts = (int) apply_filters( 'nanato_page_ordering_limit', $max_sortable_posts );
		if ( $max_sortable_posts < 5 ) {
			$max_sortable_posts = 50;
		}

		// Get post stati
		$post_stati = get_post_stati( array( 'show_in_admin_all_list' => true ) );

		// Query siblings
		$siblings_query = array(
			'depth'                  => 1,
			'posts_per_page'         => $max_sortable_posts,
			'post_type'              => $post->post_type,
			'post_status'            => $post_stati,
			'post_parent'            => $parent_id,
			'post__not_in'           => $excluded,
			'orderby'                => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'suppress_filters'       => true,
			'ignore_sticky_posts'    => true,
		);

		$siblings = new WP_Query( $siblings_query );

		// Disable post revisions for menu order changes
		remove_action( 'post_updated', 'wp_save_post_revision' );

		foreach ( $siblings->posts as $sibling ) {
			if ( $sibling->ID === $post->ID ) {
				continue;
			}

			// Position the moved post
			if ( $nextid === $sibling->ID ) {
				wp_update_post(
					array(
						'ID'          => $post->ID,
						'menu_order'  => $start,
						'post_parent' => $parent_id,
					)
				);

				$ancestors            = get_post_ancestors( $post->ID );
				$new_pos[ $post->ID ] = array(
					'menu_order'  => $start,
					'post_parent' => $parent_id,
					'depth'       => count( $ancestors ),
				);
				$start++;
			}

			// Update sibling menu order
			if ( $sibling->menu_order !== $start ) {
				wp_update_post(
					array(
						'ID'         => $sibling->ID,
						'menu_order' => $start,
					)
				);
			}
			$new_pos[ $sibling->ID ] = $start;
			$start++;

			// Position at end if no nextid
			if ( ! $nextid && $previd === $sibling->ID ) {
				wp_update_post(
					array(
						'ID'          => $post->ID,
						'menu_order'  => $start,
						'post_parent' => $parent_id,
					)
				);

				$ancestors            = get_post_ancestors( $post->ID );
				$new_pos[ $post->ID ] = array(
					'menu_order'  => $start,
					'post_parent' => $parent_id,
					'depth'       => count( $ancestors ),
				);
				$start++;
			}
		}

		// Handle pagination
		if ( ! isset( $return_data->next ) && $siblings->max_num_pages > 1 ) {
			$return_data->next = array(
				'id'       => $post->ID,
				'previd'   => $previd,
				'nextid'   => $nextid,
				'start'    => $start,
				'excluded' => array_merge( array_keys( $new_pos ), $excluded ),
			);
		} else {
			$return_data->next = false;
		}

		do_action( 'nanato_page_ordering_ordered_posts', $post, $new_pos );

		// Check for children
		if ( ! $return_data->next ) {
			$children = new WP_Query(
				array(
					'posts_per_page'         => 1,
					'post_type'              => $post->post_type,
					'post_status'            => $post_stati,
					'post_parent'            => $post->ID,
					'fields'                 => 'ids',
					'update_post_term_cache' => false,
					'update_post_meta_cache' => false,
					'ignore_sticky'          => true,
					'no_found_rows'          => true,
				)
			);

			if ( $children->have_posts() ) {
				return 'children';
			}
		}

		$return_data->new_pos = $new_pos;

		return $return_data;
	}

	/**
	 * Register REST API routes.
	 *
	 * @since    1.0.6
	 */
	public function register_rest_routes() {
		register_rest_route(
			'nanato-addons/v1',
			'page_ordering',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_page_ordering' ),
				'permission_callback' => array( $this, 'rest_page_ordering_permissions_check' ),
				'args'                => array(
					'id'      => array(
						'description' => __( 'ID of item to sort', 'nanato-addons' ),
						'required'    => true,
						'type'        => 'integer',
						'minimum'     => 1,
					),
					'previd'  => array(
						'description' => __( 'ID of previous item', 'nanato-addons' ),
						'required'    => true,
						'type'        => array( 'boolean', 'integer' ),
					),
					'nextid'  => array(
						'description' => __( 'ID of next item', 'nanato-addons' ),
						'required'    => true,
						'type'        => array( 'boolean', 'integer' ),
					),
					'start'   => array(
						'default'     => 1,
						'description' => __( 'Start index', 'nanato-addons' ),
						'required'    => false,
						'type'        => 'integer',
					),
					'exclude' => array(
						'default'     => array(),
						'description' => __( 'Array of IDs to exclude', 'nanato-addons' ),
						'required'    => false,
						'type'        => 'array',
						'items'       => array(
							'type' => 'integer',
						),
					),
				),
			)
		);
	}

	/**
	 * Handle REST API page ordering.
	 *
	 * @since    1.0.6
	 * @param    WP_REST_Request $request The REST request object.
	 * @return   WP_REST_Response|WP_Error The response object.
	 */
	public function rest_page_ordering( WP_REST_Request $request ) {
		$post_id  = (int) $request->get_param( 'id' );
		$previd   = empty( $request->get_param( 'previd' ) ) ? false : (int) $request->get_param( 'previd' );
		$nextid   = empty( $request->get_param( 'nextid' ) ) ? false : (int) $request->get_param( 'nextid' );
		$start    = (int) $request->get_param( 'start' );
		$excluded = empty( $request->get_param( 'excluded' ) ) ? array( $post_id ) : array_filter( (array) $request->get_param( 'excluded' ), 'intval' );

		if ( false === $post_id || ( false === $previd && false === $nextid ) ) {
			return new WP_Error( 'invalid', __( 'Missing mandatory parameters.', 'nanato-addons' ) );
		}

		$result = $this->page_ordering( $post_id, $previd, $nextid, $start, $excluded );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return new WP_REST_Response(
			array(
				'status'   => 200,
				'response' => 'success',
				'data'     => $result,
			)
		);
	}

	/**
	 * Check REST API permissions.
	 *
	 * @since    1.0.6
	 * @param    WP_REST_Request $request The REST request object.
	 * @return   bool|WP_Error True if authorized, false or WP_Error otherwise.
	 */
	public function rest_page_ordering_permissions_check( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'id' );

		// Check user can edit post
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return false;
		}

		$post_type     = get_post_type( $post_id );
		$post_type_obj = get_post_type_object( $post_type );

		// Check post type allows REST
		if ( ! $post_type || empty( $post_type_obj ) || empty( $post_type_obj->show_in_rest ) ) {
			return false;
		}

		// Check post type is sortable
		if ( ! $this->is_post_type_sortable( $post_type ) ) {
			return new WP_Error( 'not_enabled', esc_html__( 'This post type is not sortable.', 'nanato-addons' ) );
		}

		return true;
	}

	/**
	 * Check if post type is sortable.
	 *
	 * @since    1.0.6
	 * @param    string $post_type The post type to check.
	 * @return   bool True if sortable, false otherwise.
	 */
	private function is_post_type_sortable( $post_type = 'post' ) {
		$page_ordering_options = get_option( 'nanato_addons_page_ordering_options', array() );
		
		// Check if posts are explicitly enabled
		if ( 'post' === $post_type && ! empty( $page_ordering_options['enable_for_posts'] ) ) {
			// Add page-attributes support to posts if enabled
			add_post_type_support( 'post', 'page-attributes' );
		}
		
		// Check custom post type settings
		if ( isset( $page_ordering_options['post_types'][ $post_type ] ) ) {
			$sortable = ! empty( $page_ordering_options['post_types'][ $post_type ] );
		} else {
			// Default behavior - check for page-attributes or hierarchical
			$sortable = ( post_type_supports( $post_type, 'page-attributes' ) || is_post_type_hierarchical( $post_type ) );
		}

		/**
		 * Filter to modify sortable post types.
		 *
		 * @since 1.0.6
		 *
		 * @param bool   $sortable  Whether the post type is sortable.
		 * @param string $post_type The post type being checked.
		 */
		return apply_filters( 'nanato_page_ordering_is_sortable', $sortable, $post_type );
	}

	/**
	 * Check user edit capabilities for post type.
	 *
	 * @since    1.0.6
	 * @param    string $post_type The post type to check.
	 * @return   bool True if user can edit others' posts, false otherwise.
	 */
	private function check_edit_others_caps( $post_type ) {
		$post_type_object = get_post_type_object( $post_type );
		$edit_others_cap  = empty( $post_type_object ) ? 'edit_others_' . $post_type . 's' : $post_type_object->cap->edit_others_posts;

		/**
		 * Filter to modify edit rights.
		 *
		 * @since 1.0.6
		 *
		 * @param bool   $can_edit   Whether user can edit.
		 * @param string $post_type  The post type being checked.
		 */
		return apply_filters( 'nanato_page_ordering_edit_rights', current_user_can( $edit_others_cap ), $post_type );
	}

	/**
	 * Get walked pages for hierarchical post types.
	 *
	 * @since    1.0.6
	 * @param    string $post_type The post type.
	 * @return   array Array of top level and children pages.
	 */
	private function get_walked_pages( $post_type = 'page' ) {
		$pages = get_pages(
			array(
				'sort_column' => 'menu_order title',
				'post_type'   => $post_type,
			)
		);

		$top_level_pages = array();
		$children_pages  = array();

		foreach ( $pages as $page ) {
			if ( 0 === $page->post_parent ) {
				$top_level_pages[] = $page;
			} else {
				if ( ! isset( $children_pages[ $page->post_parent ] ) ) {
					$children_pages[ $page->post_parent ] = array();
				}
				$children_pages[ $page->post_parent ][] = $page;
			}
		}

		return array(
			'top_level_pages' => $top_level_pages,
			'children_pages'  => $children_pages,
		);
	}

	/**
	 * Redirect to referer.
	 *
	 * @since    1.0.6
	 */
	private function redirect_to_referer() {
		global $post_type;

		$send_back = wp_get_referer();
		if ( ! $send_back ||
			str_contains( $send_back, 'post.php' ) ||
			str_contains( $send_back, 'post-new.php' ) ) {
			if ( 'attachment' === $post_type ) {
				$send_back = admin_url( 'upload.php' );
			} else {
				$send_back = admin_url( 'edit.php' );
				if ( ! empty( $post_type ) ) {
					$send_back = add_query_arg( 'post_type', $post_type, $send_back );
				}
			}
		} else {
			$send_back = remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'ids' ), $send_back );
		}

		wp_safe_redirect( $send_back );
		exit;
	}
}
