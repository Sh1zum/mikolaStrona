<?php 
class ec_colorwidget extends WP_Widget{
	
	function __construct( ){
		$widget_ops = array('classname' => 'ec_colorwidget', 'description' => 'Displays a Filter by Color Widget For Your WP EasyCart' );
		parent::__construct('ec_colorwidget', 'WP EasyCart Color Filter', $widget_ops);
	}
	
	function form( $instance ){ 
		
		if( isset( $instance[ 'option_id' ] ) ) {
			$option_id = $instance[ 'option_id' ];
		}else {
			$option_id = 0;
		}
		
		global $wpdb;
		$option_sets = $wpdb->get_results( "SELECT ec_option.option_id, ec_option.option_name FROM ec_option WHERE ec_option.option_type = 'basic-swatch' ORDER BY ec_option.option_name ASC" );
		
		echo "<p><label for=\"" . $this->get_field_name( 'option_id' ) . "\">" . _e( 'Option Set:' ) . "</label>";
		echo "<select class=\"widefat\" id=\"" . $this->get_field_id( 'option_id' ) . "\" name=\"" . $this->get_field_name( 'option_id' ) . "\">";
		echo "<option value=\"0\">Select Option Set</option>";
		foreach( $option_sets as $optionset ){
			echo "<option value=\"" . $optionset->option_id . "\"";
			if( $optionset->option_id == $option_id )
				echo " selected=\"selected\"";
			echo ">" . $optionset->option_name . "</option>";
		}
		echo "</select></p>";
		
		$defaults = array();
		$instance = wp_parse_args( (array) $instance, $defaults);
		
	}
	
	function update($new_instance, $old_instance){
		$instance = array();
		$instance['option_id'] = ( !empty( $new_instance['option_id'] ) ) ? strip_tags( $new_instance['option_id'] ) : '';

		return $instance;
	}
	
	
	function widget($args, $instance){
	
		extract( $args );
		if( isset( $instance['option_id'] ) )
			$option_id = apply_filters( 'widget_option_id', $instance['option_id'] );
		else
			$option_id = 0;
			
		if( $option_id != 0 ){
			
			global $wpdb;
			$option = $GLOBALS['ec_options']->get_option( $option_id );
			$optionitems = $GLOBALS['ec_options']->get_optionitems( $option_id );
			
			// WIDGET CODE GOES HERE
			$mysqli = new ec_db();
			$filter = new ec_filter(0);
			
			$storepageid = get_option('ec_option_storepage');
			if( function_exists( 'icl_object_id' ) ){
				$storepageid = icl_object_id( $storepageid, 'page', true, ICL_LANGUAGE_CODE );
			}
			$store_page = get_permalink( $storepageid );
			
			if( class_exists( "WordPressHTTPS" ) && isset( $_SERVER['HTTPS'] ) ){
				$https_class = new WordPressHTTPS( );
				$store_page = $https_class->makeUrlHttps( $store_page );
			}
			
			if( substr_count( $store_page, '?' ) )						$permalink_divider = "&";
			else														$permalink_divider = "?";
			
			if( isset( $_GET['menuid'] ) || isset( $_GET['submenuid'] ) || isset( $_GET['subsubmenuid'] ) ){
				//Old Linking Format Code
				if( isset( $_GET['menuid'] ) ){
					$level = 1;
					$menu_id = $_GET['menuid'];
				}else if( isset( $_GET['submenuid'] ) ){
					$level = 2;
					$menu_id = $_GET['submenuid'];
				}else if( isset( $_GET['subsubmenuid'] ) ){
					$level = 3;
					$menu_id = $_GET['subsubmenuid'];
				}else{
					$level = 0;
					$menu_id = 0;
				}
			}else if( isset( $GLOBALS['ec_store_shortcode_options'] ) ){
				
				// If content loads first, we can grab the shortcode option
				$menulevel1 = $GLOBALS['ec_store_shortcode_options'][0];
				$menulevel2 = $GLOBALS['ec_store_shortcode_options'][1];
				$menulevel3 = $GLOBALS['ec_store_shortcode_options'][2];
				
				if( $menulevel1 != "NOMENU" ){
					$level = 1;
					$menu_id = $menulevel1;
				}else if( $menulevel2 != "NOSUBMENU" ){
					$level = 2;
					$menu_id = $menulevel2;
				}else if( $menulevel3 != "NOSUBSUBMENU" ){
					$level = 3;
					$menu_id = $menulevel3;
				}else{
					$level = 0;
					$menu_id = 0;
				}
				
			}else{
				// Otherwise hope that someone didn't manually add shortcode to page and pull based on post id
				global $wp_query;
				$post_obj = $wp_query->get_queried_object();
				if( isset( $post_obj ) && isset( $post_obj->ID ) ){
					$post_id = $post_obj->ID;
					$menulevel1 = $GLOBALS['ec_menu']->get_menu_row_from_post_id( $post_id, 1 );
					$menulevel2 = $GLOBALS['ec_menu']->get_menu_row_from_post_id( $post_id, 2 );
					$menulevel3 = $GLOBALS['ec_menu']->get_menu_row_from_post_id( $post_id, 3 );
					
					if( count( $menulevel1 ) > 0 ){
						$level = 1;
						$menu_id = $menulevel1->menulevel1_id;
					}else if( count( $menulevel2 ) > 0 ){
						$level = 2;
						$menu_id = $menulevel2->menulevel2_id;
					}else if( count( $menulevel3 ) > 0 ){
						$level = 3;
						$menu_id = $menulevel3->menulevel3_id;
					}else{
						$level = 0;
						$menu_id = 0;
					}
				}else{
					$level = 0;
					$menu_id = 0;
				}
			}
			
			global $wp_query;
			$post_obj = $wp_query->get_queried_object();
			if( isset( $post_obj ) && isset( $post_obj->ID ) ){
				$post_id = $post_obj->ID;
				$manufacturer = $GLOBALS['ec_manufacturers']->get_manufacturer_id_from_post_id( $post_id );
				$group = $GLOBALS['ec_categories']->get_category_id_from_post_id( $post_id );
				
				if( isset( $_GET['manufacturer'] ) )
					$man_id = $_GET['manufacturer'];
				else if( isset( $GLOBALS['ec_store_shortcode_options'] ) && $GLOBALS['ec_store_shortcode_options'][3] != "NOMANUFACTURER" )
					$man_id = $GLOBALS['ec_store_shortcode_options'][3];
				else if( isset( $manufacturer ) )
					$man_id = $manufacturer->manufacturer_id;
				else
					$man_id = 0;
					
				if( isset( $_GET['group_id'] ) )
					$group_id = $_GET['group_id'];
				else if( isset( $GLOBALS['ec_store_shortcode_options'] ) && $GLOBALS['ec_store_shortcode_options'][4] != "NOGROUP" )
					$group_id = $GLOBALS['ec_store_shortcode_options'][4];
				else if( isset( $group ) )
					$group_id = $group->category_id;
				else
					$group_id = 0;
			}else{
				$man_id = 0;
				$group_id = 0;
			}
	
			if( $option ){
				echo $before_widget;
				echo $before_title . $option->option_name . $after_title;
				
				if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_color_widget.php' ) )	
					include( WP_PLUGIN_DIR . "/wp-easycart-data/design/layout/" . get_option( 'ec_option_base_layout' ) . "/ec_color_widget.php");
				else
					include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . "/design/layout/" . get_option( 'ec_option_latest_layout' ) . "/ec_color_widget.php");
				
				echo $after_widget;
			}
			
		}
	
	}
 
}
?>