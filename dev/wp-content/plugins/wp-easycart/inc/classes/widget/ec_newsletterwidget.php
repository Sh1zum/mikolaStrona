<?php 
class ec_newsletterwidget extends WP_Widget{
	
	function __construct( ){
		$widget_ops = array('classname' => 'ec_newsletterwidget', 'description' => 'Displays the Newsletter Sign Up For Your WP EasyCart' );
		parent::__construct('ec_newsletterwidget', 'WP EasyCart Newsletter Sign Up', $widget_ops);
	}
	
	function form($instance){
		if( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}else {
			$title = __( 'Newsletter Sign Up', 'text_domain' );
		}
		
		if( isset( $instance[ 'widget_label' ] ) ) {
			$widget_label = $instance[ 'widget_label' ];
		}else {
			$widget_label = __( 'Email', 'text_domain' );
		}
		
		if( isset( $instance[ 'widget_name_label' ] ) ) {
			$widget_name_label = $instance[ 'widget_name_label' ];
		}else {
			$widget_name_label = __( 'Name', 'text_domain' );
		}
		
		if( isset( $instance[ 'widget_submit' ] ) ) {
			$widget_submit = $instance[ 'widget_submit' ];
		}else {
			$widget_submit = __( 'Sign Up', 'text_domain' );
		}
		
		echo "<p><label for=\"" . $this->get_field_name( 'title' ) . "\">" . _e( 'Title:' ) . "</label><input class=\"widefat\" id=\"" . $this->get_field_id( 'title' ) . "\" name=\"" . $this->get_field_name( 'title' ) . "\" type=\"text\" value=\"" . esc_attr( $title ) . "\" /></p>";
		
		echo "<p><label for=\"" . $this->get_field_name( 'widget_name_label' ) . "\">" . _e( 'Name Label (Leave blank to leave out name):' ) . "</label><input class=\"widefat\" id=\"" . $this->get_field_id( 'widget_name_label' ) . "\" name=\"" . $this->get_field_name( 'widget_name_label' ) . "\" type=\"text\" value=\"" . esc_attr( $widget_name_label ) . "\" /></p>";
		
		echo "<p><label for=\"" . $this->get_field_name( 'widget_label' ) . "\">" . _e( 'Email Label:' ) . "</label><input class=\"widefat\" id=\"" . $this->get_field_id( 'widget_label' ) . "\" name=\"" . $this->get_field_name( 'widget_label' ) . "\" type=\"text\" value=\"" . esc_attr( $widget_label ) . "\" /></p>";
		
		echo "<p><label for=\"" . $this->get_field_name( 'widget_submit' ) . "\">" . _e( 'Button Label:' ) . "</label><input class=\"widefat\" id=\"" . $this->get_field_id( 'widget_submit' ) . "\" name=\"" . $this->get_field_name( 'widget_submit' ) . "\" type=\"text\" value=\"" . esc_attr( $widget_submit ) . "\" /></p>";
		
		$defaults = array();
		$instance = wp_parse_args( (array) $instance, $defaults);
	}
	
	function update($new_instance, $old_instance){
		$instance = array();
		$instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['widget_label'] = ( !empty( $new_instance['widget_label'] ) ) ? strip_tags( $new_instance['widget_label'] ) : '';
		$instance['widget_name_label'] = ( !empty( $new_instance['widget_name_label'] ) ) ? strip_tags( $new_instance['widget_name_label'] ) : '';
		$instance['widget_submit'] = ( !empty( $new_instance['widget_submit'] ) ) ? strip_tags( $new_instance['widget_submit'] ) : '';

		return $instance;
	}
	
	
	function widget($args, $instance){
	
		extract( $args );
		if( isset( $instance['title'] ) )
			$title = apply_filters( 'widget_title', $instance['title'] );
		else
			$title = "";
		
		if( isset( $instance['widget_label'] ) )
			$widget_label = apply_filters( 'widget_label', $instance['widget_label'] );
		else
			$widget_label = "";
		
		if( isset( $instance['widget_name_label'] ) )
			$widget_name_label = apply_filters( 'widget_name_label', $instance['widget_name_label'] );
		
		if( isset( $instance['widget_submit'] ) )
			$widget_submit = apply_filters( 'widget_submit', $instance['widget_submit'] );
		else
			$widget_submit = "";
		
		// Translate if Needed
		$title = $GLOBALS['language']->convert_text( $title );
		$widget_label = $GLOBALS['language']->convert_text( $widget_label );
		$widget_submit = $GLOBALS['language']->convert_text( $widget_submit );
	
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
		
		if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_newsletter_widget.php' ) )	
			include( WP_PLUGIN_DIR . "/wp-easycart-data/design/layout/" . get_option( 'ec_option_base_layout' ) . "/ec_newsletter_widget.php");
		else
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . "/design/layout/" . get_option( 'ec_option_latest_layout' ) . "/ec_newsletter_widget.php");
		
		echo $after_widget;
	}
 
}
?>