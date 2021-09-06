<div class="ec_admin_list_line_item">
            
	<?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_product_list_display_loader" ); ?>
            
	<div class="ec_admin_settings_label">
		<div class="dashicons-before dashicons-screenoptions"></div>
		<span><?php _e( 'Product List Display', 'wp-easycart' ); ?></span>
		<a href="<?php echo wp_easycart_admin( )->helpsystem->print_docs_url('settings', 'product-settings', 'product-list');?>" target="_blank" class="ec_help_icon_link">
			<div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
		</a>
    	<?php echo wp_easycart_admin( )->helpsystem->print_vids_url('settings', 'product-settings', 'product-list');?>
	</div>
    
    <div class="ec_admin_settings_input ec_admin_settings_products_section">
       
		<?php wp_easycart_admin( )->load_toggle_group( 'ec_option_show_sort_box', 'wpeasycart_admin_update_sort_box_view( ); ec_admin_save_product_options', get_option( 'ec_option_show_sort_box' ), __( 'Product Sorting', 'wp-easycart' ), __( 'Enabling this allows your customers to sort by things like price, title, and rating.', 'wp-easycart' ) ); ?>
		
		<?php 
		$sort_options = array(
			(object) array(
				'value'	=> '0',
				'label'	=> __( 'Default Sorting (admin determined sort order)', 'wp-easycart' )
			),
			(object) array(
				'value'	=> '1',
				'label'	=> __( 'Price Low-High', 'wp-easycart' )
			),
			(object) array(
				'value'	=> '2',
				'label'	=> __( 'Price High-Low', 'wp-easycart' )
			),
			(object) array(
				'value'	=> '3',
				'label'	=> __( 'Title A-Z', 'wp-easycart' )
			),
			(object) array(
				'value'	=> '4',
				'label'	=> __( 'Title Z-A', 'wp-easycart' )
			),
			(object) array(
				'value'	=> '5',
				'label'	=> __( 'Newest First', 'wp-easycart' )
			),
			(object) array(
				'value'	=> '6',
				'label'	=> __( 'Best Rating First', 'wp-easycart' )
			),
			(object) array(
				'value'	=> '7',
				'label'	=> __( 'Most Viewed', 'wp-easycart' )
			)
		);
		?>
		
		<?php wp_easycart_admin( )->load_toggle_group_select( 'ec_option_default_store_filter', 'ec_admin_save_product_text_setting', get_option( 'ec_option_default_store_filter' ), __( 'Product Sorting: Default Selection', 'wp-easycart' ), __( 'This is the default selected option when a customer first visits your store.', 'wp-easycart' ), $sort_options, 'ec_admin_store_filter_default_row', ( ( get_option( 'ec_option_show_sort_box' ) == '1' ) ? true : false ), false ); ?>
		
		<?php wp_easycart_admin( )->load_toggle_group( 'ec_option_product_filter_0', 'ec_admin_save_product_options', get_option( 'ec_option_product_filter_0' ), __( 'Product Sorting: Default Sorting', 'wp-easycart' ), __( 'Enabling this to show default sorting in the sort box.', 'wp-easycart' ), 'ec_admin_store_filter_0_row', ( ( get_option( 'ec_option_show_sort_box' ) == '1' ) ? true : false ) ); ?>
		
		<?php wp_easycart_admin( )->load_toggle_group( 'ec_option_product_filter_1', 'ec_admin_save_product_options', get_option( 'ec_option_product_filter_1' ), __( 'Product Sorting: Price Low-High', 'wp-easycart' ), __( 'Enabling this to show pricing low-high in the sort box.', 'wp-easycart' ), 'ec_admin_store_filter_1_row', ( ( get_option( 'ec_option_show_sort_box' ) == '1' ) ? true : false ) ); ?>
		
		<?php wp_easycart_admin( )->load_toggle_group( 'ec_option_product_filter_2', 'ec_admin_save_product_options', get_option( 'ec_option_product_filter_2' ), __( 'Product Sorting: Price High-Low', 'wp-easycart' ), __( 'Enabling this to show pricing high-low in the sort box.', 'wp-easycart' ), 'ec_admin_store_filter_2_row', ( ( get_option( 'ec_option_show_sort_box' ) == '1' ) ? true : false ) ); ?>
		
		<?php wp_easycart_admin( )->load_toggle_group( 'ec_option_product_filter_3', 'ec_admin_save_product_options', get_option( 'ec_option_product_filter_3' ), __( 'Product Sorting: Title A-Z', 'wp-easycart' ), __( 'Enabling this to show title a-z in the sort box.', 'wp-easycart' ), 'ec_admin_store_filter_3_row', ( ( get_option( 'ec_option_show_sort_box' ) == '1' ) ? true : false ) ); ?>
		
		<?php wp_easycart_admin( )->load_toggle_group( 'ec_option_product_filter_4', 'ec_admin_save_product_options', get_option( 'ec_option_product_filter_4' ), __( 'Product Sorting: Title Z-A', 'wp-easycart' ), __( 'Enabling this to show title z-a in the sort box.', 'wp-easycart' ), 'ec_admin_store_filter_4_row', ( ( get_option( 'ec_option_show_sort_box' ) == '1' ) ? true : false ) ); ?>
		
		<?php wp_easycart_admin( )->load_toggle_group( 'ec_option_product_filter_5', 'ec_admin_save_product_options', get_option( 'ec_option_product_filter_5' ), __( 'Product Sorting: Newest', 'wp-easycart' ), __( 'Enabling this to show sort by newest in the sort box.', 'wp-easycart' ), 'ec_admin_store_filter_5_row', ( ( get_option( 'ec_option_show_sort_box' ) == '1' ) ? true : false ) ); ?>
		
		<?php wp_easycart_admin( )->load_toggle_group( 'ec_option_product_filter_6', 'ec_admin_save_product_options', get_option( 'ec_option_product_filter_6' ), __( 'Product Sorting: Best Rating', 'wp-easycart' ), __( 'Enabling this to show sort by rating in the sort box.', 'wp-easycart' ), 'ec_admin_store_filter_6_row', ( ( get_option( 'ec_option_show_sort_box' ) == '1' ) ? true : false ) ); ?>
		
		<?php wp_easycart_admin( )->load_toggle_group( 'ec_option_product_filter_7', 'ec_admin_save_product_options', get_option( 'ec_option_product_filter_7' ), __( 'Product Sorting: Most Viewed', 'wp-easycart' ), __( 'Enabling this to show sort by most viewed in the sort box.', 'wp-easycart' ), 'ec_admin_store_filter_7_row', ( ( get_option( 'ec_option_show_sort_box' ) == '1' ) ? true : false ) ); ?>
		
		<?php wp_easycart_admin( )->load_toggle_group( 'ec_option_short_description_on_product', 'ec_admin_save_product_options', get_option( 'ec_option_short_description_on_product' ), __( 'Product Grid Type: Display Short Description', 'wp-easycart' ), __( 'Enabling this will show the short description on the grid layout type.', 'wp-easycart' ) ); ?>
		
		<?php wp_easycart_admin( )->load_toggle_group( 'ec_option_show_featured_categories', 'ec_admin_save_product_options', get_option( 'ec_option_show_featured_categories' ), __( 'Product List: Show Featured Categories First', 'wp-easycart' ), __( 'Enabling this will show the featured categories first on the store landing page.', 'wp-easycart' ) ); ?>
		
		<?php wp_easycart_admin( )->load_toggle_group( 'ec_option_enable_product_paging', 'ec_admin_save_product_options', get_option( 'ec_option_enable_product_paging' ), __( 'Product List: Paging', 'wp-easycart' ), __( 'Enabling this will show paging on your product list pages, disabling shows all available products on the page.', 'wp-easycart' ) ); ?>
    
    </div>
    
    <div class="ec_admin_settings_input">
        <input type="submit" class="ec_admin_settings_simple_button" onclick="return ec_admin_save_product_list_display_options( );" value="<?php _e( 'Save Options', 'wp-easycart' ); ?>" />
    </div>
    
</div>