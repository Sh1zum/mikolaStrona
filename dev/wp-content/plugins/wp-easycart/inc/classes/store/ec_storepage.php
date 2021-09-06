<?php

class ec_storepage{
	
	private $mysqli;										// ec_db structure
	
	private $is_details;									// BOOL
	private $product_list;									// ec_productlist structure
	private $category_list;									// ec_categorylist structure
	private $product;										// ec_product structure
	private $model_number;									// VARCHAR 255
	private $optionitem_id;									// INT
	private $category_view;									// BOOL
	
	public $previous_model_number;							// VARCHAR 255
	public $number_in_product_list;							// INT
	public $count_of_product_list;							// INT
	public $next_model_number;								// VARCHAR 255
	public $cart_page;
	public $account_page;
	public $store_page;
	public $permalink_divider;
	
	// Short Code Info
	public $menu_id;
	public $submenu_id;
	public $subsubmenu_id;
	public $manufacturer_id;
	public $group_id;
	
	
	// Page Options
	public $page_options;									// Array
	
	////////////////////////////////////////////////////////
	// CONSTUCTOR FUNCTION
	////////////////////////////////////////////////////////
	function __construct( $menuid = "NOMENU", $submenuid = "NOSUBMENU", $subsubmenuid = "NOSUBSUBMENU", $manufacturerid = "NOMANUFACTURER", $groupid = "NOGROUP", $modelnumber = "NOMODELNUMBER" ){
		
		$this->mysqli = new ec_db( );
		if( isset( $GLOBALS['ec_page_options'] ) )
			$this->page_options = $GLOBALS['ec_page_options']->page_options;
		else
			$this->page_options = array( );
		
		$this->update_statistics( $menuid, $submenuid, $subsubmenuid, $manufacturerid, $groupid, $modelnumber );
		
		if( $modelnumber != "NOMODELNUMBER" )								$this->is_details = true;
		else																$this->is_details = $this->get_is_details( );
		
		if(	!$this->is_details )											$this->setup_products(  $menuid, $submenuid, $subsubmenuid, $manufacturerid, $groupid );
		else{												
			if( $modelnumber != "NOMODELNUMBER" )							$this->model_number = $modelnumber;
			else															$this->model_number = $_GET['model_number'];
			
			if( isset( $_GET['optionitem_id'] ) )							$this->optionitem_id = $_GET['optionitem_id'];
			else															$this->optionitem_id = 0;
																			
																			$this->setup_details( );
		}
		
		$accountpageid = get_option( 'ec_option_accountpage' );
		$cartpageid = get_option( 'ec_option_cartpage' );
		$storepageid = get_option( 'ec_option_storepage' );
		
		if( function_exists( 'icl_object_id' ) ){
			$accountpageid = icl_object_id( $accountpageid, 'page', true, ICL_LANGUAGE_CODE );
		}
		
		if( function_exists( 'icl_object_id' ) ){
			$cartpageid = icl_object_id( $cartpageid, 'page', true, ICL_LANGUAGE_CODE );
		}
		
		if( function_exists( 'icl_object_id' ) ){
			$storepageid = icl_object_id( $storepageid, 'page', true, ICL_LANGUAGE_CODE );
		}
		
		$this->account_page = get_permalink( $accountpageid );
		$this->cart_page = get_permalink( $cartpageid );
		$this->store_page = get_permalink( $storepageid );
		
		if( class_exists( "WordPressHTTPS" ) && isset( $_SERVER['HTTPS'] ) ){
			$https_class = new WordPressHTTPS( );
			$this->account_page = $https_class->makeUrlHttps( $this->account_page );
			$this->cart_page = $https_class->makeUrlHttps( $this->cart_page );
			$this->store_page = $https_class->makeUrlHttps( $this->store_page );
		}
		
		if( substr_count( $this->store_page, '?' ) )						$this->permalink_divider = "&";
		else																$this->permalink_divider = "?";
		
		$this->menu_id = $menuid;
		$this->submenu_id = $submenuid;
		$this->subsubmenu_id = $subsubmenuid;
		$this->manufacturer_id = $manufacturerid;
		$this->group_id = $groupid;
	}

	////////////////////////////////////////////////////////
	// STORE PAGE SETUP FUNCTIONS
	////////////////////////////////////////////////////////
	private function setup_products( $menuid, $submenuid, $subsubmenuid, $manufacturerid, $groupid ){
		
		$this->product_list = new ec_productlist( false, $menuid, $submenuid, $subsubmenuid, $manufacturerid, $groupid, "", $this->page_options );
		$this->category_list = new ec_categorylist( $groupid );
			
	}
	
	private function setup_details( ){
		
		$db = new ec_db( );
		global $wpdb;
		$products = $db->get_product_list( $wpdb->prepare( " WHERE product.model_number = %s AND product.activate_in_store = 1", $this->model_number ), "", "", "", "wpeasycart-product-only-".$this->model_number );
		
		if( count( $products ) > 0 ){
			
			$this->product = new ec_product( $products[0], 0, 1, 0 );
		
		}else{
			
			global $wp_query;
			$wp_query->is_404 = true;
			$wp_query->is_single = false;
			$wp_query->is_page = false;
			
			include( get_query_template( '404' ) );
			exit();
				
		}
		
	}
	
	////////////////////////////////////////////////////////
	// STORE PAGE MAIN DISPLAY FUNCTIONS
	////////////////////////////////////////////////////////
	public function display_store_success(){
		$model_number = "";
		$title = "";
		if( isset( $_GET['model'] ) ){
			$model_number = $_GET['model'];
			$db = new ec_db( );
			global $wpdb;
			$products = $db->get_product_list( $wpdb->prepare( "WHERE product.model_number = %s", $model_number ), "", "", "", "wpeasycart-product-only-".$model_number );
			if( count( $products ) > 0 ){
				$title = $products[0]['title'];
			}
		}
		
		$success_notes = array(	"addtocart" => $GLOBALS['language']->get_text( "ec_success", "store_added_to_cart" ),
								"inquiry_sent" => $GLOBALS['language']->get_text( "ec_success", "inquiry_sent" ) 
							   );
		
		if( isset( $_GET['ec_store_success'] ) && $_GET['ec_store_success'] != "addtocart" ){
			echo "<div class=\"ec_cart_success\"><div>" . $success_notes[ $_GET['ec_store_success'] ] . "</div></div>";
		}else if( isset( $_GET['ec_store_success'] ) ){
			echo "<div class=\"ec_cart_success\"><div>" . str_replace( "[prod_title]", $GLOBALS['language']->convert_text( $title ), $success_notes[ $_GET['ec_store_success'] ] ) . " ";
			$cartpage = new ec_cartpage( );
			$cartpage->display_checkout_button( $GLOBALS['language']->get_text( 'cart', 'cart_checkout' ) );
			echo "</div></div>";
		}
	}
	
	public function display_store_error(){
		$error_notes = array( "minquantity" => $GLOBALS['language']->get_text( "ec_errors", "minquantity" ) 
							);
		echo "<div class=\"ec_cart_error\"><div>" . $error_notes[ $_GET['ec_store_error'] ] . "</div></div>";
	}
	
	public function display_store_page( ){
		
		if( get_option( 'ec_option_restrict_store' ) ){
			$restricted = explode( "***", get_option( 'ec_option_restrict_store' ) );
		}
		//current_user_can('manage_options') || 
		if( !get_option( 'ec_option_restrict_store' ) || $GLOBALS['ec_user']->user_level == "admin" || in_array( $GLOBALS['ec_user']->user_level, $restricted ) ){
			$paging = 1;
			if( isset( $_GET['pagenum'] ) )
				$paging = $_GET['pagenum'];
			if( isset( $_GET['ec_store_success'] ) )			$this->display_store_success( );
			if( isset( $_GET['ec_store_error'] ) )				$this->display_store_error( );
			if( !$this->is_details && $this->category_list->num_categories > 0 && get_option( 'ec_option_show_featured_categories' ) && $paging == 1 )
																$this->display_category_view( );
			if(	!$this->is_details )							$this->display_products_page( );
			else												$this->display_product_details_page( );
		}else{
			
			$this->show_restricted_store( );
			
		}
		
	}
	
	public function display_category_page( ){
		$this->display_category_view( );
	}
	
	private function display_category_view( ){
		if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_product_category_view.php' ) )	
			include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option('ec_option_base_layout') . '/ec_product_category_view.php' );	
		else
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option('ec_option_latest_layout') . '/ec_product_category_view.php' );
	}
	
	private function display_products_page( ){
		if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_product_page.php' ) )	
			include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option('ec_option_base_layout') . '/ec_product_page.php' );	
		else
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option('ec_option_latest_layout') . '/ec_product_page.php' );
	}
	
	public function has_products( ){
		if( count( $this->product_list->products ) > 0 )
			return true;
		else
			return false;
	}
	
	private function display_product_details_page( ){
		$storepageid = get_option('ec_option_storepage');
		$cartpageid = get_option('ec_option_cartpage');
		$accountpageid = get_option('ec_option_accountpage');
		
		$storepage = get_permalink( $storepageid );
		$cartpage = get_permalink( $cartpageid );
		$accountpage = get_permalink( $accountpageid );
		
		if(substr_count($storepage, '?'))							$permalinkdivider = "&";
		else														$permalinkdivider = "?";
		
		echo "<script>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');ga('create', '" . get_option( 'ec_option_googleanalyticsid' ) . "', 'auto');ga('send', 'pageview');ga('require', 'ec');ga('ec:addImpression',{'id': '" . str_replace( "'", "\'", $this->product->model_number ) . "','name': '" . str_replace( "'", "\'", $this->product->title ) . "','price': '" . number_format( $this->product->price, 2, '.', '' ) . "',});ga('send', 'pageview');function  ec_google_addToCart( ){ga('create', '" . get_option( 'ec_option_googleanalyticsid' ) . "', 'auto');ga('require', 'ec');ga('ec:addProduct', {'id': '" . str_replace( "'", "\'", $this->product->model_number ) . "','name': '" . str_replace( "'", "\'", $this->product->title ) . "','price': '" . number_format( $this->product->price, 2, '.', '' ) . "','quantity': document.getElementById( 'product_quantity_" . $this->product->model_number . "' )});ga('ec:setAction', 'add');ga('send', 'event', 'UX', 'click', 'add to cart');}</script>";
		
		if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_product_details_page.php' ) )	
			include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option('ec_option_base_layout') . '/ec_product_details_page.php' );
		else
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option('ec_option_latest_layout') . '/ec_product_details_page.php' );
		
		if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/admin_panel.php" ) )
			echo "<script>ec_initialize_options();</script>";
	}
	
	////////////////////////////////////////////////////////
	// PRODUCT MENU FILTER BAR DISPLAY FUNCTIONS
	////////////////////////////////////////////////////////
	private function product_menu_filter( ){
		if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_product_menu_filter.php' ) )	
			include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option('ec_option_base_layout') . '/ec_product_menu_filter.php' );
		else
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option('ec_option_latest_layout') . '/ec_product_menu_filter.php' );
	}
	
	private function product_filter_menu_items( $divider ){
		$this->product_list->display_filter_menu( $divider );
	}
	
	////////////////////////////////////////////////////////
	// PRODUCT FILTER AND PAGESET BAR DISPLAY FUNCTIONS
	////////////////////////////////////////////////////////
	private function product_filter_bar( ){
		if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_product_filter_bar.php' ) )	
			include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option('ec_option_base_layout') . '/ec_product_filter_bar.php' );
		else
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option('ec_option_latest_layout') . '/ec_product_filter_bar.php' );
	}
	
	private function product_filter_combo( ){
		$this->product_list->display_filter_combo( );
	}
	
	private function product_items_per_page( $divider ){
		$this->product_list->display_items_per_page( $divider );
	}
	
	private function product_current_page( ){
		$this->product_list->display_current_page( );
	}
	
	private function product_total_pages( ){
		$this->product_list->display_total_pages( );
	}
	
	private function product_paging( $divider ){
		$this->product_list->display_product_paging( $divider );
	}
	
	////////////////////////////////////////////////////////
	// PRODUCT DISPLAY FUNCTIONS
	////////////////////////////////////////////////////////
	private function product_list( ){
		$this->product_list->display_product_list( );
	}
	
	private function category_list( ){
		$this->category_list->display_category_list( );
	}
	
	////////////////////////////////////////////////////////
	// PRODUCT DETAILS DISPLAY FUNCTIONS
	////////////////////////////////////////////////////////
	private function product_details_title( ){
		echo $this->product->display_product_details_title_link();
	}
	
	private function product_details_price( ){
		$this->product->display_product_details_pricing();
	}
	
	private function product_details_discount_percentage( $text1, $text2 ){
		$this->product->display_product_details_discount_percentage( $text1, $text2 );	
	}
	
	private function product_details_rating( ){
		$this->product->display_product_details_rating( );	
	}
	
	private function product_details_number_reviews( ){
		$this->product->display_product_details_number_reviews( );	
	}
	
	private function product_details_option1( ){
		$this->product->product_details_option1_swatches( );	
	}
	
	private function product_details_option2( ){
		$this->product->product_details_option2_swatches( );	
	}
	
	private function product_details_option3( ){
		$this->product->product_details_option3_swatches( );	
	}
	
	private function product_details_option4( ){
		$this->product->product_details_option4_swatches( );	
	}
	
	private function product_details_option5( ){
		$this->product->product_details_option5_swatches( );	
	}
	
	public function display_product_previous_category_link( $link_text ){
		if( $this->previous_model_number != "" && $this->product->product_id )
			echo "<a href=\"" . $this->store_page . $this->permalink_divider . "model_number=" . $this->previous_model_number . $this->product->get_additional_link_options() . "\" class=\"ec_product_title_link\">" . $link_text . "</a>";
		else
			echo $link_text;
	}
	
	public function display_product_number_in_category_list( ){
		echo $this->number_in_product_list;
	}
	
	public function display_product_count_in_category_list( ){
		echo $this->count_of_product_list;
	}
	
	public function display_product_next_category_link( $link_text ){
		if( $this->next_model_number )
		echo "<a href=\"" . $this->store_page . $this->permalink_divider . "model_number=" . $this->next_model_number . $this->product->get_additional_link_options() . "\" class=\"ec_product_title_link\">" . $link_text . "</a>";
		else
		echo $link_text;
	}
	
	public function display_optional_banner( ){
		$menu_level = $this->product_list->filter->get_menu_level( );
		if( $menu_level == 1 && isset( $this->product_list->filter->menulevel1->menu_id ) ){
			$menu_row = $GLOBALS['ec_menu']->get_menu_row( $this->product_list->filter->menulevel1->menu_id, 1 );
		}else if( $menu_level == 2 && isset( $this->product_list->filter->menulevel2->menu_id ) ){
			$menu_row = $GLOBALS['ec_menu']->get_menu_row( $this->product_list->filter->menulevel2->menu_id, 2 );
		}else if( $menu_level == 3 && isset( $this->product_list->filter->menulevel3->menu_id ) ){
			$menu_row = $GLOBALS['ec_menu']->get_menu_row( $this->product_list->filter->menulevel3->menu_id, 3 );
		}
		
		if( isset( $menu_row ) ){
			if( $menu_row->banner_image != "" ){
				if( substr( $menu_row->banner_image, 0, 7 ) == 'http://' || substr( $menu_row->banner_image, 0, 8 ) == 'https://' )
					echo "<img src=\"" . $menu_row->banner_image . "\" alt=\"" . $menu_row->name . "\" />";	
				else if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/products/banners/" . $menu_row->banner_image ) )	
					echo "<img src=\"" . plugins_url( "wp-easycart-data/products/banners/" . $menu_row->banner_image ) . "\" alt=\"" . $menu_row->name . "\" />";	
				else
					echo "<img src=\"" . plugins_url( EC_PLUGIN_DIRECTORY . "/products/banners/" . $menu_row->banner_image ) . "\" alt=\"" . $menu_row->name . "\" />";	
			}
		}
	}
	
	public function has_banner( ){
		$menu_level = $this->product_list->filter->get_menu_level( );
		if( $menu_level == 1 && isset( $this->product_list->filter->menulevel1->menu_id ) ){
			$menu_row = $GLOBALS['ec_menu']->get_menu_row( $this->product_list->filter->menulevel1->menu_id, 1 );
		}else if( $menu_level == 2 && isset( $this->product_list->filter->menulevel2->menu_id ) ){
			$menu_row = $GLOBALS['ec_menu']->get_menu_row( $this->product_list->filter->menulevel2->menu_id, 2 );
		}else if( $menu_level == 3 && isset( $this->product_list->filter->menulevel3->menu_id ) ){
			$menu_row = $GLOBALS['ec_menu']->get_menu_row( $this->product_list->filter->menulevel3->menu_id, 3 );
		}
		
		if( isset( $menu_row ) && isset( $menu_row->banner_image ) && $menu_row->banner_image != "" ){
			return true;
		}else{
			return false;
		}
	}
	
	public function get_products_no_limit( ){
		
		return $this->product_list->get_products_no_limit( );
	
	}
	
	private function show_restricted_store( ){
		
		if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_product_restricted.php' ) )	
			include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option('ec_option_base_layout') . '/ec_product_restricted.php' );
		else
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option('ec_option_latest_layout') . '/ec_product_restricted.php' );
		
	}
	
	////////////////////////////////////////////////////////
	// MAIN HELPER FUNCTIONS
	////////////////////////////////////////////////////////
	private function get_is_details( ){
		if( isset( $_GET['model_number'] ) )					return true;
		else													return false;
	}
	
	private function update_statistics( $menuid, $submenuid, $subsubmenuid, $manufacturerid, $groupid, $modelnumber ){
		
		$db = new ec_db( );
		if( $modelnumber != "NOMODELNUMBER" ){
			$db->update_product_views( $modelnumber );
		}else if( $menuid != "NOMENU" ){
			$db->update_menu_views( $menuid );
		}else if( $submenuid != "NOSUBMENU" ){
			$db->update_submenu_views( $submenuid );
		}else if( $subsubmenuid != "NOSUBSUBMENU" ){
			$db->update_subsubmenu_views( $subsubmenuid );
		}
	}
}

?>