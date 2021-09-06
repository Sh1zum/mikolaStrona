<?php wp_easycart_admin( )->load_new_slideout( 'product' ); ?>
<?php wp_easycart_admin( )->load_new_slideout( 'manufacturer' ); ?>
<?php wp_easycart_admin( )->load_new_slideout( 'optionset' ); ?>
<?php wp_easycart_admin( )->load_new_slideout( 'advanced-optionset' ); ?>

<div class="ec_admin_message_error" id="ec_admin_product_activate_error"<?php if( !$this->id || $this->product->activate_in_store ){ ?> style="display:none;"<?php }?>><?php _e( 'Your product is NOT ACTIVE and is currently not showing on your online store', 'wp-easycart' ); ?> <a href="#" style="float:right; margin:0 15px 0;" onclick="jQuery( document.getElementById( 'activate_in_store' ) ).prop( 'checked', true ); ec_admin_save_product_details_basic( ); jQuery( this ).parent( ).fadeOut( ); return false;"><?php _e( 'Activate', 'wp-easycart' ); ?></a></div>
<div class="ec_admin_message_error" id="ec_admin_product_store_startup_error"<?php if( !$this->id || $this->product->show_on_startup ){ ?> style="display:none;"<?php }?>><?php _e( 'Your product is NOT showing on your main store page.', 'wp-easycart' ); ?> <a href="#" style="float:right; margin:0 15px 0;" onclick="jQuery( document.getElementById( 'show_on_startup' ) ).prop( 'checked', true ); ec_admin_save_product_details_general_options( ); jQuery( this ).parent( ).fadeOut( ); return false;"><?php _e( 'Add to Store', 'wp-easycart' ); ?></a></div>

<input type="hidden" name="ec_admin_form_action" value="<?php echo $this->form_action; ?>" />
<input type="hidden" name="product_id" id="product_id"value="<?php echo $this->product->product_id; ?>" />
  
  <div class="ec_admin_settings_panel ec_admin_details_panel">
    <div class="ec_admin_important_numbered_list">
      
      <div class="ec_admin_flex_row">
        <div class="ec_admin_list_line_item ec_admin_col_12 ec_admin_col_first">
          <?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_product_details_basic_loader" ); ?>
          <div class="ec_admin_settings_label ec_admin_expand_section_header">
            <div class="dashicons-before dashicons-tag"></div>
            <span id="product_title"><?php if( !$this->id ){ ?><?php _e( 'CREATE NEW PRODUCT', 'wp-easycart' ); ?><?php }else{ ?><?php _e( 'EDIT PRODUCT', 'wp-easycart' ); ?><?php }?></span>
            <div class="ec_page_product_title_button_wrap ec_page_title_button_wrap">
            	<a href="<?php echo $this->docs_link; ?>" target="_blank" class="ec_help_icon_link">
                    <div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
                </a>
                <?php echo wp_easycart_admin( )->helpsystem->print_vids_url('products', 'products', 'details');?>
                <a href="admin.php?page=wp-easycart-products&subpage=products&ec_admin_form_action=add-new" class="ec_page_title_button<?php if( !$this->id ){ ?> ec_admin_hidden<?php }?>" id="ec_admin_product_details_add_new_button" onclick="wp_easycart_admin_open_slideout( 'new_product_box' ); return false;"><?php _e( 'Add New Product', 'wp-easycart' ); ?></a>
                <a href="<?php echo wp_easycart_admin_products( )->get_product_link( $this->product->product_id ); ?>" target="_blank" class="ec_page_title_button<?php if( !$this->id ){ ?> ec_admin_hidden<?php }?>" id="ec_admin_product_details_view_product_link"><?php _e( 'View Product', 'wp-easycart' ); ?></a>
                <a href="<?php echo $this->action; ?>" class="ec_page_title_button"><?php _e( 'Back to Products', 'wp-easycart' ); ?></a>
            </div>
          </div>
          <div class="ec_admin_settings_input ec_admin_settings_currency_section">
            <?php do_action( 'wp_easycart_admin_product_details_basic_fields' ); ?>
            <div class="ec_admin_products_submit">
                <input type="submit" class="ec_admin_products_simple_button" id="product_create_button" onclick="return ec_admin_save_product_details_basic( );" value="<?php if( !$this->id ){ ?><?php _e( 'Create New Product', 'wp-easycart' ); ?><?php }else{ ?><?php _e( 'Update Product', 'wp-easycart' ); ?><?php }?>" />
            </div>
          </div>
        </div>
      </div>
      <?php do_action( 'wp_easycart_admin_product_details_sections_pre' ); ?>
      
      <div class="ec_admin_flex_row<?php if( !$this->id ){ ?> ec_admin_hidden<?php }?>">
        <div class="ec_admin_list_line_item ec_admin_col_12 ec_admin_col_first ec_admin_collapsable">
          <?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_product_details_images_loader" ); ?>
          <div class="ec_admin_settings_label ec_admin_expand_section_header">
            <div class="dashicons-before dashicons-format-gallery"></div>
            <span><?php _e( 'PRODUCT IMAGES', 'wp-easycart' ); ?></span>
            <a href="#images" class="ec_admin_expand_section" data-section="ec_admin_product_details_images_section"><div class="dashicons-before dashicons-arrow-down-alt2"></div></a>
            <div class="ec_page_product_title_button_wrap ec_page_title_button_wrap">
            	<a href="<?php echo $this->docs_link; ?>" target="_blank" class="ec_help_icon_link">
              		<div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
              	</a>
            </div>
          </div>
          <div class="ec_admin_settings_input ec_admin_collapsed_section" id="ec_admin_product_details_images_section">
            <?php do_action( 'wp_easycart_admin_product_details_images_fields' ); ?>
            <div class="ec_admin_products_submit">
                <input type="submit" class="ec_admin_products_simple_button" onclick="return ec_admin_save_product_details_images( );" value="<?php _e( 'Update Images', 'wp-easycart' ); ?>" />
            </div>
          </div>
        </div>
      </div>
      
      <div class="ec_admin_flex_row<?php if( !$this->id ){ ?> ec_admin_hidden<?php }?>">
        <div class="ec_admin_address_line_item ec_admin_list_line_item ec_admin_col_12 ec_admin_col_first ec_admin_collapsable">
          <?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_product_details_quantities_loader" ); ?>
          <div class="ec_admin_settings_label ec_admin_expand_section_header">
            <div class="dashicons-before dashicons-chart-area"></div>
            <span><?php _e( 'QUANTITY OPTIONS', 'wp-easycart' ); ?></span>
            <a href="#quantities" class="ec_admin_expand_section" data-section="ec_admin_product_details_quantities_section"><div class="dashicons-before dashicons-arrow-down-alt2"></div></a>
            <div class="ec_page_product_title_button_wrap ec_page_title_button_wrap">
            	<a href="<?php echo $this->docs_link; ?>" target="_blank" class="ec_help_icon_link">
              		<div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
              	</a>
            </div>
          </div>
          <div class="ec_admin_settings_input ec_admin_collapsed_section" id="ec_admin_product_details_quantities_section">
            <?php do_action( 'wp_easycart_admin_product_details_quantity_fields' ); ?>
            <div class="ec_admin_products_submit">
                <input type="submit" class="ec_admin_products_simple_button" onclick="return ec_admin_save_product_details_quantities( );" value="<?php _e( 'Update Quantities', 'wp-easycart' ); ?>" />
            </div>
            <?php do_action( 'wp_easycart_admin_product_details_optionitem_quantity_fields' ); ?>
          </div>
        </div>
      </div>
      
      <div class="ec_admin_flex_row<?php if( !$this->id ){ ?> ec_admin_hidden<?php }?>">
        <div class="ec_admin_address_line_item ec_admin_list_line_item ec_admin_col_12 ec_admin_col_first ec_admin_collapsable">
          <?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_product_details_pricing_loader" ); ?>
          <div class="ec_admin_settings_label ec_admin_expand_section_header">
            <div class="dashicons-before dashicons-chart-pie"></div>
            <span><?php _e( 'PRICING OPTIONS', 'wp-easycart' ); ?></span>
            <a href="#pricing" class="ec_admin_expand_section" data-section="ec_admin_product_details_pricing_section"><div class="dashicons-before dashicons-arrow-down-alt2"></div></a>
            <div class="ec_page_product_title_button_wrap ec_page_title_button_wrap">
            	<a href="<?php echo $this->docs_link; ?>" target="_blank" class="ec_help_icon_link">
              		<div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
              	</a>
            </div>
          </div>
          <div class="ec_admin_settings_input ec_admin_collapsed_section" id="ec_admin_product_details_pricing_section">
            <?php do_action( 'wp_easycart_admin_product_details_pricing_fields' ); ?>
            <div class="ec_admin_products_submit">
                <input type="submit" class="ec_admin_products_simple_button" onclick="return ec_admin_save_product_details_pricing( );" value="<?php _e( 'Update Pricing', 'wp-easycart' ); ?>" />
            </div>
            <?php do_action( 'wp_easycart_admin_product_details_advanced_pricing_fields' ); ?>
          </div>
        </div>
      </div>
      
      <div class="ec_admin_flex_row<?php if( !$this->id ){ ?> ec_admin_hidden<?php }?>">
        <div class="ec_admin_list_line_item ec_admin_col_12 ec_admin_col_first ec_admin_collapsable">
          <?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_product_details_options_loader" ); ?>
          <div class="ec_admin_settings_label ec_admin_expand_section_header">
            <div class="dashicons-before dashicons-admin-settings"></div>
            <span><?php _e( 'OPTION SETS', 'wp-easycart' ); ?></span>
            <a href="#options" class="ec_admin_expand_section" data-section="ec_admin_product_details_options_section"><div class="dashicons-before dashicons-arrow-down-alt2"></div></a>
            <div class="ec_page_product_title_button_wrap ec_page_title_button_wrap">
            	<a href="<?php echo $this->docs_link; ?>" target="_blank" class="ec_help_icon_link">
              		<div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
              	</a>
            </div>
          </div>
          <div class="ec_admin_settings_input ec_admin_collapsed_section" id="ec_admin_product_details_options_section">
            
            <div class="ec_admin_option_add_new_row"><input type="button" value="<?php _e( 'QUICK OPTION CREATOR', 'wp-easycart' ); ?>" onclick="ec_admin_open_new_option( );" /></div>
            <div class="ec_admin_option_add_new_row"><a href="admin.php?page=wp-easycart-products&subpage=option" target="_blank"><?php _e( 'FULL OPTION MANAGER', 'wp-easycart' ); ?></a></div>
            
            <?php do_action( 'wp_easycart_admin_product_details_options_fields' ); ?>
            <div class="ec_admin_products_submit">
                <input type="submit" class="ec_admin_products_simple_button" onclick="return ec_admin_save_product_details_options( );" value="<?php _e( 'Update Options', 'wp-easycart' ); ?>" />
            </div>
          </div>
        </div>
      </div>
      
      <div class="ec_admin_flex_row<?php if( !$this->id ){ ?> ec_admin_hidden<?php }?>">
        <div class="ec_admin_list_line_item ec_admin_col_12 ec_admin_col_first ec_admin_collapsable">
          <?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_product_details_general_options_loader" ); ?>
          <div class="ec_admin_settings_label ec_admin_expand_section_header">
            <div class="dashicons-before dashicons-admin-tools"></div>
            <span><?php _e( 'BASIC SETTINGS', 'wp-easycart' ); ?></span>
            <a href="#general-options" class="ec_admin_expand_section" data-section="ec_admin_product_details_general_options_section"><div class="dashicons-before dashicons-arrow-down-alt2"></div></a>
            <div class="ec_page_product_title_button_wrap ec_page_title_button_wrap">
            	<a href="<?php echo $this->docs_link; ?>" target="_blank" class="ec_help_icon_link">
              		<div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
              	</a>
            </div>
          </div>
          <div class="ec_admin_settings_input ec_admin_collapsed_section" id="ec_admin_product_details_general_options_section" style="padding-top:10px;">
            <?php do_action( 'wp_easycart_admin_product_details_general_options_fields' ); ?>
            <div class="ec_admin_products_submit">
                <input type="submit" class="ec_admin_products_simple_button" onclick="return ec_admin_save_product_details_general_options( );" value="<?php _e( 'Update General Options', 'wp-easycart' ); ?>" />
            </div>
          </div>
        </div>
      </div>
      
      <div class="ec_admin_flex_row<?php if( !$this->id ){ ?> ec_admin_hidden<?php }?>">
        <div class="ec_admin_address_line_item ec_admin_list_line_item ec_admin_col_12 ec_admin_col_first ec_admin_collapsable">
          <?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_product_details_featured_products_loader" ); ?>
          <div class="ec_admin_settings_label ec_admin_expand_section_header">
            <div class="dashicons-before dashicons-exerpt-view"></div>
            <span><?php _e( 'FEATURED PRODUCTS', 'wp-easycart' ); ?></span>
            <a href="#featured-products" class="ec_admin_expand_section" data-section="ec_admin_product_details_featured_products_section"><div class="dashicons-before dashicons-arrow-down-alt2"></div></a>
            <div class="ec_page_product_title_button_wrap ec_page_title_button_wrap">
            	<a href="<?php echo $this->docs_link; ?>" target="_blank" class="ec_help_icon_link">
              		<div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
              	</a>
            </div>
          </div>
          <div class="ec_admin_settings_input ec_admin_collapsed_section" id="ec_admin_product_details_featured_products_section">
            <?php do_action( 'wp_easycart_admin_product_details_featured_products_fields' ); ?>
            <div class="ec_admin_products_submit">
                <input type="submit" class="ec_admin_products_simple_button" onclick="return ec_admin_save_product_details_featured_products( );" value="<?php _e( 'Update Featured Products', 'wp-easycart' ); ?>" />
            </div>
          </div>
        </div>
      </div>
      
      <div class="ec_admin_flex_row<?php if( !$this->id ){ ?> ec_admin_hidden<?php }?>">
        <div class="ec_admin_list_line_item ec_admin_col_12 ec_admin_col_first ec_admin_collapsable">
          <?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_product_details_seo_loader" ); ?>
          <div class="ec_admin_settings_label ec_admin_expand_section_header">
            <div class="dashicons-before dashicons-chart-area"></div>
            <span><?php _e( 'SEO OPTIONS', 'wp-easycart' ); ?></span>
            <a href="#seo" class="ec_admin_expand_section" data-section="ec_admin_product_details_seo_section"><div class="dashicons-before dashicons-arrow-down-alt2"></div></a>
            <div class="ec_page_product_title_button_wrap ec_page_title_button_wrap">
            	<a href="<?php echo $this->docs_link; ?>" target="_blank" class="ec_help_icon_link">
              		<div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
              	</a>
            </div>
          </div>
          <div class="ec_admin_settings_input ec_admin_collapsed_section" id="ec_admin_product_details_seo_section">
            <?php do_action( 'wp_easycart_admin_product_details_seo_fields' ); ?>
            <div class="ec_admin_products_submit">
                <input type="submit" class="ec_admin_products_simple_button" onclick="return ec_admin_save_product_details_seo( );" value="<?php _e( 'Update SEO', 'wp-easycart' ); ?>" />
            </div>
          </div>
        </div>
      </div>
      
      
      <div class="ec_admin_flex_row<?php if( !$this->id ){ ?> ec_admin_hidden<?php }?>">
        <div class="ec_admin_list_line_item ec_admin_col_12 ec_admin_col_first ec_admin_collapsable">
          <?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_product_details_menus_loader" ); ?>
          <div class="ec_admin_settings_label ec_admin_expand_section_header">
            <div class="dashicons-before dashicons-forms"></div>
            <span><?php _e( 'MENU LOCATIONS', 'wp-easycart' ); ?></span>
            <a href="#menus" class="ec_admin_expand_section" data-section="ec_admin_product_details_menus_section"><div class="dashicons-before dashicons-arrow-down-alt2"></div></a>
            <div class="ec_page_product_title_button_wrap ec_page_title_button_wrap">
            	<a href="<?php echo $this->docs_link; ?>" target="_blank" class="ec_help_icon_link">
              		<div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
              	</a>
            </div>
          </div>
          <div class="ec_admin_settings_input ec_admin_collapsed_section" id="ec_admin_product_details_menus_section">
            <?php do_action( 'wp_easycart_admin_product_details_menus_fields' ); ?>
            <div class="ec_admin_products_submit">
                <input type="submit" class="ec_admin_products_simple_button" onclick="return ec_admin_save_product_details_menus( );" value="<?php _e( 'Update Menus', 'wp-easycart' ); ?>" />
            </div>
          </div>
        </div>
      </div>
      <div class="ec_admin_flex_row<?php if( !$this->id ){ ?> ec_admin_hidden<?php }?>">
        <div class="ec_admin_list_line_item ec_admin_col_12 ec_admin_col_first ec_admin_collapsable">
          <?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_product_details_categories_loader" ); ?>
          <div class="ec_admin_settings_label ec_admin_expand_section_header">
            <div class="dashicons-before dashicons-list-view"></div>
            <span><?php _e( 'CATEGORY LOCATIONS', 'wp-easycart' ); ?></span>
            <a href="#categories" class="ec_admin_expand_section" data-section="ec_admin_product_details_categories_section"><div class="dashicons-before dashicons-arrow-down-alt2"></div></a>
            <div class="ec_page_product_title_button_wrap ec_page_title_button_wrap">
            	<a href="<?php echo $this->docs_link; ?>" target="_blank" class="ec_help_icon_link">
              		<div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
              	</a>
            </div>
          </div>
          <div class="ec_admin_settings_input ec_admin_collapsed_section" id="ec_admin_product_details_categories_section">
            <?php do_action( 'wp_easycart_admin_product_details_categories_fields' ); ?>
          </div>
        </div>
      </div>
      
      <div class="ec_admin_flex_row<?php if( !$this->id ){ ?> ec_admin_hidden<?php }?>">
        <div class="ec_admin_list_line_item ec_admin_col_12 ec_admin_col_first ec_admin_collapsable">
          <?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_product_details_packaging_loader" ); ?>
          <div class="ec_admin_settings_label ec_admin_expand_section_header">
            <div class="dashicons-before dashicons-move"></div>
            <span><?php _e( 'PACKAGING OPTIONS', 'wp-easycart' ); ?></span>
            <a href="#packaging" class="ec_admin_expand_section" data-section="ec_admin_product_details_packaging_section"><div class="dashicons-before dashicons-arrow-down-alt2"></div></a>
            <div class="ec_page_product_title_button_wrap ec_page_title_button_wrap">
            	<a href="<?php echo $this->docs_link; ?>" target="_blank" class="ec_help_icon_link">
              		<div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
              	</a>
            </div>
          </div>
          <div class="ec_admin_settings_input ec_admin_collapsed_section" id="ec_admin_product_details_packaging_section">
            <?php do_action( 'wp_easycart_admin_product_details_packaging_fields' ); ?>
            <div class="ec_admin_products_submit">
                <input type="submit" class="ec_admin_products_simple_button" onclick="return ec_admin_save_product_details_packaging( );" value="<?php _e( 'Update Packaging', 'wp-easycart' ); ?>" />
            </div>
          </div>
        </div>
      </div>
      
      <div class="ec_admin_flex_row<?php if( !$this->id ){ ?> ec_admin_hidden<?php }?>">
        <div class="ec_admin_list_line_item ec_admin_col_12 ec_admin_col_first ec_admin_collapsable">
          <?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_product_details_shipping_loader" ); ?>
          <div class="ec_admin_settings_label ec_admin_expand_section_header">
            <div class="dashicons-before dashicons-store"></div>
            <span><?php _e( 'SHIPPING OPTIONS', 'wp-easycart' ); ?></span>
            <a href="#shipping" class="ec_admin_expand_section" data-section="ec_admin_product_details_shipping_section"><div class="dashicons-before dashicons-arrow-down-alt2"></div></a>
            <div class="ec_page_product_title_button_wrap ec_page_title_button_wrap">
            	<a href="<?php echo $this->docs_link; ?>" target="_blank" class="ec_help_icon_link">
              		<div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
              	</a>
            </div>
          </div>
          <div class="ec_admin_settings_input ec_admin_collapsed_section" id="ec_admin_product_details_shipping_section">
             <?php do_action( 'wp_easycart_admin_product_details_shipping_fields' ); ?>
            <div class="ec_admin_products_submit">
                <input type="submit" class="ec_admin_products_simple_button" onclick="return ec_admin_save_product_details_shipping( );" value="<?php _e( 'Update Shipping', 'wp-easycart' ); ?>" />
            </div>
          </div>
        </div>
      </div>
      
      <div class="ec_admin_flex_row<?php if( !$this->id ){ ?> ec_admin_hidden<?php }?>">
        <div class="ec_admin_list_line_item ec_admin_col_12 ec_admin_col_first ec_admin_collapsable">
          <?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_product_details_tax_loader" ); ?>
          <div class="ec_admin_settings_label ec_admin_expand_section_header">
            <div class="dashicons-before dashicons-cart"></div>
            <span><?php _e( 'TAX OPTIONS', 'wp-easycart' ); ?></span>
            <a href="#tax" class="ec_admin_expand_section" data-section="ec_admin_product_details_tax_section"><div class="dashicons-before dashicons-arrow-down-alt2"></div></a>
            <div class="ec_page_product_title_button_wrap ec_page_title_button_wrap">
            	<a href="<?php echo $this->docs_link; ?>" target="_blank" class="ec_help_icon_link">
              		<div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
              	</a>
            </div>
          </div>
          <div class="ec_admin_settings_input ec_admin_collapsed_section" id="ec_admin_product_details_tax_section">
            <?php do_action( 'wp_easycart_admin_product_details_tax_fields' ); ?>
            <div class="ec_admin_products_submit">
                <input type="submit" class="ec_admin_products_simple_button" onclick="return ec_admin_save_product_details_tax( );" value="<?php _e( 'Update Tax', 'wp-easycart' ); ?>" />
            </div>
          </div>
        </div>
      </div>
      
      <div class="ec_admin_flex_row<?php if( !$this->id ){ ?> ec_admin_hidden<?php }?>">
        <div class="ec_admin_address_line_item ec_admin_list_line_item ec_admin_col_12 ec_admin_col_first ec_admin_collapsable">
          <?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_product_details_short_description_loader" ); ?>
          <div class="ec_admin_settings_label ec_admin_expand_section_header">
            <div class="dashicons-before dashicons-menu"></div>
            <span><?php _e( 'SHORT DESCRIPTION', 'wp-easycart' ); ?></span>
            <a href="#short-description" class="ec_admin_expand_section" data-section="ec_admin_product_details_short_description_section"><div class="dashicons-before dashicons-arrow-down-alt2"></div></a>
            <div class="ec_page_product_title_button_wrap ec_page_title_button_wrap">
            	<a href="<?php echo $this->docs_link; ?>" target="_blank" class="ec_help_icon_link">
              		<div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
              	</a>
            </div>
          </div>
          <div class="ec_admin_settings_input ec_admin_collapsed_section" id="ec_admin_product_details_short_description_section">
            <?php do_action( 'wp_easycart_admin_product_details_short_description_fields' ); ?>
            <div class="ec_admin_products_submit">
                <input type="submit" class="ec_admin_products_simple_button" onclick="return ec_admin_save_product_details_short_description( );" value="<?php _e( 'Update Short Description', 'wp-easycart' ); ?>" />
            </div>
          </div>
        </div>
      </div>
      
      <div class="ec_admin_flex_row<?php if( !$this->id ){ ?> ec_admin_hidden<?php }?>">
        <div class="ec_admin_address_line_item ec_admin_list_line_item ec_admin_col_12 ec_admin_col_first ec_admin_collapsable">
          <?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_product_details_specifications_loader" ); ?>
          <div class="ec_admin_settings_label ec_admin_expand_section_header">
            <div class="dashicons-before dashicons-analytics"></div>
            <span><?php _e( 'SPECIFICATIONS', 'wp-easycart' ); ?></span>
            <a href="#specifications" class="ec_admin_expand_section" data-section="ec_admin_product_details_specifications_section"><div class="dashicons-before dashicons-arrow-down-alt2"></div></a>
            <div class="ec_page_product_title_button_wrap ec_page_title_button_wrap">
            	<a href="<?php echo $this->docs_link; ?>" target="_blank" class="ec_help_icon_link">
              		<div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
              	</a>
            </div>
          </div>
          <div class="ec_admin_settings_input ec_admin_collapsed_section" id="ec_admin_product_details_specifications_section">
            <?php do_action( 'wp_easycart_admin_product_details_specifications_fields' ); ?>
            <div class="ec_admin_products_submit">
                <input type="submit" class="ec_admin_products_simple_button" onclick="return ec_admin_save_product_details_specifications( );" value="<?php _e( 'Update Specifications', 'wp-easycart' ); ?>" />
            </div>
          </div>
        </div>
      </div>
      
      <div class="ec_admin_flex_row<?php if( !$this->id ){ ?> ec_admin_hidden<?php }?>">
        <div class="ec_admin_address_line_item ec_admin_list_line_item ec_admin_col_12 ec_admin_col_first ec_admin_collapsable">
          <?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_product_details_ordercompleted_loader" ); ?>
          <div class="ec_admin_settings_label ec_admin_expand_section_header">
            <div class="dashicons-before dashicons-welcome-write-blog"></div>
            <span><?php _e( 'ORDER COMPLETED NOTE', 'wp-easycart' ); ?></span>
            <a href="#ordercompleted" class="ec_admin_expand_section" data-section="ec_admin_product_details_ordercompleted_section"><div class="dashicons-before dashicons-arrow-down-alt2"></div></a>
            <div class="ec_page_product_title_button_wrap ec_page_title_button_wrap">
            	<a href="<?php echo $this->docs_link; ?>" target="_blank" class="ec_help_icon_link">
              		<div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
              	</a>
            </div>
          </div>
          <div class="ec_admin_settings_input ec_admin_collapsed_section" id="ec_admin_product_details_ordercompleted_section">
            <?php do_action( 'wp_easycart_admin_product_details_order_completed_note_fields' ); ?>
            <div class="ec_admin_products_submit">
                <input type="submit" class="ec_admin_products_simple_button" onclick="return ec_admin_save_product_details_order_completed_note( );" value="<?php _e( 'Update Note', 'wp-easycart' ); ?>" />
            </div>
          </div>
        </div>
      </div>
      
      <div class="ec_admin_flex_row<?php if( !$this->id ){ ?> ec_admin_hidden<?php }?>">
        <div class="ec_admin_address_line_item ec_admin_list_line_item ec_admin_col_12 ec_admin_col_first ec_admin_collapsable">
          <?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_product_details_ordercompleted_email_loader" ); ?>
          <div class="ec_admin_settings_label ec_admin_expand_section_header">
            <div class="dashicons-before dashicons-welcome-write-blog"></div>
            <span><?php _e( 'ORDER EMAIL NOTE', 'wp-easycart' ); ?></span>
            <a href="#ordercompletedemail" class="ec_admin_expand_section" data-section="ec_admin_product_details_ordercompletedemail_section"><div class="dashicons-before dashicons-arrow-down-alt2"></div></a>
            <div class="ec_page_product_title_button_wrap ec_page_title_button_wrap">
            	<a href="<?php echo $this->docs_link; ?>" target="_blank" class="ec_help_icon_link">
              		<div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
              	</a>
            </div>
          </div>
          <div class="ec_admin_settings_input ec_admin_collapsed_section" id="ec_admin_product_details_ordercompletedemail_section">
            <?php do_action( 'wp_easycart_admin_product_details_order_completed_email_note_fields' ); ?>
            <div class="ec_admin_products_submit">
                <input type="submit" class="ec_admin_products_simple_button" onclick="return ec_admin_save_product_details_order_completed_email_note( );" value="<?php _e( 'Update Note', 'wp-easycart' ); ?>" />
            </div>
          </div>
        </div>
      </div>
      
      <div class="ec_admin_flex_row<?php if( !$this->id ){ ?> ec_admin_hidden<?php }?>">
        <div class="ec_admin_address_line_item ec_admin_list_line_item ec_admin_col_12 ec_admin_col_first ec_admin_collapsable">
          <?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_product_details_ordercompleted_details_loader" ); ?>
          <div class="ec_admin_settings_label ec_admin_expand_section_header">
            <div class="dashicons-before dashicons-welcome-write-blog"></div>
            <span><?php _e( 'ORDER DETAILS NOTE', 'wp-easycart' ); ?></span>
            <a href="#ordercompleteddetails" class="ec_admin_expand_section" data-section="ec_admin_product_details_ordercompleteddetails_section"><div class="dashicons-before dashicons-arrow-down-alt2"></div></a>
            <div class="ec_page_product_title_button_wrap ec_page_title_button_wrap">
            	<a href="<?php echo $this->docs_link; ?>" target="_blank" class="ec_help_icon_link">
              		<div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
              	</a>
            </div>
          </div>
          <div class="ec_admin_settings_input ec_admin_collapsed_section" id="ec_admin_product_details_ordercompleteddetails_section">
            <?php do_action( 'wp_easycart_admin_product_details_order_completed_details_note_fields' ); ?>
            <div class="ec_admin_products_submit">
                <input type="submit" class="ec_admin_products_simple_button" onclick="return ec_admin_save_product_details_order_completed_details_note( );" value="<?php _e( 'Update Note', 'wp-easycart' ); ?>" />
            </div>
          </div>
        </div>
      </div>
      
      <div class="ec_admin_flex_row<?php if( !$this->id ){ ?> ec_admin_hidden<?php }?>">
        <div class="ec_admin_address_line_item ec_admin_list_line_item ec_admin_col_12 ec_admin_col_first ec_admin_collapsable">
          <?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_product_details_tags_loader" ); ?>
          <div class="ec_admin_settings_label ec_admin_expand_section_header">
            <div class="dashicons-before dashicons-tablet"></div>
            <span><?php _e( 'IMAGE TAGS', 'wp-easycart' ); ?></span>
            <a href="#tags" class="ec_admin_expand_section" data-section="ec_admin_product_details_tags_section"><div class="dashicons-before dashicons-arrow-down-alt2"></div></a>
            <div class="ec_page_product_title_button_wrap ec_page_title_button_wrap">
            	<a href="<?php echo $this->docs_link; ?>" target="_blank" class="ec_help_icon_link">
              		<div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
              	</a>
            </div>
          </div>
          <div class="ec_admin_settings_input ec_admin_collapsed_section" id="ec_admin_product_details_tags_section">
            <?php do_action( 'wp_easycart_admin_product_details_tags_fields' ); ?>
            <div class="ec_admin_products_submit">
                <input type="submit" class="ec_admin_products_simple_button" onclick="return ec_admin_save_product_details_tags( );" value="<?php _e( 'Update Tags', 'wp-easycart' ); ?>" />
            </div>
          </div>
        </div>
      </div>
      
      <div class="ec_admin_flex_row<?php if( !$this->id ){ ?> ec_admin_hidden<?php }?>">
        <div class="ec_admin_list_line_item ec_admin_col_12 ec_admin_col_first ec_admin_collapsable">
          <?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_product_details_downloads_loader" ); ?>
          <div class="ec_admin_settings_label ec_admin_expand_section_header">
            <div class="dashicons-before dashicons-arrow-down-alt"></div>
            <span><?php _e( 'DOWNLOAD OPTIONS', 'wp-easycart' ); ?></span>
            <a href="#downloads" class="ec_admin_expand_section" data-section="ec_admin_product_details_downloads_section"><div class="dashicons-before dashicons-arrow-down-alt2"></div></a>
            <div class="ec_page_product_title_button_wrap ec_page_title_button_wrap">
            	<a href="<?php echo $this->docs_link; ?>" target="_blank" class="ec_help_icon_link">
              		<div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
              	</a>
            </div>
          </div>
          <div class="ec_admin_settings_input ec_admin_collapsed_section" id="ec_admin_product_details_downloads_section">
            <?php do_action( 'wp_easycart_admin_product_details_downloads_fields' ); ?>
            <div class="ec_admin_products_submit">
                <input type="submit" class="ec_admin_products_simple_button" onclick="return ec_admin_save_product_details_downloads( );" value="<?php _e( 'Update Downloads', 'wp-easycart' ); ?>" />
            </div>
          </div>
        </div>
      </div>
      
      <div class="ec_admin_flex_row<?php if( !$this->id ){ ?> ec_admin_hidden<?php }?>">
        <div class="ec_admin_list_line_item ec_admin_col_12 ec_admin_col_first ec_admin_collapsable">
          <?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_product_details_subscription_loader" ); ?>
          <div class="ec_admin_settings_label ec_admin_expand_section_header">
            <div class="dashicons-before dashicons-image-rotate"></div>
            <span><?php _e( 'SUBSCRIPTION OPTIONS', 'wp-easycart' ); ?></span>
            <a href="#subscription" class="ec_admin_expand_section" data-section="ec_admin_product_details_subscription_section"><div class="dashicons-before dashicons-arrow-down-alt2"></div></a>
            <div class="ec_page_product_title_button_wrap ec_page_title_button_wrap">
            	<a href="<?php echo $this->docs_link; ?>" target="_blank" class="ec_help_icon_link">
              		<div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
              	</a>
            </div>
          </div>
          <div class="ec_admin_settings_input ec_admin_collapsed_section" id="ec_admin_product_details_subscription_section">
            <?php do_action( 'wp_easycart_admin_product_details_subscription_fields' ); ?>
            <div style="font-size:12px;">*<?php _e( 'NOTE: This product type is only compatible with Stripe', 'wp-easycart' ); ?></div>
          	<div class="ec_admin_products_submit">
                <input type="submit" class="ec_admin_products_simple_button" onclick="return ec_admin_save_product_details_subscription( );" value="<?php _e( 'Update Subscription', 'wp-easycart' ); ?>" />
            </div>
          </div>
        </div>
      </div>
      
      <div class="ec_admin_flex_row<?php if( !$this->id ){ ?> ec_admin_hidden<?php }?>">
        <div class="ec_admin_list_line_item ec_admin_col_12 ec_admin_col_first ec_admin_collapsable">
          <?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_product_details_deconetwork_loader" ); ?>
          <div class="ec_admin_settings_label ec_admin_expand_section_header">
            <div class="dashicons-before dashicons-admin-appearance"></div>
            <span><?php _e( 'DECONETWORK OPTIONS', 'wp-easycart' ); ?></span>
            <a href="#deconetwork" class="ec_admin_expand_section" data-section="ec_admin_product_details_deconetwork_section"><div class="dashicons-before dashicons-arrow-down-alt2"></div></a>
            <div class="ec_page_product_title_button_wrap ec_page_title_button_wrap">
            	<a href="<?php echo $this->docs_link; ?>" target="_blank" class="ec_help_icon_link">
              		<div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
              	</a>
            </div>
          </div>
          <div class="ec_admin_settings_input ec_admin_collapsed_section" id="ec_admin_product_details_deconetwork_section">
            <?php do_action( 'wp_easycart_admin_product_details_deconetwork_fields' ); ?>
            <div class="ec_admin_products_submit">
                <input type="submit" class="ec_admin_products_simple_button" onclick="return ec_admin_save_product_details_deconetwork( );" value="<?php _e( 'Update Deconetwork', 'wp-easycart' ); ?>" />
            </div>
          </div>
        </div>
      </div>
      
      <div class="ec_admin_flex_row<?php if( !$this->id ){ ?> ec_admin_hidden<?php }?>">
        <div class="ec_admin_list_line_item ec_admin_col_12 ec_admin_col_first ec_admin_collapsable">
          <?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_product_details_google_merchant_loader" ); ?>
          <div class="ec_admin_settings_label ec_admin_expand_section_header">
            <div class="dashicons-before dashicons-rest-api"></div>
            <span><?php _e( 'GOOGLE MERCHANT OPTIONS', 'wp-easycart' ); ?></span>
            <a href="#googlemerchant" class="ec_admin_expand_section" data-section="ec_admin_product_details_googlemerchant_section"><div class="dashicons-before dashicons-arrow-down-alt2"></div></a>
            <div class="ec_page_product_title_button_wrap ec_page_title_button_wrap">
            	<a href="<?php echo $this->docs_link; ?>" target="_blank" class="ec_help_icon_link">
              		<div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
              	</a>
            </div>
          </div>
          <div class="ec_admin_settings_input ec_admin_collapsed_section" id="ec_admin_product_details_googlemerchant_section">
            <div class="ec_admin_row_heading_title">Google Merchant - <a href="https://support.google.com/merchants/answer/7052112?hl=en" target="_blank"><?php _e( 'PLEASE REVIEW HERE FOR VALID VALUES', 'wp-easycart' ); ?></a>!</div>
            <?php do_action( 'wp_easycart_admin_product_details_googlemerchant_fields' ); ?>
            <div class="ec_admin_products_submit">
                <input type="submit" class="ec_admin_products_simple_button" onclick="return ec_admin_save_product_details_googlemerchant( );" value="<?php _e( 'Update Google Merchant', 'wp-easycart' ); ?>" />
            </div>
          </div>
        </div>
      </div>
      
      <?php do_action( 'wp_easycart_admin_product_details_sections_post' ); ?>
      
      <div class="ec_admin_details_footer">
          <div class="ec_page_title_button_wrap">
              <a href="<?php echo $this->action; ?>" class="ec_page_title_button"><?php _e( 'Back to Products', 'wp-easycart' ); ?></a>
          </div>
      </div>
    </div>
</div>