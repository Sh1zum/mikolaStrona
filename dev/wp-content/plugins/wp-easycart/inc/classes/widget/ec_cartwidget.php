<?php 
class ec_cartwidget extends WP_Widget{
	
	function __construct( ){
		$widget_ops = array('classname' => 'ec_cartwidget', 'description' => 'Displays a Minicart For Your WP EasyCart' );
		parent::__construct('ec_cartwidget', 'WP EasyCart Minicart', $widget_ops);
	}
	
	function form( $instance ){ 
		if( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}else {
			$title = __( 'Shopping Cart', 'text_domain' );
		}
		
		if( isset( $instance[ 'link_text' ] ) ) {
			$link_text = $instance[ 'link_text' ];
		}else {
			$link_text = __( 'Shopping Cart', 'text_domain' );
		}
		
		if( isset( $instance[ 'use_popup_minicart' ] ) ) {
			$use_popup_minicart = $instance[ 'use_popup_minicart' ];
		}else {
			$use_popup_minicart = __( '1', 'text_domain' );
		}
		
		if( isset( $instance[ 'open_on_click' ] ) ) {
			$open_on_click = $instance[ 'open_on_click' ];
		}else {
			$open_on_click = __( '0', 'text_domain' );
		}
		
		if( isset( $instance[ 'open_on_mouseover' ] ) ) {
			$open_on_mouseover = $instance[ 'open_on_mouseover' ];
		}else {
			$open_on_mouseover = __( '1', 'text_domain' );
		}
		
		echo "<p><label for=\"" . $this->get_field_name( 'title' ) . "\">" . _e( 'Title:' ) . "</label><input class=\"widefat\" id=\"" . $this->get_field_id( 'title' ) . "\" name=\"" . $this->get_field_name( 'title' ) . "\" type=\"text\" value=\"" . esc_attr( $title ) . "\" /></p>";
		
		echo "<p><label for=\"" . $this->get_field_name( 'link_text' ) . "\">" . _e( 'Link Text:' ) . "</label><input class=\"widefat\" id=\"" . $this->get_field_id( 'link_text' ) . "\" name=\"" . $this->get_field_name( 'link_text' ) . "\" type=\"text\" value=\"" . esc_attr( $link_text ) . "\" /></p>";
		
		echo "<p><label for=\"" . $this->get_field_name( 'use_popup_minicart' ) . "\">" . _e( 'Minicart Popup:' ) . "</label><select class=\"widefat\" id=\"" . $this->get_field_id( 'use_popup_minicart' ) . "\" name=\"" . $this->get_field_name( 'use_popup_minicart' ) . "\"><option value=\"1\"";
		if( $use_popup_minicart == 1 )
		echo " selected=\"selected\"";
		echo ">On</option><option value=\"0\"";
		if( $use_popup_minicart == 0 )
		echo "selected=\"selected\"";
		echo ">Off</option></select></p>";
		
		echo "<p><label for=\"" . $this->get_field_name( 'open_on_click' ) . "\">" . _e( 'Open on Click:' ) . "</label><select class=\"widefat\" id=\"" . $this->get_field_id( 'open_on_click' ) . "\" name=\"" . $this->get_field_name( 'open_on_click' ) . "\"><option value=\"1\"";
		if( $open_on_click == 1 )
		echo " selected=\"selected\"";
		echo ">Yes</option><option value=\"0\"";
		if( $open_on_click == 0 )
		echo "selected=\"selected\"";
		echo ">No</option></select></p>";
		
		echo "<p><label for=\"" . $this->get_field_name( 'open_on_mouseover' ) . "\">" . _e( 'Open on Hover:' ) . "</label><select class=\"widefat\" id=\"" . $this->get_field_id( 'open_on_mouseover' ) . "\" name=\"" . $this->get_field_name( 'open_on_mouseover' ) . "\"><option value=\"1\"";
		if( $open_on_mouseover == 1 )
		echo " selected=\"selected\"";
		echo ">Yes</option><option value=\"0\"";
		if( $open_on_mouseover == 0 )
		echo "selected=\"selected\"";
		echo ">No</option></select></p>";
		
		$defaults = array();
		$instance = wp_parse_args( (array) $instance, $defaults);
	}
	
	function update($new_instance, $old_instance){
		$instance = array();
		$instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['link_text'] = ( !empty( $new_instance['link_text'] ) ) ? strip_tags( $new_instance['link_text'] ) : '';
		$instance['use_popup_minicart'] = ( !empty( $new_instance['use_popup_minicart'] ) ) ? strip_tags( $new_instance['use_popup_minicart'] ) : '';
		$instance['open_on_click'] = ( !empty( $new_instance['open_on_click'] ) ) ? strip_tags( $new_instance['open_on_click'] ) : '';
		$instance['open_on_mouseover'] = ( !empty( $new_instance['open_on_mouseover'] ) ) ? strip_tags( $new_instance['open_on_mouseover'] ) : '';

		return $instance;
	}
	
	
	function widget($args, $instance){
	
		extract( $args );
		if( isset( $instance['title'] ) )
			$title = apply_filters( 'widget_title', $instance['title'] );
		else
			$title = "";
		if( isset( $instance['link_text'] ) )
			$link_text = apply_filters( 'widget_link_text', $instance['link_text'] );
		else
			$link_text = "";
		if( isset( $instance['use_popup_minicart'] ) )
			$use_popup_minicart = apply_filters( 'widget_use_popup_minicart', $instance['use_popup_minicart'] );
		else
			$use_popup_minicart = "";
		if( isset( $instance['open_on_click'] ) )
			$open_on_click = apply_filters( 'widget_open_on_click', $instance['open_on_click'] );
		else
			$open_on_click = "";
		if( isset( $instance['open_on_mouseover'] ) )
			$open_on_mouseover = apply_filters( 'widget_open_on_mouseover', $instance['open_on_mouseover'] );
		else
			$open_on_mouseover = "";
		
		// Translate if Needed
		$title = $GLOBALS['language']->convert_text( $title );
		$link_text = $GLOBALS['language']->convert_text( $link_text );
	
		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		
		// WIDGET CODE GOES HERE
		$mysqli = new ec_db();
		$filter = new ec_filter(0);
		$cart = new ec_cart( $GLOBALS['ec_cart_data']->ec_cart_id );
		$subtotal = $cart->subtotal;
		
		$cartpageid = get_option('ec_option_cartpage');
		if( function_exists( 'icl_object_id' ) ){
			$cartpageid = icl_object_id( $cartpageid, 'page', true, ICL_LANGUAGE_CODE );
		}
		$cart_page = get_permalink( $cartpageid );
		
		if( class_exists( "WordPressHTTPS" ) && isset( $_SERVER['HTTPS'] ) ){
			$https_class = new WordPressHTTPS( );
			$cart_page = $https_class->makeUrlHttps( $cart_page );
		}
		
		if( substr_count( $cart_page, '?' ) )						$permalink_divider = "&";
		else														$permalink_divider = "?";
		
		$cart = new ec_cart( $GLOBALS['ec_cart_data']->ec_cart_id );
		$page_id = get_the_ID();
		
		//if( $page_id != $cartpageid ){
			if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_cart_widget.php' ) )	
				include( WP_PLUGIN_DIR . "/wp-easycart-data/design/layout/" . get_option( 'ec_option_base_layout' ) . "/ec_cart_widget.php");
			else
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . "/design/layout/" . get_option( 'ec_option_latest_layout' ) . "/ec_cart_widget.php");
		//}
		
		echo $after_widget;
	}
 
}
?>