<?php 

class ec_filter{
	protected $mysqli;											// ec_db structure
	
	public $perpage;											// ec_perpage structure
	
	public $menulevel1;											// ec_menuitem structure
	public $menulevel2;											// ec_menuitem structure
	public $menulevel3;											// ec_menuitem structure
	public $forced_menu_level = 0;								// menu level can be forced for shortcodes, INT
	
	public $show_on_startup;									// BOOL
	public $product_only;										// BOOL
	
	public $search;												// VARCHAR
	
	public $menufacturer;										// ec_manufacturer structure
	public $group_id;											// INT
	public $model_number;										// VARCHAR
	public $pricepoint_id;										// INT
	public $current_filter;										// INT
	public $default_first_filter;								// INT
	
	private $store_page;										// VARCHAR
	private $permalink_divider;									// CHAR
	
	function __construct( ){
		$this->mysqli = new ec_db();
		
		$this->default_first_filter = get_option( 'ec_option_default_store_filter' );
		$this->current_filter = $this->get_current_filter();
		$this->perpage = new ec_perpage();
		
		$this->menulevel1 = new ec_menuitem( $this->get_menu1_id( ), 1 );
		$this->menulevel2 = new ec_menuitem( $this->get_menu2_id( ), 2 );
		$this->menulevel3 = new ec_menuitem( $this->get_menu3_id( ), 3 );
		$this->product_only = $this->get_product_only( );
		
		$this->search = $this->get_search( );
		
		$this->manufacturer = new ec_manufacturer( $this->get_manufacturer_id(), "" );
		$this->group_id = $this->get_group_id();
		$this->pricepoint_id = $this->get_pricepoint_id();
		$this->model_number = $this->get_model_number();
		
		$this->show_on_startup = $this->get_show_on_startup( );
		
		$store_page_id = get_option('ec_option_storepage');
		
		if( function_exists( 'icl_object_id' ) ){
			$store_page_id = icl_object_id( $store_page_id, 'page', true, ICL_LANGUAGE_CODE );
		}
		
		$this->store_page = get_permalink( $store_page_id );
		
		if( class_exists( "WordPressHTTPS" ) && isset( $_SERVER['HTTPS'] ) ){
			$https_class = new WordPressHTTPS( );
			$this->store_page = $https_class->makeUrlHttps( $this->store_page );
		}
		
		if( substr_count( $this->store_page, '?' ) )			$this->permalink_divider = "&";
		else													$this->permalink_divider = "?";
	}
	
	public function get_cache_key( ){
		
		if( $this->model_number != "" )
			return "wpeasycart-product-only-".$this->model_number;
		
		else{
			$current_page = 1;
			if( isset( $_GET['pagenum'] ) )
				$current_page = htmlspecialchars( $_GET['pagenum'], ENT_QUOTES );
			$key = "wpeasycart-product-list";
			$key .= "-s" . $this->show_on_startup;
			$key .= "-f".$this->get_current_filter( );
			$key .= "-pe".$this->perpage->selected;
			$key .= "-man".$this->get_manufacturer_id( );
			$key .= "-m1".$this->get_menu1_id( );
			$key .= "-m2".$this->get_menu2_id( );
			$key .= "-m3".$this->get_menu3_id( );
			$key .= "-c3".$this->get_group_id( );
			$key .= "-se".$this->get_search( );
			$key .= "-pp".$this->get_pricepoint_id( );
			$key .= "-cp".$current_page;
			if( isset( $_GET['optionitem_id'] ) )
				$key .= "-op".$_GET['optionitem_id'];
			
			return $key;
		}
	}
	
	private function get_current_filter(){
		if( isset($_GET['filternum']) )							return $_GET['filternum'];
		else													return $this->default_first_filter;
	}
	
	private function get_menu1_id(){
		
		if( $this->get_menu_level() == 1 )						return $this->mysqli->get_menulevel1_id( $this->get_displayed_menu_id( ) );
		
		else if( $this->get_menu_level() == 2 )					return $this->mysqli->get_menulevel1_id_from_menulevel2( $this->get_displayed_menu_id( ) );
			
		else if( $this->get_menu_level() == 3 ){				$id2 = $this->mysqli->get_menulevel2_id_from_menulevel3( $this->get_displayed_menu_id( ) );
																return $this->mysqli->get_menulevel1_id_from_menulevel2( $id2 );
																
		}else													return 0;
		
	}
	
	private function get_menu2_id(){
		if( $this->get_menu_level() == 2)						return $this->mysqli->get_menulevel2_id( $this->get_displayed_menu_id( ) );
			
		else if( $this->get_menu_level() == 3)					return $this->mysqli->get_menulevel2_id_from_menulevel3( $this->get_displayed_menu_id() );
		
		else													return 0;
	}
	
	private function get_menu3_id(){
		if( $this->get_menu_level() == 3)						return $this->mysqli->get_menulevel3_id( $this->get_displayed_menu_id( ) );
																return 0;
	}
	
	private function get_displayed_menu_id(){
		if(isset($_GET['menuid']))								return $this->mysqli->get_menulevel1_id( $_GET['menuid'] );
		else if( isset( $GLOBALS['ec_store_shortcode_options'] ) && isset( $GLOBALS['ec_store_shortcode_options'][0] ) && $GLOBALS['ec_store_shortcode_options'][0] != 0 )								
																return $this->mysqli->get_menulevel1_id( $GLOBALS['ec_store_shortcode_options'][0] );
		else if(isset($_GET['submenuid']))						return $this->mysqli->get_menulevel2_id( $_GET['submenuid'] );
		else if( isset( $GLOBALS['ec_store_shortcode_options'] ) && isset( $GLOBALS['ec_store_shortcode_options'][1] ) && $GLOBALS['ec_store_shortcode_options'][1] != 0 )								
																return $this->mysqli->get_menulevel2_id( $GLOBALS['ec_store_shortcode_options'][1] );
		else if(isset($_GET['subsubmenuid']))					return $this->mysqli->get_menulevel3_id( $_GET['subsubmenuid'] );
		else if( isset( $GLOBALS['ec_store_shortcode_options'] ) && isset( $GLOBALS['ec_store_shortcode_options'][2] ) && $GLOBALS['ec_store_shortcode_options'][2] != 0 )								
																return $this->mysqli->get_menulevel3_id( $GLOBALS['ec_store_shortcode_options'][2] );
		else													return 0;
	}
	
	public function get_menu_level(){
		if($this->forced_menu_level != 0)						return $this->forced_menu_level;
		else if(isset($_GET['menuid']))							return 1;
		else if(isset($_GET['submenuid']))						return 2;
		else if(isset($_GET['subsubmenuid']))					return 3;
		else if( isset( $GLOBALS['ec_store_shortcode_options'] ) && isset( $GLOBALS['ec_store_shortcode_options'][0] ) && $GLOBALS['ec_store_shortcode_options'][0] != 0 )								
																return 1;
		else if( isset( $GLOBALS['ec_store_shortcode_options'] ) && isset( $GLOBALS['ec_store_shortcode_options'][1] ) && $GLOBALS['ec_store_shortcode_options'][1] != 0 )								
																return 2;
		else if( isset( $GLOBALS['ec_store_shortcode_options'] ) && isset( $GLOBALS['ec_store_shortcode_options'][2] ) && $GLOBALS['ec_store_shortcode_options'][2] != 0 )								
																return 3;
		else													return 0;
	}
	
	public function get_show_on_startup( ){
		if( $this->get_menu_level( ) == 0 && $this->manufacturer->manufacturer_id == 0 && $this->model_number == "" && $this->search == "" && $this->group_id == 0 )						
																return true;
		else if( isset( $_GET['featured'] ) )					return true;
		else													return false;	
	}
	
	public function get_product_only( ){
		if( isset( $_GET['model_number'] ) && !$this->has_filters() )					return true;
		else													return false;	
	}
	
	public function get_search( ){
		if( isset( $_GET['ec_search'] ) )						return $_GET['ec_search'];
		else													return "";	
	}
	
	public function get_manufacturer_id(){
		if( isset( $_GET['manufacturer'] ) )					return $this->mysqli->get_manufacturer_id( $_GET['manufacturer'] );
		else if( isset( $GLOBALS['ec_store_shortcode_options'] ) && isset( $GLOBALS['ec_store_shortcode_options'][3] ) && $GLOBALS['ec_store_shortcode_options'][3] != 0 )								
																return $GLOBALS['ec_store_shortcode_options'][3];
		else													return 0;	
	}
	
	private function get_pricepoint_id(){
		if(isset($_GET['pricepoint']))							return $this->mysqli->get_pricepoint_id( $_GET['pricepoint'] );
		else													return 0;	
	}
	
	public function get_group_id(){
		if( isset( $_GET['group_id'] ) )						return $this->mysqli->get_category_id( $_GET['group_id'] );
		else if( isset( $GLOBALS['ec_store_shortcode_options'] ) && isset( $GLOBALS['ec_store_shortcode_options'][4] ) && $GLOBALS['ec_store_shortcode_options'][4] != 0 )														return $this->mysqli->get_category_id( $GLOBALS['ec_store_shortcode_options'][4] );
		else													return 0;
	}
	
	private function get_model_number(){
		if(isset($_GET['model_number']))						return $GLOBALS['ec_products']->get_model_number( $_GET['model_number'] );
		else if(isset($this->model_number))						return $this->model_number;
		else													return "";
	}
	
	public function get_menu_id(){
		if( isset( $this->menulevel1->menu_id ) )
			return $this->menulevel1->menu_id;
		else
			return 0;
	}
	
	public function get_submenu_id(){
		if( isset( $this->menulevel2->menu_id ) )
			return $this->menulevel2->menu_id;
		else
			return 0;
	}
	
	public function get_subsubmenu_id(){
		if( isset( $this->menulevel3->menu_id ) )
			return $this->menulevel3->menu_id;
		else
			return 0;
	}
	
	public function get_menu_name(){
		if( isset( $this->menulevel1->menu_name ) )
			return $this->menulevel1->menu_name;
		else
			return 0;
	}
	
	public function get_submenu_name(){
		if( isset( $this->menulevel2->menu_name ) )
			return $this->menulevel2->menu_name;
		else
			return 0;
	}
	
	public function get_subsubmenu_name(){
		if( isset( $this->menulevel3->menu_name ) )
			return $this->menulevel3->menu_name;
		else
			return 0;
	}
	
	public function get_menu_permalink( ){
		return $this->ec_get_permalink( $this->menulevel1->post_id, "menu" );
	}
	
	public function get_submenu_permalink( ){
		return $this->ec_get_permalink( $this->menulevel2->post_id, "submenu" );
	}
	
	public function get_subsubmenu_permalink( ){
		return $this->ec_get_permalink( $this->menulevel3->post_id, "subsubmenu" );
	}
	
	public function get_menu_link(){
		return "<a href=\"". $this->get_menu_permalink( ) . "\" class=\"ec_store_link\">" . $this->menu->level1->name( ) . "</a>";
	}
	
	public function get_submenu_link(){
		return "<a href=\"". $this->get_submenu_permalink( ) . "\" class=\"ec_store_link\">" . $this->menu->level2->name . "</a>";
	}
	
	public function get_subsubmenu_link(){
		return "<a href=\"". $this->get_subsubmenu_permalink( ) . "\" class=\"ec_store_link\">" . $this->menu->level3->name . "</a>";
	}
	
	public function if_level_1_get_name(){
		if($this->get_menu_level() == 1)				return $this->menu->level1->name;
		else											return "";	
	}
	
	public function if_level_2_get_name(){
		if($this->get_menu_level() == 2)				return $this->menu->level2->name;
		else											return "";	
	}
	
	public function if_level_3_get_name(){
		if($this->get_menu_level() == 3)				return $this->menu->level3->name;
		else											return "";	
	}
	
	public function is_sort_selected($num){
		if($this->current_filter == $num)				return true;
		else											return false;	
	}
	
	public function get_items_per_page( $divider ){
		
		return $this->perpage->get_items_per_page( $divider, $this->get_link_string( 2 ) );
		
	}
	
	public function get_link_string( $leave_out ){
		
		// First we should check if the store shortcode is even on the page. This will tell us what to use as the initial link
		$has_store_shortcode = false;
		global $wp_query;
		$post_obj = $wp_query->get_queried_object();
		if( isset( $post_obj ) && isset( $post_obj->post_content ) ){
			$post_content = $post_obj->post_content;
		}else{
			$post_content = "";
		}
		if( strstr( $post_content, "[ec_store" ) )
			$has_store_shortcode = true;
		
		if( $leave_out != 1 && ( isset( $_GET['menuid'] ) || isset( $_GET['submenuid'] ) || isset( $_GET['subsubmenuid'] ) ) ){
			// First try and get a permalink from the id
			if( isset( $_GET['subsubmenuid'] ) )
				$menu_row = $GLOBALS['ec_menu']->get_menu_row( $_GET['subsubmenuid'], 3 );
			else if( isset( $_GET['submenuid'] ) )
				$menu_row = $GLOBALS['ec_menu']->get_menu_row( $_GET['submenuid'], 2 );
			else if( isset( $_GET['menuid'] ) )
				$menu_row = $GLOBALS['ec_menu']->get_menu_row( $_GET['menuid'], 1 );
				
			if( count( $menu_row ) > 0 ){
				if( $has_store_shortcode )
					$ret_string = $this->ec_get_permalink( $menu_row->post_id, "menurow", $menu_row ) . $this->permalink_divider;
				else
					$ret_string = $this->store_page . $this->permalink_divider;
			}else{
				
				$ret_string = $this->store_page . $this->permalink_divider;
				if( $this->get_menu_level() == 1 )						$ret_string .= "menuid=" . $this->menulevel1->menu_id . "&amp;menu=" . $this->get_menu_name();
				else if( $this->get_menu_level() == 2 ) 				$ret_string .= "submenuid=" . $this->menulevel2->menu_id . "&amp;submenu=" . $this->get_submenu_name();
				else if( $this->get_menu_level() == 3 )					$ret_string .= "subsubmenuid=" . $this->menulevel3->menu_id . "&amp;subsubmenu=" . $this->get_subsubmenu_name();
			}
		}else if( $leave_out != 1 ){
			global $wp_query;
			$post_obj = $wp_query->get_queried_object();
			if( isset( $post_obj ) && isset( $post_obj->ID ) ){
				$post_id = $post_obj->ID;
			}else{
				$post_id = 0;
			}
			
			if( $post_id && $post_id != get_option('ec_option_storepage') ){
				$ret_string = get_permalink( $post_id ) . $this->permalink_divider;
			}else{
				$manufacturer = $GLOBALS['ec_manufacturers']->get_manufacturer_id_from_post_id( $post_id );
				$product = $GLOBALS['ec_products']->get_product_from_post_id( $post_id );
				if( ( isset( $manufacturer ) && $leave_out == 3 ) || ( isset( $product ) && $leave_out == 3 ) || ( isset( $product ) && $leave_out == 4 ) )
					$ret_string = $this->store_page . $this->permalink_divider;
				else{
					if( $has_store_shortcode )
						$ret_string = $this->ec_get_permalink( $post_id, "store" ) . $this->permalink_divider;
					else
						$ret_string = $this->store_page . $this->permalink_divider;
				}
			}
		}else{
			$ret_string = $this->store_page . $this->permalink_divider;
		}
		
		if( $leave_out != 2 )										$ret_string .= "&amp;perpage=" . $this->perpage->selected;
		
		if( $leave_out != 3 ){
		
			if($this->manufacturer->manufacturer_id != 0)			$ret_string .= "&amp;manufacturer=".$this->manufacturer->manufacturer_id;
			
		}
		
		
		if( $leave_out != 3 && $leave_out != 4 ){
		
			if($this->pricepoint_id != 0)							$ret_string .= "&amp;pricepoint=" . $this->pricepoint_id;
			
		}
		
		if( $leave_out != 5 ){
	
			if($this->current_filter != 0)							$ret_string .= "&amp;filternum=" . $this->current_filter;
			
		}
		
		if( $leave_out != 6 ){
	
			if($this->group_id != 0)								$ret_string .= "&amp;group_id=" . $this->group_id;
			
		}
		
		if( $leave_out != 7 ){
	
			if( isset( $_GET['optionitem_id'] ) )					$ret_string .= "&amp;optionitem_id=" . $_GET['optionitem_id'];
			
		}
		
		return $ret_string;
		
	}
	
	public function get_where_query(){
		
		global $wpdb;
		
		if( $this->has_filters( ) || $this->product_only ){
			
			$ret_string = "WHERE product.activate_in_store = 1";
			
			if( $this->get_menu_level() == 1 && $this->get_menu_id( ) != 0 )						
				$ret_string .= $wpdb->prepare( " AND ( product.menulevel1_id_1 = %s OR product.menulevel2_id_1 = %s OR product.menulevel3_id_1 = %s )", $this->get_menu_id( ), $this->get_menu_id( ), $this->get_menu_id( ) );
			
			if( $this->get_menu_level() == 2 && $this->get_submenu_id( ) != 0 ) 						
				$ret_string .= $wpdb->prepare( " AND ( product.menulevel1_id_2 = %s OR product.menulevel2_id_2 = %s OR product.menulevel3_id_2 = %s )", $this->get_submenu_id( ), $this->get_submenu_id( ), $this->get_submenu_id( ) );
			
			if( $this->get_menu_level() == 3 && $this->get_subsubmenu_id( ) != 0 )						
				$ret_string .= $wpdb->prepare( " AND ( product.menulevel1_id_3 = %s OR product.menulevel2_id_3 = %s OR product.menulevel3_id_3 = %s )", $this->get_subsubmenu_id( ), $this->get_subsubmenu_id( ), $this->get_subsubmenu_id( ) );
			
			if( $this->search != "" ){
				$exploded_search = explode( ' ', $this->search );
				$ret_string .= " AND (";
				$item_num = 0;
				foreach( $exploded_search as $search_item ){
					$search_clean = $this->mysqli->clean_search( '%' . $search_item . '%' );
					
					$search_terms = array( );
					if( get_option( 'ec_option_search_title' ) )
						$search_terms[] = "product.title";
						
					if( get_option( 'ec_option_search_model_number' ) )
						$search_terms[] = "product.model_number";
					
					if( get_option( 'ec_option_search_manufacturer' ) )
						$search_terms[] = "manufacturer.name";
						
					if( get_option( 'ec_option_search_description' ) )
						$search_terms[] = "product.description";
					
					if( get_option( 'ec_option_search_short_description' ) )
						$search_terms[] = "product.short_description";
					
					if( get_option( 'ec_option_search_menu' ) ){
						$search_terms[] = "ec_menulevel1.name";
						$search_terms[] = "ec_menulevel2.name";
						$search_terms[] = "ec_menulevel3.name";
					}
					
					$search_terms = apply_filters( 'wpeasycart_search_terms', $search_terms );
					
					if( $item_num > 0 && get_option( 'ec_option_search_by_or' ) )
						$ret_string .= " OR";
					else if( $item_num > 0 )
						$ret_string .= " AND";
					
					$ret_string .= " ( ";
					for( $j=0; $j<count( $search_terms ); $j++ ){
						if( $j > 0 )
							$ret_string .= " OR ";
						$ret_string .= $search_terms[$j] . " LIKE " . $search_clean;
					}
					$ret_string .= " ) ";
					$item_num++;
				}
				$ret_string .= " )";
				
			}
			
			if( $this->show_on_startup )							$ret_string .= " AND product.show_on_startup = 1 ";
			
			if( $this->product_only )								$ret_string .= " AND product.model_number = '" . $this->model_number . "' ";
			
			if( $this->manufacturer->manufacturer_id != 0 )			$ret_string .= " AND product.manufacturer_id = ".$this->manufacturer->manufacturer_id;
			
			if( $this->group_id != 0 )								$ret_string .= " AND ec_categoryitem.category_id = ".$this->group_id;
																	
			if( $this->pricepoint_id != 0 )							$ret_string .= $this->get_price_point_where( );
			
																	return $ret_string;
			
		}else														return " WHERE product.show_on_startup = 1 AND product.activate_in_store = 1";
	}
	
	public function get_order_by_query( $page_options = NULL ){
		
		if(isset($_GET['ec_search']))								return " ORDER BY search_match_score DESC, product.title ASC";
		else if($this->current_filter == 1)							return " ORDER BY product.price ASC";
		else if($this->current_filter == 2)							return " ORDER BY product.price DESC";
		else if($this->current_filter == 3)							return " ORDER BY product.title ASC";
		else if($this->current_filter == 4)							return " ORDER BY product.title DESC";
		else if($this->current_filter == 5)							return " ORDER BY product.added_to_db_date DESC";
		else if($this->current_filter == 6)							return " ORDER BY review_average DESC";
		else if($this->current_filter == 7)							return " ORDER BY product.views DESC";
		else if( isset( $page_options ) && isset( $page_options->product_order ) ){
			
			$order = json_decode( stripslashes( $page_options->product_order ) );
			$ret_string = " ORDER BY FIELD( model_number";
			foreach( $order as $model_number ){
				$ret_string .= ", '" . $model_number . "'";
			}
			$ret_string .= " )";
			return $ret_string;
		
		}else														return " ORDER BY product.price ASC";
	}
	
	private function has_filters( ){
		
		if(	$this->get_menu_level() != 0 || 
			( isset( $this->manufacturer ) && $this->manufacturer->manufacturer_id != 0 && $this->manufacturer->manufacturer_id != 0 ) || 
			$this->group_id != 0 ||
			$this->pricepoint_id != 0 || 
			$this->model_number != "" || 
			$this->search != ""  ||
			$this->show_on_startup
		  )
																	return true;
		else														return false;
			
	}
	
	private function get_price_point_where( ){
		$pricepoint_row = $this->mysqli->get_pricepoint_row( $this->pricepoint_id );
		if( $pricepoint_row->is_less_than )
			return " AND product.price < " . $pricepoint_row->high_point;
		else if( $pricepoint_row->is_greater_than )
			return " AND product.price > " . $pricepoint_row->low_point;
		else
			return " AND product.price <= " . $pricepoint_row->high_point . " AND product.price >= " . $pricepoint_row->low_point;
	}
	
	private function ec_get_permalink( $postid, $link_type, $menu_row = NULL ){
		
		if( !get_option( 'ec_option_use_old_linking_style' ) && $postid != "0" ){
			return get_permalink( $postid );
		}else{
			if( $link_type == "store" )
				return $this->store_page . $this->permalink_divider;
			else if( $link_type == "menu" )
				return $this->store_page . $this->permalink_divider . "menuid=" . $this->get_menu1_id( ) . "&menuname=" . $this->get_menu_name( );
			else if( $link_type == "submenu" )
				return $this->store_page . $this->permalink_divider . "submenuid=" . $this->get_menu2_id( ) . "&submenuname=" . $this->get_submenu_name( );
			else if( $link_type == "subsubmenu" )
				return $this->store_page . $this->permalink_divider . "subsubmenuid=" . $this->get_menu3_id( ) . "&subsubmenuname=" . $this->get_subsubmenu_name( );
			else if( $link_type == "menurow" ){
				if( isset( $_GET['subsubmenuid'] ) )
					return $this->store_page . $this->permalink_divider . "subsubmenuid=" . $menu_row->menulevel3_id . "&subsubmenuname=" . $menu_row->name;
				else if( isset( $_GET['submenuid'] ) )
					return $this->store_page . $this->permalink_divider . "submenuid=" . $menu_row->menulevel2_id . "&submenuname=" . $menu_row->name;
				else if( isset( $_GET['menuid'] ) )
					return $this->store_page . $this->permalink_divider . "menuid=" . $menu_row->menulevel1_id . "&menuname=" . $menu_row->name;
			}
		}
		
	}
	
}

?>