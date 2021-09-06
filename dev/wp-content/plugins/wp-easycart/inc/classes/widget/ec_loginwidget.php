<?php 
class ec_loginwidget extends WP_Widget{
	
	function __construct( ){
		$widget_ops = array('classname' => 'ec_loginwidget', 'description' => 'Displays the Login Widget For Your WP EasyCart' );
		parent::__construct('ec_loginwidget', 'WP EasyCart Login Widget', $widget_ops);
	}
	
	function form( $instance ){ 
		
		if( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}else {
			$title = __( 'Your Account', 'text_domain' );
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
	
		// Translate if needed
		$title = $GLOBALS['language']->convert_text( $title );
		
		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
	
		$account_page_id = get_option('ec_option_accountpage');
		
		if( function_exists( 'icl_object_id' ) ){
			$account_page_id = icl_object_id( $account_page_id, 'page', true, ICL_LANGUAGE_CODE );
		}
		
		$account_page = get_permalink( $account_page_id );
		
		if( class_exists( "WordPressHTTPS" ) && isset( $_SERVER['HTTPS'] ) ){
			$https_class = new WordPressHTTPS( );
			$account_page = $https_class->makeUrlHttps( $account_page );
		}
		
		if( substr_count( $account_page, '?' ) )					$permalink_divider = "&";
		else														$permalink_divider = "?";
		
		if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_login_widget.php' ) )	
			include( WP_PLUGIN_DIR . "/wp-easycart-data/design/layout/" . get_option( 'ec_option_base_layout' ) . "/ec_login_widget.php");
		else
			include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . "/design/layout/" . get_option( 'ec_option_latest_layout' ) . "/ec_login_widget.php");
		
		echo $after_widget;
	}
 
}
?>