<div class="ec_admin_list_line_item_fullwidth ec_admin_demo_data_line">
            
	<?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_store_status_loader" ); 
		$status = new wp_easycart_admin_store_status();
	
	?>
    
    <div class="ec_admin_settings_label">
        <div class="dashicons-before dashicons-admin-generic"></div>
        <span><?php _e( 'Store & Server Status', 'wp-easycart' ); ?></span>
        <a href="<?php echo wp_easycart_admin( )->helpsystem->print_docs_url('settings', 'store-status', 'settings');?>" target="_blank" class="ec_help_icon_link">
            <div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
        </a>
        <?php echo wp_easycart_admin( )->helpsystem->print_vids_url('settings', 'store-status', 'settings');?>
    </div>
    
    <div class="ec_admin_settings_input ec_admin_settings_live_payment_section">
        <?php
        $isupdate = false;
        if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "store-status" && isset( $_GET['ec_action'] ) && $_GET['ec_action'] == "send_test_email" ){
            $result = $status->ec_send_test_email( );
            if( $result )
                $isupdate = "1";
            else
                $isupdate = "2";
        }else if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "store-status" && isset( $_GET['ec_action'] ) && $_GET['ec_action'] == "reset_store_permalinks" ){
            $status->ec_reset_store_permalinks( );
            $isupdate = "3";
        }else if( isset( $_GET['ec_action'] ) && $_GET['ec_action'] == "fix_category_permalinks" ){
			global $wpdb;
			$count_fixed = 0;
			$categories = $wpdb->get_results( "SELECT * FROM ec_category" );
			foreach( $categories as $category ){
				$post = get_post( $category->post_id );
				if( !$post ){
					echo 'no post found: ' . $category->category_name;
					
					$insert_post_id = wp_insert_post( array(	
						'post_content'	=> "[ec_store groupid=\"" . $category->category_id . "\"]",
						'post_status'	=> "publish",
						'post_title'	=> $GLOBALS['language']->convert_text( $category->category_name ),
						'post_type'		=> "ec_store",
					) );
					if( $insert_post_id != 0 ){
						$post_id = $insert_post_id;
						$wpdb->query( $wpdb->prepare( "UPDATE ec_category SET post_id = %d WHERE category_id = %d", $post_id, $category->category_id ) );
						echo ' - fixed<br>';
					}else{
						echo ' - Could not fix<br>';
					}
				}
			}
		}else if( isset( $_GET['ec_action'] ) && $_GET['ec_action'] == "fix_product_permalinks" ){
			global $wpdb;
			$count_fixed = 0;
			$products = $wpdb->get_results( "SELECT activate_in_store, post_id, title, product_id, model_number FROM ec_product" );
			foreach( $products as $product ){
				$post = get_post( $product->post_id );
				if( !$post ){
					echo 'no post found: ' . $product->title;
					
					$insert_post_id = wp_insert_post( array(	
						'post_content'	=> "[ec_store modelnumber=\"" . $product->model_number . "\"]",
						'post_status'	=> "publish",
						'post_title'	=> $GLOBALS['language']->convert_text( $product->title ),
						'post_type'		=> "ec_store",
					) );
					if( $insert_post_id != 0 ){
						$post_id = $insert_post_id;
						$wpdb->query( $wpdb->prepare( "UPDATE ec_product SET post_id = %d WHERE product_id = %d", $post_id, $product->product_id ) );
						echo ' - fixed<br>';
						
					}else{
						echo ' - Could not fix<br>';
					}
					$count_fixed++;
					
				}else{ // check post status
					if( $post->post_status == 'publish' && !$product->activate_in_store ){ // should set to private
						echo 'post publish, should be private - ' . $product->title . ' - fixed<br>';
						wp_update_post( array( 'ID' => $post->ID, 'post_status'	=> "private" ) );
						$count_fixed++;
						
					}else if( $post->post_status == 'private' && $product->activate_in_store ){ // Should be publish
						echo 'post private, should be published - ' . $product->title . ' - fixed<br>';
						wp_update_post( array( 'ID' => $post->ID, 'post_status'	=> "publish" ) );
						$count_fixed++;
						
					}
				}
			}
		}
        ?>
        
        <?php
        ///////////////////////////////////////////////
        // Database Status Section
        ///////////////////////////////////////////////
        ?>
        <div class="ec_status_header"><div class="ec_status_header_text"><?php _e( 'Database Status', 'wp-easycart' ); ?></div></div>
        <?php if( $errors = $status->database_check( ) ){ ?>
        <div class="ec_status_error"><div class="dashicons-before dashicons-no"></div>
            <span class="ec_status_label"><?php _e( 'We have found problems with your WP EasyCart database structure.', 'wp-easycart' ); ?> <a href="admin.php?page=wp-easycart-status&subpage=store-status&ec_admin_form_action=repair-database"><?php _e( 'Click to Repair!', 'wp-easycart' ); ?></a>
            <br />
            <span id="wpeasycart_database_errors_min_status"><?php _e( 'For Complete Details', 'wp-easycart' ); ?> <a href="#" onclick="jQuery( '#wpeasycart_database_errors_status' ).show( ); jQuery( '#wpeasycart_database_errors_min_status' ).hide( ); return false;"><?php _e( 'Click Here', 'wp-easycart' ); ?></a></span>
            <ul id="wpeasycart_database_errors_status" style="display:none;">
            <?php foreach( $errors as $error ){
                echo '<li>' . $error['error'] . '</li>';
            } ?>
            </ul>
            </span>
        </div>
        
        <?php }else{ ?>
        <div class="ec_status_success"><div class="dashicons-before dashicons-yes"></div><span class="ec_status_label"><?php _e( 'Your database is currently correctly formatted and not missing any tables or columns.', 'wp-easycart' ); ?></span></div>
    
        <?php }?>
        
        <?php if( $isupdate && $isupdate == "1" ) { ?>
            <div id='setting-error-settings_updated' class='updated settings-success'><p><strong><?php _e( 'The receipt has been sent to the customer\'s email address and the admin.', 'wp-easycart' ); ?></strong></p></div>
        <?php }else if( $isupdate && $isupdate == "2" ){ ?>
            <div id='setting-error-settings_updated' class='updated settings-success'><p><strong><?php _e( 'The order row was not found from the entered order id.', 'wp-easycart' ); ?></strong></p></div> 
        <?php
        }else if( $isupdate && $isupdate == "3" ){ ?>
            <div id='setting-error-settings_updated' class='updated settings-success'><p><strong><?php _e( 'Your store permalinks have been reset.', 'wp-easycart' ); ?></strong></p></div> 
        <?php
        }
        ///////////////////////////////////////////////
        // Server Status Section
        ///////////////////////////////////////////////
        ?>
        
        <div class="ec_status_header"><div class="ec_status_header_text"><?php _e( 'Server Settings Status', 'wp-easycart' ); ?></div></div>
        
        <?php 
        ////////////////////////////
        // PHP Versoin Check
        ////////////////////////////
        if( $status->ec_get_php_version( ) < 5.3 ){ ?>
        <div class="ec_status_error"><div class="dashicons-before dashicons-no"></div><span class="ec_status_label"><?php _e( 'PHP 5.3 is the mimimal version accepted. We do not guarantee functionality for PHP versions below 5.3 at this time.', 'wp-easycart' ); ?></span></div>
        <?php }else{ ?>
        <div class="ec_status_success"><div class="dashicons-before dashicons-yes"></div><span class="ec_status_label"><?php echo sprintf( __( 'Your PHP Version is %s, meeting the PHP 5.3 minimal setup.', 'wp-easycart' ), $status->ec_get_php_version( ) ); ?></span></div>
        <?php } ?>
	
		<div class="ec_status_subs ec_status_success">
            <strong><?php _e( 'Common PHP Settings', 'wp-easycart' ); ?></strong><br />
            <p><?php _e( 'These settings are something you should contact your web hosting company regarding installation of modules and PHP setting adjustments.  This is likely not something EasyCart technicians would be able to assist with.', 'wp-easycart' ); ?></p>
            <p><i><strong><?php _e( 'Note: EasyCart may operate just fine in some situations with warnings in this section as some modules and settings only affect certain areas or features.  Refer to this section if you begin experiencing problems with EasyCart.', 'wp-easycart' ); ?></strong></i></p>
    
    	<?php
			// ======= Allow URL Fopen =======
			if (ini_get("allow_url_fopen") != "1")
			{
			?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-warning"></div><?php _e( 'URL fopen disabled', 'wp-easycart' ); ?></div><?php 
			} else {
			?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-yes"></div><?php _e( 'URL fopen enabled', 'wp-easycart' ); ?></div><?php
			}
		
			// ======= File Uploads =======
			if (ini_get("file_uploads") != "1")
			{
			?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-warning"></div><?php _e( 'File Uploads disabled', 'wp-easycart' ); ?></div><?php 
			} else {
			?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-yes"></div><?php _e( 'File Uploads enabled', 'wp-easycart' ); ?></div><?php
			}
			// ======= openSSL =======
			$isopenssl = extension_loaded("openssl");
			if (!$isopenssl)
			{
			?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-warning"></div><?php _e( 'Open SSL Not Installed', 'wp-easycart' ); ?></div><?php 
			} else {
			?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-yes"></div><?php _e( 'Open SSL Installed', 'wp-easycart' ); ?></div><?php
			}
			// ======= Curl =======
			$iscurl = extension_loaded("curl");
			if (!$iscurl)
			{
			?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-warning"></div><?php _e( 'Curl Not Installed', 'wp-easycart' ); ?></div><?php 
			} else {
			?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-yes"></div><?php _e( 'Curl Installed', 'wp-easycart' ); ?></div><?php
			}
			// ======= GD =======
			$isgd = extension_loaded("gd");
			if (!$isgd)
			{
			?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-warning"></div><?php _e( 'GD Not Installed', 'wp-easycart' ); ?></div><?php 
			} else {
			?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-yes"></div><?php _e( 'GD Installed', 'wp-easycart' ); ?></div><?php
			}
			// ======= SOAP =======
			$isSOAP = extension_loaded("SOAP");
			if (!$isSOAP)
			{
			?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-warning"></div><?php _e( 'SOAP Not Installed', 'wp-easycart' ); ?></div><?php 
			} else {
			?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-yes"></div><?php _e( 'SOAP Installed', 'wp-easycart' ); ?></div><?php
			}
			// ======= MySQL =======
			$isMySQL = extension_loaded("MySQL");
			$isMySQLi = extension_loaded("MySQLi");
			if( !$isMySQL && !$isMySQLi )
			{
			?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-warning"></div><?php _e( 'MySQL Not Installed', 'wp-easycart' ); ?></div><?php 
			} else {
			?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-yes"></div><?php _e( 'MySQL Installed', 'wp-easycart' ); ?></div><?php
			}
			// ======= Max File Upload Size =======
			?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-yes"></div>
            <?php _e( 'Max PHP File Upload Size (Recommended >10M)', 'wp-easycart' ); ?>: <?php echo ini_get("upload_max_filesize"); ?>
            </div><?php
			// ======= max_execution_time =======
			?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-yes"></div>
            <?php _e( 'Max PHP Execution Time (Recommended >300)', 'wp-easycart' ); ?>: <?php echo ini_get("max_execution_time"); ?>
            </div><?php
			// ======= memory_limit =======
			?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-yes"></div>
            <?php _e( 'PHP Memory Limit (Recommended >128M)', 'wp-easycart' ); ?>: <?php echo ini_get("memory_limit"); ?>
            </div><?php
			// ======= post_max_size =======
			?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-yes"></div>
            <?php _e( 'Max PHP Post Size (Recommended >10M)', 'wp-easycart' ); ?>: <?php echo ini_get("post_max_size"); ?>
            </div><?php
			/*// ======= Output  Buffering =======
			if (ini_get("output_buffering") == 0)
			{
			?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-warning"></div><?php _e( 'Output Buffering OFF', 'wp-easycart' ); ?></div><?php 
			} if (ini_get("output_buffering") == 1) {
			?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-yes"></div><?php _e( 'Output Buffering ON', 'wp-easycart' ); ?></div><?php
			}
			// ======= oAuth =======
			if (!class_exists( 'OAuth' ))
			{
			?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-warning"></div><?php _e( 'oAuth Not Installed', 'wp-easycart' ); ?></div><?php 
			} if (class_exists( 'OAuth' )) {
			?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-yes"></div><?php _e( 'oAuth Installed', 'wp-easycart' ); ?></div><?php
			}*/
			// ======= create directory =======
			$to = dirname( __FILE__ ) . "/../../../../testfolder/";
			$success = mkdir( $to, 0777 );
			if( !$success ){
			?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-warning"></div><?php _e( 'Failed to Create Directories', 'wp-easycart' ); ?></div><?php  
			} else {
			?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-yes"></div><?php _e( 'Success Creating Directories', 'wp-easycart' ); ?></div><?php
			}
			// ======= remove directory =======
			if ($success) {
				$to = dirname( __FILE__ ) . "/../../../../testfolder/";
				$remove = rmdir( $to );
				if( !$remove ){
				?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-warning"></div><?php _e( 'Failed to Remove Directories', 'wp-easycart' ); ?></div><?php 
				} else {
				?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-yes"></div><?php _e( 'Success Removing Directories', 'wp-easycart' ); ?></div><?php
				}
			} else {
			?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-warning"></div><?php _e( 'Failed to Remove Directories', 'wp-easycart' ); ?></div><?php 
			}
			
			// ======= test file write to plugins directory =======
			$ec_test_php = 'test file write'; 
		
			$ec_test_filename = dirname( __FILE__ ) . "/../../../../../testfile.php";
			$ec_test_filehandler = fopen($ec_test_filename, 'w');
			if(!$ec_test_filehandler) {
			?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-warning"></div><?php _e( 'Failed to Open File in Plugin Directory', 'wp-easycart' ); ?></div><?php 
			} else {
				if(!fwrite($ec_test_filehandler, $ec_test_php)) {
				?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-warning"></div><?php _e( 'Failed to Write File to Plugin Directory', 'wp-easycart' ); ?></div><?php 
				} else {
					if(!fclose($ec_test_filehandler)) {
						?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-warning"></div><?php _e( 'Failed to Close File in Plugin Directory', 'wp-easycart' ); ?></div><?php 
						unlink($ec_test_filename);
					} else {
						?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-yes"></div><?php _e( 'Success in Writing Files to Plugin Directory', 'wp-easycart' ); ?></div><?php
						unlink($ec_test_filename);
					}
				}
			}
            
            // ======= permalinks test =======
			if( strstr( get_option( 'permalink_structure' ), '%postname%' ) === FALSE ) {?>
                <div class="ec_status_subtitles"><div class="dashicons-before dashicons-warning"></div><?php _e( 'Not using Post Name permalinks, which could have negative effects on your store. Edit this in your Settings -> Permalinks', 'wp-easycart' ); ?></div><?php 
			} else {?>
				<div class="ec_status_subtitles"><div class="dashicons-before dashicons-yes"></div><?php _e( 'Post Name permalinks in use', 'wp-easycart' ); ?></div><?php
			}
			
			// ======= test file write to easycart plugin directory =======
			$ec_test_php = 'test file write'; 
		
			$ec_test_filename = dirname( __FILE__ ) . "/../../../../testfile.php";
			$ec_test_filehandler = fopen($ec_test_filename, 'w');
			if(!$ec_test_filehandler) {
			?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-warning"></div><?php _e( 'Failed to Open File in EasyCart Directory', 'wp-easycart' ); ?></div><?php 
			} else {
				if(!fwrite($ec_test_filehandler, $ec_test_php)) {
				?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-warning"></div><?php _e( 'Failed to Write File to EasyCart Directory', 'wp-easycart' ); ?></div><?php 
				} else {
					if(!fclose($ec_test_filehandler)) {
						?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-warning"></div><?php _e( 'Failed to Close File in EasyCart Directory', 'wp-easycart' ); ?></div><?php 
						unlink($ec_test_filename);
					} else {
						?><div class="ec_status_subtitles"><div class="dashicons-before dashicons-yes"></div><?php _e( 'Success in Writing Files to EasyCart Directory', 'wp-easycart' ); ?></div><?php
						unlink($ec_test_filename);
					}
				}
			}
            
			// ======= simpleXMLElement =======
            if( !class_exists( 'SimpleXMLElement' ) ){
                $using_live_shipping_with_sxml = false;
                if( $status->ec_using_live_shipping( ) && $status->ec_using_canadapost_shipping( ) && $status->ec_canadapost_shipping_setup( ) ){
                    $using_live_shipping_with_sxml = true;
                }else if( $status->ec_using_live_shipping( ) && $status->ec_using_dhl_shipping( ) && $status->ec_dhl_shipping_setup( ) ){
                    $using_live_shipping_with_sxml = true;
                }else if( $status->ec_using_live_shipping( ) && $status->ec_using_usps_shipping( ) && $status->ec_usps_shipping_setup( ) ){
                    $using_live_shipping_with_sxml = true;
                }else if( $status->ec_using_live_shipping( ) && $status->ec_using_ups_shipping( ) && $status->ec_ups_shipping_setup( ) ){
                    $using_live_shipping_with_sxml = true;
                }
                
                if( $using_live_shipping_with_sxml ){ ?>
                <div class="ec_status_subtitles"><div class="dashicons-before dashicons-warning"></div><?php _e( 'Your live shipping setup requires the PHP extension SimpleXMLElement to function correctly. Please contact your host to enable this feature.', 'wp-easycart' ); ?></div>   
                <?php }else{ ?>
                <div class="ec_status_subtitles"><div class="dashicons-before dashicons-yes"></div><?php _e( 'Some live shipping requires the SimpleXMLElement php extension. Currently this is not a problem, but if you add one of the live shippers that requires this, you may have problems.', 'wp-easycart' ); ?></div>
                <?php }
            }else{ ?>
                <div class="ec_status_subtitles"><div class="dashicons-before dashicons-yes"></div><?php _e( 'SimpleXMLElement installed.', 'wp-easycart' ); ?></div>
            <?php }
		?>
        </div>
        <?php
        ///////////////////////////////////////////////
        // EasyCart Status Section
        ///////////////////////////////////////////////
        ?>
        <div class="ec_status_header"><div class="ec_status_header_text"><?php _e( 'EasyCart Setup Status', 'wp-easycart' ); ?> - <a href="http://docs.wpeasycart.com/wp-easycart-installation-guide/" target="_blank"><?php _e( 'Click Here', 'wp-easycart' ); ?></a> <?php _e( 'for our Installation Guide', 'wp-easycart' ); ?></div></div>
        <?php
        ////////////////////////////
        // Data Folder Check
        ////////////////////////////
        if( $status->wpeasycart_is_data_folder_setup( ) ){ ?>
        <div class="ec_status_success"><div class="dashicons-before dashicons-yes"></div><span class="ec_status_label"><?php _e( 'Data Folders Setup Correctly', 'wp-easycart' ); ?></span></div>
        <?php }else{ ?>
        <div class="ec_status_error"><div class="dashicons-before dashicons-no"></div><span class="ec_status_label"><a href="admin.php?page=wp-easycart-status&subpage=store-status&ec_action=fix_data_folders"><?php _e( 'Fix Errors', 'wp-easycart' ); ?></a> <?php echo $status->ec_get_data_folders_error( ); ?></span></div>
        <?php } ?>
        
        <?php
        ////////////////////////////
        // Store Page Setup Check
        ////////////////////////////
        if( $status->ec_is_store_page_setup( ) ){ ?>
        <div class="ec_status_success"><div class="dashicons-before dashicons-yes"></div><span class="ec_status_label"><?php _e( 'Store Page Setup &amp; Connected Correctly', 'wp-easycart' ); ?></span></div>
        <?php }else{ ?>
        <div class="ec_status_error"><div class="dashicons-before dashicons-no"></div><span class="ec_status_label"><?php echo $status->ec_get_store_page_error( ); ?></span></div>
        <?php } ?>
        
        <?php
        ////////////////////////////
        // Cart Page Setup Check
        ////////////////////////////
        if( $status->ec_is_cart_page_setup( ) ){ ?>
        <div class="ec_status_success"><div class="dashicons-before dashicons-yes"></div><span class="ec_status_label"><?php _e( 'Cart Page Setup &amp; Connected Correctly', 'wp-easycart' ); ?></span></div>
        <?php }else{ ?>
        <div class="ec_status_error"><div class="dashicons-before dashicons-no"></div><span class="ec_status_label"><?php echo $status->ec_get_cart_page_error( ); ?></span></div>
        <?php } ?>
        
        <?php
        ////////////////////////////
        // Account Page Setup Check
        ////////////////////////////
        if( $status->ec_is_account_page_setup( ) ){ ?>
        <div class="ec_status_success"><div class="dashicons-before dashicons-yes"></div><span class="ec_status_label"><?php _e( 'Account Page Setup &amp; Connected Correctly', 'wp-easycart' ); ?></span></div>
        <?php }else{ ?>
        <div class="ec_status_error"><div class="dashicons-before dashicons-no"></div><span class="ec_status_label"><?php echo $status->ec_get_account_page_error( ); ?></span></div>
        <?php } ?>
        
        <?php
        ///////////////////////////////////////////////
        // Shipping Status Section
        ///////////////////////////////////////////////
        ?>
        <div class="ec_status_header"><div class="ec_status_header_text"><?php _e( 'Shipping Status', 'wp-easycart' ); ?> - <a href="http://docs.wpeasycart.com/wp-easycart-administrative-console-guide/?section=shipping-rates" target="_blank"><?php _e( 'Click Here', 'wp-easycart' ); ?></a> <?php _e( 'for Shipping Setup Help', 'wp-easycart' ); ?></div></div>
        <?php
		
		////////////////////////////
        // No Shipping Check
        ////////////////////////////
        if( $status->ec_using_method_shipping( ) == false && $status->ec_using_live_shipping( ) == false && $status->ec_using_price_shipping( ) == false && $status->ec_using_weight_shipping( ) == false && $status->ec_using_quantity_shipping( ) == false && $status->ec_using_percentage_shipping( ) == false && $status->ec_using_fraktjakt_shipping( ) == false){ ?>
        <div class="ec_status_error"><div class="dashicons-before dashicons-no"></div><span class="ec_status_label"><?php _e( 'No shipping methods have been setup at this time.', 'wp-easycart' ); ?></span></div>
        <?php }
		
		
        
		////////////////////////////
        // Price Based Shipping Check
        ////////////////////////////
        if( $status->ec_using_price_shipping( ) && $status->ec_price_shipping_setup( )  ){ ?>
        <div class="ec_status_success"><div class="dashicons-before dashicons-yes"></div><span class="ec_status_label"><?php _e( 'You have successfully setup price based shipping.', 'wp-easycart' ); ?></span></div>
        <?php }else if( $status->ec_using_price_shipping( ) ){ ?>
        <div class="ec_status_error"><div class="dashicons-before dashicons-no"></div><span class="ec_status_label"><?php _e( 'You have chosen to use price based shipping, but there doesn\'t appear to be any price triggers setup.', 'wp-easycart' ); ?></span></div>
        <?php }
        
        ////////////////////////////
        // Weight Based Shipping Check
        ////////////////////////////
        if( $status->ec_using_weight_shipping( ) && $status->ec_weight_shipping_setup( ) ){ ?>
        <div class="ec_status_success"><div class="dashicons-before dashicons-yes"></div><span class="ec_status_label"><?php _e( 'You have successfully setup weight based shipping.', 'wp-easycart' ); ?></span></div>
        <?php }else if( $status->ec_using_weight_shipping( ) ){ ?>
        <div class="ec_status_error"><div class="dashicons-before dashicons-no"></div><span class="ec_status_label"><?php _e( 'You have chosen to use weight based shipping, but there doesn\'t appear to be any weight triggers setup.', 'wp-easycart' ); ?></span></div>
        <?php }
        
        ////////////////////////////
        // Quantity Based Shipping Check
        ////////////////////////////
        if( $status->ec_using_quantity_shipping( ) && $status->ec_quantity_shipping_setup( ) ){ ?>
        <div class="ec_status_success"><div class="dashicons-before dashicons-yes"></div><span class="ec_status_label"><?php _e( 'You have successfully setup quantity based shipping.', 'wp-easycart' ); ?></span></div>
        <?php }else if( $status->ec_using_quantity_shipping( ) ){ ?>
        <div class="ec_status_error"><div class="dashicons-before dashicons-no"></div><span class="ec_status_label"><?php _e( 'You have chosen to use quantity based shipping, but there doesn\'t appear to be any quantity triggers setup.', 'wp-easycart' ); ?></span></div>
        <?php }
        
        ////////////////////////////
        // Percentage Based Shipping Check
        ////////////////////////////
        if( $status->ec_using_percentage_shipping( ) && $status->ec_percentage_shipping_setup( ) ){ ?>
        <div class="ec_status_success"><div class="dashicons-before dashicons-yes"></div><span class="ec_status_label"><?php _e( 'You have successfully setup percentage based shipping.', 'wp-easycart' ); ?></span></div>
        <?php }else if( $status->ec_using_percentage_shipping( ) ){ ?>
        <div class="ec_status_error"><div class="dashicons-before dashicons-no"></div><span class="ec_status_label"><?php _e( 'You have chosen to use percentage based shipping, but there doesn\'t appear to be any percentage triggers setup.', 'wp-easycart' ); ?></span></div>
        <?php }
        
        ////////////////////////////
        // Method Based Shipping Check
        ////////////////////////////
        if( $status->ec_using_method_shipping( ) && $status->ec_method_shipping_setup( ) ){ ?>
        <div class="ec_status_success"><div class="dashicons-before dashicons-yes"></div><span class="ec_status_label"><?php _e( 'You have successfully setup method based shipping.', 'wp-easycart' ); ?></span></div>
        <?php }else if( $status->ec_using_method_shipping( ) ){ ?>
        <div class="ec_status_error"><div class="dashicons-before dashicons-no"></div><span class="ec_status_label"><?php _e( 'You have chosen to use method based shipping, but there doesn\'t appear to be any method triggers setup.', 'wp-easycart' ); ?></span></div>
        <?php }
        
        ////////////////////////////
        // Live Based Shipping Check
        ////////////////////////////
        if( $status->ec_using_live_shipping( ) && !$status->ec_live_shipping_setup( ) ){ ?>
        <div class="ec_status_error"><div class="dashicons-before dashicons-no"></div><span class="ec_status_label"><?php _e( 'You have live shipping selected, but no rates are setup.', 'wp-easycart' ); ?></span></div>
        <?php }
        
        ////////////////////////////
        // UPS Shipping Check
        ////////////////////////////
        if( $status->ec_using_live_shipping( ) && $status->ec_using_ups_shipping( ) && $status->ec_ups_shipping_setup( ) ){ ?>
        <div class="ec_status_success"><div class="dashicons-before dashicons-yes"></div><span class="ec_status_label"><?php _e( 'You have successfully setup UPS live shipping.', 'wp-easycart' ); ?></span></div>
        <?php }else if( $status->ec_using_live_shipping( ) && $status->ec_using_ups_shipping( ) ){ ?>
        <div class="ec_status_error"><div class="dashicons-before dashicons-no"></div><span class="ec_status_label"><?php _e( 'UPS live shipping setup incorrectly.', 'wp-easycart' ); ?></span></div>
        <?php }
        
        ////////////////////////////
        // USPS Shipping Check
        ////////////////////////////
        if( $status->ec_using_live_shipping( ) && $status->ec_using_usps_shipping( ) && $status->ec_usps_shipping_setup( ) ){ ?>
        <div class="ec_status_success"><div class="dashicons-before dashicons-yes"></div><span class="ec_status_label"><?php _e( 'You have successfully setup USPS live shipping.', 'wp-easycart' ); ?></span></div>
        <?php }else if( $status->ec_using_live_shipping( ) && $status->ec_using_usps_shipping( ) ){ ?>
        <div class="ec_status_error"><div class="dashicons-before dashicons-no"></div><span class="ec_status_label"><?php _e( 'USPS live shipping setup incorrectly.', 'wp-easycart' ); ?></span></div>
        <?php }
        
        ////////////////////////////
        // FEDEX Shipping Check
        ////////////////////////////
        if( $status->ec_using_live_shipping( ) && $status->ec_using_fedex_shipping( ) && $status->ec_fedex_shipping_setup( ) ){ ?>
        <div class="ec_status_success"><div class="dashicons-before dashicons-yes"></div><span class="ec_status_label"><?php _e( 'You have successfully setup FedEx live shipping.', 'wp-easycart' ); ?></span></div>
        <?php }else if( $status->ec_using_live_shipping( ) && $status->ec_using_fedex_shipping( ) ){ ?>
        <div class="ec_status_error"><div class="dashicons-before dashicons-no"></div><span class="ec_status_label"><?php _e( 'FedEx live shipping setup incorrectly.', 'wp-easycart' ); ?></span></div>
        <?php }
        
        ////////////////////////////
        // DHL Shipping Check
        ////////////////////////////
        if( $status->ec_using_live_shipping( ) && $status->ec_using_dhl_shipping( ) && $status->ec_dhl_shipping_setup( ) ){ ?>
        <div class="ec_status_success"><div class="dashicons-before dashicons-yes"></div><span class="ec_status_label"><?php _e( 'You have successfully setup DHL live shipping.', 'wp-easycart' ); ?></span></div>
        <?php }else if( $status->ec_using_live_shipping( ) && $status->ec_using_dhl_shipping( ) ){ ?>
        <div class="ec_status_error"><div class="dashicons-before dashicons-no"></div><span class="ec_status_label"><?php _e( 'DHL live shipping setup incorrectly.', 'wp-easycart' ); ?></span></div>
        <?php }
        
        ////////////////////////////
        // AUSPOST Shipping Check
        ////////////////////////////
        if( $status->ec_using_live_shipping( ) && $status->ec_using_auspost_shipping( ) && $status->ec_auspost_shipping_setup( ) ){ ?>
        <div class="ec_status_success"><div class="dashicons-before dashicons-yes"></div><span class="ec_status_label"><?php _e( 'You have successfully setup Australia Post live shipping.', 'wp-easycart' ); ?></span></div>
        <?php }else if( $status->ec_using_live_shipping( ) && $status->ec_using_auspost_shipping( ) ){ ?>
        <div class="ec_status_error"><div class="dashicons-before dashicons-no"></div><span class="ec_status_label"><?php _e( 'Australia Post live shipping setup incorrectly.', 'wp-easycart' ); ?></span></div>
        <?php } 
        
        ////////////////////////////
        // Canada Post Shipping Check
        ////////////////////////////
        if( $status->ec_using_live_shipping( ) && $status->ec_using_canadapost_shipping( ) && $status->ec_canadapost_shipping_setup( ) ){ ?>
        <div class="ec_status_success"><div class="dashicons-before dashicons-yes"></div><span class="ec_status_label"><?php _e( 'You have successfully setup Canada Post live shipping.', 'wp-easycart' ); ?></span></div>
        <?php }else if( $status->ec_using_live_shipping( ) && $status->ec_using_canadapost_shipping( ) ){ ?>
        <div class="ec_status_error"><div class="dashicons-before dashicons-no"></div><span class="ec_status_label"><?php _e( 'Canada Post live shipping setup incorrectly.', 'wp-easycart' ); ?></span></div>
        <?php } 
        
        ////////////////////////////
        // Fraktjakt Shipping Check
        ////////////////////////////
        if( $status->ec_using_fraktjakt_shipping( ) && $status->ec_fraktjakt_shipping_setup( ) ){ ?>
        <div class="ec_status_success"><div class="dashicons-before dashicons-yes"></div><span class="ec_status_label"><?php _e( 'You have successfully setup Fraktjakt live shipping.', 'wp-easycart' ); ?></span></div>
        <?php }else if( $status->ec_using_fraktjakt_shipping( ) ){ ?>
        <div class="ec_status_error"><div class="dashicons-before dashicons-no"></div><span class="ec_status_label"><?php _e( 'Fraktjakt live shipping is setup incorrectly.', 'wp-easycart' ); ?></span></div>
        <?php } 
		
        ///////////////////////////////////////////////
        // Tax Status Section
        ///////////////////////////////////////////////
        ?>
        
        <div class="ec_status_header"><div class="ec_status_header_text"><?php _e( 'Tax Status', 'wp-easycart' ); ?> - <a href="http://docs.wpeasycart.com/wp-easycart-administrative-console-guide/?section=taxes" target="_blank"><?php _e( 'Click Here', 'wp-easycart' ); ?></a> <?php _e( 'for Tax Setup Help', 'wp-easycart' ); ?></div></div>
        
        <?php 
        ////////////////////////////
        // No Tax Check
        ////////////////////////////
        if( $status->ec_using_no_tax( ) ){ ?>
        <div class="ec_status_success"><div class="dashicons-before dashicons-yes"></div><span class="ec_status_label"><?php _e( 'You are setup to use no tax structure, this can be changed in the Store Admin -> Rates -> Tax Rates panel.', 'wp-easycart' ); ?></span></div>
        <?php }
        
        ////////////////////////////
        // State Tax Check
        ////////////////////////////
        if( $status->ec_using_state_tax( ) ){ ?>
        <div class="ec_status_success"><div class="dashicons-before dashicons-yes"></div><span class="ec_status_label"><?php _e( 'You have successfully configured state/province taxes.', 'wp-easycart' ); ?></span></div>
        <?php }
        
        ////////////////////////////
        // Country Tax Check
        ////////////////////////////
        if( $status->ec_using_country_tax( ) ){ ?>
        <div class="ec_status_success"><div class="dashicons-before dashicons-yes"></div><span class="ec_status_label"><?php _e( 'You have successfully configured country taxes.', 'wp-easycart' ); ?></span></div>
        <?php }
        
        ////////////////////////////
        // Gloabl Tax Check
        ////////////////////////////
        if( $status->ec_using_global_tax( ) ){ ?>
        <div class="ec_status_success"><div class="dashicons-before dashicons-yes"></div><span class="ec_status_label"><?php _e( 'You have successfully configured global taxes.', 'wp-easycart' ); ?></span></div>
        <?php }
        
        ////////////////////////////
        // Duty Tax Check
        ////////////////////////////
        if( $status->ec_using_duty_tax( ) ){ ?>
        <div class="ec_status_success"><div class="dashicons-before dashicons-yes"></div><span class="ec_status_label"><?php _e( 'You have successfully configured customs duty or export taxes.', 'wp-easycart' ); ?></span></div>
        <?php }
        
        ////////////////////////////
        // VAT Tax Check
        ////////////////////////////
        if( $status->ec_using_vat_tax( ) && $status->ec_global_vat_setup( ) ){ ?>
        <div class="ec_status_success"><div class="dashicons-before dashicons-yes"></div><span class="ec_status_label"><?php _e( 'You have successfully configured VAT.', 'wp-easycart' ); ?></span></div>
        <?php }else if( $status->ec_using_vat_tax( ) ){ ?>
        <div class="ec_status_error"><div class="dashicons-before dashicons-no"></div><span class="ec_status_label"><?php _e( 'You have selected to use VAT, but have not entered a rate and/or have not set any individual country rates.', 'wp-easycart' ); ?></span></div>
        <?php } ?>
        
        
        
        
        <div class="ec_status_header">
            <div class="ec_status_header_text"><?php _e( 'Payment Status', 'wp-easycart' ); ?> - 
            	<a href="http://docs.wpeasycart.com/wp-easycart-administrative-console-guide/?section=payment" target="_blank"><?php _e( 'Click Here', 'wp-easycart' ); ?></a> <?php _e( 'for Payment Setup Help', 'wp-easycart' ); ?>
            </div>
        </div>
        <?php

		///////////////////////////////////////////////
        // Payment Status Section
        ///////////////////////////////////////////////
        
        ////////////////////////////
        // No Payment Type Selected Check
        ////////////////////////////
        if( $status->ec_no_payment_selected( ) ){ ?>
        <div class="ec_status_error"><div class="dashicons-before dashicons-no"></div><span class="ec_status_label"><?php _e( 'You have not selected a payment method, the checkout process cannot be completed by your customers at this time.', 'wp-easycart' ); ?></span></div>
        <?php } 
        
        ////////////////////////////
        // Manual Payment Type Selected Check
        ////////////////////////////
        if( $status->ec_manual_payment_selected( ) ){ ?>
        <div class="ec_status_success"><div class="dashicons-before dashicons-yes"></div><span class="ec_status_label"><?php _e( 'You have setup your store to use manual payment. This method requires your customer to send a check or direct deposit before shipping.', 'wp-easycart' ); ?></span></div>
        <?php } 
        
        ////////////////////////////
        // Third Party Payment Type Selected Check
        ////////////////////////////
        if( $status->ec_third_party_payment_selected( ) && $status->ec_third_party_payment_setup( ) ){ ?>
        <div class="ec_status_success"><div class="dashicons-before dashicons-yes"></div><span class="ec_status_label"><?php echo sprintf( __( 'You have selected to use %s as a third party payment method and you have entered all necessary info.', 'wp-easycart' ), $status->ec_get_third_party_method( ) ); ?></span></div>
        <?php }else if( $status->ec_third_party_payment_selected( ) ){ ?>
        <div class="ec_status_error"><div class="dashicons-before dashicons-no"></div><span class="ec_status_label"><?php echo sprintf( __( 'You have selected %s, but have missed some necessary info. Go to WP EasyCart -> Settings -> Payment to resolve this.', 'wp-easycart' ), $status->ec_get_third_party_method( ) ); ?></span></div>
        <?php } 
        
        ////////////////////////////
        // Live Payment Type Selected Check
        ////////////////////////////
        if( $status->ec_live_payment_selected( ) && $status->ec_live_payment_setup( ) ){ ?>
        <div class="ec_status_success"><div class="dashicons-before dashicons-yes"></div><span class="ec_status_label"><?php echo sprintf( __( 'You have selected to use %s as a live payment method and you have entered all necessary info.', 'wp-easycart' ), $status->ec_get_live_payment_method( ) ); ?></span></div>
        <?php }else if( $status->ec_live_payment_selected( ) ){ ?>
        <div class="ec_status_error"><div class="dashicons-before dashicons-no"></div><span class="ec_status_label"><?php echo sprintf( __( 'You have selected %s, but have missed some necessary info. Go to WP EasyCart -> Settings -> Payment to resolve this.', 'wp-easycart' ), $status->ec_get_live_payment_method( ) ); ?></span></div>
        <?php } 
        
        ////////////////////////////
        // MISCELLANEOUS
        ////////////////////////////
		?>
        <div class="ec_status_header"><div class="ec_status_header_text"><?php _e( 'Miscellaneous', 'wp-easycart' ); ?></div></div>
        <?php
        ////////////////////////////
        // Provide fix for custom post type links
        ////////////////////////////
        ?>
        
        <div class="ec_status_success"><div class="dashicons-before dashicons-yes"></div><span class="ec_status_label"><?php _e( 'If you are having problems with store links', 'wp-easycart' ); ?>, <a href="admin.php?page=wp-easycart-status&subpage=store-status&ec_action=reset_store_permalinks"><?php _e( 'reset permalinks', 'wp-easycart' ); ?></a> | <a href="admin.php?page=wp-easycart-status&subpage=store-status&ec_action=reset_store_permalinks&ec_reset_phase2=true"><?php _e( 'rebuild permalinks', 'wp-easycart' ); ?></a> | <a href="admin.php?page=wp-easycart-status&subpage=store-status&ec_action=fix_category_permalinks"><?php _e( 'Fix Category Permalink Issues', 'wp-easycart' ); ?></a> | <a href="admin.php?page=wp-easycart-status&subpage=store-status&ec_action=fix_product_permalinks"><?php _e( 'Fix Product Permalink Issues', 'wp-easycart' ); ?></a></span></div>
        
    </div>
</div>