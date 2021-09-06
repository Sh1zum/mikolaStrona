<?php 
class ec_groupwidget extends WP_Widget{
	
	function __construct( ){
		$widget_ops = array('classname' => 'ec_groupwidget', 'description' => 'Displays the Categories For Your WP EasyCart' );
		parent::__construct('ec_groupwidget', 'WP EasyCart Category Filter', $widget_ops);
	}
	
	function form($instance){
		if( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}else {
			$title = __( 'Current Categories', 'text_domain' );
		}
		
		echo "<p><label for=\"" . $this->get_field_name( 'title' ) . "\">" . _e( 'Title:' ) . "</label><input class=\"widefat\" id=\"" . $this->get_field_id( 'title' ) . "\" name=\"" . $this->get_field_name( 'title' ) . "\" type=\"text\" value=\"" . esc_attr( $title ) . "\" /></p>";
		
		$defaults = array();
		$instance = wp_parse_args( (array) $instance, $defaults);
	}
	
	function update($new_instance, $old_instance){
		$instance = array();
		$instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}
	
	
	function widget($args, $instance){
	
		extract( $args );
		if( isset( $instance['title'] ) )
			$title = apply_filters( 'widget_title', $instance['title'] );
		else
			$title = "";
		
		// Translate if Needed
		$title = $GLOBALS['language']->convert_text( $title );
	
		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		
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
		
		$group_id = 0;
		if( isset( $_GET['group_id'] ) )
			$group_id = $_GET['group_id'];
		else if( isset( $GLOBALS['ec_store_shortcode_options'] ) && isset( $GLOBALS['ec_store_shortcode_options'][4] ) )
			$group_id = $GLOBALS['ec_store_shortcode_options'][4];
		
		$groups = $GLOBALS['ec_categories']->categories;
		for( $i=0; $i<count( $groups ); $i++ ){
			$groups[$i]->category_name = $GLOBALS['language']->convert_text( $groups[$i]->category_name );
		}
		
		if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_group_widget.php' ) )	
			include( WP_PLUGIN_DIR . "/wp-easycart-data/design/layout/" . get_option( 'ec_option_base_layout' ) . "/ec_group_widget.php");
		else
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . "/design/layout/" . get_option( 'ec_option_latest_layout' ) . "/ec_group_widget.php");
		
		echo $after_widget;
	}
 
}
?>