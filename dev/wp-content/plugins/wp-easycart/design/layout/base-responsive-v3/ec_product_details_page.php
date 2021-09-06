<?php
do_action( 'wp_easycart_product_details_before', $this->product );
if( trim( get_option( 'ec_option_fb_pixel' ) ) != '' ){
	echo "<script>
			fbq('track', 'ViewContent', {
				content_name: '" . str_replace( "'", "\'", $this->product->title ) . "',
				content_ids: ['" . $this->product->product_id . "'],
				content_type: 'product',
				value: " . number_format( $this->product->price, 2, '.', '' ) . ",
				currency: '" . $GLOBALS['currency']->get_currency_code( ) . "',
			});
		</script>";
}
?>
<?php
// Check for iPhone/iPad/Admin
$ipad = (bool) strpos($_SERVER['HTTP_USER_AGENT'],'iPad');
$iphone = (bool) strpos($_SERVER['HTTP_USER_AGENT'],'iPhone');

$is_admin = ( current_user_can( 'manage_options' ) && !get_option( 'ec_option_hide_live_editor' ) );

if( isset( $_GET['preview'] ) ){
	$is_preview = true;
}else{
	$is_preview = false;
}

if( isset( $_GET['previewholder'] ) )
	$is_preview_holder = true;
else
	$is_preview_holder = false;
	
// END CHECK // 

/* PREVIEW CONTENT */
if( $is_preview_holder && $is_admin && !$GLOBALS['ec_admin_details_loaded_previously'] ){ ?>

<div class="ec_admin_preview_container" id="ec_admin_preview_container">
	<div class="ec_admin_preview_content">
    	<div class="ec_admin_preview_button_container">
            <div class="ec_admin_preview_ipad_landscape"><input type="button" onclick="ec_admin_ipad_landscape_preview( );" value="iPad Landscape"></div>
            <div class="ec_admin_preview_ipad_portrait"><input type="button" onclick="ec_admin_ipad_portrait_preview( );" value="iPad Portrait"></div>
            <div class="ec_admin_preview_iphone_landscape"><input type="button" onclick="ec_admin_iphone_landscape_preview( );" value="iPhone Landscape"></div>
            <div class="ec_admin_preview_iphone_portrait"><input type="button" onclick="ec_admin_iphone_portrait_preview( );" value="iPhone Portrait"></div>
        </div>
		<div id="ec_admin_preview_content" class="ec_admin_preview_wrapper ipad landscape">
			<iframe src="<?php the_permalink( ); ?>?model_number=<?php echo $this->product->model_number; ?>&amp;preview=true" width="100%" height="100%" id="ec_admin_preview_iframe"></iframe>
		</div>
	</div>
</div><?php }else if( $is_admin && !$is_preview && ( !isset( $GLOBALS['ec_live_editor_loaded'] ) ) ){ 

$GLOBALS['ec_live_editor_loaded'] = "loaded";

?>
<div class="ec_admin_successfully_update_container" id="ec_admin_page_updated">
	<div class="ec_admin_successfully_updated">
    	<div>Your Page Settings Have Been Updated Successfully. The Page Will Now Reload.</div>
    </div>
</div>  
<div class="ec_admin_loader_container" id="ec_admin_page_updated_loader">
	<div class="ec_admin_loader">
    	<div>Updating Your Page Options...</div>
    </div>
</div>
<div class="ec_admin_loader_bg" id="ec_admin_loader_bg"></div>
<div id="ec_page_editor" class="ec_slideout_editor ec_display_editor_false ec_details_editor">
	<div id="ec_page_editor_openclose_button" class="ec_slideout_openclose" data-post-id="<?php global $post; echo $post->ID; ?>">
    	<div class="dashicons dashicons-admin-generic"></div>
    </div>
    <div class="ec_admin_preview_button"><a href="<?php the_permalink( ); ?>?model_number=<?php echo $this->product->model_number; ?>&amp;previewholder=true" target="_blank">Show Device Preview</a></div>
    <div class="ec_admin_page_size">Product Details Options</div>
    <div><strong>Main Content Color</strong></div>
    <div><input id="ec_option_details_main_color" type="color" value="<?php echo get_option( 'ec_option_details_main_color' ); ?>" /></div>
    <div><strong>Desktop Columns</strong></div>
    <div><select id="ec_option_details_columns_desktop">
    		<option value="0"<?php if( get_option( 'ec_option_details_columns_desktop' ) == "" ){?> selected="selected"<?php }?>>Select One</option>
            <option value="1"<?php if( get_option( 'ec_option_details_columns_desktop' ) == "1" ){?> selected="selected"<?php }?>>1 Column</option>
            <option value="2"<?php if( get_option( 'ec_option_details_columns_desktop' ) == "2" ){?> selected="selected"<?php }?>>2 Columns</option>
    </select></div>
    <div><strong>Tablet Landscape Columns</strong></div>
    <div><select id="ec_option_details_columns_laptop">
    		<option value="0"<?php if( get_option( 'ec_option_details_columns_laptop' ) == "" ){?> selected="selected"<?php }?>>Select One</option>
            <option value="1"<?php if( get_option( 'ec_option_details_columns_laptop' ) == "1" ){?> selected="selected"<?php }?>>1 Column</option>
            <option value="2"<?php if( get_option( 'ec_option_details_columns_laptop' ) == "2" ){?> selected="selected"<?php }?>>2 Columns</option>
    </select></div>
    <div><strong>Tablet Portrait Columns</strong></div>
    <div><select id="ec_option_details_columns_tablet_wide">
    		<option value="0"<?php if( get_option( 'ec_option_details_columns_tablet_wide' ) == "" ){?> selected="selected"<?php }?>>Select One</option>
            <option value="1"<?php if( get_option( 'ec_option_details_columns_tablet_wide' ) == "1" ){?> selected="selected"<?php }?>>1 Column</option>
            <option value="2"<?php if( get_option( 'ec_option_details_columns_tablet_wide' ) == "2" ){?> selected="selected"<?php }?>>2 Columns</option>
    </select></div>
    <div><strong>Smartphone Landscape Columns</strong></div>
    <div><select id="ec_option_details_columns_tablet">
    		<option value="0"<?php if( get_option( 'ec_option_details_columns_tablet' ) == "" ){?> selected="selected"<?php }?>>Select One</option>
            <option value="1"<?php if( get_option( 'ec_option_details_columns_tablet' ) == "1" ){?> selected="selected"<?php }?>>1 Column</option>
            <option value="2"<?php if( get_option( 'ec_option_details_columns_tablet' ) == "2" ){?> selected="selected"<?php }?>>2 Columns</option>
    </select></div>
    <div><strong>Smartphone Portrait Columns</strong></div>
    <div><select id="ec_option_details_columns_smartphone">
    		<option value="0"<?php if( get_option( 'ec_option_details_columns_smartphone' ) == "" ){?> selected="selected"<?php }?>>Select One</option>
            <option value="1"<?php if( get_option( 'ec_option_details_columns_smartphone' ) == "1" ){?> selected="selected"<?php }?>>1 Column</option>
            <option value="2"<?php if( get_option( 'ec_option_details_columns_smartphone' ) == "2" ){?> selected="selected"<?php }?>>2 Columns</option>
    </select></div>
    <div><strong>Dark/Light Text</strong></div>
    <div><select id="ec_option_use_dark_bg">
    		<option value="0"<?php if( get_option( 'ec_option_use_dark_bg' ) == "" ){?> selected="selected"<?php }?>>Select One</option>
            <option value="1"<?php if( get_option( 'ec_option_use_dark_bg' ) == "1" ){?> selected="selected"<?php }?>>White Text</option>
            <option value="0"<?php if( get_option( 'ec_option_use_dark_bg' ) == "0" ){?> selected="selected"<?php }?>>Dark Text</option>
    </select></div>
    
    <div><input type="button" value="APPLY AND SAVE" onclick="ec_admin_save_product_details_options( ); return false;" /></div>
    
    <div class="ec_admin_view_more_button">
    	<a href="<?php echo get_admin_url( ); ?>admin.php?page=wp-easycart-settings&subpage=design" target="_blank" title="More Options">View More Display Options</a>
    </div>
    
</div>
<div id="ec_current_media_size"></div>
<script>function ec_admin_save_product_details_options( ){
	jQuery( "#ec_admin_page_updated_loader" ).show( );
	jQuery( "#ec_admin_loader_bg" ).show( );
	var data = {
		action: 'ec_ajax_save_product_details_options',
		ec_option_details_main_color: jQuery( document.getElementById( 'ec_option_details_main_color' ) ).val( ),
		ec_option_details_columns_desktop: jQuery( document.getElementById( 'ec_option_details_columns_desktop' ) ).val( ),
		ec_option_details_columns_laptop: jQuery( document.getElementById( 'ec_option_details_columns_laptop' ) ).val( ),
		ec_option_details_columns_tablet_wide: jQuery( document.getElementById( 'ec_option_details_columns_tablet_wide' ) ).val( ),
		ec_option_details_columns_tablet: jQuery( document.getElementById( 'ec_option_details_columns_tablet' ) ).val( ),
		ec_option_details_columns_smartphone: jQuery( document.getElementById( 'ec_option_details_columns_smartphone' ) ).val( ),
		ec_option_use_dark_bg: jQuery( document.getElementById( 'ec_option_use_dark_bg' ) ).val( )
	}
	jQuery.ajax({url: wpeasycart_ajax_object.ajax_url, type: 'post', data: data, success: function(data){ 
		jQuery( document.getElementById( 'ec_admin_page_updated_loader' ) ).hide( );
		jQuery( document.getElementById( 'ec_admin_page_updated' ) ).show( );
		jQuery( document.getElementById( 'ec_admin_loader_bg' ) ).fadeOut( 'slow' );
		location.reload( );
	} } );
	jQuery( document.getElementById( 'ec_page_editor' ) ).animate( { left:'-290px' }, {queue:false, duration:220} ).removeClass( 'ec_display_editor_true' ).addClass( 'ec_display_editor_false' );
}</script>
<?php }// Close editor content ?>
<?php 
/* START PRODUCT DETAILS PAGE */ 
?>

<?php
/* If using Google Merchant Show Necessary META */
if( isset( $this->product->google_attributes ) && $this->product->google_attributes != NULL && $this->product->google_attributes != "" ){
	$google_attributes = json_decode( $this->product->google_attributes );
}else{
	$google_attributes = false;
}
?>
<script type="application/ld+json">
{
    "@context": "http://schema.org",
    "@type": "Product",
    "offers": {
        "@type": "Offer",
        "url": "<?php echo str_replace( '"', '&quot;', preg_replace( "/\r|\n/", "", htmlspecialchars( stripslashes( $this->product->get_product_link( ) ) ) ) ); ?>",
        "availability": "<?php echo ( !$this->product->show_stock_quantity || $this->product->stock_quantity > 0 ) ? 'InStock' : 'OutOfStock'; ?>",
        "price": "<?php echo number_format( $this->product->price, 2, '.', '' ); ?>",
        "priceValidUntil": "<?php echo date( 'Y-m-d', strtotime( '+1 year' ) ); ?>",
        "priceCurrency": "<?php echo $GLOBALS['currency']->get_currency_code( ); ?>"<?php if( $google_attributes ){ ?>,
        "itemCondition": "<?php if( strtolower( $google_attributes->condition ) == "new" ){ echo 'NewCondition'; }else if( strtolower( $google_attributes->condition ) == "used" ){ echo 'UsedCondition'; }else{ echo 'RefurbishedConiditon'; } ?>"<?php }?>
    },
    "brand": "<?php echo str_replace( '"', '&quot;', htmlspecialchars( stripslashes( $this->product->manufacturer_name ) ) ); ?>",
    "sku": "<?php echo str_replace( '"', '&quot;', htmlspecialchars( stripslashes( $this->product->model_number ) ) ); ?>",
    "name": "<?php echo str_replace( '"', '&quot;', htmlspecialchars( stripslashes( $this->product->title ) ) ); ?>",
    "description": "<?php echo ( strlen( $this->product->short_description ) > 0 ) ? str_replace( '"', '&quot;', preg_replace( "/\r|\n/", "", htmlspecialchars( strip_tags( stripslashes( $this->product->short_description ) ) ) ) ) : str_replace( '"', '&quot;', preg_replace( "/\r|\n/", "", htmlspecialchars( strip_tags( stripslashes( $this->product->description ) ) ) ) ); ?>"<?php if( $google_attributes && strlen( $google_attributes->gtin ) > 0 ){ ?>,
    "gtin": "<?php echo $google_attributes->gtin; ?>"<?php }else if( $google_attributes && strlen( $google_attributes->mpn ) > 0 ){ ?>,
    "mpn": "<?php echo $google_attributes->mpn; ?>"<?php }?>,
    "url": "<?php echo str_replace( '"', '&quot;', preg_replace( "/\r|\n/", "", htmlspecialchars( stripslashes( $this->product->get_product_link( ) ) ) ) ); ?>",<?php if( $this->product->use_customer_reviews && count( $this->product->reviews ) > 0 ){ 
    $best_review = false;
    foreach( $this->product->reviews as $one_review ){
        if( !$best_review || $one_review->rating > $best_review->rating ){
            $best_review = $one_review;
        }
    }
    if( $best_review ){
        $best_review = new ec_review( $best_review );
    ?>
    "review": {
        "@type": "Review",
        "reviewRating": {
            "@type": "Rating",
            "ratingValue": "<?php echo $best_review->rating; ?>"
        },
        "author":{
            "@type": "Person",
            "name": "<?php echo $best_review->reviewer_name; ?>"
        },
        "reviewBody": "<?php echo $best_review->description; ?>"
    },<?php }?>
    "aggregateRating": {
        "@type": "AggregateRating",
        "reviewCount": "<?php echo count( $this->product->reviews ); ?>",
        "ratingValue": "<?php echo $this->product->get_rating( ); ?>"
    },<?php }?>
    "image": {
        "@type": "ImageObject",
        "url": "<?php echo str_replace( '"', '&quot;', htmlspecialchars( stripslashes( $this->product->get_first_image_url( ) ) ) ); ?>",
        "image": "<?php echo str_replace( '"', '&quot;', htmlspecialchars( stripslashes( $this->product->get_first_image_url( ) ) ) ); ?>",
        "name": "<?php echo str_replace( '"', '&quot;', htmlspecialchars( stripslashes( $this->product->title ) ) ); ?>"
    }
}
</script>

<?php do_action( 'wpeasycart_product_description_top', $this->product->product_id ); ?>

<?php /* INQUIRY OPTIONS */ ?>
<?php if( $this->product->is_inquiry_mode && $this->product->inquiry_url == "" ){ ?>

<div class="ec_details_inquiry_popup ec_details_inquiry_popup_<?php echo $this->product->product_id; ?>">
<div class="ec_details_inquiry_popup_content">
	<div class="ec_details_inquiry_popup_padding">
		<div class="ec_details_inquiry_popup_holder">
			<div class="ec_details_inquiry_popup_main">
				<div style="display:none;" class="ec_store_loader" id="ec_inquiry_loader">Loading...</div>
				<div class="ec_store_loader_bg" id="ec_inquiry_loader_bg"></div>
				<form action="<?php echo $this->cart_page; ?>" method="POST" enctype="multipart/form-data" class="ec_add_to_cart_form">
				<div class="ec_details_options">
					<h3><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_inquiry_title' ); ?></h3>
					<strong><?php echo $this->product->title; ?></strong>
					<div class="ec_details_option_row_error ec_inquiry_error" id="ec_details_inquiry_error_<?php echo $this->product->product_id; ?>"><?php echo $GLOBALS['language']->get_text( 'ec_errors', 'missing_inquiry_options' ); ?></div>
					<div class="ec_details_option_row">
						<div class="ec_details_option_label"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_inquiry_name' ); ?></div>
						<div class="ec_details_option_data"><input type="text" name="ec_inquiry_name" id="ec_inquiry_name_<?php echo $this->product->product_id; ?>" value="" /></div>
					</div>
					<div class="ec_details_option_row">
						<div class="ec_details_option_label"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_inquiry_email' ); ?></div>
						<div class="ec_details_option_data"><input type="text" name="ec_inquiry_email" id="ec_inquiry_email_<?php echo $this->product->product_id; ?>" value="" /></div>
					</div>
					<div class="ec_details_option_row">
						<div class="ec_details_option_label"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_inquiry_message' ); ?></div>
						<div class="ec_details_option_data"><textarea name="ec_inquiry_message" id="ec_inquiry_message_<?php echo $this->product->product_id; ?>"></textarea></div>
					</div>
					<div class="ec_details_option_row">
						<div class="ec_details_option_label"></div>
						<div class="ec_details_option_data"><input type="checkbox" name="ec_inquiry_send_copy" id="ec_inquiry_send_copy_<?php echo $this->product->product_id; ?>" /> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_inquiry_send_copy' ); ?></div>
					</div>
				</div>
                
                <?php /* PRODUCT BASIC OPTIONS */ 
				$has_quantity_grid = false;
				?>
				<?php if( $this->product->has_options && !$this->product->use_advanced_optionset ){ ?>
				<div class="ec_details_options">
				<?php 
				$optionsets = array( $this->product->options->optionset1, $this->product->options->optionset2, $this->product->options->optionset3, $this->product->options->optionset4, $this->product->options->optionset5 );
				
				for( $i=0; $i<5; $i++ ){ ?>
				
					<?php
					/* START IMAGE SWATCHES AREA */
					if( count( $optionsets[$i]->optionset ) > 0 && $optionsets[$i]->option_type == 'basic-swatch' && $optionsets[$i]->optionset[0]->optionitem_icon != "" ){ ?>
					
					<div class="ec_details_option_row_error ec_option<?php echo ( $i+1 ); ?>_<?php echo $this->product->product_id; ?>" id="ec_details_option_row_error_<?php echo $optionsets[$i]->option_id; ?>_<?php echo $this->product->product_id; ?>"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_missing_option' ); ?> <?php echo $optionsets[$i]->option_label; ?></div>
					<input type="hidden" name="ec_option<?php echo ($i+1); ?>" id="ec_option<?php echo ($i+1); ?>_<?php echo $this->product->product_id; ?>" value="0" />
					<div class="ec_details_option_row">
						<div class="ec_details_option_label"><?php echo $optionsets[$i]->option_label; ?></div>
						<ul class="ec_details_swatches">
						<?php
						for( $j=0; $j<count( $optionsets[$i]->optionset ); $j++ ){
							// Check the in stock status for this option item
							if( $this->product->allow_backorders ){
								$optionitem_in_stock = true;
							}else if( $this->product->use_optionitem_quantity_tracking && ( $i > 0 || $this->product->option1quantity[$optionsets[$i]->optionset[$j]->optionitem_id] <= 0 ) ){
								$optionitem_in_stock = false;
							}else{
								$optionitem_in_stock = true;
							}
						?>
						<li class="ec_details_swatch ec_option<?php echo ($i+1); ?> ec_option<?php echo ($i+1); ?>_<?php echo $this->product->product_id; ?><?php if( $optionitem_in_stock ){ ?> ec_active <?php }?><?php if( $optionsets[$i]->optionset[$j]->optionitem_initially_selected || ( isset( $optionsets[$i]->option_meta['url_var'] ) && $optionsets[$i]->option_meta['url_var'] != '' && isset( $_GET[$optionsets[$i]->option_meta['url_var']] ) && strtolower( $_GET[$optionsets[$i]->option_meta['url_var']] ) == strtolower( $optionsets[$i]->optionset[$j]->optionitem_name ) ) || ( isset( $_GET['o'.$optionsets[$i]->option_id] ) && $_GET['o'.$optionsets[$i]->option_id] == $optionsets[$i]->optionset[$j]->optionitem_name ) ){ ?> ec_selected<?php }?>" data-optionitem-id="<?php echo $optionsets[$i]->optionset[$j]->optionitem_id; ?>"<?php if( $this->product->use_optionitem_quantity_tracking && $i == 0 ){ ?> data-optionitem-quantity="<?php echo $this->product->option1quantity[$optionsets[$i]->optionset[$j]->optionitem_id]; ?>"<?php }?> data-optionitem-price="<?php if( $optionsets[$i]->optionset[$j]->optionitem_price != "" ){ echo $optionsets[$i]->optionset[$j]->optionitem_price; }else{ echo "0.00"; } ?>" data-optionitem-price-onetime="<?php if( isset( $optionsets[$i]->optionset[$j]->optionitem_price_onetime ) && $optionsets[$i]->optionset[$j]->optionitem_price_onetime != "" ){ echo $optionsets[$i]->optionset[$j]->optionitem_price_onetime; }else{ echo "0.00"; } ?>" data-optionitem-price-override="<?php if( isset( $optionsets[$i]->optionset[$j]->optionitem_price_override ) && $optionsets[$i]->optionset[$j]->optionitem_price_override != "" ){ echo $optionsets[$i]->optionset[$j]->optionitem_price_override; }else{ echo "-1.00"; } ?>" data-optionitem-price-multiplier="<?php echo $optionsets[$i]->optionset[$j]->optionitem_price_multiplier; ?>"><img src="<?php if( substr( $optionsets[$i]->optionset[$j]->optionitem_icon, 0, 7 ) == 'http://' || substr( $optionsets[$i]->optionset[$j]->optionitem_icon, 0, 8 ) == 'https://' ){ echo $optionsets[$i]->optionset[$j]->optionitem_icon; }else{ echo plugins_url( "/wp-easycart-data/products/swatches/" . $optionsets[$i]->optionset[$j]->optionitem_icon ); } ?>" title="<?php echo str_replace( '"', '&quot;', $optionsets[$i]->optionset[$j]->optionitem_name ); ?><?php if( $optionsets[$i]->optionset[$j]->optionitem_price > 0 ){ ?> ( +<?php echo $GLOBALS['currency']->get_currency_display( $optionsets[$i]->optionset[$j]->optionitem_price ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ); ?> )<?php }else if( $optionsets[$i]->optionset[$j]->optionitem_price < 0 ){ ?> ( <?php echo $GLOBALS['currency']->get_currency_display( $optionsets[$i]->optionset[$j]->optionitem_price ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ); ?> )<?php }else if( isset( $optionsets[$i]->optionset[$j]->optionitem_price_onetime ) && $optionsets[$i]->optionset[$j]->optionitem_price_onetime > 0 ){ ?> ( +<?php echo $GLOBALS['currency']->get_currency_display( $optionsets[$i]->optionset[$j]->optionitem_price_onetime ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ); ?> )<?php }else if( isset( $optionsets[$i]->optionset[$j]->optionitem_price_onetime ) && $optionsets[$i]->optionset[$j]->optionitem_price_onetime < 0 ){ ?> ( <?php echo $GLOBALS['currency']->get_currency_display( $optionsets[$i]->optionset[$j]->optionitem_price_onetime ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ); ?> )<?php }else if( isset( $optionsets[$i]->optionset[$j]->optionitem_price_override ) && $optionsets[$i]->optionset[$j]->optionitem_price_override > -1 ){ ?> ( <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ); ?> <?php echo $GLOBALS['currency']->get_currency_display( $optionsets[$i]->optionset[$j]->optionitem_price_override ); ?> )<?php }?>" /></li>
						<?php
						}
						?>
						</ul>
						<div class="ec_option_loading" id="ec_option_loading_<?php echo ($i+1); ?>_<?php echo $this->product->product_id; ?>"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_loading_options' ); ?></div>
					</div>
					<?php
					
					// HTML Swatches
					}else if( count( $optionsets[$i]->optionset ) > 0 && $optionsets[$i]->option_type == 'basic-swatch' ){ ?>
					
					<div class="ec_details_option_row_error ec_option<?php echo ( $i+1 ); ?>_<?php echo $this->product->product_id; ?>" id="ec_details_option_row_error_<?php echo $optionsets[$i]->option_id; ?>_<?php echo $this->product->product_id; ?>"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_missing_option' ); ?> <?php echo $optionsets[$i]->option_label; ?></div>
					<input type="hidden" name="ec_option<?php echo ($i+1); ?>" id="ec_option<?php echo ($i+1); ?>_<?php echo $this->product->product_id; ?>" value="0" />
					<div class="ec_details_option_row">
						<div class="ec_details_option_label"><?php echo $optionsets[$i]->option_label; ?></div>
						<ul class="ec_details_swatches ec_details_html_swatches">
						<?php
						for( $j=0; $j<count( $optionsets[$i]->optionset ); $j++ ){
							// Check the in stock status for this option item
							if( $this->product->allow_backorders ){
								$optionitem_in_stock = true;
							}else if( $this->product->use_optionitem_quantity_tracking && ( $i > 0 || $this->product->option1quantity[$optionsets[$i]->optionset[$j]->optionitem_id] <= 0 ) ){
								$optionitem_in_stock = false;
							}else{
								$optionitem_in_stock = true;
							}
						?>
						<li class="ec_details_swatch wpeasycart-html-swatch ec_option<?php echo ($i+1); ?> ec_option<?php echo ($i+1); ?>_<?php echo $this->product->product_id; ?><?php if( $optionitem_in_stock ){ ?> ec_active <?php }?><?php if( $optionsets[$i]->optionset[$j]->optionitem_initially_selected || ( isset( $optionsets[$i]->option_meta['url_var'] ) && $optionsets[$i]->option_meta['url_var'] != '' && isset( $_GET[$optionsets[$i]->option_meta['url_var']] ) && strtolower( $_GET[$optionsets[$i]->option_meta['url_var']] ) == strtolower( $optionsets[$i]->optionset[$j]->optionitem_name ) ) || ( isset( $_GET['o'.$optionsets[$i]->optionset[$j]->option_id] ) && $_GET['o'.$optionsets[$i]->optionset[$j]->option_id] == $optionsets[$i]->optionset[$j]->optionitem_name ) ){ ?> ec_selected<?php }?>" data-optionitem-id="<?php echo $optionsets[$i]->optionset[$j]->optionitem_id; ?>"<?php if( $this->product->use_optionitem_quantity_tracking && $i == 0 ){ ?> data-optionitem-quantity="<?php echo $this->product->option1quantity[$optionsets[$i]->optionset[$j]->optionitem_id]; ?>"<?php }?> data-optionitem-price="<?php if( $optionsets[$i]->optionset[$j]->optionitem_price != "" ){ echo $optionsets[$i]->optionset[$j]->optionitem_price; }else{ echo "0.00"; } ?>" data-optionitem-price-onetime="<?php if( isset( $optionsets[$i]->optionset[$j]->optionitem_price_onetime ) && $optionsets[$i]->optionset[$j]->optionitem_price_onetime != "" ){ echo $optionsets[$i]->optionset[$j]->optionitem_price_onetime; }else{ echo "0.00"; } ?>" data-optionitem-price-override="<?php if( isset( $optionsets[$i]->optionset[$j]->optionitem_price_override ) && $optionsets[$i]->optionset[$j]->optionitem_price_override != "" ){ echo $optionsets[$i]->optionset[$j]->optionitem_price_override; }else{ echo "-1.00"; } ?>" data-optionitem-price-multiplier="<?php echo $optionsets[$i]->optionset[$j]->optionitem_price_multiplier; ?>" title="<?php if( $optionsets[$i]->optionset[$j]->optionitem_price > 0 ){ ?> ( +<?php echo $GLOBALS['currency']->get_currency_display( $optionsets[$i]->optionset[$j]->optionitem_price ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ); ?> )<?php }else if( $optionsets[$i]->optionset[$j]->optionitem_price < 0 ){ ?> ( <?php echo $GLOBALS['currency']->get_currency_display( $optionsets[$i]->optionset[$j]->optionitem_price ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ); ?> )<?php }else if( isset( $optionsets[$i]->optionset[$j]->optionitem_price_onetime ) && $optionsets[$i]->optionset[$j]->optionitem_price_onetime > 0 ){ ?> ( +<?php echo $GLOBALS['currency']->get_currency_display( $optionsets[$i]->optionset[$j]->optionitem_price_onetime ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ); ?> )<?php }else if( isset( $optionsets[$i]->optionset[$j]->optionitem_price_onetime ) && $optionsets[$i]->optionset[$j]->optionitem_price_onetime < 0 ){ ?> ( <?php echo $GLOBALS['currency']->get_currency_display( $optionsets[$i]->optionset[$j]->optionitem_price_onetime ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ); ?> )<?php }else if( isset( $optionsets[$i]->optionset[$j]->optionitem_price_override ) && $optionsets[$i]->optionset[$j]->optionitem_price_override > -1 ){ ?> ( <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ); ?> <?php echo $GLOBALS['currency']->get_currency_display( $optionsets[$i]->optionset[$j]->optionitem_price_override ); ?> )<?php }?>"><?php echo $optionsets[$i]->optionset[$j]->optionitem_name; ?></li>
						<?php
						}
						?>
						</ul>
						<div class="ec_option_loading" id="ec_option_loading_<?php echo ($i+1); ?>_<?php echo $this->product->product_id; ?>"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_loading_options' ); ?></div>
					</div>
					<?php
					/* END SWATCHES AREA */
					
					/* START COMBO BOX AREA */
					}else if( count( $optionsets[$i]->optionset ) > 0 && $optionsets[$i]->optionset[0]->optionitem_name != "" ){ ?>
					<div class="ec_details_option_row_error ec_option<?php echo ( $i+1 ); ?>_<?php echo $this->product->product_id; ?>" id="ec_details_option_row_error_<?php echo $optionsets[$i]->option_id; ?>_<?php echo $this->product->product_id; ?>"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_missing_option' ); ?> <?php echo $optionsets[$i]->option_label; ?></div>
					
					<div class="ec_details_option_row">
						<select name="ec_option<?php echo ($i+1); ?>" id="ec_option<?php echo ($i+1); ?>" class="ec_details_combo ec_option<?php echo ($i+1); ?>_<?php echo $this->product->product_id; ?> ec_option<?php echo ($i+1); ?><?php if( $this->product->use_optionitem_quantity_tracking && $i > 0 ){ ?> ec_inactive<?php }?>"<?php if( $this->product->use_optionitem_quantity_tracking && $i > 0 ){ ?> disabled="disabled"<?php }?>>
						<option value="0"<?php if( $this->product->use_optionitem_quantity_tracking && $i == 0 ){ ?> data-optionitem-quantity="<?php echo $this->product->stock_quantity; ?>"<?php }?> data-optionitem-price="0.00" data-optionitem-price-onetime="0.00" data-optionitem-price-override="-1" data-optionitem-price-multiplier="-1.00"><?php echo $optionsets[$i]->option_label; ?></option>
						<?php
						for( $j=0; $j<count( $optionsets[$i]->optionset ); $j++ ){
							// Check the in stock status for this option item
							if( $this->product->allow_backorders ){
								$optionitem_in_stock = true;
							}else if( $this->product->use_optionitem_quantity_tracking && ( $i > 0 || $this->product->option1quantity[$optionsets[$i]->optionset[$j]->optionitem_id] <= 0 ) ){
								$optionitem_in_stock = false;
							}else{
								$optionitem_in_stock = true;
							}
						?>
						<?php if( !$this->product->use_optionitem_quantity_tracking || $i != 0 || $this->product->option1quantity[$optionsets[$i]->optionset[$j]->optionitem_id] > 0 || $optionitem_in_stock ){ ?> 
						<option value="<?php echo $optionsets[$i]->optionset[$j]->optionitem_id; ?>"<?php if( $this->product->use_optionitem_quantity_tracking && $i == 0 ){ ?> data-optionitem-quantity="<?php echo $this->product->option1quantity[$optionsets[$i]->optionset[$j]->optionitem_id]; ?>"<?php }?> data-optionitem-price="<?php if( $optionsets[$i]->optionset[$j]->optionitem_price != "" ){ echo $optionsets[$i]->optionset[$j]->optionitem_price; }else{ echo "0.00"; } ?>" data-optionitem-price-onetime="<?php if( $optionsets[$i]->optionset[$j]->optionitem_price_onetime != "" ){ echo $optionsets[$i]->optionset[$j]->optionitem_price_onetime; }else{ echo "0.00"; } ?>" data-optionitem-price-override="<?php if( isset( $optionsets[$i]->optionset[$j]->optionitem_price_override ) && $optionsets[$i]->optionset[$j]->optionitem_price_override != "" ){ echo $optionsets[$i]->optionset[$j]->optionitem_price_override; }else{ echo "-1.00"; } ?>" data-optionitem-price-multiplier="<?php echo $optionsets[$i]->optionset[$j]->optionitem_price_multiplier; ?>"<?php if( $optionsets[$i]->optionset[$j]->optionitem_initially_selected || ( isset( $optionsets[$i]->option_meta['url_var'] ) && $optionsets[$i]->option_meta['url_var'] != '' && isset( $_GET[$optionsets[$i]->option_meta['url_var']] ) && strtolower( $_GET[$optionsets[$i]->option_meta['url_var']] ) == strtolower( $optionsets[$i]->optionset[$j]->optionitem_name ) ) || ( isset( $_GET['o'.$optionsets[$i]->option_id] ) && $_GET['o'.$optionsets[$i]->option_id] == $optionsets[$i]->optionset[$j]->optionitem_name ) ){ ?> selected="selected"<?php }?>><?php echo $optionsets[$i]->optionset[$j]->optionitem_name; ?><?php if( $optionsets[$i]->optionset[$j]->optionitem_price > 0 ){ echo ' (+' . $GLOBALS['currency']->get_currency_display( $optionsets[$i]->optionset[$j]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')'; }else if( $optionsets[$i]->optionset[$j]->optionitem_price < 0 ){ echo ' (' . $GLOBALS['currency']->get_currency_display( $optionsets[$i]->optionset[$j]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')'; }else if( isset( $optionsets[$i]->optionset[$j]->optionitem_price_onetime ) && $optionsets[$i]->optionset[$j]->optionitem_price_onetime > 0 ){ echo ' (+' . $GLOBALS['currency']->get_currency_display( $optionsets[$i]->optionset[$j]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')'; }else if( isset( $optionsets[$i]->optionset[$j]->optionitem_price_onetime ) && $optionsets[$i]->optionset[$j]->optionitem_price_onetime < 0 ){ echo ' (' . $GLOBALS['currency']->get_currency_display( $optionsets[$i]->optionset[$j]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')'; }else if( isset( $optionsets[$i]->optionset[$j]->optionitem_price_override ) && $optionsets[$i]->optionset[$j]->optionitem_price_override > -1 ){ echo ' (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ) . $GLOBALS['currency']->get_currency_display( $optionsets[$i]->optionset[$j]->optionitem_price_override ) . ')'; } ?></option>
						<?php }?>	
						<?php
						}
						?>
						</select>
						<div class="ec_option_loading" id="ec_option_loading_<?php echo ($i+1); ?>_<?php echo $this->product->product_id; ?>"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_loading_options' ); ?></div>
					</div>
					<?php
					}
					/* END COMBO BOX AREA*/
				}
				?>
				</div>
				<?php } ?>
				<?php /* END BASIC OPTIONS */ ?>
				
				<?php /* PRODUCT ADVANCED OPTIONS */ ?>
				<?php 
				
				$add_price_grid = 0;
				$add_order_price_grid = 0;
				$override_price_grid = -1;
				if( $this->product->use_advanced_optionset && count( $this->product->advanced_optionsets ) > 0 ){ ?>
				<div class="ec_details_options">
					<?php 
					foreach( $this->product->advanced_optionsets as $optionset ){
						$optionitems = $this->product->get_advanced_optionitems( $optionset->option_id );
					?>
					<?php 
					if( $optionset->option_required ){ 
					?>
					<div class="ec_details_option_row_error" id="ec_details_option_row_error_<?php echo $optionset->option_id; ?>_<?php echo $this->product->product_id; ?>"><?php echo $optionset->option_error_text; ?></div>
					<?php
					}
					?>
					<div class="ec_details_option_row ec_option_type_<?php echo $optionset->option_type; ?>" data-option-id="<?php echo $optionset->option_id; ?>" data-option-required="<?php echo $optionset->option_required; ?>" data-product-id="<?php echo $this->product->product_id; ?>">
						<?php if( $optionset->option_type != "combo" ){ ?>
						<div class="ec_details_option_label"><?php echo $optionset->option_label; ?></div>
						<?php }?>
						<div class="ec_details_option_data">
						<?php
						/* START ADVANCED CHECBOX TYPE */
						if( $optionset->option_type == "checkbox" ){
						?>
						
							<?php
							foreach( $optionitems as $optionitem ){
							?>
								
								<div class="ec_details_checkbox_row"><input type="checkbox" class="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>" name="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $optionitem->optionitem_id; ?>" value="<?php echo $optionitem->optionitem_name; ?>" data-product-id="<?php echo $this->product->product_id; ?>" data-optionitem-price="<?php echo $optionitem->optionitem_price; ?>" data-optionitem-price-onetime="<?php echo $optionitem->optionitem_price_onetime; ?>" data-optionitem-price-override="<?php echo $optionitem->optionitem_price_override; ?>" data-optionitem-price-multiplier="<?php echo $optionitem->optionitem_price_multiplier; ?>"<?php if( $optionitem->optionitem_initially_selected || ( isset( $optionset->option_meta['url_var'] ) && $optionset->option_meta['url_var'] != '' && isset( $_GET[$optionset->option_meta['url_var']] ) && strtolower( $_GET[$optionset->option_meta['url_var']] ) == strtolower( $optionitem->optionitem_name ) ) || ( isset( $_GET['o'.$optionset->option_id] ) && $_GET['o'.$optionset->option_id] == $optionitem->optionitem_name ) ){ ?> checked="checked"<?php }?> /> <?php echo $optionitem->optionitem_name; ?><?php if( $optionitem->optionitem_price > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitem->optionitem_price < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitem->optionitem_price_onetime > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( $optionitem->optionitem_price_onetime < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( isset( $optionitem->optionitem_price_override ) && $optionitem->optionitem_price_override > -1 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price_override ) . ')</span>'; } ?></div>
								
							<?php
							}
							?>
							
						<?php
						
						/* START ADVANCED COMBO TYPE */
						}else if( $optionset->option_type == "combo" ){
						?>
							<select name="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>" id="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $this->product->product_id; ?>" data-option-id="<?php echo $optionset->option_id; ?>">
							<option value="0" data-optionitem-price="0.000" data-optionitem-price-onetime="0.000" data-optionitem-price-override="-1.000" data-optionitem-price-multiplier="-1.000"><?php echo $optionset->option_label; ?></option>
							<?php
							foreach( $optionitems as $optionitem ){
							?>
								
								<option value="<?php echo $optionitem->optionitem_id; ?>" data-product-id="<?php echo $this->product->product_id; ?>" data-optionitem-price="<?php echo $optionitem->optionitem_price; ?>" data-optionitem-price-onetime="<?php echo $optionitem->optionitem_price_onetime; ?>" data-optionitem-price-override="<?php echo $optionitem->optionitem_price_override; ?>" data-optionitem-price-multiplier="<?php echo $optionitem->optionitem_price_multiplier; ?>"<?php if( $optionitem->optionitem_initially_selected || ( isset( $optionset->option_meta['url_var'] ) && $optionset->option_meta['url_var'] != '' && isset( $_GET[$optionset->option_meta['url_var']] ) && strtolower( $_GET[$optionset->option_meta['url_var']] ) == strtolower( $optionitem->optionitem_name ) ) || ( isset( $_GET['o'.$optionset->option_id] ) && $_GET['o'.$optionset->option_id] == $optionitem->optionitem_name ) ){ ?> selected="selected"<?php }?>><?php echo $optionitem->optionitem_name; ?><?php if( $optionitem->optionitem_price > 0 ){ echo ' (+' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')'; }else if( $optionitem->optionitem_price < 0 ){ echo ' (' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')'; }else if( $optionitem->optionitem_price_onetime > 0 ){ echo ' (+' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')'; }else if( $optionitem->optionitem_price_onetime < 0 ){ echo ' (' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')'; }else if( isset( $optionitem->optionitem_price_override ) && $optionitem->optionitem_price_override > -1 ){ echo ' (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price_override ) . ')'; } ?></option>
								
							<?php
							}
							?>
							</select>
						<?php
						
						/* START ADVANCED DATE TYPE*/
						}else if( $optionset->option_type == "date" ){
						?>
						
							<input type="text" value="<?php if( isset( $_GET['o'.$optionset->option_id] ) || ( isset( $optionset->option_meta['url_var'] ) && $optionset->option_meta['url_var'] != '' && isset( $_GET[$optionset->option_meta['url_var']] ) ) ){ echo htmlspecialchars( ( ( isset( $_GET['o'.$optionset->option_id] ) ) ? $_GET['o'.$optionset->option_id] : $_GET[$optionset->option_meta['url_var']] ), ENT_QUOTES ); } ?>" class="ec_is_datepicker" name="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>" id="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $this->product->product_id; ?>" data-product-id="<?php echo $this->product->product_id; ?>" data-optionitem-price="<?php echo $optionitems[0]->optionitem_price; ?>" data-optionitem-price-onetime="<?php echo $optionitems[0]->optionitem_price_onetime; ?>" data-optionitem-price-override="<?php echo $optionitems[0]->optionitem_price_override; ?>" data-optionitem-price-multiplier="<?php echo $optionitems[0]->optionitem_price_multiplier; ?>" /><?php if( $optionitems[0]->optionitem_price > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price_onetime > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price_onetime < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( isset( $optionitems[0]->optionitem_price_override ) && $optionitems[0]->optionitem_price_override > -1 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_override ) . ')</span>'; } ?>
						
						<?php
						
						/* START ADVANCED FILE TYPE */
						}else if( $optionset->option_type == "file" ){
						?>
						
							<input type="file" name="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>" id="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $this->product->product_id; ?>" data-product-id="<?php echo $this->product->product_id; ?>" data-optionitem-price="<?php echo $optionitems[0]->optionitem_price; ?>" data-optionitem-price-onetime="<?php echo $optionitems[0]->optionitem_price_onetime; ?>" data-optionitem-price-override="<?php echo $optionitems[0]->optionitem_price_override; ?>" data-optionitem-price-multiplier="<?php echo $optionitems[0]->optionitem_price_multiplier; ?>" /><?php if( $optionitems[0]->optionitem_price > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price_onetime > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price_onetime < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( isset( $optionitems[0]->optionitem_price_override ) && $optionitems[0]->optionitem_price_override > -1 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_override ) . ')</span>'; } ?>
						
						<?php
						
						/* START ADVANCED SWATCH TYPE */
						}else if( $optionset->option_type == "swatch" ){
					
                    	if( $optionitems[0]->optionitem_icon != '' ){
						?>
							<input type="hidden" name="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>" id="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $this->product->product_id; ?>" value="0" />
							<ul class="ec_details_swatches">
								<?php
								for( $j=0; $j<count( $optionitems ); $j++ ){
								?>
									<li class="ec_details_swatch ec_advanced ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $this->product->product_id; ?> ec_active<?php if( $optionsets[$i]->optionset[$j]->optionitem_initially_selected || ( isset( $_GET['o'.$optionset->option_id] ) && $_GET['o'.$optionset->option_id] == $optionsets[$i]->optionset[$j]->optionitem_name ) ){ ?> ec_selected<?php }?>" data-product-id="<?php echo $this->product->product_id; ?>" data-optionitem-id="<?php echo $optionitems[$j]->optionitem_id; ?>" data-option-id="<?php echo $optionset->option_to_product_id; ?>" data-optionitem-price="<?php echo $optionitems[$j]->optionitem_price; ?>" data-optionitem-price-onetime="<?php echo $optionitems[$j]->optionitem_price_onetime; ?>" data-optionitem-price-override="<?php echo $optionitems[$j]->optionitem_price_override; ?>" data-optionitem-price-multiplier="<?php echo $optionitems[$j]->optionitem_price_multiplier; ?>"><img src="<?php if( substr( $optionitems[$j]->optionitem_icon, 0, 7 ) == 'http://' || substr( $optionitems[$j]->optionitem_icon, 0, 8 ) == 'https://' ){ echo $optionitems[$j]->optionitem_icon; }else{ echo plugins_url( "/wp-easycart-data/products/swatches/" . $optionitems[$j]->optionitem_icon ); } ?>"<?php if( $optionitems[$j]->optionitem_price > 0 ){ ?> title="<?php echo str_replace( '"', '&quot;', $optionitems[$j]->optionitem_name ); ?> +<?php echo $GLOBALS['currency']->get_currency_display( $optionitems[$j]->optionitem_price ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ); ?>"<?php }else if( $optionitems[$j]->optionitem_price < 0 ){ ?> title="<?php echo str_replace( '"', '&quot;', $optionitems[$j]->optionitem_name ); ?> <?php echo $GLOBALS['currency']->get_currency_display( $optionitems[$j]->optionitem_price ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ); ?>"<?php }else if( $optionitems[$j]->optionitem_price_onetime > 0 ){ ?> title="<?php echo str_replace( '"', '&quot;', $optionitems[$j]->optionitem_name ); ?> +<?php echo $GLOBALS['currency']->get_currency_display( $optionitems[$j]->optionitem_price_onetime ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ); ?>"<?php }else if( $optionitems[$j]->optionitem_price_onetime < 0 ){ ?> title="<?php echo str_replace( '"', '&quot;', $optionitems[$j]->optionitem_name ); ?> <?php echo $GLOBALS['currency']->get_currency_display( $optionitems[$j]->optionitem_price_onetime ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ); ?>"<?php }else if( isset( $optionitems[$j]->optionitem_price_override ) && $optionitems[$j]->optionitem_price_override > -1 ){ ?> title="<?php echo str_replace( '"', '&quot;', $optionitems[$j]->optionitem_name ); ?> <?php $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ); ?> <?php echo $GLOBALS['currency']->get_currency_display( $optionitems[$j]->optionitem_price_override ); ?>"<?php }else{ ?> title="<?php echo str_replace( '"', '&quot;', $optionitems[$j]->optionitem_name ); ?>"<?php }?>/></li>
								<?php
								}
								?>
							</ul>
						
						<?php
							/* Advanced Swatch HTML */
							}else{ ?>
							<input type="hidden" name="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>" id="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $this->product->product_id; ?>" value="0" />
							<ul class="ec_details_swatches ec_details_html_swatches">
								<?php
								for( $j=0; $j<count( $optionitems ); $j++ ){
								?>
									<li class="ec_details_swatch wpeasycart-html-swatch ec_advanced ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $this->product->product_id; ?> ec_active<?php if( $optionsets[$i]->optionset[$j]->optionitem_initially_selected || ( isset( $_GET['o'.$optionset->option_id] ) && $_GET['o'.$optionset->option_id] == $optionsets[$i]->optionset[$j]->optionitem_name ) ){ ?> ec_selected<?php }?>" data-product-id="<?php echo $this->product->product_id; ?>" data-optionitem-id="<?php echo $optionitems[$j]->optionitem_id; ?>" data-option-id="<?php echo $optionset->option_to_product_id; ?>" data-optionitem-price="<?php echo $optionitems[$j]->optionitem_price; ?>" data-optionitem-price-onetime="<?php echo $optionitems[$j]->optionitem_price_onetime; ?>" data-optionitem-price-override="<?php echo $optionitems[$j]->optionitem_price_override; ?>" data-optionitem-price-multiplier="<?php echo $optionitems[$j]->optionitem_price_multiplier; ?>"<?php if( $optionitems[$j]->optionitem_price > 0 ){ ?> title="<?php echo str_replace( '"', '&quot;', $optionitems[$j]->optionitem_name ); ?> +<?php echo $GLOBALS['currency']->get_currency_display( $optionitems[$j]->optionitem_price ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ); ?>"<?php }else if( $optionitems[$j]->optionitem_price < 0 ){ ?> title="<?php echo str_replace( '"', '&quot;', $optionitems[$j]->optionitem_name ); ?> <?php echo $GLOBALS['currency']->get_currency_display( $optionitems[$j]->optionitem_price ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ); ?>"<?php }else if( $optionitems[$j]->optionitem_price_onetime > 0 ){ ?> title="<?php echo str_replace( '"', '&quot;', $optionitems[$j]->optionitem_name ); ?> +<?php echo $GLOBALS['currency']->get_currency_display( $optionitems[$j]->optionitem_price_onetime ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ); ?>"<?php }else if( $optionitems[$j]->optionitem_price_onetime < 0 ){ ?> title="<?php echo str_replace( '"', '&quot;', $optionitems[$j]->optionitem_name ); ?> <?php echo $GLOBALS['currency']->get_currency_display( $optionitems[$j]->optionitem_price_onetime ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ); ?>"<?php }else if( isset( $optionitems[$j]->optionitem_price_override ) && $optionitems[$j]->optionitem_price_override > -1 ){ ?> title="<?php echo str_replace( '"', '&quot;', $optionitems[$j]->optionitem_name ); ?> <?php $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ); ?> <?php echo $GLOBALS['currency']->get_currency_display( $optionitems[$j]->optionitem_price_override ); ?>"<?php }else{ ?> title="<?php echo str_replace( '"', '&quot;', $optionitems[$j]->optionitem_name ); ?>"<?php }?>><?php echo $optionitems[$j]->optionitem_name; ?></li>
								<?php
								}
								?>
							</ul>	
						<?php	
							}
					
						/* START ADVANCED GRID TYPE */
						}else if( $optionset->option_type == "grid" ){
							$has_quantity_grid = true;
						?>
						
							<?php
							foreach( $optionitems as $optionitem ){
							
							if( $optionitem->optionitem_initial_value > 0 ){	
								if( $optionitem->optionitem_price >= 0 ){
									$add_price_grid = $add_price_grid + $optionitem->optionitem_price;
								
								}else if( $optionitem->optionitem_price_override >= 0 ){
									$override_price_grid = $optionitem->optionitem_price_override;
								
								}else if( $optionitem->optionitem_price_onetime > 0 ){
									$add_order_price_grid = $add_order_price_grid + $optionitem->optionitem_price_onetime;
								
								}
							}
							?>
								
								<div class="ec_details_grid_row"><span><?php echo $optionitem->optionitem_name; ?></span><input type="number" min="<?php if( $this->product->min_purchase_quantity > 0 ){ echo $this->product->min_purchase_quantity; }else{ echo '0'; } ?>"<?php if( $this->product->show_stock_quantity || $this->product->max_purchase_quantity > 0 ){ ?> max="<?php if( $this->product->max_purchase_quantity > 0 ){ echo $this->product->max_purchase_quantity; }else{ echo $this->product->stock_quantity; } ?>"<?php }?> step="1" name="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $optionitem->optionitem_id; ?>" value="<?php echo number_format( $optionitem->optionitem_initial_value, 0, "", "" ); ?>" data-product-id="<?php echo $this->product->product_id; ?>" data-optionitem-price="<?php echo $optionitem->optionitem_price; ?>" data-optionitem-price-onetime="<?php echo $optionitem->optionitem_price_onetime; ?>" data-optionitem-price-override="<?php echo $optionitem->optionitem_price_override; ?>" data-optionitem-price-multiplier="<?php echo $optionitem->optionitem_price_multiplier; ?>" /><?php if( $optionitem->optionitem_price > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitem->optionitem_price < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitem->optionitem_price_onetime > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( $optionitem->optionitem_price_onetime < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( isset( $optionitem->optionitem_price_override ) && $optionitem->optionitem_price_override > -1 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price_override ) . ')</span>'; } ?></div>
								
							<?php
							}
							?>
						
						<?php
						
						/* START ADVANCED RADIO TYPE */
						}else if( $optionset->option_type == "radio" ){
						?>
						
							<?php
							foreach( $optionitems as $optionitem ){
							?>
								
								<div class="ec_details_radio_row"><input type="radio" name="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>" value="<?php echo $optionitem->optionitem_id; ?>" data-product-id="<?php echo $this->product->product_id; ?>" data-optionitem-price="<?php echo $optionitem->optionitem_price; ?>" data-optionitem-price-onetime="<?php echo $optionitem->optionitem_price_onetime; ?>" data-optionitem-price-override="<?php echo $optionitem->optionitem_price_override; ?>" data-optionitem-price-multiplier="<?php echo $optionitem->optionitem_price_multiplier; ?>"<?php if( $optionitem->optionitem_initially_selected || ( isset( $optionset->option_meta['url_var'] ) && $optionset->option_meta['url_var'] != '' && isset( $_GET[$optionset->option_meta['url_var']] ) && strtolower( $_GET[$optionset->option_meta['url_var']] ) == strtolower( $optionitem->optionitem_name ) ) || ( isset( $_GET['o'.$optionset->option_id] ) && $_GET['o'.$optionset->option_id] == $optionitem->optionitem_name ) ){ ?> checked="checked"<?php }?> /> <?php echo $optionitem->optionitem_name; ?><?php if( $optionitem->optionitem_price > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitem->optionitem_price < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitem->optionitem_price_onetime > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( $optionitem->optionitem_price_onetime < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( isset( $optionitem->optionitem_price_override ) && $optionitem->optionitem_price_override > -1 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price_override ) . ')</span>'; } ?></div>
								
							<?php
							}
							?>
							
						<?php
						
						/* START ADVANCED TEXT TYPE */
						}else if( $optionset->option_type == "text" ){
						?>
						
							<input type="text" value="<?php if( isset( $_GET['o'.$optionset->option_id] ) || ( isset( $optionset->option_meta['url_var'] ) && $optionset->option_meta['url_var'] != '' && isset( $_GET[$optionset->option_meta['url_var']] ) ) ){ echo htmlspecialchars( ( ( isset( $_GET['o'.$optionset->option_id] ) ) ? $_GET['o'.$optionset->option_id] : $_GET[$optionset->option_meta['url_var']] ), ENT_QUOTES ); }else if( $optionitems[0]->optionitem_initial_value != '' ){ echo $optionitems[0]->optionitem_initial_value; } ?>" name="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>" id="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $this->product->product_id; ?>" data-product-id="<?php echo $this->product->product_id; ?>" data-optionitem-price="<?php echo $optionitems[0]->optionitem_price; ?>" data-optionitem-price-onetime="<?php echo $optionitems[0]->optionitem_price_onetime; ?>" data-optionitem-price-override="<?php echo $optionitems[0]->optionitem_price_override; ?>" data-optionitem-price-multiplier="<?php echo $optionitems[0]->optionitem_price_multiplier; ?>" data-optionitem-price-per-character="<?php echo $optionitems[0]->optionitem_price_per_character; ?>" /><?php if( $optionitems[0]->optionitem_price > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price_onetime > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price_onetime < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( isset( $optionitems[0]->optionitem_price_override ) && $optionitems[0]->optionitem_price_override > -1 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_override ) . ')</span>'; }else if( isset( $optionitems[0]->optionitem_price_per_character ) && $optionitems[0]->optionitem_price_per_character > 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_per_character ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment_per_character' ) . ')</span>'; } ?>
						
						<?php
						
						/* START ADVANCED NUMBER TYPE */
						}else if( $optionset->option_type == "number" ){
						?>
						
							<input type="number" value="<?php if( isset( $_GET['o'.$optionset->option_id] ) || ( isset( $optionset->option_meta['url_var'] ) && $optionset->option_meta['url_var'] != '' && isset( $_GET[$optionset->option_meta['url_var']] ) ) ){ echo htmlspecialchars( ( ( isset( $_GET['o'.$optionset->option_id] ) ) ? $_GET['o'.$optionset->option_id] : $_GET[$optionset->option_meta['url_var']] ), ENT_QUOTES ); }else if( $optionitems[0]->optionitem_initial_value != '' ){ echo $optionitems[0]->optionitem_initial_value; } ?>" min="<?php echo $optionset->option_meta['min']; ?>" max="<?php echo $optionset->option_meta['max']; ?>" step="<?php echo $optionset->option_meta['step']; ?>" name="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>" id="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $this->product->product_id; ?>" data-product-id="<?php echo $this->product->product_id; ?>" data-optionitem-price="<?php echo $optionitems[0]->optionitem_price; ?>" data-optionitem-price-onetime="<?php echo $optionitems[0]->optionitem_price_onetime; ?>" data-optionitem-price-override="<?php echo $optionitems[0]->optionitem_price_override; ?>" data-optionitem-price-multiplier="<?php echo $optionitems[0]->optionitem_price_multiplier; ?>" /><?php if( $optionitems[0]->optionitem_price > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price_onetime > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price_onetime < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( isset( $optionitems[0]->optionitem_price_override ) && $optionitems[0]->optionitem_price_override > -1 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_override ) . ')</span>'; }else if( isset( $optionitems[0]->optionitem_price_per_character ) && $optionitems[0]->optionitem_price_per_character > 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_per_character ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment_per_character' ) . ')</span>'; } ?>
						
						<?php
						
						/* START ADVANCED TEXT AREA TYPE */
						}else if( $optionset->option_type == "textarea" ){
						?>
						
							<textarea name="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>" id="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $this->product->product_id; ?>" data-product-id="<?php echo $this->product->product_id; ?>" data-optionitem-price="<?php echo $optionitems[0]->optionitem_price; ?>" data-optionitem-price-onetime="<?php echo $optionitems[0]->optionitem_price_onetime; ?>" data-optionitem-price-override="<?php echo $optionitems[0]->optionitem_price_override; ?>" data-optionitem-price-multiplier="<?php echo $optionitems[0]->optionitem_price_multiplier; ?>" data-optionitem-price-per-character="<?php echo $optionitems[0]->optionitem_price_per_character; ?>"><?php if( isset( $_GET['o'.$optionset->option_id] ) || ( isset( $optionset->option_meta['url_var'] ) && $optionset->option_meta['url_var'] != '' && isset( $_GET[$optionset->option_meta['url_var']] ) ) ){ echo htmlspecialchars( ( ( isset( $_GET['o'.$optionset->option_id] ) ) ? $_GET['o'.$optionset->option_id] : $_GET[$optionset->option_meta['url_var']] ), ENT_QUOTES ); } ?></textarea><?php if( $optionitems[0]->optionitem_price > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price_onetime > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price_onetime < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( isset( $optionitems[0]->optionitem_price_override ) && $optionitems[0]->optionitem_price_override > -1 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_override ) . ')</span>'; }else if( isset( $optionitems[0]->optionitem_price_per_character ) && $optionitems[0]->optionitem_price_per_character > 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_per_character ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment_per_character' ) . ')</span>'; } ?>
						
						<?php
						
						/* START ADVANCED DIMENSIONS TYPE */
						}else if( $optionset->option_type == "dimensions1" || $optionset->option_type == "dimensions2" ){
							
							// Type 1 is NO sub dimensions (34")
							// Type 2 USES sub dimensions (34 1/2")
							
							$type = 2;
							
							if( $optionitems[0]->optionitem_name == "DimensionType1" )
								$type = 1;
						?>
						
							<input type="text" name="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_width" id="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $this->product->product_id; ?>_width" data-product-id="<?php echo $this->product->product_id; ?>" data-optionitem-price="<?php echo $optionitems[0]->optionitem_price; ?>" data-optionitem-price-onetime="<?php echo $optionitems[0]->optionitem_price_onetime; ?>" data-optionitem-price-override="<?php echo $optionitems[0]->optionitem_price_override; ?>" data-optionitem-price-multiplier="<?php echo $optionitems[0]->optionitem_price_multiplier; ?>" data-optionitem-price-per-character="<?php echo $optionitems[0]->optionitem_price_per_character; ?>" class="ec_dimensions_box ec_dimensions_width" data-option-id="<?php echo $optionset->option_to_product_id; ?>" /><?php if( $optionitems[0]->optionitem_price > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price_onetime > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price_onetime < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( isset( $optionitems[0]->optionitem_price_override ) && $optionitems[0]->optionitem_price_override > -1 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_override ) . ')</span>'; }else if( isset( $optionitems[0]->optionitem_price_per_character ) && $optionitems[0]->optionitem_price_per_character > 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_per_character ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment_per_character' ) . ')</span>'; } ?>
							
							<?php if( $type == 2 ){ ?>
							<select name="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_sub_width" id="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $this->product->product_id; ?>_sub_width" class="ec_dimensions_select">
								<option value="0">0</option>
								<option value="1/16">1/16</option>
								<option value="1/8">1/8</option>
								<option value="3/16">3/16</option>
								<option value="1/4">1/4</option>
								<option value="5/16">5/16</option>
								<option value="3/8">3/8</option>
								<option value="7/16">7/16</option>
								<option value="1/2">1/2</option>
								<option value="9/16">9/16</option>
								<option value="5/8">5/8</option>
								<option value="11/16">11/16</option>
								<option value="3/4">3/4</option>
								<option value="13/16">13/16</option>
								<option value="7/8">7/8</option>
								<option value="15/16">15/16</option>
							</select>
							<?php }?>
							
							<span class="ec_dimensions_seperator">x</span>
							
							<input type="text" name="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_height" id="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $this->product->product_id; ?>_height" data-product-id="<?php echo $this->product->product_id; ?>" data-optionitem-price="<?php echo $optionitems[0]->optionitem_price; ?>" data-optionitem-price-onetime="<?php echo $optionitems[0]->optionitem_price_onetime; ?>" data-optionitem-price-override="<?php echo $optionitems[0]->optionitem_price_override; ?>" data-optionitem-price-multiplier="<?php echo $optionitems[0]->optionitem_price_multiplier; ?>" data-optionitem-price-per-character="<?php echo $optionitems[0]->optionitem_price_per_character; ?>" class="ec_dimensions_box" /><?php if( $optionitems[0]->optionitem_price > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price_onetime > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price_onetime < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( isset( $optionitems[0]->optionitem_price_override ) && $optionitems[0]->optionitem_price_override > -1 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_override ) . ')</span>'; }else if( isset( $optionitems[0]->optionitem_price_per_character ) && $optionitems[0]->optionitem_price_per_character > 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_per_character ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment_per_character' ) . ')</span>'; } ?>
							
							<?php if( $type == 2 ){ ?>
							<select name="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_sub_height" id="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $this->product->product_id; ?>_sub_height" class="ec_dimensions_select">
								<option value="0">0</option>
								<option value="1/16">1/16</option>
								<option value="1/8">1/8</option>
								<option value="3/16">3/16</option>
								<option value="1/4">1/4</option>
								<option value="5/16">5/16</option>
								<option value="3/8">3/8</option>
								<option value="7/16">7/16</option>
								<option value="1/2">1/2</option>
								<option value="9/16">9/16</option>
								<option value="5/8">5/8</option>
								<option value="11/16">11/16</option>
								<option value="3/4">3/4</option>
								<option value="13/16">13/16</option>
								<option value="7/8">7/8</option>
								<option value="15/16">15/16</option>
							</select>
							<?php }?>
						
						<?php
						}
					?>
						</div>
					</div>				
					<?php
					}
					?>
				</div>
				<?php }?>
				<?php /* END ADVANCED OPTIONS*/ ?>
				
                <?php /* Maybe add recaptcha */ ?>
                <?php if( get_option( 'ec_option_enable_recaptcha' ) && get_option( 'ec_option_recaptcha_site_key' ) != '' ){ ?>
                <input type="hidden" id="ec_grecaptcha_response_inquiry" name="ec_grecaptcha_response_inquiry" value="" />
                <input type="hidden" id="ec_grecaptcha_site_key" value="<?php echo get_option( 'ec_option_recaptcha_site_key' ); ?>" />
                <div class="ec_cart_input_row" data-sitekey="<?php echo get_option( 'ec_option_recaptcha_site_key' ); ?>" id="ec_product_details_inquiry_recaptcha"></div>
                <?php }?>
				
				<div class="ec_details_add_to_cart">
					<input type="submit" value="<?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_inquire' ); ?>" onclick="return ec_details_submit_inquiry_<?php echo $this->product->product_id; ?>( );" style="margin-left:0px !important;" />
				</div>
                <input type="hidden" name="ec_cart_form_action" value="send_inquiry" />
                <input type="hidden" name="ec_inquiry_model_number" value="<?php echo $this->product->model_number; ?>" />
				</form>
			</div>
			<div class="ec_details_large_popup_close"><input type="button" onclick="ec_details_hide_inquiry_popup( '<?php echo $this->product->product_id; ?>' );" value="x"></div>
		</div>
	</div>
</div>
</div>
<script>
jQuery( '.ec_details_inquiry_popup_<?php echo $this->product->product_id; ?>' ).appendTo( document.body );
</script>
<?php } ?>
<?php /* END INQUIRY OPTIONS */ ?>
<?php 
$rules = array( );
foreach( $this->product->advanced_optionsets as $advanced_option ){
	if( isset( $advanced_option->conditional_logic ) ){
		$rules[$advanced_option->option_to_product_id] = json_decode( $advanced_option->conditional_logic );
	}
} 
?>  
<script>
var ec_advanced_logic_rules_<?php echo $this->product->product_id; ?> = [
	<?php
	if( count( $rules ) > 0 ){
		foreach( $rules as $key => $option_rules ){
			
			if( $option_rules->enabled ){
				echo "{
					'id': " . $key . ",
					'show_field': " . $option_rules->show_field . ",
					'and_rules': '" . $option_rules->and_rules . "',
					'rules':[";
						foreach( $option_rules->rules as $rule ){
							echo "
							{	
									'option_id': " . $rule->option_id . ",
									'operator': '" . $rule->operator . "',
									'optionitem_id': " . $rule->optionitem_id . ",
									'optionitem_value': '" . $rule->optionitem_value . "'
							},";
						}
					echo "
					]
				},";
			}
		}
	}?>
];
</script>
<section class="ec_product_details_page"><?php if( $this->product->has_promotion_text( ) ){ ?>
	<div class="ec_cart_success"><div><?php $this->product->display_promotion_text( ); ?></div></div><?php }?>
	<?php if( $this->product->is_subscription_item && $this->product->trial_period_days > 0 ){ ?>
    <div class="ec_cart_success"><?php echo $GLOBALS['language']->get_text( 'product_page', 'product_page_start_trial_1' ); ?> <?php echo $this->product->trial_period_days; ?> <?php echo $GLOBALS['language']->get_text( 'product_page', 'product_page_start_trial_2' ); ?></div>
    <?php }?>
	<?php if( isset( $_GET['ec_action'] ) && $_GET['ec_action'] == 'product-notify-unsubscribe' && isset( $_GET['unsubscribe_email'] ) && isset( $_GET['unsubscribe_id'] ) ){ 
	global $wpdb;
	$wpdb->query( $wpdb->prepare( "UPDATE ec_product_subscriber SET status = 'unsubscribed' WHERE email = %s AND product_subscriber_id = %d", $_GET['unsubscribe_email'], $_GET['unsubscribe_id'] ) );
	?>
	<div class="ec_cart_success"><?php echo $GLOBALS['language']->get_text( 'product_page', 'product_page_notify_unsubscribe_success' ); ?></div>
    <?php }?>
	<?php 
	/* START PRODUCT BREADCRUMBS */ 
	?><?php if( get_option( 'ec_option_show_breadcrumbs' ) ){ ?>
	<h4 class="ec_details_breadcrumbs" id="ec_breadcrumbs_type1">
    	<a href="<?php echo home_url( ); ?>"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_home_link' ); ?></a> / 
        <a href="<?php echo $this->store_page; ?>"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_store_link' ); ?></a> <?php if( $this->product->menuitems[0]->menulevel1_1_name ){ ?> / 
        <a href="<?php if( !get_option( 'ec_option_use_old_linking_style' ) && $this->product->post_id != "0" ){ 
			echo get_permalink( $this->product->menuitems[0]->menulevel1_1_post_id ); 
		}else{ 
			echo $this->store_page . $this->permalink_divider . "menuid=" . $this->product->menuitems[0]->menulevel1_1_menu_id;
		} ?>"><?php echo $GLOBALS['language']->convert_text( $this->product->menuitems[0]->menulevel1_1_name ); ?></a>
		<?php if( $this->product->menuitems[0]->menulevel2_1_name ){ ?> / 
        <a href="<?php if( !get_option( 'ec_option_use_old_linking_style' ) && $this->product->post_id != "0" ){ 
			echo get_permalink( $this->product->menuitems[0]->menulevel2_1_post_id );
		}else{ 
			echo $this->store_page . $this->permalink_divider . "submenuid=" . $this->product->menuitems[0]->menulevel2_1_menu_id;
		} ?>"><?php echo $GLOBALS['language']->convert_text( $this->product->menuitems[0]->menulevel2_1_name ); ?></a>
		<?php if( $this->product->menuitems[0]->menulevel3_1_name ){ ?> / 
        <a href="<?php if( !get_option( 'ec_option_use_old_linking_style' ) && $this->product->post_id != "0" ){ 
			echo get_permalink( $this->product->menuitems[0]->menulevel3_1_post_id );
		}else{
			echo $this->store_page . $this->permalink_divider . "subsubmenuid=" . $this->product->menuitems[0]->menulevel3_1_menu_id; 
		} ?>"><?php echo $GLOBALS['language']->convert_text( $this->product->menuitems[0]->menulevel3_1_name ); ?></a><?php } } }?>
    </h4>
    <?php } ?><?php 
	/* START MAIN DATA AREA FOR PRODUCT */ ?>
    <div class="ec_details_content">
        <?php /* START MOBILE SIZED CONTENT REGION */ ?>
        <div class="ec_details_mobile_title_area">
        	<h1 class="ec_details_title"><?php echo $this->product->title; ?></h1>
        	<?php if( $this->product->use_customer_reviews ){ ?>
            <div class="ec_details_review_holder">
                <span class="ec_details_review_stars">
                	<?php $rating = $this->product->get_rating( ); ?>
                	<div class="ec_product_details_star_<?php if( $rating > 0.49 ){ ?>on<?php }else{ ?>off<?php }?>"></div>
                    <div class="ec_product_details_star_<?php if( $rating > 1.49 ){ ?>on<?php }else{ ?>off<?php }?>"></div>
                    <div class="ec_product_details_star_<?php if( $rating > 2.49 ){ ?>on<?php }else{ ?>off<?php }?>"></div>
                    <div class="ec_product_details_star_<?php if( $rating > 3.49 ){ ?>on<?php }else{ ?>off<?php }?>"></div>
                    <div class="ec_product_details_star_<?php if( $rating > 4.49 ){ ?>on<?php }else{ ?>off<?php }?>"></div>
                </span>
                <div class="ec_details_reviews"><?php $this->product->display_product_number_reviews(); ?> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_reviews_text' ); ?></div>
			</div>
			<?php }?>
        	<?php 
			$vat_rate_multiplier = 1;
			if( $this->product->login_for_pricing && !$this->product->is_login_for_pricing_valid( ) ){
							  // No Pricing
							  
			}else if( ( $this->product->is_catalog_mode && get_option( 'ec_option_hide_price_seasonal' ) ) || 
					  ( $this->product->is_inquiry_mode && get_option( 'ec_option_hide_price_inquiry' ) ) ){ // NO PRICE SHOWN
			
			}else if( $this->product->vat_rate > 0  && get_option( 'ec_option_show_multiple_vat_pricing' ) ){ 
            
            	$vat_tax_class = new ec_tax( $this->product->price, $this->product->price, $this->product->price, "", "", false, 0, (object) array( 
                    'cart' => array( 
                        (object) array( 
                            'product_id' => $this->product->product_id, 
                            'total_price' => $this->product->price, 
                            'manufacturer_id' => $this->product->manufacturer_id, 
                            'is_taxable' => $this->product->is_taxable, 
                            'vat_enabled' => $this->product->vat_rate 
                        )
                    )
                ) );
                $vat_rate = apply_filters( 'wp_easycart_product_details_vat_rate', $vat_tax_class->vat_rate, $this->product );
                $vat_row = (object) array(
                    'vat_rate'  => $vat_rate,
                    'vat_added' => $vat_tax_class->vat_added,
                    'vat_included' => $vat_tax_class->vat_included
                );
				$vat_rate_multiplier = ( $vat_rate / 100 ) + 1;
			
			?>
            <?php if( get_option( 'ec_option_show_multiple_vat_pricing' ) == '1' ){ ?>
            <div class="ec_details_price ec_details_no_vat_price"><?php $this->product->display_product_pricing_no_vat( ); ?></div>
        	<?php }?>
        	<div class="ec_details_price ec_details_vat_price"><?php $this->product->display_product_pricing_vat( ); ?></div>
        	
			<?php }else{ ?>
        	<div class="ec_details_price ec_details_single_price"><?php $this->product->display_product_list_price(); ?><?php $this->product->display_price(); ?></div>
        	<?php }?>
        	<div class="ec_details_clear"></div>
        </div>
        <?php /* END MOBILE SIZED CONTENT REGION */ ?>
        <?php /* START PRODUCT IMAGES AREA */ ?>
        <div class="ec_details_images">
        <?php if( apply_filters( 'wp_easycart_product_details_show_images', true ) ){ ?>
        	<div class="ec_details_main_image ec_details_main_image_<?php echo $this->product->product_id; ?>"<?php if( get_option( 'ec_option_show_large_popup' ) ){?> onclick="ec_details_show_image_popup( '<?php echo $this->product->model_number; ?>' );"<?php }else{ ?> style="cursor:inherit;"<?php }?>>
            	<?php do_action( 'wp_easycart_product_details_image_holder_pre', $this->product ); ?>
                <img src="<?php echo $this->product->get_first_image_url( ); ?>" alt="<?php echo str_replace( '"', '&quot;', $this->product->title ); ?>" />
				<?php do_action( 'wp_easycart_product_details_image_holder_post', $this->product ); ?>
        	</div>
            
			<?php /* START MAIN IMAGE THUMBNAILS */ ?>
            <?php /* START DISPLAY FOR OPTION ITEM IMAGES USEAGE */ ?>
			<?php 
			if( $this->product->use_optionitem_images ){ ?>
            	<?php
				$optionitem_id_array = array( );
				foreach( $this->product->options->optionset1->optionset as $optionitem ){
					$optionitem_id_array[] = $optionitem->optionitem_id;
				}
				$thumbnails_displayed = 0;
				for( $i=0; $i<count( $this->product->images->imageset ); $i++ ){
					
					if( in_array( $this->product->images->imageset[$i]->optionitem_id, $optionitem_id_array ) ){
					?>
                    <div class="ec_details_thumbnails ec_details_thumbnails_<?php echo $this->product->product_id; ?><?php if( $thumbnails_displayed > 0 ){ ?> ec_inactive<?php }?><?php if( $this->product->images->imageset[$i]->image2 == "" ){ ?> ec_no_thumbnails<?php }?>" id="ec_details_thumbnails_<?php echo $this->product->images->imageset[$i]->optionitem_id; ?>_<?php echo $this->product->product_id; ?>"<?php if( trim( $this->product->images->imageset[$i]->image2 ) == "" ){ ?> style="display:none !important;"<?php }?>>
                        <div class="ec_details_thumbnail ec_details_thumbnail_<?php echo $this->product->product_id; ?> ec_active"><img src="<?php if( substr( $this->product->images->imageset[$i]->image1, 0, 7 ) == 'http://' || substr( $this->product->images->imageset[$i]->image1, 0, 8 ) == 'https://' ){ echo $this->product->images->imageset[$i]->image1; }else{ echo plugins_url( "/wp-easycart-data/products/pics1/" . $this->product->images->imageset[$i]->image1 ); } ?>" alt="<?php echo str_replace( '"', '&quot;', $this->product->title ); ?>" /></div>
                        <?php if( trim( $this->product->images->imageset[$i]->image2 ) != "" ){ ?><div class="ec_details_thumbnail ec_details_thumbnail_<?php echo $this->product->product_id; ?>"><img src="<?php if( substr( $this->product->images->imageset[$i]->image2, 0, 7 ) == 'http://' || substr( $this->product->images->imageset[$i]->image2, 0, 8 ) == 'https://' ){ echo $this->product->images->imageset[$i]->image2; }else{ echo plugins_url( "/wp-easycart-data/products/pics2/" . $this->product->images->imageset[$i]->image2 ); } ?>" /></div><?php } ?>
                        <?php if( trim( $this->product->images->imageset[$i]->image3 ) != "" ){ ?><div class="ec_details_thumbnail ec_details_thumbnail_<?php echo $this->product->product_id; ?>"><img src="<?php if( substr( $this->product->images->imageset[$i]->image3, 0, 7 ) == 'http://' || substr( $this->product->images->imageset[$i]->image3, 0, 8 ) == 'https://' ){ echo $this->product->images->imageset[$i]->image3; }else{ echo plugins_url( "/wp-easycart-data/products/pics3/" . $this->product->images->imageset[$i]->image3 ); } ?>" alt="<?php echo str_replace( '"', '&quot;', $this->product->title ); ?>" /></div><?php } ?>
                        <?php if( trim( $this->product->images->imageset[$i]->image4 ) != "" ){ ?><div class="ec_details_thumbnail ec_details_thumbnail_<?php echo $this->product->product_id; ?>"><img src="<?php if( substr( $this->product->images->imageset[$i]->image4, 0, 7 ) == 'http://' || substr( $this->product->images->imageset[$i]->image4, 0, 8 ) == 'https://' ){ echo $this->product->images->imageset[$i]->image4; }else{ echo plugins_url( "/wp-easycart-data/products/pics4/" . $this->product->images->imageset[$i]->image4 ); } ?>" alt="<?php echo str_replace( '"', '&quot;', $this->product->title ); ?>" /></div><?php } ?>
                        <?php if( trim( $this->product->images->imageset[$i]->image5 ) != "" ){ ?><div class="ec_details_thumbnail ec_details_thumbnail_<?php echo $this->product->product_id; ?>"><img src="<?php if( substr( $this->product->images->imageset[$i]->image5, 0, 7 ) == 'http://' || substr( $this->product->images->imageset[$i]->image5, 0, 8 ) == 'https://' ){ echo $this->product->images->imageset[$i]->image5; }else{ echo plugins_url( "/wp-easycart-data/products/pics5/" . $this->product->images->imageset[$i]->image5 ); } ?>" alt="<?php echo str_replace( '"', '&quot;', $this->product->title ); ?>" /></div><?php } ?>
                    </div>
                    <?php
					
						if( $thumbnails_displayed == 0 ){ ?>
						
                    <div class="ec_details_thumbnails ec_details_thumbnails_<?php echo $this->product->product_id; ?> ec_inactive<?php if( $this->product->images->imageset[$i]->image2 == "" ){ ?> ec_no_thumbnails<?php }?>" id="ec_details_thumbnails_<?php echo $this->product->product_id; ?>"<?php if( trim( $this->product->images->imageset[$i]->image2 ) == "" ){ ?> style="display:none !important;"<?php }?>>
                        <div class="ec_details_thumbnail ec_details_thumbnail_<?php echo $this->product->product_id; ?> ec_active"><img src="<?php echo plugins_url( "/wp-easycart-data/products/pics1/" . $this->product->images->imageset[$i]->image1 ); ?>" alt="<?php echo str_replace( '"', '&quot;', $this->product->title ); ?>" /></div>
                        <?php if( trim( $this->product->images->imageset[$i]->image2 ) != "" ){ ?><div class="ec_details_thumbnail ec_details_thumbnail_<?php echo $this->product->product_id; ?>"><img src="<?php echo plugins_url( "/wp-easycart-data/products/pics2/" . $this->product->images->imageset[$i]->image2 ); ?>" alt="<?php echo str_replace( '"', '&quot;', $this->product->title ); ?>" /></div><?php } ?>
                        <?php if( trim( $this->product->images->imageset[$i]->image3 ) != "" ){ ?><div class="ec_details_thumbnail ec_details_thumbnail_<?php echo $this->product->product_id; ?>"><img src="<?php echo plugins_url( "/wp-easycart-data/products/pics3/" . $this->product->images->imageset[$i]->image3 ); ?>" alt="<?php echo str_replace( '"', '&quot;', $this->product->title ); ?>" /></div><?php } ?>
                        <?php if( trim( $this->product->images->imageset[$i]->image4 ) != "" ){ ?><div class="ec_details_thumbnail ec_details_thumbnail_<?php echo $this->product->product_id; ?>"><img src="<?php echo plugins_url( "/wp-easycart-data/products/pics4/" . $this->product->images->imageset[$i]->image4 ); ?>" alt="<?php echo str_replace( '"', '&quot;', $this->product->title ); ?>" /></div><?php } ?>
                        <?php if( trim( $this->product->images->imageset[$i]->image5 ) != "" ){ ?><div class="ec_details_thumbnail ec_details_thumbnail_<?php echo $this->product->product_id; ?>"><img src="<?php echo plugins_url( "/wp-easycart-data/products/pics5/" . $this->product->images->imageset[$i]->image5 ); ?>" alt="<?php echo str_replace( '"', '&quot;', $this->product->title ); ?>" /></div><?php } ?>
                    </div>
                        
					<?php 
						
						$thumbnails_displayed++;
					
						}
					
					}// Close test for existing option item id (bad data fix)
				
				} //Close for loop of image set
				?>
            <?php 
			/* END DISPLAY FOR OPTION ITEM IMAGES THUMNAILS */
			
			
			/* START DISPLAY FOR BASIC IMAGE THUMBNAILS */
			}else if( trim( $this->product->images->image2 ) != "" ){ ?>
            <div class="ec_details_thumbnails ec_details_thumbnails_<?php echo $this->product->product_id; ?>">
            	<div class="ec_details_thumbnail ec_details_thumbnail_<?php echo $this->product->product_id; ?> ec_active"><img src="<?php echo $this->product->get_first_image_url( ); ?>" alt="<?php echo str_replace( '"', '&quot;', $this->product->title ); ?>" /></div>
            	<div class="ec_details_thumbnail ec_details_thumbnail_<?php echo $this->product->product_id; ?>"><img src="<?php echo $this->product->get_second_image_url( ); ?>" alt="<?php echo str_replace( '"', '&quot;', $this->product->title ); ?>" /></div>
            	<?php if( trim( $this->product->images->image3 ) != "" ){ ?><div class="ec_details_thumbnail ec_details_thumbnail_<?php echo $this->product->product_id; ?>"><img src="<?php echo $this->product->get_third_image_url( ); ?>" alt="<?php echo str_replace( '"', '&quot;', $this->product->title ); ?>" /></div><?php } ?>
            	<?php if( trim( $this->product->images->image4 ) != "" ){ ?><div class="ec_details_thumbnail ec_details_thumbnail_<?php echo $this->product->product_id; ?>"><img src="<?php echo $this->product->get_fourth_image_url( ); ?>" alt="<?php echo str_replace( '"', '&quot;', $this->product->title ); ?>" /></div><?php } ?>
            	<?php if( trim( $this->product->images->image5 ) != "" ){ ?><div class="ec_details_thumbnail ec_details_thumbnail_<?php echo $this->product->product_id; ?>"><img src="<?php echo $this->product->get_fifth_image_url( ); ?>" alt="<?php echo str_replace( '"', '&quot;', $this->product->title ); ?>" /></div><?php } ?>
            </div>
            <?php }?>
            <?php /* END MAIN IMAGE THUMBNAILS */ ?>
            
            <?php /* START IMAGE MAGNIFICATION BOX */ ?>
            <?php if( !$ipad && !$iphone && get_option( 'ec_option_show_magnification' ) ){?>
            <div class="ec_details_magbox ec_details_magbox_<?php echo $this->product->product_id; ?>">
            	<div class="ec_details_magbox_image ec_details_magbox_image_<?php echo $this->product->product_id; ?>" style="background:url( '<?php echo $this->product->get_first_image_url( ); ?>' ) no-repeat"></div>
            </div>
            <?php }?>
            <?php /* END IMAGE MAGNICFICATION BOX */ ?>
            
            <?php /* START PRODUCT IMAGES POPUP AREA */ ?>
            <?php if( get_option( 'ec_option_show_large_popup' ) ){?>
            <div class="ec_details_large_popup" id="ec_details_large_popup_<?php echo $this->product->model_number; ?>">
            	<div class="ec_details_large_popup_content">
                	<div class="ec_details_large_popup_padding">
                    	<div class="ec_details_large_popup_holder">
                            <div class="ec_details_large_popup_main ec_details_large_popup_main_<?php echo $this->product->product_id; ?>"><img src="<?php echo $this->product->get_first_image_url( ); ?>" alt="<?php echo str_replace( '"', '&quot;', $this->product->title ); ?>" /></div>
                            
                            <?php /* SETUP POPUP THUMBNAILS */ ?>
                            <?php if( $this->product->use_optionitem_images ){ ?>
                                <?php
                                for( $i=0; $i<count( $this->product->images->imageset ); $i++ ){
                                    ?>
                                    <div class="ec_details_large_popup_thumbnails ec_details_large_popup_thumbnails_<?php echo $this->product->product_id; ?><?php if( $i > 0 ){ ?> ec_inactive<?php }?>" id="ec_details_large_popup_thumbnails_<?php echo $this->product->images->imageset[$i]->optionitem_id; ?>_<?php echo $this->product->product_id; ?>"<?php if( trim( $this->product->images->imageset[$i]->image2 ) == "" ){ ?> style="display:none;"<?php }?>>
                                        <div class="ec_details_large_popup_thumbnail ec_details_large_popup_thumbnail_<?php echo $this->product->product_id; ?> ec_active"><img src="<?php echo ( ( substr( $this->product->images->imageset[$i]->image1, 0, 7 ) == 'http://' || substr( $this->product->images->imageset[$i]->image1, 0, 8 ) == 'https://' ) ? $this->product->images->imageset[$i]->image1 : plugins_url( "/wp-easycart-data/products/pics1/" . $this->product->images->imageset[$i]->image1 ) ); ?>" alt="<?php echo str_replace( '"', '&quot;', $this->product->title ); ?>" /></div>
                                        <?php if( trim( $this->product->images->imageset[$i]->image2 ) != "" ){ ?><div class="ec_details_large_popup_thumbnail ec_details_large_popup_thumbnail_<?php echo $this->product->product_id; ?>"><img src="<?php echo ( ( substr( $this->product->images->imageset[$i]->image2, 0, 7 ) == 'http://' || substr( $this->product->images->imageset[$i]->image2, 0, 8 ) == 'https://' ) ? $this->product->images->imageset[$i]->image2 : plugins_url( "/wp-easycart-data/products/pics2/" . $this->product->images->imageset[$i]->image2 ) ); ?>" alt="<?php echo str_replace( '"', '&quot;', $this->product->title ); ?>" /></div><?php } ?>
                                        <?php if( trim( $this->product->images->imageset[$i]->image3 ) != "" ){ ?><div class="ec_details_large_popup_thumbnail ec_details_large_popup_thumbnail_<?php echo $this->product->product_id; ?>"><img src="<?php echo ( ( substr( $this->product->images->imageset[$i]->image3, 0, 7 ) == 'http://' || substr( $this->product->images->imageset[$i]->image3, 0, 8 ) == 'https://' ) ? $this->product->images->imageset[$i]->image3 : plugins_url( "/wp-easycart-data/products/pics3/" . $this->product->images->imageset[$i]->image3 ) ); ?>" alt="<?php echo str_replace( '"', '&quot;', $this->product->title ); ?>" /></div><?php } ?>
                                        <?php if( trim( $this->product->images->imageset[$i]->image4 ) != "" ){ ?><div class="ec_details_large_popup_thumbnail ec_details_large_popup_thumbnail_<?php echo $this->product->product_id; ?>"><img src="<?php echo ( ( substr( $this->product->images->imageset[$i]->image4, 0, 7 ) == 'http://' || substr( $this->product->images->imageset[$i]->image4, 0, 8 ) == 'https://' ) ? $this->product->images->imageset[$i]->image4 : plugins_url( "/wp-easycart-data/products/pics4/" . $this->product->images->imageset[$i]->image4 ) ); ?>" alt="<?php echo str_replace( '"', '&quot;', $this->product->title ); ?>" /></div><?php } ?>
                                        <?php if( trim( $this->product->images->imageset[$i]->image5 ) != "" ){ ?><div class="ec_details_large_popup_thumbnail ec_details_large_popup_thumbnail_<?php echo $this->product->product_id; ?>"><img src="<?php echo ( ( substr( $this->product->images->imageset[$i]->image5, 0, 7 ) == 'http://' || substr( $this->product->images->imageset[$i]->image5, 0, 8 ) == 'https://' ) ? $this->product->images->imageset[$i]->image5 : plugins_url( "/wp-easycart-data/products/pics5/" . $this->product->images->imageset[$i]->image5 ) ); ?>" alt="<?php echo str_replace( '"', '&quot;', $this->product->title ); ?>" /></div><?php } ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            <?php }else if( trim( $this->product->images->image2 ) != "" ){ ?>
                            <div class="ec_details_large_popup_thumbnails ec_details_large_popup_thumbnails_<?php echo $this->product->product_id; ?>">
                                <div class="ec_details_large_popup_thumbnail ec_details_large_popup_thumbnail_<?php echo $this->product->product_id; ?>"><img src="<?php echo $this->product->get_first_image_url( ); ?>" alt="<?php echo str_replace( '"', '&quot;', $this->product->title ); ?>" /></div>
                                <div class="ec_details_large_popup_thumbnail ec_details_large_popup_thumbnail_<?php echo $this->product->product_id; ?>"><img src="<?php echo $this->product->get_second_image_url( ); ?>" alt="<?php echo str_replace( '"', '&quot;', $this->product->title ); ?>" /></div>
                                <?php if( trim( $this->product->images->image3 ) != "" ){ ?><div class="ec_details_large_popup_thumbnail ec_details_large_popup_thumbnail_<?php echo $this->product->product_id; ?>"><img src="<?php echo $this->product->get_third_image_url( ); ?>" alt="<?php echo str_replace( '"', '&quot;', $this->product->title ); ?>" /></div><?php } ?>
                                <?php if( trim( $this->product->images->image4 ) != "" ){ ?><div class="ec_details_large_popup_thumbnail ec_details_large_popup_thumbnail_<?php echo $this->product->product_id; ?>"><img src="<?php echo $this->product->get_fourth_image_url( ); ?>" alt="<?php echo str_replace( '"', '&quot;', $this->product->title ); ?>" /></div><?php } ?>
                                <?php if( trim( $this->product->images->image5 ) != "" ){ ?><div class="ec_details_large_popup_thumbnail ec_details_large_popup_thumbnail_<?php echo $this->product->product_id; ?>"><img src="<?php echo $this->product->get_fifth_image_url( ); ?>" alt="<?php echo str_replace( '"', '&quot;', $this->product->title ); ?>" /></div><?php } ?>
                            </div>
                            <?php }?>
                            <?php /* END POPUP THUMBNAIL SETUP */ ?>
                            <div class="ec_details_large_popup_close"><input type="button" onclick="ec_details_hide_large_popup( '<?php echo $this->product->model_number; ?>' );" value="x"></div>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                jQuery( document.getElementById( 'ec_details_large_popup_<?php echo $this->product->model_number; ?>' ) ).appendTo( document.body );
            </script>
            <?php }?>
            <?php /* END PRODUCT IMAGE POPUP AREA */ ?>
            <?php } // Close image show filter ?>
            <?php do_action( 'wp_easycart_product_details_after_left_content_area', $this->product->product_id ); ?>
        </div>
        <?php /* END PRODUCT IMAGES AREA */ ?>
        
        
        <div class="ec_details_right">
        	<?php if( $this->product->inquiry_url == "" ){ // Regular Add to Cart Form ?>
        	
            <form action="<?php echo $this->cart_page; ?>" method="POST" enctype="multipart/form-data" class="ec_add_to_cart_form">
            <?php if( $this->product->is_subscription_item ){ // && !class_exists( "ec_stripe" ) ){ ?>
            <input type="hidden" name="ec_cart_form_action" value="subscribe_v3" />
            <?php }else{ ?>
            <input type="hidden" name="ec_cart_form_action" value="add_to_cart_v3" />
            <?php }?>
            <input type="hidden" name="product_id" value="<?php echo $this->product->product_id; ?>"  />
            
			<?php }else{ // Custom Inquiry Form ?>
            
            <form action="<?php echo $this->product->inquiry_url; ?>" method="GET" enctype="multipart/form-data" class="ec_add_to_cart_form">
            <input type="hidden" name="model_number" value="<?php echo $this->product->model_number; ?>"  />
            
			<?php }?>
            
        	<?php /* START TOP PRODUCT DATA */ ?>
        	<?php if( get_option( 'ec_option_show_breadcrumbs' ) ){ ?>
        	<h4 class="ec_details_breadcrumbs ec_small" id="ec_breadcrumbs_type2">
            <a href="<?php echo home_url( ); ?>"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_home_link' ); ?></a> / 
            <a href="<?php echo $this->store_page; ?>"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_store_link' ); ?></a> 
            <?php if( $this->product->menuitems[0]->menulevel1_1_name ){ ?> / 
            <a href="<?php if( !get_option( 'ec_option_use_old_linking_style' ) && $this->product->post_id != "0" ){ 
                echo get_permalink( $this->product->menuitems[0]->menulevel1_1_post_id ); 
            }else{ 
                echo $this->store_page . $this->permalink_divider . "menuid=" . $this->product->menuitems[0]->menulevel1_1_menu_id;
            } ?>"><?php echo $GLOBALS['language']->convert_text( $this->product->menuitems[0]->menulevel1_1_name ); ?></a>
            <?php if( $this->product->menuitems[0]->menulevel2_1_name ){ ?> / 
            <a href="<?php if( !get_option( 'ec_option_use_old_linking_style' ) && $this->product->post_id != "0" ){ 
                echo get_permalink( $this->product->menuitems[0]->menulevel2_1_post_id );
            }else{ 
                echo $this->store_page . $this->permalink_divider . "submenuid=" . $this->product->menuitems[0]->menulevel2_1_menu_id;
            } ?>"><?php echo $GLOBALS['language']->convert_text( $this->product->menuitems[0]->menulevel2_1_name ); ?></a>
            <?php if( $this->product->menuitems[0]->menulevel3_1_name ){ ?> / 
            <a href="<?php if( !get_option( 'ec_option_use_old_linking_style' ) && $this->product->post_id != "0" ){ 
                echo get_permalink( $this->product->menuitems[0]->menulevel3_1_post_id );
            }else{
                echo $this->store_page . $this->permalink_divider . "subsubmenuid=" . $this->product->menuitems[0]->menulevel3_1_menu_id; 
            } ?>"><?php echo $GLOBALS['language']->convert_text( $this->product->menuitems[0]->menulevel3_1_name ); ?></a><?php } } }?>
            </h4>
            <?php }?>
            
            <h1 class="ec_details_title"><?php echo $this->product->title; ?></h1>
            <?php do_action( 'wp_easycart_product_details_after_title', $this->product->product_id ); ?>
            <div class="ec_title_divider"></div>
            <?php do_action( 'wp_easycart_product_details_after_title_divider', $this->product->product_id ); ?>
            <?php if( $this->product->login_for_pricing && !$this->product->is_login_for_pricing_valid( ) ){
							  // No Pricing
							  
			}else if( ( $this->product->is_catalog_mode && get_option( 'ec_option_hide_price_seasonal' ) ) || 
					  ( $this->product->is_inquiry_mode && get_option( 'ec_option_hide_price_inquiry' ) ) ){ // NO PRICE SHOWN
			
			}else if( $this->product->vat_rate > 0  && get_option( 'ec_option_show_multiple_vat_pricing' ) ){ ?>
            
            <?php if( get_option( 'ec_option_show_multiple_vat_pricing' ) == '1' ){ ?>
            <div class="ec_details_price ec_details_no_vat_price"><?php $this->product->display_product_pricing_no_vat( ); ?></div>
        	<?php }?>
            <div class="ec_details_price ec_details_vat_price"><?php $this->product->display_product_pricing_vat( ); ?></div>
        	
			<?php }else{ ?>
            <div class="ec_details_price ec_details_single_price"><?php $this->product->display_product_list_price(); ?><?php $this->product->display_price(); ?></div>
            <?php }?>
            <?php if( $this->product->use_customer_reviews ){ ?>
            <div class="ec_details_rating">
				<?php $rating = $this->product->get_rating( ); ?>
                <div class="ec_product_details_star_<?php if( $rating > 0.49 ){ ?>on<?php }else{ ?>off<?php }?>"></div>
                <div class="ec_product_details_star_<?php if( $rating > 1.49 ){ ?>on<?php }else{ ?>off<?php }?>"></div>
                <div class="ec_product_details_star_<?php if( $rating > 2.49 ){ ?>on<?php }else{ ?>off<?php }?>"></div>
                <div class="ec_product_details_star_<?php if( $rating > 3.49 ){ ?>on<?php }else{ ?>off<?php }?>"></div>
                <div class="ec_product_details_star_<?php if( $rating > 4.49 ){ ?>on<?php }else{ ?>off<?php }?>"></div>
            </div>
            <?php }?>
            <?php if( get_option( 'ec_option_show_model_number' ) ){ ?>
            <div class="ec_details_model_number"><?php echo ucwords( $GLOBALS['language']->get_text( 'product_details', 'product_details_model_number' ) ); ?>: <?php echo $this->product->model_number; ?></div>
            <?php }?>
            <div class="ec_details_description"><?php echo nl2br( stripslashes( $this->product->short_description ) ); ?></div>
            <?php do_action( 'wp_easycart_product_details_after_description', $this->product->product_id ); ?>
            <?php /* GIFT CARD OPTIONS */ ?>
            <?php if( $this->product->is_giftcard ){ ?>
			<div class="ec_details_options">
            	<div class="ec_details_option_row_error ec_giftcard_error" id="ec_details_giftcard_error_<?php echo $this->product->product_id; ?>"><?php echo $GLOBALS['language']->get_text( 'ec_errors', 'missing_gift_card_options' ); ?></div>
                <div class="ec_details_option_row">
                	<div class="ec_details_option_label"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_gift_card_recipient_name' ); ?></div>
                	<div class="ec_details_option_data"><input type="text" name="ec_giftcard_to_name" id="ec_giftcard_to_name_<?php echo $this->product->product_id; ?>" value="" /></div>
                </div>
            
                <div class="ec_details_option_row">
                	<div class="ec_details_option_label"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_gift_card_recipient_email' ); ?></div>
                	<div class="ec_details_option_data"><input type="text" name="ec_giftcard_to_email" id="ec_giftcard_to_email_<?php echo $this->product->product_id; ?>" value="" /></div>
                </div>
            
                <div class="ec_details_option_row">
                	<div class="ec_details_option_label"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_gift_card_sender_name' ); ?></div>
                	<div class="ec_details_option_data"><input type="text" name="ec_giftcard_from_name" id="ec_giftcard_from_name_<?php echo $this->product->product_id; ?>" value="" /></div>
                </div>
            
                <div class="ec_details_option_row">
                	<div class="ec_details_option_label"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_gift_card_message' ); ?></div>
                	<div class="ec_details_option_data"><textarea name="ec_giftcard_message" id="ec_giftcard_message_<?php echo $this->product->product_id; ?>"></textarea></div>
                </div>
            </div>
            <?php }?>
            <?php /* END GIFT CARD OPTIONS */ ?>
            
            <?php /* DONATION OPTIONS */ ?>
            <?php if( $this->product->is_donation ){ ?>
            <div class="ec_details_options">
            	<div class="ec_details_option_row_error ec_donation_error" id="ec_details_donation_error_<?php echo $this->product->product_id; ?>"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_donation_error' ) . " " . $GLOBALS['currency']->get_currency_display( $this->product->price ); ?>.</div>
                <div class="ec_details_option_row">
                	<div class="ec_details_option_label"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_donation_amount' ); ?></div>
                	<div class="ec_details_option_data"><input type="number" step=".01" min="<?php echo $GLOBALS['currency']->get_number_only( $this->product->price ); ?>" name="ec_donation_amount" id="ec_donation_amount_<?php echo $this->product->product_id; ?>" value="<?php echo $GLOBALS['currency']->get_number_only( $this->product->price ); ?>" /></div>
                </div>
            </div>
            <?php } ?>
            <?php /* END DONATION OPTIONS */ ?>
            
			<?php if( $this->product->login_for_pricing && !$this->product->is_login_for_pricing_valid( ) ){
					// No Pricing
			}else if( !$this->product->using_role_price && isset( $this->product->pricetiers[0] ) && count( $this->product->pricetiers[0] ) > 1 ){ ?>
            <ul class="ec_details_tiers">
            	<?php 
				for( $pricetier_i=0; $pricetier_i < count( $this->product->pricetiers ); $pricetier_i++ ){
					
					$tier_price = $GLOBALS['currency']->get_currency_display( $this->product->pricetiers[$pricetier_i][0] );
					
					$tier_quantity_start = (int) $this->product->pricetiers[$pricetier_i][1];
					if( $pricetier_i + 1 < count( $this->product->pricetiers ) )
						$tier_quantity_end = (int) $this->product->pricetiers[$pricetier_i+1][1] - 1;
					else
						$tier_quantity_end = -1;
					
					if( $tier_quantity_end == -1 ){
						$tier_quantity_text = $tier_quantity_start . " " . $GLOBALS['language']->get_text( 'product_details', 'product_details_tier_or_more' );
					}else if( $tier_quantity_start == $tier_quantity_end  ){
						$tier_quantity_text = $tier_quantity_start;
					}else{
						$tier_quantity_text = $tier_quantity_start . "-" . $tier_quantity_end;
					}
					?>
            	<li><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_tier_buy' ); ?> <?php echo $tier_quantity_text; ?> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_tier_buy_at' ); ?> <strong><?php echo $tier_price; ?></strong> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_tier_each' ); ?></li>
                <?php }?>
            </ul>
            <?php }
			/* END TOP PRODUCT DATA */
			?>
            
            <?php /* PRODUCT BASIC OPTIONS */ 
			if( !$this->product->is_inquiry_mode || $this->product->inquiry_url != ""){
			$has_quantity_grid = false;
			?>
            <?php if( $this->product->has_options && !$this->product->use_advanced_optionset ){ ?>
            <div class="ec_details_options">
            <?php 
			$optionsets = array( $this->product->options->optionset1, $this->product->options->optionset2, $this->product->options->optionset3, $this->product->options->optionset4, $this->product->options->optionset5 );
			
			for( $i=0; $i<5; $i++ ){ ?>
            
            	<?php
				/* START IMAGE SWATCHES AREA */
					if( count( $optionsets[$i]->optionset ) > 0 && $optionsets[$i]->option_type == 'basic-swatch' && $optionsets[$i]->optionset[0]->optionitem_icon != "" ){ ?>
					
					<div class="ec_details_option_row_error ec_option<?php echo ( $i+1 ); ?>_<?php echo $this->product->product_id; ?>" id="ec_details_option_row_error_<?php echo $optionsets[$i]->option_id; ?>_<?php echo $this->product->product_id; ?>"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_missing_option' ); ?> <?php echo $optionsets[$i]->option_label; ?></div>
					<input type="hidden" name="ec_option<?php echo ($i+1); ?>" id="ec_option<?php echo ($i+1); ?>_<?php echo $this->product->product_id; ?>" value="0" />
					<div class="ec_details_option_row">
						<div class="ec_details_option_label"><?php echo $optionsets[$i]->option_label; ?><span class="ec_details_option_label_selected"></span></div>
						<ul class="ec_details_swatches">
						<?php
						for( $j=0; $j<count( $optionsets[$i]->optionset ); $j++ ){
							// Check the in stock status for this option item
							if( $this->product->allow_backorders ){
								$optionitem_in_stock = true;
							}else if( $this->product->use_optionitem_quantity_tracking && ( $i > 0 || $this->product->option1quantity[$optionsets[$i]->optionset[$j]->optionitem_id] <= 0 ) ){
								$optionitem_in_stock = false;
							}else{
								$optionitem_in_stock = true;
							}
						?>
						<li class="ec_details_swatch ec_option<?php echo ($i+1); ?> ec_option<?php echo ($i+1); ?>_<?php echo $this->product->product_id; ?><?php if( $optionitem_in_stock ){ ?> ec_active <?php }?><?php if( $optionsets[$i]->optionset[$j]->optionitem_initially_selected || ( isset( $optionsets[$i]->option_meta['url_var'] ) && $optionsets[$i]->option_meta['url_var'] != '' && isset( $_GET[$optionsets[$i]->option_meta['url_var']] ) && strtolower( $_GET[$optionsets[$i]->option_meta['url_var']] ) == strtolower( $optionsets[$i]->optionset[$j]->optionitem_name ) ) || ( isset( $_GET['o'.$optionsets[$i]->optionset[$j]->option_id] ) && $_GET['o'.$optionsets[$i]->optionset[$j]->option_id] == $optionsets[$i]->optionset[$j]->optionitem_name ) ){ ?> ec_selected<?php }?>" data-optionitem-id="<?php echo $optionsets[$i]->optionset[$j]->optionitem_id; ?>"<?php if( $this->product->use_optionitem_quantity_tracking && $i == 0 ){ ?> data-optionitem-quantity="<?php echo $this->product->option1quantity[$optionsets[$i]->optionset[$j]->optionitem_id]; ?>"<?php }?> data-optionitem-price="<?php if( $optionsets[$i]->optionset[$j]->optionitem_price != "" ){ echo $optionsets[$i]->optionset[$j]->optionitem_price; }else{ echo "0.00"; } ?>" data-optionitem-price-onetime="<?php if( isset( $optionsets[$i]->optionset[$j]->optionitem_price_onetime ) && $optionsets[$i]->optionset[$j]->optionitem_price_onetime != "" ){ echo $optionsets[$i]->optionset[$j]->optionitem_price_onetime; }else{ echo "0.00"; } ?>" data-optionitem-price-override="<?php if( isset( $optionsets[$i]->optionset[$j]->optionitem_price_override ) && $optionsets[$i]->optionset[$j]->optionitem_price_override != "" ){ echo $optionsets[$i]->optionset[$j]->optionitem_price_override; }else{ echo "-1.00"; } ?>" data-optionitem-price-multiplier="<?php echo $optionsets[$i]->optionset[$j]->optionitem_price_multiplier; ?>"><img src="<?php if( substr( $optionsets[$i]->optionset[$j]->optionitem_icon, 0, 7 ) == 'http://' || substr( $optionsets[$i]->optionset[$j]->optionitem_icon, 0, 8 ) == 'https://' ){ echo $optionsets[$i]->optionset[$j]->optionitem_icon; }else{ echo plugins_url( "/wp-easycart-data/products/swatches/" . $optionsets[$i]->optionset[$j]->optionitem_icon ); } ?>" title="<?php echo str_replace( '"', '&quot;', $optionsets[$i]->optionset[$j]->optionitem_name ); ?><?php if( $optionsets[$i]->optionset[$j]->optionitem_price > 0 ){ ?> ( +<?php echo $GLOBALS['currency']->get_currency_display( $optionsets[$i]->optionset[$j]->optionitem_price ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ); ?> )<?php }else if( $optionsets[$i]->optionset[$j]->optionitem_price < 0 ){ ?> ( <?php echo $GLOBALS['currency']->get_currency_display( $optionsets[$i]->optionset[$j]->optionitem_price ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ); ?> )<?php }else if( isset( $optionsets[$i]->optionset[$j]->optionitem_price_onetime ) && $optionsets[$i]->optionset[$j]->optionitem_price_onetime > 0 ){ ?> ( +<?php echo $GLOBALS['currency']->get_currency_display( $optionsets[$i]->optionset[$j]->optionitem_price_onetime ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ); ?> )<?php }else if( isset( $optionsets[$i]->optionset[$j]->optionitem_price_onetime ) && $optionsets[$i]->optionset[$j]->optionitem_price_onetime < 0 ){ ?> ( <?php echo $GLOBALS['currency']->get_currency_display( $optionsets[$i]->optionset[$j]->optionitem_price_onetime ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ); ?> )<?php }else if( isset( $optionsets[$i]->optionset[$j]->optionitem_price_override ) && $optionsets[$i]->optionset[$j]->optionitem_price_override > -1 ){ ?> ( <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ); ?> <?php echo $GLOBALS['currency']->get_currency_display( $optionsets[$i]->optionset[$j]->optionitem_price_override ); ?> )<?php }?>" /></li>
						<?php
						}
						?>
						</ul>
						<div class="ec_option_loading" id="ec_option_loading_<?php echo ($i+1); ?>_<?php echo $this->product->product_id; ?>"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_loading_options' ); ?></div>
					</div>
					<?php
					
					// HTML Swatches
					}else if( count( $optionsets[$i]->optionset ) > 0 && $optionsets[$i]->option_type == 'basic-swatch' ){ ?>
					
					<div class="ec_details_option_row_error ec_option<?php echo ( $i+1 ); ?>_<?php echo $this->product->product_id; ?>" id="ec_details_option_row_error_<?php echo $optionsets[$i]->option_to_product_id; ?>_<?php echo $this->product->product_id; ?>"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_missing_option' ); ?> <?php echo $optionsets[$i]->option_label; ?></div>
					<input type="hidden" name="ec_option<?php echo ($i+1); ?>" id="ec_option<?php echo ($i+1); ?>_<?php echo $this->product->product_id; ?>" value="0" />
					<div class="ec_details_option_row">
						<div class="ec_details_option_label"><?php echo $optionsets[$i]->option_label; ?></div>
						<ul class="ec_details_swatches ec_details_html_swatches">
						<?php
						for( $j=0; $j<count( $optionsets[$i]->optionset ); $j++ ){
							// Check the in stock status for this option item
							if( $this->product->allow_backorders ){
								$optionitem_in_stock = true;
							}else if( $this->product->use_optionitem_quantity_tracking && ( $i > 0 || $this->product->option1quantity[$optionsets[$i]->optionset[$j]->optionitem_id] <= 0 ) ){
								$optionitem_in_stock = false;
							}else{
								$optionitem_in_stock = true;
							}
						?>
						<li class="ec_details_swatch wpeasycart-html-swatch ec_option<?php echo ($i+1); ?> ec_option<?php echo ($i+1); ?>_<?php echo $this->product->product_id; ?><?php if( $optionitem_in_stock ){ ?> ec_active <?php }?><?php if( $optionsets[$i]->optionset[$j]->optionitem_initially_selected || ( isset( $optionsets[$i]->option_meta['url_var'] ) && $optionsets[$i]->option_meta['url_var'] != '' && isset( $_GET[$optionsets[$i]->option_meta['url_var']] ) && strtolower( $_GET[$optionsets[$i]->option_meta['url_var']] ) == strtolower( $optionsets[$i]->optionset[$j]->optionitem_name ) ) || ( isset( $_GET['o'.$optionsets[$i]->optionset[$j]->option_id] ) && $_GET['o'.$optionsets[$i]->optionset[$j]->option_id] == $optionsets[$i]->optionset[$j]->optionitem_name ) ){ ?> ec_selected<?php }?>" data-optionitem-id="<?php echo $optionsets[$i]->optionset[$j]->optionitem_id; ?>"<?php if( $this->product->use_optionitem_quantity_tracking && $i == 0 ){ ?> data-optionitem-quantity="<?php echo $this->product->option1quantity[$optionsets[$i]->optionset[$j]->optionitem_id]; ?>"<?php }?> data-optionitem-price="<?php if( $optionsets[$i]->optionset[$j]->optionitem_price != "" ){ echo $optionsets[$i]->optionset[$j]->optionitem_price; }else{ echo "0.00"; } ?>" data-optionitem-price-onetime="<?php if( isset( $optionsets[$i]->optionset[$j]->optionitem_price_onetime ) && $optionsets[$i]->optionset[$j]->optionitem_price_onetime != "" ){ echo $optionsets[$i]->optionset[$j]->optionitem_price_onetime; }else{ echo "0.00"; } ?>" data-optionitem-price-override="<?php if( isset( $optionsets[$i]->optionset[$j]->optionitem_price_override ) && $optionsets[$i]->optionset[$j]->optionitem_price_override != "" ){ echo $optionsets[$i]->optionset[$j]->optionitem_price_override; }else{ echo "-1.00"; } ?>" data-optionitem-price-multiplier="<?php echo $optionsets[$i]->optionset[$j]->optionitem_price_multiplier; ?>" title="<?php if( $optionsets[$i]->optionset[$j]->optionitem_price > 0 ){ ?> ( +<?php echo $GLOBALS['currency']->get_currency_display( $optionsets[$i]->optionset[$j]->optionitem_price ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ); ?> )<?php }else if( $optionsets[$i]->optionset[$j]->optionitem_price < 0 ){ ?> ( <?php echo $GLOBALS['currency']->get_currency_display( $optionsets[$i]->optionset[$j]->optionitem_price ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ); ?> )<?php }else if( isset( $optionsets[$i]->optionset[$j]->optionitem_price_onetime ) && $optionsets[$i]->optionset[$j]->optionitem_price_onetime > 0 ){ ?> ( +<?php echo $GLOBALS['currency']->get_currency_display( $optionsets[$i]->optionset[$j]->optionitem_price_onetime ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ); ?> )<?php }else if( isset( $optionsets[$i]->optionset[$j]->optionitem_price_onetime ) && $optionsets[$i]->optionset[$j]->optionitem_price_onetime < 0 ){ ?> ( <?php echo $GLOBALS['currency']->get_currency_display( $optionsets[$i]->optionset[$j]->optionitem_price_onetime ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ); ?> )<?php }else if( isset( $optionsets[$i]->optionset[$j]->optionitem_price_override ) && $optionsets[$i]->optionset[$j]->optionitem_price_override > -1 ){ ?> ( <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ); ?> <?php echo $GLOBALS['currency']->get_currency_display( $optionsets[$i]->optionset[$j]->optionitem_price_override ); ?> )<?php }?>"><?php echo $optionsets[$i]->optionset[$j]->optionitem_name; ?></li>
						<?php
						}
						?>
						</ul>
						<div class="ec_option_loading" id="ec_option_loading_<?php echo ($i+1); ?>_<?php echo $this->product->product_id; ?>"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_loading_options' ); ?></div>
					</div>
					<?php
					/* END SWATCHES AREA */
				
				/* START COMBO BOX AREA */
				}else if( count( $optionsets[$i]->optionset ) > 0 && $optionsets[$i]->optionset[0]->optionitem_name != "" ){ ?>
                <div class="ec_details_option_row_error ec_option<?php echo ( $i+1 ); ?>_<?php echo $this->product->product_id; ?>" id="ec_details_option_row_error_<?php echo $optionsets[$i]->option_id; ?>_<?php echo $this->product->product_id; ?>"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_missing_option' ); ?> <?php echo $optionsets[$i]->option_label; ?></div>
                
                <div class="ec_details_option_row">
					<select name="ec_option<?php echo ($i+1); ?>" id="ec_option<?php echo ($i+1); ?>" class="ec_details_combo ec_option<?php echo ($i+1); ?>_<?php echo $this->product->product_id; ?> ec_option<?php echo ($i+1); ?><?php if( $this->product->use_optionitem_quantity_tracking && $i > 0 ){ ?> ec_inactive<?php }?>"<?php if( $this->product->use_optionitem_quantity_tracking && $i > 0 ){ ?> disabled="disabled"<?php }?>>
                    <option value="0"<?php if( $this->product->use_optionitem_quantity_tracking && $i == 0 ){ ?> data-optionitem-quantity="<?php echo $this->product->stock_quantity; ?>"<?php }?> data-optionitem-price="0.00" data-optionitem-price-onetime="0.00" data-optionitem-price-override="-1" data-optionitem-price-multiplier="-1.00"><?php echo $optionsets[$i]->option_label; ?></option>
					<?php
                    for( $j=0; $j<count( $optionsets[$i]->optionset ); $j++ ){
						// Check the in stock status for this option item
						if( $this->product->allow_backorders ){
							$optionitem_in_stock = true;
						}else if( $this->product->use_optionitem_quantity_tracking && ( $i > 0 || $this->product->option1quantity[$optionsets[$i]->optionset[$j]->optionitem_id] <= 0 ) ){
							$optionitem_in_stock = false;
						}else{
							$optionitem_in_stock = true;
						}
					?>
                    <?php if( !$this->product->use_optionitem_quantity_tracking || $i != 0 || $this->product->option1quantity[$optionsets[$i]->optionset[$j]->optionitem_id] > 0 || $optionitem_in_stock ){ ?> 
					<option value="<?php echo $optionsets[$i]->optionset[$j]->optionitem_id; ?>"<?php if( $this->product->use_optionitem_quantity_tracking && $i == 0 ){ ?> data-optionitem-quantity="<?php echo $this->product->option1quantity[$optionsets[$i]->optionset[$j]->optionitem_id]; ?>"<?php }?> data-optionitem-price="<?php if( $optionsets[$i]->optionset[$j]->optionitem_price != "" ){ echo $optionsets[$i]->optionset[$j]->optionitem_price; }else{ echo "0.00"; } ?>" data-optionitem-price-onetime="<?php if( $optionsets[$i]->optionset[$j]->optionitem_price_onetime != "" ){ echo $optionsets[$i]->optionset[$j]->optionitem_price_onetime; }else{ echo "0.00"; } ?>" data-optionitem-price-override="<?php if( isset( $optionsets[$i]->optionset[$j]->optionitem_price_override ) && $optionsets[$i]->optionset[$j]->optionitem_price_override != "" ){ echo $optionsets[$i]->optionset[$j]->optionitem_price_override; }else{ echo "-1.00"; } ?>" data-optionitem-price-multiplier="<?php echo $optionsets[$i]->optionset[$j]->optionitem_price_multiplier; ?>"<?php if( $optionsets[$i]->optionset[$j]->optionitem_initially_selected || ( isset( $optionsets[$i]->option_meta['url_var'] ) && $optionsets[$i]->option_meta['url_var'] != '' && isset( $_GET[$optionsets[$i]->option_meta['url_var']] ) && strtolower( $_GET[$optionsets[$i]->option_meta['url_var']] ) == strtolower( $optionsets[$i]->optionset[$j]->optionitem_name ) ) || ( isset( $_GET['o'.$optionsets[$i]->optionset[$j]->option_id] ) && $_GET['o'.$optionsets[$i]->optionset[$j]->option_id] == $optionsets[$i]->optionset[$j]->optionitem_name ) ){ ?> selected="selected"<?php }?>><?php echo $optionsets[$i]->optionset[$j]->optionitem_name; ?><?php if( $optionsets[$i]->optionset[$j]->optionitem_price > 0 ){ echo ' (+' . $GLOBALS['currency']->get_currency_display( $optionsets[$i]->optionset[$j]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')'; }else if( $optionsets[$i]->optionset[$j]->optionitem_price < 0 ){ echo ' (' . $GLOBALS['currency']->get_currency_display( $optionsets[$i]->optionset[$j]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')'; }else if( isset( $optionsets[$i]->optionset[$j]->optionitem_price_onetime ) && $optionsets[$i]->optionset[$j]->optionitem_price_onetime > 0 ){ echo ' (+' . $GLOBALS['currency']->get_currency_display( $optionsets[$i]->optionset[$j]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')'; }else if( isset( $optionsets[$i]->optionset[$j]->optionitem_price_onetime ) && $optionsets[$i]->optionset[$j]->optionitem_price_onetime < 0 ){ echo ' (' . $GLOBALS['currency']->get_currency_display( $optionsets[$i]->optionset[$j]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')'; }else if( isset( $optionsets[$i]->optionset[$j]->optionitem_price_override ) && $optionsets[$i]->optionset[$j]->optionitem_price_override > -1 ){ echo ' (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ) . $GLOBALS['currency']->get_currency_display( $optionsets[$i]->optionset[$j]->optionitem_price_override ) . ')'; } ?></option>
                    <?php }?>	
					<?php
                    }
					?>
                    </select>
                    <div class="ec_option_loading" id="ec_option_loading_<?php echo ($i+1); ?>_<?php echo $this->product->product_id; ?>"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_loading_options' ); ?></div>
                </div>
                <?php
				}
				/* END COMBO BOX AREA*/
			}
			?>
            </div>
            <?php } ?>
            <?php /* END BASIC OPTIONS */ ?>
            
            <?php /* PRODUCT ADVANCED OPTIONS */ ?>
            <?php 
			
			$add_price_grid = 0;
			$add_order_price_grid = 0;
			$override_price_grid = -1;
			if( $this->product->use_advanced_optionset && count( $this->product->advanced_optionsets ) > 0 ){ ?>
            <div class="ec_details_options">
            	<?php 
				foreach( $this->product->advanced_optionsets as $optionset ){
					$optionitems = $this->product->get_advanced_optionitems( $optionset->option_id );
				?>
                <?php 
				if( $optionset->option_required ){ 
				?>
                <div class="ec_details_option_row_error" id="ec_details_option_row_error_<?php echo $optionset->option_to_product_id; ?>_<?php echo $this->product->product_id; ?>"><?php echo $optionset->option_error_text; ?></div>
                <?php
				}
				?>
                <div class="ec_details_option_row ec_option_type_<?php echo $optionset->option_type; ?>" data-option-id="<?php echo $optionset->option_to_product_id; ?>" data-option-required="<?php echo $optionset->option_required; ?>" data-product-id="<?php echo $this->product->product_id; ?>">
                	<?php if( $optionset->option_type != "combo" ){ ?>
                    <div class="ec_details_option_label"><?php echo $optionset->option_label; ?><?php if( $optionset->option_type == "swatch" ){ ?><span class="ec_details_option_label_selected"></span><?php }?></div>
                    <?php }?>
                    <div class="ec_details_option_data">
                	<?php
					/* START ADVANCED CHECBOX TYPE */
					if( $optionset->option_type == "checkbox" ){
					?>
                    
                    	<?php
						foreach( $optionitems as $optionitem ){
						?>
                        	
                            <div class="ec_details_checkbox_row"><input type="checkbox" class="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>" name="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $optionitem->optionitem_id; ?>" value="<?php echo $optionitem->optionitem_name; ?>" data-product-id="<?php echo $this->product->product_id; ?>" data-optionitem-price="<?php echo $optionitem->optionitem_price; ?>" data-optionitem-price-onetime="<?php echo $optionitem->optionitem_price_onetime; ?>" data-optionitem-price-override="<?php echo $optionitem->optionitem_price_override; ?>" data-optionitem-price-multiplier="<?php echo $optionitem->optionitem_price_multiplier; ?>"<?php if( $optionitem->optionitem_initially_selected || ( isset( $optionset->option_meta['url_var'] ) && $optionset->option_meta['url_var'] != '' && isset( $_GET[$optionset->option_meta['url_var']] ) && strtolower( $_GET[$optionset->option_meta['url_var']] ) == strtolower( $optionitem->optionitem_name ) ) || ( isset( $_GET['o'.$optionset->option_id] ) && $_GET['o'.$optionset->option_id] == $optionitem->optionitem_name ) ){ ?> checked="checked"<?php }?> /> <?php echo $optionitem->optionitem_name; ?><?php if( $optionitem->optionitem_price > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitem->optionitem_price < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitem->optionitem_price_onetime > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( $optionitem->optionitem_price_onetime < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( isset( $optionitem->optionitem_price_override ) && $optionitem->optionitem_price_override > -1 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price_override ) . ')</span>'; } ?></div>
                            
                        <?php
						}
						?>
                        
                    <?php
					
					/* START ADVANCED COMBO TYPE */
					}else if( $optionset->option_type == "combo" ){
					?>
                    	<select name="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>" id="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $this->product->product_id; ?>" data-option-id="<?php echo $optionset->option_id; ?>">
                        <option value="0" data-optionitem-price="0.000" data-optionitem-price-onetime="0.000" data-optionitem-price-override="-1.000" data-optionitem-price-multiplier="-1.000"><?php echo $optionset->option_label; ?></option>
                    	<?php
						foreach( $optionitems as $optionitem ){
						?>
                        	
                            <option value="<?php echo $optionitem->optionitem_id; ?>" data-product-id="<?php echo $this->product->product_id; ?>" data-optionitem-price="<?php echo $optionitem->optionitem_price; ?>" data-optionitem-price-onetime="<?php echo $optionitem->optionitem_price_onetime; ?>" data-optionitem-price-override="<?php echo $optionitem->optionitem_price_override; ?>" data-optionitem-price-multiplier="<?php echo $optionitem->optionitem_price_multiplier; ?>"<?php if( $optionitem->optionitem_initially_selected || ( isset( $optionset->option_meta['url_var'] ) && $optionset->option_meta['url_var'] != '' && isset( $_GET[$optionset->option_meta['url_var']] ) && strtolower( $_GET[$optionset->option_meta['url_var']] ) == strtolower( $optionitem->optionitem_name ) ) || ( isset( $_GET['o'.$optionset->option_id] ) && $_GET['o'.$optionset->option_id] == $optionitem->optionitem_name ) ){ ?> selected="selected"<?php }?>><?php echo $optionitem->optionitem_name; ?><?php if( $optionitem->optionitem_price > 0 ){ echo ' (+' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')'; }else if( $optionitem->optionitem_price < 0 ){ echo ' (' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')'; }else if( $optionitem->optionitem_price_onetime > 0 ){ echo ' (+' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')'; }else if( $optionitem->optionitem_price_onetime < 0 ){ echo ' (' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')'; }else if( isset( $optionitem->optionitem_price_override ) && $optionitem->optionitem_price_override > -1 ){ echo ' (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price_override ) . ')'; } ?></option>
                            
                        <?php
						}
						?>
                    	</select>
                    <?php
					
					/* START ADVANCED DATE TYPE*/
					}else if( $optionset->option_type == "date" ){
					?>
                    
                    	<input type="text" value="<?php if( isset( $_GET['o'.$optionset->option_id] ) || ( isset( $optionset->option_meta['url_var'] ) && $optionset->option_meta['url_var'] != '' && isset( $_GET[$optionset->option_meta['url_var']] ) ) ){ echo htmlspecialchars( ( ( isset( $_GET['o'.$optionset->option_id] ) ) ? $_GET['o'.$optionset->option_id] : $_GET[$optionset->option_meta['url_var']] ), ENT_QUOTES ); } ?>" class="ec_is_datepicker" name="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>" id="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $this->product->product_id; ?>" data-product-id="<?php echo $this->product->product_id; ?>" data-optionitem-price="<?php echo $optionitems[0]->optionitem_price; ?>" data-optionitem-price-onetime="<?php echo $optionitems[0]->optionitem_price_onetime; ?>" data-optionitem-price-override="<?php echo $optionitems[0]->optionitem_price_override; ?>" data-optionitem-price-multiplier="<?php echo $optionitems[0]->optionitem_price_multiplier; ?>" /><?php if( $optionitems[0]->optionitem_price > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price_onetime > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price_onetime < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( isset( $optionitems[0]->optionitem_price_override ) && $optionitems[0]->optionitem_price_override > -1 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_override ) . ')</span>'; } ?>
                    
                    <?php
					
					/* START ADVANCED FILE TYPE */
					}else if( $optionset->option_type == "file" ){
					?>
                    
                    	<input type="file" name="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>" id="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $this->product->product_id; ?>" data-product-id="<?php echo $this->product->product_id; ?>" data-optionitem-price="<?php echo $optionitems[0]->optionitem_price; ?>" data-optionitem-price-onetime="<?php echo $optionitems[0]->optionitem_price_onetime; ?>" data-optionitem-price-override="<?php echo $optionitems[0]->optionitem_price_override; ?>" data-optionitem-price-multiplier="<?php echo $optionitems[0]->optionitem_price_multiplier; ?>" /><?php if( $optionitems[0]->optionitem_price > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price_onetime > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price_onetime < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( isset( $optionitems[0]->optionitem_price_override ) && $optionitems[0]->optionitem_price_override > -1 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_override ) . ')</span>'; } ?>
                    
                    <?php
					
					/* START ADVANCED SWATCH TYPE */
					}else if( $optionset->option_type == "swatch" ){
					
                    	if( $optionitems[0]->optionitem_icon != '' ){
						?>
							<input type="hidden" name="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>" id="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $this->product->product_id; ?>" value="0" />
							<ul class="ec_details_swatches">
								<?php
								for( $j=0; $j<count( $optionitems ); $j++ ){
								?>
									<li class="ec_details_swatch ec_advanced ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $this->product->product_id; ?> ec_active<?php if( $optionitems[$j]->optionitem_initially_selected || ( isset( $optionset->option_meta['url_var'] ) && $optionset->option_meta['url_var'] != '' && isset( $_GET[$optionset->option_meta['url_var']] ) && $_GET[$optionset->option_meta['url_var']] == $optionitems[$j]->optionitem_name ) || ( isset( $_GET['o'.$optionset->option_id] ) && $_GET['o'.$optionset->option_id] == $optionitems[$j]->optionitem_name ) ){ ?> ec_selected<?php }?>" data-product-id="<?php echo $this->product->product_id; ?>" data-optionitem-id="<?php echo $optionitems[$j]->optionitem_id; ?>" data-option-id="<?php echo $optionset->option_to_product_id; ?>" data-optionitem-price="<?php echo $optionitems[$j]->optionitem_price; ?>" data-optionitem-price-onetime="<?php echo $optionitems[$j]->optionitem_price_onetime; ?>" data-optionitem-price-override="<?php echo $optionitems[$j]->optionitem_price_override; ?>" data-optionitem-price-multiplier="<?php echo $optionitems[$j]->optionitem_price_multiplier; ?>"><img src="<?php if( substr( $optionitems[$j]->optionitem_icon, 0, 7 ) == 'http://' || substr( $optionitems[$j]->optionitem_icon, 0, 8 ) == 'https://' ){ echo $optionitems[$j]->optionitem_icon; }else{ echo plugins_url( "/wp-easycart-data/products/swatches/" . $optionitems[$j]->optionitem_icon ); } ?>"<?php if( $optionitems[$j]->optionitem_price > 0 ){ ?> title="<?php echo str_replace( '"', '&quot;', $optionitems[$j]->optionitem_name ); ?> +<?php echo $GLOBALS['currency']->get_currency_display( $optionitems[$j]->optionitem_price ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ); ?>"<?php }else if( $optionitems[$j]->optionitem_price < 0 ){ ?> title="<?php echo str_replace( '"', '&quot;', $optionitems[$j]->optionitem_name ); ?> <?php echo $GLOBALS['currency']->get_currency_display( $optionitems[$j]->optionitem_price ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ); ?>"<?php }else if( $optionitems[$j]->optionitem_price_onetime > 0 ){ ?> title="<?php echo str_replace( '"', '&quot;', $optionitems[$j]->optionitem_name ); ?> +<?php echo $GLOBALS['currency']->get_currency_display( $optionitems[$j]->optionitem_price_onetime ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ); ?>"<?php }else if( $optionitems[$j]->optionitem_price_onetime < 0 ){ ?> title="<?php echo str_replace( '"', '&quot;', $optionitems[$j]->optionitem_name ); ?> <?php echo $GLOBALS['currency']->get_currency_display( $optionitems[$j]->optionitem_price_onetime ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ); ?>"<?php }else if( isset( $optionitems[$j]->optionitem_price_override ) && $optionitems[$j]->optionitem_price_override > -1 ){ ?> title="<?php echo str_replace( '"', '&quot;', $optionitems[$j]->optionitem_name ); ?> <?php $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ); ?> <?php echo $GLOBALS['currency']->get_currency_display( $optionitems[$j]->optionitem_price_override ); ?>"<?php }else{ ?> title="<?php echo str_replace( '"', '&quot;', $optionitems[$j]->optionitem_name ); ?>"<?php }?>/></li>
								<?php
								}
								?>
							</ul>
						
						<?php
							/* Advanced Swatch HTML */
							}else{ ?>
							<input type="hidden" name="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>" id="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $this->product->product_id; ?>" value="0" />
							<ul class="ec_details_swatches ec_details_html_swatches">
								<?php
								for( $j=0; $j<count( $optionitems ); $j++ ){
								?>
									<li class="ec_details_swatch wpeasycart-html-swatch ec_advanced ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $this->product->product_id; ?> ec_active<?php if( $optionitems[$j]->optionitem_initially_selected || ( isset( $optionset->option_meta['url_var'] ) && $optionset->option_meta['url_var'] != '' && isset( $_GET[$optionset->option_meta['url_var']] ) && $_GET[$optionset->option_meta['url_var']] == $optionitems[$j]->optionitem_name ) || ( isset( $_GET['o'.$optionset->option_id] ) && $_GET['o'.$optionset->option_id] == $optionitems[$j]->optionitem_name ) ){ ?> ec_selected<?php }?>" data-product-id="<?php echo $this->product->product_id; ?>" data-optionitem-id="<?php echo $optionitems[$j]->optionitem_id; ?>" data-option-id="<?php echo $optionset->option_to_product_id; ?>" data-optionitem-price="<?php echo $optionitems[$j]->optionitem_price; ?>" data-optionitem-price-onetime="<?php echo $optionitems[$j]->optionitem_price_onetime; ?>" data-optionitem-price-override="<?php echo $optionitems[$j]->optionitem_price_override; ?>" data-optionitem-price-multiplier="<?php echo $optionitems[$j]->optionitem_price_multiplier; ?>"<?php if( $optionitems[$j]->optionitem_price > 0 ){ ?> title="<?php echo str_replace( '"', '&quot;', $optionitems[$j]->optionitem_name ); ?> +<?php echo $GLOBALS['currency']->get_currency_display( $optionitems[$j]->optionitem_price ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ); ?>"<?php }else if( $optionitems[$j]->optionitem_price < 0 ){ ?> title="<?php echo str_replace( '"', '&quot;', $optionitems[$j]->optionitem_name ); ?> <?php echo $GLOBALS['currency']->get_currency_display( $optionitems[$j]->optionitem_price ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ); ?>"<?php }else if( $optionitems[$j]->optionitem_price_onetime > 0 ){ ?> title="<?php echo str_replace( '"', '&quot;', $optionitems[$j]->optionitem_name ); ?> +<?php echo $GLOBALS['currency']->get_currency_display( $optionitems[$j]->optionitem_price_onetime ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ); ?>"<?php }else if( $optionitems[$j]->optionitem_price_onetime < 0 ){ ?> title="<?php echo str_replace( '"', '&quot;', $optionitems[$j]->optionitem_name ); ?> <?php echo $GLOBALS['currency']->get_currency_display( $optionitems[$j]->optionitem_price_onetime ); ?> <?php echo $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ); ?>"<?php }else if( isset( $optionitems[$j]->optionitem_price_override ) && $optionitems[$j]->optionitem_price_override > -1 ){ ?> title="<?php echo str_replace( '"', '&quot;', $optionitems[$j]->optionitem_name ); ?> <?php $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ); ?> <?php echo $GLOBALS['currency']->get_currency_display( $optionitems[$j]->optionitem_price_override ); ?>"<?php }else{ ?> title="<?php echo str_replace( '"', '&quot;', $optionitems[$j]->optionitem_name ); ?>"<?php }?>><?php echo $optionitems[$j]->optionitem_name; ?></li>
								<?php
								}
								?>
							</ul>	
						<?php	
							}
					
					/* START ADVANCED GRID TYPE */
					}else if( $optionset->option_type == "grid" ){
						$has_quantity_grid = true;
					?>
                    
                    	<?php
						foreach( $optionitems as $optionitem ){
						
						if( $optionitem->optionitem_initial_value > 0 ){	
							if( $optionitem->optionitem_price >= 0 ){
								$add_price_grid = $add_price_grid + $optionitem->optionitem_price;
							
							}else if( $optionitem->optionitem_price_override >= 0 ){
								$override_price_grid = $optionitem->optionitem_price_override;
							
							}else if( $optionitem->optionitem_price_onetime > 0 ){
								$add_order_price_grid = $add_order_price_grid + $optionitem->optionitem_price_onetime;
							
							}
						}
						?>
                        	
                            <div class="ec_details_grid_row"><span><?php echo $optionitem->optionitem_name; ?></span><input type="number" min="<?php if( $this->product->min_purchase_quantity > 0 ){ echo $this->product->min_purchase_quantity; }else{ echo '0'; } ?>"<?php if( $this->product->show_stock_quantity || $this->product->max_purchase_quantity > 0 ){ ?> max="<?php if( $this->product->max_purchase_quantity > 0 ){ echo $this->product->max_purchase_quantity; }else{ echo $this->product->stock_quantity; } ?>"<?php }?> step="1" name="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $optionitem->optionitem_id; ?>" value="<?php echo number_format( $optionitem->optionitem_initial_value, 0, "", "" ); ?>" data-product-id="<?php echo $this->product->product_id; ?>" data-optionitem-price="<?php echo $optionitem->optionitem_price; ?>" data-optionitem-price-onetime="<?php echo $optionitem->optionitem_price_onetime; ?>" data-optionitem-price-override="<?php echo $optionitem->optionitem_price_override; ?>" data-optionitem-price-multiplier="<?php echo $optionitem->optionitem_price_multiplier; ?>" /><?php if( $optionitem->optionitem_price > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitem->optionitem_price < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitem->optionitem_price_onetime > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( $optionitem->optionitem_price_onetime < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( isset( $optionitem->optionitem_price_override ) && $optionitem->optionitem_price_override > -1 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price_override ) . ')</span>'; } ?></div>
                            
                        <?php
						}
						?>
                    
                    <?php
					
					/* START ADVANCED RADIO TYPE */
					}else if( $optionset->option_type == "radio" ){
					?>
                    
                    	<?php
						foreach( $optionitems as $optionitem ){
						?>
                        	
                            <div class="ec_details_radio_row"><input type="radio" name="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>" value="<?php echo $optionitem->optionitem_id; ?>" data-product-id="<?php echo $this->product->product_id; ?>" data-optionitem-price="<?php echo $optionitem->optionitem_price; ?>" data-optionitem-price-onetime="<?php echo $optionitem->optionitem_price_onetime; ?>" data-optionitem-price-override="<?php echo $optionitem->optionitem_price_override; ?>" data-optionitem-price-multiplier="<?php echo $optionitem->optionitem_price_multiplier; ?>"<?php if( $optionitem->optionitem_initially_selected || ( isset( $optionset->option_meta['url_var'] ) && $optionset->option_meta['url_var'] != '' && isset( $_GET[$optionset->option_meta['url_var']] ) && strtolower( $_GET[$optionset->option_meta['url_var']] ) == strtolower( $optionitem->optionitem_name ) ) || ( isset( $_GET['o'.$optionset->option_id] ) && $_GET['o'.$optionset->option_id] == $optionitem->optionitem_name ) ){ ?> checked="checked"<?php }?> /> <?php echo $optionitem->optionitem_name; ?><?php if( $optionitem->optionitem_price > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitem->optionitem_price < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitem->optionitem_price_onetime > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( $optionitem->optionitem_price_onetime < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( isset( $optionitem->optionitem_price_override ) && $optionitem->optionitem_price_override > -1 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitem->optionitem_price_override ) . ')</span>'; } ?></div>
                            
                        <?php
						}
						?>
                        
                    <?php
					
					/* START ADVANCED TEXT TYPE */
					}else if( $optionset->option_type == "text" ){
					?>
                    
                    	<input type="text" value="<?php if( isset( $_GET['o'.$optionset->option_id] ) || ( isset( $optionset->option_meta['url_var'] ) && $optionset->option_meta['url_var'] != '' && isset( $_GET[$optionset->option_meta['url_var']] ) ) ){ echo htmlspecialchars( ( ( isset( $_GET['o'.$optionset->option_id] ) ) ? $_GET['o'.$optionset->option_id] : $_GET[$optionset->option_meta['url_var']] ), ENT_QUOTES ); }else if( $optionitems[0]->optionitem_initial_value != '' ){ echo $optionitems[0]->optionitem_initial_value; } ?>" name="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>" id="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $this->product->product_id; ?>" data-product-id="<?php echo $this->product->product_id; ?>" data-optionitem-price="<?php echo $optionitems[0]->optionitem_price; ?>" data-optionitem-price-onetime="<?php echo $optionitems[0]->optionitem_price_onetime; ?>" data-optionitem-price-override="<?php echo $optionitems[0]->optionitem_price_override; ?>" data-optionitem-price-multiplier="<?php echo $optionitems[0]->optionitem_price_multiplier; ?>" data-optionitem-price-per-character="<?php echo $optionitems[0]->optionitem_price_per_character; ?>" /><?php if( $optionitems[0]->optionitem_price > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price_onetime > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price_onetime < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( isset( $optionitems[0]->optionitem_price_override ) && $optionitems[0]->optionitem_price_override > -1 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_override ) . ')</span>'; }else if( isset( $optionitems[0]->optionitem_price_per_character ) && $optionitems[0]->optionitem_price_per_character > 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_per_character ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment_per_character' ) . ')</span>'; } ?>
                    
                    <?php
					
					/* START ADVANCED NUMBER TYPE */
					}else if( $optionset->option_type == "number" ){
					?>
                    
                    	<input type="number" value="<?php if( isset( $_GET['o'.$optionset->option_id] ) || ( isset( $optionset->option_meta['url_var'] ) && $optionset->option_meta['url_var'] != '' && isset( $_GET[$optionset->option_meta['url_var']] ) ) ){ echo htmlspecialchars( ( ( isset( $_GET['o'.$optionset->option_id] ) ) ? $_GET['o'.$optionset->option_id] : $_GET[$optionset->option_meta['url_var']] ), ENT_QUOTES ); }else if( $optionitems[0]->optionitem_initial_value != '' ){ echo $optionitems[0]->optionitem_initial_value; } ?>" min="<?php echo $optionset->option_meta['min']; ?>" max="<?php echo $optionset->option_meta['max']; ?>" step="<?php echo $optionset->option_meta['step']; ?>" name="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>" id="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $this->product->product_id; ?>" data-product-id="<?php echo $this->product->product_id; ?>" data-optionitem-price="<?php echo $optionitems[0]->optionitem_price; ?>" data-optionitem-price-onetime="<?php echo $optionitems[0]->optionitem_price_onetime; ?>" data-optionitem-price-override="<?php echo $optionitems[0]->optionitem_price_override; ?>" data-optionitem-price-multiplier="<?php echo $optionitems[0]->optionitem_price_multiplier; ?>" /><?php if( $optionitems[0]->optionitem_price > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price_onetime > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price_onetime < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( isset( $optionitems[0]->optionitem_price_override ) && $optionitems[0]->optionitem_price_override > -1 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_override ) . ')</span>'; }else if( isset( $optionitems[0]->optionitem_price_per_character ) && $optionitems[0]->optionitem_price_per_character > 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_per_character ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment_per_character' ) . ')</span>'; } ?>
                    
                    <?php
					
					/* START ADVANCED TEXT AREA TYPE */
					}else if( $optionset->option_type == "textarea" ){
					?>
                    
                    	<textarea name="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>" id="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $this->product->product_id; ?>" data-product-id="<?php echo $this->product->product_id; ?>" data-optionitem-price="<?php echo $optionitems[0]->optionitem_price; ?>" data-optionitem-price-onetime="<?php echo $optionitems[0]->optionitem_price_onetime; ?>" data-optionitem-price-override="<?php echo $optionitems[0]->optionitem_price_override; ?>" data-optionitem-price-multiplier="<?php echo $optionitems[0]->optionitem_price_multiplier; ?>" data-optionitem-price-per-character="<?php echo $optionitems[0]->optionitem_price_per_character; ?>"><?php if( isset( $_GET['o'.$optionset->option_id] ) || ( isset( $optionset->option_meta['url_var'] ) && $optionset->option_meta['url_var'] != '' && isset( $_GET[$optionset->option_meta['url_var']] ) ) ){ echo htmlspecialchars( ( ( isset( $_GET['o'.$optionset->option_id] ) ) ? $_GET['o'.$optionset->option_id] : $_GET[$optionset->option_meta['url_var']] ), ENT_QUOTES ); } ?></textarea><?php if( $optionitems[0]->optionitem_price > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price_onetime > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price_onetime < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( isset( $optionitems[0]->optionitem_price_override ) && $optionitems[0]->optionitem_price_override > -1 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_override ) . ')</span>'; }else if( isset( $optionitems[0]->optionitem_price_per_character ) && $optionitems[0]->optionitem_price_per_character > 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_per_character ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment_per_character' ) . ')</span>'; } ?>
                    
                    <?php
					
					/* START ADVANCED DIMENSIONS TYPE */
					}else if( $optionset->option_type == "dimensions1" || $optionset->option_type == "dimensions2" ){
						
						// Type 1 is NO sub dimensions (34")
						// Type 2 USES sub dimensions (34 1/2")
						
						$type = 2;
						
						if( $optionitems[0]->optionitem_name == "DimensionType1" )
							$type = 1;
					?>
                    
                    	<input type="text" name="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_width" id="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $this->product->product_id; ?>_width" data-product-id="<?php echo $this->product->product_id; ?>" data-optionitem-price="<?php echo $optionitems[0]->optionitem_price; ?>" data-optionitem-price-onetime="<?php echo $optionitems[0]->optionitem_price_onetime; ?>" data-optionitem-price-override="<?php echo $optionitems[0]->optionitem_price_override; ?>" data-optionitem-price-multiplier="<?php echo $optionitems[0]->optionitem_price_multiplier; ?>" data-optionitem-price-per-character="<?php echo $optionitems[0]->optionitem_price_per_character; ?>" class="ec_dimensions_box ec_dimensions_width" data-option-id="<?php echo $optionset->option_to_product_id; ?>" /><?php if( $optionitems[0]->optionitem_price > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price_onetime > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price_onetime < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( isset( $optionitems[0]->optionitem_price_override ) && $optionitems[0]->optionitem_price_override > -1 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_override ) . ')</span>'; }else if( isset( $optionitems[0]->optionitem_price_per_character ) && $optionitems[0]->optionitem_price_per_character > 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_per_character ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment_per_character' ) . ')</span>'; } ?>
                        
                        <?php if( $type == 2 ){ ?>
                        <select name="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_sub_width" id="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $this->product->product_id; ?>_sub_width" class="ec_dimensions_select">
                        	<option value="0">0</option>
                        	<option value="1/16">1/16</option>
                        	<option value="1/8">1/8</option>
                        	<option value="3/16">3/16</option>
                        	<option value="1/4">1/4</option>
                        	<option value="5/16">5/16</option>
                        	<option value="3/8">3/8</option>
                        	<option value="7/16">7/16</option>
                        	<option value="1/2">1/2</option>
                        	<option value="9/16">9/16</option>
                        	<option value="5/8">5/8</option>
                        	<option value="11/16">11/16</option>
                        	<option value="3/4">3/4</option>
                        	<option value="13/16">13/16</option>
                        	<option value="7/8">7/8</option>
                        	<option value="15/16">15/16</option>
                        </select>
                        <?php }?>
                        
                        <span class="ec_dimensions_seperator">x</span>
                        
                        <input type="text" name="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_height" id="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $this->product->product_id; ?>_height" data-product-id="<?php echo $this->product->product_id; ?>" data-optionitem-price="<?php echo $optionitems[0]->optionitem_price; ?>" data-optionitem-price-onetime="<?php echo $optionitems[0]->optionitem_price_onetime; ?>" data-optionitem-price-override="<?php echo $optionitems[0]->optionitem_price_override; ?>" data-optionitem-price-multiplier="<?php echo $optionitems[0]->optionitem_price_multiplier; ?>" data-optionitem-price-per-character="<?php echo $optionitems[0]->optionitem_price_per_character; ?>" class="ec_dimensions_box" /><?php if( $optionitems[0]->optionitem_price > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price_onetime > 0 ){ echo '<span class="ec_product_details_option_pricing"> (+' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( $optionitems[0]->optionitem_price_onetime < 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_onetime ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_order_adjustment' ) . ')</span>'; }else if( isset( $optionitems[0]->optionitem_price_override ) && $optionitems[0]->optionitem_price_override > -1 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_new_price_option' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_override ) . ')</span>'; }else if( isset( $optionitems[0]->optionitem_price_per_character ) && $optionitems[0]->optionitem_price_per_character > 0 ){ echo '<span class="ec_product_details_option_pricing"> (' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment' ) . ' ' . $GLOBALS['currency']->get_currency_display( $optionitems[0]->optionitem_price_per_character ) . ' ' . $GLOBALS['language']->get_text( 'cart', 'cart_item_adjustment_per_character' ) . ')</span>'; } ?>
                        
                        <?php if( $type == 2 ){ ?>
                        <select name="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_sub_height" id="ec_option_adv_<?php echo $optionset->option_to_product_id; ?>_<?php echo $this->product->product_id; ?>_sub_height" class="ec_dimensions_select">
                        	<option value="0">0</option>
                        	<option value="1/16">1/16</option>
                        	<option value="1/8">1/8</option>
                        	<option value="3/16">3/16</option>
                        	<option value="1/4">1/4</option>
                        	<option value="5/16">5/16</option>
                        	<option value="3/8">3/8</option>
                        	<option value="7/16">7/16</option>
                        	<option value="1/2">1/2</option>
                        	<option value="9/16">9/16</option>
                        	<option value="5/8">5/8</option>
                        	<option value="11/16">11/16</option>
                        	<option value="3/4">3/4</option>
                        	<option value="13/16">13/16</option>
                        	<option value="7/8">7/8</option>
                        	<option value="15/16">15/16</option>
                        </select>
                        <?php }?>
                    
                    <?php
					}
				?>
                	</div>
				</div>				
				<?php
				}
				?>
            </div>
            <?php }?>
            <?php } /* INQUIRY MODE EXCLUDED */ ?>
            <?php /* END ADVANCED OPTIONS*/ ?>
            
            <?php /* PRODUCT ADD TO CART */ ?>
            <div class="ec_details_option_row_error" id="ec_addtocart_quantity_exceeded_error_<?php echo $this->product->product_id; ?>"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_maximum_quantity' ); ?></div>
            <div class="ec_details_option_row_error" id="ec_addtocart_quantity_minimum_error_<?php echo $this->product->product_id; ?>"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_minimum_quantity_text1' ); ?> <?php echo $this->product->min_purchase_quantity; ?> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_minimum_quantity_text2' ); ?></div>
            <div class="ec_details_option_row_error" id="ec_addtocart_quantity_maximum_error_<?php echo $this->product->product_id; ?>"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_maximum_quantity_text1' ); ?> <?php echo $this->product->max_purchase_quantity; ?> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_maximum_quantity_text2' ); ?></div>
            
            <?php
			
			do_action( 'wp_easycart_product_details_pre_add_to_cart', $this->product->product_id );
			
			$show_add_to_cart_area = true;
			$show_add_to_cart_area = apply_filters( 'wp_easycart_product_details_show_cart_area', $show_add_to_cart_area );
			
			if( $show_add_to_cart_area ){
			?>
            <div class="ec_details_add_to_cart_area">
				
				<?php /* CATALOG MODE */ ?>
				<?php if( apply_filters( 'wp_easycart_catalog_display', get_option( 'ec_option_display_as_catalog' ) ) ){
				// Show nothing
				
				}else if( $this->product->login_for_pricing && !$this->product->is_login_for_pricing_valid( ) && $GLOBALS['ec_user']->user_id != 0 ){ ?>
				<div class="ec_seasonal_mode"><?php echo $GLOBALS['language']->get_text( 'product_page', 'product_page_login_for_price_no_access' ); ?></div>
							
				<?php }else if( $this->product->login_for_pricing && !$this->product->is_login_for_pricing_valid( ) ){ ?>
                <div class="ec_details_add_to_cart"><a href="<?php echo $this->account_page; ?>" style="margin-left:0px !important;"><?php echo ( $this->product->login_for_pricing_label != '' ) ? $this->product->login_for_pricing_label : $GLOBALS['language']->get_text( 'product_page', 'product_page_login_for_price' ); ?></a></div>
                    
                <?php }else if( $this->product->is_catalog_mode ){ ?>
				<div class="ec_details_seasonal_mode"><?php echo $this->product->catalog_mode_phrase; ?></div>	
        		
				<?php /* INQUIRY BUTTON */ ?>
				<?php }else if( $this->product->is_inquiry_mode ){ ?>
                <div class="ec_details_add_to_cart">
                	<?php if( get_option( 'ec_option_use_inquiry_form' ) || $this->product->inquiry_url == "" ){ ?>
                    <input type="submit" value="<?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_inquire' ); ?>" <?php if( $this->product->inquiry_url == "" ){ ?>onclick="return ec_details_show_inquiry_form( '<?php echo $this->product->product_id; ?>' );" <?php }else{ ?>onclick="<?php if( trim( get_option( 'ec_option_fb_pixel' ) ) != '' ){ ?>wp_easycart_facebook_add_to_cart_track_<?php echo $this->product->product_id; ?>( ); <?php }?>return ec_details_add_to_cart_<?php echo $this->product->product_id; ?>( );" <?php }?>style="margin-left:0px !important;" />
                	<?php }else{ ?>
                    <a href="<?php echo $this->product->inquiry_url; ?>" style="margin-left:0px !important;"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_inquire' ); ?></a>
                	<?php }?>
                </div>
                
                <?php /* DecoNetwork BUTTON */ ?>
				<?php }else if( $this->product->is_deconetwork ){ ?>
                <?php if( get_option( 'ec_option_deconetwork_allow_blank_products' ) ){ // Custom option to have both add to cart and design now ?>
                    
                <div class="ec_details_quantity"<?php if( $has_quantity_grid ){ ?> style="display:none;"<?php }?>><input type="button" value="-" class="ec_minus" onclick="ec_minus_quantity( '<?php echo $this->product->product_id; ?>', <?php echo $this->product->min_purchase_quantity; ?> );" /><input type="number" value="<?php if( $this->product->min_purchase_quantity > 0 ){ echo $this->product->min_purchase_quantity; }else{ echo '1'; } ?>" name="ec_quantity" id="ec_quantity_<?php echo $this->product->product_id; ?>" autocomplete="off" step="1" min="<?php if( $this->product->min_purchase_quantity > 0 ){ echo $this->product->min_purchase_quantity; }else{ echo '1'; } ?>" class="ec_quantity"<?php if( $this->product->show_stock_quantity || $this->product->max_purchase_quantity > 0 ){ ?> max="<?php if( $this->product->max_purchase_quantity > 0 ){ echo $this->product->max_purchase_quantity; }else{ echo $this->product->stock_quantity; } ?>"<?php }?> /><input type="button" value="+" class="ec_plus" onclick="ec_plus_quantity( '<?php echo $this->product->product_id; ?>', <?php echo $this->product->show_stock_quantity; ?>, <?php if( $this->product->max_purchase_quantity > 0 ){ echo $this->product->max_purchase_quantity; }else{ echo $this->product->stock_quantity; } ?> );" /></div>
                <div class="ec_details_add_to_cart ec_deconetwork_custom_space">
                    <input type="submit" value="<?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_add_to_cart' ); ?>" onclick="<?php if( trim( get_option( 'ec_option_fb_pixel' ) ) != '' ){ ?>wp_easycart_facebook_add_to_cart_track_<?php echo $this->product->product_id; ?>( ); <?php }?>return ec_details_add_to_cart_<?php echo $this->product->product_id; ?>( );"<?php if( $has_quantity_grid ){ ?> style="margin-left:0px !important;"<?php }?> />
                </div>
                    
                <?php }?>
                
                <div class="ec_details_add_to_cart">
                	<a href="<?php echo $this->product->get_deconetwork_link( ); ?>" style="margin-left:0px !important;"><?php echo $GLOBALS['language']->get_text( 'product_page', 'product_design_now' ); ?></a>
                </div>
				
				<?php /* SUBSCRIPTION BUTTON */ ?>
				<?php }else if( $this->product->is_subscription_item ){ // && !class_exists( "ec_stripe" ) ){ ?>
                
				<?php if( !get_option( 'ec_option_subscription_one_only' ) ){ ?>
                <div class="ec_details_quantity"<?php if( $has_quantity_grid ){ ?> style="display:none;"<?php }?>><input type="button" value="-" class="ec_minus" onclick="ec_minus_quantity( '<?php echo $this->product->product_id; ?>', <?php echo $this->product->min_purchase_quantity; ?> );" /><input type="number" value="<?php if( $this->product->min_purchase_quantity > 0 ){ echo $this->product->min_purchase_quantity; }else{ echo '1'; } ?>" name="ec_quantity" id="ec_quantity_<?php echo $this->product->product_id; ?>" autocomplete="off" step="1" min="<?php if( $this->product->min_purchase_quantity > 0 ){ echo $this->product->min_purchase_quantity; }else{ echo '1'; } ?>" class="ec_quantity"<?php if( $this->product->show_stock_quantity || $this->product->max_purchase_quantity > 0 ){ ?> max="<?php if( $this->product->max_purchase_quantity > 0 ){ echo $this->product->max_purchase_quantity; }else{ echo $this->product->stock_quantity; } ?>"<?php }?> /><input type="button" value="+" class="ec_plus" onclick="ec_plus_quantity( '<?php echo $this->product->product_id; ?>', <?php echo $this->product->show_stock_quantity; ?>, <?php if( $this->product->max_purchase_quantity > 0 ){ echo $this->product->max_purchase_quantity; }else if( $this->product->show_stock_quantity ){ echo $this->product->stock_quantity; }else{ echo "10000000"; } ?> );" /></div>
                <?php }else{ ?>
                <input type="hidden" id="ec_quantity_<?php echo $this->product->product_id; ?>" value="1" />
                <?php }?>
                
                <div class="ec_details_add_to_cart">
                	<input type="submit" value="<?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_sign_up_now' ); ?>" onclick="<?php if( trim( get_option( 'ec_option_fb_pixel' ) ) != '' ){ ?>wp_easycart_facebook_add_to_cart_track_<?php echo $this->product->product_id; ?>( ); <?php }?>return ec_details_add_to_cart_<?php echo $this->product->product_id; ?>( );"<?php if( get_option( 'ec_option_subscription_one_only' ) ){ ?> style="margin-left:0px !important;"<?php }?> />
                </div>
                
                <?php /* REGULAR BUTTON + QUANTITY */ ?>
                <?php }else if( $this->product->in_stock( ) || ( $this->product->allow_backorders && $this->product->use_optionitem_quantity_tracking ) || apply_filters( 'wp_easycart_product_details_allow_add_to_cart', false, $this->product->product_id ) ){ ?>
                <div class="ec_details_quantity"<?php if( $has_quantity_grid ){ ?> style="display:none;"<?php }?>><input type="button" value="-" class="ec_minus" onclick="ec_minus_quantity( '<?php echo $this->product->product_id; ?>', <?php echo $this->product->min_purchase_quantity; ?> );" /><input type="number" value="<?php if( $this->product->min_purchase_quantity > 0 ){ echo $this->product->min_purchase_quantity; }else{ echo '1'; } ?>" name="ec_quantity" id="ec_quantity_<?php echo $this->product->product_id; ?>" autocomplete="off" step="1" min="<?php if( $this->product->min_purchase_quantity > 0 ){ echo $this->product->min_purchase_quantity; }else{ echo '1'; } ?>" class="ec_quantity"<?php if( ( !$this->product->allow_backorders && $this->product->show_stock_quantity ) || $this->product->max_purchase_quantity > 0 ){ ?> max="<?php if( $this->product->max_purchase_quantity > 0 ){ echo $this->product->max_purchase_quantity; }else{ echo $this->product->stock_quantity; } ?>"<?php }?> /><input type="button" value="+" class="ec_plus" onclick="ec_plus_quantity( '<?php echo $this->product->product_id; ?>', <?php echo $this->product->show_stock_quantity; ?>, <?php if( $this->product->max_purchase_quantity > 0 ){ echo $this->product->max_purchase_quantity; }else if( $this->product->allow_backorders ){ echo "1000000"; }else if( $this->product->show_stock_quantity ){ echo $this->product->stock_quantity; }else{ echo "10000000"; } ?> );" /></div>
                <div class="ec_details_add_to_cart">
                	<input type="submit" value="<?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_add_to_cart' ); ?>" onclick="<?php if( trim( get_option( 'ec_option_fb_pixel' ) ) != '' ){ ?>wp_easycart_facebook_add_to_cart_track_<?php echo $this->product->product_id; ?>( ); <?php }?>return ec_details_add_to_cart_<?php echo $this->product->product_id; ?>( );"<?php if( $has_quantity_grid ){ ?> style="margin-left:0px !important;"<?php }?> />
                </div>
                
                <?php /* PRICING AREA FOR OPTIONS */ ?>
				<?php if( $this->product->has_options || $this->product->use_advanced_optionset ){ ?>
                <div class="ec_details_final_price"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_your_price' ); ?> <span id="ec_final_price_<?php echo $this->product->product_id; ?>"><?php if( $override_price_grid > -1 ){ echo $GLOBALS['currency']->get_currency_display( $override_price_grid ); }else if( $add_price_grid > 0 ){ echo $GLOBALS['currency']->get_currency_display( $this->product->price + $add_price_grid ); }else{ echo $GLOBALS['currency']->get_currency_display( $this->product->price ); } ?></span></div>
				<?php } ?>
                <span class="ec_details_hidden_base_price" id="ec_base_price_<?php echo $this->product->product_id; ?>"><?php echo $this->product->price; ?></span>
                
                 <?php /* OUT OF STOCK BUT BACKORDERS ALLOWED */ ?>
                <?php }else if( $this->product->allow_backorders ){ ?>
				<div class="ec_details_quantity"<?php if( $has_quantity_grid ){ ?> style="display:none;"<?php }?>><input type="button" value="-" class="ec_minus" onclick="ec_minus_quantity( '<?php echo $this->product->product_id; ?>', <?php echo $this->product->min_purchase_quantity; ?> );" /><input type="number" value="<?php if( $this->product->min_purchase_quantity > 0 ){ echo $this->product->min_purchase_quantity; }else{ echo '1'; } ?>" name="ec_quantity" id="ec_quantity_<?php echo $this->product->product_id; ?>" autocomplete="off" step="1" min="<?php if( $this->product->min_purchase_quantity > 0 ){ echo $this->product->min_purchase_quantity; }else{ echo '1'; } ?>" class="ec_quantity"<?php if( !$this->product->allow_backorders && $this->product->max_purchase_quantity > 0 ){ ?> max="<?php echo $this->product->max_purchase_quantity; ?>"<?php }?> /><input type="button" value="+" class="ec_plus" onclick="ec_plus_quantity( '<?php echo $this->product->product_id; ?>', <?php echo $this->product->show_stock_quantity; ?>, 100000000 );" /></div>
                <div class="ec_details_add_to_cart">
                	<input type="submit" value="<?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_backorder_button' ); ?>" onclick="<?php if( trim( get_option( 'ec_option_fb_pixel' ) ) != '' ){ ?>wp_easycart_facebook_add_to_cart_track_<?php echo $this->product->product_id; ?>( ); <?php }?>return ec_details_add_to_cart_<?php echo $this->product->product_id; ?>( );"<?php if( $has_quantity_grid ){ ?> style="margin-left:0px !important;"<?php }?> />
                </div>
                
                <?php /* PRICING AREA FOR OPTIONS */ ?>
				<?php if( $this->product->has_options || $this->product->use_advanced_optionset ){ ?>
                <div class="ec_details_final_price"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_your_price' ); ?> <span id="ec_final_price_<?php echo $this->product->product_id; ?>"><?php if( $override_price_grid > -1 ){ echo $GLOBALS['currency']->get_currency_display( $override_price_grid ); }else if( $add_price_grid > 0 ){ echo $GLOBALS['currency']->get_currency_display( $this->product->price + $add_price_grid ); }else{ echo $GLOBALS['currency']->get_currency_display( $this->product->price ); } ?></span></div>
				<?php } ?>
                <span class="ec_details_hidden_base_price" id="ec_base_price_<?php echo $this->product->product_id; ?>"><?php echo $this->product->price; ?></span>
                
                <?php /* OUT OF STOCK INFO (NO ADD TO CART CASE) */ ?>
                <?php }else{ ?>
                <div class="ec_out_of_stock"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_out_of_stock' ); ?></div>
                <?php if( get_option( 'ec_option_enable_inventory_notification' ) ){ ?>
                <div class="ec_cart_success" style="display:none;" id="ec_product_details_stock_notify_complete_<?php echo $this->product->product_id; ?>"><div><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_notify_subscribe_success' ); ?></div></div>
                <div class="ec_out_of_stock_notify" id="ec_product_details_stock_notify_<?php echo $this->product->product_id; ?>">
                	<div class="ec_out_of_stock_notify_loader_cover" style="display:none;" id="ec_product_details_stock_notify_<?php echo $this->product->product_id; ?>_loader_cover"></div>
                    <div class="ec_out_of_stock_notify_loader" style="display:none;" id="ec_product_details_stock_notify_<?php echo $this->product->product_id; ?>_loader"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
                    <div class="ec_out_of_stock_notify_title"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_notify_subscribe_title' ); ?></div>
                	<div class="ec_out_of_stock_notify_input">
                    	<div class="ec_cart_error_row" id="ec_email_notify_<?php echo $this->product->product_id; ?>_error">
							<?php echo $GLOBALS['language']->get_text( 'cart_form_notices', 'cart_notice_please_enter_valid' ); ?> <?php echo $GLOBALS['language']->get_text( 'cart_contact_information', 'cart_contact_information_email' ); ?>
                    	</div>
                    	<input type="text" id="ec_email_notify_<?php echo $this->product->product_id; ?>" value="" placeholder="<?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_notify_subscribe_email_placeholder' ); ?>" />
                    </div>
                    
                    <?php if( get_option( 'ec_option_enable_recaptcha' ) && get_option( 'ec_option_recaptcha_site_key' ) != '' ){ ?>
                    <div class="ec_out_of_stock_notify_grecaptcha" style="float:left; width:100%; margin:-20px 0 5px; padding:0 15px;">
                    	<input type="hidden" id="ec_grecaptcha_response_product_details" name="ec_grecaptcha_response_product_details" value="" />
                    	<input type="hidden" id="ec_grecaptcha_site_key" value="<?php echo get_option( 'ec_option_recaptcha_site_key' ); ?>" />
                    	<div class="ec_cart_input_row" data-sitekey="<?php echo get_option( 'ec_option_recaptcha_site_key' ); ?>" id="ec_product_details_recaptcha"></div>
                    </div>
					<?php }?>
                    
                    <div class="ec_out_of_stock_notify_button">
                    	<input type="button" onclick="ec_notify_submit( <?php echo $this->product->product_id; ?> );" value="<?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_notify_subscribe_button_title' ); ?>" />
                    </div>
                </div>
                <?php }?>
				<?php }?>
            </div>
            <?php } //END FILTER FOR HIDING ADD TO CART ?>
            
            <?php if( !$this->product->in_stock( ) && !$this->product->use_optionitem_quantity_tracking && $this->product->allow_backorders ){ ?>
            <div class="ec_details_backorder_info" id="ec_back_order_info_<?php echo $this->product->product_id; ?>"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_out_of_stock' ); ?><?php if( $this->product->backorder_fill_date != "" ){ ?> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_backorder_until' ); ?> <?php echo $this->product->backorder_fill_date; ?><?php }?></div>
            <?php }?>
            
            <?php /* END ADD TO CART */ ?>
            
			<?php do_action( 'wp_easycart_product_details_post_add_to_cart', $this->product->product_id ); ?>
            
            </form>
            
            <?php /* START AREA BELOW ADD TO CART BUTTON ROW */ ?>
            <?php if( $this->product->has_options || $this->product->use_advanced_optionset ){ ?><div class="ec_details_added_price ec_details_added_price_<?php echo $this->product->product_id; ?>"<?php if( $add_order_price_grid > 0 ){ echo ' style="display:block";'; } ?>><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_one_time_addition1' ); ?> <span id="ec_added_price_<?php echo $this->product->product_id; ?>"><?php if( $add_order_price_grid > 0 ){ echo $GLOBALS['currency']->get_currency_display( $add_order_price_grid ); }else{ echo $GLOBALS['currency']->get_currency_display( $this->product->price ); } ?></span> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_one_time_addition2' ); ?></span></div><?php }?>
			
            <?php if( ( $this->product->show_stock_quantity || $this->product->use_optionitem_quantity_tracking ) && $this->product->stock_quantity > 0 && get_option( 'ec_option_show_stock_quantity' ) ){ ?><div class="ec_details_stock_total"><span id="ec_details_stock_quantity_<?php echo $this->product->product_id; ?>"><?php echo $this->product->stock_quantity; ?></span> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_left_in_stock' ); ?></div><?php }else{ ?><span id="ec_details_stock_quantity_<?php echo $this->product->product_id; ?>" style="display:none;">10000000</span><?php }?>
			
			<?php if( $this->product->min_purchase_quantity > 1 ){ ?><div class="ec_details_min_purchase_quantity"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_minimum_quantity_text1' ); ?> <?php echo $this->product->min_purchase_quantity; ?> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_minimum_quantity_text2' ); ?></div><?php }?>
			
			<?php if( $this->product->max_purchase_quantity > 0 ){ ?><div class="ec_details_min_purchase_quantity"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_maximum_quantity_text1' ); ?> <?php echo $this->product->max_purchase_quantity; ?> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_maximum_quantity_text2' ); ?></div><?php }?>
            
			<?php if( $this->product->handling_price > 0 ){ ?><div class="ec_details_handling_fee"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_handling_fee_notice1' ); ?> <?php echo $GLOBALS['currency']->get_currency_display( $this->product->handling_price ); ?> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_handling_fee_notice2' ); ?></div><?php }?>
            
			<?php if( $this->product->handling_price_each > 0 ){ ?><div class="ec_details_handling_fee"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_handling_fee_each_notice1' ); ?> <?php echo $GLOBALS['currency']->get_currency_display( $this->product->handling_price_each ); ?> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_handling_fee_each_notice2' ); ?></div><?php }?>
            
            <?php if( $this->product->subscription_signup_fee > 0 ){ ?><div class="ec_details_handling_fee"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_signup_fee_notice1' ); ?> <?php echo $GLOBALS['currency']->get_currency_display( $this->product->subscription_signup_fee ); ?> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_signup_fee_notice2' ); ?></div><?php }?>
            
			<?php if( get_option( 'ec_option_show_categories' ) ){ ?>
			<?php if( count( $this->product->categoryitems ) > 0 ){ ?><div class="ec_details_categories"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_categories' ); ?> <?php $categoryitems = array( ); foreach( $this->product->categoryitems as $categoryitem ){ $categoryitems[] = '<a href="' . $this->product->get_category_link( $categoryitem->post_id, $categoryitem->category_id ) . '">' . $GLOBALS['language']->convert_text( $categoryitem->category_name ) . '</a>'; } echo implode( ', ', $categoryitems ); ?></div><?php }?>
            <?php }?>
            
            <?php if( get_option( 'ec_option_show_manufacturer' ) ){ ?>
            <div class="ec_details_manufacturer"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_manufacturer' ); ?> <a href="<?php echo $this->product->get_manufacturer_link( ); ?>"><?php echo $GLOBALS['language']->convert_text( $this->product->manufacturer_name ); ?></a></div>
			<?php }?>
            
            <?php /* START SOCIAL ICONS */ ?>
            <?php
			if( get_option( 'ec_option_base_theme' ) ){
				$folder = "wp-easycart-data";
				$theme = get_option( 'ec_option_base_theme' );
			}else{
				$folder = "wp-easycart";
				$theme = get_option( 'ec_option_latest_theme' );
			}
			?>
            <div class="ec_details_social">
            	
                <?php if( get_option( 'ec_option_use_facebook_icon' ) ){ ?>
                <div class="ec_details_social_icon ec_facebook"><a href="<?php echo $this->product->social_icons->get_facebook_link( ); ?>" target="_blank"><img src="<?php echo $this->product->social_icons->get_icon_image( "facebook-icon.png" ); ?>" alt="Facebook" /></a></div>
                <?php }?>
            	
                <?php if( get_option( 'ec_option_use_twitter_icon' ) ){ ?>
                <div class="ec_details_social_icon ec_twitter"><a href="<?php echo $this->product->social_icons->get_twitter_link( ); ?>" target="_blank"><img src="<?php echo $this->product->social_icons->get_icon_image( "twitter-icon.png" ); ?>" alt="Twitter" /></a></div>
                <?php }?>
            	
                <?php if( get_option( 'ec_option_use_email_icon' ) ){ ?>
                <div class="ec_details_social_icon ec_email"><a href="<?php echo $this->product->social_icons->get_email_link( ); ?>" target="_blank"><img src="<?php echo $this->product->social_icons->get_icon_image( "email-icon.png" ); ?>" alt="Email" /></a></div>
                <?php }?>
            	
                <?php if( get_option( 'ec_option_use_pinterest_icon' ) ){ ?>
                <div class="ec_details_social_icon ec_pinterest"><a href="<?php echo $this->product->social_icons->get_pinterest_link( ); ?>" target="_blank"><img src="<?php echo $this->product->social_icons->get_icon_image( "pinterest-icon.png" ); ?>" alt="Pinterest" /></a></div>
                <?php }?>
            	
                <?php if( get_option( 'ec_option_use_googleplus_icon' ) ){ ?>
                <div class="ec_details_social_icon ec_googleplus"><a href="<?php echo $this->product->social_icons->get_googleplus_link( ); ?>" target="_blank"><img src="<?php echo $this->product->social_icons->get_icon_image( "google-icon.png" ); ?>" alt="Google+" /></a></div>
                <?php }?>
            	
                <?php if( get_option( 'ec_option_use_linkedin_icon' ) ){ ?>
                <div class="ec_details_social_icon ec_linkedin"><a href="<?php echo $this->product->social_icons->get_linkedin_link( ); ?>" target="_blank"><img src="<?php echo $this->product->social_icons->get_icon_image( "linkedin-icon.png" ); ?>" alt="LinkedIn" /></a></div>
                <?php }?>
            	
                <?php if( get_option( 'ec_option_use_myspace_icon' ) ){ ?>
                <div class="ec_details_social_icon ec_myspace"><a href="<?php echo $this->product->social_icons->get_myspace_link( ); ?>" target="_blank"><img src="<?php echo $this->product->social_icons->get_icon_image( "myspace-icon.png" ); ?>" alt="MySpace" /></a></div>
                <?php }?>
            	
                <?php if( get_option( 'ec_option_use_digg_icon' ) ){ ?>
                <div class="ec_details_social_icon ec_digg"><a href="<?php echo $this->product->social_icons->get_digg_link( ); ?>" target="_blank"><img src="<?php echo $this->product->social_icons->get_icon_image( "digg-icon.png" ); ?>" alt="Digg" /></a></div>
                <?php }?>
            	
                <?php if( get_option( 'ec_option_use_delicious_icon' ) ){ ?>
                <div class="ec_details_social_icon ec_delicious"><a href="<?php echo $this->product->social_icons->get_delicious_link( ); ?>" target="_blank"><img src="<?php echo $this->product->social_icons->get_icon_image( "delicious-icon.png" ); ?>" alt="Delicious" /></a></div>
                <?php }?>
                
            </div>
            <?php /* END SOCIAL ICONS */ ?>
            
        </div>
        <?php /* END RIGHT HALF */ ?>
        
    </div>
    <?php /* END TOP SECTION*/ ?>
    
    
    <?php /* START EXTRA CONTENT AREA */ ?>
    <div class="ec_details_extra_area ec_details_extra_area_<?php echo $this->product->product_id; ?>">
    	
        <ul class="ec_details_tabs">
        
        	<?php do_action( 'wpeasycart_pre_description_tab', $this->product->product_id ); ?>
        	<li class="ec_details_tab ec_details_tab_<?php echo $this->product->product_id; ?> <?php echo apply_filters( 'wpeasycart_description_initally_active', 'ec_active', $this->product->product_id ); ?> ec_description"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_description' ); ?></li>
            
            <?php do_action( 'wpeasycart_pre_specifications_tab', $this->product->product_id ); ?>
        	<?php if( $this->product->use_specifications ){ ?><li class="ec_details_tab ec_details_tab_<?php echo $this->product->product_id; ?> ec_specifications"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_specifications' ); ?></li><?php } ?>
        	
            <?php do_action( 'wpeasycart_pre_customer_reviews_tab', $this->product->product_id ); ?>
            <?php if( $this->product->use_customer_reviews ){ ?><li class="ec_details_tab ec_details_tab_<?php echo $this->product->product_id; ?> ec_customer_reviews"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_customer_reviews' ); ?> (<?php echo count( $this->product->reviews ); ?>)</li><?php } ?>
        	
            <?php do_action( 'wpeasycart_addon_product_details_tab', $this->product->product_id ); ?>
        
        </ul>
        
        <div class="ec_details_description_tab ec_details_description_tab_<?php echo $this->product->product_id; ?>" <?php echo apply_filters( 'wpeasycart_description_content_initally_active', '', $this->product->product_id ); ?>>
        	
			<?php 
			// START CONTENT EDITING SECTION
			if( $is_admin && !$is_preview && substr( $this->product->description, 0, 3 ) != "[ec" ){ ?>
            <div class="ec_details_edit_buttons">
            	<div class="ec_details_edit_button" id="ec_details_edit_description_<?php echo $this->product->product_id; ?>"><input type="button" value="Edit Description" onclick="ec_admin_show_description_editor_<?php echo $this->product->product_id; ?>( );" /></div>
        		<div class="ec_details_edit_button" id="ec_details_save_description_<?php echo $this->product->product_id; ?>"><input type="button" value="Save Description" onclick="ec_admin_save_description_editor_<?php echo $this->product->product_id; ?>( );" /></div>
            </div>
            
            <div class="ec_details_description_editor ec_details_description_editor_<?php echo $this->product->product_id; ?>">
				<?php
					$content = do_shortcode( stripslashes( $this->product->description ) );
					$content = str_replace( ']]>', ']]&gt;', $content );
					wp_editor( $content, 'desc_' . $this->product->model_number, array( ) );
				?>
            </div>
        	<?php } 
			//END CONTENT EDITING SECTION ?>
            
            <div class="ec_details_description_content ec_details_description_content_<?php echo $this->product->product_id; ?>">
				<?php
                	if( substr( $this->product->description, 0, 3 ) == "[ec" ){
                		$this->product->display_product_description( );
					}else{
        				$content = do_shortcode( stripslashes( $this->product->description ) );
						$content = str_replace( ']]>', ']]&gt;', $content );
						echo $content;
					}
				?>
			</div>
            
		</div>
        
        <?php if( $this->product->use_specifications ){ ?>
        <div class="ec_details_specifications_tab ec_details_specifications_tab_<?php echo $this->product->product_id; ?>">
        
        	<?php 
			// START CONTENT EDITING SECTION
			if( $is_admin && !$is_preview && substr( $this->product->specifications, 0, 3 ) != "[ec" ){ ?>
            <div class="ec_details_edit_buttons">
            	<div class="ec_details_edit_button" id="ec_details_edit_specifications_<?php echo $this->product->product_id; ?>"><input type="button" value="Edit Specifications" onclick="ec_admin_show_specifications_editor_<?php echo $this->product->product_id; ?>( );" /></div>
        		<div class="ec_details_edit_button" id="ec_details_save_specifications_<?php echo $this->product->product_id; ?>"><input type="button" value="Save Specifications" onclick="ec_admin_save_specifications_editor_<?php echo $this->product->product_id; ?>( );" /></div>
            </div>
            
            <div class="ec_details_specifications_editor ec_details_specifications_editor_<?php echo $this->product->product_id; ?>">
				<?php
                	$content = do_shortcode( stripslashes( $this->product->specifications ) );
					$content = stripslashes( str_replace( ']]>', ']]&gt;', $content ) );
					wp_editor( $content, 'specs_' . $this->product->model_number, array( ) );
        		?>
            </div>
        	<?php } 
			//END CONTENT EDITING SECTION ?>
            
            <div class="ec_details_specifications_content ec_details_specifications_content_<?php echo $this->product->product_id; ?>">
				<?php
        			if( substr( $this->product->specifications, 0, 3 ) == "[ec" ){
                		$this->product->display_product_specifications( );
					}else{
        				$content = do_shortcode( stripslashes( $this->product->specifications ) );
						$content = stripslashes( str_replace( ']]>', ']]&gt;', $content ) );
						echo $content;
					}
				?>
			</div>
        
        </div>
        <?php }?>
        
        <?php 
		/* START CUSTOMER REVIEW AREA */
		if( $this->product->use_customer_reviews ){ ?>
        <div class="ec_details_customer_reviews_tab ec_details_customer_reviews_tab_<?php echo $this->product->product_id; ?>">
        	<?php if( count( $this->product->reviews ) > 0 ){ ?>
            <div class="ec_details_customer_reviews_left">
            	<h3><?php echo count( $this->product->reviews ); ?> <?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_reviews_for_text' ); ?> <?php echo $this->product->title; ?></h3>
            	<?php $perpage = apply_filters( 'wp_easycart_reviews_pagnation_perpage', 6 ); ?>
				<input type="hidden" id="ec_details_reviews_per_page_<?php echo $this->product->product_id; ?>" value="<?php echo $perpage; ?>" />
                <ul class="ec_details_customer_review_list" data-product-id="<?php echo $this->product->product_id; ?>">
                	<?php foreach( $this->product->reviews as $review_row ){ 
					$review = new ec_review( $review_row );
					?>
                    <li>
            	    	<div>
                        	<span class="ec_details_customer_review_date"><strong><?php echo stripslashes( $review->title ); ?></strong> - <?php echo $review->review_date; ?></span>
                            <?php if( get_option( 'ec_option_customer_review_show_user_name' ) ){ ?><span class="ec_details_customer_review_name"><?php echo $review->reviewer_name; ?></span><?php }?>
                        	<span class="ec_details_customer_review_stars" title="Rated <?php echo $review->rating; ?> of 5"><?php echo $review->display_review_stars( ); ?></span>
                        </div>
            	        <div class="ec_details_customer_review_data"><?php echo nl2br( stripslashes( $review->description ) ); ?></div>
            	    </li>
                    <?php } ?>
            	</ul>
      		</div>
            <?php }else{ ?>
            <div class="ec_details_customer_reviews_left"><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_review_no_reviews' ); ?></div>
            <?php } ?>
            <?php if( !get_option( 'ec_option_customer_review_require_login' ) || ( $GLOBALS['ec_cart_data']->cart_data->user_id != "" && $GLOBALS['ec_cart_data']->cart_data->user_id != 0 ) ){ ?>
            <div class="ec_details_customer_reviews_form">
            	<div class="ec_details_customer_reviews_form_holder">
                    <div class="ec_details_customer_review_loader_holder" id="ec_details_customer_review_loader_<?php echo $this->product->product_id; ?>">
                        <div class="ec_details_customer_review_loader"><?php echo $GLOBALS['language']->get_text( 'customer_review', 'product_details_submitting_review' ); ?></div>
                    </div>
                    <div class="ec_details_customer_review_success_holder" id="ec_details_customer_review_success_<?php echo $this->product->product_id; ?>">
                        <div class="ec_details_customer_review_success"><?php echo $GLOBALS['language']->get_text( 'customer_review', 'product_details_review_submitted' ); ?></div>
                    </div>
                    <h3><?php echo $GLOBALS['language']->get_text( 'customer_review', 'product_details_add_a_review_for' ); ?> <?php echo $this->product->title; ?></h3>
                    <div class="ec_details_option_row_error" id="ec_details_review_error_<?php echo $this->product->product_id; ?>"><?php echo $GLOBALS['language']->get_text( 'customer_review', 'review_error' ); ?></div>
                    <div class="ec_details_customer_reviews_row"><?php echo $GLOBALS['language']->get_text( 'customer_review', 'product_details_your_review_title' ); ?></div>
                    <div class="ec_details_customer_reviews_row ec_lower_space"><input type="text" id="ec_review_title_<?php echo $this->product->product_id; ?>" /></div>
                    <div class="ec_details_customer_reviews_row"><?php echo $GLOBALS['language']->get_text( 'customer_review', 'product_details_your_review_rating' ); ?></div>
                    <div class="ec_details_customer_reviews_row ec_stars">
                        <div class="ec_product_details_star_off ec_details_review_input ec_details_review_input_<?php echo $this->product->product_id; ?>" data-review-score="1" id="ec_details_review_star1_<?php echo $this->product->product_id; ?>"></div>
                        <div class="ec_product_details_star_off ec_details_review_input ec_details_review_input_<?php echo $this->product->product_id; ?>" data-review-score="2" id="ec_details_review_star2_<?php echo $this->product->product_id; ?>"></div>
                        <div class="ec_product_details_star_off ec_details_review_input ec_details_review_input_<?php echo $this->product->product_id; ?>" data-review-score="3" id="ec_details_review_star3_<?php echo $this->product->product_id; ?>"></div>
                        <div class="ec_product_details_star_off ec_details_review_input ec_details_review_input_<?php echo $this->product->product_id; ?>" data-review-score="4" id="ec_details_review_star4_<?php echo $this->product->product_id; ?>"></div>
                        <div class="ec_product_details_star_off ec_details_review_input ec_details_review_input_<?php echo $this->product->product_id; ?>" data-review-score="5" id="ec_details_review_star5_<?php echo $this->product->product_id; ?>"></div>
                    </div>
                    <div class="ec_details_customer_reviews_row"><?php echo $GLOBALS['language']->get_text( 'customer_review', 'product_details_your_review_message' ); ?></div>
                    <div class="ec_details_customer_reviews_row ec_lower_space"><textarea id="ec_review_message_<?php echo $this->product->product_id; ?>"></textarea></div>
                    <div class="ec_details_customer_reviews_row ec_details_submit_review_button_row" id="ec_details_submit_review_button_row_<?php echo $this->product->product_id; ?>"><input type="button" value="<?php echo $GLOBALS['language']->get_text( 'customer_review', 'product_details_your_review_submit' ); ?>" onclick="ec_submit_product_review_<?php echo $this->product->product_id; ?>( )" /></div>
                    <div class="ec_details_customer_reviews_row ec_details_review_submitted_button_row" id="ec_details_review_submitted_button_row_<?php echo $this->product->product_id; ?>"><input type="button" disabled="disabled" value="<?php echo $GLOBALS['language']->get_text( 'customer_review', 'product_details_review_submitted_button' ); ?>" /></div>
                </div>
            </div>
            <?php }else{ ?>
            <div class="ec_details_customer_reviews_form">
            	<div class="ec_details_customer_reviews_form_holder">
                	<?php echo $GLOBALS['language']->get_text( 'customer_review', 'product_details_review_log_in_first' ); ?>
                </div> 
            </div>
            <?php }?>
        </div>
        <?php }?>
        
        <?php do_action( 'wpeasycart_addon_product_details_tab_content', $this->product->product_id ); ?>
        
    </div>
    
    <?php /* START RELATED PRODUCTS AREA */ ?>
    <?php if( $this->product->featured_products->product1 || $this->product->featured_products->product2 || $this->product->featured_products->product3 || $this->product->featured_products->product4 ){ ?>
    <div class="ec_details_related_products_area">
    	<h3><?php echo $GLOBALS['language']->get_text( 'product_details', 'product_details_related_products' ); ?></h3>
    	<ul class="ec_details_related_products">
        	<?php if( $this->product->featured_products->product1 ){ ?>
            
            <?php
			$product = $this->product->featured_products->product1;
			if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_product.php' ) )	
				include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option('ec_option_base_layout') . '/ec_product.php' );
			else if( file_exists( WP_PLUGIN_DIR . '/' . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_product.php' ) )	
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option('ec_option_latest_layout') . '/ec_product.php' );
            ?>
			
            <?php } ?>
            
        	<?php if( $this->product->featured_products->product2 ){ ?>
            
            <?php
			$product = $this->product->featured_products->product2;
			if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_product.php' ) )	
				include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option('ec_option_base_layout') . '/ec_product.php' );
			else if( file_exists( WP_PLUGIN_DIR . '/' . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_product.php' ) )	
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option('ec_option_latest_layout') . '/ec_product.php' );
            ?>
            
            <?php } ?>
            
        	<?php if( $this->product->featured_products->product3 ){ ?>
            
            <?php
			$product = $this->product->featured_products->product3;
			if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_product.php' ) )	
				include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option('ec_option_base_layout') . '/ec_product.php' );
			else if( file_exists( WP_PLUGIN_DIR . '/' . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_product.php' ) )	
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option('ec_option_latest_layout') . '/ec_product.php' );
            ?>
            
            <?php } ?>
            
        	<?php if( $this->product->featured_products->product4 ){ ?>
            
            <?php
			$product = $this->product->featured_products->product4;
			if( file_exists( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option( 'ec_option_base_layout' ) . '/ec_product.php' ) )	
				include( WP_PLUGIN_DIR . '/wp-easycart-data/design/layout/' . get_option('ec_option_base_layout') . '/ec_product.php' );
			else if( file_exists( WP_PLUGIN_DIR . '/' . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option( 'ec_option_latest_layout' ) . '/ec_product.php' ) )	
				include( WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/design/layout/' . get_option('ec_option_latest_layout') . '/ec_product.php' );
            ?>
            
            <?php } ?>
        </ul>
	</div>
    <?php }?>
	<div style="clear:both;"></div>
</section>
<div style="clear:both;"></div>

<script>var img = new Image( );
var img_width = 400;
var img_height = 400;
var img_ratio = 1;
var tier_quantities_<?php echo $this->product->product_id; ?> = [<?php if( !$this->product->using_role_price ){ for( $tier_i = 0; $tier_i < count( $this->product->pricetiers ); $tier_i++ ){ if( $tier_i > 0 ){ echo ","; } echo $this->product->pricetiers[$tier_i][1]; } } ?>];
var tier_prices_<?php echo $this->product->product_id; ?> = [<?php if( !$this->product->using_role_price ){ for( $tier_i = 0; $tier_i < count( $this->product->pricetiers ); $tier_i++ ){ if( $tier_i > 0 ){ echo ","; } echo $this->product->pricetiers[$tier_i][0]; } } ?>];<?php if( !$ipad && !$iphone && get_option( 'ec_option_show_magnification' ) ){?>
jQuery( '.ec_details_main_image_<?php echo $this->product->product_id; ?>' ).mousemove( function( e ){
	var parentOffset = jQuery( this ).parent( ).offset( ); 
	var mouse_x = e.pageX - parentOffset.left;
	var mouse_y = e.pageY - parentOffset.top;
	var div_width = jQuery( this ).width( );
	var div_height = jQuery( this ).height( );
	var x_val = '-' + ( ( img_width - div_width ) * ( mouse_x / div_width ) ) + 'px';
	var y_val = '-' + ( ( img_height - div_height ) * ( mouse_y / div_height ) ) + 'px';
	jQuery( '.ec_details_magbox_image_<?php echo $this->product->product_id; ?>' ).css( 'background-position', x_val + ' ' + y_val );	
} );
jQuery( '.ec_details_main_image_<?php echo $this->product->product_id; ?>' ).hover( 
	function( ){ 
		img = new Image( );
		img.onload = function( ){
			img_width = this.width;
			img_height = this.height;
			img_ratio =  img_height / img_width;
			jQuery( '.ec_details_magbox' ).css( 'width', jQuery( '.ec_details_main_image_<?php echo $this->product->product_id; ?>' ).width( ) + 'px' ).css( 'height', jQuery( '.ec_details_main_image_<?php echo $this->product->product_id; ?>' ).height( ) + 'px' );
			jQuery( '.ec_details_magbox_image_<?php echo $this->product->product_id; ?>' ).css( 'width', jQuery( '.ec_details_main_image_<?php echo $this->product->product_id; ?>' ).width( ) + 'px' ).css( 'height', jQuery( '.ec_details_main_image_<?php echo $this->product->product_id; ?>' ).height( ) + 'px' );
		}
		img.src = jQuery( this ).find( 'img' ).attr( 'src' );
		jQuery( '.ec_details_magbox_<?php echo $this->product->product_id; ?>' ).fadeIn( 'fast' ); 
	}, 
	function( ){ 
		jQuery( '.ec_details_magbox_<?php echo $this->product->product_id; ?>' ).fadeOut( 'fast' ); 
	} 
);<?php }?>
jQuery( '.ec_details_thumbnail_<?php echo $this->product->product_id; ?>' ).click( function( e ){ 
	var src = jQuery( this ).find( 'img' ).attr( 'src' );
	jQuery( '.ec_details_thumbnail_<?php echo $this->product->product_id; ?>' ).removeClass( 'ec_active' );
	jQuery( this ).addClass( 'ec_active' );
	jQuery( '.ec_details_main_image_<?php echo $this->product->product_id; ?>' ).find( 'img' ).attr( 'src', src );
	jQuery( '.ec_details_large_popup_main_<?php echo $this->product->product_id; ?>' ).find( 'img' ).attr( 'src', src );
	jQuery( '.ec_details_magbox_image_<?php echo $this->product->product_id; ?>' ).css( 'background', 'url( "' + src + '" ) no-repeat' );
});
jQuery( '.ec_details_large_popup_thumbnail_<?php echo $this->product->product_id; ?>' ).click( function( e ){
	var src = jQuery( this ).find( 'img' ).attr( 'src' );
	jQuery( '.ec_details_large_popup_thumbnail_<?php echo $this->product->product_id; ?>' ).removeClass( 'ec_active' );
	jQuery( this ).addClass( 'ec_active' );
	jQuery( '.ec_details_thumbnail_<?php echo $this->product->product_id; ?>' ).removeClass( 'ec_active' );
	jQuery( '.ec_details_main_image_<?php echo $this->product->product_id; ?>' ).find( 'img' ).attr( 'src', src );
	jQuery( '.ec_details_large_popup_main_<?php echo $this->product->product_id; ?>' ).find( 'img' ).attr( 'src', src );
	jQuery( '.ec_details_magbox_image_<?php echo $this->product->product_id; ?>' ).css( 'background', 'url( "' + src + '" ) no-repeat' );
});
jQuery( '.ec_minus' ).click( function( ){
	<?php if( $this->product->use_advanced_optionset ){ ?>ec_details_advanced_adjust_price_<?php echo $this->product->product_id; ?>( );<?php }else{ ?>ec_details_base_adjust_price_<?php echo $this->product->product_id; ?>( );<?php }?>
} );
jQuery( '.ec_plus' ).click( function( ){
	<?php if( $this->product->use_advanced_optionset ){ ?>ec_details_advanced_adjust_price_<?php echo $this->product->product_id; ?>( );<?php }else{ ?>ec_details_base_adjust_price_<?php echo $this->product->product_id; ?>( );<?php }?>
} );
jQuery( document.getElementById( 'ec_quantity_<?php echo $this->product->product_id; ?>' ) ).change( function( ){
	<?php if( $this->product->use_advanced_optionset ){ ?>ec_details_advanced_adjust_price_<?php echo $this->product->product_id; ?>( );<?php }else{ ?>ec_details_base_adjust_price_<?php echo $this->product->product_id; ?>( );<?php }?>
} );
jQuery( document.getElementById( 'ec_donation_amount_<?php echo $this->product->product_id; ?>' ) ).change( function( ){
	<?php if( $this->product->use_advanced_optionset ){ ?>ec_details_advanced_adjust_price_<?php echo $this->product->product_id; ?>( );<?php }else{ ?>ec_details_base_adjust_price_<?php echo $this->product->product_id; ?>( );<?php }?>
} );
jQuery( '.ec_details_tab_<?php echo $this->product->product_id; ?>' ).click( function( ){
	jQuery( '.ec_details_tab_<?php echo $this->product->product_id; ?>' ).removeClass( 'ec_active' );
	jQuery( this ).addClass( 'ec_active' );
	jQuery( '.ec_details_extra_area_<?php echo $this->product->product_id; ?>' ).children( 'div' ).each( function( ){ jQuery( this ).hide( ) } );
	if( jQuery( this ).hasClass( 'ec_description' ) )
		jQuery( '.ec_details_description_tab_<?php echo $this->product->product_id; ?>' ).show( );
	else if( jQuery( this ).hasClass( 'ec_specifications' ) )
		jQuery( '.ec_details_specifications_tab_<?php echo $this->product->product_id; ?>' ).show( );
	else if( jQuery( this ).hasClass( 'ec_customer_reviews' ) )
		jQuery( '.ec_details_customer_reviews_tab_<?php echo $this->product->product_id; ?>' ).show( );
	else
		jQuery( '.ec_details_' + jQuery( this ).attr( 'data-tab-id' ) + '_tab' ).show( );
} );
jQuery( '.ec_details_swatches > li.ec_option1_<?php echo $this->product->product_id; ?>' ).click( function( ){
	if( jQuery( this ).hasClass( 'ec_active' ) ){
		var optionitem_id_1 = jQuery( this ).attr( 'data-optionitem-id' );
		var quantity = jQuery( this ).attr( 'data-optionitem-quantity' );
		<?php if( $this->product->use_optionitem_quantity_tracking ){ ?>
		ec_option1_selected_<?php echo $this->product->product_id; ?>( optionitem_id_1, quantity );
		<?php }?>
		ec_option1_image_change_<?php echo $this->product->product_id; ?>( optionitem_id_1, quantity );
		jQuery( '.ec_details_swatches > li.ec_option1_<?php echo $this->product->product_id; ?>' ).removeClass( 'ec_selected' );
		jQuery( this ).addClass( 'ec_selected' );
		jQuery( '.ec_option1_<?php echo $this->product->product_id; ?>.ec_details_option_row_error' ).hide( );
		jQuery( document.getElementById( 'ec_option1_<?php echo $this->product->product_id; ?>' ) ).val( optionitem_id_1 );
		jQuery( this ).parent( ).parent( ).find( '.ec_details_option_label' ).find( '.ec_details_option_label_selected' ).html( jQuery( this ).find( 'img' ).attr( 'title' ) );
		ec_details_base_adjust_price_<?php echo $this->product->product_id; ?>( );
	}
} );
jQuery( '.ec_details_swatches > li.ec_option2_<?php echo $this->product->product_id; ?>' ).click( function( ){
	if( jQuery( this ).hasClass( 'ec_active' ) ){
		var optionitem_id_1 = 0;
		if( jQuery( '.ec_details_swatches > li.ec_option1_<?php echo $this->product->product_id; ?>.ec_selected' ).length ){
			optionitem_id_1 = jQuery( '.ec_details_swatches > li.ec_option1_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-id' );
		}else{
			optionitem_id_1 = jQuery( '.ec_details_combo.ec_option1_<?php echo $this->product->product_id; ?>' ).val( );
		}
		var optionitem_id_2 = jQuery( this ).attr( 'data-optionitem-id' );
		<?php if( $this->product->use_optionitem_quantity_tracking ){ ?>
		var quantity = jQuery( this ).attr( 'data-optionitem-quantity' );
		option2_selected_<?php echo $this->product->product_id; ?>( optionitem_id_1, optionitem_id_2, quantity );
		<?php }?>
		jQuery( '.ec_details_swatches > li.ec_option2_<?php echo $this->product->product_id; ?>' ).removeClass( 'ec_selected' );
		jQuery( this ).addClass( 'ec_selected' );
		jQuery( '.ec_option2_<?php echo $this->product->product_id; ?>.ec_details_option_row_error' ).hide( );
		jQuery( document.getElementById( 'ec_option2_<?php echo $this->product->product_id; ?>' ) ).val( optionitem_id_2 );
		jQuery( this ).parent( ).parent( ).find( '.ec_details_option_label' ).find( '.ec_details_option_label_selected' ).html( jQuery( this ).find( 'img' ).attr( 'title' ) );
		ec_details_base_adjust_price_<?php echo $this->product->product_id; ?>( );
	}
} );
jQuery( '.ec_details_swatches > li.ec_option3_<?php echo $this->product->product_id; ?>' ).click( function( ){
	if( jQuery( this ).hasClass( 'ec_active' ) ){
		var optionitem_id_1 = 0;
		if( jQuery( '.ec_details_swatches > li.ec_option1_<?php echo $this->product->product_id; ?>.ec_selected' ).length ){
			optionitem_id_1 = jQuery( '.ec_details_swatches > li.ec_option1_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-id' );
		}else{
			optionitem_id_1 = jQuery( '.ec_details_combo.ec_option1_<?php echo $this->product->product_id; ?>' ).val( );
		}
		var optionitem_id_2 = 0;
		if( jQuery( '.ec_details_swatches > li.ec_option2_<?php echo $this->product->product_id; ?>.ec_selected' ).length ){
			optionitem_id_2 = jQuery( '.ec_details_swatches > li.ec_option2_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-id' );
		}else{
			optionitem_id_2 = jQuery( '.ec_details_combo.ec_option2_<?php echo $this->product->product_id; ?>' ).val( );
		}
		var optionitem_id_3 = jQuery( this ).attr( 'data-optionitem-id' );
		<?php if( $this->product->use_optionitem_quantity_tracking ){ ?>
		var quantity = jQuery( this ).attr( 'data-optionitem-quantity' );
		option3_selected_<?php echo $this->product->product_id; ?>( optionitem_id_1, optionitem_id_2, optionitem_id_3, quantity );
		<?php }?>
		jQuery( '.ec_details_swatches > li.ec_option3_<?php echo $this->product->product_id; ?>' ).removeClass( 'ec_selected' );
		jQuery( this ).addClass( 'ec_selected' );
		jQuery( '.ec_option3_<?php echo $this->product->product_id; ?>.ec_details_option_row_error' ).hide( );
		jQuery( document.getElementById( 'ec_option3_<?php echo $this->product->product_id; ?>' ) ).val( optionitem_id_3 );
		jQuery( this ).parent( ).parent( ).find( '.ec_details_option_label' ).find( '.ec_details_option_label_selected' ).html( jQuery( this ).find( 'img' ).attr( 'title' ) );
		ec_details_base_adjust_price_<?php echo $this->product->product_id; ?>( );
	}
} );
jQuery( '.ec_details_swatches > li.ec_option4_<?php echo $this->product->product_id; ?>' ).click( function( ){
	if( jQuery( this ).hasClass( 'ec_active' ) ){
		var optionitem_id_1 = 0;
		if( jQuery( '.ec_details_swatches > li.ec_option1_<?php echo $this->product->product_id; ?>.ec_selected' ).length ){
			optionitem_id_1 = jQuery( '.ec_details_swatches > li.ec_option1_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-id' );
		}else{
			optionitem_id_1 = jQuery( '.ec_details_combo.ec_option1_<?php echo $this->product->product_id; ?>' ).val( );
		}
		var optionitem_id_2 = 0;
		if( jQuery( '.ec_details_swatches > li.ec_option2_<?php echo $this->product->product_id; ?>.ec_selected' ).length ){
			optionitem_id_2 = jQuery( '.ec_details_swatches > li.ec_option2_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-id' );
		}else{
			optionitem_id_2 = jQuery( '.ec_details_combo.ec_option2_<?php echo $this->product->product_id; ?>' ).val( );
		}
		var optionitem_id_3 = 0;
		if( jQuery( '.ec_details_swatches > li.ec_option3_<?php echo $this->product->product_id; ?>.ec_selected' ).length ){
			optionitem_id_3 = jQuery( '.ec_details_swatches > li.ec_option3_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-id' );
		}else{
			optionitem_id_3 = jQuery( '.ec_details_combo.ec_option3_<?php echo $this->product->product_id; ?>' ).val( );
		}
		var optionitem_id_4 = jQuery( this ).attr( 'data-optionitem-id' );
		<?php if( $this->product->use_optionitem_quantity_tracking ){ ?>
		var quantity = jQuery( this ).attr( 'data-optionitem-quantity' );
		option4_selected_<?php echo $this->product->product_id; ?>( optionitem_id_1, optionitem_id_2, optionitem_id_3, optionitem_id_4, quantity );
		<?php }?>
		jQuery( '.ec_details_swatches > li.ec_option4_<?php echo $this->product->product_id; ?>' ).removeClass( 'ec_selected' );
		jQuery( this ).addClass( 'ec_selected' );
		jQuery( '.ec_option4_<?php echo $this->product->product_id; ?>.ec_details_option_row_error' ).hide( );
		jQuery( document.getElementById( 'ec_option4_<?php echo $this->product->product_id; ?>' ) ).val( optionitem_id_4 );
		jQuery( this ).parent( ).parent( ).find( '.ec_details_option_label' ).find( '.ec_details_option_label_selected' ).html( jQuery( this ).find( 'img' ).attr( 'title' ) );
		ec_details_base_adjust_price_<?php echo $this->product->product_id; ?>( );
	}
} );
jQuery( '.ec_details_swatches > li.ec_option5_<?php echo $this->product->product_id; ?>' ).click( function( ){
	if( jQuery( this ).hasClass( 'ec_active' ) ){
		var optionitem_id_5 = jQuery( this ).attr( 'data-optionitem-id' );
		jQuery( '.ec_details_swatches > li.ec_option5_<?php echo $this->product->product_id; ?>' ).removeClass( 'ec_selected' );
		var quantity = jQuery( this ).attr( 'data-optionitem-quantity' );
		jQuery( document.getElementById( 'ec_details_stock_quantity_<?php echo $this->product->product_id; ?>' ) ).html( quantity );
		jQuery( document.getElementById('ec_quantity_<?php echo $this->product->product_id; ?>' ) ).attr( 'max', quantity );
		if( Number( jQuery( document.getElementById('ec_quantity_<?php echo $this->product->product_id; ?>' ) ).val( ) ) > Number( quantity ) ){
			 jQuery( document.getElementById('ec_quantity_<?php echo $this->product->product_id; ?>' ) ).val( quantity );
		}
		jQuery( this ).addClass( 'ec_selected' );
		jQuery( '.ec_option5_<?php echo $this->product->product_id; ?>.ec_details_option_row_error' ).hide( );
		jQuery( document.getElementById( 'ec_option5_<?php echo $this->product->product_id; ?>' ) ).val( optionitem_id_5 );
		jQuery( this ).parent( ).parent( ).find( '.ec_details_option_label' ).find( '.ec_details_option_label_selected' ).html( jQuery( this ).find( 'img' ).attr( 'title' ) );
		ec_details_base_adjust_price_<?php echo $this->product->product_id; ?>( );
	}
} );
jQuery( '.ec_details_combo.ec_option1_<?php echo $this->product->product_id; ?>' ).change( function( ){
	var optionitem_id_1 = jQuery( '.ec_details_combo.ec_option1_<?php echo $this->product->product_id; ?>' ).val( );
	var quantity = jQuery( '.ec_details_combo.ec_option1_<?php echo $this->product->product_id; ?> option:selected' ).attr( 'data-optionitem-quantity' );
	<?php if( $this->product->use_optionitem_quantity_tracking ){ ?>
	if( optionitem_id_1 != '0' ){
		ec_option1_selected_<?php echo $this->product->product_id; ?>( optionitem_id_1, quantity );
	}else{
		jQuery( '.ec_details_combo.ec_option2_<?php echo $this->product->product_id; ?>' ).val( '0' ).prop( 'disabled', true ).addClass( 'ec_inactive' );
		jQuery( '.ec_details_combo.ec_option3_<?php echo $this->product->product_id; ?>' ).val( '0' ).prop( 'disabled', true ).addClass( 'ec_inactive' );
		jQuery( '.ec_details_combo.ec_option4_<?php echo $this->product->product_id; ?>' ).val( '0' ).prop( 'disabled', true ).addClass( 'ec_inactive' );
		jQuery( '.ec_details_combo.ec_option5_<?php echo $this->product->product_id; ?>' ).val( '0' ).prop( 'disabled', true ).addClass( 'ec_inactive' );
		jQuery( document.getElementById( 'ec_details_stock_quantity_<?php echo $this->product->product_id; ?>' ) ).html( quantity );	
	}
	<?php }?>
	ec_option1_image_change_<?php echo $this->product->product_id; ?>( optionitem_id_1, quantity );
	jQuery( '.ec_option1_<?php echo $this->product->product_id; ?>.ec_details_option_row_error' ).hide( );
	ec_details_base_adjust_price_<?php echo $this->product->product_id; ?>( );
} );
jQuery( '.ec_details_combo.ec_option2_<?php echo $this->product->product_id; ?>' ).change( function( ){
	<?php if( $this->product->use_optionitem_quantity_tracking ){ ?>
	var optionitem_id_1 = 0;
	if( jQuery( '.ec_details_swatches > li.ec_option1_<?php echo $this->product->product_id; ?>.ec_selected' ).length ){
		optionitem_id_1 = jQuery( '.ec_details_swatches > li.ec_option1_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-id' );
	}else{
		optionitem_id_1 = jQuery( '.ec_details_combo.ec_option1_<?php echo $this->product->product_id; ?>' ).val( );
	}
	var optionitem_id_2 = jQuery( '.ec_details_combo.ec_option2_<?php echo $this->product->product_id; ?>' ).val( );
	var quantity = jQuery( '.ec_details_combo.ec_option2_<?php echo $this->product->product_id; ?> option:selected' ).attr( 'data-optionitem-quantity' );
	if( optionitem_id_2 != '0' ){
		option2_selected_<?php echo $this->product->product_id; ?>( optionitem_id_1, optionitem_id_2, quantity );
	}else{
		jQuery( document.getElementById( 'ec_details_stock_quantity_<?php echo $this->product->product_id; ?>' ) ).html( quantity );	
	}
	<?php }?>
	jQuery( '.ec_option2_<?php echo $this->product->product_id; ?>.ec_details_option_row_error' ).hide( );
	ec_details_base_adjust_price_<?php echo $this->product->product_id; ?>( );
} );
jQuery( '.ec_details_combo.ec_option3_<?php echo $this->product->product_id; ?>' ).change( function( ){
	<?php if( $this->product->use_optionitem_quantity_tracking ){ ?>
	var optionitem_id_1 = 0;
	if( jQuery( '.ec_details_swatches > li.ec_option1_<?php echo $this->product->product_id; ?>.ec_selected' ).length ){
		optionitem_id_1 = jQuery( '.ec_details_swatches > li.ec_option1_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-id' );
	}else{
		optionitem_id_1 = jQuery( '.ec_details_combo.ec_option1_<?php echo $this->product->product_id; ?>' ).val( );
	}
	var optionitem_id_2 = 0;
	if( jQuery( '.ec_details_swatches > li.ec_option2_<?php echo $this->product->product_id; ?>.ec_selected' ).length ){
		optionitem_id_2 = jQuery( '.ec_details_swatches > li.ec_option2_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-id' );
	}else{
		optionitem_id_2 = jQuery( '.ec_details_combo.ec_option2_<?php echo $this->product->product_id; ?>' ).val( );
	}
	var optionitem_id_3 = jQuery( '.ec_details_combo.ec_option3_<?php echo $this->product->product_id; ?>' ).val( );
	var quantity = jQuery( '.ec_details_combo.ec_option3_<?php echo $this->product->product_id; ?> option:selected' ).attr( 'data-optionitem-quantity' );
	if( optionitem_id_3 != '0' ){
		option3_selected_<?php echo $this->product->product_id; ?>( optionitem_id_1, optionitem_id_2, optionitem_id_3, quantity );
	}else{
		jQuery( document.getElementById( 'ec_details_stock_quantity_<?php echo $this->product->product_id; ?>' ) ).html( quantity );	
	}
	<?php }?>
	jQuery( '.ec_option3_<?php echo $this->product->product_id; ?>.ec_details_option_row_error' ).hide( );
	ec_details_base_adjust_price_<?php echo $this->product->product_id; ?>( );
} );
jQuery( '.ec_details_combo.ec_option4_<?php echo $this->product->product_id; ?>' ).change( function( ){
	<?php if( $this->product->use_optionitem_quantity_tracking ){ ?>
	var optionitem_id_1 = 0;
	if( jQuery( '.ec_details_swatches > li.ec_option1_<?php echo $this->product->product_id; ?>.ec_selected' ).length ){
		optionitem_id_1 = jQuery( '.ec_details_swatches > li.ec_option1_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-id' );
	}else{
		optionitem_id_1 = jQuery( '.ec_details_combo.ec_option1_<?php echo $this->product->product_id; ?>' ).val( );
	}
	var optionitem_id_2 = 0;
	if( jQuery( '.ec_details_swatches > li.ec_option2_<?php echo $this->product->product_id; ?>.ec_selected' ).length ){
		optionitem_id_2 = jQuery( '.ec_details_swatches > li.ec_option2_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-id' );
	}else{
		optionitem_id_2 = jQuery( '.ec_details_combo.ec_option2_<?php echo $this->product->product_id; ?>' ).val( );
	}
	var optionitem_id_3 = 0;
	if( jQuery( '.ec_details_swatches > li.ec_option3_<?php echo $this->product->product_id; ?>.ec_selected' ).length ){
		optionitem_id_3 = jQuery( '.ec_details_swatches > li.ec_option3_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-id' );
	}else{
		optionitem_id_3 = jQuery( '.ec_details_combo.ec_option3_<?php echo $this->product->product_id; ?>' ).val( );
	}
	var optionitem_id_4 = jQuery( '.ec_details_combo.ec_option4_<?php echo $this->product->product_id; ?>' ).val( );
	var quantity = jQuery( '.ec_details_combo.ec_option4_<?php echo $this->product->product_id; ?> option:selected' ).attr( 'data-optionitem-quantity' );
	if( optionitem_id_4 != '0' ){
		option4_selected_<?php echo $this->product->product_id; ?>( optionitem_id_1, optionitem_id_2, optionitem_id_3, optionitem_id_4, quantity );
	}else{
		jQuery( document.getElementById( 'ec_details_stock_quantity_<?php echo $this->product->product_id; ?>' ) ).html( quantity );	
	}
	<?php }?>
	jQuery( '.ec_option4_<?php echo $this->product->product_id; ?>.ec_details_option_row_error' ).hide( );
	ec_details_base_adjust_price_<?php echo $this->product->product_id; ?>( );
} );
jQuery( '.ec_details_combo.ec_option5_<?php echo $this->product->product_id; ?>' ).change( function( ){
	<?php if( $this->product->use_optionitem_quantity_tracking ){ ?>
	var quantity = jQuery( '.ec_details_combo.ec_option5_<?php echo $this->product->product_id; ?> option:selected' ).attr( 'data-optionitem-quantity' );
	jQuery( document.getElementById( 'ec_details_stock_quantity_<?php echo $this->product->product_id; ?>' ) ).html( quantity );
	<?php }?>
	jQuery( '.ec_option5_<?php echo $this->product->product_id; ?>.ec_details_option_row_error' ).hide( );
	ec_details_base_adjust_price_<?php echo $this->product->product_id; ?>( );
} );
jQuery( '.ec_details_checkbox_row > input, .ec_details_radio_row > input' ).click( function( ){
	ec_details_advanced_conditional_logic_<?php echo $this->product->product_id; ?>( );
	ec_details_advanced_adjust_price_<?php echo $this->product->product_id; ?>( );
} );
jQuery( '.ec_details_option_row.ec_option_type_combo > .ec_details_option_data > select' ).change( function( ){
	ec_details_advanced_conditional_logic_<?php echo $this->product->product_id; ?>( );
	ec_details_advanced_adjust_price_<?php echo $this->product->product_id; ?>( );
} );
jQuery( '.ec_details_option_row.ec_option_type_date > .ec_details_option_data > input' ).change( function( ){
	ec_details_advanced_conditional_logic_<?php echo $this->product->product_id; ?>( );
	ec_details_advanced_adjust_price_<?php echo $this->product->product_id; ?>( );
} );
jQuery( '.ec_details_option_row.ec_option_type_file > .ec_details_option_data > input' ).change( function( ){
	ec_details_advanced_conditional_logic_<?php echo $this->product->product_id; ?>( );
	ec_details_advanced_adjust_price_<?php echo $this->product->product_id; ?>( );
} );
jQuery( '.ec_details_option_row.ec_option_type_text > .ec_details_option_data > input' ).change( function( ){
	ec_details_advanced_conditional_logic_<?php echo $this->product->product_id; ?>( );
	ec_details_advanced_adjust_price_<?php echo $this->product->product_id; ?>( );
} );
jQuery( '.ec_details_option_row.ec_option_type_number > .ec_details_option_data > input' ).change( function( ){
	ec_details_advanced_conditional_logic_<?php echo $this->product->product_id; ?>( );
	ec_details_advanced_adjust_price_<?php echo $this->product->product_id; ?>( );
} );
jQuery( '.ec_details_option_row.ec_option_type_textarea > .ec_details_option_data > textarea' ).change( function( ){
	ec_details_advanced_conditional_logic_<?php echo $this->product->product_id; ?>( );
	ec_details_advanced_adjust_price_<?php echo $this->product->product_id; ?>( );
} );
jQuery( '.ec_details_grid_row > input' ).change( function( ){
	ec_details_advanced_conditional_logic_<?php echo $this->product->product_id; ?>( );
	ec_details_advanced_adjust_price_<?php echo $this->product->product_id; ?>( );
} );
jQuery( '.ec_details_swatches > li.ec_advanced' ).click( function( ){
	var optionitem_id = jQuery( this ).attr( 'data-optionitem-id' );
	var option_id = jQuery( this ).attr( 'data-option-id' );
	var product_id = '<?php echo $this->product->product_id; ?>';
	jQuery( document.getElementById( 'ec_option_adv_' + option_id + "_" + product_id ) ).val( optionitem_id );
	jQuery( '.ec_details_swatches > li.ec_option_adv_' + option_id + "_" + product_id ).removeClass( 'ec_selected' );
	jQuery( this ).addClass( 'ec_selected' );
	jQuery( document.getElementById( 'ec_details_option_row_error_' + option_id + "_" + product_id ) ).hide( );
	jQuery( this ).parent( ).parent( ).parent( ).find( '.ec_details_option_label' ).find( '.ec_details_option_label_selected' ).html( jQuery( this ).find( 'img' ).attr( 'title' ) );
	ec_details_advanced_conditional_logic_<?php echo $this->product->product_id; ?>( );
	ec_details_advanced_adjust_price_<?php echo $this->product->product_id; ?>( );
} );
jQuery( '.ec_details_option_row.ec_option_type_dimensions1 > .ec_details_option_data > input' ).change( function( ){
	ec_details_advanced_conditional_logic_<?php echo $this->product->product_id; ?>( );
	ec_details_advanced_adjust_price_<?php echo $this->product->product_id; ?>( );
} );
jQuery( '.ec_details_option_row.ec_option_type_dimensions2 > .ec_details_option_data > input' ).change( function( ){
	ec_details_advanced_conditional_logic_<?php echo $this->product->product_id; ?>( );
	ec_details_advanced_adjust_price_<?php echo $this->product->product_id; ?>( );
} );
jQuery( '.ec_details_option_row.ec_option_type_dimensions2 > .ec_details_option_data > select' ).change( function( ){
	ec_details_advanced_conditional_logic_<?php echo $this->product->product_id; ?>( );
	ec_details_advanced_adjust_price_<?php echo $this->product->product_id; ?>( );
} );
jQuery( document ).ready( function( ){
	ec_details_base_adjust_price_<?php echo $this->product->product_id; ?>( );
	ec_details_advanced_adjust_price_<?php echo $this->product->product_id; ?>( );
} );
function ec_details_base_adjust_price_<?php echo $this->product->product_id; ?>( ){
	if( jQuery( document.getElementById( 'ec_base_price_<?php echo $this->product->product_id; ?>' ) ).length == 0 )
		return;
	var base_price = Number( jQuery( document.getElementById( 'ec_base_price_<?php echo $this->product->product_id; ?>' ) ).html( ) );
	var current_quantity = jQuery( document.getElementById( 'ec_quantity_<?php echo $this->product->product_id; ?>' ) ).val( );
	for( var i=0; i<tier_quantities_<?php echo $this->product->product_id; ?>.length; i++ ){
		if( tier_quantities_<?php echo $this->product->product_id; ?>[i] <= current_quantity )
			base_price = Number( tier_prices_<?php echo $this->product->product_id; ?>[i] );
	}
	var option1_price_adj = 0;
	var option2_price_adj = 0;
	var option3_price_adj = 0;
	var option4_price_adj = 0;
	var option5_price_adj = 0;
	var option1_price_add = 0;
	var option2_price_add = 0;
	var option3_price_add = 0;
	var option4_price_add = 0;
	var option5_price_add = 0;
	var option1_price_override = -1;
	var option2_price_override = -1;
	var option3_price_override = -1;
	var option4_price_override = -1;
	var option5_price_override = -1;
	// Option 1 Price Adjustment
	if( jQuery( '.ec_details_swatches > li.ec_option1_<?php echo $this->product->product_id; ?>.ec_selected' ).length ){
		option1_price_adj = jQuery( '.ec_details_swatches > li.ec_option1_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-price' );
		option1_price_add = jQuery( '.ec_details_swatches > li.ec_option1_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-price-onetime' );
		option1_price_override = Number( jQuery( '.ec_details_swatches > li.ec_option1_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-price-override' ) );
		option1_price_multiplier = Number( jQuery( '.ec_details_swatches > li.ec_option1_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-price-multiplier' ) );
	}else if( jQuery( '.ec_details_combo.ec_option1_<?php echo $this->product->product_id; ?>' ).length ){
		option1_price_adj = jQuery( '.ec_details_combo.ec_option1_<?php echo $this->product->product_id; ?> option:selected' ).attr( 'data-optionitem-price' );
		option1_price_add = jQuery( '.ec_details_combo.ec_option1_<?php echo $this->product->product_id; ?> option:selected' ).attr( 'data-optionitem-price-onetime' );
		option1_price_override = Number( jQuery( '.ec_details_combo.ec_option1_<?php echo $this->product->product_id; ?> option:selected' ).attr( 'data-optionitem-price-override' ) );
		option1_price_multiplier = Number( jQuery( '.ec_details_combo.ec_option1_<?php echo $this->product->product_id; ?> option:selected' ).attr( 'data-optionitem-price-multiplier' ) );
	}
	// Option 2 Price Adjustment
	if( jQuery( '.ec_details_swatches > li.ec_option2_<?php echo $this->product->product_id; ?>.ec_selected' ).length ){
		option2_price_adj = jQuery( '.ec_details_swatches > li.ec_option2_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-price' );
		option2_price_add = jQuery( '.ec_details_swatches > li.ec_option2_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-price-onetime' );
		option2_price_override = Number( jQuery( '.ec_details_swatches > li.ec_option2_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-price-override' ) );
		option2_price_multiplier = Number( jQuery( '.ec_details_swatches > li.ec_option2_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-price-multiplier' ) );
	}else if( jQuery( '.ec_details_combo.ec_option2_<?php echo $this->product->product_id; ?>' ).length ){
		option2_price_adj = jQuery( '.ec_details_combo.ec_option2_<?php echo $this->product->product_id; ?> option:selected' ).attr( 'data-optionitem-price' );
		option2_price_add = jQuery( '.ec_details_combo.ec_option2_<?php echo $this->product->product_id; ?> option:selected' ).attr( 'data-optionitem-price-onetime' );
		option2_price_override = Number( jQuery( '.ec_details_combo.ec_option2_<?php echo $this->product->product_id; ?> option:selected' ).attr( 'data-optionitem-price-override' ) );
		option2_price_multiplier = Number( jQuery( '.ec_details_combo.ec_option2_<?php echo $this->product->product_id; ?> option:selected' ).attr( 'data-optionitem-price-multiplier' ) );
	}
	// Option 3 Price Adjustment
	if( jQuery( '.ec_details_swatches > li.ec_option3_<?php echo $this->product->product_id; ?>.ec_selected' ).length ){
		option3_price_adj = jQuery( '.ec_details_swatches > li.ec_option3_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-price' );
		option3_price_add = jQuery( '.ec_details_swatches > li.ec_option3_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-price-onetime' );
		option3_price_override = Number( jQuery( '.ec_details_swatches > li.ec_option3_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-price-override' ) );
		option3_price_multiplier = Number( jQuery( '.ec_details_swatches > li.ec_option3_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-price-multiplier' ) );
	}else if( jQuery( '.ec_details_combo.ec_option3_<?php echo $this->product->product_id; ?>' ).length ){
		option3_price_adj = jQuery( '.ec_details_combo.ec_option3_<?php echo $this->product->product_id; ?> option:selected' ).attr( 'data-optionitem-price' );
		option3_price_add = jQuery( '.ec_details_combo.ec_option3_<?php echo $this->product->product_id; ?> option:selected' ).attr( 'data-optionitem-price-onetime' );
		option3_price_override = Number( jQuery( '.ec_details_combo.ec_option3_<?php echo $this->product->product_id; ?> option:selected' ).attr( 'data-optionitem-price-override' ) );
		option3_price_multiplier = Number( jQuery( '.ec_details_combo.ec_option3_<?php echo $this->product->product_id; ?> option:selected' ).attr( 'data-optionitem-price-multiplier' ) );
	}
	// Option 4 Price Adjustment
	if( jQuery( '.ec_details_swatches > li.ec_option4_<?php echo $this->product->product_id; ?>.ec_selected' ).length ){
		option4_price_adj = jQuery( '.ec_details_swatches > li.ec_option4_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-price' );
		option4_price_add = jQuery( '.ec_details_swatches > li.ec_option4_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-price-onetime' );
		option4_price_override = Number( jQuery( '.ec_details_swatches > li.ec_option4_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-price-override' ) );
		option4_price_multiplier = Number( jQuery( '.ec_details_swatches > li.ec_option4_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-price-multiplier' ) );
	}else if( jQuery( '.ec_details_combo.ec_option4_<?php echo $this->product->product_id; ?>' ).length ){
		option4_price_adj = jQuery( '.ec_details_combo.ec_option4_<?php echo $this->product->product_id; ?> option:selected' ).attr( 'data-optionitem-price' );
		option4_price_add = jQuery( '.ec_details_combo.ec_option4_<?php echo $this->product->product_id; ?> option:selected' ).attr( 'data-optionitem-price-onetime' );
		option4_price_override = Number( jQuery( '.ec_details_combo.ec_option4_<?php echo $this->product->product_id; ?> option:selected' ).attr( 'data-optionitem-price-override' ) );
		option4_price_multiplier = Number( jQuery( '.ec_details_combo.ec_option4_<?php echo $this->product->product_id; ?> option:selected' ).attr( 'data-optionitem-price-multiplier' ) );
	}
	// Option 5 Price Adjustment
	if( jQuery( '.ec_details_swatches > li.ec_option5_<?php echo $this->product->product_id; ?>.ec_selected' ).length ){
		option5_price_adj = jQuery( '.ec_details_swatches > li.ec_option5_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-price' );
		option5_price_add = jQuery( '.ec_details_swatches > li.ec_option5_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-price-onetime' );
		option5_price_override = Number( jQuery( '.ec_details_swatches > li.ec_option5_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-price-override' ) );
		option5_price_multiplier = Number( jQuery( '.ec_details_swatches > li.ec_option5_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-price-multiplier' ) );
	}else if( jQuery( '.ec_details_combo.ec_option5_<?php echo $this->product->product_id; ?>' ).length ){
		option5_price_adj = jQuery( '.ec_details_combo.ec_option5_<?php echo $this->product->product_id; ?> option:selected' ).attr( 'data-optionitem-price' );
		option5_price_add = jQuery( '.ec_details_combo.ec_option5_<?php echo $this->product->product_id; ?> option:selected' ).attr( 'data-optionitem-price-onetime' );
		option5_price_override = Number( jQuery( '.ec_details_combo.ec_option5_<?php echo $this->product->product_id; ?> option:selected' ).attr( 'data-optionitem-price-override' ) );
		option5_price_multiplier = Number( jQuery( '.ec_details_combo.ec_option5_<?php echo $this->product->product_id; ?> option:selected' ).attr( 'data-optionitem-price-multiplier' ) );
	}
	var num_decimals = <?php echo $GLOBALS['currency']->get_decimal_length( ); ?>;
	var decimal_symbol = '<?php echo $GLOBALS['currency']->get_decimal_symbol( ); ?>';
	var grouping_symbol = '<?php echo str_replace( "'", "&apos;", $GLOBALS['currency']->get_grouping_symbol( ) ); ?>';
	var new_price = base_price + Number( option1_price_adj ) + Number( option2_price_adj ) + Number( option3_price_adj ) + Number( option4_price_adj ) + Number( option5_price_adj );
	var order_price = Number( option1_price_add ) + Number( option2_price_add ) + Number( option3_price_add ) + Number( option4_price_add ) + Number( option5_price_add );
	var override_price = -1;
	if( option1_price_override > -1 )
		override_price = option1_price_override;
	else if( option2_price_override > -1 )
		override_price = option2_price_override;
	else if( option3_price_override > -1 )
		override_price = option3_price_override;
	else if( option4_price_override > -1 )
		override_price = option4_price_override;
	else if( option5_price_override > -1 )
		override_price = option5_price_override; 
	if( override_price > -1 ){
		jQuery( document.getElementById( 'ec_final_price_<?php echo $this->product->product_id; ?>' ) ).html( ec_details_format_money( override_price ) );
		jQuery( '.ec_details_price.ec_details_single_price > .ec_product_sale_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( override_price ) ); } );
		jQuery( '.ec_details_price.ec_details_single_price > .ec_product_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( override_price ) ); } );
		<?php if( isset( $vat_row ) && $vat_row->vat_added ){?>jQuery( '.ec_details_price.ec_details_no_vat_price > .ec_product_sale_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( override_price ) ); } );
		jQuery( '.ec_details_price.ec_details_vat_price > .ec_product_sale_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( override_price * <?php echo $vat_rate_multiplier; ?> ) ); } );
	<?php }else{?>jQuery( '.ec_details_price.ec_details_no_vat_price > .ec_product_sale_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( override_price / <?php echo $vat_rate_multiplier; ?> ) ); } );
		jQuery( '.ec_details_price.ec_details_vat_price > .ec_product_sale_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( override_price ) ); } );
	<?php }?>}else{
		jQuery( document.getElementById( 'ec_final_price_<?php echo $this->product->product_id; ?>' ) ).html( ec_details_format_money( new_price ) );
		jQuery( '.ec_details_price.ec_details_single_price > .ec_product_sale_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( new_price ) ); } );
		jQuery( '.ec_details_price.ec_details_single_price > .ec_product_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( new_price ) ); } );
		<?php if( isset( $vat_row ) && $vat_row->vat_added ){?>jQuery( '.ec_details_price.ec_details_no_vat_price > .ec_product_sale_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( new_price ) ); } );
		jQuery( '.ec_details_price.ec_details_vat_price > .ec_product_sale_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( new_price * <?php echo $vat_rate_multiplier; ?> ) ); } );
	<?php }else{?>jQuery( '.ec_details_price.ec_details_no_vat_price > .ec_product_sale_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( new_price / <?php echo $vat_rate_multiplier; ?> ) ); } );
		jQuery( '.ec_details_price.ec_details_vat_price > .ec_product_sale_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( new_price ) ); } );
	<?php }?>}
	if( order_price != 0 ){
		jQuery( '.ec_details_added_price_<?php echo $this->product->product_id; ?>' ).show( );
		jQuery( document.getElementById( 'ec_added_price_<?php echo $this->product->product_id; ?>' ) ).html( ec_details_format_money( order_price ) );
	}else{
		jQuery( '.ec_details_added_price_<?php echo $this->product->product_id; ?>' ).hide( );
	}
}
function ec_details_advanced_adjust_price_<?php echo $this->product->product_id; ?>( ){	
	if( jQuery( document.getElementById( 'ec_base_price_<?php echo $this->product->product_id; ?>' ) ).length == 0 )
		return;
	var base_price = Number( jQuery( document.getElementById( 'ec_base_price_<?php echo $this->product->product_id; ?>' ) ).html( ) );
	// Get a quantity in case we need to use in calculating price
	var current_quantity = 1;
	if( jQuery( document.getElementById( 'ec_quantity_<?php echo $this->product->product_id; ?>' ) ).length > 0 ){
		current_quantity = jQuery( document.getElementById( 'ec_quantity_<?php echo $this->product->product_id; ?>' ) ).val( );
	}
	if( jQuery( '.ec_details_grid_row > input' ).length > 0 ){
		current_quantity = 0;
		jQuery( '.ec_details_grid_row > input' ).each( function( ){
			current_quantity = current_quantity + Number( jQuery( this ).val( ) );
		} );
	}
	for( var i=0; i<tier_quantities_<?php echo $this->product->product_id; ?>.length; i++ ){
		if( tier_quantities_<?php echo $this->product->product_id; ?>[i] <= current_quantity )
			base_price = Number( tier_prices_<?php echo $this->product->product_id; ?>[i] );
	}
	var override_price = -1;
	var price_multiplier = 1;
	// Checkbox Price Adjustments
	var checkbox_adj = 0;
	var checkbox_add = 0;
	// Combox Price Adjustments
	var combo_adj = 0;
	var combo_add = 0;
	// Date Price Adjustments
	var date_adj = 0;
	var date_add = 0;
	// File Price Adjustments
	var file_adj = 0;
	var file_add = 0;
	// Swatch Price Adjustments
	var swatch_adj = 0;
	var swatch_add = 0;
	// Grid Price Adjustments
	var grid_quantity = 0;
	var grid_adj = 0;
	var grid_add = 0;
	var grid_override = 0;
	// Radio Price Adjustments
	var radio_adj = 0;
	var radio_add = 0;
	// Text Price Adjustments
	var text_adj = 0;
	var text_add = 0;
	// Number Price Adjustments
	var number_adj = 0;
	var number_add = 0;
	// Textarea Price Adjustments
	var textarea_adj = 0;
	var textarea_add = 0;
	// Dimensions Price Adjustments
	var has_sq_footage = false;
	var sq_footage = 1;
	jQuery( '.ec_details_grid_row:visible > input' ).each( function( ){
		if( jQuery( this ).val( ) > 0 ){
			grid_quantity += Number( jQuery( this ).val( ) );
			if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price' ) != 0 ){
				grid_adj += ( ( Number( jQuery( this ).attr( 'data-optionitem-price' ) ) ) * ( Number( jQuery( this ).val( ) ) ) );
			}
			if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-onetime' ) != 0 ){
				grid_add += Number( jQuery( this ).attr( 'data-optionitem-price-onetime' ) );
			}
			if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-override' ) >= 0 ){
				grid_override += ( Number( jQuery( this ).attr( 'data-optionitem-price-override' ) ) - base_price ) * ( ( Number( jQuery( this ).val( ) ) ) );
			}
			if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-multiplier' ) > 0 ){
				price_multiplier = price_multiplier * Number( jQuery( this ).attr( 'data-optionitem-price-multiplier' ) );
			}
		}
	} );
	jQuery( '.ec_details_checkbox_row:visible > input:checked' ).each( function( ){
		if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price' ) != 0 ){
			if( grid_quantity )
				checkbox_adj += Number( jQuery( this ).attr( 'data-optionitem-price' ) ) * grid_quantity;
			else
				checkbox_adj += Number( jQuery( this ).attr( 'data-optionitem-price' ) );
		}
		if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-onetime' ) != 0 ){
			checkbox_add += Number( jQuery( this ).attr( 'data-optionitem-price-onetime' ) );
		}
		if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-override' ) >= 0 ){
			override_price = Number( jQuery( this ).attr( 'data-optionitem-price-override' ) );
		}
		if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-multiplier' ) > 0 ){
			price_multiplier = price_multiplier * Number( jQuery( this ).attr( 'data-optionitem-price-multiplier' ) );
		}
	} );
	jQuery( '.ec_details_option_row.ec_option_type_combo:visible > .ec_details_option_data > select option:selected' ).each( function( ){
		if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price' ) != 0 ){
			if( grid_quantity )
				combo_adj += Number( jQuery( this ).attr( 'data-optionitem-price' ) ) * grid_quantity;
			else
				combo_adj += Number( jQuery( this ).attr( 'data-optionitem-price' ) );
		}
		if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-onetime' ) != 0 ){
			combo_add += Number( jQuery( this ).attr( 'data-optionitem-price-onetime' ) );
		}
		if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-override' ) >= 0 ){
			override_price = Number( jQuery( this ).attr( 'data-optionitem-price-override' ) );
		}
		if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-multiplier' ) > 0 ){
			price_multiplier = price_multiplier * Number( jQuery( this ).attr( 'data-optionitem-price-multiplier' ) );
		}
	} );
	jQuery( '.ec_details_option_row.ec_option_type_date:visible > .ec_details_option_data > input' ).each( function( ){
		if( jQuery( this ).val( ) != "" ){
			if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price' ) != 0 ){
				if( grid_quantity )
					date_adj += Number( jQuery( this ).attr( 'data-optionitem-price' ) ) * grid_quantity;
				else
					date_adj += Number( jQuery( this ).attr( 'data-optionitem-price' ) );
			}
			if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-onetime' ) != 0 ){
				date_add += Number( jQuery( this ).attr( 'data-optionitem-price-onetime' ) );
			}
			if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-override' ) >= 0 ){
				override_price = Number( jQuery( this ).attr( 'data-optionitem-price-override' ) );
			}
			if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-multiplier' ) > 0 ){
				price_multiplier = price_multiplier * Number( jQuery( this ).attr( 'data-optionitem-price-multiplier' ) );
			}
		}
	} );
	jQuery( '.ec_details_option_row.ec_option_type_file:visible > .ec_details_option_data > input' ).each( function( ){
		if( jQuery( this ).val( ) != "" ){
			if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price' ) != 0 ){
				if( grid_quantity )
					file_adj += Number( jQuery( this ).attr( 'data-optionitem-price' ) ) * grid_quantity;
				else
					file_adj += Number( jQuery( this ).attr( 'data-optionitem-price' ) );
			}
			if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-onetime' ) != 0 ){
				file_add += Number( jQuery( this ).attr( 'data-optionitem-price-onetime' ) );
			}
			if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-override' ) >= 0 ){
				override_price = Number( jQuery( this ).attr( 'data-optionitem-price-override' ) );
			}
			if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-multiplier' ) > 0 ){
				price_multiplier = price_multiplier * Number( jQuery( this ).attr( 'data-optionitem-price-multiplier' ) );
			}
		}
	} );
	jQuery( '.ec_details_swatch.ec_advanced.ec_selected:visible' ).each( function( ){
		if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price' ) != 0 ){
			if( grid_quantity )
				swatch_adj += Number( jQuery( this ).attr( 'data-optionitem-price' ) ) * grid_quantity;
			else
				swatch_adj += Number( jQuery( this ).attr( 'data-optionitem-price' ) );
		}
		if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-onetime' ) != 0 ){
			swatch_add += Number( jQuery( this ).attr( 'data-optionitem-price-onetime' ) );
		}
		if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-override' ) >= 0 ){
			override_price = Number( jQuery( this ).attr( 'data-optionitem-price-override' ) );
		}
		if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-multiplier' ) > 0 ){
			price_multiplier = price_multiplier * Number( jQuery( this ).attr( 'data-optionitem-price-multiplier' ) );
		}
	} );
	jQuery( '.ec_details_radio_row:visible > input:checked' ).each( function( ){
		if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price' ) != 0 ){
			if( grid_quantity )
				radio_adj += Number( jQuery( this ).attr( 'data-optionitem-price' ) ) * grid_quantity;
			else
				radio_adj += Number( jQuery( this ).attr( 'data-optionitem-price' ) );
		}
		if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-onetime' ) != 0 ){
			radio_add += Number( jQuery( this ).attr( 'data-optionitem-price-onetime' ) );
		}
		if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-override' ) >= 0 ){
			override_price = Number( jQuery( this ).attr( 'data-optionitem-price-override' ) );
		}
		if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-multiplier' ) > 0 ){
			price_multiplier = price_multiplier * Number( jQuery( this ).attr( 'data-optionitem-price-multiplier' ) );
		}
	} );
	jQuery( '.ec_details_option_row.ec_option_type_text:visible > .ec_details_option_data > input' ).each( function( ){
		if( jQuery( this ).val( ) != "" ){
			if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price' ) != 0 ){
				if( grid_quantity )
					text_adj += Number( jQuery( this ).attr( 'data-optionitem-price' ) ) * grid_quantity;
				else
					text_adj += Number( jQuery( this ).attr( 'data-optionitem-price' ) );
			}
			if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-onetime' ) != 0 ){
				text_add += Number( jQuery( this ).attr( 'data-optionitem-price-onetime' ) );
			}
			if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-override' ) >= 0 ){
				override_price = Number( jQuery( this ).attr( 'data-optionitem-price-override' ) );
			}
			if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-multiplier' ) > 0 ){
				price_multiplier = price_multiplier * Number( jQuery( this ).attr( 'data-optionitem-price-multiplier' ) );
			}
			if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-per-character' ) > 0 ){
				var num_characters = Number(  jQuery( this ).val( ).replace( / /g, '' ).length );
				var price_per_char = Number( jQuery( this ).attr( 'data-optionitem-price-per-character' ) );
				text_adj = text_adj + ( num_characters * price_per_char );
			}
		}
	} );
	jQuery( '.ec_details_option_row.ec_option_type_number:visible > .ec_details_option_data > input' ).each( function( ){
		if( jQuery( this ).val( ) != "" && jQuery( this ).val( ) != "0" ){
			if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price' ) != 0 ){
				if( grid_quantity > 0 )
					number_adj += Number( jQuery( this ).val( ) ) * Number( jQuery( this ).attr( 'data-optionitem-price' ) ) * grid_quantity;
				else
					number_adj += Number( jQuery( this ).val( ) ) * Number( jQuery( this ).attr( 'data-optionitem-price' ) );
			}
			if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-onetime' ) != 0 ){
				number_add += Number( jQuery( this ).attr( 'data-optionitem-price-onetime' ) );
			}
			if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-override' ) >= 0 ){
				override_price = Number( jQuery( this ).attr( 'data-optionitem-price-override' ) );
			}
			if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-multiplier' ) > 0 ){
				price_multiplier = price_multiplier * Number( jQuery( this ).val( ) ) * Number( jQuery( this ).attr( 'data-optionitem-price-multiplier' ) );
			}
		}
	} );
	jQuery( '.ec_details_option_row.ec_option_type_textarea:visible > .ec_details_option_data > textarea' ).each( function( ){
		if( jQuery( this ).val( ) != "" ){
			if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price' ) != 0 ){
				textarea_adj += Number( jQuery( this ).attr( 'data-optionitem-price' ) );
			}
			if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-onetime' ) != 0 ){
				textarea_add += Number( jQuery( this ).attr( 'data-optionitem-price-onetime' ) );
			}
			if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-override' ) >= 0 ){
				override_price = Number( jQuery( this ).attr( 'data-optionitem-price-override' ) );
			}
			if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-multiplier' ) > 0 ){
				price_multiplier = price_multiplier * Number( jQuery( this ).attr( 'data-optionitem-price-multiplier' ) );
			}
			if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-optionitem-price-per-character' ) > 0 ){
				var num_characters = Number(  jQuery( this ).val( ).replace( / /g, '' ).length );
				var price_per_char = Number( jQuery( this ).attr( 'data-optionitem-price-per-character' ) );
				textarea_adj = textarea_adj + ( num_characters * price_per_char );
			}
		}
	} );
	jQuery( '.ec_dimensions_width' ).each( function( ){
			if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' ){
			has_sq_footage = true;
			var option_id = jQuery( this ).attr( 'data-option-id' );
			var product_id = '<?php echo $this->product->product_id; ?>';
			var width = jQuery( document.getElementById( 'ec_option_adv_' + option_id + '_' + product_id + '_width' ) ).val( );
			var sub_width = 0;
			if( jQuery( document.getElementById( 'ec_option_adv_' + option_id + '_' + product_id + '_sub_width' ) ).length )
				var sub_width = jQuery( document.getElementById( 'ec_option_adv_' + option_id + '_' + product_id + '_sub_width' ) ).val( );
			var height = jQuery( document.getElementById( 'ec_option_adv_' + option_id + '_' + product_id + '_height' ) ).val( );
			var sub_height = 0;
			if( jQuery( document.getElementById( 'ec_option_adv_' + option_id + '_' + product_id + '_sub_height' ) ).length )
				var sub_height = jQuery( document.getElementById( 'ec_option_adv_' + option_id + '_' + product_id + '_sub_height' ) ).val( );
			if( width != "" && height != "" )
				sq_footage = ec_details_get_sq_footage_<?php echo $this->product->product_id; ?>( width, sub_width, height, sub_height ) * Number( jQuery( document.getElementById( 'ec_quantity_<?php echo $this->product->product_id; ?>' ) ).val( ) );	
		}
	} );
	if( grid_quantity > 0 )
		base_price = base_price * grid_quantity;
	var new_price = ( base_price + Number( checkbox_adj ) + Number( combo_adj ) + Number( date_adj ) + Number( file_adj ) + Number( swatch_adj ) + Number( grid_adj ) + Number( grid_override ) + Number( radio_adj ) + Number( text_adj ) + Number( number_adj ) + Number( textarea_adj ) );
	var new_price_sqft = new_price * sq_footage;
	var override_price_final = override_price + Number( checkbox_adj ) + Number( combo_adj ) + Number( date_adj ) + Number( file_adj ) + Number( swatch_adj ) + Number( grid_adj ) + Number( grid_override ) + Number( radio_adj ) + Number( text_adj ) + Number( number_adj ) + Number( textarea_adj );
	var override_price_sqft = override_price_final * sq_footage;
	var order_price = Number( checkbox_add ) + Number( combo_add ) + Number( date_add ) + Number( file_add ) + Number( swatch_add ) + Number( grid_add ) + Number( radio_add ) + Number( text_add ) + Number( number_add ) + Number( textarea_add );
	if( override_price > -1 ){
		jQuery( document.getElementById( 'ec_final_price_<?php echo $this->product->product_id; ?>' ) ).html( ec_details_format_money( override_price_sqft ) );
		jQuery( '.ec_details_price.ec_details_single_price > .ec_product_sale_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( override_price_final ) ); } );
		jQuery( '.ec_details_price.ec_details_single_price > .ec_product_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( override_price_final ) ); } );
		<?php if( isset( $vat_row ) && $vat_row->vat_added ){?>jQuery( '.ec_details_price.ec_details_no_vat_price > .ec_product_sale_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( override_price_final ) ); } );
		jQuery( '.ec_details_price.ec_details_vat_price > .ec_product_sale_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( override_price_final * <?php echo $vat_rate_multiplier; ?> ) ); } );
		jQuery( '.ec_details_price.ec_details_no_vat_price > .ec_product_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( override_price_final ) ); } );
		jQuery( '.ec_details_price.ec_details_vat_price > .ec_product_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( override_price_final * <?php echo $vat_rate_multiplier; ?> ) ); } );
	<?php }else{ ?>jQuery( '.ec_details_price.ec_details_no_vat_price > .ec_product_sale_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( override_price_final / <?php echo $vat_rate_multiplier; ?> ) ); } );
		jQuery( '.ec_details_price.ec_details_vat_price > .ec_product_sale_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( override_price_final ) ); } );
		jQuery( '.ec_details_price.ec_details_no_vat_price > .ec_product_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( override_price_final / <?php echo $vat_rate_multiplier; ?> ) ); } );
		jQuery( '.ec_details_price.ec_details_vat_price > .ec_product_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( override_price_final ) ); } );
	<?php }?>}else{
		jQuery( document.getElementById( 'ec_final_price_<?php echo $this->product->product_id; ?>' ) ).html( ec_details_format_money( new_price_sqft ) );
		jQuery( '.ec_details_price.ec_details_single_price > .ec_product_sale_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( new_price ) ); } );
		jQuery( '.ec_details_price.ec_details_single_price > .ec_product_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( new_price ) ); } );
		<?php if( isset( $vat_row ) && $vat_row->vat_added ){?>jQuery( '.ec_details_price.ec_details_no_vat_price > .ec_product_sale_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( new_price ) ); } );
		jQuery( '.ec_details_price.ec_details_vat_price > .ec_product_sale_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( new_price * <?php echo $vat_rate_multiplier; ?> ) ); } );
		jQuery( '.ec_details_price.ec_details_no_vat_price > .ec_product_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( new_price ) ); } );
		jQuery( '.ec_details_price.ec_details_vat_price > .ec_product_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( new_price * <?php echo $vat_rate_multiplier; ?> ) ); } );
	<?php }else{ ?>jQuery( '.ec_details_price.ec_details_no_vat_price > .ec_product_sale_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( new_price / <?php echo $vat_rate_multiplier; ?> ) ); } );
		jQuery( '.ec_details_price.ec_details_vat_price > .ec_product_sale_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( new_price ) ); } );
		jQuery( '.ec_details_price.ec_details_no_vat_price > .ec_product_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( new_price / <?php echo $vat_rate_multiplier; ?> ) ); } );
		jQuery( '.ec_details_price.ec_details_vat_price > .ec_product_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( new_price ) ); } );
	<?php }?>}
	if( price_multiplier != 1 && override_price > -1 ){
		jQuery( document.getElementById( 'ec_final_price_<?php echo $this->product->product_id; ?>' ) ).html( ec_details_format_money( override_price_sqft * Number( price_multiplier ) ) );
		jQuery( '.ec_details_price.ec_details_single_price > .ec_product_sale_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( override_price_final * Number( price_multiplier ) ) ); } );
		jQuery( '.ec_details_price.ec_details_single_price > .ec_product_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( override_price_final * Number( price_multiplier ) ) ); } );
		<?php if( isset( $vat_row ) && $vat_row->vat_added ){?>jQuery( '.ec_details_price.ec_details_no_vat_price > .ec_product_sale_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( override_price_final * Number( price_multiplier ) ) ); } );
		jQuery( '.ec_details_price.ec_details_vat_price > .ec_product_sale_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( override_price_final * Number( price_multiplier ) * <?php echo $vat_rate_multiplier; ?> ) ); } );
		jQuery( '.ec_details_price.ec_details_no_vat_price > .ec_product_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( override_price_final * Number( price_multiplier ) ) ); } );
		jQuery( '.ec_details_price.ec_details_vat_price > .ec_product_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( override_price_final * Number( price_multiplier ) * <?php echo $vat_rate_multiplier; ?> ) ); } );
	<?php }else{ ?>jQuery( '.ec_details_price.ec_details_no_vat_price > .ec_product_sale_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( override_price_final * Number( price_multiplier ) / <?php echo $vat_rate_multiplier; ?> ) ); } );
		jQuery( '.ec_details_price.ec_details_vat_price > .ec_product_sale_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( override_price_final * Number( price_multiplier ) ) ); } );
		jQuery( '.ec_details_price.ec_details_no_vat_price > .ec_product_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( override_price_final * Number( price_multiplier ) / <?php echo $vat_rate_multiplier; ?> ) ); } );
		jQuery( '.ec_details_price.ec_details_vat_price > .ec_product_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( override_price_final * Number( price_multiplier ) ) ); } );
	<?php }?>}else if( price_multiplier != 1 ){
		jQuery( document.getElementById( 'ec_final_price_<?php echo $this->product->product_id; ?>' ) ).html( ec_details_format_money( new_price * Number( price_multiplier ) ) );
		jQuery( '.ec_details_price.ec_details_single_price > .ec_product_sale_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( new_price * Number( price_multiplier ) ) ); } );
		jQuery( '.ec_details_price.ec_details_single_price > .ec_product_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( new_price * Number( price_multiplier ) ) ); } );
		<?php if( isset( $vat_row ) && $vat_row->vat_added ){?>jQuery( '.ec_details_price.ec_details_no_vat_price > .ec_product_sale_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( new_price * Number( price_multiplier ) ) ); } );
		jQuery( '.ec_details_price.ec_details_vat_price > .ec_product_sale_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( new_price * Number( price_multiplier ) * <?php echo $vat_rate_multiplier; ?> ) ); } );
		jQuery( '.ec_details_price.ec_details_no_vat_price > .ec_product_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( new_price * Number( price_multiplier ) ) ); } );
		jQuery( '.ec_details_price.ec_details_vat_price > .ec_product_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( new_price * Number( price_multiplier ) * <?php echo $vat_rate_multiplier; ?> ) ); } );
	<?php }else{ ?>jQuery( '.ec_details_price.ec_details_no_vat_price > .ec_product_sale_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( new_price * Number( price_multiplier ) / <?php echo $vat_rate_multiplier; ?> ) ); } );
		jQuery( '.ec_details_price.ec_details_vat_price > .ec_product_sale_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( new_price * Number( price_multiplier ) ) ); } );
		jQuery( '.ec_details_price.ec_details_no_vat_price > .ec_product_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( new_price * Number( price_multiplier ) / <?php echo $vat_rate_multiplier; ?> ) ); } );
		jQuery( '.ec_details_price.ec_details_vat_price > .ec_product_price_<?php echo $this->product->product_id; ?>' ).each( function( ){ jQuery( this ).html( ec_details_format_money( new_price * Number( price_multiplier ) ) ); } );
	<?php }?>}
	if( order_price != 0 ){
		jQuery( '.ec_details_added_price_<?php echo $this->product->product_id; ?>' ).show( );
		jQuery( document.getElementById( 'ec_added_price_<?php echo $this->product->product_id; ?>' ) ).html( ec_details_format_money( order_price ) );
	}else{
		jQuery( '.ec_details_added_price_<?php echo $this->product->product_id; ?>' ).hide( );
	}
}
function ec_details_advanced_conditional_logic_<?php echo $this->product->product_id; ?>( ){
	for( var i=0; i<ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>.length; i++ ){
		if( ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].rules.length > 0 ){
			if( ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].show_field ){
				jQuery( '.ec_details_option_row[data-option-id="' + ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].id + '"]' ).hide( );
			}else{
				jQuery( '.ec_details_option_row[data-option-id="' + ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].id + '"]' ).show( );
			}
			if( ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].and_rules == 'AND' ){
				var rules_match = true;
				for( var j=0; j<ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].rules.length; j++ ){
					jQuery( '.ec_details_option_row' ).each( function( ){
						if( jQuery( this ).attr( 'data-option-id' ) == ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].rules[j].option_id ){
							var val = 0;
							var selected_val = ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].rules[j].optionitem_id;
							if( jQuery( this ).hasClass( 'ec_option_type_checkbox' ) ){
								jQuery( this ).find( 'input' ).each( function( ){ 
									if( jQuery( this ).attr( 'name' ) == 'ec_option_adv_' + ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].rules[j].option_id + '_' + ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].rules[j].optionitem_id ){
										if( jQuery( this ).is( ':checked' ) ){
											val = ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].rules[j].optionitem_id;
										}
									}
								} );
								
							}else if( jQuery( this ).hasClass( 'ec_option_type_radio' ) ){
								jQuery( this ).find( 'input' ).each( function( ){ 
									if( jQuery( this ).val( ) == ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].rules[j].optionitem_id ){
										if( jQuery( this ).is( ':checked' ) ){
											val = ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].rules[j].optionitem_id;
										}
									}
								} );
								
							}else if( jQuery( this ).hasClass( 'ec_option_type_swatch' ) ){
								jQuery( this ).find( '.ec_details_swatch.ec_selected' ).each( function( ){
									if( jQuery( this ).attr( 'data-optionitem-id' ) == ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].rules[j].optionitem_id ){
										val = ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].rules[j].optionitem_id;
									}
								} );
								
							}else if( jQuery( this ).hasClass( 'ec_option_type_combo' ) ){
								var val = jQuery( this ).find( 'select' ).val( );
								
							}else if( jQuery( this ).hasClass( 'ec_option_type_textarea' ) ){
								var val = jQuery( this ).find( 'textarea' ).val( );
								selected_val = ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].rules[j].optionitem_value;
								
							}else{
								var val = jQuery( this ).find( 'input' ).val( );
								selected_val = ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].rules[j].optionitem_value;
							}
							if( ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].rules[j].operator == '=' ){
								if( val != selected_val ){
									rules_match = false;
								}
							}else if( ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].rules[j].operator == '!=' ){
								if( val == selected_val ){
									rules_match = false;
								}
							}
						}
					} );
				}
			}else{
				var rules_match = false;
				for( var j=0; j<ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].rules.length; j++ ){
					jQuery( '.ec_details_option_row' ).each( function( ){
						if( jQuery( this ).attr( 'data-option-id' ) == ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].rules[j].option_id ){
							var val = 0;
							var selected_val = ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].rules[j].optionitem_id;
							if( jQuery( this ).hasClass( 'ec_option_type_checkbox' ) ){
								jQuery( this ).find( 'input' ).each( function( ){ 
									if( jQuery( this ).attr( 'name' ) == 'ec_option_adv_' + ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].rules[j].option_id + '_' + ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].rules[j].optionitem_id ){
										if( jQuery( this ).is( ':checked' ) ){
											val = ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].rules[j].optionitem_id;
										}
									}
								} );
								
							}else if( jQuery( this ).hasClass( 'ec_option_type_radio' ) ){
								jQuery( this ).find( 'input' ).each( function( ){ 
									if( jQuery( this ).val( ) == ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].rules[j].optionitem_id ){
										if( jQuery( this ).is( ':checked' ) ){
											val = ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].rules[j].optionitem_id;
										}
									}
								} );
								
							}else if( jQuery( this ).hasClass( 'ec_option_type_swatch' ) ){
								jQuery( this ).find( '.ec_details_swatch.ec_selected' ).each( function( ){
									if( jQuery( this ).attr( 'data-optionitem-id' ) == ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].rules[j].optionitem_id ){
										val = ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].rules[j].optionitem_id;
									}
								} );
								
							}else if( jQuery( this ).hasClass( 'ec_option_type_combo' ) ){
								var val = jQuery( this ).find( 'select' ).val( );
								
							}else if( jQuery( this ).hasClass( 'ec_option_type_textarea' ) ){
								var val = jQuery( this ).find( 'textarea' ).val( );
								selected_val = ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].rules[j].optionitem_value;
								
							}else{
								var val = jQuery( this ).find( 'input' ).val( );
								selected_val = ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].rules[j].optionitem_value;
							}
							if( ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].rules[j].operator == '=' ){
								if( val == selected_val ){
									rules_match = true;
								}
							}else if( ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].rules[j].operator == '!=' ){
								if( val != selected_val ){
									rules_match = true;
								}
							}
						}
					} );
				}
			}
			if( rules_match ){
				if( ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].show_field ){
					jQuery( '.ec_details_option_row[data-option-id="' + ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].id + '"]' ).show( );
				}else{
					jQuery( '.ec_details_option_row[data-option-id="' + ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].id + '"]' ).hide( );
					if( jQuery( '.ec_details_option_row[data-option-id="' + ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].id + '"]' ).hasClass( 'ec_option_type_checkbox' ) ){
						jQuery( '.ec_details_option_row[data-option-id="' + ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].id + '"]' ).find( 'input' ).each( function( ){ jQuery( this ).attr( 'checked', false ); } );
					}else if( jQuery( '.ec_details_option_row[data-option-id="' + ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].id + '"]' ).hasClass( 'ec_option_type_radio' ) ){
						jQuery( '.ec_details_option_row[data-option-id="' + ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].id + '"]' ).find( 'input' ).each( function( ){ jQuery( this ).attr( 'checked', false ); } );
					}else if( jQuery( '.ec_details_option_row[data-option-id="' + ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].id + '"]' ).hasClass( 'ec_option_type_swatch' ) ){
						jQuery( '.ec_details_option_row[data-option-id="' + ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].id + '"]' ).find( '.ec_details_swatch' ).each( function( ){ jQuery( this ).removeClass( 'ec_selected' ); } );
						jQuery( document.getElementById( 'ec_option_adv_' + ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].id + '_<?php echo $this->product->product_id; ?>' ) ).val( '0' );
					}else{
						jQuery( '.ec_details_option_row[data-option-id="' + ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].id + '"]' ).find( 'input' ).each( function( ){ jQuery( this ).val( '' ); } );
						jQuery( '.ec_details_option_row[data-option-id="' + ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].id + '"]' ).find( 'textarea' ).each( function( ){ jQuery( this ).val( '' ); } );
						jQuery( '.ec_details_option_row[data-option-id="' + ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].id + '"]' ).find( 'select' ).each( function( ){ jQuery( this ).val( 0 ); } );
					}
				}
			}else{
				if( ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].show_field ){
					if( jQuery( '.ec_details_option_row[data-option-id="' + ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].id + '"]' ).hasClass( 'ec_option_type_checkbox' ) ){
						jQuery( '.ec_details_option_row[data-option-id="' + ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].id + '"]' ).find( 'input' ).each( function( ){ jQuery( this ).attr( 'checked', false ); } );
					}else if( jQuery( '.ec_details_option_row[data-option-id="' + ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].id + '"]' ).hasClass( 'ec_option_type_radio' ) ){
						jQuery( '.ec_details_option_row[data-option-id="' + ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].id + '"]' ).find( 'input' ).each( function( ){ jQuery( this ).attr( 'checked', false ); } );
					}else if( jQuery( '.ec_details_option_row[data-option-id="' + ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].id + '"]' ).hasClass( 'ec_option_type_swatch' ) ){
						jQuery( '.ec_details_option_row[data-option-id="' + ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].id + '"]' ).find( '.ec_details_swatch' ).each( function( ){ jQuery( this ).removeClass( 'ec_selected' ); } );
						jQuery( document.getElementById( 'ec_option_adv_' + ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].id + '_<?php echo $this->product->product_id; ?>' ) ).val( '0' );
					}else{
						jQuery( '.ec_details_option_row[data-option-id="' + ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].id + '"]' ).find( 'input' ).each( function( ){ jQuery( this ).val( '' ); } );
						jQuery( '.ec_details_option_row[data-option-id="' + ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].id + '"]' ).find( 'textarea' ).each( function( ){ jQuery( this ).val( '' ); } );
						jQuery( '.ec_details_option_row[data-option-id="' + ec_advanced_logic_rules_<?php echo $this->product->product_id; ?>[i].id + '"]' ).find( 'select' ).each( function( ){ jQuery( this ).val( 0 ); } );
					}
				}
			}
		}
	}
}
ec_details_advanced_conditional_logic_<?php echo $this->product->product_id; ?>( );
function ec_details_get_sq_footage_<?php echo $this->product->product_id; ?>( width, sub_width, height, sub_height ){
	var sub_width_decimal = ec_details_get_sub_dimension_decimal_<?php echo $this->product->product_id; ?>( sub_width );
	width = Number( Number( width ) + sub_width_decimal ) / <?php if( !get_option( 'ec_option_enable_metric_unit_display' ) ){ echo "12"; }else{ echo "1000"; } ?>;
	var sub_height_decimal = ec_details_get_sub_dimension_decimal_<?php echo $this->product->product_id; ?>( sub_height );
	height = Number( Number( height ) + sub_height_decimal ) / <?php if( !get_option( 'ec_option_enable_metric_unit_display' ) ){ echo "12"; }else{ echo "1000"; } ?>;
	return width*height;
}
function ec_details_get_sub_dimension_decimal_<?php echo $this->product->product_id; ?>( sub_dimension ){
	if( sub_dimension == "0" ){
		return 0;
	}else if( sub_dimension == "1/16" ){
		return .0625;
	}else if( sub_dimension == "1/8" ){
		return .1250;
	}else if( sub_dimension == "3/16" ){
		return .1875;
	}else if( sub_dimension == "1/4" ){
		return .2500;
	}else if( sub_dimension == "5/16" ){
		return .3125;
	}else if( sub_dimension == "3/8" ){
		return .3750;
	}else if( sub_dimension == "7/16" ){
		return .4375;
	}else if( sub_dimension == "1/2" ){
		return .5000;
	}else if( sub_dimension == "9/16" ){
		return .5625;
	}else if( sub_dimension == "5/8" ){
		return .6250;
	}else if( sub_dimension == "11/16" ){
		return .6875;
	}else if( sub_dimension == "3/4" ){
		return .7500;
	}else if( sub_dimension == "13/16" ){
		return .8125;
	}else if( sub_dimension == "7/8" ){
		return .8750;
	}else if( sub_dimension == "15/16" ){
		return .9375;
	}else{
		return 0;
	}
}
function ec_details_format_money( price, num_decimals, grouping_symbol, decimal_symbol ){
	var currency_symbol = '<?php echo $GLOBALS['currency']->get_symbol( ); ?>';
	var num_decimals = <?php echo $GLOBALS['currency']->get_decimal_length( ); ?>;
	var decimal_symbol = '<?php echo $GLOBALS['currency']->get_decimal_symbol( ); ?>';
	var grouping_symbol = '<?php echo str_replace( "'", "&apos;", $GLOBALS['currency']->get_grouping_symbol( ) ); ?>';
	var conversion_rate = '<?php echo $GLOBALS['currency']->get_conversion_rate( ); ?>';
	var symbol_location = <?php echo $GLOBALS['currency']->get_symbol_location( ); ?>;
	var currency_code = '<?php echo $GLOBALS['currency']->get_currency_code( ); ?>';
	var show_currency_code = '<?php echo $GLOBALS['currency']->get_show_currency_code( ); ?>';
	price = ec_pricing_round( price * Number( conversion_rate ), num_decimals );
	var n = price,
        num_decimals = isNaN(num_decimals = Math.abs(num_decimals)) ? 2 : num_decimals,
        decimal_symbol = decimal_symbol == undefined ? "." : decimal_symbol,
        grouping_symbol = grouping_symbol == undefined ? "," : grouping_symbol,
        i = parseInt(n = price.toFixed( num_decimals ) ) + "",
        j = (j = i.length) > 3 ? j % 3 : 0;
    var formatted = (j ? i.substr(0, j) + grouping_symbol : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + grouping_symbol) + (num_decimals ? decimal_symbol + Math.abs(n - i).toFixed(num_decimals).slice(2) : "");
	if( symbol_location ){
		formatted = currency_symbol + formatted;
	}else{
		formatted = formatted + currency_symbol;
	}
	if( show_currency_code == '1' ){
		formatted = currency_code + ' ' +  formatted;
	}
	return formatted;
};
function ec_pricing_round(number, places) {
    var multiplier = Math.pow(10, places+2); // get two extra digits
    var fixed = Math.floor(number*multiplier); // convert to integer
    fixed += 50; // round down on anything less than x.xxx50
    fixed = Math.floor(fixed/100); // chop off last 2 digits
    return fixed/Math.pow(10, places);
}
function ec_details_submit_inquiry_<?php echo $this->product->product_id; ?>( ){
	var errors = 0;
	if( document.getElementById( 'ec_inquiry_name_<?php echo $this->product->product_id; ?>' ) ){
		if( jQuery( document.getElementById( 'ec_inquiry_name_<?php echo $this->product->product_id; ?>' ) ).val( ) == "" || !ec_validate_email( jQuery( document.getElementById( 'ec_inquiry_email_<?php echo $this->product->product_id; ?>' ) ).val( ) ) || jQuery( document.getElementById( 'ec_inquiry_message_<?php echo $this->product->product_id; ?>' ) ).val( ) == "" ){
			jQuery( document.getElementById( 'ec_details_inquiry_error_<?php echo $this->product->product_id; ?>' ) ).show( );
			errors++;
		}else{
			jQuery( document.getElementById( 'ec_details_inquiry_error_<?php echo $this->product->product_id; ?>' ) ).hide( );
		}
	}
	if( jQuery( document.getElementById( 'ec_grecaptcha_response_inquiry' ) ).length ){
		var recaptcha_response = jQuery( document.getElementById( 'ec_grecaptcha_response_inquiry' ) ).val( );
		if( !recaptcha_response.length ){
			jQuery( '#ec_product_details_inquiry_recaptcha > div' ).css( 'border', '1px solid red' );
			errors++;
		}else{
			jQuery( '#ec_product_details_inquiry_recaptcha > div' ).css( 'border', 'none' );
		}
	}
	if( !ec_details_add_to_cart_<?php echo $this->product->product_id; ?>( ) ){
		errors++;
	}
	if( errors > 0 )
		return false;
	else{
		jQuery( document.getElementById( 'ec_inquiry_loader_<?php echo $this->product->product_id; ?>' ) ).show( );
		jQuery( document.getElementById( 'ec_inquiry_loader_bg_<?php echo $this->product->product_id; ?>' ) ).show( );
		return true;
	}
}
function ec_details_add_to_cart_<?php echo $this->product->product_id; ?>( ){
	var errors = 0; 
	// Basic Option 1 Error Check
	if( jQuery( '.ec_details_swatch.ec_option1_<?php echo $this->product->product_id; ?>' ).length && !jQuery( '.ec_option1_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-id' ) ){ 
		jQuery( '.ec_option1_<?php echo $this->product->product_id; ?>.ec_details_option_row_error' ).show( ); 
		errors++; 
	}else if( jQuery( '.ec_details_combo.ec_option1_<?php echo $this->product->product_id; ?>' ).length && ( jQuery( '.ec_details_combo.ec_option1.ec_option1_<?php echo $this->product->product_id; ?>' ).val( ) == null || jQuery( '.ec_details_combo.ec_option1.ec_option1_<?php echo $this->product->product_id; ?>' ).val( ) == '0' ) ){ 
		jQuery( '.ec_option1_<?php echo $this->product->product_id; ?>.ec_details_option_row_error' ).show( ); 
		errors++; 
	}else{ 
		jQuery( '.ec_option1_<?php echo $this->product->product_id; ?>.ec_details_option_row_error' ).hide( );
	}
	// Basic Option 2 Error Check
	if( jQuery( '.ec_details_swatch.ec_option2_<?php echo $this->product->product_id; ?>' ).length && !jQuery( '.ec_option2_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-id' ) ){ 
		jQuery( '.ec_option2_<?php echo $this->product->product_id; ?>.ec_details_option_row_error' ).show( ); 
		errors++; 
	}else if( jQuery( '.ec_details_combo.ec_option2_<?php echo $this->product->product_id; ?>' ).length && ( jQuery( '.ec_details_combo.ec_option2.ec_option2_<?php echo $this->product->product_id; ?>' ).val( ) == null || jQuery( '.ec_details_combo.ec_option2.ec_option2_<?php echo $this->product->product_id; ?>' ).val( ) == '0' ) ){ 
		jQuery( '.ec_option2_<?php echo $this->product->product_id; ?>.ec_details_option_row_error' ).show( ); 
		errors++; 
	}else{ 
		jQuery( '.ec_option2_<?php echo $this->product->product_id; ?>.ec_details_option_row_error' ).hide( ); 
	}
	// Basic Option 3 Error Check
	if( jQuery( '.ec_details_swatch.ec_option3_<?php echo $this->product->product_id; ?>' ).length && !jQuery( '.ec_option3_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-id' ) ){ 
		jQuery( '.ec_option3_<?php echo $this->product->product_id; ?>.ec_details_option_row_error' ).show( );
		errors++; 
	}else if( jQuery( '.ec_details_combo.ec_option3_<?php echo $this->product->product_id; ?>' ).length && ( jQuery( '.ec_details_combo.ec_option3.ec_option3_<?php echo $this->product->product_id; ?>' ).val( ) == null || jQuery( '.ec_details_combo.ec_option3.ec_option3_<?php echo $this->product->product_id; ?>' ).val( ) == '0' ) ){ 
		jQuery( '.ec_option3_<?php echo $this->product->product_id; ?>.ec_details_option_row_error' ).show( ); 
		errors++; 
	}else{ 
		jQuery( '.ec_option3_<?php echo $this->product->product_id; ?>.ec_details_option_row_error' ).hide( ); 
	}
	// Basic Option 4 Error Check
	if( jQuery( '.ec_details_swatch.ec_option4_<?php echo $this->product->product_id; ?>' ).length && !jQuery( '.ec_option4_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-id' ) ){ 
		jQuery( '.ec_option4_<?php echo $this->product->product_id; ?>.ec_details_option_row_error' ).show( ); 
		errors++; 
	}else if( jQuery( '.ec_details_combo.ec_option4_<?php echo $this->product->product_id; ?>' ).length && ( jQuery( '.ec_details_combo.ec_option4.ec_option4_<?php echo $this->product->product_id; ?>' ).val( ) == null || jQuery( '.ec_details_combo.ec_option4.ec_option4_<?php echo $this->product->product_id; ?>' ).val( ) == '0' ) ){ 
		jQuery( '.ec_option4_<?php echo $this->product->product_id; ?>.ec_details_option_row_error' ).show( ); 
		errors++; 
	}else{ 
		jQuery( '.ec_option4_<?php echo $this->product->product_id; ?>.ec_details_option_row_error' ).hide( ); 
	}
	// Basic Option 5 Error Check
	if( jQuery( '.ec_details_swatch.ec_option5_<?php echo $this->product->product_id; ?>' ).length && !jQuery( '.ec_option5_<?php echo $this->product->product_id; ?>.ec_selected' ).attr( 'data-optionitem-id' ) ){ 
		jQuery( '.ec_option5_<?php echo $this->product->product_id; ?>.ec_details_option_row_error' ).show( ); 
		errors++; 
	}else if( jQuery( '.ec_details_combo.ec_option5_<?php echo $this->product->product_id; ?>' ).length && ( jQuery( '.ec_details_combo.ec_option5.ec_option5_<?php echo $this->product->product_id; ?>' ).val( ) == null || jQuery( '.ec_details_combo.ec_option5.ec_option5_<?php echo $this->product->product_id; ?>' ).val( ) == '0' ) ){ 
		jQuery( '.ec_option5_<?php echo $this->product->product_id; ?>.ec_details_option_row_error' ).show( ); 
		errors++; 
	}else{ 
		jQuery( '.ec_option5_<?php echo $this->product->product_id; ?>.ec_details_option_row_error' ).hide( ); 
	}
	// --------Advanced Option Checks---------- //
	// Select Box Check
	var advanced_select_rows = jQuery( '.ec_details_option_row.ec_option_type_combo:visible' );
	advanced_select_rows.each( function( ){
		if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-option-required' ) == '1' ){ // Option is Required
			if( jQuery( document.getElementById( 'ec_option_adv_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).val( ) == '0' ){
				jQuery( document.getElementById( 'ec_details_option_row_error_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).show( );
				errors++;
			}else{
				jQuery( document.getElementById( 'ec_details_option_row_error_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).hide( );
			}
		}
	} );
	// Check Box Check
	var advanced_checkbox_rows = jQuery( '.ec_details_option_row.ec_option_type_checkbox:visible' );
	advanced_checkbox_rows.each( function( ){
		if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-option-required' ) == '1' ){ // Option is Required
			var selected_checkboxes = jQuery( "input.ec_option_adv_" + jQuery( this ).attr( 'data-option-id' ) + ":checked" );
			if( selected_checkboxes.length ){
				jQuery( document.getElementById( 'ec_details_option_row_error_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).hide( );
			}else{
				jQuery( document.getElementById( 'ec_details_option_row_error_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).show( );
				errors++;
			}
		}
	} );
	// Date Box Check
	var advanced_date_rows = jQuery( '.ec_details_option_row.ec_option_type_date:visible' );
	advanced_date_rows.each( function( ){
		if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-option-required' ) == '1' ){ // Option is Required
			if( jQuery( document.getElementById( 'ec_option_adv_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).val( ) == "" ){
				jQuery( document.getElementById( 'ec_details_option_row_error_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).show( );
				errors++;
			}else{
				jQuery( document.getElementById( 'ec_details_option_row_error_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).hide( );
			}
		}
	} );
	// File Upload Check
	var advanced_file_rows = jQuery( '.ec_details_option_row.ec_option_type_file:visible' );
	advanced_file_rows.each( function( ){
		if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-option-required' ) == '1' ){ // Option is Required	
			if( jQuery( document.getElementById( 'ec_option_adv_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).val( ) ){
				jQuery( document.getElementById( 'ec_details_option_row_error_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).hide( );
			}else{
				jQuery( document.getElementById( 'ec_details_option_row_error_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).show( );
				errors++;
			}
		}
	} );
	// Swatch Check
	var advanced_swatch_rows = jQuery( '.ec_details_option_row.ec_option_type_swatch:visible' );
	advanced_swatch_rows.each( function( ){
		if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-option-required' ) == '1' ){ // Option is Required
			var advanced_selected_swatches = jQuery( ".ec_details_swatch.ec_option_adv_" + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' + ".ec_selected" );
			if( advanced_selected_swatches.length ){
				jQuery( document.getElementById( 'ec_details_option_row_error_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).hide( );
			}else{
				jQuery( document.getElementById( 'ec_details_option_row_error_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).show( );
				errors++;
			}
		}
	} );
	// Radio Button Check
	var advanced_radio_rows = jQuery( '.ec_details_option_row.ec_option_type_radio:visible' );
	advanced_radio_rows.each( function( ){
		if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-option-required' ) == '1' ){ // Option is Required
			var selected_radios = jQuery( "input[name='ec_option_adv_" + jQuery( this ).attr( 'data-option-id' ) + "']:checked" );
			if( selected_radios.length ){
				jQuery( document.getElementById( 'ec_details_option_row_error_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).hide( );
			}else{
				jQuery( document.getElementById( 'ec_details_option_row_error_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).show( );
				errors++;
			}
		}
	} );
	// Text Box Check
	var advanced_text_rows = jQuery( '.ec_details_option_row.ec_option_type_text:visible' );
	advanced_text_rows.each( function( ){
		if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-option-required' ) == '1' ){ // Option is Required	
			if( jQuery( document.getElementById( 'ec_option_adv_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).val( ) != "" ){
				jQuery( document.getElementById( 'ec_details_option_row_error_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).hide( );
			}else{
				jQuery( document.getElementById( 'ec_details_option_row_error_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).show( );
				errors++;
			}
		}
	} );
	// Text Area Check
	var advanced_textarea_rows = jQuery( '.ec_details_option_row.ec_option_type_textarea:visible' );
	advanced_textarea_rows.each( function( ){
		if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-option-required' ) == '1' ){ // Option is Required	
			if( jQuery( document.getElementById( 'ec_option_adv_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).val( ) != "" && jQuery( document.getElementById( 'ec_option_adv_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).val( ) != 0 ){
				jQuery( document.getElementById( 'ec_details_option_row_error_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).hide( );
			}else{
				jQuery( document.getElementById( 'ec_details_option_row_error_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).show( );
				errors++;
			}
		}
	} );
	// Number Box Check
	var advanced_text_rows = jQuery( '.ec_details_option_row.ec_option_type_number:visible' );
	advanced_text_rows.each( function( ){
		if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-option-required' ) == '1' ){ // Option is Required	
			if( jQuery( document.getElementById( 'ec_option_adv_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).val( ) != "" ){
				jQuery( document.getElementById( 'ec_details_option_row_error_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).hide( );
			}else{
				jQuery( document.getElementById( 'ec_details_option_row_error_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).show( );
				errors++;
			}
		}
	} );
	// Quantity Grid Check
	var advanced_grid_rows = jQuery( '.ec_details_option_row.ec_option_type_grid:visible' );
	advanced_grid_rows.each( function( ){
		if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-option-required' ) == '1' ){ // Option is Required
			var grid_items = jQuery( this ).find( ".ec_details_option_data > .ec_details_grid_row > input" );
			var total_grid_quantity = 0;
			grid_items.each( 
				function( ){ 
					total_grid_quantity += jQuery( this ).val( ); 
				} 
			);
			if( total_grid_quantity > 0 ){
				jQuery( document.getElementById( 'ec_details_option_row_error_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).hide( );
			}else{
				jQuery( document.getElementById( 'ec_details_option_row_error_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).show( );
				errors++;
			}
		}
	} );
	// Dimensions Type 1 Check
	var advanced_dimensions_rows = jQuery( '.ec_details_option_row.ec_option_type_dimensions1:visible' );
	advanced_dimensions_rows.each( function( ){
		if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-option-required' ) == '1' ){ // Option is Required	
			// Test Width + Height
			if( jQuery( document.getElementById( 'ec_option_adv_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' + '_width' ) ).val( ) != "" && jQuery( document.getElementById( 'ec_option_adv_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' + '_height' ) ).val( ) != "" ){
				jQuery( document.getElementById( 'ec_details_option_row_error_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).hide( );
			}else{
				jQuery( document.getElementById( 'ec_details_option_row_error_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).show( );
				errors++;
			}
			
		}
	} );
	// Dimensions Type 2 Check
	var advanced_dimensions_rows = jQuery( '.ec_details_option_row.ec_option_type_dimensions2:visible' );
	advanced_dimensions_rows.each( function( ){
		if( jQuery( this ).attr( 'data-product-id' ) == '<?php echo $this->product->product_id; ?>' && jQuery( this ).attr( 'data-option-required' ) == '1' ){ // Option is Required	
			// Test Width + Height
			if( jQuery( document.getElementById( 'ec_option_adv_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' + '_width' ) ).val( ) != "" && jQuery( document.getElementById( 'ec_option_adv_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' + '_height' ) ).val( ) != "" ){
				jQuery( document.getElementById( 'ec_details_option_row_error_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).hide( );
			}else{
				jQuery( document.getElementById( 'ec_details_option_row_error_' + jQuery( this ).attr( 'data-option-id' ) + '_<?php echo $this->product->product_id; ?>' ) ).show( );
				errors++;
			}
			
		}
	} );
	// -------END Advanced Option Checks------- //
	// -------START GIFT CARD CHECK ----------- //
	var gift_card_errors = 0;
	if( document.getElementById( 'ec_giftcard_to_name_<?php echo $this->product->product_id; ?>' ) && jQuery( document.getElementById( 'ec_giftcard_to_name_<?php echo $this->product->product_id; ?>' ) ).val( ) == "" ){
		errors++;
		gift_card_errors++;
	}
	if( document.getElementById( 'ec_giftcard_to_email_<?php echo $this->product->product_id; ?>' ) && jQuery( document.getElementById( 'ec_giftcard_to_email_<?php echo $this->product->product_id; ?>' ) ).val( ) == "" ){
		errors++;
		gift_card_errors++;
	}
	if( document.getElementById( 'ec_giftcard_from_name_<?php echo $this->product->product_id; ?>' ) && jQuery( document.getElementById( 'ec_giftcard_from_name_<?php echo $this->product->product_id; ?>' ) ).val( ) == "" ){
		errors++;
		gift_card_errors++;
	}
	if( document.getElementById( 'ec_giftcard_message_<?php echo $this->product->product_id; ?>' ) && jQuery( document.getElementById( 'ec_giftcard_message_<?php echo $this->product->product_id; ?>' ) ).val( ) == "" ){
		errors++;
		gift_card_errors++;
	}
	if( gift_card_errors ){
		jQuery( document.getElementById( 'ec_details_giftcard_error_<?php echo $this->product->product_id; ?>' ) ).show( );
	}else{
		jQuery( document.getElementById( 'ec_details_giftcard_error_<?php echo $this->product->product_id; ?>' ) ).hide( );
	}
	// -------END GIFT CARD CHECK   ----------- //
	// -------START DONATION CHECK  ----------- //
	if( document.getElementById( 'ec_donation_amount_<?php echo $this->product->product_id; ?>' ) ){
		if( isNaN( jQuery( document.getElementById( 'ec_donation_amount_<?php echo $this->product->product_id; ?>' ) ).val( ) ) || Number( jQuery( document.getElementById( 'ec_donation_amount_<?php echo $this->product->product_id; ?>' ) ).val( ) ) < <?php echo $this->product->price; ?> ){
			jQuery( document.getElementById( 'ec_details_donation_error_<?php echo $this->product->product_id; ?>' ) ).show( );
			errors++;
		}else{
			jQuery( document.getElementById( 'ec_details_donation_error_<?php echo $this->product->product_id; ?>' ) ).hide( );
		}
	}
	// -------END DONATION CHECK    ----------- //
	// -------START INQUIRY CHECK  ----------- //
	if( document.getElementById( 'ec_inquiry_name' ) ){
		if( jQuery( document.getElementById( 'ec_inquiry_name' ) ).val( ) == "" || jQuery( document.getElementById( 'ec_inquiry_email' ) ).val( ) == "" || jQuery( document.getElementById( 'ec_inquiry_message' ) ).val( ) == "" ){
			jQuery( document.getElementById( 'ec_details_inquiry_error' ) ).show( );
			errors++;
		}else{
			jQuery( document.getElementById( 'ec_details_inquiry_error' ) ).hide( );
		}
	}
	// -------END INQUIRY CHECK    ----------- //
	// Stock Quantity Check
	var entered_quantity = Number( jQuery( document.getElementById( 'ec_quantity_<?php echo $this->product->product_id; ?>' ) ).val( ) );
	var allowed_quantity = 9999999999999;
	if( jQuery( document.getElementById( 'ec_details_stock_quantity' ) ).length ){
		allowed_quantity = Number( jQuery( document.getElementById( 'ec_details_stock_quantity' ) ).html( ) );
	}
	// Backorder Check
	if( allowed_quantity <= 0 && jQuery( document.getElementById( 'ec_back_order_info_<?php echo $this->product->product_id; ?>' ) ).length ){
		allowed_quantity = 1000000;
	}
	// Check Stock Quantity
	if( entered_quantity > allowed_quantity ){
		jQuery( document.getElementById( 'ec_addtocart_quantity_exceeded_error_<?php echo $this->product->product_id; ?>' ) ).show( );
		errors++;
	}else{
		jQuery( document.getElementById( 'ec_addtocart_quantity_exceeded_error_<?php echo $this->product->product_id; ?>' ) ).hide( );
	}
	// Minimum Quantity Check
	var min_quantity = <?php echo $this->product->min_purchase_quantity; ?>;
	if( entered_quantity < min_quantity ){
		jQuery( document.getElementById( 'ec_addtocart_quantity_minimum_error_<?php echo $this->product->product_id; ?>' ) ).show( );
		errors++;
	}else{
		jQuery( document.getElementById( 'ec_addtocart_quantity_minimum_error_<?php echo $this->product->product_id; ?>' ) ).hide( );
	}
	// Maximum Quantity Check
	var max_quantity = <?php echo $this->product->max_purchase_quantity; ?>;
	if( max_quantity > 0 && entered_quantity > max_quantity ){
		jQuery( document.getElementById( 'ec_addtocart_quantity_maximum_error_<?php echo $this->product->product_id; ?>' ) ).show( );
		errors++;
	}else{
		jQuery( document.getElementById( 'ec_addtocart_quantity_maximum_error_<?php echo $this->product->product_id; ?>' ) ).hide( );
	}<?php do_action( 'wp_easycart_add_to_cart_js_validation_end', $this->product->product_id ); ?>
	if( errors > 0 )
		return false;
	else
		return true;
}<?php if( trim( get_option( 'ec_option_fb_pixel' ) ) != '' ){ ?>
function wp_easycart_facebook_add_to_cart_track_<?php echo $this->product->product_id; ?>( ){
	if( ec_details_add_to_cart_<?php echo $this->product->product_id; ?>( ) ){
		fbq('track', 'AddToCart', {
			content_name: '<?php echo str_replace( "'", "\'", $this->product->title ); ?>',
			content: [{id: '<?php echo $this->product->product_id; ?>', quantity: jQuery( document.getElementById( 'ec_quantity_<?php echo $this->product->product_id; ?>' ) ).val( ), item_price: <?php echo number_format( $this->product->price, 2, '.', '' ); ?>}],
			content_type: 'product',
			value: Number( jQuery( document.getElementById( 'ec_quantity_<?php echo $this->product->product_id; ?>' ) ).val( ) * <?php echo number_format( $this->product->price, 2, '.', '' ); ?> ).toFixed( 2 ),
			currency: '<?php echo $GLOBALS['currency']->get_currency_code( ); ?>'
		});
	}
}<?php }?>
jQuery( '.ec_details_review_input_<?php echo $this->product->product_id; ?>' ).hover( function( ){	
	var score = jQuery( this ).attr( 'data-review-score' );
	jQuery( '.ec_details_review_input_<?php echo $this->product->product_id; ?>' ).removeClass( 'ec_product_details_star_on' ).addClass( 'ec_product_details_star_off' );
	for( var i=0; i<score; i++ ){
		jQuery( document.getElementById( 'ec_details_review_star' + (i+1) + '_<?php echo $this->product->product_id; ?>' ) ).removeClass( 'ec_product_details_star_off' ).addClass( 'ec_product_details_star_on' );
	}
} );
jQuery( '.ec_product_openclose' ).hide( );
function ec_option1_image_change_<?php echo $this->product->product_id; ?>( optionitem_id_1, quantity ){<?php if( $this->product->use_optionitem_images ){ ?>
	if( optionitem_id_1 != 0 ){
		jQuery( '.ec_details_thumbnails_<?php echo $this->product->product_id; ?>' ).hide( );
		jQuery( '.ec_details_large_popup_thumbnails_<?php echo $this->product->product_id; ?>' ).hide( );
		jQuery( document.getElementById( 'ec_details_thumbnails_' + optionitem_id_1 + '_<?php echo $this->product->product_id; ?>' ) ).find( '.ec_details_thumbnail' ).first( ).trigger( 'click' );
		if( !jQuery( document.getElementById( 'ec_details_thumbnails_' + optionitem_id_1 + '_<?php echo $this->product->product_id; ?>' ) ).hasClass( 'ec_no_thumbnails' ) ){
			jQuery( document.getElementById( 'ec_details_thumbnails_' + optionitem_id_1 + '_<?php echo $this->product->product_id; ?>' ) ).show( );
			if( document.getElementById( 'ec_details_large_popup_thumbnails_' + optionitem_id_1 + '_<?php echo $this->product->product_id; ?>' ) ){
				document.getElementById( 'ec_details_large_popup_thumbnails_' + optionitem_id_1 + '_<?php echo $this->product->product_id; ?>' ).style.display = "inline-block";
			}
		}
	}<?php }?>
}
function ec_option1_selected_<?php echo $this->product->product_id; ?>( optionitem_id_1, quantity ){<?php if( $this->product->use_optionitem_images ){ ?>
	jQuery( '.ec_details_thumbnails_<?php echo $this->product->product_id; ?>' ).hide( );
	jQuery( '.ec_details_large_popup_thumbnails_<?php echo $this->product->product_id; ?>' ).hide( );
	jQuery( document.getElementById( 'ec_details_thumbnails_' + optionitem_id_1 + '_<?php echo $this->product->product_id; ?>' ) ).find( '.ec_details_thumbnail' ).first( ).trigger( 'click' );
	if( !jQuery( document.getElementById( 'ec_details_thumbnails_' + optionitem_id_1 + '_<?php echo $this->product->product_id; ?>' ) ).hasClass( 'ec_no_thumbnails' ) ){
		jQuery( document.getElementById( 'ec_details_thumbnails_' + optionitem_id_1 + '_<?php echo $this->product->product_id; ?>' ) ).show( );
		jQuery( document.getElementById( 'ec_details_large_popup_thumbnails_' + optionitem_id_1 + '_<?php echo $this->product->product_id; ?>' ) ).show( );
	}<?php }?>
	jQuery( '.ec_details_swatches > li.ec_option2_<?php echo $this->product->product_id; ?>' ).removeClass( 'ec_selected' ).removeClass( 'ec_active' );
	jQuery( '.ec_details_swatches > li.ec_option3_<?php echo $this->product->product_id; ?>' ).removeClass( 'ec_selected' ).removeClass( 'ec_active' );
	jQuery( '.ec_details_swatches > li.ec_option4_<?php echo $this->product->product_id; ?>' ).removeClass( 'ec_selected' ).removeClass( 'ec_active' );
	jQuery( '.ec_details_swatches > li.ec_option5_<?php echo $this->product->product_id; ?>' ).removeClass( 'ec_selected' ).removeClass( 'ec_active' );
	jQuery( '.ec_details_combo.ec_option2_<?php echo $this->product->product_id; ?>' ).val( '0' ).prop( 'disabled', true ).addClass( 'ec_inactive' );
	jQuery( '.ec_details_combo.ec_option3_<?php echo $this->product->product_id; ?>' ).val( '0' ).prop( 'disabled', true ).addClass( 'ec_inactive' );
	jQuery( '.ec_details_combo.ec_option4_<?php echo $this->product->product_id; ?>' ).val( '0' ).prop( 'disabled', true ).addClass( 'ec_inactive' );
	jQuery( '.ec_details_combo.ec_option5_<?php echo $this->product->product_id; ?>' ).val( '0' ).prop( 'disabled', true ).addClass( 'ec_inactive' );
	jQuery( document.getElementById( 'ec_details_stock_quantity_<?php echo $this->product->product_id; ?>' ) ).html( quantity );
	jQuery( document.getElementById('ec_quantity_<?php echo $this->product->product_id; ?>' ) ).attr( 'max', quantity );
	if( Number( jQuery( document.getElementById('ec_quantity_<?php echo $this->product->product_id; ?>' ) ).val( ) ) > Number( quantity ) ){
		 jQuery( document.getElementById('ec_quantity_<?php echo $this->product->product_id; ?>' ) ).val( quantity );
	}
	jQuery( document.getElementById( 'ec_option_loading_2_<?php echo $this->product->product_id; ?>' ) ).show( );
	var next_options = jQuery( '.ec_details_swatches > li.ec_option2_<?php echo $this->product->product_id; ?>' );
	if( next_options.length ){
		next_options.each( function( ){
			jQuery( this ).removeClass( 'ec_active' );
		} );
		var data = {
			action: 'ec_ajax_get_optionitem_quantities',
			optionitem_id_1: optionitem_id_1,
			product_id: '<?php echo $this->product->product_id; ?>'
		};
		jQuery.ajax( { url: wpeasycart_ajax_object.ajax_url, type: 'post', data: data, success: function( data ){ 
			var json_data = JSON.parse( data );
			jQuery( document.getElementById( 'ec_option_loading_2_<?php echo $this->product->product_id; ?>' ) ).hide( );
			var i=0;
			next_options.each( function( ){
				if( json_data.length > i && json_data[i].optionitem_id == jQuery( this ).attr( 'data-optionitem-id' ) ){
					if( json_data[i].optionitem_id == jQuery( this ).attr( 'data-optionitem-id' ) ){
						jQuery( this ).attr( 'data-optionitem-quantity', json_data[i].quantity );
						if( json_data[i].quantity > 0 ){
							jQuery( this ).addClass( 'ec_active' );
						}
						i++;
					}
				}
			} );
		} } );
	}else{
		next_options = jQuery( '.ec_details_combo.ec_option2_<?php echo $this->product->product_id; ?> option' );
		next_options.each( function( ){
			jQuery( this ).removeClass( 'ec_active' );
		} );
		var data = {
			action: 'ec_ajax_get_optionitem_quantities',
			optionitem_id_1: optionitem_id_1,
			product_id: '<?php echo $this->product->product_id; ?>'
		};
		jQuery.ajax( { url: wpeasycart_ajax_object.ajax_url, type: 'post', data: data, success: function( data ){ 
			jQuery( '.ec_details_combo.ec_option2_<?php echo $this->product->product_id; ?>' ).prop( 'disabled', false ).removeClass( 'ec_inactive' );
			var json_data = JSON.parse( data );
			jQuery( document.getElementById( 'ec_option_loading_2_<?php echo $this->product->product_id; ?>' ) ).hide( );
			var i=0;
			next_options.each( function( ){
				if( i >= json_data.length ){
					jQuery( this ).attr( 'data-optionitem-quantity', 0 );
					jQuery( this ).hide( );
					jQuery( this ).prop( 'disabled', true );
					
				}else if( jQuery( this ).val( ) == json_data[i].optionitem_id ){
					jQuery( this ).show( );
					jQuery( this ).prop( 'disabled', false );
					jQuery( this ).attr( 'data-optionitem-quantity', json_data[i].quantity )
					i++;
					
				}else{
					jQuery( this ).attr( 'data-optionitem-quantity', 0 );
					jQuery( this ).hide( );
					jQuery( this ).prop( 'disabled', true );
				}
			} );
		} } );
	}
}
function option2_selected_<?php echo $this->product->product_id; ?>( optionitem_id_1, optionitem_id_2, quantity ){	
	jQuery( '.ec_details_swatches > li.ec_option3_<?php echo $this->product->product_id; ?>' ).removeClass( 'ec_selected' ).removeClass( 'ec_active' );
	jQuery( '.ec_details_swatches > li.ec_option4_<?php echo $this->product->product_id; ?>' ).removeClass( 'ec_selected' ).removeClass( 'ec_active' );
	jQuery( '.ec_details_swatches > li.ec_option5_<?php echo $this->product->product_id; ?>' ).removeClass( 'ec_selected' ).removeClass( 'ec_active' );
	jQuery( '.ec_details_combo.ec_option3_<?php echo $this->product->product_id; ?>' ).val( '0' ).prop( 'disabled', true ).addClass( 'ec_inactive' );
	jQuery( '.ec_details_combo.ec_option4_<?php echo $this->product->product_id; ?>' ).val( '0' ).prop( 'disabled', true ).addClass( 'ec_inactive' );
	jQuery( '.ec_details_combo.ec_option5_<?php echo $this->product->product_id; ?>' ).val( '0' ).prop( 'disabled', true ).addClass( 'ec_inactive' );
	jQuery( '.ec_option2_<?php echo $this->product->product_id; ?>.ec_details_option_row_error' ).hide( );
	jQuery( document.getElementById( 'ec_details_stock_quantity_<?php echo $this->product->product_id; ?>' ) ).html( quantity );
	jQuery( document.getElementById('ec_quantity_<?php echo $this->product->product_id; ?>' ) ).attr( 'max', quantity );
	if( Number( jQuery( document.getElementById('ec_quantity_<?php echo $this->product->product_id; ?>' ) ).val( ) ) > Number( quantity ) ){
		 jQuery( document.getElementById('ec_quantity_<?php echo $this->product->product_id; ?>' ) ).val( quantity );
	}
	var next_options = jQuery( '.ec_details_swatches > li.ec_option3_<?php echo $this->product->product_id; ?>' );
	jQuery( document.getElementById( 'ec_option_loading_3_<?php echo $this->product->product_id; ?>' ) ).show( );
	if( next_options.length ){
		next_options.each( function( ){
			jQuery( this ).removeClass( 'ec_active' );
		} );
		var data = {
			action: 'ec_ajax_get_optionitem_quantities',
			optionitem_id_1: optionitem_id_1,
			optionitem_id_2: optionitem_id_2,
			product_id: '<?php echo $this->product->product_id; ?>'
		};
		jQuery.ajax( { url: wpeasycart_ajax_object.ajax_url, type: 'post', data: data, success: function( data ){ 
			var json_data = JSON.parse( data );
			jQuery( document.getElementById( 'ec_option_loading_3_<?php echo $this->product->product_id; ?>' ) ).hide( );
			var i=0;
			next_options.each( function( ){
				if( json_data.length > i && json_data[i].optionitem_id == jQuery( this ).attr( 'data-optionitem-id' ) ){
					jQuery( this ).attr( 'data-optionitem-quantity', json_data[i].quantity );
					if( json_data[i].quantity > 0 ){
						jQuery( this ).addClass( 'ec_active' );
					}
					i++;
				}
			} );
		} } );
	}else{
		next_options = jQuery( '.ec_details_combo.ec_option3_<?php echo $this->product->product_id; ?> option' );
		next_options.each( function( ){
			jQuery( this ).removeClass( 'ec_active' );
		} );
		var data = {
			action: 'ec_ajax_get_optionitem_quantities',
			optionitem_id_1: optionitem_id_1,
			optionitem_id_2: optionitem_id_2,
			product_id: '<?php echo $this->product->product_id; ?>'
		};
		jQuery.ajax( { url: wpeasycart_ajax_object.ajax_url, type: 'post', data: data, success: function( data ){ 
			jQuery( '.ec_details_combo.ec_option3_<?php echo $this->product->product_id; ?>' ).prop( 'disabled', false ).removeClass( 'ec_inactive' );
			var json_data = JSON.parse( data );
			jQuery( document.getElementById( 'ec_option_loading_3_<?php echo $this->product->product_id; ?>' ) ).hide( );
			var i=0;
			next_options.each( function( ){
				if( i >= json_data.length ){
					jQuery( this ).attr( 'data-optionitem-quantity', 0 );
					jQuery( this ).hide( );
					jQuery( this ).prop( 'disabled', true );
					
				}else if( jQuery( this ).val( ) == json_data[i].optionitem_id ){
					jQuery( this ).show( );
					jQuery( this ).prop( 'disabled', false );
					jQuery( this ).attr( 'data-optionitem-quantity', json_data[i].quantity )
					i++;
					
				}else{
					jQuery( this ).attr( 'data-optionitem-quantity', 0 );
					jQuery( this ).hide( );
					jQuery( this ).prop( 'disabled', true );
				}
			} );
		} } );
	}
}
function option3_selected_<?php echo $this->product->product_id; ?>( optionitem_id_1, optionitem_id_2, optionitem_id_3, quantity ){	
	jQuery( '.ec_details_swatches > li.ec_option4_<?php echo $this->product->product_id; ?>' ).removeClass( 'ec_selected' ).removeClass( 'ec_active' );
	jQuery( '.ec_details_swatches > li.ec_option5_<?php echo $this->product->product_id; ?>' ).removeClass( 'ec_selected' ).removeClass( 'ec_active' );
	jQuery( '.ec_details_combo.ec_option4_<?php echo $this->product->product_id; ?>' ).val( '0' ).prop( 'disabled', true ).addClass( 'ec_inactive' );
	jQuery( '.ec_details_combo.ec_option5_<?php echo $this->product->product_id; ?>' ).val( '0' ).prop( 'disabled', true ).addClass( 'ec_inactive' );
	jQuery( '.ec_option3_<?php echo $this->product->product_id; ?>.ec_details_option_row_error' ).hide( );
	jQuery( document.getElementById( 'ec_details_stock_quantity_<?php echo $this->product->product_id; ?>' ) ).html( quantity );
	jQuery( document.getElementById('ec_quantity_<?php echo $this->product->product_id; ?>' ) ).attr( 'max', quantity );
	if( Number( jQuery( document.getElementById('ec_quantity_<?php echo $this->product->product_id; ?>' ) ).val( ) ) > Number( quantity ) ){
		 jQuery( document.getElementById('ec_quantity_<?php echo $this->product->product_id; ?>' ) ).val( quantity );
	}
	var next_options = jQuery( '.ec_details_swatches > li.ec_option4_<?php echo $this->product->product_id; ?>' );
	jQuery( document.getElementById( 'ec_option_loading_4_<?php echo $this->product->product_id; ?>' ) ).show( );
	if( next_options.length ){
		next_options.each( function( ){
			jQuery( this ).removeClass( 'ec_active' );
		} );
		var data = {
			action: 'ec_ajax_get_optionitem_quantities',
			optionitem_id_1: optionitem_id_1,
			optionitem_id_2: optionitem_id_2,
			optionitem_id_3: optionitem_id_3,
			product_id: '<?php echo $this->product->product_id; ?>'
		};
		jQuery.ajax( { url: wpeasycart_ajax_object.ajax_url, type: 'post', data: data, success: function( data ){ 
			var json_data = JSON.parse( data );
			jQuery( document.getElementById( 'ec_option_loading_4_<?php echo $this->product->product_id; ?>' ) ).hide( );
			var i=0;
			next_options.each( function( ){
				if( json_data.length > i && json_data[i].optionitem_id == jQuery( this ).attr( 'data-optionitem-id' ) ){
					jQuery( this ).attr( 'data-optionitem-quantity', json_data[i].quantity );
					if( json_data[i].quantity > 0 ){
						jQuery( this ).addClass( 'ec_active' );
					}
					i++;
				}
			} );
		} } );
	}else{
		next_options = jQuery( '.ec_details_combo.ec_option4_<?php echo $this->product->product_id; ?> option' );
		next_options.each( function( ){
			jQuery( this ).removeClass( 'ec_active' );
		} );
		var data = {
			action: 'ec_ajax_get_optionitem_quantities',
			optionitem_id_1: optionitem_id_1,
			optionitem_id_2: optionitem_id_2,
			optionitem_id_3: optionitem_id_3,
			product_id: '<?php echo $this->product->product_id; ?>'
		};
		jQuery.ajax( { url: wpeasycart_ajax_object.ajax_url, type: 'post', data: data, success: function( data ){ 
			jQuery( '.ec_details_combo.ec_option4_<?php echo $this->product->product_id; ?>' ).prop( 'disabled', false ).removeClass( 'ec_inactive' );
			var json_data = JSON.parse( data );
			jQuery( document.getElementById( 'ec_option_loading_4_<?php echo $this->product->product_id; ?>' ) ).hide( );
			var i=0;
			next_options.each( function( ){
				if( i >= json_data.length ){
					jQuery( this ).attr( 'data-optionitem-quantity', 0 );
					jQuery( this ).hide( );
					jQuery( this ).prop( 'disabled', true );
					
				}else if( jQuery( this ).val( ) == json_data[i].optionitem_id ){
					jQuery( this ).show( );
					jQuery( this ).prop( 'disabled', false );
					jQuery( this ).attr( 'data-optionitem-quantity', json_data[i].quantity )
					i++;
					
				}else{
					jQuery( this ).attr( 'data-optionitem-quantity', 0 );
					jQuery( this ).hide( );
					jQuery( this ).prop( 'disabled', true );
				}
			} );
		} } );
	}
}
function option4_selected_<?php echo $this->product->product_id; ?>( optionitem_id_1, optionitem_id_2, optionitem_id_3, optionitem_id_4, quantity ){	
	jQuery( '.ec_details_swatches > li.ec_option5_<?php echo $this->product->product_id; ?>' ).removeClass( 'ec_selected' ).removeClass( 'ec_active' );
	jQuery( '.ec_details_combo.ec_option5_<?php echo $this->product->product_id; ?>' ).val( '0' ).prop( 'disabled', true ).addClass( 'ec_inactive' );
	jQuery( '.ec_option4.ec_details_option_row_error' ).hide( );
	jQuery( document.getElementById( 'ec_details_stock_quantity_<?php echo $this->product->product_id; ?>' ) ).html( quantity );
	jQuery( document.getElementById('ec_quantity_<?php echo $this->product->product_id; ?>' ) ).attr( 'max', quantity );
	if( Number( jQuery( document.getElementById('ec_quantity_<?php echo $this->product->product_id; ?>' ) ).val( ) ) > Number( quantity ) ){
		 jQuery( document.getElementById('ec_quantity_<?php echo $this->product->product_id; ?>' ) ).val( quantity );
	}
	var next_options = jQuery( '.ec_details_swatches > li.ec_option5_<?php echo $this->product->product_id; ?>' );
	jQuery( document.getElementById( 'ec_option_loading_5_<?php echo $this->product->product_id; ?>' ) ).show( );
	if( next_options.length ){
		next_options.each( function( ){
			jQuery( this ).removeClass( 'ec_active' );
		} );
		var data = {
			action: 'ec_ajax_get_optionitem_quantities',
			optionitem_id_1: optionitem_id_1,
			optionitem_id_2: optionitem_id_2,
			optionitem_id_3: optionitem_id_3,
			optionitem_id_4: optionitem_id_4,
			product_id: '<?php echo $this->product->product_id; ?>'
		};
		jQuery.ajax( { url: wpeasycart_ajax_object.ajax_url, type: 'post', data: data, success: function( data ){ 
			var json_data = JSON.parse( data );
			jQuery( document.getElementById( 'ec_option_loading_5_<?php echo $this->product->product_id; ?>' ) ).hide( );
			var i=0;
			next_options.each( function( ){
				if( json_data.length > i && json_data[i].optionitem_id == jQuery( this ).attr( 'data-optionitem-id' ) ){
					jQuery( this ).attr( 'data-optionitem-quantity', json_data[i].quantity );
					if( json_data[i].quantity > 0 ){
						jQuery( this ).addClass( 'ec_active' );
					}
					i++;
				}
			} );
		} } );
	}else{
		next_options = jQuery( '.ec_details_combo.ec_option5_<?php echo $this->product->product_id; ?> option' );
		next_options.each( function( ){
			jQuery( this ).removeClass( 'ec_active' );
		} );
		var data = {
			action: 'ec_ajax_get_optionitem_quantities',
			optionitem_id_1: optionitem_id_1,
			optionitem_id_2: optionitem_id_2,
			optionitem_id_3: optionitem_id_3,
			optionitem_id_4: optionitem_id_4,
			product_id: '<?php echo $this->product->product_id; ?>'
		};
		jQuery.ajax( { url: wpeasycart_ajax_object.ajax_url, type: 'post', data: data, success: function( data ){ 
			jQuery( '.ec_details_combo.ec_option5_<?php echo $this->product->product_id; ?>' ).prop( 'disabled', false ).removeClass( 'ec_inactive' );
			var json_data = JSON.parse( data );
			jQuery( document.getElementById( 'ec_option_loading_5_<?php echo $this->product->product_id; ?>' ) ).hide( );
			var i=0;
			next_options.each( function( ){
				if( i >= json_data.length ){
					jQuery( this ).attr( 'data-optionitem-quantity', 0 );
					jQuery( this ).hide( );
					jQuery( this ).prop( 'disabled', true );
					
				}else if( jQuery( this ).val( ) == json_data[i].optionitem_id ){
					jQuery( this ).show( );
					jQuery( this ).prop( 'disabled', false );
					jQuery( this ).attr( 'data-optionitem-quantity', json_data[i].quantity )
					i++;
					
				}else{
					jQuery( this ).attr( 'data-optionitem-quantity', 0 );
					jQuery( this ).hide( );
					jQuery( this ).prop( 'disabled', true );
				}
			} );
		} } );
	}
}
function ec_submit_product_review_<?php echo $this->product->product_id; ?>( ){	
	var review_title = jQuery( document.getElementById( 'ec_review_title_<?php echo $this->product->product_id; ?>' ) ).val( );
	var review_score = 0;
	for( var i=1; i<=5; i++ ){
		if( jQuery( document.getElementById( 'ec_details_review_star' + i + '_<?php echo $this->product->product_id; ?>' ) ).hasClass( 'ec_product_details_star_on' ) ){
			review_score++;
		}
	}
	var review_message = jQuery( document.getElementById( 'ec_review_message_<?php echo $this->product->product_id; ?>' ) ).val( );
	if( review_title != "" && review_score != 0 && review_message != "" ){
		// Submit a filled out review
		jQuery( document.getElementById( 'ec_details_customer_review_loader_<?php echo $this->product->product_id; ?>' ) ).show( );
		jQuery( document.getElementById( 'ec_details_review_error_<?php echo $this->product->product_id; ?>' ) ).hide( );
		var data = {
			action: 'ec_ajax_insert_customer_review',
			review_title: review_title,
			review_score: review_score,
			review_message: review_message,
			product_id: '<?php echo $this->product->product_id; ?>'
		};
		jQuery.ajax( { url: wpeasycart_ajax_object.ajax_url, type: 'post', data: data, success: function( data ){ 
			jQuery( document.getElementById( 'ec_details_customer_review_loader_<?php echo $this->product->product_id; ?>' ) ).hide( ); 
			jQuery( document.getElementById( 'ec_details_submit_review_button_row_<?php echo $this->product->product_id; ?>' ) ).hide( );
			jQuery( document.getElementById( 'ec_details_review_submitted_button_row_<?php echo $this->product->product_id; ?>' ) ).show( );
			jQuery( document.getElementById( 'ec_details_customer_review_success_<?php echo $this->product->product_id; ?>' ) ).show( ).delay( 1500 ).fadeOut( 'slow' ); 
		} } );
	}else{
		// Something is missing, display error.
		jQuery( document.getElementById( 'ec_details_review_error_<?php echo $this->product->product_id; ?>' ) ).show( );
	}
}
</script><?php if( $is_admin && !$is_preview ){ ?>
<script>
function ec_admin_show_description_editor_<?php echo $this->product->product_id; ?>( ){
	jQuery( '.ec_details_description_content_<?php echo $this->product->product_id; ?>' ).hide( );
	jQuery( '.ec_details_description_editor_<?php echo $this->product->product_id; ?>' ).show( );
}
function ec_admin_save_description_editor_<?php echo $this->product->product_id; ?>( ){
	var new_html = tinymce.get( 'desc_<?php echo $this->product->model_number; ?>' ).getContent( );
	jQuery( '.ec_details_description_editor_<?php echo $this->product->product_id; ?>' ).hide( );
	jQuery( '.ec_details_description_content_<?php echo $this->product->product_id; ?>' ).html( new_html ).show( );
	var data = {
		action: 'ec_ajax_ec_update_product_description',
		description: new_html,
		product_id: '<?php echo $this->product->product_id; ?>'
	};
	jQuery.ajax( { url: wpeasycart_ajax_object.ajax_url, type: 'post', data: data, success: function( data ){ } } );
}
function ec_admin_show_specifications_editor_<?php echo $this->product->product_id; ?>( ){	
	jQuery( '.ec_details_specifications_content_<?php echo $this->product->product_id; ?>' ).hide( );
	jQuery( '.ec_details_specifications_editor_<?php echo $this->product->product_id; ?>' ).show( );
}
function ec_admin_save_specifications_editor_<?php echo $this->product->product_id; ?>( ){
	var new_html = tinymce.get( 'specs_<?php echo $this->product->model_number; ?>' ).getContent( );
	jQuery( '.ec_details_specifications_editor_<?php echo $this->product->product_id; ?>' ).hide( );
	jQuery( '.ec_details_specifications_content_<?php echo $this->product->product_id; ?>' ).html( new_html ).show( );
	var data = {
		action: 'ec_ajax_ec_update_product_specifications',
		specifications: new_html,
		product_id: '<?php echo $this->product->product_id; ?>'
	};
	jQuery.ajax( { url: wpeasycart_ajax_object.ajax_url, type: 'post', data: data, success: function( data ){ } } );
}
</script><?php } ?>
<?php /* END NEW PRODUCT DETAILS CONTENT */ ?>

<?php if( get_option( 'ec_option_cache_prevent' ) ){ ?>
<script type="text/javascript">
	if( jQuery( document.getElementById( 'ec_product_details_recaptcha' ) ).length ){
		var wpeasycart_register_recaptcha = grecaptcha.render( document.getElementById( 'ec_product_details_recaptcha' ), {
			'sitekey' : jQuery( document.getElementById( 'ec_grecaptcha_site_key' ) ).val( ),
			'callback' : wpeasycart_product_details_recaptcha_callback
		});
	}
	if( jQuery( document.getElementById( 'ec_product_details_inquiry_recaptcha' ) ).length ){
		var wpeasycart_inquiry_recaptcha = grecaptcha.render( document.getElementById( 'ec_product_details_inquiry_recaptcha' ), {
			'sitekey' : jQuery( document.getElementById( 'ec_grecaptcha_site_key' ) ).val( ),
			'callback' : wpeasycart_inquiry_recaptcha_callback
		});
	}
</script>
<?php }?>