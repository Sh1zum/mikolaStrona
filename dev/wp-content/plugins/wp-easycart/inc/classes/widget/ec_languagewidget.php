<?php 
class ec_languagewidget extends WP_Widget{
	
	function __construct( ){
		$widget_ops = array('classname' => 'ec_languagewidget', 'description' => 'Displays a Language Convertor for WP EasyCart' );
		parent::__construct('ec_languagewidget', 'WP EasyCart Language Selector', $widget_ops);
	}
	
	function form( $instance ){ 
		if( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}else {
			$title = __( '[EN]Shop Language[/EN][FR]Boutique Langue[/FR][NL]Shop Taal[/NL]', 'text_domain' );
		}
		
		if( isset( $instance[ 'available_languages' ] ) ) {
			$available_languages = $instance[ 'available_languages' ];
		}else {
			$available_languages = __( 'EN:English,FR:Français,NL:Nederlands', 'text_domain' );
		}
		
		echo "<p><label for=\"" . $this->get_field_name( 'title' ) . "\">" . _e( 'Title:' ) . "</label><input class=\"widefat\" id=\"" . $this->get_field_id( 'title' ) . "\" name=\"" . $this->get_field_name( 'title' ) . "\" type=\"text\" value=\"" . esc_attr( $title ) . "\" /></p>";
		
		echo "<p><label for=\"" . $this->get_field_name( 'available_languages' ) . "\">" . _e( 'Available Languages (format: EN:English,FR:French):' ) . "</label><input class=\"widefat\" id=\"" . $this->get_field_id( 'available_languages' ) . "\" name=\"" . $this->get_field_name( 'available_languages' ) . "\" type=\"text\" value=\"" . esc_attr( $available_languages ) . "\" /></p>";
		
		$defaults = array();
		$instance = wp_parse_args( (array) $instance, $defaults);
	}
	
	function update($new_instance, $old_instance){
		$instance = array();
		$instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['available_languages'] = ( !empty( $new_instance['available_languages'] ) ) ? strip_tags( $new_instance['available_languages'] ) : '';

		return $instance;
	}
	
	
	function widget($args, $instance){
	
		// Get the Widget Vars
		extract( $args );
		if( isset( $instance['title'] ) )
			$title = apply_filters( 'widget_title', $instance['title'] );
		else
			$title = "";
		if( isset( $instance['available_languages'] ) )
			$available_languages = apply_filters( 'widget_available_languages', $instance['available_languages'] );
		else
			$available_languages = "";
			
		// Process the language string
		$language_arrs = explode( ",", $available_languages );
		$languages = array( );
		
		for( $i=0; $i<count( $language_arrs ); $i++ ){
			$language = explode( ":", $language_arrs[$i] );
			$languages[] = $language;
		}
		
		// Get the selected language
		if( count( $languages ) > 0 ){
			$selected_language = $GLOBALS['language']->language_code;
			
		}else{
			$languages[] = array( "EN", "English" );
			$selected_language = "EN";
		}
		
		// Get the correct title
		$title = $GLOBALS['language']->convert_text( $title );
		
		// Display the widget
		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		
		
		// WIDGET CODE GOES HERE
		echo "<form action=\"\" method=\"POST\" id=\"language\">";
		echo "<select name=\"ec_language_conversion\" onchange=\"document.getElementById('language').submit();\">";
		foreach( $languages as $language ){
			echo "<option value=\"" . $language[0] . "\"";
			if( $selected_language == $language[0] )
				echo " selected=\"selected\"";
			echo ">" . $language[1] . "</option>";
		}
		echo "</select>";
		echo "</form>";
		
		echo $after_widget;
	}
 
}
?>