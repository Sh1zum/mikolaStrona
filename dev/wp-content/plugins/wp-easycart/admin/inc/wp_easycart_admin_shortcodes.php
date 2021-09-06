<?php
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'wp_easycart_admin_shortcodes' ) ) :

final class wp_easycart_admin_shortcodes{
	
	protected static $_instance = null;
	
	public static function instance( ) {
		
		if( is_null( self::$_instance ) ) {
			self::$_instance = new self(  );
		}
		return self::$_instance;
	
	}
		
	public function __construct( ){
		add_filter( "mce_external_plugins", array( $this, 'add_buttons' ) );
		add_filter( 'mce_buttons', array( $this, 'register_buttons' ) );
		add_action( 'admin_footer', array( $this, 'print_editor' ) );
	}
	
	/***********************************************************************************
	* BASIC SHORTCODE EDITOR FUNCTIONS
	************************************************************************************/
	public function add_buttons( $plugin_array ) {
		$plugin_array['wpeasycart'] = plugins_url( ) . '/wp-easycart/admin/js/editor.js';
		return $plugin_array;
	}
	public function register_buttons( $buttons ) {
		array_push( $buttons, 'ec_show_editor' );
		return $buttons;
	}
	
	public function print_editor( ){
		if( current_user_can( 'manage_options' ) ){
			$screen = get_current_screen( );
			if( $screen->parent_base == 'edit' ){
				echo "<div class=\"ec_editor_box_container\" id=\"ec_editor_window\">";
				echo "<a href=\"#\" class=\"ec_editor_close\" onclick=\"return ec_close_editor( );\"><span>x</span></a>";
				echo "<h3 class=\"ec_editor_heading\">" . __( 'Insert EasyCart Shortcodes', 'wp-easycart' ) . "</h3>";
				echo "<div class=\"ec_editor_inner_container\">";
				// Start Container Inner
				$this->print_editor_shortcode_menu( ); // Shortcode Menu
				// Store shortcode, no options, nothing needed
				$this->print_editor_categories( );// Store Table Shortcode Panel
				$this->print_editor_category_view( );// Store Table Shortcode Panel
				$this->print_editor_store_table( );// Store Table Shortcode Panel
				$this->print_editor_product_menu( );// Product Menu Store Shortcode Panel
				$this->print_editor_product_category( );// Product Category Store Shortcode Panel
				$this->print_editor_manufacturer_group( );// Manufacturer Group Store Shortcode Panel
				$this->print_editor_product_details( );// Product Details Store Shortcode Panel
				// Cart shortcode, no options, nothing needed
				// Account shortcode, no options, nothing needed
				$this->print_editor_single_product( );// Single Product Shortcode Panel
				$this->print_editor_multiple_products( );// Multiple Products Shortcode Panel
				$this->print_editor_add_to_cart( );// Add to Cart Shortcode Panel
				// Cart Display shortcode, no options, nothing needed
				$this->print_editor_membership_content( );// Add to Cart Shortcode Panel
				// End Container Inner
				echo "</div>";
				echo "</div>";
				echo "<div class=\"ec_editor_overlay\" id=\"ec_editor_bg\"></div>";
				echo "<script>jQuery( document.getElementById( 'ec_editor_window' ) ).appendTo( document.body );</script>";
				echo "<script>jQuery( document.getElementById( 'ec_editor_bg' ) ).appendTo( document.body );</script>";
			}
		}
	}
	
	// Shortcode Menu
	public function print_editor_shortcode_menu( ){
		echo "<ul class=\"ec_column_holder\" id=\"ec_shortcode_menu\">";
			echo "<li data-ecshortcode=\"ec_store\"><div>" . __( 'STORE', 'wp-easycart' ) . "</div></li>";
			echo "<li data-ecshortcode=\"ec_categories\"><div>" . __( 'CATEGORIES', 'wp-easycart' ) . "</div></li>";
			echo "<li data-ecshortcode=\"ec_category_view\"><div>" . __( 'CATEGORY DISPLAY', 'wp-easycart' ) . "</div></li>";
			echo "<li data-ecshortcode=\"ec_store_table\"><div>" . __( 'STORE TABLE', 'wp-easycart' ) . "</div></li>";
			echo "<li data-ecshortcode=\"ec_menu\"><div>" . __( 'PRODUCT MENU', 'wp-easycart' ) . "</div></li>";
			echo "<li data-ecshortcode=\"ec_category\"><div>" . __( 'PRODUCT CATEGORY', 'wp-easycart' ) . "</div></li>";
			echo "<li data-ecshortcode=\"ec_manufacturer\"><div>" . __( 'MANUFACTURER GROUP', 'wp-easycart' ) . "</div></li>";
			echo "<li data-ecshortcode=\"ec_productdetails\"><div>" . __( 'PRODUCT DETAILS', 'wp-easycart' ) . "</div></li>";
			echo "<li data-ecshortcode=\"ec_cart\"><div>" . __( 'CART', 'wp-easycart' ) . "</div></li>";
			echo "<li data-ecshortcode=\"ec_account\"><div>" . __( 'ACCOUNT', 'wp-easycart' ) . "</div></li>";
			echo "<li data-ecshortcode=\"ec_singleitem\"><div>" . __( 'SINGLE ITEM', 'wp-easycart' ) . "</div></li>";
			echo "<li data-ecshortcode=\"ec_selecteditems\"><div>" . __( 'SELECT ITEMS', 'wp-easycart' ) . "</div></li>";
			if( !file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/head_content.php" ) ){
			echo "<li data-ecshortcode=\"ec_addtocart\"><div>" . __( 'ADD TO CART BUTTON', 'wp-easycart' ) . "</div></li>";
			echo "<li data-ecshortcode=\"ec_cartdisplay\"><div>" . __( 'CART DISPLAY', 'wp-easycart' ) . "</div></li>";
			}
			echo "<li data-ecshortcode=\"ec_membership\"><div>" . __( 'MEMBERSHIP CONTENT', 'wp-easycart' ) . "</div></li>";
		echo "</ul>";
	}
	
	/***********************************************************************************
	* BEGIN FUNCTIONS FOR THE CATEGORIES PANEL
	************************************************************************************/
	// Product Menu Shortcode Creator Panel
	public function print_editor_categories( ){
		echo "<div class=\"ec_editor_panel\" id=\"ec_categories\">";
			echo "<div class=\"ec_editor_select_row\"><input type=\"button\" value=\"" . __( 'BACK', 'wp-easycart' ) . "\" class=\"ec_editor_button backlink\"></div>";
			echo "<div class=\"ec_editor_help_text\">" . __( 'Select a category if you would like to display a categories children, otherwise no selection will show all categories with no parent.', 'wp-easycart' ) . "</div>";
			echo "<div class=\"ec_editor_select_row\"><span class=\"ec_editor_select_row_label\">" . __( 'Category', 'wp-easycart' ) . ":</span><span class=\"ec_editor_select_row_input\">";
			$this->print_category_select_none_optional( 'ec_editor_categories_category_select' );
			echo "</span></div>";
			echo "<div class=\"ec_editor_submit_row\"><span class=\"ec_editor_select_row_input\"><input type=\"button\" value=\"" . __( 'ADD SHORTCODE', 'wp-easycart' ) . "\" id=\"ec_add_categories\" class=\"ec_editor_button\"></span></div>";
			
		echo "</div>";
	}
	
	/***********************************************************************************
	* BEGIN FUNCTIONS FOR THE CATEGORIES PANEL
	************************************************************************************/
	// Product Menu Shortcode Creator Panel
	public function print_editor_category_view( ){
		echo "<div class=\"ec_editor_panel\" id=\"ec_category_view\">";
			echo "<div class=\"ec_editor_select_row\"><input type=\"button\" value=\"" . __( 'BACK', 'wp-easycart' ) . "\" class=\"ec_editor_button backlink\"></div>";
			echo "<div class=\"ec_editor_help_text\">" . __( 'You may select a category if you would like to show a category\'s children or leave with no selection to show all categories with no parent.', 'wp-easycart' ) . "</div>";
			echo "<div class=\"ec_editor_select_row\"><span class=\"ec_editor_select_row_label\">" . __( 'Category', 'wp-easycart' ) . ":</span><span class=\"ec_editor_select_row_input\">";
			$this->print_category_select_none_optional( 'ec_editor_category_view_category_select' );
			echo "</span></div>";
			echo "<div class=\"ec_editor_select_row\"><span class=\"ec_editor_select_row_label\">" . __( 'Columns', 'wp-easycart' ) . ":</span><span class=\"ec_editor_select_row_input\">";
			$this->print_columns( 'ec_editor_category_view_columns', 3 );
			echo "</span></div>";
			echo "<div class=\"ec_editor_submit_row\"><span class=\"ec_editor_select_row_input\"><input type=\"button\" value=\"" . __( 'ADD SHORTCODE', 'wp-easycart' ) . "\" id=\"ec_add_category_view\" class=\"ec_editor_button\"></span></div>";
			
		echo "</div>";
	}
	
	public function print_category_select_none_optional( $id ){
		echo "<select class=\"ec_editor_select_box\" id=\"" . $id . "\">";
		$db = new ec_db( );
		$category_items = $db->get_category_list( );
		if( count( $category_items ) > 0 ){
			echo "<option value=\"0\">" . __( 'Show Featured Categories', 'wp-easycart' ) . "</option>";
			echo "<option value=\"-1\">" . __( 'Show Top Level Categories', 'wp-easycart' ) . "</option>";
			foreach( $category_items as $category ){
				echo "<option value=\"" . $category->category_id . "\">" . $category->category_name . "</option>";
			}
		}else{
			echo "<option value=\"0\">" . __( 'No Category Items Exist', 'wp-easycart' ) . "</option>";
		}
		echo "</select>";
	}
	
	public function print_columns( $id, $val ){
		echo "<input type=\"text\" id=\"" . $id . "\" value=\"" . $val . "\" class=\"ec_editor_input_box\" />";
	}
	
	/***********************************************************************************
	* BEGIN FUNCTIONS FOR THE STORE TABLE PANEL
	************************************************************************************/
	// Product Menu Shortcode Creator Panel
	public function print_editor_store_table( ){
		echo "<div class=\"ec_editor_panel\" id=\"ec_store_table\">";
			echo "<div class=\"ec_editor_select_row\"><input type=\"button\" value=\"" . __( 'BACK', 'wp-easycart' ) . "\" class=\"ec_editor_button backlink\"></div>";
			echo "<div class=\"ec_editor_error\" id=\"ec_product_menu_error\"><span>" . __( 'Please select a menu item at the minimum', 'wp-easycart' ) . "</span></div>";
			echo "<div class=\"ec_editor_help_text\">" . __( 'If you select nothing, all products will be shown in alphabetical order. You may select products, menus (3 levels) and/or categories to display in a table list view. You may also customize the columns that are displayed. All products are ordered by title from A-Z.', 'wp-easycart' ) . "</div>";
			echo "<div class=\"ec_editor_select_row\"><span class=\"ec_editor_select_row_label\">" . __( 'Products', 'wp-easycart' ) . ":</span><span class=\"ec_editor_select_row_input\">";
			$this->print_product_multiple_select( 'ec_editor_table_product_select' );
			echo "</span></div>";
			echo "<div class=\"ec_editor_select_row\"><span class=\"ec_editor_select_row_label\">" . __( 'Menu', 'wp-easycart' ) . ":</span><span class=\"ec_editor_select_row_input\">";
			$this->print_menu_multiple_select( 'ec_editor_table_menu_select' );
			echo "</div>";
			echo "<div class=\"ec_editor_select_row\"><span class=\"ec_editor_select_row_label\">" . __( 'Sub Menu', 'wp-easycart' ) . ":</span><span class=\"ec_editor_select_row_input\">";
			$this->print_submenu_multiple_select( 'ec_editor_table_submenu_select', 0 );
			echo "</span></div>";
			echo "<div class=\"ec_editor_select_row\"><span class=\"ec_editor_select_row_label\">" . __( 'SubSub Menu', 'wp-easycart' ) . ":</span><span class=\"ec_editor_select_row_input\">";
			$this->print_subsubmenu_multiple_select( 'ec_editor_table_subsubmenu_select', 0 );
			echo "</span></div>";
			echo "<div class=\"ec_editor_select_row\"><span class=\"ec_editor_select_row_label\">" . __( 'Category', 'wp-easycart' ) . ":</span><span class=\"ec_editor_select_row_input\">";
			$this->print_category_multiple_select( 'ec_editor_table_category_select' );
			echo "</span></div>";
			$default_fields = array( 'model_number', 'title', 'price', 'details_link' );
			$default_labels = array( __( 'SKU', 'wp-easycart' ), __( 'Product Name', 'wp-easycart' ), __( 'Price', 'wp-easycart' ), __( 'More', 'wp-easycart' ) );
			for( $j=0; $j<count( $default_fields ); $j++ ){
				echo "<div class=\"ec_editor_select_row\" id=\"ec_editor_table_column_" . $j . "\"><span class=\"ec_editor_select_row_label\">" . __( 'Column', 'wp-easycart' ) . " " . ($j+1) . "</span><span class=\"ec_editor_select_row_input\">";
				$this->print_product_label_input( 'ec_editor_table_label_' . $j, $default_labels[$j] );
				echo " ";
				$this->print_product_field_list_box( 'ec_editor_table_field_' . $j, $default_fields[$j] );
				echo "</span></div>";
			}
			echo "<div class=\"ec_editor_select_row\" id=\"ec_editor_table_column_" . $j . "\"><span class=\"ec_editor_select_row_label\">" . __( 'Link Label (optional)', 'wp-easycart' ) . "</span><span class=\"ec_editor_select_row_input\">";
			$this->print_product_label_input( 'ec_editor_table_view_details_text', "view more" );
			echo "</span></div>";
			echo "<div class=\"ec_editor_submit_row\"><span class=\"ec_editor_select_row_input\"><input type=\"button\" value=\"" . __( 'ADD SHORTCODE', 'wp-easycart' ) . "\" id=\"ec_add_store_table\" class=\"ec_editor_button\"></span></div>";
			
		echo "</div>";
	}
	
	public function print_product_label_input( $id, $val ){
		echo "<input type=\"text\" id=\"" . $id . "\" value=\"" . $val . "\" class=\"ec_editor_input_box\" />";
	}
	
	public function print_product_field_list_box( $id, $selected ){
		echo "<select class=\"ec_editor_select_box\" id=\"" . $id . "\">";
		$items = array( 'product_id', 'model_number', 'title', 'price', 'details_link', 'description', 'specifications', 'stock_quantity', 'weight', 'width', 'height', 'length' );
		echo "<option value=\"0\">" . __( 'Select a Product Field', 'wp-easycart' ) . "</option>";
		foreach( $items as $item ){
			echo "<option value=\"" . $item. "\"";
			if( $item == $selected )
				echo " selected=\"selected\"";
			echo ">" . $item . "</option>";
		}
		echo "</select>";
	}
	
	/***********************************************************************************
	* BEGIN FUNCTIONS FOR THE PRODUCT MENU PANEL
	************************************************************************************/
	// Product Menu Shortcode Creator Panel
	public function print_editor_product_menu( ){
		echo "<div class=\"ec_editor_panel\" id=\"ec_product_menu\">";
			echo "<div class=\"ec_editor_select_row\"><input type=\"button\" value=\"" . __( 'BACK', 'wp-easycart' ) . "\" class=\"ec_editor_button backlink\"></div>";
			echo "<div class=\"ec_editor_error\" id=\"ec_product_menu_error\"><span>" . __( 'Please select a menu item at the minimum', 'wp-easycart' ) . "</span></div>";
			echo "<div class=\"ec_editor_help_text\">" . __( 'To display a product menu item page, select a menu item below. If you want to display a sub menu or a subsub menu, then select the menu, followed by the submenu and/or the subsubmenu.', 'wp-easycart' ) . "</div>";
			echo "<div class=\"ec_editor_select_row\"><span class=\"ec_editor_select_row_label\">" . __( 'Menu', 'wp-easycart' ) . ":</span><span id=\"ec_editor_menu_holder\" class=\"ec_editor_select_row_input\">";
			$this->print_menu_select( 'ec_editor_menu_select' );
			echo "</div>";
			echo "<div class=\"ec_editor_select_row\"><span class=\"ec_editor_select_row_label\">" . __( 'Sub Menu', 'wp-easycart' ) . ":</span><span id=\"ec_editor_submenu_holder\" class=\"ec_editor_select_row_input\">";
			$this->print_submenu_select( 'ec_editor_submenu_select', 0 );
			echo "</span></div>";
			echo "<div class=\"ec_editor_select_row\"><span class=\"ec_editor_select_row_label\">" . __( 'SubSub Menu', 'wp-easycart' ) . ":</span><span id=\"ec_editor_subsubmenu_holder\" class=\"ec_editor_select_row_input\">";
			$this->print_subsubmenu_select( 'ec_editor_subsubmenu_select', 0 );
			echo "</span></div>";
			echo "<div class=\"ec_editor_submit_row\"><span class=\"ec_editor_select_row_input\"><input type=\"button\" value=\"" . __( 'ADD SHORTCODE', 'wp-easycart' ) . "\" id=\"ec_add_product_menu\" class=\"ec_editor_button\"></span></div>";
			
		echo "</div>";
	}
	
	// Print all main menu items in a select box
	public function print_menu_select( $id ){
		echo "<select class=\"ec_editor_select_box\" id=\"" . $id . "\" onchange=\"ec_editor_select_menu_change( );\">";
		$db = new ec_db( );
		$menu_items = $db->get_menulevel1_items( );
		if( count( $menu_items ) > 0 ){
			echo "<option value=\"0\">" . __( 'Select a Menu Item', 'wp-easycart' ) . "</option>";
			foreach( $menu_items as $menu ){
				echo "<option value=\"" . $menu->menulevel1_id . "\">" . $menu->menu1_name . "</option>";
			}
		}else{
			echo "<option value=\"0\">" . __( 'No Menu Items Exist', 'wp-easycart' ) . "</option>";
		}
		echo "</select>";
	}
	
	// Print all main menu items in a select box
	public function print_menu_multiple_select( $id ){
		echo "<select multiple=\"multiple\" class=\"ec_editor_select_box\" id=\"" . $id . "\">";
		$db = new ec_db( );
		$menu_items = $db->get_menulevel1_items( );
		if( count( $menu_items ) > 0 ){
			echo "<option value=\"0\">" . __( 'Select a Menu Item', 'wp-easycart' ) . "</option>";
			foreach( $menu_items as $menu ){
				echo "<option value=\"" . $menu->menulevel1_id . "\">" . $menu->menu1_name . "</option>";
			}
		}else{
			echo "<option value=\"0\">" . __( 'No Menu Items Exist', 'wp-easycart' ) . "</option>";
		}
		echo "</select>";
	}
	
	// Print all sub menu items for a particular menu item in a select box
	public function print_submenu_select( $id, $menuid ){
		echo "<select class=\"ec_editor_select_box\" id=\"" . $id . "\" onchange=\"ec_editor_select_submenu_change( );\">";
		if( $menuid > 0 ){
			$db = new ec_db( );
			$menu_items = $db->get_menulevel2_items( );
			if( count( $menu_items ) > 0 ){
				echo "<option value=\"0\">" . __( 'Select a Menu Item (optional)', 'wp-easycart' ) . "</option>";
				foreach( $menu_items as $menu ){
					if( $menu->menulevel1_id == $menuid ){
						echo "<option value=\"" . $menu->menulevel2_id . "\">" . $menu->menu2_name . "</option>";
					}
				}
			}else{
				echo "<option value=\"0\">" . __( 'No SubMenu Items Exist', 'wp-easycart' ) . "</option>";
			}
		}else{
			echo "<option value=\"0\">" . __( 'No Menu Item Selected', 'wp-easycart' ) . "</option>";
		}
		echo "</select>";
	}
	
	// Print all sub menu items for a particular menu item in a multi select box
	public function print_submenu_multiple_select( $id, $menuid ){
		echo "<select multiple=\"multiple\" class=\"ec_editor_select_box\" id=\"" . $id . "\">";
		$db = new ec_db( );
		$menu_items = $db->get_menulevel2_items( );
		if( count( $menu_items ) > 0 ){
			echo "<option value=\"0\">" . __( 'Select a Menu Item (optional)', 'wp-easycart' ) . "</option>";
			foreach( $menu_items as $menu ){
				echo "<option value=\"" . $menu->menulevel2_id . "\">" . $menu->menu2_name . "</option>";
			}
		}else{
			echo "<option value=\"0\">" . __( 'No SubMenu Items Exist', 'wp-easycart' ) . "</option>";
		}
		echo "</select>";
	}
	
	// Print all sub sub menu items for a particular menu item in a select box
	public function print_subsubmenu_select( $id, $submenuid ){
		echo "<select class=\"ec_editor_select_box\" id=\"" . $id . "\">";
		if( $submenuid > 0 ){
			$db = new ec_db( );
			$menu_items = $db->get_menulevel3_items( );
			if( count( $menu_items ) > 0 ){
				echo "<option value=\"0\">" . __( 'Select a SubSub Menu Item (optional)', 'wp-easycart' ) . "</option>";
				foreach( $menu_items as $menu ){
					if( $menu->menulevel2_id == $submenuid ){
						echo "<option value=\"" . $menu->menulevel3_id . "\">" . $menu->menu3_name . "</option>";
					}
				}
			}else{
				echo "<option value=\"0\">" . __( 'No SubSubMenu Items Exist', 'wp-easycart' ) . "</option>";
			}
		}else{
			echo "<option value=\"0\">" . __( 'No Sub Menu Item Selected', 'wp-easycart' ) . "</option>";
		}
		echo "</select>";
	}
	
	// Print all sub sub menu items for a particular menu item in a multiple select box
	public function print_subsubmenu_multiple_select( $id, $submenuid ){
		echo "<select multiple=\"multiple\" class=\"ec_editor_select_box\" id=\"" . $id . "\">";
		$db = new ec_db( );
		$menu_items = $db->get_menulevel3_items( );
		if( count( $menu_items ) > 0 ){
			echo "<option value=\"0\">" . __( 'Select a SubSub Menu Item (optional)', 'wp-easycart' ) . "</option>";
			foreach( $menu_items as $menu ){
				echo "<option value=\"" . $menu->menulevel3_id . "\">" . $menu->menu3_name . "</option>";
			}
		}else{
			echo "<option value=\"0\">" . __( 'No SubSubMenu Items Exist', 'wp-easycart' ) . "</option>";
		}
		echo "</select>";
	}
	
	// Ajax calls
	public function editor_update_sub_menu( ){
		if( current_user_can( 'manage_options' ) ){
			$id = $_POST['id'];
			$menuid = $_POST['menuid'];
			$this->print_submenu_select( $id, $menuid );
			die( );
		}
	}
	
	public function editor_update_subsub_menu( ){
		if( current_user_can( 'manage_options' ) ){
			$id = $_POST['id'];
			$submenuid = $_POST['submenuid'];
			$this->print_subsubmenu_select( $id, $submenuid );
			die( );
		}
	}
	
	/***********************************************************************************
	* BEGIN FUNCTIONS FOR THE PRODUCT CATEGORY PANEL
	************************************************************************************/
	// Product Category Shortcode Creator Panel
	public function print_editor_product_category( ){
		echo "<div class=\"ec_editor_panel\" id=\"ec_product_category\">";
			echo "<div class=\"ec_editor_select_row\"><input type=\"button\" value=\"" . __( 'BACK', 'wp-easycart' ) . "\" class=\"ec_editor_button backlink\"></div>";
			echo "<div class=\"ec_editor_error\" id=\"ec_product_category_error\"><span>" . __( 'Please select a category item', 'wp-easycart' ) . "</span></div>";
			echo "<div class=\"ec_editor_help_text\">" . __( 'This shortcode displays a category group which can be created in the store admin in the submenu of the products section.', 'wp-easycart' ) . "</div>";
			echo "<div class=\"ec_editor_select_row\"><span class=\"ec_editor_select_row_label\">" . __( 'Category', 'wp-easycart' ) . ":</span><span class=\"ec_editor_select_row_input\">";
			$this->print_category_select( 'ec_editor_category_select' );
			echo "</div>";
			echo "<div class=\"ec_editor_submit_row\"><span class=\"ec_editor_select_row_input\"><input type=\"button\" value=\"" . __( 'ADD SHORTCODE', 'wp-easycart' ) . "\" id=\"ec_add_product_category\" class=\"ec_editor_button\"></span></div>";
			
		echo "</div>";
	}
	
	// Print all main category items in a select box
	public function print_category_select( $id ){
		echo "<select class=\"ec_editor_select_box\" id=\"" . $id . "\">";
		$db = new ec_db( );
		$category_items = $db->get_category_list( );
		if( count( $category_items ) > 0 ){
			echo "<option value=\"0\">" . __( 'Select a Category Item', 'wp-easycart' ) . "</option>";
			foreach( $category_items as $category ){
				echo "<option value=\"" . $category->category_id . "\">" . $category->category_name . "</option>";
			}
		}else{
			echo "<option value=\"0\">" . __( 'No Category Items Exist', 'wp-easycart' ) . "</option>";
		}
		echo "</select>";
	}
	
	// Print all main categirt items in a multi select box
	public function print_category_multiple_select( $id ){
		echo "<select multiple=\"multiple\" class=\"ec_editor_select_box\" id=\"" . $id . "\">";
		$db = new ec_db( );
		$category_items = $db->get_category_list( );
		if( count( $category_items ) > 0 ){
			echo "<option value=\"0\">" . __( 'Select a Category Item', 'wp-easycart' ) . "</option>";
			foreach( $category_items as $category ){
				echo "<option value=\"" . $category->category_id . "\">" . $category->category_name . "</option>";
			}
		}else{
			echo "<option value=\"0\">" . __( 'No Category Items Exist', 'wp-easycart' ) . "</option>";
		}
		echo "</select>";
	}
	
	/***********************************************************************************
	* BEGIN FUNCTIONS FOR THE MANUFACTURER GROUP PANEL
	************************************************************************************/
	// Product Category Shortcode Creator Panel
	public function print_editor_manufacturer_group( ){
		echo "<div class=\"ec_editor_panel\" id=\"ec_manufacturer_group\">";
			echo "<div class=\"ec_editor_select_row\"><input type=\"button\" value=\"" . __( 'BACK', 'wp-easycart' ) . "\" class=\"ec_editor_button backlink\"></div>";
			echo "<div class=\"ec_editor_error\" id=\"ec_manufacturer_group_error\"><span>" . __( 'Please select a manufacturer', 'wp-easycart' ) . "</span></div>";
			echo "<div class=\"ec_editor_help_text\">" . __( 'This shortcode displays a manufacturer group, which consists of all products assigned to the selected manufacturer (think of it as a product filter by manufacturer).', 'wp-easycart' ) . "</div>";
			echo "<div class=\"ec_editor_select_row\"><span class=\"ec_editor_select_row_label\">" . __( 'Manufacturer', 'wp-easycart' ) . ":</span><span class=\"ec_editor_select_row_input\">";
			$this->print_manufacturer_select( 'ec_editor_manufacturer_select' );
			echo "</div>";
			echo "<div class=\"ec_editor_submit_row\"><span class=\"ec_editor_select_row_input\"><input type=\"button\" value=\"" . __( 'ADD SHORTCODE', 'wp-easycart' ) . "\" id=\"ec_add_manufacturer_group\" class=\"ec_editor_button\"></span></div>";
			
		echo "</div>";
	}
	
	// Print all main menu items in a select box
	public function print_manufacturer_select( $id ){
		echo "<select class=\"ec_editor_select_box\" id=\"" . $id . "\">";
		$db = new ec_db( );
		$manufacturers = $db->get_manufacturer_list( );
		if( count( $manufacturers ) > 0 ){
			echo "<option value=\"0\">" . __( 'Select a Manufacturer', 'wp-easycart' ) . "</option>";
			foreach( $manufacturers as $manufacturer ){
				echo "<option value=\"" . $manufacturer->manufacturer_id . "\">" . $manufacturer->name . "</option>";
			}
		}else{
			echo "<option value=\"0\">" . __( 'No Manufacturers Exist', 'wp-easycart' ) . "</option>";
		}
		echo "</select>";
	}
	
	/***********************************************************************************
	* BEGIN FUNCTIONS FOR THE PRODUCT DETAILS PANEL
	************************************************************************************/
	// Product Category Shortcode Creator Panel
	public function print_editor_product_details( ){
		echo "<div class=\"ec_editor_panel\" id=\"ec_productdetails_menu\">";
			echo "<div class=\"ec_editor_select_row\"><input type=\"button\" value=\"" . __( 'BACK', 'wp-easycart' ) . "\" class=\"ec_editor_button backlink\"></div>";
			echo "<div class=\"ec_editor_error\" id=\"ec_productdetails_error\"><span>" . __( 'Please Select a Product', 'wp-easycart' ) . "</span></div>";
			echo "<div class=\"ec_editor_help_text\">" . __( 'This shortcode displays a single product\'s details on the specified page.', 'wp-easycart' ) . "</div>";
			echo "<div class=\"ec_editor_select_row\"><span class=\"ec_editor_select_row_label\">" . __( 'Product Model Number', 'wp-easycart' ) . ":</span><span class=\"ec_editor_select_row_input\">";
			$this->print_productdetails_select( 'ec_editor_productdetails_select' );
			echo "</div>";
			echo "<div class=\"ec_editor_submit_row\"><span class=\"ec_editor_select_row_input\"><input type=\"button\" value=\"" . __( 'ADD SHORTCODE', 'wp-easycart' ) . "\" id=\"ec_add_productdetails\" class=\"ec_editor_button\"></span></div>";
			
		echo "</div>";
	}
	
	// Print all main menu items in a select box
	public function print_productdetails_select( $id ){
		
		global $wpdb;
		$total = $wpdb->get_var( "SELECT COUNT( ec_product.product_id ) as total FROM ec_product" );
		
		if( $total > 500 ){
			
			echo "<input type=\"text\" class=\"ec_editor_select_box\" id=\"" . $id . "\">";
			
		}else{
			
			echo "<select class=\"ec_editor_select_box\" id=\"" . $id . "\">";
			$products = $wpdb->get_results( "SELECT ec_product.title, ec_product.model_number FROM ec_product ORDER BY ec_product.title" );
			if( count( $products ) > 0 ){
				echo "<option value=\"0\">" . __( 'Select a Product', 'wp-easycart' ) . "</option>";
				for( $i=0; $i<count( $products ); $i++ ){
					echo "<option value=\"" . $products[$i]->model_number . "\">" . $products[$i]->title . "</option>";
				}
			}else{
				echo "<option value=\"0\">" . __( 'No Products Exist', 'wp-easycart' ) . "</option>";
			}
			echo "</select>";
			
		}
		
	}
	
	/***********************************************************************************
	* BEGIN FUNCTIONS FOR THE SINGLE PRODUCT PANEL
	************************************************************************************/
	// Product Category Shortcode Creator Panel
	public function print_editor_single_product( ){
		echo "<div class=\"ec_editor_panel\" id=\"ec_single_product\">";
			echo "<div class=\"ec_editor_select_row\"><input type=\"button\" value=\"" . __( 'BACK', 'wp-easycart' ) . "\" class=\"ec_editor_button backlink\"></div>";
			echo "<div class=\"ec_editor_error\" id=\"ec_single_product_error\"><span>" . __( 'Please Select a Product', 'wp-easycart' ) . "</span></div>";
			echo "<div class=\"ec_editor_help_text\">" . __( 'This shortcode displays a single product with a view details button.', 'wp-easycart' ) . "</div>";
			echo "<div class=\"ec_editor_select_row\"><span class=\"ec_editor_select_row_label\">" . __( 'Product ID', 'wp-easycart' ) . ":</span><span class=\"ec_editor_select_row_input\">";
			$this->print_product_select( 'ec_editor_single_product_select' );
			echo "</div>";
			echo "<div class=\"ec_editor_select_row\"><span class=\"ec_editor_select_row_label\">" . __( 'Display Type', 'wp-easycart' ) . ":</span><span class=\"ec_editor_select_row_input\">";
			$this->print_product_display_type_select( 'ec_editor_single_product_display_type' );
			echo "</div>";
			echo "<div class=\"ec_editor_submit_row\"><span class=\"ec_editor_select_row_input\"><input type=\"button\" value=\"" . __( 'ADD SHORTCODE', 'wp-easycart' ) . "\" id=\"ec_add_single_product\" class=\"ec_editor_button\"></span></div>";
			
		echo "</div>";
	}
	
	// Print all main menu items in a select box
	public function print_product_select( $id ){
		
		global $wpdb;
		$total = $wpdb->get_var( "SELECT COUNT( ec_product.product_id ) as total FROM ec_product" );
		
		if( $total > 500 ){
			
			echo "<input type=\"text\" class=\"ec_editor_select_box\" id=\"" . $id . "\">";
			
		}else{
			
			echo "<select class=\"ec_editor_select_box\" id=\"" . $id . "\">";
			$products = $wpdb->get_results( "SELECT ec_product.product_id, ec_product.title, ec_product.model_number FROM ec_product ORDER BY ec_product.title" );
			if( count( $products ) > 0 ){
				echo "<option value=\"0\">" . __( 'Select a Product', 'wp-easycart' ) . "</option>";
				for( $i=0; $i<count( $products ); $i++ ){
					echo "<option value=\"" . $products[$i]->product_id . "\">" . $products[$i]->title . "</option>";
				}
			}else{
				echo "<option value=\"0\">" . __( 'No Products Exist', 'wp-easycart' ) . "</option>";
			}
			echo "</select>";
			
		}
		
	}
	
	// Print the display types available for the product display
	public function print_product_display_type_select( $id ){
		echo "<select class=\"ec_editor_select_box\" id=\"" . $id . "\">";
			echo "<option value=\"1\" selected=\"selected\">" . __( 'Default Product Display Type', 'wp-easycart' ) . "</option>";
			echo "<option value=\"2\">" . __( 'Same as Product Widget Display', 'wp-easycart' ) . "</option>";
			echo "<option value=\"3\">" . __( 'Custom Display Type 1', 'wp-easycart' ) . "</option>";
		echo "</select>";
	}
	
	/***********************************************************************************
	* BEGIN FUNCTIONS FOR THE MULTIPLE PRODUCTS PANEL
	************************************************************************************/
	// Product Category Shortcode Creator Panel
	public function print_editor_multiple_products( ){
		echo "<div class=\"ec_editor_panel\" id=\"ec_multiple_products\">";
			echo "<div class=\"ec_editor_select_row\"><input type=\"button\" value=\"" . __( 'BACK', 'wp-easycart' ) . "\" class=\"ec_editor_button backlink\"></div>";
			echo "<div class=\"ec_editor_error\" id=\"ec_multiple_products_error\"><span>" . __( 'Please Select at Least One Product', 'wp-easycart' ) . "</span></div>";
			echo "<div class=\"ec_editor_help_text\">" . __( 'This shortcode displays multiple products that can be selected one at a time. Each is displayed with a view details button.', 'wp-easycart' ) . "</div>";
			echo "<div class=\"ec_editor_multiple_select_row\"><span class=\"ec_editor_select_row_label\">" . __( 'Product', 'wp-easycart' ) . ":</span><span class=\"ec_editor_select_row_input\">";
			$this->print_product_multiple_select( 'ec_editor_multiple_products_select' );
			echo "</div>";
			echo "<div class=\"ec_editor_select_row\"><span class=\"ec_editor_select_row_label\">" . __( 'Display Type', 'wp-easycart' ) . ":</span><span class=\"ec_editor_select_row_input\">";
			$this->print_product_display_type_select( 'ec_editor_multiple_products_display_type' );
			echo "</div>";
			if( file_exists( WP_PLUGIN_DIR . "/wp-easycart-data/design/theme/" . get_option( 'ec_option_base_theme' ) . "/admin_panel.php" ) ){
				echo "<div class=\"ec_editor_select_row\"><span class=\"ec_editor_select_row_label\">" . __( 'Columns', 'wp-easycart' ) . ":</span><span class=\"ec_editor_select_row_input\">";
				$this->print_product_columns_select( 'ec_editor_multiple_products_columns' );
				echo "</div>";
			}
			echo "<div class=\"ec_editor_submit_row\"><span class=\"ec_editor_select_row_input\"><input type=\"button\" value=\"" . __( 'ADD SHORTCODE', 'wp-easycart' ) . "\" id=\"ec_add_multiple_products\" class=\"ec_editor_button\"></span></div>";
			
		echo "</div>";
	}
	
	// Print all main menu items in a select box
	public function print_product_multiple_select( $id ){
		global $wpdb;
		$total = $wpdb->get_var( "SELECT COUNT( ec_product.product_id ) as total FROM ec_product" );
		
		if( $total > 500 ){
			
			echo "<input type=\"text\" class=\"ec_editor_select_box\" id=\"" . $id . "\">";
			
		}else{
			
			echo "<select multiple=\"multiple\" class=\"ec_editor_select_box\" id=\"" . $id . "\">";
			global $wpdb;
			$products = $wpdb->get_results( "SELECT ec_product.product_id, ec_product.title, ec_product.model_number FROM ec_product ORDER BY ec_product.title" );
			if( count( $products ) > 0 ){
				for( $i=0; $i<count( $products ); $i++ ){
					echo "<option value=\"" . $products[$i]->product_id . "\">" . $products[$i]->title . "</option>";
				}
			}else{
				echo "<option value=\"0\">" . __( 'No Products Exist', 'wp-easycart' ) . "</option>";
			}
			echo "</select>";
			
		}
	}
	
	public function print_product_columns_select( $id ){
		echo "<select class=\"ec_editor_select_box\" id=\"" . $id . "\">";
			echo "<option value=\"1\">1</option>";
			echo "<option value=\"2\">2</option>";
			echo "<option value=\"3\" selected=\"selected\">3</option>";
			echo "<option value=\"4\">4</option>";
			echo "<option value=\"5\">5</option>";
		echo "</select>";
	}
	
	/***********************************************************************************
	* BEGIN FUNCTIONS FOR THE ADD TO CART PANEL
	************************************************************************************/
	// Product Category Shortcode Creator Panel
	public function print_editor_add_to_cart( ){
		echo "<div class=\"ec_editor_panel\" id=\"ec_add_to_cart\">";
			echo "<div class=\"ec_editor_select_row\"><input type=\"button\" value=\"" . __( 'BACK', 'wp-easycart' ) . "\" class=\"ec_editor_button backlink\"></div>";
			echo "<div class=\"ec_editor_error\" id=\"ec_add_to_cart_error\"><span>" . __( 'Please Select a Product', 'wp-easycart' ) . "</span></div>";
			echo "<div class=\"ec_editor_help_text\">" . __( 'This shortcode displays an add to cart button (with options if attached) of a single product.', 'wp-easycart' ) . "</div>";
			echo "<div class=\"ec_editor_select_row\"><span class=\"ec_editor_select_row_label\">" . __( 'Product ID', 'wp-easycart' ) . ":</span><span class=\"ec_editor_select_row_input\">";
			$this->print_product_select( 'ec_editor_add_to_cart_product_select' );
			echo "</div>";
			echo "<div class=\"ec_editor_submit_row\"><span class=\"ec_editor_select_row_input\"><input type=\"button\" value=\"" . __( 'ADD SHORTCODE', 'wp-easycart' ) . "\" id=\"ec_add_add_to_cart\" class=\"ec_editor_button\"></span></div>";
			
		echo "</div>";
	}
	// Reusing the print product select option
	
	/***********************************************************************************
	* BEGIN FUNCTIONS FOR THE MEMBERSHIP CONTENT PANEL
	************************************************************************************/
	// Membership Content Creator Panel
	public function print_editor_membership_content( ){
		echo "<div class=\"ec_editor_panel\" id=\"ec_membership_menu\">";
			echo "<div class=\"ec_editor_select_row\"><input type=\"button\" value=\"" . __( 'BACK', 'wp-easycart' ) . "\" class=\"ec_editor_button backlink\"></div>";
			echo "<div class=\"ec_editor_error\" id=\"ec_membership_error\"><span>" . __( 'Please Select at Least One Product', 'wp-easycart' ) . "</span></div>";
			echo "<div class=\"ec_editor_help_text\">" . __( 'This shortcode allows you to require a user to be subscribed to a product or one product in a group of products. For example, you could create a single content page that has a bronze, silver, and gold membership level with content for all three, just silver and gold, and just gold. In addition, it gives you an alternate content area', 'wp-easycart' ) . "</div>";
			echo "<div class=\"ec_editor_multiple_select_row\"><span class=\"ec_editor_select_row_label\">" . __( 'Product(s)', 'wp-easycart' ) . ":</span><span class=\"ec_editor_select_row_input\">";
			$this->print_product_multiple_select( 'ec_editor_membership_multiple_product_select' );
			echo "</div>";
			echo "<div class=\"ec_editor_submit_row\"><span class=\"ec_editor_select_row_input\"><input type=\"button\" value=\"" . __( 'ADD SHORTCODE', 'wp-easycart' ) . "\" id=\"ec_add_membership\" class=\"ec_editor_button\"></span></div>";
			
		echo "</div>";
	}
	
}
endif; // End if class_exists check

function wp_easycart_admin_shortcodes( ){
	return wp_easycart_admin_shortcodes::instance( );
}
wp_easycart_admin_shortcodes( );

add_action( 'wp_ajax_ec_editor_update_sub_menu', 'wp_easycart_admin_editor_update_sub_menu' );
function wp_easycart_admin_editor_update_sub_menu( ){
	if( current_user_can( 'manage_options' ) ){
		$id = $_POST['id'];
		$menuid = $_POST['menuid'];
		wp_easycart_admin_shortcodes( )->print_submenu_select( $id, $menuid );
		die( );
	}
}
add_action( 'wp_ajax_ec_editor_update_subsub_menu', 'wp_easycart_admin_editor_update_subsub_menu' );
function wp_easycart_admin_editor_update_subsub_menu( ){
	if( current_user_can( 'manage_options' ) ){
		$id = $_POST['id'];
		$submenuid = $_POST['submenuid'];
		wp_easycart_admin_shortcodes( )->print_subsubmenu_select( $id, $submenuid );
		die( );
	}
}