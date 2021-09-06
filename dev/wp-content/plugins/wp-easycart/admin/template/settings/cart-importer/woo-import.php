<div class="ec_admin_list_line_item ec_admin_demo_data_line">
            
	<?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_woo_importer" ); ?>
    
    <div class="ec_admin_settings_label">
        <div class="dashicons-before dashicons-migrate"></div>
        <span><?php _e( 'WooCommerce Importer', 'wp-easycart' ); ?></span>
        <a href="<?php echo wp_easycart_admin( )->helpsystem->print_docs_url('settings', 'cart-importer', 'woo');?>" target="_blank" class="ec_help_icon_link">
            <div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
        </a>
        <?php echo wp_easycart_admin( )->helpsystem->print_vids_url('settings', 'cart-importer', 'woo');?>
    </div>
    
    <div class="ec_admin_settings_input ec_admin_settings_live_payment_section">
        
        <?php

		if( isset( $_GET['ec_success'] ) && $_GET['ec_success'] == "woo-imported" ){ ?>
			<div  class="ec_save_success">
				<p><?php _e( 'Your WooCommerce store has been imported to the EasyCart. There are no guarantees that all options have been imported, becuase Woo offers so many extensions. Please check over the data and manually add anything that may be missing.', 'wp-easycart' ); ?></p>
			</div>
		<?php } else if( isset( $_GET['ec_action'] ) && $_GET['ec_action'] == "import-woo-products" ){	?>
			<div  class="ec_save_success">
				<p><?php _e( 'Importing... Please Wait...', 'wp-easycart' ); ?></p>
			</div>
        <?php } ?>
        <form action="admin.php?page=wp-easycart-settings&subpage=cart-importer&ec_action=import-woo-products" method="POST" enctype="multipart/form-data" name="wpeasycart_admin_form" id="wpeasycart_admin_form_import1" novalidate="novalidate">
        <div  class="settings_list_items"><p><?php _e( 'Importing your data from your WooCommerce store is as simple as a click of a button! Although we do our best to import your data, not everything is transferrable or is known about all extensions available to the Woo system. The following information is imported by our system:', 'wp-easycart' ); ?></p>
        <ul>
            <li><?php _e( 'Woo Product Categories', 'wp-easycart' ); ?></li>
            <li><?php _e( 'Woo Attributes are imported as option sets to our system', 'wp-easycart' ); ?></li>
            <li><?php _e( 'Woo Products are imported by the following rules:', 'wp-easycart' ); ?><ul>
                <li><?php _e( 'Title, Description, Short Description, Price (Sale/Regular), Allow Comments, Taxable, Download, Service Item (Virtual), SKU, Download File, Download Limit, Download Expiry, Manage Stock, Stock Status, Stock Quantity', 'wp-easycart' ); ?></li>
                <li><?php _e( 'Connects Imported Attributes (now option sets) to products the same as Woo has connected.', 'wp-easycart' ); ?></li>
                <li><?php _e( 'Connects Product Categories to Products.', 'wp-easycart' ); ?></li>
                <li><?php _e( 'If no SKU available, random model number is created.', 'wp-easycart' ); ?></li>
                <li><?php _e( 'Product images are copied into our system from WordPress upload system', 'wp-easycart' ); ?></li>
                <li><?php _e( 'Limited to 5 images and first 5 of image gallery used', 'wp-easycart' ); ?></li>
                <li><?php _e( 'If no image gallery, uses featured image', 'wp-easycart' ); ?></li>
            </ul></li>
        </ul>
        
        </div>
        
        <?php if( class_exists( "WooCommerce" ) ){ ?>
        
        <div class="ec_admin_settings_input"><input type="submit" value="<?php _e( 'IMPORT WooCommerce DATA NOW', 'wp-easycart' ); ?>" class="ec_admin_settings_simple_button" /></div>
        
        
        <?php }else{ ?>
        <div>
        <div class="error">
            <p><?php _e( 'We cannot detect a version of WooCommerce installed, which may mean that this section does not apply to your site or WooCommerce has been deactivated. In order for us to complete a successful import from WooCommerce, we need a copy of WooCommerce Installed and activated on your site.', 'wp-easycart' ); ?></p>
        </div>
        </div>
        
        <?php }?>
        </form>
       	

    </div>
</div>

<?php
	if( isset( $_GET['ec_action'] ) && $_GET['ec_action'] == "import-woo-products" ){	
		global $wpdb;
		$prefix = $wpdb->prefix;
		$new_optionsets = array( ); // Keep list of attribute ids to option_ids
		$new_categories = array( ); // Keep list of cat ids to category_ids
		$new_products = array( ); // Keep list of product ids to post ids
		$add_crosssale = array( ); // Keep list of product_id + cross-sale post_ids, cross-reference new_product with post_id to update products
		
		$optionsets = $wpdb->get_results( "SELECT * FROM " . $prefix . "woocommerce_attribute_taxonomies" );
		
		foreach( $optionsets as $optionset ){
			$attribute_id = $optionset->attribute_id;
			$option_name = $optionset->attribute_name;
			$option_label = $optionset->attribute_label;
			$option_type = $optionset->attribute_type;
			
			if( $option_type == "select" ){
				$option_type = "combo";
			}
			
			$optionitems = $wpdb->get_results( $wpdb->prepare( "SELECT " . $prefix . "terms.* FROM " . $prefix . "term_taxonomy LEFT JOIN " . $prefix . "terms ON (" . $prefix . "terms.term_id = " . $prefix . "term_taxonomy.term_id ) WHERE " . $prefix . "term_taxonomy.taxonomy = %s", "pa_" . $option_name ) );
			
			// Insert option
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_option( option_name, option_label, option_type, option_required ) VALUES( %s, %s, %s, 0 )", $option_name, $option_label, $option_type ) );
			$option_id = $wpdb->insert_id;
			$new_optionsets["pa_" . $option_name] = $option_id;
			
			// Insert option items
			$order_num = 0;
			foreach( $optionitems as $optionitem ){
				$wpdb->query( $wpdb->prepare( "INSERT INTO ec_optionitem( option_id, optionitem_name, optionitem_order ) VALUES( %d, %s, %d )", $option_id, $optionitem->name, $order_num ) );
				$order_num++;
			}
		}
		
		$categories = $wpdb->get_results( "SELECT " . $prefix . "terms.* FROM " . $prefix . "term_taxonomy LEFT JOIN " . $prefix . "terms ON (" . $prefix . "terms.term_id = " . $prefix . "term_taxonomy.term_id ) WHERE " . $prefix . "term_taxonomy.taxonomy = 'product_cat'" );
		foreach( $categories as $category ){
			
			// Insert Category
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_category( category_name ) VALUES( %s )", $category->name ) );
			$category_id = $wpdb->insert_id;
			$new_categories["id-".$category->term_id] = $category_id;
			
			// Insert Category WordPress Post
			$post = array(	'post_content'	=> "[ec_store groupid=\"" . $category_id . "\"]",
							'post_status'	=> "publish",
							'post_title'	=> $category->name,
							'post_type'		=> "ec_store"
						  );
			$post_id = wp_insert_post( $post );
			
			// Update Category Post ID
			$wpdb->query( $wpdb->prepare( "UPDATE ec_category SET ec_category.post_id = %s WHERE ec_category.category_id = %d", $post_id, $category_id ) );
			
		}
		
		//----------manufacturer-------
		$wpdb->query( "INSERT INTO ec_manufacturer( `name` ) VALUES( 'Woo Products' )" );
		$manufacturer_id = $wpdb->insert_id;
		
		// Insert a WordPress Custom post type post.
		$post = array(	'post_content'	=> "[ec_store manufacturerid=\"" . $manufacturer_id . "\"]",
						'post_status'	=> "publish",
						'post_title'	=> "WOO Products",
						'post_type'		=> "ec_store"
		);
		$post_id = wp_insert_post( $post );
		
		// Update manufacturer
		$wpdb->query( $wpdb->prepare( "UPDATE ec_manufacturer SET ec_manufacturer.post_id = %s WHERE ec_manufacturer.manufacturer_id = %d", $post_id, $manufacturer_id ) );
		
		$product_args = array( 'posts_per_page' => 100000, 'offset' => 0, 'post_type' => 'product' );
		$woo_products = get_posts( $product_args );
		
		foreach( $woo_products as $product ){
			
			$post_meta = get_post_meta( $product->ID );
			
			//----------model number-------
			$sku = $post_meta['_sku'][0];
			if( $sku == "" )
				$model_number = rand( 0, 9 ) . rand( 0, 9 ) . rand( 0, 9 ) . rand( 0, 9 ) . rand( 0, 9 ) . rand( 0, 9 ) . rand( 0, 9 ) . rand( 0, 9 ) . rand( 0, 9 );
			else
				$model_number = $sku;
			
			//---------basic info------------
			$title = $product->post_title;
			$description = $product->post_content;
			$short_description = $product->post_excerpt;
			
			//---------activate in store------
			$visibility = $post_meta['_visibility'][0]; // visible if show in store
			if( $product->post_status == "publish" ){
				$is_active = true;
			}else{
				$is_active = false;
			}
			
			if( $is_active && $visibility == "visible" )
				$activate_in_store = true;
			else
				$activate_in_store = false;
			
			//-----------price options------
			$regular_price = $post_meta['_regular_price'][0];
			$sale_price = $post_meta['_sale_price'][0]; // use if not empty
			$price = $post_meta['_price'][0]; // Not sure what is different between price and regular price?
			if( $sale_price != "" ){ // If a sale is setup
				$price = $sale_price;
				$list_price = $regular_price;
			}
			
			//------------tax options-------
			$tax_status = $post_meta['_tax_status'][0]; // taxable if tax enabled
			if( $tax_status == "taxable" )
				$is_taxable = true;
			else
				$is_taxable = false;
			
			//------------stock options------
			$manage_stock = $post_meta['_manage_stock'][0]; // yes if we should we keep track of stock
			$stock_status = $post_meta['_stock_status'][0]; // instock if available
			$stock = $post_meta['_stock'][0]; // Stock value
			if( $manage_stock == "yes" && $stock != "" ){
				$stock_quantity = $stock;
			}else if( $stock_status == "instock" ){
				$stock_quantity = 9999;
			}else{
				$stock_quantity = 0;
			}
			if( $manage_stock == "yes" )
				$show_stock_quantity = true;
			else
				$show_stock_quantity = false;
			
			//------------diminsions options-----
			$virtual = $post_meta['_virtual'][0]; // set values to 0 if service item
			$weight = $post_meta['_weight'][0]; // if no value, set to 0?
			if( $weight == "" || $virtual == "yes" )
				$weight = 0;
			$length = $post_meta['_length'][0];
			if( $length == "" || $virtual == "yes"  )
				$length = 0;
			$width = $post_meta['_width'][0];
			if( $width == "" || $virtual == "yes"  )
				$width = 0;
			$height = $post_meta['_height'][0];
			if( $height == "" || $virtual == "yes"  )
				$height = 0;
			
			//-----------custom reviews option-----
			if( $product->comment_status == "open" )
				$use_customer_reviews = true;
			else
				$use_customer_reviews = false;
				
			$reviews = get_comments( array( "post_id" => $product->ID ) );
			
			//----------download-----------
			$downloadable = $post_meta['_downloadable'][0]; // no if not downloadable
			if( $downloadable == "yes" ){
				$files = maybe_unserialize( $post_meta['_downloadable_files'][0] );
				foreach( $files as $file ){
					break;
				}
				
				$path = pathinfo( $file['file'] );
				$file_name = $path['filename'] . "_" . rand( 100000, 999999 ) . "." . $path['extension'];
				copy( $file['file'], WP_PLUGIN_DIR . "/wp-easycart-data/products/downloads/" . $file_name );
				
				$is_download = true;
				$download_file_name = $file_name;
				$maximum_downloads_allowed = $post_meta['_download_limit'][0];
				$download_timelimit_seconds = $post_meta['_download_expiry'][0] * 24 * 60 * 60;
			}else{
				$is_download = false;
				$download_file_name = "";
				$maximum_downloads_allowed = 0;
				$download_timelimit_seconds = 0;
			}
			
			//----------images-------------
			$image1 = wp_get_attachment_url( get_post_thumbnail_id( $product->ID ) );
			$image2 = "";
			$image3 = "";
			$image4 = "";
			$image5 = "";
			
			$gallery_images_string = $post_meta['_product_image_gallery'][0];
			$gallery_images_array = explode( ",", $gallery_images_string );
			if( $gallery_images_array[0] != "" ){
				$product_images = array( );
				foreach( $gallery_images_array as $gallery_item ){
					$product_images[] = wp_get_attachment_url( $gallery_item );
				}
				
				for( $i=0; $i<count( $product_images ) && $i<5; $i++ ){
					if( $i == 0 )
						$image1 = $product_images[$i];
					else if( $i == 1 )
						$image2 = $product_images[$i];
					else if( $i == 2 )
						$image3 = $product_images[$i];
					else if( $i == 3 )
						$image4 = $product_images[$i];
					else if( $i == 4 )
						$image5 = $product_images[$i];
				}
			}
			
			//----------options setup------
			$product_attributes = maybe_unserialize( $post_meta['_product_attributes'][0] ); // need to link to option sets
			$product_options = array( );
			foreach( $product_attributes as $key => $value ){
				$product_options[] = $new_optionsets[$key]; // Add option_id to product option array
			}
			
			//-------------categories-------
			$product_cats = $wpdb->get_results( $wpdb->prepare( "SELECT " . $prefix . "term_relationships.term_taxonomy_id FROM " . $prefix . "term_relationships, " . $prefix . "terms, " . $prefix . "term_taxonomy WHERE " . $prefix . "term_taxonomy.taxonomy = 'product_cat' AND " . $prefix . "term_taxonomy.term_id = " . $prefix . "terms.term_id AND " . $prefix . "terms.term_id = " . $prefix . "term_relationships.term_taxonomy_id AND " . $prefix . "term_relationships.object_id = %d", $product->ID ) );
			
			$product_categories = array( );
			foreach( $product_cats as $value ){
				$product_categories[] = $new_categories["id-".$value->term_taxonomy_id]; // Add category_id to product option array
			}
			
			//----------featured products--
			$crosssell_ids = maybe_unserialize( $post_meta['_crosssell_ids'][0] );
			
			//----------startup show-----
			$show_on_startup = true;
			
			//----------shippable status---
			$is_shippable = true;
			if( $is_download || $weight <= 0 )
				$is_shippable = false;
			
			//------------Need to insert product----------
			$wpdb->query( $wpdb->prepare( "INSERT INTO ec_product( 	model_number, activate_in_store, title, description, 
																	price, list_price, stock_quantity, weight, width, 
																	height, length, use_customer_reviews, manufacturer_id, download_file_name, 
																	image1, image2, image3, image4, image5, 
																	use_advanced_optionset, featured_product_id_1, featured_product_id_2, featured_product_id_3, featured_product_id_4, 
																	is_download, is_taxable, is_shippable, show_on_startup, show_stock_quantity, 
																	maximum_downloads_allowed, download_timelimit_seconds ) VALUES( 
																	%s, %d, %s, %s, 
																	%s, %s, %d, %s, %s, 
																	%s, %s, %d, %d, %s, 
																	%s, %s, %s, %s, %s, 
																	1, %d, %d, %d, %d, 
																	%d, %d, %d, %d, %d, 
																	%s, %s )",
																	$model_number, $activate_in_store, $title, $description,
																	$price, $list_price, $stock_quantity, $weight, $width,
																	$height, $length, $use_customer_reviews, $manufacturer_id, $download_file_name,
																	$image1, $image2, $image3, $image4, $image5,
																	$featured_id_1, $featured_id_2, $featured_id_3, $featured_id_4,
																	$is_download, $is_taxable, $is_shippable, $show_on_startup, $show_stock_quantity,
																	$maximum_downloads_allowed, $download_timelimit_seconds ) );
			$product_id = $wpdb->insert_id;
			$new_products["id-".$product->ID] = $product_id;
			if( $crosssell_ids )
				$add_crosssale["id-".$product_id] = $crosssell_ids;
			
			
			// Need to Insert WordPress Post
			if( $activate_in_store )
				$status = "publish";
			else
				$status = "private";
			
			$post = array(	'post_content'	=> "[ec_store modelnumber=\"" . $model_number . "\"]",
							'post_status'	=> $status,
							'post_title'	=> $title,
							'post_type'		=> "ec_store"
						  );
			$post_id = wp_insert_post( $post );
			
			// Need to Update Product for post id
			$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET ec_product.post_id = %s WHERE ec_product.product_id = %d", $post_id, $product_id ) );
			
			// Apply optionsets to product, ec_option_to_product
			foreach( $product_options as $option_id ){
				$wpdb->query( $wpdb->prepare( "INSERT INTO ec_option_to_product( option_id, product_id ) VALUES( %d, %d )", $option_id, $product_id ) );
			}
			
			// Apply products to categories via ec_categoryitem
			foreach( $product_categories as $category_id ){
				$wpdb->query( $wpdb->prepare( "INSERT INTO ec_categoryitem( category_id, product_id ) VALUES( %d, %d )", $category_id, $product_id ) );
			}
			
			// Add Reviews to System
			foreach( $reviews as $review ){
				$approved = $review->comment_approved;
				$rating = get_comment_meta( $review->comment_ID, 'rating', true );
				$comment_title = "";
				$comment_description = $review->comment_content;
				$date_submitted = $review->comment_date;
				$wpdb->query( $wpdb->prepare( "INSERT INTO ec_review( product_id, approved, rating, title, description, date_submitted ) VALUES( %d, %d, %d, %s, %s, %s )", $product_id, $approved, $rating, $comment_title, $comment_description, $date_submitted ) );
			}
			
		}
		
		// Now add cross sales to products
		foreach( $add_crosssale as $key => $value ){
			
			$product_id = substr( $key, 3 );
			$featured_ids = array( );
			foreach( $value as $the_post_id ){
				$featured_ids[] = $new_products["id-".$the_post_id];
			}
			$featured_id_1 = 0;
			if( count( $featured_ids ) > 0 )
				$featured_id_1 = $featured_ids[0];
			$featured_id_2 = 0;
			if( count( $featured_ids ) > 1 )
				$featured_id_2 = $featured_ids[1];
			$featured_id_3 = 0;
			if( count( $featured_ids ) > 2 )
				$featured_id_3 = $featured_ids[2];
			$featured_id_4 = 0;
			if( count( $featured_ids ) > 3 )
				$featured_id_4 = $featured_ids[3];
				
			$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET ec_product.featured_product_id_1 = %d, ec_product.featured_product_id_2 = %d, ec_product.featured_product_id_3 = %d, ec_product.featured_product_id_4 = %d WHERE ec_product.product_id = %d", $featured_id_1, $featured_id_2, $featured_id_3, $featured_id_4, $product_id ) ); 
			
		}
		
		header( "location:admin.php?page=wp-easycart-settings&subpage=cart-importer&ec_success=woo-imported" );
	}
?>