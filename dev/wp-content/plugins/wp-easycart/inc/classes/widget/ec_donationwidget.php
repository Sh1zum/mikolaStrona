<?php 
class ec_donationwidget extends WP_Widget{
	
	function __construct( ){
		$widget_ops = array('classname' => 'ec_donationwidget', 'description' => 'Displays a donation goal thermometer for donation products in your WP EasyCart' );
		parent::__construct('ec_donationwidget', 'WP EasyCart Donation Goal Thermometer', $widget_ops);
	}
	
	function form( $instance ){ 
		if( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}else {
			$title = __( 'Donation Goal', 'text_domain' );
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
		global $post;
		if( isset( $post ) )
			$post_id = $post->ID;
		else
			$post_id = 0;
			
		if( isset( $_GET['model_number'] ) )
			$model_number = $_GET['model_number'];
		else
			$model_number = "";
		
		// Now with post_id try and find a matching product
		$db = new ec_db( );
		$products = $db->get_product_list( sprintf( " WHERE product.post_id = %d OR product.model_number = '%s'", $post_id, $model_number ), "", "", "" );
		
		if( count( $products ) > 0 && $products[0]["is_donation"]  ){
			extract( $args );
			if( isset( $instance['title'] ) )
				$title = apply_filters( 'widget_title', $instance['title'] );
			else
				$title = "";
			
			//Translate if Needed
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
			
			
			$donation_order_total = $db->get_donation_order_total( $products[0]['model_number'] );
		
			$raised_total = $GLOBALS['currency']->get_currency_display( $donation_order_total );
			$goal_total = $GLOBALS['currency']->get_currency_display( $products[0]['weight'] );
			if( $donation_order_total == 0 )
				$percent_used = 0;
			else
				$percent_used = $donation_order_total / $products[0]['weight'];
		
			if( $percent_used > 1 )
				$percent_used = 1;
			
			if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_donation_widget.php' ) )	
				include( WP_PLUGIN_DIR . "/wp-easycart-data/design/layout/" . get_option( 'ec_option_base_layout' ) . "/ec_donation_widget.php");
			
			else if( file_exists( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . "/design/layout/" . get_option( 'ec_option_latest_layout' ) . '/ec_donation_widget.php' ) )	
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . "/design/layout/" . get_option( 'ec_option_latest_layout' ) . "/ec_donation_widget.php");
			
			else
				echo "Could not find the donation widget layout file.";
			echo "<div style=\"clear:both\"></div>";
			echo $after_widget;
		}
	}
 
}
?>