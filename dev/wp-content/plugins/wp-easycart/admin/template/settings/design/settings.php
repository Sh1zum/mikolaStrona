<div class="ec_admin_list_line_item ec_admin_demo_data_line">
            
	<div class="ec_admin_settings_label">
        <div class="dashicons-before dashicons-admin-generic"></div>
        <span><?php _e( 'Design Settings', 'wp-easycart' ); ?></span>
        <a href="<?php echo wp_easycart_admin( )->helpsystem->print_docs_url('settings', 'design', 'settings');?>" target="_blank" class="ec_help_icon_link">
            <div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
        </a>
        <?php echo wp_easycart_admin( )->helpsystem->print_vids_url('settings', 'design', 'settings');?>
    </div>
    
    <div class="ec_admin_settings_input wp_easycart_admin_no_padding">
        
        <?php 
		$gfonts_str = file_get_contents( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/design/google-fonts.json' ); 
		$gfonts = json_decode( $gfonts_str );
        $font_options = array( 
            (object) array(
                'value'	=> '',
                'label'	=> __( 'Use Default', 'wp-easycart' )
            )
        );
        foreach( $gfonts->items as $font ){
            $font_options[] = (object) array(
                'value'	=> $font->family,
                'label'	=> $font->family
            );
        }
        $font_options[] = (object) array(
            'value'	=> 'custom',
            'label'	=> __( 'Add Custom Font', 'wp-easycart' )
        );
		?>
        <?php wp_easycart_admin( )->load_toggle_group_select( 'ec_option_font_main', 'ec_admin_save_design_text_setting', get_option( 'ec_option_font_main' ), __( 'Font Selection', 'wp-easycart' ), __( 'Choose a google font for all EasyCart elements.', 'wp-easycart' ), $font_options, 'ec_admin_ec_option_font_main_row', true, false ); ?>
        <div style="float:left; width:100%; margin:-25px 0 20px; text-align:right;">
            <a href="https://fonts.google.com" target="_blank"><?php _e( 'View Google Fonts', 'wp-easycart' ); ?></a>
        </div>
        
        <?php wp_easycart_admin( )->load_toggle_group_text( 'ec_option_font_custom', 'ec_admin_save_design_text_setting', get_option( 'ec_option_font_custom' ), __( 'Font Family', 'wp-easycart' ), __( 'This font must be loaded into your site to function.', 'wp-easycart' ), '', 'ec_admin_ec_option_font_custom_row', ( ( get_option( 'ec_option_font_main' ) == 'custom' ) ? true : false ), false ); ?>
        
        <?php wp_easycart_admin( )->load_toggle_group( 'ec_option_no_rounded_corners', 'ec_admin_save_design_options', !get_option( 'ec_option_no_rounded_corners' ), __( 'Enable Rounded Corners', 'wp-easycart' ), __( 'This will round most corners within the site, choose the best option to match your theme.', 'wp-easycart' ) ); ?>
        
        <?php wp_easycart_admin( )->load_toggle_group( 'ec_option_hide_live_editor', 'ec_admin_save_design_options', !get_option( 'ec_option_hide_live_editor' ), __( 'Enable Live Design Editor', 'wp-easycart' ), __( 'This enables the live design editor on the front-end of your site.', 'wp-easycart' ) ); ?>
        
        <?php wp_easycart_admin( )->load_toggle_group( 'ec_option_use_custom_post_theme_template', 'ec_admin_save_design_options', get_option( 'ec_option_use_custom_post_theme_template' ), __( 'Enable Custom Post Template', 'wp-easycart' ), __( 'This is an advanced option to be used with your theme files to customize the store product pages.', 'wp-easycart' ) ); ?>
        
        <?php wp_easycart_admin( )->load_toggle_group( 'ec_option_match_store_meta', 'ec_admin_save_design_options', get_option( 'ec_option_match_store_meta' ), __( 'Enable Match Store Meta', 'wp-easycart' ), __( 'This is an advanced option and tries to match the main store page meta to hopefully keep the design consistent from store page to product details page.', 'wp-easycart' ) ); ?>

    </div>
    
</div>