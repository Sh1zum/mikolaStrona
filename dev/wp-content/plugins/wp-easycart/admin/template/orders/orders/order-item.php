<?php
$advanced_options = $wpdb->get_results( $wpdb->prepare( "SELECT ec_order_option.* FROM ec_order_option WHERE ec_order_option.orderdetail_id = %s ORDER BY order_option_id", $line_item->orderdetail_id ));
if( $advanced_options ){
    $order_detail_row = $wpdb->get_row( $wpdb->prepare( "SELECT ec_orderdetail.is_deconetwork, ec_orderdetail.deconetwork_id, ec_orderdetail.deconetwork_name, ec_orderdetail.deconetwork_product_code, ec_orderdetail.deconetwork_options, ec_orderdetail.deconetwork_color_code, ec_orderdetail.product_id, ec_orderdetail.deconetwork_image_link FROM ec_orderdetail WHERE ec_orderdetail.orderdetail_id = %d", $line_item->orderdetail_id ) );
    if( $order_detail_row !== false && $order_detail_row->is_deconetwork ){
        $deconetwork1 = new stdClass( );
        $deconetwork1->orderdetail_id = $advanced_options->orderdetail_id;
        $deconetwork1->option_name = __( 'DecoNetwork ID', 'wp-easycart' ) . ': ';
        $deconetwork1->optionitem_name = "";
        $deconetwork1->option_type = "text";
        $deconetwork1->option_value = $order_detail_row->deconetwork_id;
        $deconetwork1->option_price_change = "";
        
        $advanced_options[] = $deconetwork1;
        
        $deconetwork2 = new stdClass( );
        $deconetwork2->option_name = __( 'DecoNetwork Name', 'wp-easycart' ) . ': ';
        $deconetwork2->optionitem_name = "";
        $deconetwork2->option_type = "text";
        $deconetwork2->option_value =  $order_detail_row->deconetwork_name;
        $deconetwork2->option_price_change = "";
        $advanced_options[] = $deconetwork2;
        
        $deconetwork3 = new stdClass( );
        $deconetwork3->option_name = __( 'DecoNetwork Product Code', 'wp-easycart' ) . ': ';
        $deconetwork3->optionitem_name = "";
        $deconetwork3->option_type = "text";
        $deconetwork3->option_value = $order_detail_row->deconetwork_product_code;
        $deconetwork3->option_price_change = "";
        $advanced_options[] = $deconetwork3;
        
        $deconetwork4 = new stdClass( );
        $deconetwork4->option_name = __( 'DecoNetwork Options', 'wp-easycart' ) . ': ';
        $deconetwork4->optionitem_name = "";
        $deconetwork4->option_type = "text";
        $deconetwork4->option_value = $order_detail_row->deconetwork_options;
        $deconetwork4->option_price_change = "";
        $advanced_options[] = $deconetwork4;
        
        $deconetwork5 = new stdClass( );
        $deconetwork5->option_name = __( 'DecoNetwork Color Code', 'wp-easycart' ) . ': ';
        $deconetwork5->optionitem_name = "";
        $deconetwork5->option_type = "text";
        $deconetwork5->option_value = $order_detail_row->deconetwork_color_code;
        $deconetwork5->option_price_change = "";
        $advanced_options[] = $deconetwork5;
        
        $deconetwork6 = new stdClass( );
        $deconetwork6->option_name = __( 'DecoNetwork Image Link', 'wp-easycart' ) . ': ';
        $deconetwork6->optionitem_name = "";
        $deconetwork6->option_type = "text";
        $deconetwork6->option_value = $order_detail_row->deconetwork_image_link;
        $deconetwork6->option_price_change = "";
        $advanced_options[] = $deconetwork6;
    }
} ?>
<div class="ec_admin_order_details_line_item" id="ec_admin_order_details_line_item_<?php echo $line_item->orderdetail_id; ?>">
    
    <div class="ec_admin_order_details_item_actions">
        <?php $delete_line_action = apply_filters( 'wp_easycart_admin_order_details_delete_line_action', 'show_pro_required' ); ?>
        <?php $edit_line_action = apply_filters( 'wp_easycart_admin_order_details_edit_line_action', 'show_pro_required' ); ?>
        <div class="dashicons-before dashicons-trash" onclick="<?php echo $delete_line_action; ?>( '<?php echo $line_item->orderdetail_id; ?>' ); return false;"></div>
        <div class="dashicons-before dashicons-edit" onclick="<?php echo $edit_line_action; ?>( '<?php echo $line_item->orderdetail_id; ?>' ); return false;" id="ec_admin_order_line_edit_<?php echo $line_item->orderdetail_id; ?>"></div>
    </div>
    <div class="ec_admin_order_details_item_details">
        <span id="ec_admin_order_details_item_title_display_<?php echo $line_item->orderdetail_id; ?>"><?php echo htmlentities( stripslashes( $line_item->title ), ENT_NOQUOTES );?></span>
        <div class="ec_details_option_label">SKU:</div> <div class="ec_details_option_value" id="ec_admin_order_details_item_model_number_display_<?php echo $line_item->orderdetail_id; ?>"><?php echo htmlentities( stripslashes( $line_item->model_number ), ENT_NOQUOTES );?></div>
        <?php
        if( $line_item->optionitem_label_1 || $line_item->optionitem_name_1 ){
            if( $line_item->optionitem_label_1 )
                echo '<div class="ec_details_option_label">'.htmlentities( stripslashes( $line_item->optionitem_label_1 ), ENT_NOQUOTES ).':</div> ';
            else 
                echo '<div class="ec_details_option_label">' . __( 'Option', 'wp-easycart' ) . ' 1:</div> ';
            echo '<div class="ec_details_option_value" id="ec_admin_order_details_item_optionitem_name_1_display_' . $line_item->orderdetail_id . '"> '.htmlentities( stripslashes( $line_item->optionitem_name_1 ), ENT_NOQUOTES ).'</div>';
        }
        if( $line_item->optionitem_label_2 || $line_item->optionitem_name_2 ){
            if($line_item->optionitem_label_2)
                echo '<div class="ec_details_option_label">'.htmlentities( stripslashes( $line_item->optionitem_label_2 ), ENT_NOQUOTES ).':</div> ';
            else 
                echo '<div class="ec_details_option_label">' . __( 'Option', 'wp-easycart' ) . ' 2:</div> ';
            echo '<div class="ec_details_option_value" id="ec_admin_order_details_item_optionitem_name_2_display_' . $line_item->orderdetail_id . '"> '.htmlentities( stripslashes( $line_item->optionitem_name_2 ), ENT_NOQUOTES ).'</div>';
        }
        if( $line_item->optionitem_label_3 || $line_item->optionitem_name_3 ){
            if($line_item->optionitem_label_3)
                echo '<div class="ec_details_option_label">'.htmlentities( stripslashes( $line_item->optionitem_label_3 ), ENT_NOQUOTES ).':</div> ';
            else 
                echo '<div class="ec_details_option_label">' . __( 'Option', 'wp-easycart' ) . ' 3:</div> ';
            echo '<div class="ec_details_option_value" id="ec_admin_order_details_item_optionitem_name_3_display_' . $line_item->orderdetail_id . '"> '.htmlentities( stripslashes( $line_item->optionitem_name_3 ), ENT_NOQUOTES ).'</div>';
        }
        if( $line_item->optionitem_label_4 || $line_item->optionitem_name_4 ){
            if($line_item->optionitem_label_4)
                echo '<div class="ec_details_option_label">'.htmlentities( stripslashes( $line_item->optionitem_label_4 ), ENT_NOQUOTES ).':</div> ';
            else 
                echo '<div class="ec_details_option_label">' . __( 'Option', 'wp-easycart' ) . ' 4:</div> ';
            echo '<div class="ec_details_option_value" id="ec_admin_order_details_item_optionitem_name_4_display_' . $line_item->orderdetail_id . '"> '.htmlentities( stripslashes( $line_item->optionitem_name_4 ), ENT_NOQUOTES ).'</div>';
        }
        if( $line_item->optionitem_label_5 || $line_item->optionitem_name_5 ){
            if($line_item->optionitem_label_5)
                echo '<div class="ec_details_option_label">'.htmlentities( stripslashes( $line_item->optionitem_label_5 ), ENT_NOQUOTES ).':</div> ';
            else 
                echo '<div class="ec_details_option_label">' . __( 'Option', 'wp-easycart' ) . ' 5:</div> ';
            echo '<div class="ec_details_option_value" id="ec_admin_order_details_item_optionitem_name_5_display_' . $line_item->orderdetail_id . '"> '.htmlentities( stripslashes( $line_item->optionitem_name_5 ), ENT_NOQUOTES ).'</div>';
        }
        
        foreach( $advanced_options as $advanced_option ){
            if( $advanced_option->option_name )
                echo '<div class="ec_details_option_label">'.htmlentities( stripslashes( $advanced_option->option_name ), ENT_NOQUOTES ).':</div> ';
            else
                echo '<div class="ec_details_option_label">' . __( 'Option', 'wp-easycart' ) . ':</div> ';
            if( $advanced_option->option_type == 'file' )
				echo '<div class="ec_details_option_value"> <a href="' . plugins_url( '/wp-easycart-data/products/uploads/' . htmlentities( stripslashes( $advanced_option->option_value ), ENT_NOQUOTES ) ) . '" target="_blank">' . __( 'Download File', 'wp-easycart' ) . '</a></div>';
			else if( $advanced_option->option_type == "grid" )
				echo '<div class="ec_details_option_value"> '.htmlentities( stripslashes( $advanced_option->optionitem_name ), ENT_NOQUOTES ) . " (" . htmlentities( stripslashes( $advanced_option->option_value ), ENT_NOQUOTES ) . ")".'</div>';
			else
				echo '<div class="ec_details_option_value" id="ec_admin_order_details_item_adv_optionitem_' . $line_item->orderdetail_id . '_' . $advanced_option->order_option_id . '"> '.htmlentities( stripslashes( $advanced_option->option_value ), ENT_NOQUOTES ).'</div>';
        }
        
        if( $line_item->is_giftcard ){
            if( $line_item->giftcard_id ){
                echo '<div class="ec_details_option_label">' . __( 'Gift Card ID', 'wp-easycart' ) . ':</div> ';
                echo '<div class="ec_details_option_value"> '.htmlentities( stripslashes( $line_item->giftcard_id ), ENT_NOQUOTES ).'</div>';
            }
            if($line_item->gift_card_email) {
                echo '<div class="ec_details_option_label">' . __( 'Gift Card Send to Email', 'wp-easycart' ) . ':</div> ';
                echo '<div class="ec_details_option_value"> '.htmlentities( stripslashes( $line_item->gift_card_email ), ENT_NOQUOTES ).'</div>';
            }
            if($line_item->gift_card_to_name) {
                echo '<div class="ec_details_option_label">' . __( 'Gift Card To', 'wp-easycart' ) . ':</div> ';
                echo '<div class="ec_details_option_value"> '.htmlentities( stripslashes( $line_item->gift_card_to_name ), ENT_NOQUOTES ).'</div>';
            }
            if($line_item->gift_card_from_name) {
                echo '<div class="ec_details_option_label">' . __( 'Gift Card From', 'wp-easycart' ) . ':</div> ';
                echo '<div class="ec_details_option_value"> '.htmlentities( stripslashes( $line_item->gift_card_from_name ), ENT_NOQUOTES ).'</div>';
            }
            if($line_item->gift_card_message) {
                echo '<div class="ec_details_option_label">' . __( 'Gift Card Message', 'wp-easycart' ) . ':</div> ';
                echo '<div class="ec_details_option_value"> '.htmlentities( stripslashes( $line_item->gift_card_message ), ENT_NOQUOTES ).'</div>';
            }
            if($line_item->gift_card_email) {
                echo '<div class="ec_details_option_label">' . __( 'Manage', 'wp-easycart' ) . ':</div> ';
                echo '<div class="ec_details_option_value"><a href="#" onclick="return ec_admin_resend_giftcard('.$line_item->order_id.', '.$line_item->orderdetail_id.');">Resend Gift Card Email</a></div>';
            }
        }
        
        if( $line_item->is_download ){
            if( $line_item->is_amazon_download == 1 ){
                if( $line_item->amazon_key ){
                    echo '<div class="ec_details_option_label">' . __( 'Download File Name (S3 Server)', 'wp-easycart' ) . ':</div> ';
                    echo '<div class="ec_details_option_value"> '.htmlentities( stripslashes( $line_item->amazon_key ), ENT_NOQUOTES ).'</div>';
                }
            }else{
                if( $line_item->download_file_name ){
                    echo '<div class="ec_details_option_label">' . __( 'Download File Name (Web Server)', 'wp-easycart' ) . ':</div> ';
                    echo '<div class="ec_details_option_value"> '.htmlentities( stripslashes( $line_item->download_file_name ), ENT_NOQUOTES ).'</div>';
                }
            }
            if( $line_item->maximum_downloads_allowed ){
                echo '<div class="ec_details_option_label">' . __( 'Max Downloads Allowed', 'wp-easycart' ) . ':</div> ';
                echo '<div class="ec_details_option_value"> '.$line_item->maximum_downloads_allowed.'</div>';
            }
            if( $line_item->download_timelimit_seconds ){
                echo '<div class="ec_details_option_label">' . __( 'Download Time (seconds)', 'wp-easycart' ) . ':</div> ';
                echo '<div class="ec_details_option_value"> '.$line_item->download_timelimit_seconds.'</div>';
            }
            echo '<div><div class="ec_details_option_label">' . __( 'Unique Download ID (view manage downloads to find key)', 'wp-easycart' ) . ':</div></div>';
            echo '<div class="ec_details_option_value">';
            echo '<select class="ec_order_download_key select2" style="width:100% !important; float:left;" data-orderdetail-id="' . $line_item->orderdetail_id . '">';
            echo '<option value="0">' . __( 'Select Download', 'wp-easycart' ) . '</option>';
            if( $line_item->download_key != '0' ){
                echo '<option value="' . htmlentities( stripslashes( $line_item->download_key ), ENT_NOQUOTES ) . '" selected="selected">' . htmlentities( stripslashes( $line_item->download_key ), ENT_NOQUOTES ) . '</option>';
            }
            echo '</select>';
            echo '</div>';
        }
        
        if( $line_item->subscription_id != 0 ){
            echo '<div class="ec_details_option_label">' . __( 'Subscription ID', 'wp-easycart' ) . ' ('.$line_item->subscription_id.'):</div> ';
            echo '<div class="ec_details_option_value"><a href="admin.php?page=wp-easycart-orders&subpage=subscriptions&subscription_id='.$line_item->subscription_id.'&ec_admin_form_action=edit" target="_blank">' . __( 'View Subscription Information', 'wp-easycart' ) . '</a></div>';
        }
		
		do_action( 'wp_easycart_admin_order_details_item', $line_item );
		
        ?> 
    </div>
    <div class="ec_admin_order_details_item_price" id="ec_admin_order_details_item_price_display_<?php echo $line_item->orderdetail_id;?>"><?php echo $line_item->quantity;?><span> x </span><?php if( $GLOBALS['currency']->get_symbol_location( ) ){ echo $GLOBALS['currency']->get_symbol( ); } ?><?php echo apply_filters( 'wp_easycart_cart_item_unit_price_display', number_format( $line_item->unit_price, 2 ), $line_item->product_id );?></div>
    <div class="ec_admin_order_details_item_total" id="ec_admin_order_details_item_total_display_<?php echo $line_item->orderdetail_id;?>"><?php if( $GLOBALS['currency']->get_symbol_location( ) ){ echo $GLOBALS['currency']->get_symbol( ); } ?><?php echo number_format( $line_item->total_price, 2 );?></div>
	<?php do_action( 'wp_easycart_admin_order_details_line_item_end', $line_item ); ?>
</div>