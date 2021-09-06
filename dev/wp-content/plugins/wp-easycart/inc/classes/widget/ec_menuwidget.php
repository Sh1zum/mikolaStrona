<?php 
class ec_menuwidget extends WP_Widget{
	
	function __construct( ){
		$widget_ops = array('classname' => 'ec_menuwidget', 'description' => 'Displays a Menu For Your WP EasyCart' );
		parent::__construct('ec_menuwidget', 'WP EasyCart Menu', $widget_ops);
	}
	
	function form( $instance ){
		if( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}else {
			$title = __( 'Store Menu', 'text_domain' );
		}
		
		if( isset( $instance[ 'menutype' ] ) ) {
			$menutype = $instance[ 'menutype' ];
		}else {
			$menutype = __( '1', 'text_domain' );
		}
		
		$defaults = array(
			'menutype' => '1',
			'title' => 'Store Menu'
		);
		$instance = wp_parse_args( (array) $instance, $defaults);
		$menutype = $instance['menutype'];
		$title = $instance['title'];
	
		echo "<p><label for=\"" . $this->get_field_name( 'title' ) . "\">" . _e( 'Title:' ) . "</label><input class=\"widefat\" id=\"" . $this->get_field_id( 'title' ) . "\" name=\"" . $this->get_field_name( 'title' ) . "\" type=\"text\" value=\"" . esc_attr( $title ) . "\" /></p>";
		
		echo "<p><label for=\"" . $this->get_field_id('menutype') . "\">Menu Type: 
		<select class=\"widefat\" id=\"" . $this->get_field_id('menutype') . "\" name=\"" . $this->get_field_name('menutype') . "\"><option value=\"1\"";
		if( $menutype == "1" )
		echo " selected=\"selected\"";
		echo ">Horizontal Menu</option><option value=\"2\"";
		if( $menutype == "2" )
		echo " selected=\"selected\"";
		echo ">Vertical Menu</option></select></label></p>";
		
	}
	
	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['menutype'] = $new_instance['menutype'];
		return $instance;
	}
	
	
	function widget($args, $instance){
	
		extract($args, EXTR_SKIP);
	
		echo $before_widget;
		$menutype = empty($instance['menutype']) ? ' ' : apply_filters('widget_menutype', $instance['menutype']);
		if( isset( $instance['title'] ) )
			$title = apply_filters( 'widget_title', $instance['title'] );
		else
			$title = "";
		
		// Translate if needed
		$title = $GLOBALS['language']->convert_text( $title );
	
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
			
		// WIDGET CODE GOES HERE
		$mysqli = new ec_db();
		$menu = $GLOBALS['ec_menu'];
		
		if( isset( $_GET['submenuid'] ) || isset( $_GET['subsubmenuid'] ) ){
			//Old Linking Format Code
			if( isset( $_GET['submenuid'] ) ){
				$level = "menu";
				$menu_id = $mysqli->get_menulevel1_id_from_menulevel2( $_GET['submenuid'] );
			}else if( isset( $_GET['subsubmenuid'] ) ){
				$level = "submenu";
				$menu_id = $mysqli->get_menulevel2_id_from_menulevel3( $_GET['subsubmenuid'] );
				$level2 = "menu";
				$menu_id2 = $mysqli->get_menulevel1_id_from_menulevel2( $menu_id );
			}else{
				$level = 0;
				$menu_id = 0;
			}
		}else{
			//New Linking Format Code
			global $wp_query;
			$post_obj = $wp_query->get_queried_object();
			if( isset( $post_obj ) && isset( $post_obj->ID ) ){
				$post_id = $post_obj->ID;
				$menulevel2 = $GLOBALS['ec_menu']->get_menu_row_from_post_id( $post_id, 2 );
				$menulevel3 = $GLOBALS['ec_menu']->get_menu_row_from_post_id( $post_id, 3 );
				
				if( $menulevel2 ){
					$level = "menu";
					$menu_id = $mysqli->get_menulevel1_id_from_menulevel2( $menulevel2->menulevel2_id );
				
				}else if( $menulevel3 ){
					$level = "submenu";
					$menu_id = $mysqli->get_menulevel2_id_from_menulevel3( $menulevel3->menulevel3_id );
					$level2 = "menu";
					$menu_id2 = $mysqli->get_menulevel1_id_from_menulevel2( $menu_id );
				
				}else{
					$level = 0;
					$menu_id = 0;
				}
			}else{
				$level = 0;
				$menu_id = 0;
			}
		}
		
		if($menutype == "1"){
			if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_menu_horizontal_widget.php' ) )	
				include( WP_PLUGIN_DIR . "/wp-easycart-data/design/layout/" . get_option( 'ec_option_base_layout' ) . "/ec_menu_horizontal_widget.php");
			else
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . "/design/layout/" . get_option( 'ec_option_latest_layout' ) . "/ec_menu_horizontal_widget.php");
		}else{
			if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_menu_vertical_widget.php' ) )	
				include( WP_PLUGIN_DIR . "/wp-easycart-data/design/layout/" . get_option( 'ec_option_base_layout' ) . "/ec_menu_vertical_widget.php");
			else
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . "/design/layout/" . get_option( 'ec_option_latest_layout' ) . "/ec_menu_vertical_widget.php");
		}
		
		echo $after_widget;
	}
 
}
?>