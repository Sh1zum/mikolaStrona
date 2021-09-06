<?php

class ec_language{
	
	private $selected_language;										// VARCHAR
	public $language_code;											// VARCHAR(2)
	
	public $language_data;											// OBJECT
	public $languages;												// ARRAY of VARCHAR
	
	function __construct( $selected_language = 'NONE' ){
		
		if( $selected_language == 'NONE' ){
			$this->selected_language = strtolower( get_option( 'ec_option_language' ) );
			
			if( isset( $_GET['lang'] ) ){
				$this->language_code = strtoupper( htmlspecialchars( $_GET['lang'], ENT_QUOTES ) );
				$GLOBALS['ec_cart_data']->cart_data->translate_to = $this->language_code;
			
			}else if( isset( $_COOKIE['ec_translate_to'] ) ){
				$this->language_code = strtoupper( htmlspecialchars( $_COOKIE['ec_translate_to'], ENT_QUOTES ) );
				$GLOBALS['ec_cart_data']->cart_data->translate_to = $this->language_code;
			
			}else{
				$this->language_code = "NEEDTOSET";
			}
		}else{
			$this->selected_language = strtolower( $selected_language );
			$this->language_code = strtoupper( $selected_language );
		}
		
		// Decode the language packs
		$this->language_data = $this->get_decoded_language_data( );
		if( !$this->language_data ){ // Language data is new, lets set it up from the text files
			$this->language_data = $this->get_new_language_data( );
			$this->save_language_data( );
		}
		$this->languages = $this->get_languages( );
		
		// If no language has been defined, figure out the code from the txt files
		if( $this->language_code == "NEEDTOSET" ){
			if( isset( $this->language_data ) && isset( $this->language_data->{$this->selected_language} ) && isset( $this->language_data->{$this->selected_language}->options ) && isset( $this->language_data->{$this->selected_language}->options->language_code->options->code ) && isset( $this->language_data->{$this->selected_language}->options->language_code->options->code->value ) ){
				$this->language_code = strtoupper( $this->language_data->{$this->selected_language}->options->language_code->options->code->value );
			}else{
				$this->language_code = "NONE";
			}
		}else{ // Session or WPML defined, update the selected language to match
			$this->set_selected_language( );
		}
	}
	
	public function set_language( $code ){
		$this->language_code = strtoupper( htmlspecialchars( $code, ENT_QUOTES ) );
		$GLOBALS['ec_cart_data']->cart_data->translate_to = $this->language_code;
		$this->set_selected_language( );
	}
	
	public function add_new_language( $file_name ){
		$this->language_data->{$file_name} = $this->get_language_file_decoded( $file_name . ".txt" );
		$this->save_language_data( );
		$this->languages = $this->get_languages( );
	}
	
	public function update_language_data( ){
		
		if( isset( $_POST['isupdate'] ) ){

			$language_file = $_POST['file_name'];
			$language_section = $_POST['key_section'];
			
			foreach( $_POST['ec_language_field'] as $key => $value ){
				
				$this->language_data->{$language_file}->options->{$language_section}->options->{$key}->value = htmlspecialchars( stripslashes( $value ), ENT_NOQUOTES, "UTF-8" );
				
			}
			
		}
		$file_names = $this->get_language_file_list( );
		foreach( $file_names as $file_name ){
			if( in_array( $file_name, $this->languages ) )
				$this->update_language_entry( $file_name );
		}
		$this->save_language_data( );
		$this->languages = $this->get_languages( );
	}
	
	public function update_language_item( $language_file, $language_section, $key, $value ){
		$this->language_data->{$language_file}->options->{$language_section}->options->{$key}->value = htmlspecialchars( stripslashes( $value ), ENT_NOQUOTES, "UTF-8" );
		$this->save_language_data( );
		$this->languages = $this->get_languages( );
	}
	
	public function remove_language( $file_name ){
		$language_data = (object) array();
		foreach( $this->language_data as $key => $data ){
			if( $key != $file_name ){
				$language_data->{$key} = $data;
			}
		}
		$this->language_data = $language_data;
		$this->save_language_data( );
		$this->languages = $this->get_languages( );
	}
	
	public function get_languages_array( ){
		return $this->languages;
	}
	
	public function get_language_data( ){
		return $this->language_data;
	}
	
	/************************************************************
	/* INITIALIZATION FUNCTIONS
	/************************************************************/
	
	// Set selected language if the session is set or WPML language is defined
	private function set_selected_language( ){
		if( isset( $this->language_data ) ){
			$languages = $this->get_languages( );
			for( $i=0; $i<count( $languages ); $i++ ){
				$item_language_code = $this->language_data->{$languages[$i]}->options->language_code->options->code->value;
				if( strtoupper( $this->language_code ) == strtoupper( $item_language_code ) ){
					$this->selected_language = strtolower( $languages[$i] );
					break;
				}
			}
		}
	}
	
	// Get an array of languages from the language_data
	// Returns: array of text
	private function get_languages( ){
		$language_arrays = get_object_vars( $this->language_data );
		return array_keys( $language_arrays );
	}
	
	// Gets a complete object for the language data. Used on site initialization only.
	// Returns: Language data Object
	private function get_new_language_data( ){
		$language_data = (object) array();
		$file_names = $this->get_language_file_list( );
		
		for( $i=0; $i<count( $file_names ); $i++ ){
			if( $file_names[$i] == "en-us" ){
				$language_data->{$file_names[$i]} = $this->get_language_file_decoded( $file_names[$i] . ".txt" );
			}
		}
		
		return $language_data;
		
	}
	
	// Gets a list of language text files
	// Returns: Array of file names
	public function get_language_file_list( ){
		$file_names = array();
		$dir = WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . "/inc/language/";
		$handle = opendir( $dir );
		while( false !== ( $file = readdir( $handle ) ) ){
			$extension = pathinfo( $file, PATHINFO_EXTENSION );
			$name = pathinfo( $file, PATHINFO_FILENAME );
			if( $extension == "txt" )
			$file_names[] = $name;
		}
		return $file_names;
	}
	
	// Gets the json decoded file data for a language file
	// Returns: Language Object
	private function get_language_file_decoded( $file_name ){
		return json_decode( $this->get_language_file_contents( $file_name ) );
	}
	
	// Gets the contents of a language file as a string
	// Returns: Language File String
	private function get_language_file_contents( $file_name ){
		$contents = file_get_contents( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . "/inc/language/" . $file_name, true );	
		//$this->language_file_checker( $contents );
		return $contents;
	}
	
	/************************************************************
	/* FRONT END FUNCTIONS
	/************************************************************/
	
	// Gets the text for a language variable
	// Returns: Matching Text
	public function get_text( $lang_section, $lang_var ){
		if( isset( $this->language_data->{$this->selected_language} ) && 
			isset( $this->language_data->{$this->selected_language}->options->{$lang_section} ) && 
			isset( $this->language_data->{$this->selected_language}->options->{$lang_section}->options->{$lang_var} ) )
				
			return str_replace( "[terms]", "<a href=\"" . stripslashes( get_option( 'ec_option_terms_link' ) ) . "\" target=\"_blank\">", str_replace( "[/terms]", "</a>", str_replace( "[privacy]", "<a href=\"" . stripslashes( get_option( 'ec_option_privacy_link' ) ) . "\" target=\"_blank\">", str_replace( "[/privacy]", "</a>", $this->language_data->{$this->selected_language}->options->{$lang_section}->options->{$lang_var}->value ) ) ) );
	}
	
	public function convert_text( $text ){
		if( $this->language_code != "NONE" && preg_match_all( '/[\[][a-zA-Z][a-zA-Z][\]]|[\[][\/][a-zA-Z][a-zA-Z][\]]/', $text, $matches ) > 0 ){
			$text_arr = preg_split( '/[\[][\/]|[\[]|[\]]/', $text );
			$texts = array( );
			
			for( $i=1; $i<count( $text_arr ); $i++ ){
				$key = strtoupper( $text_arr[$i] );
				$val = $text_arr[($i+1)];
				$texts[$key] = $val;
				$i = $i + 3;
			}
			
			if( isset( $texts[ strtoupper( $this->language_code ) ] ) ){
				return $texts[ strtoupper( $this->language_code ) ];
			}
		}
			
		return $text;
	
	}
	
	/************************************************************
	/* ADMIN FUNCTIONS
	/************************************************************/
	
	public function export_language( $language_key ){
		
		$download_content = json_encode( $this->language_data->{$language_key} );
		
		header( "Cache-Control: public, must-revalidate" );
		header( "Pragma: no-cache" );
		header( "Content-Type: text/plain" );
		header( "Content-Length: " . strlen( $download_content ) );
		header( 'Content-Disposition: attachment; filename="' . $language_key . '.txt"' );
		header( "Content-Transfer-Encoding: binary\n" );
		echo json_encode( $this->language_data->{$language_key} );
	
	}
	
	private function update_language_entry( $file_name ){
		$new_language_object = $this->get_language_file_decoded( $file_name . ".txt" );	
		$new_array = get_object_vars( $new_language_object->options );
		$new_keys = array_keys( $new_array );
		
		$current_language_object = $this->language_data->{$file_name};
		$current_array = get_object_vars( $current_language_object->options );
		$current_keys = array_keys( $current_array );
		
		foreach( $new_keys as $new_key ){
			if( !in_array( $new_key, $current_keys ) ){
				$this->language_data->{$file_name}->options->{$new_key} = $new_language_object->options->{$new_key};
			}else{
				$new_sub_array = get_object_vars( $new_language_object->options->{$new_key}->options );
				$new_sub_keys = array_keys( $new_sub_array );
				
				$current_sub_array = get_object_vars( $current_language_object->options->{$new_key}->options );
				$current_sub_keys = array_keys( $current_sub_array );
				
				foreach( $new_sub_keys as $new_sub_key ){
					if( !in_array( $new_sub_key, $current_sub_keys ) ){
						$this->language_data->{$file_name}->options->{$new_key}->options->{$new_sub_key} = $new_language_object->options->{$new_key}->options->{$new_sub_key};
					}
				}
			}
				
		}
		
	}
	
	// Adds a new language file to the language data heap.
	// Returns: NULL
	private function add_new_language_file( $file_name ){
		if( !isset( $this->language_data->{$file_name} ) )
			$this->language_data->{$file_name} = $this->get_language_file_decoded( $file_name . ".txt" );	
	}
	
	// Updates the language_data wordpress option
	// Returns: NULL
	public function save_language_data( ){
		foreach( $this->language_data as $language_file => $files ){
			foreach( $files as $language_section => $sections ){
				if( is_array( $sections ) || is_object( $sections ) ){
					foreach( $sections as $key => $value ){
						if( isset( $this->language_data->{$language_file} ) && isset( $this->language_data->{$language_file}->options->{$language_section} ) && isset( $this->language_data->{$language_file}->options->{$language_section}->options->{$key} ) ){
							$this->language_data->{$language_file}->options->{$language_section}->options->{$key}->value = str_replace( '"', '\"', $value->value );
						}
					}
				}
			}
		}
		update_option( 'ec_option_language_data', $this->get_encoded_language_data( ) );
	}
	
	// encodes the language data
	// Returns: json encoded language data
	private function get_encoded_language_data( ){
		return json_encode( $this->language_data );
	}
	
	// decodes the language data
	// Returns: json decoded language data in object format
	private function get_decoded_language_data( ){
		return json_decode( html_entity_decode( get_option( 'ec_option_language_data' ) ) );
	}
	
	//////////////////////////
	// TESTING FUNCTIONS
	//////////////////////////
	private function language_file_checker( $file_contents ){
		$this->check_bracket_count( $file_contents );
		$this->check_for_quotes( $file_contents );
	}
	
	private function check_bracket_count( $file_contents ){
		$open_count = substr_count( trim($file_contents), "{" );
		$close_count = substr_count( trim($file_contents), "}" );
		
		if( $open_count > $close_count ){
			throw new Exception( "Too many open brackets in language file." );
		}else if( $close_count > $open_count ){
			throw new Exception( "Too many closed brackets in language file." );
		}
	}
	
	private function check_for_quotes( $file_contents ){
		$open_bracket_found = false;
		$open_paren_found = false;
		$closed_paren_found = false;
		for( $i=0; $i<strlen($file_contents); $i++){
			$char = substr( $file_contents, $i, 1 );
			
			if( $open_bracket_found && $char != '"' ){
				throw new Exception( "Needed a paren after open bracket at character " . $i . "." );
			}else{
				$open_bracket_found = false;
			}
			
			if( $open_paren_found && $char == '"' ){
				$closed_paren_found = true;
				$open_paren_found = false;
			}
			
			if( $closed_paren_found && $char != ':' && $char != ',' && $char != "}" ){
				throw new Exception( "Expected a : or , or } after a closed paren at character " . $i . "." );
			}else{
				$closed_paren_found = false;	
			}
		}
	}
	
}

?>