<?php 
class ec_searchwidget extends WP_Widget{
	
	function __construct( ){
		$widget_ops = array('classname' => 'ec_searchwidget', 'description' => 'Displays the Product Search Filter For Your WP EasyCart' );
		parent::__construct('ec_searchwidget', 'WP EasyCart Product Search Filter', $widget_ops);
	}
	
	function form( $instance ){ 
		if( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}else {
			$title = __( 'Product Search', 'text_domain' );
		}
		
		if( isset( $instance[ 'label' ] ) ) {
			$label = $instance[ 'label' ];
		}else {
			$label = __( 'Search', 'text_domain' );
		}
		
		echo "<p><label for=\"" . $this->get_field_name( 'title' ) . "\">" . _e( 'Title:' ) . "</label><input class=\"widefat\" id=\"" . $this->get_field_id( 'title' ) . "\" name=\"" . $this->get_field_name( 'title' ) . "\" type=\"text\" value=\"" . esc_attr( $title ) . "\" /></p>";
		
		echo "<p><label for=\"" . $this->get_field_name( 'label' ) . "\">" . _e( 'Title:' ) . "</label><input class=\"widefat\" id=\"" . $this->get_field_id( 'label' ) . "\" name=\"" . $this->get_field_name( 'label' ) . "\" type=\"text\" value=\"" . esc_attr( $label ) . "\" /></p>";
		
		$defaults = array();
		$instance = wp_parse_args( (array) $instance, $defaults);
	}
	
	function update($new_instance, $old_instance){
		$instance = array();
		$instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['label'] = ( !empty( $new_instance['label'] ) ) ? strip_tags( $new_instance['label'] ) : '';

		return $instance;
	}
	
	
	function widget($args, $instance){
	
		extract( $args );
		if( isset( $instance['title'] ) )
			$title = apply_filters( 'widget_title', $instance['title'] );
		else
			$title = "";
		if( isset( $instance['label'] ) )
			$label = apply_filters( 'widget_label', $instance['label'] );
		else
			$label = "";
		
		// Translate if needed
		$title = $GLOBALS['language']->convert_text( $title );
		$label = $GLOBALS['language']->convert_text( $label );
	
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
		
		if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_search_widget.php' ) )	
			include( WP_PLUGIN_DIR . "/wp-easycart-data/design/layout/" . get_option( 'ec_option_base_layout' ) . "/ec_search_widget.php");
		else
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . "/design/layout/" . get_option( 'ec_option_latest_layout' ) . "/ec_search_widget.php");
		
		echo $after_widget;
	}
 
}
?>