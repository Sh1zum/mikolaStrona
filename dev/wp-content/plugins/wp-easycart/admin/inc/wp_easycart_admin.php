<?php

if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'wp_easycart_admin' ) ) :

final class wp_easycart_admin{
	
	protected static $_instance = null;
	
	private $wpdb;
	
	public $available_url;
	public $preloader;
	
	public $date_diff;
	
	public $month_sales_total;
	public $month_name;
	public $month_percentage_change;
	public $month_percentage_goal;
	public $month_goal_total;
	
	public $daily_sales;
	public $weekly_sales;
	public $monthly_sales;
	public $yearly_sales;
	
	public $daily_items_sold;
	public $weekly_items_sold;
	public $monthly_items_sold;
	public $yearly_items_sold;
	
	public $daily_abandonment;
	public $weekly_abandonment;
	public $monthly_abandonment;
	public $yearly_abandonment;
	
	public $new_orders;
	public $new_unviewed_orders;
	public $pending_reviews;
	public $cart_users;
	
	public $settings;
	public $shipping_zones;
	public $shipping_zones_items;
	public $countries;
	public $states;
	
	public $store_page;
	public $cart_page;
	public $account_page;
	public $permalink_divider;
	
	public static function instance( ) {
		
		if( is_null( self::$_instance ) ) {
			self::$_instance = new self(  );
		}
		return self::$_instance;
	
	}
	
	public function __construct( ){ 
	
		if( !defined( 'WP_EASYCART_ADMIN_PLUGIN_DIR' ) )
			define( 'WP_EASYCART_ADMIN_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			
		if( ! defined( 'WP_EASYCART_ADMIN_PLUGIN_URL' ) )
			define( 'WP_EASYCART_ADMIN_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

		if( ! defined( 'WP_EASYCART_ADMIN_PLUGIN_FILE' ) )
			define( 'WP_EASYCART_ADMIN_PLUGIN_FILE', __FILE__ );
	
		if( !defined( 'WP_EASYCART_ADMIN_DB_VERSION' ) )
			define( 'WP_EASYCART_ADMIN_DB_VERSION', 0.1 );
			
		// Keep reference to wpdb
		global $wpdb;
		$this->wpdb =& $wpdb;
		
		if( is_callable( 'socket_create' ) && is_callable( 'socket_connect' ) && is_callable( 'socket_close' ) ){
			$socket = socket_create( AF_INET, SOCK_STREAM, SOL_TCP );
			$connection = @socket_connect( $socket, "connect.wpeasycart.com", 443 );
			$this->available_url = ( $connection ) ? "https://connect.wpeasycart.com" : "https://support.wpeasycart.com";
			@socket_close( $socket );
		}else{
			$this->available_url = "https://connect.wpeasycart.com";
		}
		
		$this->preloader = new wp_easycart_admin_preloader( );
		$this->helpsystem = new wp_easycart_admin_online_docs( );
		
		$this->set_date_diff( );
		
		if( isset( $_GET['page'] ) && (
			$_GET['page'] == 'wp-easycart-dashboard' || 
			$_GET['page'] == 'wp-easycart-license-status' ||
			$_GET['page'] == 'wp-easycart-products' || 
			$_GET['page'] == 'wp-easycart-orders' || 
			$_GET['page'] == 'wp-easycart-users' || 
			$_GET['page'] == 'wp-easycart-rates' || 
			$_GET['page'] == 'wp-easycart-settings' || 
			$_GET['page'] == 'wp-easycart-status' || 
			$_GET['page'] == 'wp-easycart-registration'
		) ){
			// Setup Basic Variables for Admin Design
			$this->month_sales_total = $this->get_month_sales_total( );
			$this->month_name = date( 'F' );
			$this->month_percentage_change = $this->get_month_percentage_change( );
			$this->month_goal_total =  get_option( 'ec_option_admin_sales_goal');
			$this->month_percentage_goal = ( $this->month_sales_total / $this->month_goal_total ) * 100;
			
			if( $_GET['page'] == 'wp-easycart-dashboard' ){
				$this->new_orders = $this->get_total_new_orders( );
				$this->pending_reviews = $this->get_total_new_reviews( );
				$this->cart_users = $this->get_total_cart_users( );
			}
		}
		
		$this->new_unviewed_orders = $this->get_total_new_unviewed_orders( );

		// EasyCart Admin Actions
		add_action( 'wp_easycart_admin_messages', array( $this, 'load_upsell_image' ) );
		add_action( 'wp_easycart_admin_upsell_popup', array( $this, 'load_upsell_popup' ) );
		add_action( 'wp_easycart_admin_mobile_navigation', array( $this, 'load_mobile_navigation' ), 1, 0 );
		add_action( 'wp_easycart_admin_left_navigation', array( $this, 'load_left_navigation' ), 1, 0 );
		add_action( 'wp_easycart_admin_head_navigation', array( $this, 'load_head_navigation' ), 1, 0 );
		add_action( 'wp_easycart_admin_messages', array( $this, 'print_admin_message' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'add_ec_nag_widget' ) );

		// Hook Actions
        add_action( 'admin_init', array( $this, 'load_translation' ) );
		add_action( 'admin_init', array( $this, 'delete_gateway_log' ) );
		add_action( 'admin_init', array( $this, 'complete_init' ) );
		add_action( 'admin_init', array( $this, 'setup_pro_hooks' ) );
		add_action( 'admin_init', array( $this, 'process_actions' ) );
		add_action( 'admin_init', array( $this, 'change_uploads_dir' ), 999 );
		add_action( 'admin_menu', array( $this, 'setup_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'load_block_editor_assets' ) );
		add_action( 'admin_notices', array( $this, 'wp_easycart_pro_check' ) );
		add_action( 'admin_notices', array( $this, 'elementor_check' ) );
		add_action( 'admin_notices', array( $this, 'square_check' ) );
		add_action( 'admin_notices', array( $this, 'database_check' ) );
		add_action( 'add_meta_boxes', array( $this, 'page_lock_meta' ), 10, 2 );
		add_action( 'save_post', array( $this, 'save_page_lock_meta' ) );
		
		// WordPress Filters
		add_filter( 'admin_title', array( $this, 'set_title' ), 10, 2 );
	}
	
	public function save_page_lock_meta( $post_id ){
		if( current_user_can( 'manage_options' ) ){
			if( array_key_exists( 'wpeasycart_restrict_product_id', $_POST ) ){
				update_post_meta( $post_id, 'wpeasycart_restrict_product_id', $_POST['wpeasycart_restrict_product_id'] );
			}
			
			if( array_key_exists( 'wpeasycart_restrict_user_id', $_POST ) ){
				update_post_meta( $post_id, 'wpeasycart_restrict_user_id', $_POST['wpeasycart_restrict_user_id'] );
			}
			
			if( array_key_exists( 'wpeasycart_restrict_role_id', $_POST ) ){
				update_post_meta( $post_id, 'wpeasycart_restrict_role_id', $_POST['wpeasycart_restrict_role_id'] );
			}
			
			if( array_key_exists( 'wpeasycart_restrict_redirect_url', $_POST ) ){
				update_post_meta( $post_id, 'wpeasycart_restrict_redirect_url', $_POST['wpeasycart_restrict_redirect_url'] );
			}
			
			if( array_key_exists( 'wpeasycart_restrict_redirect_url_auth', $_POST ) ){
				update_post_meta( $post_id, 'wpeasycart_restrict_redirect_url_auth', $_POST['wpeasycart_restrict_redirect_url_auth'] );
			}
			
			if( array_key_exists( 'wpeasycart_restrict_redirect_url_not_auth', $_POST ) ){
				update_post_meta( $post_id, 'wpeasycart_restrict_redirect_url_not_auth', $_POST['wpeasycart_restrict_redirect_url_not_auth'] );
			}
		}
	}
	
	public function page_lock_meta( $post_type, $post ){
		if( current_user_can( 'manage_options' ) && ( $post_type == 'page' || $post_type == 'post' || $post_type == 'ec_store' ) ){
			add_meta_box( 
				'wp-easycart-product-lock',
				__( 'WP EasyCart Limit Access', 'wp-easycart' ),
				array( $this, 'load_page_lock_meta_box' ),
				array( 'page', 'post', 'ec_store' ),
				'side',
				'default'
			);
		}
	}
	
	public function load_page_lock_meta_box( $post ){
		global $wpdb;
		$products = $wpdb->get_results( "SELECT product_id, title FROM ec_product ORDER BY title ASC LIMIT 500" );
		$users = $wpdb->get_results( "SELECT user_id, first_name, last_name FROM ec_user ORDER BY last_name ASC, first_name ASC LIMIT 500" );
		$user_roles = $wpdb->get_results( "SELECT role_label FROM ec_role ORDER BY role_label ASC" );
		
		$selected_product = get_post_meta( $post->ID, 'wpeasycart_restrict_product_id', true );
		$selected_user = get_post_meta( $post->ID, 'wpeasycart_restrict_user_id', true );
		$selected_role = get_post_meta( $post->ID, 'wpeasycart_restrict_role_id', true );
		$selected_redirect = get_post_meta( $post->ID, 'wpeasycart_restrict_redirect_url', true );
		$selected_redirect_auth = get_post_meta( $post->ID, 'wpeasycart_restrict_redirect_url_auth', true );
		$selected_redirect_not_auth = get_post_meta( $post->ID, 'wpeasycart_restrict_redirect_url_not_auth', true );
		
		if( count( $products ) >= 500 ){
			echo '<label for="wpeasycart_restrict_product_id">' . __( 'Option 1: Restrict by Product', 'wp-easycart' ) . '</label>';
			echo '<input type="text" name="wpeasycart_restrict_product_id" id="wpeasycart_restrict_product_id" class="postbox" value="' . $selected_product . '" placeholder="' . __( 'Enter Product ID', 'wp-easycart' ) . '">';
		}else{
			echo '<label for="wpeasycart_restrict_product_id">' . __( 'Option 1: Restrict by Product', 'wp-easycart' ) . '</label>
			<select name="wpeasycart_restrict_product_id[]" id="wpeasycart_restrict_product_id" class="postbox" multiple>
				<option value="">' . __( 'No Restriction', 'wp-easycart' ) . '</option>';
				
			foreach( $products as $product ){
				echo '<option value="' . $product->product_id . '"' . ( ( ( is_array( $selected_product ) && in_array( $product->product_id, $selected_product ) ) || ( !is_array( $selected_product ) && $product->product_id == $selected_product ) ) ? ' selected="selected"' : '' ). '>' . $product->title . '</option>';
			}
			echo '</select>';
		}
		
		if( count( $users ) >= 500 ){
			echo '<label for="wpeasycart_restrict_user_id">' . __( 'Option 2: Restrict by User', 'wp-easycart' ) . '</label>';
			echo '<input type="text" name="wpeasycart_restrict_user_id" id="wpeasycart_restrict_user_id" class="postbox" value="' . $selected_user . '" placeholder="' . __( 'Enter User ID', 'wp-easycart' ) . '">';
		}else{
			echo '<label for="wpeasycart_restrict_user_id">' . __( 'Option 2: Restrict by User', 'wp-easycart' ) . '</label>
			<select name="wpeasycart_restrict_user_id[]" id="wpeasycart_restrict_user_id" class="postbox" multiple>
				<option value="">' . __( 'No Restriction', 'wp-easycart' ) . '</option>';
				
			foreach( $users as $user ){
				echo '<option value="' . $user->user_id . '"' . ( ( ( is_array( $selected_user ) && in_array( $user->user_id, $selected_user ) ) || ( !is_array( $selected_user ) && $user->user_id == $selected_user ) ) ? ' selected="selected"' : '' ). '>' . $user->first_name . ' ' . $user->last_name . '</option>';
			}
			echo '</select>';
		}
		
		echo '<label for="wpeasycart_restrict_role_id">' . __( 'Option 3: Restrict by User Role', 'wp-easycart' ) . '</label>';
		echo '<select name="wpeasycart_restrict_role_id[]" id="wpeasycart_restrict_role_id" class="postbox" multiple>';
		echo '<option value="">' . __( 'No Restriction', 'wp-easycart' ) . '</option>';
		foreach( $user_roles as $role ){
			echo '<option value="' . $role->role_label . '"' . ( ( ( is_array( $selected_role ) && in_array( $role->role_label, $selected_role ) ) || ( !is_array( $selected_role ) && $role->role_label == $selected_role ) ) ? ' selected="selected"' : '' ). '>' . $role->role_label . '</option>';
		}
		echo '</select>';
		
		echo '<label for="wpeasycart_restrict_redirect_url">' . __( 'Redirect - Not Logged In + Not Authorized', 'wp-easycart' ) . '</label>';
		echo '<input type="text" name="wpeasycart_restrict_redirect_url" id="wpeasycart_restrict_redirect_url" class="postbox" value="' . $selected_redirect . '" placeholder="https://www.site.com">';
		
		echo '<label for="wpeasycart_restrict_redirect_url_auth">' . __( 'Redirect - Logged In + Authorized', 'wp-easycart' ) . '</label>';
		echo '<input type="text" name="wpeasycart_restrict_redirect_url_auth" id="wpeasycart_restrict_redirect_url_auth" class="postbox" value="' . $selected_redirect_auth . '" placeholder="https://www.site.com">';
		
		echo '<label for="wpeasycart_restrict_redirect_url_not_auth">' . __( 'Redirect - Logged In + Not Authorized', 'wp-easycart' ) . '</label>';
		echo '<input type="text" name="wpeasycart_restrict_redirect_url_not_auth" id="wpeasycart_restrict_redirect_url_not_auth" class="postbox" value="' . $selected_redirect_not_auth . '" placeholder="https://www.site.com">';
		
		echo '<p>' . __( 'Note: You must turn off guest checkout or select a download or subscription product from the menu above. Subscription products will check the user has an active subscription. You must create a page to redirect to if a user does not have authorization.', 'wp-easycart' ) . '</p>';
	}

	public function add_ec_nag_widget( ){
		wp_add_dashboard_widget( 'ec_free_dashboard_widget', __( 'WP EasyCart FREE Edition', 'wp-easycart' ), array( $this, 'ec_dashboard_nag_widget' ) );
        global $wp_meta_boxes;
		$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
		$widget_backup = array( 'ec_free_dashboard_widget' => $normal_dashboard['ec_free_dashboard_widget'] );
		unset( $normal_dashboard['ec_free_dashboard_widget'] );
		$sorted_dashboard = array_merge( $widget_backup, $normal_dashboard );
		$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
	}
	
	public function ec_dashboard_nag_widget( $post, $callback_args ){
		echo "<div style='text-align:center;font-size: 1.3em;'>" . __( 'Are you enjoying your FREE Shopping Cart?', 'wp-easycart' ) . '<br>' . __( 'Want to unlock more awesome features?', 'wp-easycart' ) . '<br/><br/>' . __( 'Upgrade to', 'wp-easycart' ) . ' <strong>' . __( 'Professional', 'wp-easycart' ) . '</strong> & <strong>' . __( 'Premium', 'wp-easycart' ) . '</strong> ' . __( 'editions!', 'wp-easycart' ) . '<br/>';
		echo "<a href='https://www.wpeasycart.com/wordpress-shopping-cart-pricing/?upsell=9' target='_blank'><img src='https://www.wpeasycart.com/images/ec_dashboard_nag_image.jpg' style='max-width:100%;margin: 10px;'/></a>";
		echo "<a class='button button-primary' href='https://www.wpeasycart.com/wordpress-shopping-cart-pricing/?upsell=9' target='_blank'>" . __( 'Upgrade Today!', 'wp-easycart' ) . "</a></div>";
    }
    
    public function load_translation( ){
        load_plugin_textdomain( 'wp-easycart', FALSE, WP_EASYCART_ADMIN_PLUGIN_DIR . '/lang/' );
    }
	
	public function delete_gateway_log( ){ 
		if( current_user_can( 'manage_options' ) && isset( $_GET['ec_admin_form_action'])  && $_GET['ec_admin_form_action'] == 'ec_delete_gateway_log' ){
			wp_easycart_admin_miscellaneous( )->delete_gateway_log( );
		}
	}
	
	public function complete_init( ){
		
		// Link Information
		$store_page_id = get_option('ec_option_storepage');
		$cart_page_id = get_option('ec_option_cartpage');
		$account_page_id = get_option('ec_option_accountpage');
		
		if( function_exists( 'icl_object_id' ) ){
			$store_page_id = icl_object_id( $store_page_id, 'page', true, ICL_LANGUAGE_CODE );
			$cart_page_id = icl_object_id( $cart_page_id, 'page', true, ICL_LANGUAGE_CODE );
			$account_page_id = icl_object_id( $account_page_id, 'page', true, ICL_LANGUAGE_CODE );
		}
		
		$this->store_page = get_permalink( $store_page_id );
		$this->cart_page = get_permalink( $cart_page_id );
		$this->account_page = get_permalink( $account_page_id );
		
		if( class_exists( "WordPressHTTPS" ) && isset( $_SERVER['HTTPS'] ) ){
			$https_class = new WordPressHTTPS( );
			$this->store_page = $https_class->makeUrlHttps( $this->store_page );
			$this->cart_page = $https_class->makeUrlHttps( $this->cart_page );
			$this->account_page = $https_class->makeUrlHttps( $this->account_page );
		}
		
		if( substr_count( $this->cart_page, '?' ) )					$this->permalink_divider = "&";
		else														$this->permalink_divider = "?";
		
	}
	
	public function process_actions( ){ 
		if( current_user_can( 'manage_options' ) && (isset( $_POST['ec_admin_form_action'] ) || isset( $_GET['ec_admin_form_action'] )) ){
			$actions = new wp_easycart_admin_actions( );
			$actions->process_action( );
		}
		
		if( current_user_can( 'manage_options' ) && isset( $_GET['ec_trial'] ) && $_GET['ec_trial'] == 'start' ){
			$this->start_pro_trial( );
			wp_redirect( "admin.php?page=wp-easycart-registration" );
		}
		
		if( current_user_can( 'manage_options' ) && isset( $_GET['ec_install'] ) && $_GET['ec_install'] == 'pro' ){
			if( !file_exists( WP_PLUGIN_DIR . '/' . EC_PLUGIN_DIRECTORY . '-pro/wp-easycart-admin-pro.php' ) ){
				$this->install_pro_plugin( 0 );
			}
			if( file_exists( WP_PLUGIN_DIR . '/' . EC_PLUGIN_DIRECTORY . '-pro/wp-easycart-admin-pro.php' ) && !is_plugin_active( 'wp-easycart-pro/wp-easycart-admin-pro.php' ) ){
				activate_plugin( WP_PLUGIN_DIR . '/' . EC_PLUGIN_DIRECTORY . '-pro/wp-easycart-admin-pro.php', NULL, 0, 1 );
			}
			wp_redirect( "admin.php?page=wp-easycart-registration" );
		}
	}
	
	public function change_uploads_dir( ){
		add_filter( 'upload_dir', array( $this, 'custom_download_location' ) );
	}
	
	public function custom_download_location( $upload ){
		if( isset( $_REQUEST['is_wpec_download'] ) && $_REQUEST['is_wpec_download'] == '1' ){
			if( !is_dir( $upload['basedir'] . '/wp-easycart' ) ){
				mkdir( $upload['basedir'] . '/wp-easycart', 0755 );
				$index_file = fopen( $upload['basedir'] . '/wp-easycart/index.html', "w" );
				fclose( $index_file );
			}
			$upload['subdir']  = "/wp-easycart";
			$upload['path']    = $upload['basedir'] . "/wp-easycart";
			$upload['url']     = $upload['baseurl'] . "/wp-easycart";
		}
		return $upload;
	}
	
	/* STATS FUNCTIONS */
	private function set_date_diff( ){
		global $wpdb;
		$now_server = $this->wpdb->get_var( "SELECT NOW( ) AS the_time" );
		$now_timestamp = strtotime( $now_server );
		$now_gmt_timestampt = time( );
		$storage_offset = $now_timestamp - $now_gmt_timestampt;
		$local_offset = get_option('gmt_offset') * 60 * 60;
		$this->date_diff = ( $local_offset - $storage_offset ) / 3600;
	}
	
	private function get_month_sales_total( ){
		if( $this->date_diff < 0 ){
			return $this->wpdb->get_var( "SELECT ( SUM( ec_order.sub_total ) - SUM( ec_order.discount_total ) - SUM( ec_order.refund_total ) ) as total FROM ec_order LEFT JOIN ec_orderstatus ON ec_orderstatus.status_id = ec_order.orderstatus_id WHERE MONTH( DATE_SUB( NOW( ), INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) = MONTH( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) AND YEAR( DATE_SUB( NOW( ), INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) = YEAR( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) AND ec_orderstatus.`is_approved` = 1" );
		}else{
			return $this->wpdb->get_var( "SELECT ( SUM( ec_order.sub_total ) - SUM( ec_order.discount_total ) - SUM( ec_order.refund_total ) ) as total FROM ec_order LEFT JOIN ec_orderstatus ON ec_orderstatus.status_id = ec_order.orderstatus_id WHERE MONTH( DATE_ADD( NOW( ), INTERVAL " . $this->date_diff . " HOUR ) ) = MONTH( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) AND YEAR( DATE_ADD( NOW( ), INTERVAL " . $this->date_diff . " HOUR ) ) = YEAR( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) AND ec_orderstatus.`is_approved` = 1" );
		}
	}
	
	private function get_month_percentage_change( ){
		if( $this->date_diff < 0 ){
			$last_month = $this->wpdb->get_var( "SELECT ( SUM( ec_order.sub_total ) - SUM( ec_order.discount_total ) - SUM( ec_order.refund_total ) ) as total FROM ec_order LEFT JOIN ec_orderstatus ON ec_orderstatus.status_id = ec_order.orderstatus_id WHERE MONTH( DATE_SUB( DATE_SUB( NOW( ), INTERVAL " . ($this->date_diff*-1) . " HOUR ), INTERVAL 30 DAY ) ) = MONTH( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) AND YEAR( DATE_SUB( DATE_SUB( NOW( ), INTERVAL " . ($this->date_diff*-1) . " HOUR ), INTERVAL 30 DAY ) ) = YEAR( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) AND ec_orderstatus.`is_approved` = 1" );
		}else{
			$last_month = $this->wpdb->get_var( "SELECT ( SUM( ec_order.sub_total ) - SUM( ec_order.discount_total ) - SUM( ec_order.refund_total ) ) as total FROM ec_order LEFT JOIN ec_orderstatus ON ec_orderstatus.status_id = ec_order.orderstatus_id WHERE MONTH( DATE_SUB( DATE_ADD( NOW( ), INTERVAL " . $this->date_diff . " HOUR ), INTERVAL 30 DAY ) ) = MONTH( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) AND YEAR( DATE_SUB( DATE_ADD( NOW( ), INTERVAL " . $this->date_diff . " HOUR ), INTERVAL 30 DAY ) ) = YEAR( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) AND ec_orderstatus.`is_approved` = 1" );
		}
		if($last_month == null) $last_month = 0;
		$datestring = 'first day of last month';
		$dt = date_create( $datestring );
		$month = intval( date_format( $dt, 'm' ) );
		$year = intval( date_format( $dt, 'Y' ) );
		$days_last = $this->days_in_month( $month, $year );
		$days_this_month = intval( date( 'j' ) );
		
		if( ( $last_month / $days_last ) * $days_this_month == 0 )
			return 0;
		else
			return ( ( $this->month_sales_total / ( ( $last_month / $days_last ) * $days_this_month ) ) - 1 ) * 100; //compare total over same number of days between months
	}
	
	private function days_in_month( $month, $year ){ 
		return $month == 2 ? ( $year % 4 ? 28 : ( $year % 100 ? 29 : ( $year % 400 ? 28 : 29 ) ) ) : ( ( $month - 1 ) % 7 % 2 ? 30 : 31 ); 
	}
	
	public function get_dashboard_data( $date_type, $chart_type, $product_id ){
		return $this->{"get_".$date_type."_".$chart_type}( $product_id );
	}
	
	private function get_daily_sales( $product_id ){
		$sales = array( );
		$product_where = "";
		if( $product_id ){
			$product_where = $this->wpdb->prepare( "AND ec_orderdetail.product_id = %d ", $product_id );
             if( $this->date_diff < 0 ){
                $sales_data = $this->wpdb->get_results( "SELECT IFNULL( SUM( ec_orderdetail.total_price ), 0 ) as total, 0 AS discount_total, 0 AS refund_total, DATE_FORMAT( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ), '%m/%d/%Y' ) as date, DAY( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) AS order_day, MONTH( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) AS order_month FROM ec_orderdetail LEFT JOIN ec_order ON ec_order.order_id = ec_orderdetail.order_id LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id WHERE DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) > DATE_SUB( DATE_SUB( NOW( ), INTERVAL " . ($this->date_diff*-1) . " HOUR ), INTERVAL 14 DAY ) AND ec_orderstatus.is_approved = 1 " . $product_where . "GROUP BY order_day, order_month ORDER BY ec_order.order_date DESC" );
            }else{
                $sales_data = $this->wpdb->get_results( "SELECT IFNULL( SUM( ec_orderdetail.total_price ), 0 ) as total, SUM( ec_order.discount_total ) AS discount_total, SUM( ec_order.refund_total ) AS refund_total, DATE_FORMAT( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ), '%m/%d/%Y' ) as date, DAY( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) AS order_day, MONTH( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) AS order_month FROM ec_orderdetail LEFT JOIN ec_order ON ec_order.order_id = ec_orderdetail.order_id LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id WHERE ec_order.order_date > DATE_SUB( DATE_ADD( NOW( ), INTERVAL " . $this->date_diff . " HOUR ), INTERVAL 14 DAY ) AND ec_orderstatus.is_approved = 1 " . $product_where . "GROUP BY order_day, order_month ORDER BY ec_order.order_date DESC" );
            }
		}else{
            if( $this->date_diff < 0 ){
                $sales_data = $this->wpdb->get_results( "SELECT SUM( ec_order.sub_total ) as total, SUM( ec_order.discount_total ) AS discount_total, SUM( ec_order.refund_total ) AS refund_total, DATE_FORMAT( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ), '%m/%d/%Y' ) as date, DAY( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) AS order_day, MONTH( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) AS order_month FROM ec_order LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id WHERE DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) > DATE_SUB( DATE_SUB( NOW( ), INTERVAL " . ($this->date_diff*-1) . " HOUR ), INTERVAL 14 DAY ) AND ec_orderstatus.is_approved = 1 " . $product_where . "GROUP BY order_day, order_month ORDER BY ec_order.order_date DESC" );
            }else{
                $sales_data = $this->wpdb->get_results( "SELECT SUM( ec_order.sub_total ) as total, SUM( ec_order.discount_total ) AS discount_total, SUM( ec_order.refund_total ) AS refund_total, DATE_FORMAT( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ), '%m/%d/%Y' ) as date, DAY( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) AS order_day, MONTH( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) AS order_month FROM ec_order LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id WHERE ec_order.order_date > DATE_SUB( DATE_ADD( NOW( ), INTERVAL " . $this->date_diff . " HOUR ), INTERVAL 14 DAY ) AND ec_orderstatus.is_approved = 1 " . $product_where . "GROUP BY order_day, order_month ORDER BY ec_order.order_date DESC" );
            }
        }
		$current_index = 0;
		for( $i=0; $i<14; $i++ ){
			if( count( $sales_data ) > $current_index && $sales_data[$current_index]->date == date( 'm/d/Y', strtotime( '- ' . $i . 'day' ) ) ){
				$sales[] = $sales_data[$current_index];
				$current_index++;
			}else{
				$sales[] = (object) array( 'date' => date( 'm/d/Y', strtotime( '- ' . $i . 'day' ) ), 'total' => 0, 'discount_total' => 0, 'refund_total' => 0 );
			}
		}
		return $sales;
	}
	
	private function get_weekly_sales( $product_id ){
		$sales = array( );
		$product_where = "";
		if( $product_id ){
			$product_where = $this->wpdb->prepare( "AND ec_orderdetail.product_id = %d ", $product_id );
            if( $this->date_diff < 0 ){
                $sales_data = $this->wpdb->get_results( "SELECT IFNULL( SUM( ec_orderdetail.total_price ), 0 ) as total, 0 AS discount_total, 0 AS refund_total, DATE_FORMAT( DATE_SUB( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ), INTERVAL ( DAYOFWEEK( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) - 1 ) DAY ), '%m/%d/%Y' ) as date, WEEK( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) AS order_week, YEAR( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) AS order_year FROM ec_orderdetail LEFT JOIN ec_order ON ec_order.order_id = ec_orderdetail.order_id LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id WHERE DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) > DATE_SUB( DATE_SUB( NOW( ), INTERVAL " . ($this->date_diff*-1) . " HOUR ), INTERVAL 14 WEEK ) AND ec_orderstatus.is_approved = 1 " . $product_where . "GROUP BY date ORDER BY ec_order.order_date DESC" );
            }else{
                $sales_data = $this->wpdb->get_results( "SELECT IFNULL( SUM( ec_orderdetail.total_price ), 0 ) as total, 0 AS discount_total, 0 AS refund_total, DATE_FORMAT( DATE_SUB( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ), INTERVAL ( DAYOFWEEK( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) - 1 ) DAY ), '%m/%d/%Y' ) as date, WEEK( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) AS order_week, YEAR( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) AS order_year FROM ec_orderdetail LEFT JOIN ec_order ON ec_order.order_id = ec_orderdetail.order_id LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id WHERE ec_order.order_date > DATE_SUB( DATE_ADD( NOW( ), INTERVAL " . $this->date_diff . " HOUR ), INTERVAL 14 WEEK ) AND ec_orderstatus.is_approved = 1 " . $product_where . "GROUP BY date ORDER BY ec_order.order_date DESC" );
            }
		}else{
            if( $this->date_diff < 0 ){
                $sales_data = $this->wpdb->get_results( "SELECT IFNULL( SUM( ec_order.sub_total ), 0 ) as total, SUM( ec_order.discount_total ) AS discount_total, SUM( ec_order.refund_total ) AS refund_total, DATE_FORMAT( DATE_SUB( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ), INTERVAL ( DAYOFWEEK( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) - 1 ) DAY ), '%m/%d/%Y' ) as date, WEEK( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) AS order_week, YEAR( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) AS order_year FROM ec_order LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id WHERE DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) > DATE_SUB( DATE_SUB( NOW( ), INTERVAL " . ($this->date_diff*-1) . " HOUR ), INTERVAL 14 WEEK ) AND ec_orderstatus.is_approved = 1 " . $product_where . "GROUP BY date ORDER BY ec_order.order_date DESC" );
            }else{
                $sales_data = $this->wpdb->get_results( "SELECT IFNULL( SUM( ec_order.sub_total ), 0 ) as total, SUM( ec_order.discount_total ) AS discount_total, SUM( ec_order.refund_total ) AS refund_total, DATE_FORMAT( DATE_SUB( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ), INTERVAL ( DAYOFWEEK( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) - 1 ) DAY ), '%m/%d/%Y' ) as date, WEEK( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) AS order_week, YEAR( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) AS order_year FROM ec_order LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id WHERE ec_order.order_date > DATE_SUB( DATE_ADD( NOW( ), INTERVAL " . $this->date_diff . " HOUR ), INTERVAL 14 WEEK ) AND ec_orderstatus.is_approved = 1 " . $product_where . "GROUP BY date ORDER BY ec_order.order_date DESC" );
            }
        }
		$current_index = 0;
		for( $i=0; $i<14; $i++ ){
			if( count( $sales_data ) > $current_index && $sales_data[$current_index]->date == date( 'm/d/Y', strtotime( '- ' . $i . 'weeks', strtotime( '-' . date( 'w' ) . ' days' ) ) ) ){
				$sales[] = $sales_data[$current_index];
				$current_index++;
			}else{
				$sales[] = (object) array( 'date' => date( 'm/d/Y', strtotime( '- ' . $i . 'week', strtotime( '-' . date( 'w' ) . ' days' ) ) ), 'total' => 0, 'discount_total' => 0, 'refund_total' => 0 );
			}
		}
		return $sales;
	}
	
	private function get_monthly_sales( $product_id ){
		$sales = array( );
		$product_where = "";
		if( $product_id ){
			$product_where = $this->wpdb->prepare( "AND ec_orderdetail.product_id = %d ", $product_id );
            if( $this->date_diff < 0 ){
                $sales_data = $this->wpdb->get_results( "SELECT IFNULL( SUM( ec_orderdetail.total_price ), 0 ) as total, 0 AS discount_total, 0 AS refund_total, DATE_FORMAT( DATE_SUB( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ), INTERVAL ( DAYOFMONTH( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) - 1 ) DAY ), '%m/%d/%Y' ) as date, MONTH( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) AS order_month, MONTHNAME( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) AS order_month, YEAR( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) AS order_year FROM ec_orderdetail LEFT JOIN ec_order ON ec_order.order_id = ec_orderdetail.order_id LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id WHERE DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) > DATE_SUB( DATE_SUB( NOW( ), INTERVAL " . ($this->date_diff*-1) . " HOUR ), INTERVAL 14 MONTH ) AND ec_orderstatus.is_approved = 1 " . $product_where . "GROUP BY order_month, order_year ORDER BY ec_order.order_date DESC" );
            }else{
                $sales_data = $this->wpdb->get_results( "SELECT IFNULL( SUM( ec_orderdetail.total_price ), 0 ) as total, 0 AS discount_total, 0 AS refund_total, DATE_FORMAT( DATE_SUB( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ), INTERVAL ( DAYOFMONTH( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) - 1 ) DAY ), '%m/%d/%Y' ) as date, MONTH( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) AS order_month, MONTHNAME( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) AS order_month, YEAR( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) AS order_year FROM ec_orderdetail LEFT JOIN ec_order ON ec_order.order_id = ec_orderdetail.order_id LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id WHERE DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) > DATE_SUB( DATE_ADD( NOW( ), INTERVAL " . $this->date_diff . " HOUR ), INTERVAL 14 MONTH ) AND ec_orderstatus.is_approved = 1 " . $product_where . "GROUP BY order_month, order_year ORDER BY ec_order.order_date DESC" );
            }
		}else{
            if( $this->date_diff < 0 ){
                $sales_data = $this->wpdb->get_results( "SELECT IFNULL( SUM( ec_order.sub_total ), 0 ) as total, SUM( ec_order.discount_total ) AS discount_total, SUM( ec_order.refund_total ) AS refund_total, DATE_FORMAT( DATE_SUB( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ), INTERVAL ( DAYOFMONTH( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) - 1 ) DAY ), '%m/%d/%Y' ) as date, MONTH( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) AS order_month, MONTHNAME( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) AS order_month, YEAR( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) AS order_year FROM ec_order LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id WHERE DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) > DATE_SUB( DATE_SUB( NOW( ), INTERVAL " . ($this->date_diff*-1) . " HOUR ), INTERVAL 14 MONTH ) AND ec_orderstatus.is_approved = 1 " . $product_where . "GROUP BY order_month, order_year ORDER BY ec_order.order_date DESC" );
            }else{
                $sales_data = $this->wpdb->get_results( "SELECT IFNULL( SUM( ec_order.sub_total ), 0 ) as total, SUM( ec_order.discount_total ) AS discount_total, SUM( ec_order.refund_total ) AS refund_total, DATE_FORMAT( DATE_SUB( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ), INTERVAL ( DAYOFMONTH( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) - 1 ) DAY ), '%m/%d/%Y' ) as date, MONTH( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) AS order_month, MONTHNAME( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) AS order_month, YEAR( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) AS order_year FROM ec_order LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id WHERE DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) > DATE_SUB( DATE_ADD( NOW( ), INTERVAL " . $this->date_diff . " HOUR ), INTERVAL 14 MONTH ) AND ec_orderstatus.is_approved = 1 " . $product_where . "GROUP BY order_month, order_year ORDER BY ec_order.order_date DESC" );
            }
        }
		$current_index = 0;
		for( $i=0; $i<14; $i++ ){
			if( count( $sales_data ) > $current_index && $sales_data[$current_index]->date == date( 'm/d/Y', strtotime( '- ' . $i . 'months', strtotime( '-' . (date( 'd' )-1) . ' days' ) ) ) ){
				$sales[] = $sales_data[$current_index];
				$current_index++;
			}else{
				$sales[] = (object) array( 'date' => date( 'm/d/Y', strtotime( '- ' . $i . 'month', strtotime( '-' . (date( 'd' )-1) . ' days' ) ) ), 'total' => 0, 'discount_total' => 0, 'refund_total' => 0 );
			}
		}
		return $sales;
	}
	
	private function get_yearly_sales( $product_id ){
		$sales = array( );
		$product_where = "";
		if( $product_id ){
			$product_where = $this->wpdb->prepare( "AND ec_orderdetail.product_id = %d ", $product_id );
            if( $this->date_diff < 0 ){
                $sales_data = $this->wpdb->get_results( "SELECT IFNULL( SUM( ec_orderdetail.total_price ), 0 ) as total, 0 AS discount_total, 0 AS refund_total, DATE_FORMAT( DATE_SUB( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ), INTERVAL ( DAYOFYEAR( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) - 1 ) DAY ), '%m/%d/%Y' ) as date, YEAR( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) AS order_year FROM ec_orderdetail LEFT JOIN ec_order ON ec_order.order_id = ec_orderdetail.order_id LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id WHERE DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) > DATE_SUB( DATE_SUB( NOW( ), INTERVAL " . ($this->date_diff*-1) . " HOUR ), INTERVAL 14 YEAR ) AND ec_orderstatus.is_approved = 1 " . $product_where . "GROUP BY order_year ORDER BY ec_order.order_date DESC" );
            }else{
                $sales_data = $this->wpdb->get_results( "SELECT IFNULL( SUM( ec_orderdetail.total_price ), 0 ) as total, 0 AS discount_total, 0 AS refund_total, DATE_FORMAT( DATE_SUB( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ), INTERVAL ( DAYOFYEAR( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) - 1 ) DAY ), '%m/%d/%Y' ) as date, YEAR( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) AS order_year FROM ec_orderdetail LEFT JOIN ec_order ON ec_order.order_id = ec_orderdetail.order_id LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id WHERE ec_order.order_date > DATE_SUB( DATE_ADD( NOW( ), INTERVAL " . $this->date_diff . " HOUR ), INTERVAL 14 YEAR ) AND ec_orderstatus.is_approved = 1 " . $product_where . "GROUP BY order_year ORDER BY ec_order.order_date DESC" );
            }
		}else{
            if( $this->date_diff < 0 ){
                $sales_data = $this->wpdb->get_results( "SELECT IFNULL( SUM( ec_order.sub_total ), 0 ) as total, SUM( ec_order.discount_total ) AS discount_total, SUM( ec_order.refund_total ) AS refund_total, DATE_FORMAT( DATE_SUB( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ), INTERVAL ( DAYOFYEAR( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) - 1 ) DAY ), '%m/%d/%Y' ) as date, YEAR( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) AS order_year FROM ec_order LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id WHERE DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) > DATE_SUB( DATE_SUB( NOW( ), INTERVAL " . ($this->date_diff*-1) . " HOUR ), INTERVAL 14 YEAR ) AND ec_orderstatus.is_approved = 1 " . $product_where . "GROUP BY order_year ORDER BY ec_order.order_date DESC" );
            }else{
                $sales_data = $this->wpdb->get_results( "SELECT IFNULL( SUM( ec_order.sub_total ), 0 ) as total, SUM( ec_order.discount_total ) AS discount_total, SUM( ec_order.refund_total ) AS refund_total, DATE_FORMAT( DATE_SUB( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ), INTERVAL ( DAYOFYEAR( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) - 1 ) DAY ), '%m/%d/%Y' ) as date, YEAR( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) AS order_year FROM ec_order LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id WHERE ec_order.order_date > DATE_SUB( DATE_ADD( NOW( ), INTERVAL " . $this->date_diff . " HOUR ), INTERVAL 14 YEAR ) AND ec_orderstatus.is_approved = 1 " . $product_where . "GROUP BY order_year ORDER BY ec_order.order_date DESC" );
            }
        }
		$current_index = 0;
		for( $i=0; $i<14; $i++ ){
			if( count( $sales_data ) > $current_index && $sales_data[$current_index]->date == date( 'm/d/Y', strtotime( '- ' . $i . 'years', strtotime( '-' . date('z') . ' days' ) ) ) ){
				$sales[] = $sales_data[$current_index];
				$current_index++;
			}else{
				$sales[] = (object) array( 'date' => date( 'm/d/Y', strtotime( '- ' . $i . 'years', strtotime( '-' . date('z') . ' days' ) ) ), 'total' => 0, 'discount_total' => 0, 'refund_total' => 0 );
			}
		}
		return $sales;
	}
	
	private function get_daily_items_sold( $product_id ){
		$sales = array( );
		$product_where = "";
		if( $product_id ){
			$product_where = $this->wpdb->prepare( "AND ec_orderdetail.product_id = %d ", $product_id );
		}
		if( $this->date_diff < 0 ){
			$sales_data = $this->wpdb->get_results( "SELECT IFNULL( SUM( ec_orderdetail.quantity ), 0 ) as total, 0 AS discount_total, 0 AS refund_total, DATE_FORMAT( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ), '%m/%d/%Y' ) as date, DAY( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) AS order_day FROM ec_orderdetail LEFT JOIN ec_order ON ec_order.order_id = ec_orderdetail.order_id LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id WHERE DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) > DATE_SUB( DATE_SUB( NOW( ), INTERVAL " . ($this->date_diff*-1) . " HOUR ), INTERVAL 14 DAY ) AND ec_orderstatus.is_approved = 1 " . $product_where . "GROUP BY order_day ORDER BY ec_order.order_date DESC" );
		}else{
			$sales_data = $this->wpdb->get_results( "SELECT IFNULL( SUM( ec_orderdetail.quantity ), 0 ) as total, 0 AS discount_total, 0 AS refund_total, DATE_FORMAT( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ), '%m/%d/%Y' ) as date, DAY( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) AS order_day FROM ec_orderdetail LEFT JOIN ec_order ON ec_order.order_id = ec_orderdetail.order_id LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id WHERE ec_order.order_date > DATE_SUB( DATE_ADD( NOW( ), INTERVAL " . $this->date_diff . " HOUR ), INTERVAL 14 DAY ) AND ec_orderstatus.is_approved = 1 " . $product_where . "GROUP BY order_day ORDER BY ec_order.order_date DESC" );
		}
		$current_index = 0;
		for( $i=0; $i<14; $i++ ){
			if( count( $sales_data ) > $current_index && $sales_data[$current_index]->date == date( 'm/d/Y', strtotime( '- ' . $i . 'day' ) ) ){
				$sales[] = $sales_data[$current_index];
				$current_index++;
			}else{
				$sales[] = (object) array( 'date' => date( 'm/d/Y', strtotime( '- ' . $i . 'day' ) ), 'total' => 0, 'discount_total' => 0, 'refund_total' => 0 );
			}
		}
		return $sales;
	}
	
	private function get_weekly_items_sold( $product_id ){
		$sales = array( );
		$product_where = "";
		if( $product_id ){
			$product_where = $this->wpdb->prepare( "AND ec_orderdetail.product_id = %d ", $product_id );
		}
		if( $this->date_diff < 0 ){
			$sales_data = $this->wpdb->get_results( "SELECT IFNULL( SUM( ec_orderdetail.quantity ), 0 ) as total, 0 AS discount_total, 0 AS refund_total, DATE_FORMAT( DATE_SUB( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ), INTERVAL ( DAYOFWEEK( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) - 1 ) DAY ), '%m/%d/%Y' ) as date, WEEK( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) AS order_week, YEAR( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) AS order_year FROM ec_orderdetail LEFT JOIN ec_order ON ec_order.order_id = ec_orderdetail.order_id LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id WHERE ec_order.order_date > DATE_SUB( DATE_SUB( NOW( ), INTERVAL " . ($this->date_diff*-1) . " HOUR ), INTERVAL 14 WEEK ) AND ec_orderstatus.is_approved = 1 " . $product_where . "GROUP BY order_week, order_year ORDER BY ec_order.order_date DESC" );
		}else{
			$sales_data = $this->wpdb->get_results( "SELECT IFNULL( SUM( ec_orderdetail.quantity ), 0 ) as total, 0 AS discount_total, 0 AS refund_total, DATE_FORMAT( DATE_SUB( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ), INTERVAL ( DAYOFWEEK( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) - 1 ) DAY ), '%m/%d/%Y' ) as date, WEEK( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) AS order_week, YEAR( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) AS order_year FROM ec_orderdetail LEFT JOIN ec_order ON ec_order.order_id = ec_orderdetail.order_id LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id WHERE ec_order.order_date > DATE_SUB( DATE_ADD( NOW( ), INTERVAL " . $this->date_diff . " HOUR ), INTERVAL 14 WEEK ) AND ec_orderstatus.is_approved = 1 " . $product_where . "GROUP BY order_week, order_year ORDER BY ec_order.order_date DESC" );
		}
		$current_index = 0;
		for( $i=0; $i<14; $i++ ){
			if( count( $sales_data ) > $current_index && $sales_data[$current_index]->date == date( 'm/d/Y', strtotime( '- ' . $i . 'weeks', strtotime( '-' . date( 'w' ) . ' days' ) ) ) ){
				$sales[] = $sales_data[$current_index];
				$current_index++;
			}else{
				$sales[] = (object) array( 'date' => date( 'm/d/Y', strtotime( '- ' . $i . 'week', strtotime( '-' . date( 'w' ) . ' days' ) ) ), 'total' => 0, 'discount_total' => 0, 'refund_total' => 0 );
			}
		}
		return $sales;
	}
	
	private function get_monthly_items_sold( $product_id ){
		$sales = array( );
		$product_where = "";
		if( $product_id ){
			$product_where = $this->wpdb->prepare( "AND ec_orderdetail.product_id = %d ", $product_id );
		}
		if( $this->date_diff < 0 ){
			$sales_data = $this->wpdb->get_results( "SELECT IFNULL( SUM( ec_orderdetail.quantity ), 0 ) as total, 0 AS discount_total, 0 AS refund_total, DATE_FORMAT( DATE_SUB( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ), INTERVAL ( DAYOFMONTH( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) - 1 ) DAY ), '%m/%d/%Y' ) as date, MONTHNAME( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) AS order_month, MONTH( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) AS order_month, YEAR( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) AS order_year FROM ec_orderdetail LEFT JOIN ec_order ON ec_order.order_id = ec_orderdetail.order_id LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id WHERE ec_order.order_date > DATE_SUB( DATE_SUB( NOW( ), INTERVAL " . ($this->date_diff*-1) . " HOUR ), INTERVAL 14 MONTH ) AND ec_orderstatus.is_approved = 1 " . $product_where . "GROUP BY order_month, order_year ORDER BY ec_order.order_date DESC" );
		}else{
			$sales_data = $this->wpdb->get_results( "SELECT IFNULL( SUM( ec_orderdetail.quantity ), 0 ) as total, 0 AS discount_total, 0 AS refund_total, DATE_FORMAT( DATE_SUB( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ), INTERVAL ( DAYOFMONTH( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) - 1 ) DAY ), '%m/%d/%Y' ) as date, MONTHNAME( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) AS order_month, MONTH( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) AS order_month, YEAR( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) AS order_year FROM ec_orderdetail LEFT JOIN ec_order ON ec_order.order_id = ec_orderdetail.order_id LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id WHERE ec_order.order_date > DATE_SUB( DATE_ADD( NOW( ), INTERVAL " . $this->date_diff . " HOUR ), INTERVAL 14 MONTH ) AND ec_orderstatus.is_approved = 1 " . $product_where . "GROUP BY order_month, order_year ORDER BY ec_order.order_date DESC" );
		}
		$current_index = 0;
		for( $i=0; $i<14; $i++ ){
			if( count( $sales_data ) > $current_index && $sales_data[$current_index]->date == date( 'm/d/Y', strtotime( '- ' . $i . 'months', strtotime( '-' . (date( 'd' )-1) . ' days' ) ) ) ){
				$sales[] = $sales_data[$current_index];
				$current_index++;
			}else{
				$sales[] = (object) array( 'date' => date( 'm/d/Y', strtotime( '- ' . $i . 'month', strtotime( '-' . (date( 'd' )-1) . ' days' ) ) ), 'total' => 0, 'discount_total' => 0, 'refund_total' => 0 );
			}
		}
		return $sales;
	}
	
	private function get_yearly_items_sold( $product_id ){
		$sales = array( );
		$product_where = "";
		if( $product_id ){
			$product_where = $this->wpdb->prepare( "AND ec_orderdetail.product_id = %d ", $product_id );
		}
		if( $this->date_diff < 0 ){
			$sales_data = $this->wpdb->get_results( "SELECT IFNULL( SUM( ec_orderdetail.quantity ), 0 ) as total, 0 AS discount_total, 0 AS refund_total, DATE_FORMAT( DATE_SUB( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ), INTERVAL ( DAYOFYEAR( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) - 1 ) DAY ), '%m/%d/%Y' ) as date, YEAR( DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) ) AS order_year FROM ec_orderdetail LEFT JOIN ec_order ON ec_order.order_id = ec_orderdetail.order_id LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id WHERE ec_order.order_date > DATE_SUB( DATE_SUB( NOW( ), INTERVAL " . ($this->date_diff*-1) . " HOUR ), INTERVAL 14 YEAR ) AND ec_orderstatus.is_approved = 1 " . $product_where . "GROUP BY order_year ORDER BY ec_order.order_date DESC" );
		}else{
			$sales_data = $this->wpdb->get_results( "SELECT IFNULL( SUM( ec_orderdetail.quantity ), 0 ) as total, 0 AS discount_total, 0 AS refund_total, DATE_FORMAT( DATE_SUB( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ), INTERVAL ( DAYOFYEAR( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) - 1 ) DAY ), '%m/%d/%Y' ) as date, YEAR( DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) ) AS order_year FROM ec_orderdetail LEFT JOIN ec_order ON ec_order.order_id = ec_orderdetail.order_id LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id WHERE ec_order.order_date > DATE_SUB( DATE_ADD( NOW( ), INTERVAL " . $this->date_diff . " HOUR ), INTERVAL 14 YEAR ) AND ec_orderstatus.is_approved = 1 " . $product_where . "GROUP BY order_year ORDER BY ec_order.order_date DESC" );
		}
		$current_index = 0;
		for( $i=0; $i<14; $i++ ){
			if( count( $sales_data ) > $current_index && $sales_data[$current_index]->date == date( 'm/d/Y', strtotime( '- ' . $i . 'years', strtotime( '-' . date('z') . ' days' ) ) ) ){
				$sales[] = $sales_data[$current_index];
				$current_index++;
			}else{
				$sales[] = (object) array( 'date' => date( 'm/d/Y', strtotime( '- ' . $i . 'years', strtotime( '-' . date('z') . ' days' ) ) ), 'total' => 0, 'discount_total' => 0, 'refund_total' => 0 );
			}
		}
		return $sales;
	}
	
	private function get_daily_abandonment( $product_id ){
		$sales = array( );
		$product_where = "";
		if( $product_id ){
			$product_where = $this->wpdb->prepare( "AND ec_tempcart.product_id = %d ", $product_id );
		}
		if( $this->date_diff < 0 ){
			$sales_data = $this->wpdb->get_results( "SELECT COUNT( ec_tempcart.session_id ) as total, 0 AS discount_total, 0 AS refund_total, DATE_FORMAT( ec_tempcart.last_changed_date, '%m/%d/%Y') as date, DAY( ec_tempcart.last_changed_date ) as cart_day, MONTH( ec_tempcart.last_changed_date ) as cart_month FROM ec_tempcart WHERE ec_tempcart.last_changed_date > DATE_SUB( DATE_SUB( NOW( ), INTERVAL " . ($this->date_diff*-1) . " HOUR ), INTERVAL 14 DAY ) " . $product_where . "GROUP BY cart_day, cart_month ORDER BY ec_tempcart.last_changed_date DESC" );
		}else{
			$sales_data = $this->wpdb->get_results( "SELECT COUNT( ec_tempcart.session_id ) as total, 0 AS discount_total, 0 AS refund_total, DATE_FORMAT( ec_tempcart.last_changed_date, '%m/%d/%Y') as date, DAY( ec_tempcart.last_changed_date ) as cart_day, MONTH( ec_tempcart.last_changed_date ) as cart_month FROM ec_tempcart WHERE ec_tempcart.last_changed_date > DATE_SUB( DATE_ADD( NOW( ), INTERVAL " . $this->date_diff . " HOUR ), INTERVAL 14 DAY ) " . $product_where . "GROUP BY cart_day, cart_month ORDER BY ec_tempcart.last_changed_date DESC" );
		}
		$current_index = 0;
		for( $i=0; $i<14; $i++ ){
			if( count( $sales_data ) > $current_index && $sales_data[$current_index]->date == date( 'm/d/Y', strtotime( '- ' . $i . 'day' ) ) ){
				$sales[] = $sales_data[$current_index];
				$current_index++;
			}else{
				$sales[] = (object) array( 'date' => date( 'm/d/Y', strtotime( '- ' . $i . 'day' ) ), 'total' => 0, 'discount_total' => 0, 'refund_total' => 0 );
			}
		}
		return $sales;
	}
	
	private function get_weekly_abandonment( $product_id ){
		$sales = array( );
		$product_where = "";
		if( $product_id ){
			$product_where = $this->wpdb->prepare( "AND ec_tempcart.product_id = %d ", $product_id );
		}
		if( $this->date_diff < 0 ){
			$sales_data = $this->wpdb->get_results( "SELECT COUNT( ec_tempcart.session_id ) as total, 0 AS discount_total, 0 AS refund_total, DATE_FORMAT( DATE_SUB( ec_tempcart.last_changed_date, INTERVAL ( DAYOFWEEK( ec_tempcart.last_changed_date ) - 1 ) DAY ), '%m/%d/%Y' ) as date, WEEK( ec_tempcart.last_changed_date ) as cart_week, YEAR( ec_tempcart.last_changed_date ) as cart_year FROM ec_tempcart WHERE ec_tempcart.last_changed_date > DATE_SUB( DATE_SUB( NOW( ), INTERVAL " . ($this->date_diff*-1) . " HOUR ), INTERVAL 14 WEEK ) " . $product_where . "GROUP BY cart_week, cart_year ORDER BY ec_tempcart.last_changed_date DESC" );
		}else{
			$sales_data = $this->wpdb->get_results( "SELECT COUNT( ec_tempcart.session_id ) as total, 0 AS discount_total, 0 AS refund_total, DATE_FORMAT( DATE_SUB( ec_tempcart.last_changed_date, INTERVAL ( DAYOFWEEK( ec_tempcart.last_changed_date ) - 1 ) DAY ), '%m/%d/%Y' ) as date, WEEK( ec_tempcart.last_changed_date ) as cart_week, YEAR( ec_tempcart.last_changed_date ) as cart_year FROM ec_tempcart WHERE ec_tempcart.last_changed_date > DATE_SUB( DATE_ADD( NOW( ), INTERVAL " . $this->date_diff . " HOUR ), INTERVAL 14 WEEK ) " . $product_where . "GROUP BY cart_week, cart_year ORDER BY ec_tempcart.last_changed_date DESC" );
		}
		$current_index = 0;
		for( $i=0; $i<14; $i++ ){
			if( count( $sales_data ) > $current_index && $sales_data[$current_index]->date == date( 'm/d/Y', strtotime( '- ' . $i . 'weeks', strtotime( '-' . date( 'w' ) . ' days' ) ) ) ){
				$sales[] = $sales_data[$current_index];
				$current_index++;
			}else{
				$sales[] = (object) array( 'date' => date( 'm/d/Y', strtotime( '- ' . $i . 'week', strtotime( '-' . date( 'w' ) . ' days' ) ) ), 'total' => 0, 'discount_total' => 0, 'refund_total' => 0 );
			}
		}
		return $sales;
	}
	
	private function get_monthly_abandonment( $product_id ){
		$sales = array( );
		$product_where = "";
		if( $product_id ){
			$product_where = $this->wpdb->prepare( "AND ec_tempcart.product_id = %d ", $product_id );
		}
		if( $this->date_diff < 0 ){
			$sales_data = $this->wpdb->get_results( "SELECT COUNT( ec_tempcart.session_id ) as total, 0 AS discount_total, 0 AS refund_total, DATE_FORMAT( DATE_SUB( ec_tempcart.last_changed_date, INTERVAL ( DAYOFMONTH( ec_tempcart.last_changed_date ) - 1 ) DAY ), '%m/%d/%Y' ) as date, MONTHNAME( ec_tempcart.last_changed_date ) AS cart_month, MONTH( ec_tempcart.last_changed_date ) as cart_month, YEAR( ec_tempcart.last_changed_date ) as cart_year FROM ec_tempcart WHERE ec_tempcart.last_changed_date > DATE_SUB( DATE_SUB( NOW( ), INTERVAL " . ($this->date_diff*-1) . " HOUR ), INTERVAL 14 MONTH ) " . $product_where . "GROUP BY cart_month, cart_year ORDER BY ec_tempcart.last_changed_date DESC" );
		}else{
			$sales_data = $this->wpdb->get_results( "SELECT COUNT( ec_tempcart.session_id ) as total, 0 AS discount_total, 0 AS refund_total, DATE_FORMAT( DATE_SUB( ec_tempcart.last_changed_date, INTERVAL ( DAYOFMONTH( ec_tempcart.last_changed_date ) - 1 ) DAY ), '%m/%d/%Y' ) as date, MONTHNAME( ec_tempcart.last_changed_date ) AS cart_month, MONTH( ec_tempcart.last_changed_date ) as cart_month, YEAR( ec_tempcart.last_changed_date ) as cart_year FROM ec_tempcart WHERE ec_tempcart.last_changed_date > DATE_SUB( DATE_ADD( NOW( ), INTERVAL " . $this->date_diff . " HOUR ), INTERVAL 14 MONTH ) " . $product_where . "GROUP BY cart_month, cart_year ORDER BY ec_tempcart.last_changed_date DESC" );
		}
		$current_index = 0;
		for( $i=0; $i<14; $i++ ){
			if( count( $sales_data ) > $current_index && $sales_data[$current_index]->date == date( 'm/d/Y', strtotime( '- ' . $i . 'months', strtotime( '-' . (date( 'd' )-1) . ' days' ) ) ) ){
				$sales[] = $sales_data[$current_index];
				$current_index++;
			}else{
				$sales[] = (object) array( 'date' => date( 'm/d/Y', strtotime( '- ' . $i . 'month', strtotime( '-' . (date( 'd' )-1) . ' days' ) ) ), 'total' => 0, 'discount_total' => 0, 'refund_total' => 0 );
			}
		}
		return $sales;
	}
	
	private function get_yearly_abandonment( $product_id ){
		$sales = array( );
		$product_where = "";
		if( $product_id ){
			$product_where = $this->wpdb->prepare( "AND ec_tempcart.product_id = %d ", $product_id );
		}
		if( $this->date_diff < 0 ){
			$sales_data = $this->wpdb->get_results( "SELECT COUNT( ec_tempcart.session_id ) as total, 0 AS discount_total, 0 AS refund_total, DATE_FORMAT( DATE_SUB( ec_tempcart.last_changed_date, INTERVAL ( DAYOFYEAR( ec_tempcart.last_changed_date ) - 1 ) DAY ), '%m/%d/%Y' ) as date, YEAR( ec_tempcart.last_changed_date ) as cart_year FROM ec_tempcart WHERE ec_tempcart.last_changed_date > DATE_SUB( DATE_SUB( NOW( ), INTERVAL " . ($this->date_diff*-1) . " HOUR ), INTERVAL 14 YEAR ) " . $product_where . "GROUP BY cart_year ORDER BY ec_tempcart.last_changed_date DESC" );
		}else{
			$sales_data = $this->wpdb->get_results( "SELECT COUNT( ec_tempcart.session_id ) as total, 0 AS discount_total, 0 AS refund_total, DATE_FORMAT( DATE_SUB( ec_tempcart.last_changed_date, INTERVAL ( DAYOFYEAR( ec_tempcart.last_changed_date ) - 1 ) DAY ), '%m/%d/%Y' ) as date, YEAR( ec_tempcart.last_changed_date ) as cart_year FROM ec_tempcart WHERE ec_tempcart.last_changed_date > DATE_SUB( DATE_ADD( NOW( ), INTERVAL " . $this->date_diff . " HOUR ), INTERVAL 14 YEAR ) " . $product_where . "GROUP BY cart_year ORDER BY ec_tempcart.last_changed_date DESC" );
		}
		$current_index = 0;
		for( $i=0; $i<14; $i++ ){
			if( count( $sales_data ) > $current_index && $sales_data[$current_index]->date == date( 'm/d/Y', strtotime( '- ' . $i . 'years', strtotime( '-' . date('z') . ' days' ) ) ) ){
				$sales[] = $sales_data[$current_index];
				$current_index++;
			}else{
				$sales[] = (object) array( 'date' => date( 'm/d/Y', strtotime( '- ' . $i . 'years', strtotime( '-' . date('z') . ' days' ) ) ), 'total' => 0, 'discount_total' => 0, 'refund_total' => 0 );
			}
		}
		return $sales;
	}
	
	private function get_total_new_orders( ){
		if( $this->date_diff < 0 ){
			return $this->wpdb->get_var( "SELECT COUNT( ec_order.order_id ) as total FROM ec_order WHERE DATE_SUB( ec_order.order_date, INTERVAL " . ($this->date_diff*-1) . " HOUR ) > DATE_SUB( DATE_SUB( NOW( ), INTERVAL " . ($this->date_diff*-1) . " HOUR ), INTERVAL 1 WEEK )" );
		}else{
			return $this->wpdb->get_var( "SELECT COUNT( ec_order.order_id ) as total FROM ec_order WHERE DATE_ADD( ec_order.order_date, INTERVAL " . $this->date_diff . " HOUR ) > DATE_SUB( DATE_ADD( NOW( ), INTERVAL " . $this->date_diff . " HOUR ), INTERVAL 1 WEEK )" );
		}
	}
	
	private function get_total_new_reviews( ){
		return $this->wpdb->get_var( "SELECT COUNT( ec_review.review_id ) as total FROM ec_review WHERE ec_review.approved = 0" );
	}
	
	private function get_total_cart_users( ){
		return $this->wpdb->get_var( "SELECT COUNT( ec_user.user_id ) as total FROM ec_user" );
	}
	
	private function get_sales_dataset( $start_date, $end_date, $range = 'daily', $product_id = false, $country = false, $billing_country = false ){
		$days_length = $this->get_sales_dataset_length( $start_date, $end_date, $range );
        if( $range == 'daily' && $days_length <= 2 ){
            $days_length = ($days_length+1) * 24;
            $range = 'hourly';
        }
		
        $product_where = "";
        $country_where = "";
        $billing_country_where = "";
        if( $product_id ){
			$product_where = $this->wpdb->prepare( "AND ec_orderdetail.product_id = %d ", $product_id );
        }
        if( $country ){
			$country_where = $this->wpdb->prepare( "AND ec_order.shipping_country = %s ", $country );
        }
        if( $billing_country ){
			$billing_country_where = $this->wpdb->prepare( "AND ec_order.billing_country = %s ", $billing_country );
        }
        
        if( $range == 'hourly' ){
            $groupby = 'order_hour, order_day';
        }else if( $range == 'daily' ){
            $groupby = 'order_day, order_month';
        }else if( $range == 'weekly' ){
            $groupby = 'order_week, order_year';
        }else if( $range == 'monthly' ){
            $groupby = 'order_month, order_year';
        }else if( $range == 'yearly' ){
            $groupby = 'order_year';
        }
        
        $date_diff = $this->date_diff;
        $date_diff_func = 'DATE_ADD';
        if( $date_diff < 0 ){
            $date_diff = $date_diff * -1;
            $date_diff_func = 'DATE_SUB';
        }
        
        $sales_data = $this->wpdb->get_results( "SELECT 
                SUM( 
					IFNULL( ( SELECT SUM( ec_orderdetail.total_price ) FROM ec_orderdetail WHERE ec_orderdetail.order_id = ec_order.order_id ), 0 ) 
				) as total,
                SUM( 
					IFNULL( ( SELECT SUM( ec_orderdetail.total_price ) FROM ec_orderdetail WHERE ec_orderdetail.order_id = ec_order.order_id " . $product_where . " " . $country_where . " " . $billing_country_where . " ), 0 ) 
				) as item_total,
				SUM( ec_order.tax_total ) AS tax_total,
				SUM( ec_order.vat_total ) AS vat_total,
				SUM( ec_order.gst_total ) AS gst_total,
				SUM( ec_order.hst_total ) AS hst_total,
				SUM( ec_order.pst_total ) AS pst_total,
				SUM( ec_order.shipping_total ) AS shipping_total,
                SUM( ec_order.discount_total ) AS discount_total, 
                SUM( ec_order.refund_total ) AS refund_total,
                DATE_FORMAT( " . $date_diff_func . "( ec_order.order_date, INTERVAL " . $date_diff . " HOUR ), '%m/%d/%Y' ) as date, 
                HOUR( " . $date_diff_func . "( ec_order.order_date, INTERVAL " . $date_diff . " HOUR ) ) AS order_hour, 
                DAY( " . $date_diff_func . "( ec_order.order_date, INTERVAL " . $date_diff . " HOUR ) ) AS order_day, 
                WEEK( " . $date_diff_func . "( ec_order.order_date, INTERVAL " . $date_diff . " HOUR ) ) AS order_week, 
                MONTH( " . $date_diff_func . "( ec_order.order_date, INTERVAL " . $date_diff . " HOUR ) ) AS order_month, 
                YEAR( " . $date_diff_func . "( ec_order.order_date, INTERVAL " . $date_diff . " HOUR ) ) AS order_year 
            FROM 
                ec_order
                LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id 
            WHERE 
                " . $date_diff_func . "( ec_order.order_date, INTERVAL " . $date_diff . " HOUR ) >= '" . $start_date . "' AND 
                " . $date_diff_func . "( ec_order.order_date, INTERVAL " . $date_diff . " HOUR ) <= '" . $end_date . " 23:59:59' AND 
                ec_orderstatus.is_approved = 1 " . $country_where . " " . $billing_country_where . "
            GROUP BY 
                " . $groupby . " 
            ORDER BY 
                ec_order.order_date ASC" 
        );
		
		$sales = array( );
		for( $i=0; $i<=$days_length; $i++ ){
			$found = false;
			if( $range == 'hourly' ){
                $test_day = date( 'm/d/Y H:m', strtotime( '+' . $i . ' hours', strtotime( $start_date ) ) );
            }else if( $range == 'daily' ){
                $test_day = date( 'm/d/Y', strtotime( '+' . $i . ' days', strtotime( $start_date ) ) );
            }else if( $range == 'weekly' ){
                $test_day = date( 'm/d/Y', strtotime( 'sunday last week', strtotime( '+' . ($i*7) . ' day', strtotime( $start_date ) ) ) );
            }else if( $range == 'monthly' ){
                $test_day = date( 'm/d/Y',  strtotime( 'first day of this month', strtotime( '+' . $i . ' month', strtotime( $start_date ) ) ) );
            }else if( $range == 'yearly' ){
                $test_day = date( 'm/d/Y', strtotime( '+' . $i . ' year', strtotime( $start_date ) ) );
            }
            for( $j=0; $j<count( $sales_data ) && !$found; $j++ ){
				if( $range == 'hourly' && $test_day == date( 'm/d/Y H:m', strtotime( $sales_data[$j]->date . ' ' . $sales_data[$j]->order_hour . ':00' ) ) ){
                    $sales_data[$j]->date = date( 'm/d/Y H:m', strtotime( $sales_data[$j]->date . ' ' . $sales_data[$j]->order_hour . ':00' ) );
					$sales[] = $sales_data[$j];
					$found = true;
				}else if( $range == 'weekly' && $test_day == date( 'm/d/Y', strtotime( 'sunday last week', strtotime( $sales_data[$j]->date ) ) ) ){
                    $sales_data[$j]->date = date( 'm/d/Y', strtotime( 'sunday last week', $sales_data[$j]->date ) );
					$sales[] = $sales_data[$j];
					$found = true;
				}else if( $range == 'monthly' && $test_day == date( 'm/d/Y', strtotime( 'first day of this month', strtotime( $sales_data[$j]->date ) ) ) ){
                    $sales_data[$j]->date = date( 'm/d/Y', strtotime( 'first day of this month', strtotime( $sales_data[$j]->date ) ) );
					$sales[] = $sales_data[$j];
					$found = true;
				}else if( $range == 'yearly' && $test_day == date( 'm/d/Y', strtotime( 'first day of january', strtotime( $sales_data[$j]->date ) ) ) ){
                    $sales_data[$j]->date = date( 'm/d/Y', strtotime( 'first day of january', strtotime( $sales_data[$j]->date ) ) );
					$sales[] = $sales_data[$j];
					$found = true;
				}else if( $test_day == $sales_data[$j]->date ){
					$sales[] = $sales_data[$j];
					$found = true;
				}
			}
			if( !$found ){
                if( $range == 'hourly' ){
                    $sales[] = (object) array(
                        'total'	=> 0,
                        'discount_total' => 0,
                        'refund_total' => 0,
						'shipping_total' => 0,
						'tax_total' => 0,
						'vat_total' => 0,
						'gst_total' => 0,
						'hst_total' => 0,
						'pst_total' => 0,
                        'date' => date( 'm/d/Y H:m', strtotime( '+' . $i . ' hours', strtotime( $start_date ) ) ),
                        'order_day'	=> date( 'd', strtotime( '+' . $i . ' hours', strtotime( $start_date ) ) ),
                        'order_month' => date( 'm', strtotime( '+' . $i . ' hours', strtotime( $start_date ) ) )
                    );
                }else if( $range == 'daily' ){
                    $sales[] = (object) array(
                        'total'	=> 0,
                        'discount_total' => 0,
                        'refund_total' => 0,
						'shipping_total' => 0,
						'tax_total' => 0,
						'vat_total' => 0,
						'gst_total' => 0,
						'hst_total' => 0,
						'pst_total' => 0,
                        'date' => date( 'm/d/Y', strtotime( '+' . $i . ' days', strtotime( $start_date ) ) ),
                        'order_day'	=> date( 'd', strtotime( '+' . $i . ' days', strtotime( $start_date ) ) ),
                        'order_month' => date( 'm', strtotime( '+' . $i . ' days', strtotime( $start_date ) ) )
                    );
                }else if( $range == 'weekly' ){
                    $sales[] = (object) array(
                        'total'	=> 0,
                        'discount_total' => 0,
                        'refund_total' => 0,
						'shipping_total' => 0,
						'tax_total' => 0,
						'vat_total' => 0,
						'gst_total' => 0,
						'hst_total' => 0,
						'pst_total' => 0,
                        'date' => date( 'm/d/Y', strtotime( '+' . ($i*7) . ' days', strtotime( $start_date ) ) ),
                        'order_day'	=> date( 'd', strtotime( '+' . ($i*7) . ' days', strtotime( $start_date ) ) ),
                        'order_month' => date( 'm', strtotime( '+' . ($i*7) . ' days', strtotime( $start_date ) ) )
                    );
                }else if( $range == 'monthly' ){
                    $sales[] = (object) array(
                        'total'	=> 0,
                        'discount_total' => 0,
                        'refund_total' => 0,
						'shipping_total' => 0,
						'tax_total' => 0,
						'vat_total' => 0,
						'gst_total' => 0,
						'hst_total' => 0,
						'pst_total' => 0,
                        'date' => date( 'm/d/Y', strtotime( '+' . $i . ' months', strtotime( $start_date ) ) ),
                        'order_day'	=> date( 'd', strtotime( '+' . $i . ' months', strtotime( $start_date ) ) ),
                        'order_month' => date( 'm', strtotime( '+' . $i . ' months', strtotime( $start_date ) ) )
                    );
                }else if( $range == 'yearly' ){
                    $sales[] = (object) array(
                        'total'	=> 0,
                        'discount_total' => 0,
                        'refund_total' => 0,
						'shipping_total' => 0,
						'tax_total' => 0,
						'vat_total' => 0,
						'gst_total' => 0,
						'hst_total' => 0,
						'pst_total' => 0,
                        'date' => date( 'm/d/Y', strtotime( '+' . $i . ' years', strtotime( $start_date ) ) ),
                        'order_day'	=> date( 'd', strtotime( '+' . $i . ' years', strtotime( $start_date ) ) ),
                        'order_month' => date( 'm', strtotime( '+' . $i . ' years', strtotime( $start_date ) ) )
                    );
                }
			}
		}
		
		$data_items = array( );
		for( $i=0; $i<count( $sales ); $i++ ){
            if( $product_id ){
                $data_items[] =  $sales[$i]->item_total;
            }else{
                $data_items[] = ( $sales[$i]->total + $sales[$i]->shipping_total + $sales[$i]->tax_total + $sales[$i]->vat_total + $sales[$i]->gst_total + $sales[$i]->hst_total + $sales[$i]->pst_total - $sales[$i]->discount_total - $sales[$i]->refund_total );
            }
		}
		
		return $data_items;
	}
	
	private function get_items_dataset( $start_date, $end_date, $range = 'daily', $product_id = false, $country = false, $billing_country = false ){
		$days_length = $this->get_sales_dataset_length( $start_date, $end_date, $range );
        if( $range == 'daily' && $days_length <= 2 ){
            $days_length = ($days_length+1) * 24;
            $range = 'hourly';
        }
		
		$product_where = "";
        $country_where = "";
        $billing_country_where = "";
		if( $product_id ){
			$product_where = $this->wpdb->prepare( "AND ec_orderdetail.product_id = %d ", $product_id );
		}
        if( $country ){
			$country_where = $this->wpdb->prepare( "AND ec_order.shipping_country = %s ", $country );
        }
        if( $billing_country ){
			$billing_country_where = $this->wpdb->prepare( "AND ec_order.billing_country = %s ", $billing_country );
        }
		
		if( $range == 'hourly' ){
            $groupby = 'order_hour, order_day';
        }else if( $range == 'daily' ){
            $groupby = 'order_day, order_month';
        }else if( $range == 'weekly' ){
            $groupby = 'order_week, order_year';
        }else if( $range == 'monthly' ){
            $groupby = 'order_month, order_year';
        }else if( $range == 'yearly' ){
            $groupby = 'order_year';
        }
        
        $date_diff = $this->date_diff;
        $date_diff_func = 'DATE_ADD';
        if( $date_diff < 0 ){
            $date_diff = $date_diff * -1;
            $date_diff_func = 'DATE_SUB';
        }
		
		$sales_data = $this->wpdb->get_results( "SELECT 
                SUM( 
					IFNULL( ( SELECT SUM( ec_orderdetail.quantity ) FROM ec_orderdetail WHERE ec_orderdetail.order_id = ec_order.order_id " . $product_where . " " . $country_where . " " . $billing_country_where . "), 0 ) 
				) as total,
				0 AS discount_total, 
                0 AS refund_total, 
                DATE_FORMAT( " . $date_diff_func . "( ec_order.order_date, INTERVAL " . $date_diff . " HOUR ), '%m/%d/%Y' ) as date, 
                HOUR( " . $date_diff_func . "( ec_order.order_date, INTERVAL " . $date_diff . " HOUR ) ) AS order_hour, 
                DAY( " . $date_diff_func . "( ec_order.order_date, INTERVAL " . $date_diff . " HOUR ) ) AS order_day, 
                WEEK( " . $date_diff_func . "( ec_order.order_date, INTERVAL " . $date_diff . " HOUR ) ) AS order_week, 
                MONTH( " . $date_diff_func . "( ec_order.order_date, INTERVAL " . $date_diff . " HOUR ) ) AS order_month, 
                YEAR( " . $date_diff_func . "( ec_order.order_date, INTERVAL " . $date_diff . " HOUR ) ) AS order_year 
            FROM 
                ec_orderdetail 
                LEFT JOIN ec_order ON ec_order.order_id = ec_orderdetail.order_id 
                LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id 
            WHERE 
                " . $date_diff_func . "( ec_order.order_date, INTERVAL " . $date_diff . " HOUR ) >= '" . $start_date . "' AND 
                " . $date_diff_func . "( ec_order.order_date, INTERVAL " . $date_diff . " HOUR ) <= '" . $end_date . " 23:59:59' AND 
                ec_orderstatus.is_approved = 1 
                " . $product_where . "
            GROUP BY 
                " . $groupby . " 
            ORDER BY 
                ec_order.order_date DESC"
        );
		
		$sales = array( );
		for( $i=0; $i<=$days_length; $i++ ){
			$found = false;
			if( $range == 'hourly' ){
                $test_day = date( 'm/d/Y H:m', strtotime( '+' . $i . ' hours', strtotime( $start_date ) ) );
            }else if( $range == 'daily' ){
                $test_day = date( 'm/d/Y', strtotime( '+' . $i . ' days', strtotime( $start_date ) ) );
            }else if( $range == 'weekly' ){
                $test_day = date( 'm/d/Y', strtotime( 'sunday last week', strtotime( '+' . ($i*7) . ' day', strtotime( $start_date ) ) ) );
            }else if( $range == 'monthly' ){
                $test_day = date( 'm/d/Y',  strtotime( 'first day of this month', strtotime( '+' . $i . ' month', strtotime( $start_date ) ) ) );
            }else if( $range == 'yearly' ){
                $test_day = date( 'm/d/Y', strtotime( '+' . $i . ' year', strtotime( $start_date ) ) );
            }
            for( $j=0; $j<count( $sales_data ) && !$found; $j++ ){
				if( $range == 'hourly' && $test_day == date( 'm/d/Y H:m', strtotime( $sales_data[$j]->date . ' ' . $sales_data[$j]->order_hour . ':00' ) ) ){
                    $sales_data[$j]->date = date( 'm/d/Y H:m', strtotime( $sales_data[$j]->date . ' ' . $sales_data[$j]->order_hour . ':00' ) );
					$sales[] = $sales_data[$j];
					$found = true;
				}else if( $range == 'weekly' && $test_day == date( 'm/d/Y', strtotime( 'sunday last week', strtotime( $sales_data[$j]->date ) ) ) ){
                    $sales_data[$j]->date = date( 'm/d/Y', strtotime( 'sunday last week', $sales_data[$j]->date ) );
					$sales[] = $sales_data[$j];
					$found = true;
				}else if( $range == 'monthly' && $test_day == date( 'm/d/Y', strtotime( 'first day of this month', strtotime( $sales_data[$j]->date ) ) ) ){
                    $sales_data[$j]->date = date( 'm/d/Y', strtotime( 'first day of this month', strtotime( $sales_data[$j]->date ) ) );
					$sales[] = $sales_data[$j];
					$found = true;
				}else if( $range == 'yearly' && $test_day == date( 'm/d/Y', strtotime( 'first day of january', strtotime( $sales_data[$j]->date ) ) ) ){
                    $sales_data[$j]->date = date( 'm/d/Y', strtotime( 'first day of january', strtotime( $sales_data[$j]->date ) ) );
					$sales[] = $sales_data[$j];
					$found = true;
				}else if( $test_day == $sales_data[$j]->date ){
					$sales[] = $sales_data[$j];
					$found = true;
				}
			}
			if( !$found ){
                if( $range == 'hourly' ){
                    $sales[] = (object) array(
                        'total'	=> 0,
                        'discount_total' => 0,
                        'refund_total' => 0,
                        'date' => date( 'm/d/Y H:m', strtotime( '+' . $i . ' hours', strtotime( $start_date ) ) ),
                        'order_day'	=> date( 'd', strtotime( '+' . $i . ' hours', strtotime( $start_date ) ) ),
                        'order_month' => date( 'm', strtotime( '+' . $i . ' hours', strtotime( $start_date ) ) )
                    );
                }else if( $range == 'daily' ){
                    $sales[] = (object) array(
                        'total'	=> 0,
                        'discount_total' => 0,
                        'refund_total' => 0,
                        'date' => date( 'm/d/Y', strtotime( '+' . $i . ' days', strtotime( $start_date ) ) ),
                        'order_day'	=> date( 'd', strtotime( '+' . $i . ' days', strtotime( $start_date ) ) ),
                        'order_month' => date( 'm', strtotime( '+' . $i . ' days', strtotime( $start_date ) ) )
                    );
                }else if( $range == 'weekly' ){
                    $sales[] = (object) array(
                        'total'	=> 0,
                        'discount_total' => 0,
                        'refund_total' => 0,
                        'date' => date( 'm/d/Y', strtotime( '+' . ($i*7) . ' days', strtotime( $start_date ) ) ),
                        'order_day'	=> date( 'd', strtotime( '+' . ($i*7) . ' days', strtotime( $start_date ) ) ),
                        'order_month' => date( 'm', strtotime( '+' . ($i*7) . ' days', strtotime( $start_date ) ) )
                    );
                }else if( $range == 'monthly' ){
                    $sales[] = (object) array(
                        'total'	=> 0,
                        'discount_total' => 0,
                        'refund_total' => 0,
                        'date' => date( 'm/d/Y', strtotime( '+' . $i . ' months', strtotime( $start_date ) ) ),
                        'order_day'	=> date( 'd', strtotime( '+' . $i . ' months', strtotime( $start_date ) ) ),
                        'order_month' => date( 'm', strtotime( '+' . $i . ' months', strtotime( $start_date ) ) )
                    );
                }else if( $range == 'yearly' ){
                    $sales[] = (object) array(
                        'total'	=> 0,
                        'discount_total' => 0,
                        'refund_total' => 0,
                        'date' => date( 'm/d/Y', strtotime( '+' . $i . ' years', strtotime( $start_date ) ) ),
                        'order_day'	=> date( 'd', strtotime( '+' . $i . ' years', strtotime( $start_date ) ) ),
                        'order_month' => date( 'm', strtotime( '+' . $i . ' years', strtotime( $start_date ) ) )
                    );
                }
			}
		}
		
		$data_items = array( );
		for( $i=0; $i<count( $sales ); $i++ ){
			$data_items[] = ( $sales[$i]->total - $sales[$i]->discount_total - $sales[$i]->refund_total );
		}
		
		return $data_items;
	}
	
	private function get_carts_dataset( $start_date, $end_date, $range = 'daily', $product_id = false, $country = false, $billing_country = false ){
		$days_length = $this->get_sales_dataset_length( $start_date, $end_date, $range );
        if( $range == 'daily' && $days_length <= 2 ){
            $days_length = ($days_length+1) * 24;
            $range = 'hourly';
        }
		
        $product_where = "";
        $country_where = "";
        $billing_country_where = "";
		if( $product_id ){
			$product_where = $this->wpdb->prepare( "AND ec_tempcart.product_id = %d ", $product_id );
		}
        if( $country ){
			$country_where = $this->wpdb->prepare( "AND ec_tempcart_data.shipping_country = %s ", $country );
        }
        if( $billing_country ){
			$billing_country_where = $this->wpdb->prepare( "AND ec_tempcart_data.billing_country = %s ", $billing_country );
        }
        
        if( $range == 'hourly' ){
            $groupby = 'order_hour, order_day';
        }else if( $range == 'daily' ){
            $groupby = 'order_day, order_month';
        }else if( $range == 'weekly' ){
            $groupby = 'order_week, order_year';
        }else if( $range == 'monthly' ){
            $groupby = 'order_month, order_year';
        }else if( $range == 'yearly' ){
            $groupby = 'order_year';
        }
        
        $date_diff = $this->date_diff;
        $date_diff_func = 'DATE_ADD';
        if( $date_diff < 0 ){
            $date_diff = $date_diff * -1;
            $date_diff_func = 'DATE_SUB';
        }
		    
		$sales_data = $this->wpdb->get_results( "SELECT 
                COUNT( ec_tempcart.session_id ) as total, 
                0 AS discount_total, 
                0 AS refund_total, 
                DATE_FORMAT( ec_tempcart.last_changed_date, '%m/%d/%Y') as date, 
                HOUR( " . $date_diff_func . "( ec_tempcart.last_changed_date, INTERVAL " . $date_diff . " HOUR ) ) AS order_hour, 
                DAY( " . $date_diff_func . "( ec_tempcart.last_changed_date, INTERVAL " . $date_diff . " HOUR ) ) AS order_day, 
                WEEK( " . $date_diff_func . "( ec_tempcart.last_changed_date, INTERVAL " . $date_diff . " HOUR ) ) AS order_week, 
                MONTH( " . $date_diff_func . "( ec_tempcart.last_changed_date, INTERVAL " . $date_diff . " HOUR ) ) AS order_month, 
                YEAR( " . $date_diff_func . "( ec_tempcart.last_changed_date, INTERVAL " . $date_diff . " HOUR ) ) AS order_year 
            FROM 
                ec_tempcart 
                LEFT JOIN ec_tempcart_data ON ( ec_tempcart_data.session_id = ec_tempcart.session_id )
            WHERE 
                " . $date_diff_func . "( ec_tempcart.last_changed_date, INTERVAL " . $date_diff . " HOUR ) >= '" . $start_date . "' AND 
                " . $date_diff_func . "( ec_tempcart.last_changed_date, INTERVAL " . $date_diff . " HOUR ) <= '" . $end_date . " 23:59:59' 
                " . $product_where . "
                " . $country_where . " 
                " . $billing_country_where . "
            GROUP BY 
                " . $groupby . " 
            ORDER BY 
                ec_tempcart.last_changed_date DESC"
        );
		
		$sales = array( );
		for( $i=0; $i<=$days_length; $i++ ){
			$found = false;
			if( $range == 'hourly' ){
                $test_day = date( 'm/d/Y H:m', strtotime( '+' . $i . ' hours', strtotime( $start_date ) ) );
            }else if( $range == 'daily' ){
                $test_day = date( 'm/d/Y', strtotime( '+' . $i . ' days', strtotime( $start_date ) ) );
            }else if( $range == 'weekly' ){
                $test_day = date( 'm/d/Y', strtotime( 'sunday last week', strtotime( '+' . ($i*7) . ' day', strtotime( $start_date ) ) ) );
            }else if( $range == 'monthly' ){
                $test_day = date( 'm/d/Y',  strtotime( 'first day of this month', strtotime( '+' . $i . ' month', strtotime( $start_date ) ) ) );
            }else if( $range == 'yearly' ){
                $test_day = date( 'm/d/Y', strtotime( '+' . $i . ' year', strtotime( $start_date ) ) );
            }
            for( $j=0; $j<count( $sales_data ) && !$found; $j++ ){
				if( $range == 'hourly' && $test_day == date( 'm/d/Y H:m', strtotime( $sales_data[$j]->date . ' ' . $sales_data[$j]->order_hour . ':00' ) ) ){
                    $sales_data[$j]->date = date( 'm/d/Y H:m', strtotime( $sales_data[$j]->date . ' ' . $sales_data[$j]->order_hour . ':00' ) );
					$sales[] = $sales_data[$j];
					$found = true;
				}else if( $range == 'weekly' && $test_day == date( 'm/d/Y', strtotime( 'sunday last week', strtotime( $sales_data[$j]->date ) ) ) ){
                    $sales_data[$j]->date = date( 'm/d/Y', strtotime( 'sunday last week', $sales_data[$j]->date ) );
					$sales[] = $sales_data[$j];
					$found = true;
				}else if( $range == 'monthly' && $test_day == date( 'm/d/Y', strtotime( 'first day of this month', strtotime( $sales_data[$j]->date ) ) ) ){
                    $sales_data[$j]->date = date( 'm/d/Y', strtotime( 'first day of this month', strtotime( $sales_data[$j]->date ) ) );
					$sales[] = $sales_data[$j];
					$found = true;
				}else if( $range == 'yearly' && $test_day == date( 'm/d/Y', strtotime( 'first day of january', strtotime( $sales_data[$j]->date ) ) ) ){
                    $sales_data[$j]->date = date( 'm/d/Y', strtotime( 'first day of january', strtotime( $sales_data[$j]->date ) ) );
					$sales[] = $sales_data[$j];
					$found = true;
				}else if( $test_day == $sales_data[$j]->date ){
					$sales[] = $sales_data[$j];
					$found = true;
				}
			}
			if( !$found ){
                if( $range == 'hourly' ){
                    $sales[] = (object) array(
                        'total'	=> 0,
                        'discount_total' => 0,
                        'refund_total' => 0,
                        'date' => date( 'm/d/Y H:m', strtotime( '+' . $i . ' hours', strtotime( $start_date ) ) ),
                        'order_day'	=> date( 'd', strtotime( '+' . $i . ' hours', strtotime( $start_date ) ) ),
                        'order_month' => date( 'm', strtotime( '+' . $i . ' hours', strtotime( $start_date ) ) )
                    );
                }else if( $range == 'daily' ){
                    $sales[] = (object) array(
                        'total'	=> 0,
                        'discount_total' => 0,
                        'refund_total' => 0,
                        'date' => date( 'm/d/Y', strtotime( '+' . $i . ' days', strtotime( $start_date ) ) ),
                        'order_day'	=> date( 'd', strtotime( '+' . $i . ' days', strtotime( $start_date ) ) ),
                        'order_month' => date( 'm', strtotime( '+' . $i . ' days', strtotime( $start_date ) ) )
                    );
                }else if( $range == 'weekly' ){
                    $sales[] = (object) array(
                        'total'	=> 0,
                        'discount_total' => 0,
                        'refund_total' => 0,
                        'date' => date( 'm/d/Y', strtotime( '+' . ($i*7) . ' days', strtotime( $start_date ) ) ),
                        'order_day'	=> date( 'd', strtotime( '+' . ($i*7) . ' days', strtotime( $start_date ) ) ),
                        'order_month' => date( 'm', strtotime( '+' . ($i*7) . ' days', strtotime( $start_date ) ) )
                    );
                }else if( $range == 'monthly' ){
                    $sales[] = (object) array(
                        'total'	=> 0,
                        'discount_total' => 0,
                        'refund_total' => 0,
                        'date' => date( 'm/d/Y', strtotime( '+' . $i . ' months', strtotime( $start_date ) ) ),
                        'order_day'	=> date( 'd', strtotime( '+' . $i . ' months', strtotime( $start_date ) ) ),
                        'order_month' => date( 'm', strtotime( '+' . $i . ' months', strtotime( $start_date ) ) )
                    );
                }else if( $range == 'yearly' ){
                    $sales[] = (object) array(
                        'total'	=> 0,
                        'discount_total' => 0,
                        'refund_total' => 0,
                        'date' => date( 'm/d/Y', strtotime( '+' . $i . ' years', strtotime( $start_date ) ) ),
                        'order_day'	=> date( 'd', strtotime( '+' . $i . ' years', strtotime( $start_date ) ) ),
                        'order_month' => date( 'm', strtotime( '+' . $i . ' years', strtotime( $start_date ) ) )
                    );
                }
			}
		}
		
		$data_items = array( );
		for( $i=0; $i<count( $sales ); $i++ ){
			$data_items[] = ( $sales[$i]->total - $sales[$i]->discount_total - $sales[$i]->refund_total );
		}
		
		return $data_items;
	}
	
	private function get_sales_dataset_length( $start_date, $end_date, $range = 'daily' ){
		$diff = strtotime( $end_date ) - strtotime( $start_date );
		if( $range == 'daily' )
            return round( $diff / ( 60 * 60 * 24 ) );
        else if( $range == 'weekly' )
            return ceil( $diff / ( 60 * 60 * 24 * 7 ) ) - 1;
        else if( $range == 'monthly' )
            return ( (int) date( 'm', strtotime( $end_date ) ) - (int) date( 'm', strtotime( $start_date ) ) ) + ( 12 * ( (int) date( 'Y', strtotime( $end_date ) ) - (int) date( 'Y', strtotime( $start_date ) ) ) );
        else if( $range == 'yearly' )
            return (int) date( 'Y', strtotime( $end_date ) ) - (int) date( 'Y', strtotime( $start_date ) );
	}
	
	function hex2rgba( $color, $opacity = false ){
		$default = 'rgba( 0, 0, 0, .7 )';
		if( empty( $color ) ){
			return $default; 
		}
		
		if( $color[0] == '#' ){
			$color = substr( $color, 1 );
		}

		if( strlen( $color ) == 6 ){
			$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		}else if( strlen( $color ) == 3 ){
			$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		}else{
			return $default;
		}

		$rgb =  array_map( 'hexdec', $hex );

		if( $opacity ){
			if( abs( $opacity ) > 1 ){
				$opacity = 1.0;
			}
			$output = 'rgba(' . implode( ",", $rgb ) . ',' . $opacity . ')';
		}else{
			$output = 'rgb(' . implode( ",", $rgb ) . ')';
		}

		return $output;
	}
    
    public function get_order_report( $start_date, $end_date, $product_id = false, $country = false, $billing_country = false ){
        $product_where = "";
        $country_where = "";
        $billing_country_where = "";
        if( $product_id ){
            $product_where = $this->wpdb->prepare( "AND ec_orderdetail.product_id = %d ", $product_id );
        }
        if( $country ){
            $country_where = $this->wpdb->prepare( "AND ec_order.shipping_country = %s ", $country );
        }
        if( $billing_country ){
			$billing_country_where = $this->wpdb->prepare( "AND ec_order.billing_country = %s ", $billing_country );
        }

        $date_diff = $this->date_diff;
        $date_diff_func = 'DATE_ADD';
        if( $date_diff < 0 ){
            $date_diff = $date_diff * -1;
            $date_diff_func = 'DATE_SUB';
        }

        $sales_data = $this->wpdb->get_results( "SELECT 
                ec_order.order_date,
                ec_orderstatus.order_status,
                ec_orderdetail.orderdetail_id,
                ec_orderdetail.order_id,
                ec_order.payment_method,
                ec_order.sub_total,
                ec_order.tip_total,
                ec_order.tax_total,
                ec_order.refund_total,
                ec_order.shipping_total,
                ec_order.discount_total,
                ec_order.vat_total,
                ec_order.vat_rate,
                ec_order.duty_total,
                ec_order.gst_total,
                ec_order.gst_rate,
                ec_order.pst_total,
                ec_order.pst_rate,
                ec_order.hst_total,
                ec_order.hst_rate,
                ec_order.grand_total,
                ec_order.user_id,
                ec_order.use_expedited_shipping,
                ec_order.shipping_method,
                ec_order.shipping_carrier,
                ec_order.shipping_service_code,
                ec_order.tracking_number,
                ec_order.giftcard_id as gift_card_used,
                ec_order.promo_code as promo_code_used,
                ec_orderdetail.product_id,
                ec_orderdetail.title,
                ec_orderdetail.model_number,
                ec_orderdetail.unit_price,
                ec_orderdetail.total_price,
                ec_orderdetail.quantity,
                ec_orderdetail.optionitem_name_1,
                ec_orderdetail.optionitem_name_2,
                ec_orderdetail.optionitem_name_3,
                ec_orderdetail.optionitem_name_4,
                ec_orderdetail.optionitem_name_5,
                ec_order.order_notes,
                ec_order.order_customer_notes,
                ec_order.user_email,
                ec_order.user_level,
                ec_order.billing_first_name,
                ec_order.billing_last_name,
                ec_order.billing_company_name,
                ec_order.billing_address_line_1,
                ec_order.billing_address_line_2,
                ec_order.billing_city,
                ec_order.billing_state,
                ec_order.billing_zip,
                ec_order.billing_country,
                billing_country.name_cnt as billing_country_name, 
                ec_order.billing_phone,
                ec_order.shipping_first_name,
                ec_order.shipping_last_name,
                ec_order.shipping_company_name,
                ec_order.shipping_address_line_1,
                ec_order.shipping_address_line_2,
                ec_order.shipping_city,
                ec_order.shipping_state,
                ec_order.shipping_zip,
                ec_order.shipping_country,
                shipping_country.name_cnt as shipping_country_name,
                ec_order.shipping_phone,
                ec_order.vat_registration_number,
                ec_order.agreed_to_terms,
                ec_order.order_ip_address,
                ec_orderdetail.use_advanced_optionset,
                ec_orderdetail.giftcard_id,
                ec_orderdetail.shipper_id,
                ec_orderdetail.shipper_first_name,
                ec_orderdetail.shipper_last_name,
                ec_orderdetail.gift_card_message,
                ec_orderdetail.gift_card_from_name,
                ec_orderdetail.gift_card_to_name,
                ec_orderdetail.gift_card_email,
                ec_orderdetail.download_file_name,
                ec_orderdetail.download_key,
                ec_orderdetail.deconetwork_id,
                ec_orderdetail.deconetwork_name,
                ec_orderdetail.deconetwork_product_code,
                ec_orderdetail.deconetwork_options,
                ec_orderdetail.deconetwork_color_code,
                ec_orderdetail.deconetwork_product_id,
                ec_orderdetail.deconetwork_image_link,
                ec_orderdetail.subscription_signup_fee,
                ec_order.order_weight,
                ec_order.order_gateway,
                ec_order.card_holder_name,
                ec_order.creditcard_digits,
                ec_order.cc_exp_month,
                ec_order.cc_exp_year,
                ec_order.subscription_id,
                ec_order.stripe_charge_id,
                ec_order.nets_transaction_id,
                ec_order.gateway_transaction_id,
                ec_order.paypal_email_id,
                ec_order.paypal_transaction_id,
                ec_order.paypal_payer_id,
                ec_order.fraktjakt_order_id,
                ec_order.fraktjakt_shipment_id,
                ec_response.response_text as gateway_response
            FROM 
                ec_order 
                LEFT OUTER JOIN ec_orderdetail ON ec_order.order_id = ec_orderdetail.order_id
                LEFT JOIN ec_country as billing_country ON billing_country.iso2_cnt = ec_order.billing_country 
                LEFT JOIN ec_country as shipping_country ON shipping_country.iso2_cnt = ec_order.shipping_country 
                LEFT JOIN ec_orderstatus ON ec_orderstatus.status_id = ec_order.orderstatus_id
                LEFT JOIN ec_response ON ec_response.order_id = ec_order.order_id 
            WHERE 
                " . $date_diff_func . "( ec_order.order_date, INTERVAL " . $date_diff . " HOUR ) >= '" . $start_date . "' AND 
                " . $date_diff_func . "( ec_order.order_date, INTERVAL " . $date_diff . " HOUR ) <= '" . $end_date . " 23:59:59'
                " . $product_where . "
                " . $country_where . "
                " . $billing_country_where . "
            ORDER BY 
                ec_order.order_date ASC, ec_orderdetail.order_id, ec_orderdetail.orderdetail_id", ARRAY_A
        );
		
		$url_link = '';
		
        $keys = ( count( $sales_data ) > 0 ) ? array_keys( $sales_data[0] ) : array( 'order_date','order_status', 'orderdetail_id', 'order_id', 'payment_method', 'sub_total', 'tip_total', 'tax_total', 'refund_total', 'shipping_total', 'discount_total', 'vat_total', 'vat_rate', 'duty_total', 'gst_total', 'gst_rate', 'pst_total', 'pst_rate', 'hst_total', 'hst_rate', 'grand_total', 'user_id', 'use_expedited_shipping', 'shipping_method', 'shipping_carrier', 'shipping_service_code', 'tracking_number', 'gift_card_used', 'promo_code_used', 'product_id', 'title', 'model_number', 'unit_price', 'total_price', 'quantity', 'optionitem_name_1', 'optionitem_name_2', 'optionitem_name_3', 'optionitem_name_4', 'optionitem_name_5', 'order_notes', 'order_customer_notes', 'user_email', 'user_level', 'billing_first_name', 'billing_last_name', 'billing_company_name', 'billing_address_line_1', 'billing_address_line_2', 'billing_city', 'billing_state', 'billing_zip', 'billing_country', 'billing_country_name', 'billing_phone', 'shipping_first_name', 'shipping_last_name', 'shipping_company_name', 'shipping_address_line_1', 'shipping_address_line_2', 'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_country', 'shipping_country_name', 'shipping_phone', 'vat_registration_number', 'agreed_to_terms', 'order_ip_address', 'use_advanced_optionset', 'giftcard_id', 'shipper_id', 'shipper_first_name', 'shipper_last_name', 'gift_card_message', 'gift_card_from_name', 'gift_card_to_name', 'gift_card_email', 'download_file_name', 'download_key', 'deconetwork_id', 'deconetwork_name', 'deconetwork_product_code', 'deconetwork_options', 'deconetwork_color_code', 'deconetwork_product_id', 'deconetwork_image_link', 'subscription_signup_fee', 'order_weight', 'order_gateway', 'card_holder_name', 'creditcard_digits', 'cc_exp_month', 'cc_exp_year', 'subscription_id', 'stripe_charge_id', 'nets_transaction_id', 'gateway_transaction_id', 'paypal_email_id', 'paypal_transaction_id', 'paypal_payer_id', 'fraktjakt_order_id', 'fraktjakt_shipment_id', 'gateway_response' );

        $single_use_key_names = apply_filters( 
            'wp_easycart_order_export_single_keys', 
            array( 	
                "sub_total", "tip_total", "tax_total", "tax_total", "shipping_total", "discount_total", "vat_total", "refund_total",
                "vat_rate", "hst_total", "hst_rate", "pst_total", "pst_rate", "gst_total", "gst_rate", "grand_total",
                "order_date", "order_status", "payment_method", "shipping_method", "tracking_number", "promo_code_used",
                "order_customer_notes", "agreed_to_terms", "order_ip_address", "order_weight", 
                "order_gateway", "card_holder_name", "creditcard_digits", "cc_exp_month", "cc_exp_year", "stripe_charge_id", "order_notes", 
                "gateway_response"
            ) 
        );

        $upload_dir = wp_upload_dir( );
        $file_name = 'order-report-' . $start_date . '_' . $end_date . '-' . rand( 1000000, 999999999 ) . '.csv';
        $url_path = $upload_dir['path'] . '/' . $file_name;
        $url_link = $upload_dir['url'] . '/' . $file_name;
        $file = fopen( $url_path, "w" );

        fputcsv( $file, $keys );
        
        if( count( $sales_data ) > 0 ){
			$prev_order = 0;
			$is_new_order = false;
            foreach( $sales_data as $result ){
	
				if( $result['order_id'] != $prev_order ){
					$prev_order = $result['order_id'];
					$is_new_order = true;
				}

				if( $result['order_gateway'] == "authorize" ){
					$response_exploded = explode( ",", $result['gateway_response'] );
					if( count( $response_exploded ) > 3 ){
						 $result['gateway_response'] = $response_exploded[3];
					}
				}else if( $result['order_gateway'] == "paypal" ){
					preg_match_all( "/\[payment_status\] \=\> (.*)\n/", $result['gateway_response'], $output_array );
					if( count( $output_array ) > 1 ){
						 $result['gateway_response'] = $output_array[1][0];
					}
				}else if( $result['order_gateway'] == "stripe" ){
					preg_match_all( "/\[status\] \=\> (.*)\n/", $result['gateway_response'], $output_array );
					if( count( $output_array ) > 1 ){
						 $result['gateway_response'] = $output_array[1][0];
					}
				}

				$new_line = array( );

				foreach( $keys as $key ){

					if( $key == "advanced_product_options" ){
						$option_sql = "SELECT 
								ec_order_option.option_value 
							   FROM 
								ec_order_option 
							   WHERE 
								ec_order_option.orderdetail_id = %s 
							   ORDER BY 
								ec_order_option.order_option_id ASC";
						$option_results = $wpdb->get_results( $wpdb->prepare( $option_sql, $result['orderdetail_id'] ) );

						$optionlist = '';
						$first = true;
						foreach( $option_results as $option_row ){
							if( !$first )
								$optionlist .= ', ';
							$optionlist .= $option_row->option_value;
							$first = false;
						}
						$new_line[] = $optionlist;

					}else{

						$value = $result[$key];

						if( in_array( $key, $single_use_key_names ) && !$is_new_order ){
							$new_line[] = "0.00";

						}else if( !isset( $value ) || $value == "" ){
							$new_line[] = "";

						}else if( $key == 'billing_zip' || $key == 'shipping_zip' ){
							$new_line[] = "=\"" . $value . "\"";

						}else{
							$new_line[] = $value;

						}

					}

				}


				fputcsv( $file, $new_line );

				$is_new_order = false;

			}
			
			fclose( $file );
        }
        
        return $url_link;
    }
    
    public function get_single_stats( $start_date, $end_date, $start_date2 = false, $end_date2 = false, $product_id = false, $country = false, $billing_country = false ){
		
		$product_where = "";
		$product_where_cart = "";
        $country_where = "";
        $country_where_cart = "";
        $billing_country_where = "";
        $billing_country_where_cart = "";
        if( $product_id ){
			$product_where = $this->wpdb->prepare( "AND ec_orderdetail.product_id = %d ", $product_id );
			$product_where_cart = $this->wpdb->prepare( "AND ec_tempcart.product_id = %d ", $product_id );
		}
        if( $country ){
            $country_where = $this->wpdb->prepare( "AND ec_order.shipping_country = %s ", $country );
			$country_where_cart = $this->wpdb->prepare( "AND ec_tempcart_data.shipping_country = %s ", $country );
        }
        if( $billing_country ){
			$billing_country_where = $this->wpdb->prepare( "AND ec_order.billing_country = %s ", $billing_country );
			$billing_country_where_cart = $this->wpdb->prepare( "AND ec_tempcart_data.billing_country = %s ", $billing_country );
        }
		$date_diff = $this->date_diff;
		$date_diff_type = 'DATE_ADD';
		if( $this->date_diff < 0 ){
			$date_diff = $date_diff * -1;
			$date_diff_type = 'DATE_SUB';
		}
		
		$sales_data = $this->wpdb->get_row( "SELECT 
				COUNT( ec_order.order_id ) AS order_count,
				COUNT( ec_order.user_id ) AS customer_count,
				SUM( 
                    IFNULL( ( SELECT COUNT( ec_orderdetail.order_id ) FROM ec_orderdetail WHERE ec_orderdetail.order_id = ec_order.order_id " . $product_where . " " . $country_where . " " . $billing_country_where . " ), 0 )
                ) AS item_order_count,
				SUM( 
                    IFNULL( ( SELECT COUNT( ec_order_item.user_id ) FROM ec_orderdetail LEFT JOIN ec_order AS ec_order_item ON ec_order_item.order_id = ec_orderdetail.order_id WHERE ec_orderdetail.order_id = ec_order.order_id " . $product_where . " " . $country_where . " " . $billing_country_where . " ), 0 )
                ) AS item_customer_count,
				SUM( 
					IFNULL( ( SELECT SUM( ec_orderdetail.total_price ) FROM ec_orderdetail WHERE ec_orderdetail.order_id = ec_order.order_id " . $country_where . " " . $billing_country_where . "), 0 ) 
				) as total,
                SUM( 
					IFNULL( ( SELECT SUM( ec_orderdetail.total_price ) FROM ec_orderdetail WHERE ec_orderdetail.order_id = ec_order.order_id " . $product_where . " " . $country_where . " " . $billing_country_where . "), 0 ) 
				) as item_total,
				SUM( ec_order.discount_total ) AS discount_total, 
				SUM( ec_order.refund_total ) AS refund_total,  
				SUM( ec_order.shipping_total ) AS shipping_total, 
				SUM( ec_order.tax_total ) AS tax_total, 
				SUM( ec_order.vat_total ) AS vat_total, 
				SUM( ec_order.gst_total ) AS gst_total, 
				SUM( ec_order.pst_total ) AS pst_total, 
				SUM( ec_order.hst_total ) AS hst_total 
			FROM 
				ec_order
				LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id 
			WHERE 
				" . $date_diff_type . "( ec_order.order_date, INTERVAL " . $date_diff . " HOUR ) >= '" . $start_date . " 00:00:00' AND 
				" . $date_diff_type . "( ec_order.order_date, INTERVAL " . $date_diff . " HOUR ) <= '" . $end_date . " 23:59:59' AND 
				( ec_orderstatus.is_approved = 1 OR ec_orderstatus.status_id = 16 ) " . $country_where . " " . $billing_country_where
		);
		
		$sales_data_item = $this->wpdb->get_row( "SELECT 
				IFNULL( SUM( ec_orderdetail.quantity ), 0 ) as item_count
			FROM 
				ec_orderdetail 
				LEFT JOIN ec_order ON ec_order.order_id = ec_orderdetail.order_id 
				LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id 
			WHERE 
				" . $date_diff_type . "( ec_order.order_date, INTERVAL " . $date_diff . " HOUR ) >= '" . $start_date . " 00:00:00' AND 
				" . $date_diff_type . "( ec_order.order_date, INTERVAL " . $date_diff . " HOUR ) <= '" . $end_date . " 23:59:59' AND 
				( ec_orderstatus.is_approved = 1 OR ec_orderstatus.status_id = 16 ) " . $product_where . " " . $country_where . " " . $billing_country_where
		);
		
		$sales_data_cart = $this->wpdb->get_row( "SELECT 
				COUNT( ec_tempcart.session_id ) as cart_total
			FROM 
				ec_tempcart
                LEFT JOIN ec_tempcart_data ON ( ec_tempcart_data.session_id = ec_tempcart.session_id ) 
			WHERE 
				" . $date_diff_type . "( ec_tempcart.last_changed_date, INTERVAL " . $date_diff . " HOUR ) >= '" . $start_date . "' AND 
				" . $date_diff_type . "( ec_tempcart.last_changed_date, INTERVAL " . $date_diff . " HOUR ) <= '" . $end_date . " 23:59:59' " . $product_where_cart . " " . $country_where_cart . " " . $billing_country_where_cart
		);
		
		if( $start_date2 ){
			$sales_data2 = $this->wpdb->get_row( "SELECT 
					COUNT( ec_order.order_id ) AS order_count,
					COUNT( ec_order.user_id ) AS customer_count,
					SUM( 
                        IFNULL( ( SELECT COUNT( ec_orderdetail.order_id ) FROM ec_orderdetail WHERE ec_orderdetail.order_id = ec_order.order_id " . $product_where . " " . $country_where . " " . $billing_country_where . " ), 0 )
                    ) AS item_order_count,
                    SUM( 
                        IFNULL( ( SELECT COUNT( ec_order_item.user_id ) FROM ec_orderdetail LEFT JOIN ec_order AS ec_order_item ON ec_order_item.order_id = ec_orderdetail.order_id WHERE ec_orderdetail.order_id = ec_order.order_id " . $product_where . " " . $country_where . " " . $billing_country_where . " ), 0 )
                    ) AS item_customer_count,
                    SUM( 
						IFNULL( ( SELECT SUM( ec_orderdetail.total_price ) FROM ec_orderdetail WHERE ec_orderdetail.order_id = ec_order.order_id " . $country_where . " " . $billing_country_where . "), 0 ) 
					) as total,
					SUM( 
						IFNULL( ( SELECT SUM( ec_orderdetail.total_price ) FROM ec_orderdetail WHERE ec_orderdetail.order_id = ec_order.order_id " . $product_where . " " . $country_where . " " . $billing_country_where . "), 0 ) 
					) as item_total,
					SUM( ec_order.discount_total ) AS discount_total, 
					SUM( ec_order.refund_total ) AS refund_total,  
					SUM( ec_order.shipping_total ) AS shipping_total, 
					SUM( ec_order.tax_total ) AS tax_total, 
					SUM( ec_order.vat_total ) AS vat_total, 
					SUM( ec_order.gst_total ) AS gst_total, 
					SUM( ec_order.pst_total ) AS pst_total, 
					SUM( ec_order.hst_total ) AS hst_total 
				FROM 
					ec_order
					LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id 
				WHERE 
					" . $date_diff_type . "( ec_order.order_date, INTERVAL " . $date_diff . " HOUR ) >= '" . $start_date2 . "' AND 
					" . $date_diff_type . "( ec_order.order_date, INTERVAL " . $date_diff . " HOUR ) <= '" . $end_date2 . " 23:59:59' AND 
					( ec_orderstatus.is_approved = 1 OR ec_orderstatus.status_id = 16 ) " . $country_where . " " . $billing_country_where
			);
		
			$sales_data_item2 = $this->wpdb->get_row( "SELECT 
					IFNULL( SUM( ec_orderdetail.quantity ), 0 ) as item_count
				FROM 
					ec_orderdetail 
					LEFT JOIN ec_order ON ec_order.order_id = ec_orderdetail.order_id 
					LEFT JOIN ec_orderstatus ON ec_order.orderstatus_id = ec_orderstatus.status_id 
				WHERE 
					" . $date_diff_type . "( ec_order.order_date, INTERVAL " . $date_diff . " HOUR ) >= '" . $start_date2 . " 00:00:00' AND 
					" . $date_diff_type . "( ec_order.order_date, INTERVAL " . $date_diff . " HOUR ) <= '" . $end_date2 . " 23:59:59' AND 
					( ec_orderstatus.is_approved = 1 OR ec_orderstatus.status_id = 16 ) " . $product_where . " " . $country_where . " " . $billing_country_where
			);

			$sales_data_cart2 = $this->wpdb->get_row( "SELECT 
					COUNT( ec_tempcart.session_id ) as cart_total
				FROM 
					ec_tempcart 
                    LEFT JOIN ec_tempcart_data ON ( ec_tempcart_data.session_id = ec_tempcart.session_id )
				WHERE 
					" . $date_diff_type . "( ec_tempcart.last_changed_date, INTERVAL " . $date_diff . " HOUR ) >= '" . $start_date2 . "' AND 
					" . $date_diff_type . "( ec_tempcart.last_changed_date, INTERVAL " . $date_diff . " HOUR ) <= '" . $end_date2 . " 23:59:59' " . $product_where_cart . " " . $country_where_cart . " " . $billing_country_where_cart
			);
		}
		
		return (object) array(
            'gross_revenue' => (object) array(
				'set1' => ( ( $product_id ) ? $GLOBALS['currency']->get_currency_display( ( $sales_data ) ? $sales_data->item_total : 0 ) : $GLOBALS['currency']->get_currency_display( ( $sales_data ) ? $sales_data->total + $sales_data->shipping_total + $sales_data->tax_total + $sales_data->vat_total + $sales_data->gst_total + $sales_data->hst_total + $sales_data->pst_total - $sales_data->refund_total - $sales_data->discount_total : 0 ) ),
				'set2' => ( ( $product_id ) ? $GLOBALS['currency']->get_currency_display( ( $start_date2 && $sales_data2 ) ? $sales_data2->item_total : 0 ) : $GLOBALS['currency']->get_currency_display( ( $start_date2 && $sales_data2 ) ? $sales_data2->total + $sales_data2->shipping_total + $sales_data2->tax_total + $sales_data2->vat_total + $sales_data2->gst_total + $sales_data2->hst_total + $sales_data2->pst_total - $sales_data2->refund_total - $sales_data2->discount_total : 0 ) ),
				'diff' => ( ( $product_id ) ? ( ( !$start_date2 || !$sales_data2 || !$sales_data2->item_total ) ? '' : number_format( ( ( ( $sales_data ) ? $sales_data->item_total : 0 ) - ( ( $start_date2 && $sales_data2 ) ? $sales_data2->item_total : 0 ) ) / ( ( $start_date2 && $sales_data2 ) ? $sales_data2->item_total : 0 ) * 100, 2, '.', '' ) ) : ( ( !$start_date2 || !$sales_data2 || !$sales_data2->item_total ) ? '' : number_format( ( ( ( $sales_data ) ? $sales_data->total + $sales_data->shipping_total + $sales_data->tax_total + $sales_data->vat_total + $sales_data->gst_total + $sales_data->hst_total + $sales_data->pst_total - $sales_data->refund_total - $sales_data->discount_total : 0 ) - ( ( $start_date2 && $sales_data2 ) ? $sales_data2->total + $sales_data2->shipping_total + $sales_data2->tax_total + $sales_data2->vat_total + $sales_data2->gst_total + $sales_data2->hst_total + $sales_data2->pst_total - $sales_data2->refund_total - $sales_data2->discount_total : 0 ) ) / ( ( $start_date2 && $sales_data2 ) ? $sales_data2->total + $sales_data2->shipping_total + $sales_data2->tax_total + $sales_data2->vat_total + $sales_data2->gst_total + $sales_data2->hst_total + $sales_data2->pst_total - $sales_data2->refund_total - $sales_data2->discount_total : 0 ) * 100, 2, '.', '' ) ) )
			),
            'shipping' => (object) array(
				'set1' => ( $product_id ) ? $GLOBALS['currency']->get_currency_display( 0 ) : $GLOBALS['currency']->get_currency_display( ( $sales_data ) ? $sales_data->shipping_total : 0 ),
				'set2' => ( $product_id ) ? $GLOBALS['currency']->get_currency_display( 0 ) : $GLOBALS['currency']->get_currency_display( ( $start_date2 && $sales_data2 ) ? $sales_data2->shipping_total : 0 ),
				'diff' => ( $product_id ) ? '' : ( ( $start_date2 && $sales_data2 && $sales_data2->shipping_total > 0 ) ? number_format( ( ( ( $sales_data ) ? $sales_data->shipping_total : 0 ) - ( ( $start_date2 && $sales_data2 && $sales_data2->shipping_total > 0 ) ? $sales_data2->shipping_total : 0 ) ) / ( ( $start_date2 && $sales_data2 ) ? $sales_data2->shipping_total : 0 ) * 100, 2, '.', '' ) : '' )
			),
            'tax' => (object) array(
				'set1' => ( $product_id ) ? $GLOBALS['currency']->get_currency_display( 0 ) : $GLOBALS['currency']->get_currency_display( ( $sales_data ) ? $sales_data->tax_total + $sales_data->vat_total + $sales_data->gst_total + $sales_data->pst_total + $sales_data->hst_total : 0 ),
				'set2' => ( $product_id ) ? $GLOBALS['currency']->get_currency_display( 0 ) : $GLOBALS['currency']->get_currency_display( ( $start_date2 && $sales_data2 ) ? $sales_data2->tax_total + $sales_data2->vat_total + $sales_data2->gst_total + $sales_data2->pst_total + $sales_data2->hst_total : 0 ),
				'diff' => ( $product_id ) ? '' : ( ( $start_date2 && $sales_data2 && ( $sales_data2->tax_total + $sales_data2->vat_total + $sales_data2->gst_total + $sales_data2->pst_total + $sales_data2->hst_total ) > 0 ) ? number_format( ( ( ( $sales_data ) ? $sales_data->tax_total + $sales_data->vat_total + $sales_data->gst_total + $sales_data->pst_total + $sales_data->hst_total : 0 ) - ( ( $start_date2 && $sales_data2 ) ? $sales_data2->tax_total + $sales_data2->vat_total + $sales_data2->gst_total + $sales_data2->pst_total + $sales_data2->hst_total : 0 ) ) / ( ( $start_date2 && $sales_data2 ) ? $sales_data2->tax_total + $sales_data2->vat_total + $sales_data2->gst_total + $sales_data2->pst_total + $sales_data2->hst_total : 0 ) * 100, 2, '.', '' ) : '' )
			),
            'discount' => (object) array(
				'set1' => ( $product_id ) ? $GLOBALS['currency']->get_currency_display( 0 ) : $GLOBALS['currency']->get_currency_display( ( $sales_data ) ? $sales_data->discount_total : 0 ),
				'set2' => ( $product_id ) ? $GLOBALS['currency']->get_currency_display( 0 ) : $GLOBALS['currency']->get_currency_display( ( $start_date2 && $sales_data2 ) ? $sales_data2->discount_total : 0 ),
				'diff' => ( $product_id ) ? '' : ( ( $start_date2 && $sales_data2 && $sales_data2->discount_total > 0 ) ? number_format( ( ( ( $sales_data ) ? $sales_data->discount_total : 0 ) - ( ( $start_date2 && $sales_data2 ) ? $sales_data2->discount_total : 0 ) ) / ( ( $start_date2 && $sales_data2 ) ? $sales_data2->discount_total : 0 ) * 100, 2, '.', '' ) : '' )
			),
            'refund' => (object) array(
				'set1' => ( $product_id ) ? $GLOBALS['currency']->get_currency_display( 0 ) : $GLOBALS['currency']->get_currency_display( ( $sales_data ) ? $sales_data->refund_total : 0 ),
				'set2' => ( $product_id ) ? $GLOBALS['currency']->get_currency_display( 0 ) : $GLOBALS['currency']->get_currency_display( ( $start_date2 && $sales_data2 ) ? $sales_data2->refund_total : 0 ),
				'diff' => ( $product_id ) ? '' : ( ( $start_date2 && $sales_data2 && $sales_data2->refund_total > 0 ) ? number_format( ( ( ( $sales_data ) ? $sales_data->refund_total : 0 ) - ( ( $start_date2 && $sales_data2 ) ? $sales_data2->refund_total : 0 ) ) / ( ( $start_date2 && $sales_data2 ) ? $sales_data2->refund_total : 0 ) * 100, 2, '.', '' ) : '' )
			),
            'net_revenue' => (object) array(
				'set1' => ( $product_id ) ? $GLOBALS['currency']->get_currency_display( 0 ) : $GLOBALS['currency']->get_currency_display( ( $sales_data ) ? $sales_data->total - $sales_data->discount_total - $sales_data->refund_total : 0 ),
				'set2' => ( $product_id ) ? $GLOBALS['currency']->get_currency_display( 0 ) : $GLOBALS['currency']->get_currency_display( ( $start_date2 && $sales_data2 ) ? $sales_data2->total - $sales_data2->discount_total - $sales_data2->refund_total : 0 ),
				'diff' => ( $product_id || !$sales_data || !$sales_data2 || $sales_data2->total - $sales_data2->discount_total - $sales_data2->refund_total == 0 ) ? '' : ( ( $start_date2 && $sales_data2 ) ? number_format( ( ( ( $sales_data ) ? $sales_data->total - $sales_data->discount_total - $sales_data->refund_total : 0 ) - ( ( $start_date2 && $sales_data2 ) ? $sales_data2->total - $sales_data2->discount_total - $sales_data2->refund_total : 0 ) ) / ( ( $start_date2 && $sales_data2 ) ? $sales_data2->total - $sales_data2->discount_total - $sales_data2->refund_total : 0 ) * 100, 2, '.', '' ) : '' )
			),
            'items' => (object) array(
				'set1' => ( $sales_data_item ) ? $sales_data_item->item_count : 0,
				'set2' => ( $start_date2 && $sales_data_item2 ) ? $sales_data_item2->item_count : 0,
				'diff' => ( $start_date2 && $sales_data_item2 && $sales_data_item2->item_count > 0 ) ? number_format( ( ( ( $sales_data_item ) ? $sales_data_item->item_count : 0 ) - ( ( $start_date2 && $sales_data_item2 ) ? $sales_data_item2->item_count : 0 ) ) / ( ( $start_date2 && $sales_data_item2 ) ? $sales_data_item2->item_count : 0 ) * 100, 2, '.', '' ) : ''
			),
            'customers' => (object) array(
				'set1' => ( $product_id ) ? ( ( $sales_data && $sales_data->item_customer_count ) ? $sales_data->item_customer_count : 0 ) : ( ( $sales_data && $sales_data->customer_count ) ? $sales_data->customer_count : 0 ),
				'set2' => ( $product_id ) ? ( ( $start_date2 && $sales_data2 && $sales_data2->item_customer_count ) ? $sales_data2->item_customer_count : 0 ) : ( ( $start_date2 && $sales_data2 && $sales_data2->customer_count ) ? $sales_data2->customer_count : 0 ),
				'diff' => ( $product_id ) ? ( ( $start_date2 && $sales_data2 && $sales_data2->item_customer_count > 0 ) ? number_format( ( ( ( $sales_data ) ? $sales_data->item_customer_count : 0 ) - ( ( $start_date2 && $sales_data2 ) ? $sales_data2->item_customer_count : 0 ) ) / ( ( $start_date2 && $sales_data2 ) ? $sales_data2->item_customer_count : 0 ) * 100, 2, '.', '' ) : '' ) : ( ( $start_date2 && $sales_data2 && $sales_data2->customer_count > 0 ) ? number_format( ( ( ( $sales_data ) ? $sales_data->customer_count : 0 ) - ( ( $start_date2 && $sales_data2 ) ? $sales_data2->customer_count : 0 ) ) / ( ( $start_date2 && $sales_data2 ) ? $sales_data2->customer_count : 0 ) * 100, 2, '.', '' ) : '' )
			),
            'carts' => (object) array(
				'set1' => ( $sales_data_cart ) ? $sales_data_cart->cart_total : 0,
				'set2' => ( $start_date2 && $sales_data_cart2 ) ? $sales_data_cart2->cart_total : 0,
				'diff' => ( $start_date2 && $sales_data2 && $sales_data_cart2->cart_total > 0 ) ? number_format( ( ( ( $sales_data_cart ) ? $sales_data_cart->cart_total : 0 ) - ( ( $start_date2 && $sales_data_cart2 ) ? $sales_data_cart2->cart_total : 0 ) ) / ( ( $start_date2 && $sales_data_cart2 ) ? $sales_data_cart2->cart_total : 0 ) * 100, 2, '.', '' ) : ''
			),
            'orders' => (object) array(
				'set1' => ( $product_id ) ? ( ( $sales_data && $sales_data->item_order_count ) ? $sales_data->item_order_count : 0 ) : ( ( $sales_data && $sales_data->order_count ) ? $sales_data->order_count : 0 ),
				'set2' => ( $product_id ) ? ( ( $start_date2 && $sales_data2 && $sales_data2->item_order_count ) ? $sales_data2->item_order_count : 0 ) : ( ( $start_date2 && $sales_data2 && $sales_data2->order_count ) ? $sales_data2->order_count : 0 ),
				'diff' => ( $product_id ) ? ( ( $start_date2 && $sales_data2 && $sales_data2->item_order_count > 0 ) ? number_format( ( ( ( $sales_data ) ? $sales_data->item_order_count : 0 ) - ( ( $start_date2 && $sales_data2 ) ? $sales_data2->item_order_count : 0 ) ) / ( ( $start_date2 && $sales_data2 ) ? $sales_data2->item_order_count : 0 ) * 100, 2, '.', '' ) : '' ) : ( ( $start_date2 && $sales_data2 && $sales_data2->order_count > 0 ) ? number_format( ( ( ( $sales_data ) ? $sales_data->order_count : 0 ) - ( ( $start_date2 && $sales_data2 ) ? $sales_data2->order_count : 0 ) ) / ( ( $start_date2 && $sales_data2 ) ? $sales_data2->order_count : 0 ) * 100, 2, '.', '' ) : '' )
			),
        );
    }
	
	public function get_stats( $type, $start_date, $end_date, $start_date2 = false, $end_date2 = false, $range = 'daily', $product_id = false, $country = false, $billing_country = false ){
        if( $type == 'sales' ){
			$chart_label = __( 'Sales', 'wp-easycart' );
		}else if( $type == 'items' ){
			$chart_label = __( 'Items Sold', 'wp-easycart' );
		}else if( $type == 'carts' ){
			$chart_label = __( 'Abandoned Carts', 'wp-easycart' );
		}
		if( $start_date2 ){
            $data_items = $this->{'get_' . $type . '_dataset'}( $start_date, $end_date, $range, $product_id, $country, $billing_country );
            $data_items2 = $this->{'get_' . $type . '_dataset'}( $start_date2, $end_date2, $range, $product_id, $country, $billing_country );
            $days1 = $this->get_sales_dataset_length( $start_date, $end_date, $range );
            if( $range == 'daily' && $days1 <= 2 ){
                $days1 = ($days1+1) * 24;
                $range = 'hourly';
            }
            $labels = array( );
            $labels1 = array( );
            $labels2 = array( );
            for( $i=0; $i<=$days1; $i++ ){
                if( $range == 'hourly' ){
                    $labels[] = ( $i % 2 ) ? date( 'g:00 a', strtotime( '+' . $i . ' hour', strtotime( $start_date ) ) ) : '';
                    $labels1[] = date( 'h:00 l', strtotime( '+' . $i . ' hour', strtotime( $start_date ) ) );
                    $labels2[] = date( 'h:00 l', strtotime( '+' . $i . ' hour', strtotime( $start_date2 ) ) );

                }else if( $range == 'daily' ){
                    $labels[] = ( $i % 2 ) ? date( 'M d', strtotime( '+' . $i . ' day', strtotime( $start_date ) ) ) : '';
                    $labels1[] = date( 'M d', strtotime( '+' . $i . ' day', strtotime( $start_date ) ) );
                    $labels2[] = date( 'M d', strtotime( '+' . $i . ' day', strtotime( $start_date2 ) ) );

                }else if( $range == 'weekly' ){
                    $labels[] = date( 'M d, Y', strtotime( 'sunday last week', strtotime( '+' . ($i*7) . ' day', strtotime( $start_date ) ) ) );
                    $labels1[] = date( 'M d, Y', strtotime( 'sunday last week', strtotime( '+' . ($i*7) . ' day', strtotime( $start_date ) ) ) );
                    $labels2[] = date( 'M d, Y', strtotime( 'sunday last week', strtotime( '+' . ($i*7) . ' day', strtotime( $start_date2 ) ) ) );

                }else if( $range == 'monthly' ){
                    $labels[] = date( 'M d, Y', strtotime( '+' . $i . ' month', strtotime( $start_date ) ) );
                    $labels1[] = date( 'M d, Y', strtotime( '+' . $i . ' month', strtotime( $start_date ) ) );
                    $labels2[] = date( 'M d, Y', strtotime( '+' . $i . ' month', strtotime( $start_date2 ) ) );

                }else if( $range == 'yearly' ){
                    $labels[] = date( 'M d, Y', strtotime( '+' . $i . ' year', strtotime( $start_date ) ) );
                    $labels1[] = date( 'M d, Y', strtotime( '+' . $i . ' year', strtotime( $start_date ) ) );
                    $labels2[] = date( 'M d, Y', strtotime( '+' . $i . ' year', strtotime( $start_date2 ) ) );

                }
            }

            $data = (object) array(
                'labels'	=> $labels,
                'datasets'	=> array( 
                    (object) array(
                        'label'	=> $chart_label . " " . date( 'M, d', strtotime( $start_date ) ) . ' - ' . date( 'M, d Y', strtotime( $end_date ) ),
                        'backgroundColor' => $this->hex2rgba( get_option( 'ec_option_admin_color' ), .15 ),
                        'borderColor'   => $this->hex2rgba( get_option( 'ec_option_admin_color' ), .8 ),
						'borderWidth'	=> 1,
                        'data' => $data_items,
                        'datalabels' => $labels1
                    ),
                    (object) array(
                        'label'	=> $chart_label . " " . date( 'M, d', strtotime( $start_date2 ) ) . ' - ' . date( 'M, d Y', strtotime( $end_date2 ) ),
                        'backgroundColor' => 'rgba( 0, 0, 0, .05 )',
                        'borderColor'   => 'rgba( 0, 0, 0, .2 )',
						'borderWidth'	=> 1,
                        'data' => $data_items2,
                        'datalabels' => $labels2
                    )
                )
            );
            
        }else{
            $data_items = $this->{'get_' . $type . '_dataset'}( $start_date, $end_date, $range, $product_id, $country, $billing_country );
            $days1 = $this->get_sales_dataset_length( $start_date, $end_date, $range );
            if( $range == 'daily' && $days1 <= 2 ){
                $days1 = ( $days1 + 1 ) * 24;
                $range = 'hourly';
            }
            $labels = array( );
            $labels1 = array( );
            for( $i=0; $i<=$days1; $i++ ){
                if( $range == 'hourly' ){
                    $labels[] = ( $i % 2 ) ? date( 'g:00 a', strtotime( '+' . $i . ' hour', strtotime( $start_date ) ) ) : '';
                    $labels1[] = date( 'h:00 l', strtotime( '+' . $i . ' hour', strtotime( $start_date ) ) );
            
                }else if( $range == 'daily' ){
                    $labels[] = ( $i % 2 ) ? date( 'M d', strtotime( '+' . $i . ' day', strtotime( $start_date ) ) ) : '';
                    $labels1[] = date( 'M d', strtotime( '+' . $i . ' day', strtotime( $start_date ) ) );
            
                }else if( $range == 'weekly' ){
                    $labels[] = date( 'M d, Y', strtotime( 'sunday last week', strtotime( '+' . ($i*7) . ' day', strtotime( $start_date ) ) ) );
                    $labels1[] = date( 'M d, Y', strtotime( 'sunday last week', strtotime( '+' . ($i*7) . ' day', strtotime( $start_date ) ) ) );
            
                }else if( $range == 'monthly' ){
                    $labels[] = date( 'M d, Y', strtotime( '+' . $i . ' month', strtotime( $start_date ) ) );
                    $labels1[] = date( 'M d, Y', strtotime( '+' . $i . ' month', strtotime( $start_date ) ) );
            
                }else if( $range == 'yearly' ){
                    $labels[] = date( 'M d, Y', strtotime( '+' . $i . ' year', strtotime( $start_date ) ) );
                    $labels1[] = date( 'M d, Y', strtotime( '+' . $i . ' year', strtotime( $start_date ) ) );
            
                }
            }

            $data = (object) array(
                'labels'	=> $labels,
                'datasets'	=> array( 
                    (object) array(
                        'label'	=> $chart_label . " " . date( 'M, d', strtotime( $start_date ) ) . ' - ' . date( 'M, d', strtotime( $end_date ) ),
                        'backgroundColor' => $this->hex2rgba( get_option( 'ec_option_admin_color' ), .15 ),
                        'borderColor'   => $this->hex2rgba( get_option( 'ec_option_admin_color' ), .8 ),
						'borderWidth'	=> 1,
                        'data' => $data_items,
                        'datalabels' => $labels1
                    )
                )
            );
        }
		return json_encode( $data );
	}
	
	private function get_total_new_unviewed_orders( ){
		return $this->wpdb->get_var( "SELECT COUNT( ec_order.order_id ) as total FROM ec_order WHERE ec_order.order_viewed = 0" );
	}
	/* END STATS FUNCTIONS */
	
	public function setup_menu( ){
		
		if( function_exists( 'wp_easycart_admin_license' ) ){
			$license = wp_easycart_admin_license( )->license_check();
			$license_data = wp_easycart_admin_license( )->license_data;
            $license_info = get_option( 'wp_easycart_license_info' );
		}
		
		if( function_exists( 'wp_easycart_admin_license' ) && ( !wp_easycart_admin_license( )->active_license || $license_data->is_trial ) ){
			$registration_count = 1;
			$registration_label = sprintf( __( 'Registration %s', 'wp-easycart' ), "<span class='update-plugins count-$registration_count' title='" . __( 'License', 'wp-easycart' ) . "'><span class='update-count'>" . number_format_i18n($registration_count) . "</span></span>" );
            $license_label = sprintf( __( 'Store Status %s', 'wp-easycart' ), "<span class='update-plugins count-$registration_count' title='" . __( 'License', 'wp-easycart' ) . "'><span class='update-count'>" . number_format_i18n($registration_count) . "</span></span>" );
		} else {
			$registration_count = 0;
			$registration_label = sprintf( __( 'Registration %s', 'wp-easycart' ), "<span class='update-plugins count-$registration_count' title='" . __( 'License', 'wp-easycart' ) . "'><span class='update-count'>" . number_format_i18n($registration_count) . "</span></span>" );
            
            if( function_exists( 'wp_easycart_admin_license' ) ){
                $test_now = time( );
                $test_expiration = strtotime( $license_data->support_end_date );
                $test_diff = $test_expiration - $test_now;
                $days_left = round( $test_diff / ( 60 * 60 * 24 ) );
                $days_left = ( $days_left < 0 ) ? 0 : $days_left; // No Negative
                if( $days_left < 70 ){
                    $license_label = sprintf( __( 'Store Status %s', 'wp-easycart' ), "<span class='update-plugins count-1' title='" . __( 'License', 'wp-easycart' ) . "'><span class='update-count'>" . number_format_i18n(1) . "</span></span>" );
                }else{
                    $license_label = __( 'Store Status', 'wp-easycart' );
                }
            }else{
                $license_label = __( 'Store Status', 'wp-easycart' );
            }
		}
		
		//new unread order notification
		$orders_count = $this->new_unviewed_orders;
		$order_label = sprintf( __( 'Orders %s', 'wp-easycart' ), "<span class='update-plugins count-$orders_count' title='" . __( 'New Orders', 'wp-easycart' ) . "'><span class='update-count'>" . number_format_i18n($orders_count) . "</span></span>" );
		
		//total notifications
		$total_notifications = $registration_count + $orders_count;
		$mainmenu_label = sprintf( __( 'WP EasyCart %s', 'wp-easycart' ), "<span class='update-plugins count-$total_notifications' title='" . __( 'New Orders', 'wp-easycart' ) . "'><span class='update-count'>" . number_format_i18n($total_notifications) . "</span></span>" );

        $store_status_label = __( 'Diagnostics', 'wp-easycart' );
        if( !$this->database_check_current( ) ){
            $store_status_label .= '<span class="update-plugins count-1" title="' . __( 'Status Errors', 'wp-easycart' ) . '"><span class="update-count">1</span></span>';
        }
        
		add_menu_page( 'WP EasyCart', $mainmenu_label, 'manage_options', 'wp-easycart-dashboard', array( $this, 'load_dashboard' ), 'dashicons-cart', 58 );
		add_menu_page( __( 'Extensions', 'wp-easycart' ), __( 'Extensions', 'wp-easycart' ), 'manage_options', 'ec_adminv2', array( $this, 'load_extensions_page' ), 'dashicons-cart', 59 );
	
		add_submenu_page( 'wp-easycart-dashboard', __( 'WP EasyCart Reports', 'wp-easycart' ), __( 'Reports', 'wp-easycart' ), 'manage_options', 'wp-easycart-dashboard', array( $this, 'load_dashboard' ) );
		add_submenu_page( 'wp-easycart-dashboard', __( 'WP EasyCart Store Status', 'wp-easycart' ), $license_label, 'manage_options', 'wp-easycart-license-status', array( $this, 'load_license_status' ) );
		add_submenu_page( 'wp-easycart-dashboard', __( 'WP EasyCart Products', 'wp-easycart' ), __( 'Products', 'wp-easycart' ), 'manage_options', 'wp-easycart-products', array( $this, 'load_products' ) );
		add_submenu_page( 'wp-easycart-dashboard', __( 'WP EasyCart Orders', 'wp-easycart' ), $order_label, 'manage_options', 'wp-easycart-orders', array( $this, 'load_orders' ) );
		add_submenu_page( 'wp-easycart-dashboard', __( 'WP EasyCart Users', 'wp-easycart' ), __( 'Users', 'wp-easycart' ), 'manage_options', 'wp-easycart-users', array( $this, 'load_users' ) );
		add_submenu_page( 'wp-easycart-dashboard', __( 'WP EasyCart Marketing', 'wp-easycart' ), __( 'Marketing', 'wp-easycart' ), 'manage_options', 'wp-easycart-rates', array( $this, 'load_marketing' ) );
		add_submenu_page( 'wp-easycart-dashboard', __( 'WP EasyCart Settings', 'wp-easycart' ), __( 'Settings', 'wp-easycart' ), 'manage_options', 'wp-easycart-settings', array( $this, 'load_settings' ) );
		add_submenu_page( 'wp-easycart-dashboard', __( 'WP EasyCart Diagnostics', 'wp-easycart' ), $store_status_label, 'manage_options', 'wp-easycart-status', array( $this, 'load_status' ) );
		add_submenu_page( 'wp-easycart-dashboard', __( 'WP EasyCart Registration', 'wp-easycart' ), $registration_label , 'manage_options', 'wp-easycart-registration', array( $this, 'load_registration' ) );
        
        if( function_exists( 'wp_easycart_admin_license' ) ){
            if( $license_data->is_trial ){
                add_submenu_page( 'wp-easycart-dashboard', __( 'Upgrade to Premium', 'wp-easycart' ), '<strong id="wp-easycart-premium-link" style="color:#c0fc14;">' . __( 'Upgrade to Premium', 'wp-easycart' ) . '</strong>', 'manage_options', 'wp-easycart-license-manage-trial', array( $this, 'upgrade_premium_none' ) );
                add_filter( 'clean_url', array( $this, 'upgrade_premium' ), 10, 3);
                
            }else if( isset( $license_data->support_end_date ) && time( ) > strtotime( $license_data->support_end_date ) ){ // Expired
                add_submenu_page( 'wp-easycart-dashboard', __( 'RENEW LICENSE', 'wp-easycart' ), '<strong id="wp-easycart-premium-link" style="color:#ffa800;">' . __( 'RENEW LICENSE', 'wp-easycart' ) . '</strong>', 'manage_options', 'wp-easycart-license-renew', array( $this, 'upgrade_premium_none' ) );
                add_filter( 'clean_url', array( $this, 'upgrade_premium' ), 10, 3);

            }else if( isset( $license_data->support_end_date ) && $license_data->model_number == 'ec400' ){ // Pro User
                add_submenu_page( 'wp-easycart-dashboard', __( 'Upgrade to Premium', 'wp-easycart' ), '<strong id="wp-easycart-premium-link" style="color:#c0fc14;">' . __( 'Upgrade to Premium', 'wp-easycart' ) . '</strong>', 'manage_options', 'wp-easycart-license-upgrade', array( $this, 'upgrade_premium_none' ) );
                add_filter( 'clean_url', array( $this, 'upgrade_premium' ), 10, 3);

            }else if( isset( $license_data->support_end_date ) && $license_data->model_number == 'ec410' ){ // Premium User
                add_submenu_page( 'wp-easycart-dashboard', __( 'Check License Status', 'wp-easycart' ), '<strong id="wp-easycart-premium-link" style="color:#c0fc14;">' . __( 'Check License Status', 'wp-easycart' ) . '</strong>', 'manage_options', 'wp-easycart-license-manage', array( $this, 'upgrade_premium_none' ) );
                add_filter( 'clean_url', array( $this, 'upgrade_premium' ), 10, 3);

            }else{ // Missing Key Data?
                add_submenu_page( 'wp-easycart-dashboard', __( 'Check License Status', 'wp-easycart' ), '<strong id="wp-easycart-premium-link" style="color:#c0fc14;">' . __( 'Check License Status', 'wp-easycart' ) . '</strong>', 'manage_options', 'wp-easycart-license-manage', array( $this, 'upgrade_premium_none' ) );
                add_filter( 'clean_url', array( $this, 'upgrade_premium' ), 10, 3);

            }
            
        }else{ // FREE USER 
            add_submenu_page( 'wp-easycart-dashboard', __( 'Upgrade to Premium', 'wp-easycart' ), '<strong id="wp-easycart-premium-link" style="color:#c0fc14;">' . __( 'Upgrade to Premium', 'wp-easycart' ) . '</strong>', 'manage_options', 'wp-easycart-premium', array( $this, 'upgrade_premium_none' ) );
            add_filter( 'clean_url', array( $this, 'upgrade_premium' ), 10, 3);
        }
		  
	}
	
	public function setup_pro_hooks( ){
		/* Products Tab*/
		add_action( 'wp_easycart_admin_subscription_plans_list', array( $this, 'show_upgrade' ) );
		add_action( 'wp_easycart_admin_subscription_plans_details', array( $this, 'show_upgrade' ) );
		add_filter( 'wp_easycart_admin_advanced_option_type', array( $this, 'filter_option_type' ) );
		
		/* Marketing Tab */
		add_action( 'wp_easycart_admin_subscriptions_list', array( $this, 'show_upgrade' ) );
		add_action( 'wp_easycart_admin_subscriptions_details', array( $this, 'show_upgrade' ) );
		add_action( 'wp_easycart_admin_downloads_list', array( $this, 'show_upgrade' ) );
		add_action( 'wp_easycart_admin_downloads_details', array( $this, 'show_upgrade' ) );
		add_action( 'wp_easycart_admin_abandon_cart_load', array( $this, 'show_upgrade' ) );
		add_action( 'wp_easycart_admin_coupon_list', array( $this, 'show_upgrade' ) );
		add_action( 'wp_easycart_admin_coupon_details', array( $this, 'show_upgrade' ) );
		add_action( 'wp_easycart_admin_giftcard_list', array( $this, 'show_upgrade' ) );
		add_action( 'wp_easycart_admin_giftcard_details', array( $this, 'show_upgrade' ) );
		add_action( 'wp_easycart_admin_promotion_list', array( $this, 'show_upgrade' ) );
		add_action( 'wp_easycart_admin_promotion_details', array( $this, 'show_upgrade' ) );
		
		$pro_plugin_base = 'wp-easycart-pro/wp-easycart-admin-pro.php';
		$pro_plugin_file = WP_PLUGIN_DIR . '/' . $pro_plugin_base;
		
		$pro_plugin_base_legacy = 'wp-easycart-admin/wp-easycart-admin-pro.php';
		$pro_plugin_file_legacy = WP_PLUGIN_DIR . '/' . $pro_plugin_base_legacy;
		
		if( file_exists( $pro_plugin_file ) || file_exists( $pro_plugin_file_legacy ) ){
			remove_action( 'wp_easycart_admin_messages', array( wp_easycart_admin( ), 'load_upsell_image' ) );
			add_filter( 'wp_easycart_admin_lock_icon', array( $this, 'remove_lock_icon' ) );
			remove_filter( 'wp_easycart_admin_advanced_option_type', array( $this, 'filter_option_type' ) );
			remove_action( 'wp_dashboard_setup', array( $this, 'add_ec_nag_widget' ) );
		}
		
		do_action( 'wp_easycart_admin_pro_ready' );
	}
	
	public function remove_lock_icon( $content ){
		return "";
	}
	
	public function load_dashboard( ){
		add_action( 'wp_easycart_admin_shell_content', array( $this, 'load_dashboard_content' ), 1, 0 );
		$this->load_admin_shell( );
	}
	
	public function load_dashboard_content( ){
		include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/dashboard.php' );
	}
    
    public function load_license_status( ){
        add_action( 'wp_easycart_admin_shell_content', array( $this, 'load_license_status_content' ), 1, 0 );
		$this->load_admin_shell( );
    }
	
	public function load_license_status_content( ){
		include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/license-status.php' );
	}
    
    public function display_stat_circle( $label, $percent, $box_title = false, $box_content = false, $box_link = false, $box_link_text = false ){
        $color = 'green';
        if( $percent < .7 && $percent > .35 ){
            $color = 'orange';
        }else if( $percent <= .35 && $percent != 0 ){
            $color = 'red';
        }else if( $percent == 0 ){
            $color = 'grey';
        }else if( $percent == -1 ){
            $color = 'grey';
        }
        $degree = number_format( $percent * 360, 1, '.', '' );
        if( $percent == -1 ){
            $degree = 0;
        }
        $target = ( $box_link && strtolower( substr( $box_link, 0, 5 ) ) == 'https' ) ? '_blank' : '_self';
        echo '<div class="ec_admin_status_circle ' . ( ( $percent >= .5 ) ? 'over-half ' : '' ) . $color . '">
            <span' . ( ( $percent == -1 ) ? ' class="expired"' : '' ) . '>' . $label . '</span>
            <div class="ec_admin_status_circle_slice">
                <div class="ec_admin_status_circle_bar" style="-webkit-transform: rotate(' . $degree . 'deg); -moz-transform: rotate(' . $degree . 'deg); -ms-transform: rotate(' . $degree . 'deg); -o-transform: rotate(' . $degree . 'deg); transform: rotate(' . $degree . 'deg);"></div>
                <div class="ec_admin_status_circle_fill"></div>
            </div>
        </div>';
        
        if( $box_content ){
            echo '<div class="ec_admin_status_circle_content">
                <h4>' . $box_title . '</h4>
                <div>' . $box_content . '</div>';
            
            if( $box_link && $box_link_text ){
                echo '
                    <a href="' . $box_link . '"' . ( ( $percent == -1 ) ? ' style="background-color:#e03333"' : '' ) . ' target="' . $target . '">' . $box_link_text . '</a>';
            }
            
            echo '</div>';
        }
    }
    
    public function upgrade_premium_none( ){
        // External Link
    }
    
    public function upgrade_premium( $url, $original_url, $_context ){
        if( preg_match( '/(?:wp-easycart-premium)$/i', $url ) ){
            remove_filter( 'clean_url', array( $this, 'upgrade_premium' ), 10 );
            $url = 'https://www.wpeasycart.com/wordpress-shopping-cart-pricing/';
        }else if( preg_match( '/(?:wp-easycart-license-manage)$/i', $url ) ){
            remove_filter( 'clean_url', array( $this, 'upgrade_premium' ), 10 );
            $url = 'https://www.wpeasycart.com/my-account/';
        }else if( preg_match( '/(?:wp-easycart-license-manage-trial)$/i', $url ) ){
            $license_info = get_option( 'wp_easycart_license_info' );
            remove_filter( 'clean_url', array( $this, 'upgrade_premium' ), 10 );
            $url = 'https://www.wpeasycart.com/products/wp-easycart-trial-upgrade/?transaction_key=' . $license_info['transaction_key'] . '&license_type=Premium';
        }else if( preg_match( '/(?:wp-easycart-license-renew)$/i', $url ) ){
            $license_data = wp_easycart_admin_license( )->license_data;
            $license_info = get_option( 'wp_easycart_license_info' );
            $url = ( $license_data->model_number == 'ec400' ) ? 'https://www.wpeasycart.com/products/wp-easycart-professional-support-upgrades/?transaction_key=' . $license_info['transaction_key'] : 'https://www.wpeasycart.com/products/wp-easycart-premium-support-extensions/?transaction_key=' . $license_info['transaction_key'];
            remove_filter( 'clean_url', array( $this, 'upgrade_premium' ), 10 );
        }else if( preg_match( '/(?:wp-easycart-license-upgrade)$/i', $url ) ){
            $license_data = wp_easycart_admin_license( )->license_data;
            $license_info = get_option( 'wp_easycart_license_info' );
            $url = 'https://www.wpeasycart.com/products/wp-easycart-premium-support-extensions/?transaction_key=' . $license_info['transaction_key'];
            remove_filter( 'clean_url', array( $this, 'upgrade_premium' ), 10 );
        }
        return $url;
    }
	
	public function load_extensions_page( ){
		add_action( 'wp_easycart_admin_shell_content', array( $this, 'load_extensions_content' ), 1, 0 );
		$this->load_admin_shell( );
	}
	
	public function load_reports( ){
		add_action( 'wp_easycart_admin_shell_content', array( $this, 'load_reports_content' ), 1, 0 );
		$this->load_admin_shell( );
	}
	
	public function load_reports_content( ){
		include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/reports.php' );
	}
	
	public function load_admin( ){
		add_action( 'wp_easycart_admin_shell_content', array( $this, 'load_admin_content' ), 1, 0 );
		$this->load_admin_shell( );
	}
	
	public function load_admin_content( ){
		include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/admin.php' );
	}
	
	public function load_settings( ){
		add_action( 'wp_easycart_admin_shell_content', array( $this, 'load_settings_content' ), 1, 0 );
		$this->load_admin_shell( );
	}
	
	public function load_products( ){
		add_action( 'wp_easycart_admin_shell_content', array( $this, 'load_products_content' ), 1, 0 );
		$this->load_admin_shell( );
	}
	
	public function load_orders( ){
		add_action( 'wp_easycart_admin_shell_content', array( $this, 'load_orders_content' ), 1, 0 );
		$this->load_admin_shell( );
	}
	
	public function load_users( ){
		add_action( 'wp_easycart_admin_shell_content', array( $this, 'load_users_content' ), 1, 0 );
		$this->load_admin_shell( );
	}
	
	public function load_marketing( ){
		add_action( 'wp_easycart_admin_shell_content', array( $this, 'load_marketing_content' ), 1, 0 );
		$this->load_admin_shell( );
	}
	
	public function load_status( ){
		add_action( 'wp_easycart_admin_shell_content', array( $this, 'load_status_content' ), 1, 0 );
		$this->load_admin_shell( );
	}
	public function load_registration( ){
		add_action( 'wp_easycart_admin_shell_content', array( $this, 'load_registration_content' ), 1, 0 );
		$this->load_admin_shell( );
	}
	
	public function init_shipping_data( ){
	
		$this->settings = $this->wpdb->get_row( "SELECT * FROM ec_setting" );
		$this->shipping_zones = $this->wpdb->get_results( "SELECT * FROM ec_zone ORDER BY zone_name ASC" );
		$this->shipping_zones_items = $this->wpdb->get_results( "SELECT ec_zone_to_location.zone_to_location_id, ec_zone_to_location.zone_id, ec_zone_to_location.iso2_cnt, ec_zone_to_location.code_sta, ec_country.name_cnt AS country_name, ec_state.name_sta AS state_name FROM ec_zone, ec_zone_to_location LEFT JOIN ec_country ON ec_country.iso2_cnt = ec_zone_to_location.iso2_cnt LEFT JOIN ec_state ON ( ec_state.code_sta = ec_zone_to_location.code_sta AND ec_state.idcnt_sta =  ec_country.id_cnt ) WHERE ec_zone.zone_id = ec_zone_to_location.zone_id ORDER BY ec_zone.zone_name ASC" );
		$this->countries = $this->wpdb->get_results( "SELECT * FROM ec_country ORDER BY sort_order ASC" );
		$this->states = $this->wpdb->get_results( "SELECT ec_state.*, ec_country.name_cnt as country_name FROM ec_state LEFT JOIN ec_country ON ec_country.id_cnt = ec_state.idcnt_sta ORDER BY ec_state.sort_order ASC" );
								
	}
		
	public function load_settings_content( ){
			
		include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/setup-actions.php' );
		$ec_admin_settings = new ec_admin_settings( );
		
		// Try to Process Form Actions if Needed
		if( isset( $_POST['ec_admin_settings_action'] ) ){
			$ec_admin_settings->process_form_action( $_POST['ec_admin_settings_action'] );
		}
		
		$this->init_shipping_data( );
		global $wpdb;
		if( !get_option( 'ec_option_setup_wizard_done' ) && $result = $wpdb->get_row( "SELECT product_id FROM ec_product LIMIT 1" ) ){
		   update_option( 'ec_option_setup_wizard_done', 1 );
    	}
		
		// Display Page Setup
		if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "setup-wizard" ){
			wp_easycart_admin_setup_wizard( )->load_setup_wizard( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "initial-setup" ){
			if( !get_option( 'ec_option_setup_wizard_done' ) ){
				wp_easycart_admin_setup_wizard( )->load_setup_wizard( );
			}else{
				wp_easycart_admin_initial_setup( )->load_initial_setup( );
			}
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "products" ){
			wp_easycart_admin_products( )->load_products_setup( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "tax" ){
			 wp_easycart_admin_taxes( )->load_tax_setup( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "shipping-settings" ){
			$shipping = new wp_easycart_admin_shipping( );
			$shipping->load_shipping_setup( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "shipping-rates" ){
			$shipping = new wp_easycart_admin_shipping( );
			$shipping->load_shipping_rates( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "payment" ){
			wp_easycart_admin_payments( )->load_payments( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "checkout" ){
			$checkout = new wp_easycart_admin_checkout( );
			$checkout->load_checkout( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "account" ){
			$account = new wp_easycart_admin_account( );
			$account->load_account( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "miscellaneous"){
			wp_easycart_admin_miscellaneous( )->load_miscellaneous( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "language-editor" ){
			$language_editor = new wp_easycart_admin_language_editor( );
			$language_editor->load_language( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "design" ){
			$design = new wp_easycart_admin_design( );
			$design->load_design( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "third-party" ){
			wp_easycart_admin_third_party( )->load_third_party( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "email-setup" ){
			$email = new wp_easycart_admin_email_settings( );
			$email->load_email( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "cart-importer" ){
			wp_easycart_admin_cart_importer( )->load_cart_importer( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "country" ){
			wp_easycart_admin_country( )->load_country_list( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "states" ){
			wp_easycart_admin_states( )->load_states_list( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "perpage" ){
			wp_easycart_admin_perpage( )->load_perpage_list( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "pricepoint" ){
			wp_easycart_admin_pricepoint( )->load_pricepoint_list( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "logs" ){
			wp_easycart_admin_logging( )->load_log_list( );
		}else{
			if( !get_option( 'ec_option_setup_wizard_done' ) ){
				wp_easycart_admin_setup_wizard( )->load_setup_wizard( );
			}else{
				wp_easycart_admin_initial_setup( )->load_initial_setup( );
			}
		}
	}
	
	public function load_products_content( ){
		if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "option" ){
			wp_easycart_admin_option( )->load_option_list( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "inventory" ){
			wp_easycart_admin_inventory( )->load_inventory_list( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "optionitems" ){
			wp_easycart_admin_option( )->load_optionitem_list( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "category" ){
			wp_easycart_admin_category( )->load_category_list( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "category-products" ){
			wp_easycart_admin_category( )->load_category_product_list( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "category-products-manage" ){
			wp_easycart_admin_category( )->load_category_product_manage_list( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "menus" ){
			wp_easycart_admin_menus( )->load_menus_list( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "submenus" ){
			wp_easycart_admin_menus( )->load_submenus_list( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "subsubmenus" ){
			wp_easycart_admin_menus( )->load_subsubmenus_list( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "manufacturers" ){
			wp_easycart_admin_manufacturers( )->load_manufacturers_list( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "reviews" ){
			wp_easycart_admin_reviews( )->load_reviews_list( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "subscriptionplans" ){
			wp_easycart_admin_subscription_plans( )->load_subscription_plans_list( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "products" ){
			wp_easycart_admin_products( )->load_products_list( );
		}else{
			wp_easycart_admin_products( )->load_products_list( );
		}
	}
	
	public function load_orders_content( ){
		if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "orders" ){
			wp_easycart_admin_orders( )->load_orders_list( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "subscriptions" ){
			$subscriptions = new wp_easycart_admin_subscriptions( );
			$subscriptions->load_subscriptions_list( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "downloads" ){
			$downloads = new wp_easycart_admin_downloads( );
			$downloads->load_downloads_list( );
		}else{
			wp_easycart_admin_orders( )->load_orders_list( );
		}
	}
	
	public function load_users_content( ){
		if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "accounts" ){
			wp_easycart_admin_users( )->load_users_list( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "user-roles" ){
			wp_easycart_admin_user_role( )->load_user_role_list( );
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "subscribers" ){
			wp_easycart_admin_subscribers( )->load_subscriber_list( );
		}else{
			wp_easycart_admin_users( )->load_users_list( );
		}
	}
	
	public function load_marketing_content( ){
		if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "gift-cards" ){
			$giftcards = new wp_easycart_admin_giftcards( );
			$giftcards = $giftcards->load_giftcards_list();
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "coupons" ){
			$coupons = new wp_easycart_admin_coupons( );
			$coupons = $coupons->load_coupons_list();
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "promotions" ){
			$promotions = new wp_easycart_admin_promotions( );
			$promotions = $promotions->load_promotions_list();
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "abandon-cart" ){
			$abandon_cart = new wp_easycart_admin_abandon_cart( );
			$abandon_cart->load_abandon_cart( );
		}else{
			$coupons = new wp_easycart_admin_coupons( );
			$coupons = $coupons->load_coupons_list();
		}
	}
	
	public function load_status_content( ){
		wp_easycart_admin_store_status( )->load_status( );
	}
	
	public function load_registration_content( ){
		$registration = new wp_easycart_admin_registration( );
		$registration->load_registration_status( );
	}
	
	public function load_shipping_form( $shipping_type ){
		include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/shipping/' . $shipping_type . '.php' );
	}
	
	public function load_extensions( ){
		add_action( 'wp_easycart_admin_shell_content', array( $this, 'load_extensions_content' ), 1, 0 );
		$this->load_admin_shell( );
	}
	
	public function load_extensions_content( ){
		wp_easycart_admin_extensions( )->load_extensions( );
	}
	
	private function load_admin_shell( ){
		include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/shell.php' );
	}
	
	public function load_mobile_navigation( ){
		include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/mobile_nav.php' );
	}
	
	public function load_left_navigation( ){
		include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/left_nav.php' );
	}
	
	public function load_head_navigation( ){
		include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/head_nav.php' );
	}
	
	public function set_title( $admin_title, $title ){
		if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-products" && ( !isset( $_GET['subpage'] ) || $_GET['subpage'] == "products" ) ){
			return __( 'WP EasyCart Products', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-products" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "inventory" ){
			return __( 'WP EasyCart Inventory', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-products" && isset( $_GET['subpage'] ) && ( $_GET['subpage'] == "option" || $_GET['subpage'] == "optionitems" ) ){
			return __( 'WP EasyCart Option Sets', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-products" && isset( $_GET['subpage'] ) && ( $_GET['subpage'] == "category" || $_GET['subpage'] == "category-products" || $_GET['subpage'] == "category-products-manage" ) ){
			return __( 'WP EasyCart Categories', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-products" && isset( $_GET['subpage'] ) && ( $_GET['subpage'] == "menus" || $_GET['subpage'] == "submenus" || $_GET['subpage'] == "subsubmenus" ) ){
			return __( 'WP EasyCart Menus', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-products" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "manufacturers" ){
			return __( 'WP EasyCart Manufacturers', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-products" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "reviews" ){
			return __( 'WP EasyCart Product Reviews', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-products" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "subscriptionplans" ){
			return __( 'WP EasyCart Subscription Plans', 'wp-easycart' );
		
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-orders" && ( !isset( $_GET['subpage'] ) || $_GET['subpage'] == "orders" ) ){
			return __( 'WP EasyCart Orders', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-orders" && isset( $_GET['subpage'] )&& $_GET['subpage'] == "subscriptions" ){
			return __( 'WP EasyCart Subscriptions', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-orders" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "downloads" ){
			return __( 'WP EasyCart Downloads', 'wp-easycart' );
		
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-users" && ( !isset( $_GET['subpage'] ) || $_GET['subpage'] == "accounts" ) ){
			return __( 'WP EasyCart User Accounts', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-users" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "user-roles" ){
			return __( 'WP EasyCart User Roles', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-users" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "subscribers" ){
			return __( 'WP EasyCart Subscribers', 'wp-easycart' );
			
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-rates" && ( !isset( $_GET['subpage'] ) || $_GET['subpage'] == "coupons" ) ){
			return __( 'WP EasyCart Coupons', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-rates" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "gift-cards" ){
			return __( 'WP EasyCart Gift Cards', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-rates" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "promotions" ){
			return __( 'WP EasyCart Promotions', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-rates" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "abandon-cart" ){
			return __( 'WP EasyCart Abandoned Cart', 'wp-easycart' );
		
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && ( !isset( $_GET['subpage'] ) || $_GET['subpage'] == "initial-setup" ) ){
			return __( 'WP EasyCart Initial Setup', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "products" ){
			return __( 'WP EasyCart Product Settings', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "tax" ){
			return __( 'WP EasyCart Tax Settings', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "shipping-settings" ){
			return __( 'WP EasyCart Shipping Settings', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "shipping-rates" ){
			return __( 'WP EasyCart Shipping Rates', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "payment" ){
			return __( 'WP EasyCart Payment Settings', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "checkout" ){
			return __( 'WP EasyCart Checkout Settings', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "account" ){
			return __( 'WP EasyCart Account Settings', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "miscellaneous" ){
			return __( 'WP EasyCart Additional Settings', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "language-editor" ){
			return __( 'WP EasyCart Language Editor', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "design" ){
			return __( 'WP EasyCart Design Settings', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "email-setup" ){
			return __( 'WP EasyCart Email Setup', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "third-party" ){
			return __( 'WP EasyCart Third Party Settings', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "cart-importer" ){
			return __( 'WP EasyCart Cart Importer', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "country" ){
			return __( 'WP EasyCart Country Management', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "states" ){
			return __( 'WP EasyCart State Management', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "perpage" ){
			return __( 'WP EasyCart Per Page Settings', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "pricepoint" ){
			return __( 'WP EasyCart Price Point Settings', 'wp-easycart' );
		}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "logs" ){
			return __( 'WP EasyCart Logs', 'wp-easycart' );
		
		}else{
			return $admin_title;
		}
	}
	
	public function load_block_editor_assets( ){
		if( current_user_can( 'manage_options' ) && is_admin( ) ){
			wp_register_script( 'wp_easycart_block_js', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/js/block.js' ), array( 'wp-blocks', 'wp-i18n', 'wp-element' ), EC_CURRENT_VERSION );
			wp_enqueue_script( 'wp_easycart_block_js' );
			wp_localize_script( 'wp_easycart_block_js', 'wp_easycart_categories', $this->get_categories_cdata( ) );
			wp_localize_script( 'wp_easycart_block_js', 'wp_easycart_manufacturers', $this->get_manufacturers_cdata( ) );
			wp_localize_script( 'wp_easycart_block_js', 'wp_easycart_menulevel1', $this->get_menu_level1_cdata( ) );
			wp_localize_script( 'wp_easycart_block_js', 'wp_easycart_menulevel2', $this->get_menu_level2_cdata( ) );
			wp_localize_script( 'wp_easycart_block_js', 'wp_easycart_menulevel3', $this->get_menu_level3_cdata( ) );
			wp_localize_script( 'wp_easycart_block_js', 'wp_easycart_products', $this->get_products_cdata( ) );
			wp_localize_script( 'wp_easycart_block_js', 'wp_easycart_products_model', $this->get_products_model_cdata( ) );
            
            wp_register_style( 'wp_easycart_block_css', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/css/block.css' ), array( ), EC_CURRENT_VERSION );
			wp_enqueue_style( 'wp_easycart_block_css' );
		}
	}
	
	private function get_categories_cdata( ){
		global $wpdb;
		return $wpdb->get_results( "SELECT category_id AS value, category_name AS label FROM ec_category ORDER BY category_name ASC LIMIT 2000" );
	}
	
	private function get_manufacturers_cdata( ){
		global $wpdb;
		return $wpdb->get_results( "SELECT manufacturer_id AS value, ec_manufacturer.`name` AS label FROM ec_manufacturer ORDER BY ec_manufacturer.`name` ASC LIMIT 2000" );
	}
	
	private function get_menu_level1_cdata( ){
		global $wpdb;
		return $wpdb->get_results( "SELECT menulevel1_id AS value, ec_menulevel1.`name` AS label FROM ec_menulevel1 ORDER BY ec_menulevel1.`name` ASC LIMIT 2000" );
	}
	
	private function get_menu_level2_cdata( ){
		global $wpdb;
		return $wpdb->get_results( "SELECT menulevel1_id, menulevel2_id AS value, ec_menulevel2.`name` AS label FROM ec_menulevel2 ORDER BY ec_menulevel2.`name` ASC LIMIT 2000" );
	}
	
	private function get_menu_level3_cdata( ){
		global $wpdb;
		return $wpdb->get_results( "SELECT menulevel2_id, menulevel3_id AS value, ec_menulevel3.`name` AS label FROM ec_menulevel3 ORDER BY ec_menulevel3.`name` ASC LIMIT 2000" );
	}
	
	private function get_products_cdata( ){
		global $wpdb;
		return $wpdb->get_results( "SELECT product_id AS value, ec_product.`title` AS label FROM ec_product ORDER BY ec_product.`title` ASC LIMIT 2000" );
	}
	
	private function get_products_model_cdata( ){
		global $wpdb;
		return $wpdb->get_results( "SELECT model_number AS value, ec_product.`title` AS label FROM ec_product ORDER BY ec_product.`title` ASC LIMIT 2000" );
	}
	
	public function load_scripts( ){
		
		if( current_user_can( 'manage_options' ) ){
			$https_link = "";
			if( class_exists( "WordPressHTTPS" ) ){
				$https_class = new WordPressHTTPS( );
				$https_link = $https_class->makeUrlHttps( admin_url( 'admin-ajax.php' ) );
			}else{
				$https_link = str_replace( "http://", "https://", admin_url( 'admin-ajax.php' ) );
			}
			
			wp_register_style( 'wp_easycart_deactivate_css', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/css/deactivate.css' ), array( ), EC_CURRENT_VERSION );
			wp_enqueue_style( 'wp_easycart_deactivate_css' );
			
			wp_register_script( 'wp_easycart_deactivate_js', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/js/deactivate.js' ), array( 'jquery' ), EC_CURRENT_VERSION );
			wp_enqueue_script( 'wp_easycart_deactivate_js' );
			
			if( is_ssl( ) ){
				wp_localize_script( 'wp_easycart_deactivate_js', 'ajax_object', array( 'ajax_url' => $https_link ) );
			}else{
				wp_localize_script( 'wp_easycart_deactivate_js', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
			}
		}
		
		if( current_user_can( 'manage_options' ) && isset( $_GET['page'] ) && ( substr( $_GET['page'], 0, 11 ) == "wp-easycart" || $_GET['page'] == 'ec_adminv2' ) ){
			
			$https_link = "";
			if( class_exists( "WordPressHTTPS" ) ){
				$https_class = new WordPressHTTPS( );
				$https_link = $https_class->makeUrlHttps( admin_url( 'admin-ajax.php' ) );
			}else{
				$https_link = str_replace( "http://", "https://", admin_url( 'admin-ajax.php' ) );
			}
			
			wp_enqueue_media( );
			
			wp_enqueue_style( 'wp_easycart_select2_css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css' );
			wp_enqueue_script( 'wp_easycart_select2_js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array('jquery') );
			
			wp_register_script( 'wp_easycart_validation_js', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/js/validation.js' ), array( 'jquery' ), EC_CURRENT_VERSION );
			wp_enqueue_script( 'wp_easycart_validation_js' );
            
            wp_register_script( 'wp_easycart_admin_js', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/js/admin.js' ), array( 'jquery', 'jquery-ui-sortable' ), EC_CURRENT_VERSION );
            wp_enqueue_script( 'wp_easycart_admin_js' );
 			
			if( is_ssl( ) ){
				wp_localize_script( 'wp_easycart_admin_js', 'ajax_object', array( 'ajax_url' => $https_link ) );
			}else{
				wp_localize_script( 'wp_easycart_admin_js', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
			}
			
            if( isset( $_GET['page'] ) && $_GET['page'] == 'wp-easycart-dashboard' ){
                wp_register_script( 'wp_easycart_moment_js', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/js/moment.min.js' ), array( 'jquery' ), EC_CURRENT_VERSION );
                wp_enqueue_script( 'wp_easycart_moment_js' );
                
                wp_register_script( 'wp_easycart_charts_js', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/js/Chart.js' ), array( 'jquery' ), EC_CURRENT_VERSION );
                wp_enqueue_script( 'wp_easycart_charts_js' );
                
                wp_register_script( 'wp_easycart_daterange_js', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/js/daterangepicker.min.js' ), array( 'jquery' ), EC_CURRENT_VERSION );
                wp_enqueue_script( 'wp_easycart_daterange_js' );

                wp_enqueue_style( 'wp_easycart_daterange_css', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/css/daterangepicker.css' ) );
            
            }
            
            wp_enqueue_script( 'wp-color-picker' );
            wp_enqueue_style( 'wp-color-picker' );
            
            $color_picker_strings = array(
                'clear'            => __( 'Clear', 'wp-easycart' ),
                'clearAriaLabel'   => __( 'Clear color', 'wp-easycart' ),
                'defaultString'    => __( 'Default', 'wp-easycart' ),
                'defaultAriaLabel' => __( 'Select default color', 'wp-easycart' ),
                'pick'             => __( 'Select Color', 'wp-easycart' ),
                'defaultLabel'     => __( 'Color value', 'wp-easycart' ),
            );
            wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $color_picker_strings );
			
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_register_style( 'wpeasycart-jquery-ui-css', 'https://code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css' );
            
			wp_enqueue_style( 'wpeasycart-jquery-ui-css' );
			wp_enqueue_style( 'wp-color-picker' );
			
			if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-products" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "category" ){
				wp_register_script( 'wp_easycart_admin_category_js', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/js/category.js' ), array( 'jquery', 'jquery-ui-sortable' ), EC_CURRENT_VERSION );
				wp_enqueue_script( 'wp_easycart_admin_category_js' );	
			}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-orders" && ( !isset( $_GET['subpage'] ) || $_GET['subpage'] == "orders" ) ){
				wp_register_script( 'wp_easycart_admin_orders_js', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/js/orders.js' ), array( 'jquery', 'jquery-ui-datepicker' ), EC_CURRENT_VERSION );
				wp_enqueue_script( 'wp_easycart_admin_orders_js' );
			}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-products" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "menus" ){
				wp_register_script( 'wp_easycart_admin_menus_js', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/js/menus.js' ), array( 'jquery' ), EC_CURRENT_VERSION );
				wp_enqueue_script( 'wp_easycart_admin_menus_js' );
			}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-products" && isset( $_GET['subpage'] ) && ( $_GET['subpage'] == "option" || $_GET['subpage'] == "optionitems" ) ){
				wp_register_script( 'wp_easycart_admin_option_js', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/js/option.js' ), array( 'jquery', 'jquery-ui-sortable' ), EC_CURRENT_VERSION );
				wp_enqueue_script( 'wp_easycart_admin_option_js' );
			}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-products" && ( !isset( $_GET['subpage'] ) || $_GET['subpage'] == "products" ) ){
				wp_register_script( 'wp_easycart_admin_product_js', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/js/products.js' ), array( 'jquery' ), EC_CURRENT_VERSION );
				wp_enqueue_script( 'wp_easycart_admin_product_js' );
			}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-users" && ( !isset( $_GET['subpage'] ) || $_GET['subpage'] == "accounts" || $_GET['subpage'] == "user-roles" ) ){
				wp_register_script( 'wp_easycart_admin_users_js', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/js/users.js' ), array( 'jquery' ), EC_CURRENT_VERSION );
				wp_enqueue_script( 'wp_easycart_admin_users_js' );
			}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && ( !isset( $_GET['subpage'] ) || $_GET['subpage'] == "initial-setup" ) ){
				wp_register_script( 'wp_easycart_admin_initial_setup_js', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/js/initial-setup.js' ), array( 'jquery' ), EC_CURRENT_VERSION );
				wp_enqueue_script( 'wp_easycart_admin_initial_setup_js' );
			}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "products" ){
				wp_register_script( 'wp_easycart_admin_products_js', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/js/products.js' ), array( 'jquery', 'jquery-ui-sortable' ), EC_CURRENT_VERSION );
				wp_enqueue_script( 'wp_easycart_admin_products_js' );
			}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "tax" ){
				wp_register_script( 'wp_easycart_admin_tax_js', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/js/tax.js' ), array( 'jquery' ), EC_CURRENT_VERSION );
				wp_enqueue_script( 'wp_easycart_admin_tax_js' );
			}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "shipping-settings" ){
				wp_register_script( 'wp_easycart_admin_shipping_settings_js', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/js/shipping-settings.js' ), array( 'jquery' ), EC_CURRENT_VERSION );
				wp_enqueue_script( 'wp_easycart_admin_shipping_settings_js' );
			}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "shipping-rates" ){
				wp_register_script( 'wp_easycart_admin_shipping_rates_js', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/js/shipping-rates.js' ), array( 'jquery' ), EC_CURRENT_VERSION );
				wp_enqueue_script( 'wp_easycart_admin_shipping_rates_js' );
			}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "payment" ){
				wp_register_script( 'wp_easycart_admin_payment_js', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/js/payment.js' ), array( 'jquery' ), EC_CURRENT_VERSION );
				wp_enqueue_script( 'wp_easycart_admin_payment_js' );
			}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "checkout" ){
				wp_register_script( 'wp_easycart_admin_checkout_js', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/js/checkout.js' ), array( 'jquery' ), EC_CURRENT_VERSION );
				wp_enqueue_script( 'wp_easycart_admin_checkout_js' );
			}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "account" ){
				wp_register_script( 'wp_easycart_admin_account_js', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/js/account.js' ), array( 'jquery' ), EC_CURRENT_VERSION );
				wp_enqueue_script( 'wp_easycart_admin_account_js' );
			}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "miscellaneous" ){
				wp_register_script( 'wp_easycart_admin_miscellaneous_js', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/js/miscellaneous.js' ), array( 'jquery' ), EC_CURRENT_VERSION );
				wp_enqueue_script( 'wp_easycart_admin_miscellaneous_js' );
			}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "language-editor" ){
				wp_register_script( 'wp_easycart_admin_language_editor_js', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/js/language-editor.js' ), array( 'jquery' ), EC_CURRENT_VERSION );
				wp_enqueue_script( 'wp_easycart_admin_language_editor_js' );
			}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "design" ){
				wp_register_script( 'wp_easycart_admin_design_js', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/js/design.js' ), array( 'jquery' ), EC_CURRENT_VERSION );
				wp_enqueue_script( 'wp_easycart_admin_design_js' );
			}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "third-party" ){
				wp_register_script( 'wp_easycart_admin_third_party_js', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/js/third-party.js' ), array( 'jquery' ), EC_CURRENT_VERSION );
				wp_enqueue_script( 'wp_easycart_admin_third_party_js' );
			}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "email-setup" ){
				wp_register_script( 'wp_easycart_admin_email_settings_js', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/js/email-settings.js' ), array( 'jquery' ), EC_CURRENT_VERSION );
				wp_enqueue_script( 'wp_easycart_admin_email_settings_js' );
			}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "cart-importer" ){
				wp_register_script( 'wp_easycart_admin_cart_importer_js', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/js/cart-importer.js' ), array( 'jquery' ), EC_CURRENT_VERSION );
				wp_enqueue_script( 'wp_easycart_admin_cart_importer_js' );
			}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && isset( $_GET['subpage'] ) && $_GET['subpage'] == "pricepoint" ){
				wp_register_script( 'wp_easycart_admin_pricepoint_js', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/js/pricepoint.js' ), array( 'jquery' ), EC_CURRENT_VERSION );
				wp_enqueue_script( 'wp_easycart_admin_pricepoint_js' );
			}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" && ( !isset( $_GET['subpage'] ) || $_GET['subpage'] == "setup-wizard" ) ){
				wp_register_script( 'wp_easycart_admin_product_js', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/js/products.js' ), array( 'jquery' ), EC_CURRENT_VERSION );
				wp_enqueue_script( 'wp_easycart_admin_product_js' );
			}
			
			wp_register_style( 'wp_easycart_admin_css', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/css/admin.css' ), array( ), EC_CURRENT_VERSION );
			wp_enqueue_style( 'wp_easycart_admin_css' );
			
			wp_register_style( 'wp_easycart_upgrade_css', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/css/upgrade.css' ), array( ), EC_CURRENT_VERSION );
			wp_enqueue_style( 'wp_easycart_upgrade_css' );
			
			add_editor_style( );
			add_thickbox( );
			wp_enqueue_script('common');
			wp_enqueue_script( 'post' );
			wp_enqueue_script('jquery-color');
			wp_enqueue_script( 'editor' );
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_script( 'tiny_mce' );
			wp_enqueue_script( 'editorremov' );
			wp_enqueue_script( 'editor-functions' );
				
		}
		
		wp_register_style( 'wp_easycart_editor_css', plugins_url( EC_PLUGIN_DIRECTORY . '/admin/css/editor.css' ), array( ), EC_CURRENT_VERSION );
		wp_enqueue_style( 'wp_easycart_editor_css' );
		
		wp_localize_script( 'wp_easycart_admin_js', 'wp_easycart_admin_vars', array(
			'ajaxURL' => admin_url( 'admin-ajax.php' ),
			'ec_option_newsletter_done' => ( ( get_option( 'ec_option_bcc_email_addresses' ) != 'youremail@url.com' || get_option( 'ec_option_newsletter_done' ) ) ? 1 : 0 ),
			'ec_option_currency'		=> get_option( 'ec_option_currency' )
		) );
	}
	
	public function wp_easycart_pro_check( ){
		$pro_plugin_base = 'wp-easycart-pro/wp-easycart-admin-pro.php';
		$pro_plugin_file = WP_PLUGIN_DIR . '/' . $pro_plugin_base;
		if( file_exists( $pro_plugin_file ) && !is_plugin_active( $pro_plugin_base ) ) {
			echo '<div class="updated">';
			echo '<p>' . __( 'WP EasyCart PRO is installed but NOT ACTIVATED. Please', 'wp-easycart' ) . ' <a href="' . $this->get_pro_activation_link( ) . '">' . __( 'click here to activate your WP EasyCart PRO plugin', 'wp-easycart' ) . '</a>.</p>';
			echo '</div>';
		}
	}
	
	public function elementor_check( ){
		if( !get_option( 'ec_option_hide_elementor_notice' ) ){
			$elementor_plugin_base = 'elementor/elementor.php';
			$elementor_plugin_file = WP_PLUGIN_DIR . '/' . $elementor_plugin_base;
			if( file_exists( $elementor_plugin_file ) && is_plugin_active( $elementor_plugin_base ) ) {
				echo '<div class="updated" style="position:relative;" id="wp_easycart_elementor_notice">';
				echo '<p>' . sprintf( __( 'Elementor can cause issues with WP EasyCart. If you are having issues, please %s read more by clicking here %s as the issue is easy to resolve!', 'wp-easycart' ), '<a href="http://docs.wpeasycart.com/wp-easycart-administrative-console-guide/?section=elementor-page-builder" target="_blank">', '</a>' ) . '</p>';
				echo '<button type="button" class="notice-dismiss" onclick="wp_easycart_hide_elementor_notice( );"><span class="screen-reader-text">' . __( 'Dismiss this notice.', 'wp-easycart' ) . '</span></button>';
				echo '</div>';
				echo "<script>
				function wp_easycart_hide_elementor_notice( ){
					jQuery( document.getElementById( 'wp_easycart_elementor_notice' ) ).fadeOut( 'slow' );
					var data = {
						action: 'ec_admin_ajax_hide_elementor_notice'
					};
					jQuery.ajax({url: ajax_object.ajax_url, type: 'post', data: data, success: function(data){ } } );
				}
				</script>";
			}
		}
	}
    
    public function square_check( ){
        if( current_user_can( 'manage_options' ) && get_option( 'ec_option_payment_process_method' ) == 'square' && !get_option( 'ec_option_square_is_sandbox' ) && get_option( 'ec_option_square_access_token' ) == '' ){
            echo '<div class="error notice"><p>' . __( 'Your Square connection is no longer active and you cannot receive payments. Please visit the Settings -> Payments and reconnect to begin processing payments again.', 'wp-easycart' ) . '</p></div>';
            
        }else if( current_user_can( 'manage_options' ) && get_option( 'ec_option_payment_process_method' ) == 'square' && !get_option( 'ec_option_square_is_sandbox' ) && get_option( 'ec_option_square_access_token' ) != '' && strtotime( '+15 day', strtotime( get_option( 'ec_option_square_token_expires' ) ) ) < time( ) ){
            echo '<div class="error notice"><p>' . __( 'Your Square connection has expired, please visit the Settings -> Payments and renew the connection to begin processing payments again.', 'wp-easycart' ) . '</p></div>';
            
        }
    }
	
    public function database_check( ){
        if( !$this->database_check_current( ) ){
            $db_manager = new ec_db_manager( );
            $errors = $db_manager->verify_db( );
            if( count( $errors ) ){
                echo '<div class="error notice">';
                    echo '<p>' . __( 'We have found problems with your WP EasyCart database structure.', 'wp-easycart' ) . ' <a href="admin.php?page=wp-easycart-status&subpage=store-status&ec_admin_form_action=repair-database">' . __( 'Click to Repair!', 'wp-easycart' ) . '</a> ' . __( 'If you would like to dismiss this notice', 'wp-easycart' ) . ', <a href="admin.php?page=wp-easycart-status&subpage=store-status&ec_admin_form_action=dismiss-database-error">' . __( 'please click here', 'wp-easycart' ) . '</a>.</p>';
                    echo '<p><span id="wpeasycart_database_errors_min">' . __( 'For Complete Details', 'wp-easycart' ) . ', <a href="#" onclick="jQuery( \'#wpeasycart_database_errors\' ).show( ); jQuery( \'#wpeasycart_database_errors_min\' ).hide( ); return false;">' . __( 'Click Here', 'wp-easycart' ) . '</a></span><ul id="wpeasycart_database_errors" style="display:none;">';
                    foreach( $errors as $error ){
                        echo '<li>' . $error['error'] . '</li>';
                    }
                    echo '</ul></p>';
                echo '</div>';
            }
            
        }
    }
    
    public function database_check_current( ){
        if( !get_option( 'ec_option_db_version_verified' ) || version_compare( str_replace( '_', '.', EC_CURRENT_VERSION ), get_option( 'ec_option_db_version_verified' ), '<' ) ){
            return false;
        }
        return true;
    }
	
	public function get_pro_activation_link( ){ 
		$pro_plugin_file = WP_PLUGIN_DIR . '/wp-easycart-pro/wp-easycart-admin-pro.php';
		if( strpos( $pro_plugin_file, '/' ) ){
			$pro_plugin_file = str_replace( '/', '%2F', $pro_plugin_file );
		}
		$activate_url = sprintf( admin_url( 'plugins.php?action=activate&plugin=%s&plugin_status=all&paged=1&s' ), $pro_plugin_file ); 
		$_REQUEST['plugin'] = $pro_plugin_file;
		$activate_url = wp_nonce_url( $activate_url, 'activate-plugin_' . $pro_plugin_file );
		return $activate_url;
	}
	
	public function print_admin_message( ){
		$success_messages = apply_filters( 'wp_easycart_admin_success_messages', array( ) );
		$warning_messages = apply_filters( 'wp_easycart_admin_warning_messages', array( ) );
		$error_messages = apply_filters( 'wp_easycart_admin_error_messages', array( ) );
		
		if( count( $success_messages ) > 0 ){
			echo '<div id="ec_message" class="ec_admin_message_success"><div class="dashicons-before dashicons-thumbs-up"></div>' . implode( ', ', $success_messages ) . '</div>';
		
		}else if( count( $warning_messages ) > 0 ){
			echo '<div id="ec_message" class="ec_admin_message_warning"><div class="dashicons-before dashicons-warning"></div>' . implode( ', ', $warning_messages ) . '</div>';
		
		}else if( count( $error_messages ) > 0 ){
			echo '<div id="ec_message" class="ec_admin_message_error"><div class="dashicons-before dashicons-thumbs-down"></div>' . implode( ', ', $error_messages ) . '</div>';
		
		}
		
		if( !get_option( 'ec_option_allow_tracking' ) ){
			echo '<div id="ec_message" class="ec_admin_message_success ec_admin_allow_tracking">' . sprintf( __( 'Please help improve WP EasyCart by sending us %s for your plugin.', 'wp-easycart' ), '<a href="https://www.wpeasycart.com/terms-and-conditions/" target="_blank">' . __( 'basic usage data', 'wp-easycart' ) . '</a>' ) . ' <a href="admin.php?page=wp-easycart-settings&subpage=miscellaneous&ec_admin_form_action=allow-usage-tracking" class="ec_admin_tracking_allow" onclick="wp_easycart_allow_tracking( ); jQuery( this ).parent( ).fadeOut( ); return false;">' . __( 'allow', 'wp-easycart' ) . '</a><a href="admin.php?page=wp-easycart-settings&subpage=miscellaneous&ec_admin_form_action=deny-usage-tracking" class="ec_admin_tracking_deny" onclick="wp_easycart_deny_tracking( ); jQuery( this ).parent( ).fadeOut( ); return false;">' . __( 'deny', 'wp-easycart' ) . '</a></div>';
		}
	}
	
	public function load_upsell_image( ){
		if( isset( $_GET['page'] ) && $_GET['page'] == 'wp-easycart-settings' && !isset( $_GET['subpage'] ) )
			return;
		
		if( isset( $_GET['subpage'] ) && $_GET['subpage'] == 'setup-wizard' )
			return;
		
		if( isset( $_GET['page'] ) && $_GET['page'] == 'wp-easycart-dashboard' )
			return;
		
		if( isset( $_GET['page'] ) && $_GET['page'] == 'wp-easycart-license-status' )
			return;
		
		echo '<div style="width:100%; text-align:center; max-width:100%;"><a href="admin.php?page=wp-easycart-registration&ec_trial=start"><img src="' . plugins_url( 'wp-easycart/admin/images/banner-ad-' . rand(1,2) . '-750x100.jpg?v=' . EC_CURRENT_VERSION ) . '" style="max-width:100%; height:auto;" alt="' . __( 'Start Your PRO Trial Today!', 'wp-easycart' ) . '" /></a></div>';
	}
	
	public function load_upsell_popup( ){
		echo '<div id="ec_admin_upsell_popup"><div class="ec_admin_upsell_popup_close"><a href="#" onclick="hide_pro_required( ); return false;"><div class="dashicons-before dashicons-dismiss"></div></a></div><div class="ec_admin_upsell_popup_inner"><div class="ec_admin_upsell_popup_content">';
		$this->show_upgrade( );
		echo '<div style="clear:both;"></div></div></div></div>';
		echo '<script>jQuery( document.getElementById( \'ec_admin_upsell_popup\' ) ).appendTo( document.body );</script>';
	}
	
	public function show_upgrade( ){
		include( apply_filters( 'wp_easycart_admin_upgrade_file', WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/upgrade/upgrade-screen.php' ) );
	}
	
	public function load_new_slideout( $slide ){
		if( $slide == 'product' ){
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/products/products/new-product-slideout.php' );
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/products/products/quick-edit-product-slideout.php' );
		
		}else if( $slide == 'manufacturer' ){
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/products/manufacturers/new-manufacturer-slideout.php' );
		
		}else if( $slide == 'optionset' ){
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/products/options/new-optionset-slideout.php' );
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/products/options/new-optionitem-slideout.php' );
		
		}else if( $slide == 'advanced-optionset' ){
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/products/options/new-advanced-optionset-slideout.php' );
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/products/options/new-advanced-optionitem-slideout.php' );
		
		}else if( $slide == 'order' ){
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/orders/orders/order-quick-edit-slideout.php' );
		
		}
	}
	
	public function filter_option_type( ){
		return 'upgrade_required';
	}
	
	public function redirect( $page, $subpage, $args ){
		$url = $this->get_redirect_url( $page, $subpage, $args );
		wp_redirect( $url );
		exit( );
	}
	
	private function get_redirect_url( $page, $subpage, $args ){
		$url = "admin.php?page=" . $page . "&subpage=" . $subpage;
		foreach( $args as $key => $value ){
			$url .= "&" . $key . '=' . $value;
		}
		return $url;
	}
	
	public function start_pro_trial( ){
		
		// Required Trial Info
		$current_user = wp_get_current_user( );
		$name = $current_user->user_firstname . " " . $current_user->user_lastname;
		$email = $current_user->user_email;
		
		// Try and Install PRO
		if( !file_exists( WP_PLUGIN_DIR . '/' . EC_PLUGIN_DIRECTORY . '-pro/wp-easycart-admin-pro.php' ) ){
			$this->install_pro_plugin(  );
		}
		
		if( !file_exists( WP_PLUGIN_DIR . '/' . EC_PLUGIN_DIRECTORY . '-pro/wp-easycart-admin-pro.php' ) ){
			echo __( "Error installing the WP EasyCart PRO plugin. Please try again or contact support@wpeasycart.com for assistance.", 'wp-easyart' );
			die( );
		}
		
		if( !is_plugin_active( 'wp-easycart-pro/wp-easycart-admin-pro.php' ) ){
			activate_plugin( WP_PLUGIN_DIR . '/' . EC_PLUGIN_DIRECTORY . '-pro/wp-easycart-admin-pro.php', NULL, 0, 1 );
		}
		
		if( !is_plugin_active( 'wp-easycart-pro/wp-easycart-admin-pro.php' ) ){
			echo __( "Error activating WP EasyCart PRO, please visit your plugins page and click activate or contact support@wpeasycart.com for assistance.", 'wp-easycart' );
			die( );
		}
		
		if( !class_exists( 'ec_license_manager' ) ){
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '-pro/license/ec_license_manager.php' );
		}
		
		// Get Key and License the Trial
		$license_key = $this->create_trial_license( $name, $email );
		if( !$license_key ){
			echo __( "Error creating trial key. Something may be wrong with our server, please contact support@wpeasycart.com for assistance.", 'wp-easyart' ) . "<br>";
			die( );
			
		}else if( $license_key == "key_exists" ){
			// Should load from 
			
		}else{
			$license_manager = new ec_license_manager( );
			$license_manager->ec_activate_license( $name, $email, $license_key );
		}
	}
	
	private function install_pro_plugin( $is_trial = 1 ){
		
		// Echo out html for screen
		echo '<html>';
			echo '<head>';
				echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
				echo '<title>' . __( 'Install WP EasyCart PRO', 'wp-easycart' ) . '</title>';
				echo '<style type="text/css">';
					echo 'html{ height:100%; margin:0; padding:0; }';
					echo 'body{ display:block; height:100%; margin:0; padding:0; color:#444; font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif; font-size:13px; line-height:1.4em; min-width:600px; }';
					echo 'img{ display:block; margin:0 auto; }';
					echo '.box-container{ -moz-box-shadow:0px 0px 100px rgba(0, 0, 0, 0.5); -webkit-box-shadow:0px 0px 100px rgba(0, 0, 0, 0.5); box-shadow:0px 0px 100px rgba(0, 0, 0, 0.5); -moz-border-radius:10px; -webkit-border-radius:10px; border-radius:10px; position:fixed; top:15%; left:50%; width:550px; margin:0 0 0 -225px; background:#FFF; overflow:auto; padding:0px; -webkit-box-sizing:border-box; -moz-box-sizing:border-box; box-sizing:border-box; z-index:99999; max-height:420px; }';
					echo '.box-container > div{ padding:25px; border-color:#FFFFFF; border-width:4px; border-style:solid; border-radius:0px; }';
					echo 'h1{ font-weight:normal; margin:10px 0 25px; text-align:center; font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif; font-size:26px; }';
					echo 'a{ background:#79af40; margin:15px auto 0; display:block; text-align:center; color:#FFF; padding:12px 20px; border-radius:5px; text-decoration:none; font-size:16px; font-weight:normal; }';
					echo 'a:hover{ background:#92c845; }';
				echo '</style>';
			echo '</head>';
			echo '<body style="background:#f7f4e8;" bgcolor="#f7f4e8">';
				echo '<div class="box-container"><div>';
				echo '<img src="https://www.wpeasycart.com/wp-content/uploads/2018/01/easycart-logo-1-11-2018.png" alt="WP EasyCart" title="WP EasyCart" />';
		
		$url = "https://connect.wpeasycart.com/downloads/professional-admin/wp-easycart-pro.zip";
		$method = '';

		if ( false === ( $creds = request_filesystem_credentials( esc_url_raw( $url ), $method, false, false, array() ) ) ) {
			return false;
		}

		if ( ! WP_Filesystem( $creds ) ) {
			request_filesystem_credentials( esc_url_raw( $url ), $method, true, false, array() );
			return false;
		}

		if ( ! class_exists( 'Plugin_Upgrader', false ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		}

		$title     = "Installing WP EasyCart PRO";
		$skin_args = array(
			'type'   => 'web',
			'title'  => sprintf( $title, 'WP EasyCart PRO' ),
			'url'    => esc_url_raw( $url ),
			'nonce'  => 'install-plugin_wp-easycart-pro',
			'plugin' => 'wp-easycart-pro',
			'extra'  => array( ),
		);
		
		add_filter( 'install_plugin_complete_actions', array( $this, 'remove_actions_install', 3 ) );
		$skin = new Plugin_Installer_Skin( $skin_args );
		$upgrader = new Plugin_Upgrader( $skin );
		$upgrader->install( $skin_args['url'] );
		
		if( $is_trial ){
			echo '<a href="admin.php?page=wp-easycart-registration&ec_trial=start">' . __( 'CLICK HERE TO COMPLETE INSTALLATION', 'wp-easycart' ) . '</a>';
		}else{
			echo '<a href="admin.php?page=wp-easycart-registration&ec_install=pro">' . __( 'CLICK HERE TO COMPLETE INSTALLATION', 'wp-easycart' ) . '</a>';
		}
		
		echo '</div></div></body></html>';
		
		die( );
	}
	
	private function remove_actions_install( $actions, $api, $file ){
		return array( '' );
	}
	
	private function create_trial_license( $name, $email ){
		$action_url = 'https://licensing.wpeasycart.com/trial/start/start.php';

		$url = site_url( );
		$url = str_replace( 'http://', '', $url );
		$url = str_replace( 'https://', '', $url );
		$url = str_replace( 'www.', '', $url );
		
		$action_url .= '?ec_action=start_trial';
		$action_url .= '&site_url=' . esc_attr( $url );
		$action_url .= '&customername=' . esc_attr( $name );
		$action_url .= '&customeremail=' . esc_attr( $email );
		
		$response = wp_remote_get( $action_url, array( 'timeout' => 30, 'sslverify' => false ) );
		if ( is_wp_error( $response ) ) {
			return false;
		}
		return wp_remote_retrieve_body( $response );
	}
    
    public function adjust_hex_brightness( $hexCode, $adjustPercent ){
        $hexCode = ltrim($hexCode, '#');
        if( strlen( $hexCode ) == 3 ){
            $hexCode = $hexCode[0] . $hexCode[0] . $hexCode[1] . $hexCode[1] . $hexCode[2] . $hexCode[2];
        }
        $hexCode = array_map( 'hexdec', str_split( $hexCode, 2 ) );
        foreach( $hexCode as & $color ){
            $adjustableLimit = $adjustPercent < 0 ? $color : 255 - $color;
            $adjustAmount = ceil( $adjustableLimit * $adjustPercent );
            $color = str_pad( dechex( $color + $adjustAmount ), 2, '0', STR_PAD_LEFT );
        }
        return '#' . implode( $hexCode );
    }
	
	public function load_toggle_group( $id, $change_func, $enabled, $title, $subtitle, $row_id = false, $default_show = true ){
		echo '<div class="wp-easycart-admin-toggle-group"' . ( ( $row_id ) ? ' id="' . $row_id . '"' : '' ) . ( ( !$default_show ) ? ' style="display:none;"' : '' ) . '>
			<input type="checkbox" name="' . $id . '" id="' . $id . '" onchange="' . $change_func . '( jQuery( this ) );" value="1"';
			if( $enabled == "1" ){ 
				echo " checked=\"checked\""; 
			}
			echo '/> 
			<label for="' . $id . '">
				<span class="wp-easycart-admin-aural">' . __( 'Show', 'wp-easycart' ) . ': </span> ' . $title . ' <div class="subtitle">' . $subtitle . '</div>
			</label>
			<div class="wp-easycart-admin-onoffswitch wp-easycart-admin-pull-right" aria-hidden="true">
				<div class="wp-easycart-admin-onoffswitch-label">
					<div class="wp-easycart-admin-onoffswitch-inner"></div>
					<div class="wp-easycart-admin-onoffswitch-switch">
						<div class="wp-easycart-admin-dual-ring wp_easycart_toggle_saving" style="display: none;"></div>
						<div class="dashicons-before dashicons-yes-alt wp_easycart_toggle_saved" style="display: none;"></div>
					</div>
				</div>
			</div>
		</div>';
	}
	
	public function load_toggle_group_text( $id, $change_func, $value, $title, $subtitle, $placeholder, $row_id, $default_show = true, $last = false, $show_loader = true ){
		echo '<div class="wp-easycart-admin-toggle-group-text" style="';
		if( $last ){
			echo 'margin-bottom:0px !important;';
		}
		if( !$default_show ){ 
			echo ' display:none;';
		}
		echo '" id="' . $row_id . '">
			<label for="' . $id . '">
				' . $title . '
				<div class="subtitle">' . $subtitle . '</div>
			</label>
			<fieldset class="wp-easycart-admin-field-container">
				<input name="' . $id . '" id="' . $id . '" type="text" placeholder="' . $placeholder . '" value="' . $value . '" onchange="return ' . $change_func . '( jQuery( this ) );" class="wp-easycart-admin-field" />';
        if( $show_loader ){
        echo '
				<div class="wp-easycart-admin-icons-container">
					<div class="wp-easycart-admin-icon-close" onclick="return ' . $change_func . '( jQuery( this ) );">
						<div class="wp-easycart-admin-icon-close-check"></div>
						<div class="wp-easycart-admin-dual-ring wp_easycart_toggle_saving" style="display: none;"></div>
						<div class="dashicons-before dashicons-yes-alt wp_easycart_toggle_saved" style="display: none;"></div>
					</div>
				</div>';
        }
        echo '
			</fieldset>
		</div>';
	}
	
	public function load_toggle_group_percentage( $id, $change_func, $value, $title, $subtitle, $placeholder, $row_id, $default_show = true, $last = false, $show_loader = true ){
		if( $value < 1 ){
            $value = $value * 100;
        }
        echo '<div class="wp-easycart-admin-toggle-group-text" style="';
		if( $last ){
			echo 'margin-bottom:0px !important;';
		}
		if( !$default_show ){ 
			echo ' display:none;';
		}
		echo '" id="' . $row_id . '">
			<label for="' . $id . '">
				' . $title . '
				<div class="subtitle">' . $subtitle . '</div>
			</label>
			<fieldset class="wp-easycart-admin-field-container">
				<input name="' . $id . '" id="' . $id . '" type="text" placeholder="' . $placeholder . '" value="' . $value . '" onchange="return ' . $change_func . '( jQuery( this ) );" class="wp-easycart-admin-field" />
                <span class="wp-easycart-admin-field-percentage">%</span>';
        if( $show_loader ){
        echo '
				<div class="wp-easycart-admin-icons-container">
					<div class="wp-easycart-admin-icon-close" onclick="return ' . $change_func . '( jQuery( this ) );">
						<div class="wp-easycart-admin-icon-close-check"></div>
						<div class="wp-easycart-admin-dual-ring wp_easycart_toggle_saving" style="display: none;"></div>
						<div class="dashicons-before dashicons-yes-alt wp_easycart_toggle_saved" style="display: none;"></div>
					</div>
				</div>';
        }
        echo '
			</fieldset>
		</div>';
	}
	
	public function load_toggle_group_textarea( $id, $change_func, $value, $title, $subtitle, $placeholder, $row_id, $default_show = true, $last = false ){
		echo '<div class="wp-easycart-admin-toggle-group-text" style="';
		if( $last ){
			echo 'margin-bottom:0px !important;';
		}
		if( !$default_show ){ 
			echo ' display:none;';
		}
		echo '" id="' . $row_id . '">
			<label for="' . $id . '">
				' . $title . '
				<div class="subtitle">' . $subtitle . '</div>
			</label>
			<fieldset class="wp-easycart-admin-field-container">
				<textarea name="' . $id . '" id="' . $id . '" placeholder="' . $placeholder . '" onchange="return ' . $change_func . '( jQuery( this ) );" class="wp-easycart-admin-field">' . $value . '</textarea>
				<div class="wp-easycart-admin-icons-container">
					<div class="wp-easycart-admin-icon-close" onclick="return ' . $change_func . '( jQuery( this ) );">
						<div class="wp-easycart-admin-icon-close-check"></div>
						<div class="wp-easycart-admin-dual-ring wp_easycart_toggle_saving" style="display: none;"></div>
						<div class="dashicons-before dashicons-yes-alt wp_easycart_toggle_saved" style="display: none;"></div>
					</div>
				</div>
			</fieldset>
		</div>';
	}
	
	public function load_toggle_group_color( $id, $change_func, $value, $title, $subtitle, $placeholder, $row_id, $default_show = true, $last = false ){
		echo '<div class="wp-easycart-admin-toggle-group-text" style="';
		if( $last ){
			echo 'margin-bottom:0px !important;';
		}
		if( !$default_show ){ 
			echo ' display:none;';
		}
		echo '" id="' . $row_id . '">
			<label for="' . $id . '">
				' . $title . '
				<div class="subtitle">' . $subtitle . '</div>
			</label>
			<fieldset class="wp-easycart-admin-field-container">
				<input name="' . $id . '" id="' . $id . '" type="text" placeholder="' . $placeholder . '" value="' . $value . '" onchange="return ' . $change_func . '( jQuery( this ) );" class="wp-easycart-admin-field ec_color_block_input" />
				<div class="wp-easycart-admin-icons-container">
					<div class="wp-easycart-admin-icon-close" onclick="return ' . $change_func . '( jQuery( this ) );">
						<div class="wp-easycart-admin-icon-close-check"></div>
						<div class="wp-easycart-admin-dual-ring wp_easycart_toggle_saving" style="display: none;"></div>
						<div class="dashicons-before dashicons-yes-alt wp_easycart_toggle_saved" style="display: none;"></div>
					</div>
				</div>
			</fieldset>
		</div>';
	}
	
	public function load_toggle_group_image( $id, $change_func, $value, $title, $subtitle, $placeholder, $row_id, $default_show = true, $last = false ){
		echo '<div class="wp-easycart-admin-toggle-group-text" style="';
		if( $last ){
			echo 'margin-bottom:0px !important;';
		}
		if( !$default_show ){ 
			echo ' display:none;';
		}
		echo '" id="' . $row_id . '">
			<label for="' . $id . '">
				' . $title . '
				<div class="subtitle">' . $subtitle . '</div>
			</label>
			<fieldset class="wp-easycart-admin-field-container">
				<input name="' . $id . '" id="' . $id . '" type="text" placeholder="' . $placeholder . '" value="' . $value . '" onchange="return ' . $change_func . '( jQuery( this ) );" class="wp-easycart-admin-field" />
				<input type="button" class="wp_easycart_admin_image_upload_button" data-input-id="' . $id . '" data-image-id="' . $id . '_image" data-delete-id="' . $id . '_remove_link" id="' . $id . '_upload_logo_button" type="button" value="' . __( 'Upload Image', 'wp-easycart' ) . '" />
				<div class="wp-easycart-admin-icons-container">
					<div class="wp-easycart-admin-icon-close" onclick="return ' . $change_func . '( jQuery( this ) );">
						<div class="wp-easycart-admin-icon-close-check"></div>
						<div class="wp-easycart-admin-dual-ring wp_easycart_toggle_saving" style="display: none;"></div>
						<div class="dashicons-before dashicons-yes-alt wp_easycart_toggle_saved" style="display: none;"></div>
					</div>
				</div>
			</fieldset>
		</div>';
		
		echo '<div style="float:left; height:auto; position:relative;">
			<a href="#" onclick="wp_easycart_admin_remove_image( jQuery( this ) ); return false;" id="' . $id . '_remove_link" style="position:absolute; top:13px; right:5px; padding:8px 10px; text-decoration:none; background:#7bb141; border-radius:100px; color:#FFF; font-weight:bold; line-height:1em;' . ( ( $value == '' ) ? ' display:none;' : '' ) . '">delete</a>
			<img src="' . $value . '" id="' . $id . '_image" style="float:left; max-width:100%; max-height:250px; margin:10px 0; padding:10px; border:1px solid #a2ab9f;' . ( ( $value == '' ) ? ' display:none;' : '' ) . '" />
		</div>';
	}
	
	public function load_toggle_group_select( $id, $change_func, $value, $title, $subtitle, $options, $row_id, $default_show = true, $last = false, $multiple = false, $show_loader = true ){
		echo '<div class="wp-easycart-admin-toggle-group-text" style="';
		if( $last ){
			echo 'margin-bottom:0px !important;';
		}
		if( !$default_show ){ 
			echo ' display:none;';
		}
		echo '" id="' . $row_id . '">
			<label for="' . $id . '">
				' . $title . '
				<div class="subtitle">' . $subtitle . '</div>
			</label>
			<fieldset class="wp-easycart-admin-field-container">
				<select name="' . $id . '" id="' . $id . '" onchange="return ' . $change_func . '( jQuery( this ) );" class="wp-easycart-admin-field' . ( ($multiple) ? ' wp-easycart-admin-field-select-multiple' : '' ) . '"' . ( ($multiple) ? ' multiple' : '' ) . '>';
		
		foreach( $options as $option ){
			echo '<option value="' . $option->value . '"' . ( ( ( $multiple && in_array( $option->value, $value ) ) || ( !$multiple && $option->value == $value ) ) ? ' selected="selected"' : '' ) . '>' . $option->label . '</option>';
		}
		
		echo '
				</select>';
        if( $show_loader ){
        echo '
				<div class="wp-easycart-admin-icons-container">
					<div class="wp-easycart-admin-icon-close">
						<div class="wp-easycart-admin-dual-ring wp_easycart_toggle_saving" style="display: none;"></div>
						<div class="dashicons-before dashicons-yes-alt wp_easycart_toggle_saved" style="display: none;"></div>
					</div>
				</div>';
        }
        echo '
			</fieldset>
		</div>';
	}
	
	public function load_editable_table( $table_id, $columns, $data, $actions, $add_new_func, $update_func, $bulk_actions = array( 'delete' => 'Delete Selected' )  ){
		echo '<div class="wp-easycart-editable-table-holder" id="' . $table_id . '">';
			$this->print_editable_table_bulk_actions( $table_id, $bulk_actions );
			echo '<table class="wp-easycart-editable-table pagination-10" data-update-func="' . $update_func . '">';
				$this->print_editable_table_header( $table_id, $columns, $actions, $bulk_actions );
				echo '<tbody>';
					foreach( $data as $item ){
						$this->print_editable_table_row( $table_id, $columns, $item, $actions, $bulk_actions );
					}
					$this->print_editable_table_row_default( $table_id, $columns, $actions, $bulk_actions );
					$this->print_editable_table_row_none( $table_id, $columns, count( $data ), $bulk_actions );
					if( count( $actions ) > 0 ){
					   $this->print_editable_table_add_new( $table_id, $columns, $add_new_func, $bulk_actions );
                    }
				echo '</tbody>';
			echo '</table>';
			$this->print_editable_table_pagination( $table_id, $data );
		echo '</div>';
	}
	
	private function print_editable_table_bulk_actions( $table_id, $bulk_actions ){
        echo '<div class="wp-easycart-editable-table-bulk">';
            if( count( $bulk_actions ) ){
                echo '<select id="' . $table_id . '_bulk_action" data-table-id="' . $table_id . '">';
                    echo '<option value="">' . __( 'Select an Action', 'wp-easycart' ) . '</option>';
                    foreach( $bulk_actions as $bulk_action => $bulk_action_label ){
                        echo '<option value="' . $bulk_action . '">' . $bulk_action_label . '</option>';
                    }
                echo '</select>';
                echo '<button class="wp-easycart-editable-table-bulk-apply" data-table-id="' . $table_id . '">' . __( 'Apply', 'wp-easycart' ) . '</button>';
            }
            echo '<div class="wp-easycart-editable-table-search-bar">';
                echo '<input type="search" name="search" pattern=".*\S.*">';
                echo '<button class="wp-easycart-editable-table-search-btn" onclick="return false;">';
                    echo '<span>' . __( 'Search', 'wp-easycart' ) . '</span>';
                echo '</button>';
            echo '</div>';
        echo '</div>';
	}
	
	private function print_editable_table_pagination( $table_id, $data ){
		echo '<div class="wp-easycart-editable-table-pagination">';
			echo '<select class="' . $table_id . '_per_page">';
				echo '<option value="10" selected="selected">10 ' . __( 'rows', 'wp-easycart' ) . '</option>';
				echo '<option value="25">25 ' . __( 'rows', 'wp-easycart' ) . '</option>';
				echo '<option value="50">50 ' . __( 'rows', 'wp-easycart' ) . '</option>';
				echo '<option value="100">100 ' . __( 'rows', 'wp-easycart' ) . '</option>';
			echo '</select>';
			echo '<ul>';
				if( ceil( count( $data ) / 10 ) > 3 ){
					echo '<li data-page="1" class="wp-easycart-editable-table-pagination-first">&#60;&#60;</li>';
				}
				for( $i=1; $i<=ceil( count( $data ) / 10 ); $i++ ){
					echo '<li data-page="' . $i . '" class="wp-easycart-editable-page-item' . ( ( $i==1 ) ? ' selected' : '' ) . '">' . $i . '</li>';
				}
				if( ceil( count( $data ) / 10 ) > 3 ){
					echo '<li data-page="' . ceil( count( $data ) / 10 ) . '" class="wp-easycart-editable-table-pagination-last">&#62;&#62;</li>';
				}
			echo '</ul>';
			echo '<div class="wp-easycart-editable-table-paging">' . sprintf( __( 'Showing %s of %s', 'wp-easycart' ), '<span class="wp-easycart-editable-table-paging-showing">' . ( ( count( $data ) > 0 ) ? 1 : 0 ) . '-' . ( ( count( $data ) > 10 ) ? 10 : count( $data ) ) . '</span>', '<span class="wp-easycart-editable-table-paging-total">' . count( $data ) . '</span>' ) . '</div>';
		echo '</div>';
	}
	
	private function print_editable_table_header( $table_id, $columns, $actions, $bulk_actions ){
		echo '<thead>';
			echo '<tr>';
                if( count( $bulk_actions ) ){
				    echo '<th><input type="checkbox" class="wpeasycart-editable-table-select-all" /></th>';
                }
                foreach( $columns as $column ){
					echo '<th style="text-align:' . ( ( isset( $column['labelpos'] ) ) ? $column['labelpos'] : 'left' ) . ';' . ( ( isset( $column['width'] ) ) ? ' width:' . $column['width'] . ';' : '' ) . '"' . ( ( !isset( $column['sort'] ) || $column['sort'] ) ? ' class="sortable"' : '' ) . ' data-column="' . $column['id'] . '" data-type="' . ( ( isset( $column['type'] ) ) ? $column['type'] : '' ) . '">' . $column['label'] . ( ( !isset( $column['sort'] ) || $column['sort'] ) ? '<span class="dashicons dashicons-sort wpeasycart-editable-table-sort"></span>' : '' ) . '</th>';
				}
                if( count( $actions ) > 0 ){
				    echo '<th></th>';
                }
			echo '</tr>';
		echo '</thead>';
	}
	
	private function print_editable_table_row_default( $table_id, $columns, $actions, $bulk_actions ){
		echo '<tr class="wp-easycart-editable-table-row-default">';
        if( count( $bulk_actions ) ){
            echo '<td><input type="checkbox" id="' . $table_id . '_" class="wp-easycart-editable-table-select-item" /></td>';
        }
        foreach( $columns as $column ){
			$this->print_editable_table_column( $table_id, $column, ( ( isset( $column['default'] ) ) ? $column['default'] : '' ), '' );
		}
		$this->print_editable_table_actions( $table_id, $actions, '' );
		echo '</tr>';
	}
	
	private function print_editable_table_row( $table_id, $columns, $item, $actions, $bulk_actions ){
		echo '<tr class="wp-easycart-editable-table-row" data-id="' . $item->id . '">';
		if( count( $bulk_actions ) ){
            echo '<td><input type="checkbox" id="' . $table_id . '_' . $item->id . '" class="wp-easycart-editable-table-select-item" /></td>';
        }
        foreach( $columns as $column ){
            $this->print_editable_table_column( $table_id, $column, $item->{$column['id']}, $item->id );
		}
		$this->print_editable_table_actions( $table_id, $actions, $item->id );
		echo '</tr>';
	}
	
	private function print_editable_table_row_none( $table_id, $columns, $row_count, $bulk_actions ){
        $add_columns = ( count( $bulk_actions ) > 0 ) ? 2 : 1;
		echo '<tr class="wp-easycart-editable-table-row-none"' . ( ( $row_count > 0 ) ? 'style="display:none;"' : '' ) . '>';
			echo '<td colspan="' . ( count( $columns ) + $add_columns ) . '">' . __( 'No rows, add new below.', 'wp-easycart' ) . '</td>';
		echo '</tr>';
	}
	
	private function print_editable_table_add_new( $table_id, $columns, $add_new_func, $bulk_actions ){
		echo '<tr class="wp-easycart-editable-table-add-new-break"><td colspan="' . ( count( $columns ) + 2 ) . '"></td></tr>';
		echo '<tr class="wp-easycart-editable-table-add-new">';
			if( count( $bulk_actions ) ){
                echo '<td></td>';
            }
            foreach( $columns as $column ){
               $this->print_editable_table_column( $table_id, $column, ( ( isset( $column['default'] ) && is_array( $column['default'] ) ) ? $column['default']['value'] : ( ( isset( $column['default'] ) ) ? $column['default'] : '' ) ), '0' );
			}
			echo '<td><div class="dashicons dashicons-plus wpeasycart-editable-table-add-new" data-table="' . $table_id . '" data-func="' . ( ( is_array( $add_new_func ) ) ? $add_new_func['add_func'] : $add_new_func ) . '"' . ( ( is_array( $add_new_func ) ) ? 'data-callback="' . $add_new_func['callback_func'] . '"' : '' ) . '></div></td>';
		echo '</tr>';
	}
	
	private function print_editable_table_column( $table_id, $column, $value, $id ){
		echo '<td' . ( ( isset( $column['labelpos'] ) ) ? ' style="text-align:' . $column['labelpos'] . ';"' : '' ) . ' data-column="' . $column['id'] . '">';
			if( $column['type'] == 'combo' ){
				$this->print_editable_table_column_select( $table_id, $column, $value, $id );
			}else if( $column['type'] == 'text' ){
				$this->print_editable_table_column_text( $table_id, $column, $value, $id );
			}else if( $column['type'] == 'percentage' ){
				$this->print_editable_table_column_percentage( $table_id, $column, $value, $id );
			}else if( $column['type'] == 'checkbox' ){
				$this->print_editable_table_column_checkbox( $table_id, $column, $value, $id );
			}else if( $column['type'] == 'multitag' ){
				$this->print_editable_table_column_multitag( $table_id, $column, $value, $id );
			}else{
				echo '<div class="wp-easycart-editable-table-read-only" id="' . $table_id . '_' . $column['id'] . '_' . $id . '">' . $value . '</div>';
			}
		echo '</td>';
	}
	
	private function print_editable_table_column_select( $table_id, $column, $value, $id ){
		$group_id = '';
		if( $id != '0' ){
			echo '<button class="wp-easycart-editable-table-update-row"><div class="dashicons dashicons-yes"></div></button>';
		}
		echo '<select id="' . $table_id . '_' . $column['id'] . '_' . $id . '" class="wp-easycart-editable-table-input ' . ( ( !isset( $column['required'] ) || $column['required'] ) ? 'wp-easycart-editable-table-input-required' : '' ) . ' ' . ( ( isset( $column['cssclass'] ) ) ? $column['cssclass'] : '' ) . ' ' . $table_id . '_input_' . $id . '" data-id="' . $column['id'] . '" data-default="' . $column['default']['value'] . '"' . ( ( $id != '0' && isset( $column['readonly'] ) && $column['readonly'] ) ? ' disabled="disabled"' : '' ) . '>';
			echo '<option value="' . $column['default']['value'] . '">' . __( $column['default']['label'], 'wp-easycart' ) . '</option>';
		foreach( $column['options'] as $option ){
			if( isset( $option->group_id ) && $option->group_id != $group_id ){
				if( $group_id != '' ){
					echo '</optgroup>';
				}
				echo '<optgroup label="' . $option->group_id . '">';
				$group_id = $option->group_id;
			}
			echo '<option value="' . $option->value . '"' . ( ( $option->value == $value ) ? ' selected="selected"' : '' ) . ( ( isset( $option->group_id ) ) ? ' data-group="' . $option->group_id : '' ) . '">' . $option->label . '</option>';
		}
		if( $group_id != 0 ){
			echo '</optgroup>';
		}
		echo '</select>';
	}
	
	private function print_editable_table_column_text( $table_id, $column, $value, $id ){
		if( $id != '0' ){
			echo '<button class="wp-easycart-editable-table-update-row"><div class="dashicons dashicons-yes"></div></button>';
		}
		echo '<input type="text" value="' . $value . '" id="' . $table_id . '_' . $column['id'] . '_' . $id . '" class="wp-easycart-editable-table-input ' . ( ( !isset( $column['required'] ) || $column['required'] ) ? 'wp-easycart-editable-table-input-required' : '' ) . ' ' . ( ( isset( $column['cssclass'] ) ) ? $column['cssclass'] : '' ) . ' ' . $table_id . '_input_' . $id . '" data-id="' . $column['id'] . '" data-default="' . ( ( isset( $column['default'] ) ) ? $column['default'] : '' ) . '"' . ( ( $id != '0' && isset( $column['readonly'] ) && $column['readonly'] ) ? ' readonly="readonly"' : '' ) . ' />';
	}
	
	private function print_editable_table_column_percentage( $table_id, $column, $value, $id ){
		if( $id != '0' ){
			echo '<button class="wp-easycart-editable-table-update-row"><div class="dashicons dashicons-yes"></div></button>';
		}
		echo '<input type="text" value="' . $value . '" id="' . $table_id . '_' . $column['id'] . '_' . $id . '" class="wp-easycart-editable-table-input ' . ( ( !isset( $column['required'] ) || $column['required'] ) ? 'wp-easycart-editable-table-input-required' : '' ) . ' ' . ( ( isset( $column['cssclass'] ) ) ? $column['cssclass'] : '' ) . ' percentage ' . $table_id . '_input_' . $id . '" data-id="' . $column['id'] . '" data-default="' . ( ( isset( $column['default'] ) ) ? $column['default'] : '' ) . '"' . ( ( $id != '0' && isset( $column['readonly'] ) && $column['readonly'] ) ? ' readonly="readonly"' : '' ) . ' />%';
	}
	
	private function print_editable_table_column_checkbox( $table_id, $column, $value, $id ){
		echo '<input type="checkbox" value="1"';
		if( $value ){
			echo ' checked="checked"';
		}
		echo ' id="' . $table_id . '_' . $column['id'] . '_' . $id . '" class="wp-easycart-editable-table-input ' . ( ( !isset( $column['required'] ) || $column['required'] ) ? 'wp-easycart-editable-table-input-required' : '' ) . ' ' . ( ( isset( $column['cssclass'] ) ) ? $column['cssclass'] : '' ) . ' ' . $table_id . '_input_' . $id . '" data-id="' . $column['id'] . '" data-default="' . ( ( isset( $column['default'] ) ) ? $column['default'] : '' ) . '"' . ( ( $id != '0' && isset( $column['readonly'] ) && $column['readonly'] ) ? ' readonly="readonly"' : '' ) . ' />';
	}
	
	private function print_editable_table_column_multitag( $table_id, $column, $value, $id ){
		$group_id = '';
        $value = json_decode( $value );
		if( $id != '0' ){
			echo '<button class="wp-easycart-editable-table-update-row"><div class="dashicons dashicons-yes"></div></button>';
		}
		echo '<select multiple="multiple" id="' . $table_id . '_' . $column['id'] . '_' . $id . '" class="select2-multiple wp-easycart-editable-table-input ' . ( ( !isset( $column['required'] ) || $column['required'] ) ? 'wp-easycart-editable-table-input-required' : '' ) . ' ' . ( ( isset( $column['cssclass'] ) ) ? $column['cssclass'] : '' ) . ' ' . $table_id . '_input_' . $id . '" data-id="' . $column['id'] . '" data-default="' . $column['default']['value'] . '"' . ( ( $id != '0' && isset( $column['readonly'] ) && $column['readonly'] ) ? ' disabled="disabled"' : '' ) . '>';
			echo '<option value="' . $column['default']['value'] . '"' . ( ( !$value || in_array( $column['default']['value'], $value ) ) ? ' selected="selected"' : '' ) . '>' . __( $column['default']['label'], 'wp-easycart' ) . '</option>';
		foreach( $column['options'] as $option ){
			if( isset( $option->group_id ) && $option->group_id != $group_id ){
				if( $group_id != '' ){
					echo '</optgroup>';
				}
				echo '<optgroup label="' . $option->group_id . '">';
				$group_id = $option->group_id;
			}
			echo '<option value="' . $option->value . '"' . ( ( in_array( $option->value, $value ) ) ? ' selected="selected"' : '' ) . ( ( isset( $option->group_id ) ) ? ' data-group="' . $option->group_id . '"' : '' ) . '>' . $option->label . '</option>';
		}
        if( count( $column['options'] ) <= 1 && is_array( $value ) ){
            foreach( $value as $value_item ){
                if( $value_item != '' ){
                    echo '<option value="' . $value_item . '" selected="selected">' . $value_item . '</option>';
                }
            }
        }
		if( $group_id != 0 ){
			echo '</optgroup>';
		}
		echo '</select>';
	}
	
	private function print_editable_table_actions( $table_id, $actions, $id ){
		if( count( $actions ) > 0 ){
            echo '<td class="wp-easycart-editable-table-actions">';
            foreach( $actions as $action_id => $action ){
                echo '<a href="#" class="wpeasycart-editable-table-delete" data-table="' . $table_id . '" data-id="' . $id . '" data-func="' . $action['function'] . '"' . ( ( isset( $action['callback'] ) ) ? ' data-callback="' . $action['callback'] . '"' : '' ) . ' title="' . __( $action['label'], 'wp-easycart' ) . '"><div class="dashicons ' . $action['icon'] . '"></div></a>';
            }
            echo '</td>';
        }
	}
	
}
endif; // End if class_exists check

function wp_easycart_admin( ){
	return wp_easycart_admin::instance( );
}
wp_easycart_admin( );

add_action( 'wp_ajax_ec_admin_ajax_popup_newsletter_close', 'ec_admin_ajax_popup_newsletter_close' );
function ec_admin_ajax_popup_newsletter_close( ){
	if( isset( $_POST['wpeasycart_newsletter_customeremail'] ) && isset( $_POST['wpeasycart_join_newsletter'] ) && $_POST['wpeasycart_join_newsletter'] == '1' ){
		$customeremail = $_POST['wpeasycart_newsletter_customeremail'];
		$customername = get_bloginfo( 'name' );
		$site_url = site_url( );
		$site_url = str_replace( 'http://', '', $site_url );
		$site_url = str_replace( 'https://', '', $site_url );
		$site_url = str_replace( 'www.', '', $site_url );
		file_get_contents( sprintf( 'https://licensing.wpeasycart.com/licensing/activatetrial.php?customeremail=%s&customername=%s&siteurl=%s', urlencode( esc_attr( $customeremail ) ), urlencode( esc_attr( $customername ) ), urlencode( esc_attr( $site_url ) ) ) );
	}
	if( isset( $_POST['wpeasycart_newsletter_customeremail'] ) ){
		update_option( 'ec_option_bcc_email_addresses', esc_attr( $_POST['wpeasycart_newsletter_customeremail'] ) );
	}
	update_option( 'ec_option_newsletter_done', 1 );
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_allow_tracking', 'ec_admin_ajax_allow_tracking' );
function ec_admin_ajax_allow_tracking( ){
	update_option( 'ec_option_allow_tracking', '1' );
	if( !function_exists( 'wp_easycart_admin_tracking' ) ){
		include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/inc/wp_easycart_admin_tracking.php' );
	}
	do_action( 'wpeasycart_admin_usage_tracking_accepted' );
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_deny_tracking', 'ec_admin_ajax_deny_tracking' );
function ec_admin_ajax_deny_tracking( ){
	update_option( 'ec_option_allow_tracking', '-1' );
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_close_review_us', 'ec_admin_ajax_close_review_us' );
function ec_admin_ajax_close_review_us( ){
	update_option( 'ec_option_review_complete', '1' );
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_custom_deactivate', 'ec_admin_ajax_custom_deactivate' );
function ec_admin_ajax_custom_deactivate( ){
	if( !function_exists( 'wp_easycart_admin_tracking' ) ){
		include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/inc/wp_easycart_admin_tracking.php' );
	}
	do_action( 'wpeasycart_deactivated' );
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_hide_elementor_notice', 'ec_admin_ajax_hide_elementor_notice' );
function ec_admin_ajax_hide_elementor_notice( ){
	update_option( 'ec_option_hide_elementor_notice', 1 );
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_save_color_scheme', 'ec_admin_ajax_save_color_scheme' );
function ec_admin_ajax_save_color_scheme( ){
	update_option( 'ec_option_admin_color', preg_replace( '/\\\x[0-9a-f]{2}/', '', $_POST['ec_option_admin_color'] ) );
	die( );
}

add_action( 'wp_ajax_ec_admin_get_updated_stat_list', 'ec_admin_get_updated_stat_list' );
function ec_admin_get_updated_stat_list( ){
	$stats = (object) array(
		'sales' => wp_easycart_admin( )->get_stats( 'sales', $_POST['start_date'], $_POST['end_date'], $_POST['start_date2'], $_POST['end_date2'], $_POST['range'], $_POST['product'], $_POST['country'], $_POST['billing_country'] ),
		'items' => wp_easycart_admin( )->get_stats( 'items', $_POST['start_date'], $_POST['end_date'], $_POST['start_date2'], $_POST['end_date2'], $_POST['range'], $_POST['product'], $_POST['country'], $_POST['billing_country'] ),
		'carts' => wp_easycart_admin( )->get_stats( 'carts', $_POST['start_date'], $_POST['end_date'], $_POST['start_date2'], $_POST['end_date2'], $_POST['range'], $_POST['product'], $_POST['country'], $_POST['billing_country'] ),
		'single'=> wp_easycart_admin( )->get_single_stats( $_POST['start_date'], $_POST['end_date'], $_POST['start_date2'], $_POST['end_date2'], $_POST['product'], $_POST['country'], $_POST['billing_country'] ),
	);
	echo json_encode( $stats );
	die( );
}

add_action( 'wp_ajax_ec_admin_create_report_export', 'ec_admin_create_report_export' );
function ec_admin_create_report_export( ){
	$order_report = wp_easycart_admin( )->get_order_report( $_POST['start_date'], $_POST['end_date'], $_POST['product'], $_POST['country'], $_POST['billing_country'] );
    $order_report2 = ( $_POST['start_date2'] ) ? wp_easycart_admin( )->get_order_report( $_POST['start_date2'], $_POST['end_date2'], $_POST['product'], $_POST['country'], $_POST['billing_country'] ) : false;
    
    echo json_encode( (object) array( 'report1' => $order_report, 'report2' => $order_report2 ) );
	die( );
}

add_action( 'wp_ajax_ec_admin_ajax_save_terms_accepted', 'ec_admin_ajax_save_terms_accepted' );
function ec_admin_ajax_save_terms_accepted( ){
    update_option( 'ec_option_wpeasycart_terms_accepted', 1 );
    die( );
}
?>