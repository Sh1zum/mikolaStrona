<?php
   		$validate = new ec_validation; 
		$license = new ec_license;
		$language = new ec_language( );
		$language->update_language_data( ); //Do this to update the database if a new language is added
		
		if( !get_option( 'ec_option_use_seperate_language_forms' ) )
			update_option( 'ec_option_use_seperate_language_forms', 1 );
			
		if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "language-editor" && isset( $_GET['ec_action'] ) && $_GET['ec_action'] == "update_language" && isset( $_POST['ec_option_language '] ) ){
			ec_update_language_file( $language );
		
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "language-editor" && isset( $_GET['ec_action'] ) && $_GET['ec_action'] == "update-selected-language" && isset( $_POST['ec_option_language'] ) ){
			update_option( 'ec_option_language', $_POST['ec_option_language'] );
		
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "language-editor" && isset( $_GET['ec_action'] ) && $_GET['ec_action'] == "delete-language" && isset( $_GET['ec_language'] ) ){
			$language->remove_language( $_GET['ec_language'] );
		
		}else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "language-editor" && isset( $_GET['ec_action'] ) && $_GET['ec_action'] == "add-new-language" && isset( $_POST['ec_option_add_language'] ) ){
			$language->add_new_language( $_POST['ec_option_add_language'] );
		}
		
		 $language_file_list = $language->get_language_file_list( );
         $languages = $language->get_languages_array( );
         $language_data = $language->get_language_data( );
   		 $file_name = get_option( 'ec_option_language' );
   
	foreach( $language_data->{$file_name}->options as $key_section => $language_section ){
	$section_label = $language_section->label;
?>

<div class="ec_admin_list_line_item_fullwidth_language ec_admin_demo_data_line">
  <form method="post" action="admin.php?page=wp-easycart-settings&subpage=language-editor&ec_action=update_language" name="wpeasycart_admin_form" id="wpeasycart_admin_form_lang3" novalidate="novalidate">
    <input type="hidden" name="file_name" value="<?php echo $file_name; ?>" />
    <input type="hidden" name="key_section" value="<?php echo $key_section; ?>" />
    <div class="ec_admin_settings_label" style="cursor:pointer;" onclick="ec_hide_show_language_section( '<?php echo $file_name . "_" . $key_section; ?>' ); return false;">
      <div class="dashicons-before dashicons-testimonial"></div><span><?php echo $section_label; ?></span>
      <a href="#" onclick="return false;" id="<?php echo $file_name . "_" . $key_section; ?>_expand" class="ec_language_expand_btn"></a> 
      <a href="#" onclick="return false;" id="<?php echo $file_name . "_" . $key_section; ?>_contract" class="ec_language_contract_btn"></a>
    </div>
    <div class="ec_language_section_holder" id="<?php echo $file_name . "_" . $key_section; ?>">
      <div class="ec_admin_settings_input ec_admin_settings_live_payment_section ec_admin_settings_language_section">
        <?php
		foreach( $language_section->options as $key => $language_item ){
			$title = $language_item->title;
			$value = $language_item->value;
		?>
            <div class="ec_language_row_holder">
                <span class="ec_language_row_label"><?php echo $title; ?>: 
                </span>
                <span class="ec_language_row_input">
                    <input name="ec_language_field[<?php echo $key; ?>]" type="text" value="<?php echo str_replace( '"', '&quot;', $value ); ?>" style="width:100%;" />
                </span>
            </div>
        <?php 
		}
		?>
      </div>
      <input type="hidden" value="<?php echo get_option( 'ec_option_language' ); ?>" name="ec_option_language" id="ec_option_language"  />
      <input type="hidden" value="1" name="isupdate" />
      <div class="ec_admin_language_input">
        <input type="submit" value="<?php _e( 'Save Changes', 'wp-easycart' ); ?>"  class="ec_admin_settings_simple_button" />
      </div>
	</div>
  </form>
</div>
<?php }?>
<script>
function ec_hide_show_language_section( section ){
    if( jQuery( '#' + section + "_expand" ).is( ':visible' ) ){
        ec_show_language_section( section );
    }else{
        ec_hide_language_section( section );
    }
}
function ec_show_language_section( section ){
	jQuery( '#' + section ).slideDown( "slow" );
	jQuery( '#' + section + "_expand" ).hide( );
	jQuery( '#' + section + "_contract" ).show( );
}
function ec_hide_language_section( section ){
	jQuery( '#' + section ).slideUp( "slow" );
	jQuery( '#' + section + "_expand" ).show( );
	jQuery( '#' + section + "_contract" ).hide( );
}
</script>