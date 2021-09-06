// JavaScript Document
jQuery( document ).ready( function( ){
	jQuery( '.ec_flipbook_left' ).click( 
		function( event ){
			var current_image = jQuery( event.target ).parent( ).find( 'img.ec_flipbook_image' ).attr( 'src' );
			var image_list_string = jQuery( event.target ).parent( ).data( 'image-list' );
			var image_list = image_list_string.split( ',' );
			var prev = image_list[image_list.length - 1]; 
			for( var i=0; i<image_list.length; i++ ){ 
				if( image_list[i] == current_image ){ 
					break; 
				}else{ 
					prev = image_list[i]; 
				} 
			}
			jQuery( event.target ).parent( ).find( 'img.ec_flipbook_image' ).attr( 'src', prev );
		}
	);
	jQuery( '.ec_flipbook_right' ).click( 
		function( event ){
			var current_image = jQuery( event.target ).parent( ).find( 'img.ec_flipbook_image' ).attr( 'src' );
			var image_list_string = jQuery( event.target ).parent( ).data( 'image-list' );
			var image_list = image_list_string.split( ',' );
			var prev = image_list[0]; 
			for( var i=image_list.length-1; i>-1; i-- ){ 
				if( image_list[i] == current_image ){ 
					break; 
				}else{ 
					prev = image_list[i]; 
				} 
			}
			jQuery( event.target ).parent( ).find( 'img.ec_flipbook_image' ).attr( 'src', prev );
		}
	);
	wpeasycart_cart_billing_country_update( );
	wpeasycart_cart_shipping_country_update( );
	wpeasycart_account_billing_country_update( );
	wpeasycart_account_shipping_country_update( );
	jQuery( document.getElementById( 'ec_cart_billing_country' ) ).change( function( ){ wpeasycart_cart_billing_country_update( ); } );
	jQuery( document.getElementById( 'ec_cart_shipping_country' ) ).change( function( ){ wpeasycart_cart_shipping_country_update( ); } );
	jQuery( document.getElementById( 'ec_account_billing_information_country' ) ).change( function( ){ wpeasycart_account_billing_country_update( ); } );
	jQuery( document.getElementById( 'ec_account_shipping_information_country' ) ).change( function( ){ wpeasycart_account_shipping_country_update( ); } );
	if( jQuery( '.ec_menu_mini_cart' ).length ){
		jQuery( document.getElementById( 'ec_card_number' ) ).keydown( function( ){
			ec_show_cc_type( ec_get_card_type( jQuery( document.getElementById( 'ec_card_number' ) ).val( ) ) )
		} );
		// Load cart menu, updates over possible cached value
		var data = {
			action: 'ec_ajax_get_dynamic_cart_menu',
			language: wpeasycart_ajax_object.current_language
		};
		jQuery.ajax({url: wpeasycart_ajax_object.ajax_url, type: 'post', data: data, success: function( data ){ 
			jQuery( '.ec_menu_mini_cart' ).html( data );
		} } );
	}
	if( wpeasycart_isTouchDevice( ) ){
		jQuery( '.ec_product_quickview' ).hide( );
	}
    if( jQuery( document.getElementById( 'ec_card_number' ) ).length ){
	   jQuery( document.getElementById( 'ec_card_number' ) ).payment( 'formatCardNumber' );
	}
    if( jQuery( document.getElementById( 'ec_cc_expiration' ) ).length ){
	   jQuery( document.getElementById( 'ec_cc_expiration' ) ).payment( 'formatCardExpiry' );
    }
    if( jQuery( document.getElementById( 'ec_security_code' ) ).length ){
	   jQuery( document.getElementById( 'ec_security_code' ) ).payment( 'formatCardCVC' );
    }
    
	if( jQuery( '.ec_is_datepicker' ).length ){
        jQuery( '.ec_is_datepicker' ).datepicker( );
    }
	
    if( jQuery( '.ec_details_customer_review_list' ).length ){
        jQuery( '.ec_details_customer_review_list' ).each( function( ){
            var reviews = jQuery( this ).find( 'li' );
            var product_id = jQuery( this ).attr( 'data-product-id' );
            var reviews_per_page = jQuery( document.getElementById( 'ec_details_reviews_per_page_' + product_id ) ).val( );
            if( reviews.length > reviews_per_page ){
                var paging = '<div class="ec_details_customer_review_paging" id="ec_details_customer_review_paging_' + product_id + '">';
                for( var i=0; i<Math.ceil( reviews.length / reviews_per_page ); i++ ){
                    paging += '<button onclick="ec_customer_review_paging( ' + (i+1) + ', ' + product_id + ' );">' + ( i + 1 ) + '</button>';
                }
                paging += '</div>';
                jQuery( this ).after( paging );
                ec_customer_review_paging(1, product_id);
            }
        } );
	}
});
var wpeasycart_login_recaptcha;
var wpeasycart_register_recaptcha;
var wpeasycart_product_stock_recaptcha;
var wpeasycart_inquiry_recaptcha;
var wpeasycart_recaptcha_onload = function ( ){
	if( jQuery( document.getElementById( 'ec_account_login_recaptcha' ) ).length ){
		var wpeasycart_login_recaptcha = grecaptcha.render( document.getElementById( 'ec_account_login_recaptcha' ), {
			'sitekey' : jQuery( document.getElementById( 'ec_grecaptcha_site_key' ) ).val( ),
			'callback' : wpeasycart_login_recaptcha_callback
		});
	}
	if( jQuery( document.getElementById( 'ec_account_register_recaptcha' ) ).length ){
		var wpeasycart_register_recaptcha = grecaptcha.render( document.getElementById( 'ec_account_register_recaptcha' ), {
			'sitekey' : jQuery( document.getElementById( 'ec_grecaptcha_site_key' ) ).val( ),
			'callback' : wpeasycart_register_recaptcha_callback
		});
	}
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
}
function ec_customer_review_paging( page, product_id ){
    var review_ele = jQuery( '.ec_details_customer_review_list[data-product-id="' + product_id + '"]' );
	var reviews = review_ele.find( 'li' );
	var reviews_per_page = jQuery( document.getElementById( 'ec_details_reviews_per_page_' + product_id ) ).val( );
	
	if( reviews.length > reviews_per_page ){
		var i=0;
		reviews.each( function( ){
			if( i < (reviews_per_page * page ) && i >= (reviews_per_page * (page-1) ) ){
				jQuery( this ).show( );
			}else{
				jQuery( this ).hide( );
			}
			i++;
		} );
		i=0;
		review_ele.find( '#ec_details_customer_review_paging_' + product_id + ' > button' ).removeClass( 'selected' ).each( function( ){
			if( ( i+1 ) == page ){
				jQuery( this ).addClass( 'selected' );
			}
			i++;
		} );
	}
}
function wpeasycart_login_recaptcha_callback( response ){
	jQuery( document.getElementById( 'ec_grecaptcha_response_login' ) ).val( response );
	if( response.length ){
		jQuery( '#ec_account_login_recaptcha > div' ).css( 'border', 'none' );
	}else{
		jQuery( '#ec_account_login_recaptcha > div' ).css( 'border', '1px solid red' );
	}
}
function wpeasycart_register_recaptcha_callback( response ){
	jQuery( document.getElementById( 'ec_grecaptcha_response_register' ) ).val( response );
	if( response.length ){
		jQuery( '#ec_account_register_recaptcha > div' ).css( 'border', 'none' );
	}else{
		jQuery( '#ec_account_register_recaptcha > div' ).css( 'border', '1px solid red' );
	}
}
function wpeasycart_product_details_recaptcha_callback( response ){
	jQuery( document.getElementById( 'ec_grecaptcha_response_product_details' ) ).val( response );
	if( response.length ){
		jQuery( '#ec_product_details_recaptcha > div' ).css( 'border', 'none' );
	}else{
		jQuery( '#ec_product_details_recaptcha > div' ).css( 'border', '1px solid red' );
	}
}
function wpeasycart_inquiry_recaptcha_callback( response ){
	jQuery( document.getElementById( 'ec_grecaptcha_response_inquiry' ) ).val( response );
	if( response.length ){
		jQuery( '#ec_product_details_inquiry_recaptcha > div' ).css( 'border', 'none' );
	}else{
		jQuery( '#ec_product_details_inquiry_recaptcha > div' ).css( 'border', '1px solid red' );
	}
}
function wpeasycart_cart_billing_country_update( ){
	if( document.getElementById( 'ec_cart_billing_country' ) ){
		var selected_country = jQuery( document.getElementById( 'ec_cart_billing_country' ) ).val( );
		if( ec_is_state_required( selected_country ) )
			jQuery( document.getElementById( 'ec_billing_state_required' ) ).show( );
		else
			jQuery( document.getElementById( 'ec_billing_state_required' ) ).hide( );
		
		if( document.getElementById( 'ec_cart_billing_state_' + selected_country ) ){
			jQuery( '.ec_billing_state_dropdown, #ec_cart_billing_state' ).hide( );
			jQuery( document.getElementById( 'ec_cart_billing_state_' + selected_country ) ).show( );
		}else{
			jQuery( '.ec_billing_state_dropdown' ).hide( );
			jQuery( document.getElementById( 'ec_cart_billing_state' ) ).show( );
		}
	}
}
function wpeasycart_cart_shipping_country_update( ){
	if( document.getElementById( 'ec_cart_shipping_country' ) ){
		var selected_country = jQuery( document.getElementById( 'ec_cart_shipping_country' ) ).val( );
		if( ec_is_state_required( selected_country ) )
			jQuery( document.getElementById( 'ec_shipping_state_required' ) ).show( );
		else
			jQuery( document.getElementById( 'ec_shipping_state_required' ) ).hide( );
		
		if( document.getElementById( 'ec_cart_shipping_state_' + selected_country ) ){
			jQuery( '.ec_shipping_state_dropdown, #ec_cart_shipping_state' ).hide( );
			jQuery( document.getElementById( 'ec_cart_shipping_state_' + selected_country ) ).show( );
		}else{
			jQuery( '.ec_shipping_state_dropdown' ).hide( );
			jQuery( document.getElementById( 'ec_cart_shipping_state' ) ).show( );
		}
	}
}
function wpeasycart_account_billing_country_update( ){
	if( document.getElementById( 'ec_account_billing_information_country' ) ){
		var selected_country = jQuery( document.getElementById( 'ec_account_billing_information_country' ) ).val( );
		if( ec_is_state_required( selected_country ) )
			jQuery( document.getElementById( 'ec_billing_state_required' ) ).show( );
		else
			jQuery( document.getElementById( 'ec_billing_state_required' ) ).hide( );
		
		if( document.getElementById( 'ec_account_billing_information_state_' + selected_country ) ){
			jQuery( '.ec_billing_state_dropdown, #ec_account_billing_information_state' ).hide( );
			jQuery( document.getElementById( 'ec_account_billing_information_state_' + selected_country ) ).show( );
		}else{
			jQuery( '.ec_billing_state_dropdown' ).hide( );
			jQuery( document.getElementById( 'ec_account_billing_information_state' ) ).show( );
		}
	}
}
function wpeasycart_account_shipping_country_update( ){
	if( document.getElementById( 'ec_account_shipping_information_country' ) ){
		var selected_country = jQuery( document.getElementById( 'ec_account_shipping_information_country' ) ).val( );
		if( ec_is_state_required( selected_country ) )
			jQuery( document.getElementById( 'ec_shipping_state_required' ) ).show( );
		else
			jQuery( document.getElementById( 'ec_shipping_state_required' ) ).hide( );
		
		if( document.getElementById( 'ec_account_shipping_information_state_' + selected_country ) ){
			jQuery( '.ec_shipping_state_dropdown, #ec_account_shipping_information_state' ).hide( );
			jQuery( document.getElementById( 'ec_account_shipping_information_state_' + selected_country ) ).show( );
		}else{
			jQuery( '.ec_shipping_state_dropdown' ).hide( );
			jQuery( document.getElementById( 'ec_account_shipping_information_state' ) ).show( );
		}
	}
}
function wpeasycart_isTouchDevice() {
      return 'ontouchstart' in window || !!(navigator.msMaxTouchPoints);
}
function ec_product_show_quick_view_link( modelnum ){
	jQuery( document.getElementById( 'ec_product_quickview_container_' + modelnum ) ).fadeIn(100);	
}
function ec_product_hide_quick_view_link( modelnum ){
	jQuery( document.getElementById( 'ec_product_quickview_container_' + modelnum ) ).fadeOut(100);	
}
function change_product_sort( menu_id, menu_name, submenu_id, submenu_name, subsubmenu_id, subsubmenu_name, manufacturer_id, pricepoint_id, currentpage_selected, perpage, URL, divider ){
	var url_string = URL + divider + "filternum=" + document.getElementById('sortfield').value;
	if( subsubmenu_id != 0 ){
		url_string = url_string + "&subsubmenuid=" + subsubmenu_id;
		if( subsubmenu_name != 0 )
			url_string = url_string + "&subsubmenu=" + subsubmenu_name;
	}else if( submenu_id != 0 ){
		url_string = url_string + "&submenuid=" + submenu_id;
		if( submenu_name != 0 )
			url_string = url_string + "&submenu=" + submenu_name;	
	}else if( menu_id != 0 ){
		url_string = url_string + "&menuid=" + menu_id;
		if( menu_name != 0 )
			url_string = url_string + "&menu=" + menu_name;
	}
	if( manufacturer_id > 0 )
		url_string = url_string + "&manufacturer=" + manufacturer_id;
	if( pricepoint_id > 0 )
		url_string = url_string + "&pricepoint=" + pricepoint_id;
	if( currentpage_selected )
		url_string = url_string + "&pagenum=" + currentpage_selected;
	if( perpage )
		url_string = url_string + "&perpage=" + perpage; 
	window.location = url_string;
}
function ec_add_to_cart( product_id, model_number, quantity, use_quantity_tracking, min_quantity, max_quantity ){
	if( !use_quantity_tracking || ( !isNaN( quantity ) && quantity > 0 && quantity >= min_quantity && quantity <= max_quantity ) ){
		
		ec_product_hide_quick_view_link( model_number );
		jQuery( document.getElementById( 'ec_addtocart_quantity_exceeded_error_' + model_number ) ).hide( );
		jQuery( document.getElementById( 'ec_addtocart_quantity_minimum_error_' + model_number ) ).hide( );
		
		jQuery( document.getElementById( "ec_product_loader_" + model_number ) ).show( );
		var data = {
			action: 'ec_ajax_add_to_cart',
			product_id: product_id,
			model_number: model_number,
			quantity: quantity,
			cart_id: jQuery( document.getElementById( 'wp_easycart_cart_id' ) ).val( )
		};
		
		jQuery.ajax({url: wpeasycart_ajax_object.ajax_url, type: 'post', data: data, success: function( data ){ 
			var json_data = JSON.parse( data );
			jQuery( document.getElementById( "ec_product_loader_" + model_number ) ).hide( );
			jQuery( document.getElementById( "ec_product_added_" + model_number ) ).show( ).delay( 2500 ).fadeOut( 'slow' );
			
			if( document.getElementById( "ec_add_to_cart_" + product_id ) )
				jQuery( document.getElementById( "ec_add_to_cart_" + product_id ) ).css( 'display', 'none' );
			
			if( document.getElementById( "ec_added_to_cart_" + product_id ) )
				jQuery( document.getElementById( "ec_added_to_cart_" + product_id ) ).css( 'display', 'inline-block' );
			
			if( document.getElementById( "ec_add_to_cart_type6_" + product_id ) )
				jQuery( document.getElementById( "ec_add_to_cart_type6_" + product_id ) ).css( 'display', 'none' );
			
			if( document.getElementById( "ec_added_to_cart_type6_" + product_id  ) )
				jQuery( document.getElementById( "ec_added_to_cart_type6_" + product_id ) ).css( 'display', 'inline-block' );
				
			jQuery( '.ec_product_added_to_cart' ).fadeIn( 'slow' );
			jQuery( ".ec_cart_items_total" ).html( json_data[0].total_items );
			jQuery( ".ec_cart_price_total" ).html( json_data[0].total_price );
			
			if( json_data[0].total_items == 1 ){
				jQuery( ".ec_menu_cart_singular_text" ).show( );
				jQuery( ".ec_menu_cart_plural_text" ).hide( );
			}else{
				jQuery( ".ec_menu_cart_singular_text" ).hide( );
				jQuery( ".ec_menu_cart_plural_text" ).show( );
			}
			
			if( json_data[0].total_items == 0 ){
				jQuery( ".ec_cart_price_total" ).hide( );
			}else{
				jQuery( ".ec_cart_price_total" ).show( );
			}
			
			if( jQuery( '.ec_cart_widget_minicart_product_padding' ).length ){
				
				jQuery( '.ec_cart_widget_minicart_product_padding' ).append( '<div class="ec_cart_widget_minicart_product_title" id="ec_cart_widget_row_' + json_data[0].cartitem_id + '">' + json_data[0].title + ' x 1 @ ' + json_data[0].price + '</div>' );
				
			}
			
		} } );
		
	}else{
		if( !isNaN( quantity ) && ( quantity < min_quantity || quantity < 1 ) ){
			jQuery( document.getElementById( 'ec_addtocart_quantity_minimum_error_' + model_number ) ).show( );
			jQuery( document.getElementById( 'ec_addtocart_quantity_exceeded_error_' + model_number ) ).hide( );
		}else{
			jQuery( document.getElementById( 'ec_addtocart_quantity_exceeded_error_' + model_number ) ).show( );
			jQuery( document.getElementById( 'ec_addtocart_quantity_minimum_error_' + model_number ) ).hide( );
		}
	}
	
}

function ec_minus_quantity( product_id, min_quantity ){	
	var currval = jQuery( document.getElementById( 'ec_quantity_' + product_id ) ).val( );
	currval = Number( currval ) - 1;
	if( currval <= 0 ){
		currval = 1;
	}
	if( currval < min_quantity ){
		currval = min_quantity;
	}
	jQuery( document.getElementById( 'ec_quantity_' + product_id ) ).val( currval );
}

function ec_plus_quantity( product_id, track_quantity, max_quantity ){
	if( jQuery( document.getElementById( 'ec_details_stock_quantity_' + product_id ) ).length && jQuery( document.getElementById( 'ec_details_stock_quantity_' + product_id ) ).val( ) != 10000000 ){
		max_quantity = Number( jQuery( document.getElementById( 'ec_details_stock_quantity_' + product_id ) ).html( ) );
	}
	if( max_quantity > 0 && max_quantity != 10000000 ){
		jQuery( document.getElementById( 'ec_quantity_' + product_id ) ).attr( 'max', max_quantity );
	}
	var currval = Number( jQuery( document.getElementById( 'ec_quantity_' + product_id ) ).val( ) );
	if( currval < Number( max_quantity ) ){
		currval = currval + 1;
	}else if( max_quantity != 10000000 ){
		currval = Number( max_quantity );
	}else{
		currval = currval + 1;
	}
	jQuery( document.getElementById( 'ec_quantity_' + product_id ) ).val( currval );
}

function ec_cartitem_delete( cartitem_id, model_number ){
	var data = {
		action: 'ec_ajax_cartitem_delete',
		ec_v3_24: 'true',
		cartitem_id: cartitem_id
	}
	
	jQuery( document.getElementById( 'ec_cartitem_delete_' + cartitem_id ) ).hide( );
	jQuery( document.getElementById( 'ec_cartitem_deleting_' + cartitem_id ) ).show( );
	
	jQuery.ajax({url: wpeasycart_ajax_object.ajax_url, type: 'post', data: data, success: function(data){ 
		jQuery( document.getElementById( 'ec_cartitem_row_' + cartitem_id ) ).remove( );
		jQuery( document.getElementById( 'ec_cartitem_min_error_' + cartitem_id ) ).remove( );
		jQuery( document.getElementById( 'ec_cartitem_max_error_' + cartitem_id ) ).remove( );
		jQuery( document.getElementById( 'ec_cart_widget_row_' + cartitem_id ) ).remove( );
		
		// Get Response Data
		var response_obj = JSON.parse( data );
		
		// Update Cart
		ec_update_cart( response_obj );
		
	} } );
}

function ec_cartitem_update( cartitem_id, model_number ){
	var data = {
		action: 'ec_ajax_cartitem_update',
		ec_v3_24: 'true',
		cartitem_id: cartitem_id,
		quantity: jQuery( document.getElementById( 'ec_quantity_' + model_number ) ).val( )
	};
	
	jQuery( document.getElementById( 'ec_cartitem_updating_' + cartitem_id ) ).show( );
	
	jQuery.ajax({url: wpeasycart_ajax_object.ajax_url, type: 'post', data: data, success: function( data ){ 
		
		jQuery( document.getElementById( 'ec_cartitem_updating_' + cartitem_id ) ).hide( );
		
		// Get Response Data
		var response_obj = JSON.parse( data );
		
		// Update Cart
		ec_update_cart( response_obj );
		
	} } );
}

function ec_apply_coupon( ){
	
	jQuery( document.getElementById( 'ec_apply_coupon' ) ).hide( );
	jQuery( document.getElementById( 'ec_applying_coupon' ) ).show( );
	
	var data = {
		action: 'ec_ajax_redeem_coupon_code',
		ec_v3_24: 'true',
		couponcode: jQuery( document.getElementById( 'ec_coupon_code' ) ).val( )
	};
	
	jQuery.ajax( {
		url: wpeasycart_ajax_object.ajax_url, 
		type: 'post', 
		data: data, 
		success: function( data ){ 
			jQuery( document.getElementById( 'ec_apply_coupon' ) ).show( );
			jQuery( document.getElementById( 'ec_applying_coupon' ) ).hide( );
			
			// Get Response Data
			var response_obj = JSON.parse( data );
			
			// Update Cart
			ec_update_cart( response_obj );
			
			// Update Coupon Info
			if( response_obj.is_coupon_valid ){
				jQuery( document.getElementById( 'ec_coupon_error' ) ).hide( );
				jQuery( document.getElementById( 'ec_coupon_success' ) ).html( response_obj.coupon_message ).show( );
			}else{
				jQuery( document.getElementById( 'ec_coupon_success' ) ).hide( );
				jQuery( document.getElementById( 'ec_coupon_error' ) ).html( response_obj.coupon_message ).show( );
			}
		} 
	} );
}

function update_subscription_totals( product_id ){
	var shipping_selector = 0;
	var address_type = 'billing';
	if( jQuery( document.getElementById( 'ec_shipping_selector' ) ).length && jQuery( document.getElementById( 'ec_shipping_selector' ) ).is( ':checked' ) ){
		address_type = 'shipping';
		shipping_selector = 1;
	}
	
	var billing_state = jQuery( document.getElementById( 'ec_cart_billing_state' ) ).val( );
	var billing_country = jQuery( document.getElementById( 'ec_cart_billing_country' ) ).val( );
	if( billing_country != '0' && jQuery( document.getElementById( 'ec_cart_billing_state_' + billing_country ) ).length ){
		billing_state = jQuery( document.getElementById( 'ec_cart_billing_state_' + billing_country ) ).val( );
	}
	
	var shipping_state = jQuery( document.getElementById( 'ec_cart_' + address_type + '_state' ) ).val( );
	var shipping_country = jQuery( document.getElementById( 'ec_cart_' + address_type + '_country' ) ).val( );
	if( shipping_country != '0' && jQuery( document.getElementById( 'ec_cart_' + address_type + '_state_' + shipping_country ) ).length ){
		shipping_state = jQuery( document.getElementById( 'ec_cart_' + address_type + '_state_' + shipping_country ) ).val( );
	}
	
	var vat_registration_number = '';
	if( jQuery( document.getElementById( 'ec_cart_billing_vat_registration_number' ) ).length ){
		vat_registration_number = jQuery( document.getElementById( 'ec_cart_billing_vat_registration_number' ) ).val( );
	}
	
	var data = {
		action: 'ec_ajax_update_subscription_tax',
		product_id: product_id,
		shipping_selector: shipping_selector,
		
		vat_registration_number: vat_registration_number,
		
		billing_country: billing_country,
		billing_address: jQuery( document.getElementById( 'ec_cart_billing_address' ) ).val( ),
		billing_address2: jQuery( document.getElementById( 'ec_cart_billing_address2' ) ).val( ),
		billing_city: jQuery( document.getElementById( 'ec_cart_billing_city' ) ).val( ),
		billing_state: billing_state,
		billing_zip: jQuery( document.getElementById( 'ec_cart_billing_zip' ) ).val( ),
		
		shipping_country: jQuery( document.getElementById( 'ec_cart_' + address_type + '_country' ) ).val( ),
		shipping_address: jQuery( document.getElementById( 'ec_cart_' + address_type + '_address' ) ).val( ),
		shipping_address2: jQuery( document.getElementById( 'ec_cart_' + address_type + '_address2' ) ).val( ),
		shipping_city: jQuery( document.getElementById( 'ec_cart_' + address_type + '_city' ) ).val( ),
		shipping_state: shipping_state,
		shipping_zip: jQuery( document.getElementById( 'ec_cart_' + address_type + '_zip' ) ).val( )
	};
	
	jQuery.ajax( {
		url: wpeasycart_ajax_object.ajax_url, 
		type: 'post', 
		data: data, 
		success: function( data ){ 
			data_arr = JSON.parse( data );
			
			jQuery( document.getElementById( 'ec_cart_discount' ) ).html( data_arr['discount_total'] );
			jQuery( document.getElementById( 'ec_cart_discount_mobile' ) ).html( data_arr['discount_total'] );
			if( jQuery( document.getElementById( 'ec_cart_tax' ) ).length ){
				jQuery( document.getElementById( 'ec_cart_tax' ) ).html( data_arr['tax_total'] );
				jQuery( document.getElementById( 'ec_cart_tax_mobile' ) ).html( data_arr['tax_total'] );
			}
			if( jQuery( document.getElementById( 'ec_cart_vat' ) ).length ){
				jQuery( document.getElementById( 'ec_cart_vat' ) ).html( data_arr['vat_total'] );
				jQuery( document.getElementById( 'ec_cart_vat_mobile' ) ).html( data_arr['vat_total'] );
			}
			if( jQuery( document.getElementById( 'ec_cart_vat_rate' ) ).length ){
				jQuery( document.getElementById( 'ec_cart_vat_rate' ) ).html( data_arr['vat_rate_formatted'] );
			}
			
			if( jQuery( document.getElementById( 'ec_cart_hst' ) ).length ){
				jQuery( document.getElementById( 'ec_cart_hst' ) ).html( data_arr['hst_total'] );
				jQuery( document.getElementById( 'ec_cart_hst_mobile' ) ).html( data_arr['hst_total'] );
				jQuery( document.getElementById( 'ec_cart_hst_rate' ) ).html( data_arr['hst_rate'] );
				jQuery( document.getElementById( 'ec_cart_hst_rate_mobile' ) ).html( data_arr['hst_rate'] );
				if( data_arr['hst_rate'] > 0 ){
					jQuery( document.getElementById( 'ec_cart_hst_row' ) ).show( );
					jQuery( document.getElementById( 'ec_cart_hst_row_mobile' ) ).show( );
				}else{
					jQuery( document.getElementById( 'ec_cart_hst_row' ) ).hide( );
					jQuery( document.getElementById( 'ec_cart_hst_row_mobile' ) ).hide( );
				}
			}
			
			if( jQuery( document.getElementById( 'ec_cart_pst' ) ).length ){
				jQuery( document.getElementById( 'ec_cart_pst' ) ).html( data_arr['pst_total'] );
				jQuery( document.getElementById( 'ec_cart_pst_mobile' ) ).html( data_arr['pst_total'] );
				jQuery( document.getElementById( 'ec_cart_pst_rate' ) ).html( data_arr['pst_rate'] );
				jQuery( document.getElementById( 'ec_cart_pst_rate_mobile' ) ).html( data_arr['pst_rate'] );
				if( data_arr['pst_rate'] > 0 ){
					jQuery( document.getElementById( 'ec_cart_pst_row' ) ).show( );
					jQuery( document.getElementById( 'ec_cart_pst_row_mobile' ) ).show( );
				}else{
					jQuery( document.getElementById( 'ec_cart_pst_row' ) ).hide( );
					jQuery( document.getElementById( 'ec_cart_pst_row_mobile' ) ).hide( );
				}
			}
			
			if( jQuery( document.getElementById( 'ec_cart_gst' ) ).length ){
				jQuery( document.getElementById( 'ec_cart_gst' ) ).html( data_arr['gst_total'] );
				jQuery( document.getElementById( 'ec_cart_gst_mobile' ) ).html( data_arr['gst_total'] );
				jQuery( document.getElementById( 'ec_cart_gst_rate' ) ).html( data_arr['gst_rate'] );
				jQuery( document.getElementById( 'ec_cart_gst_rate_mobile' ) ).html( data_arr['gst_rate'] );
				if( data_arr['gst_rate'] > 0 ){
					jQuery( document.getElementById( 'ec_cart_gst_row' ) ).show( );
					jQuery( document.getElementById( 'ec_cart_gst_row_mobile' ) ).show( );
				}else{
					jQuery( document.getElementById( 'ec_cart_gst_row' ) ).hide( );
					jQuery( document.getElementById( 'ec_cart_gst_row_mobile' ) ).hide( );
				}
			}
			jQuery( document.getElementById( 'ec_cart_total' ) ).html( data_arr['grand_total'] );
			jQuery( document.getElementById( 'ec_cart_total_mobile' ) ).html( data_arr['grand_total'] );
			
			if( Number( data_arr['has_tax'] ) == 1 ){ 
				jQuery( '#ec_cart_tax_row' ).show( ); 
				jQuery( '#ec_cart_tax_row_mobile' ).show( ); 
			}else{ 
				jQuery( '#ec_cart_tax_row' ).hide( );
				jQuery( '#ec_cart_tax_row_mobile' ).hide( );
			}
			if( Number( data_arr['has_vat'] ) == 1 ){
				jQuery( '#ec_cart_vat_row' ).show( );
				jQuery( '#ec_cart_vat_row_mobile' ).show( );
			}else{
				jQuery( '#ec_cart_vat_row' ).hide( );
				jQuery( '#ec_cart_vat_row_mobile' ).hide( );
			}
		} 
	} );
}

function ec_apply_subscription_coupon( product_id, manufacturer_id ){
	
	jQuery( document.getElementById( 'ec_apply_coupon' ) ).hide( );
	jQuery( document.getElementById( 'ec_applying_coupon' ) ).show( );
	
	var data = {
		action: 'ec_ajax_redeem_subscription_coupon_code',
		product_id: product_id,
		manufacturer_id: manufacturer_id,
		couponcode: jQuery( document.getElementById( 'ec_coupon_code' ) ).val( )
	};
	
	jQuery.ajax( {
		url: wpeasycart_ajax_object.ajax_url, 
		type: 'post', 
		data: data, 
		success: function( data ){ 
			jQuery( document.getElementById( 'ec_apply_coupon' ) ).show( );
			jQuery( document.getElementById( 'ec_applying_coupon' ) ).hide( );
			
			data_arr = JSON.parse( data );
			
			if( data_arr['coupon_status'] == "valid" ){
				jQuery( document.getElementById( 'ec_coupon_error' ) ).hide( );
				jQuery( document.getElementById( 'ec_coupon_success' ) ).html( data_arr['coupon_message'] ).show( );
			}else{
				jQuery( document.getElementById( 'ec_coupon_success' ) ).hide( );
				jQuery( document.getElementById( 'ec_coupon_error' ) ).html( data_arr['coupon_message'] ).show( );
			}
			
			jQuery( document.getElementById( 'ec_cart_discount' ) ).html( data_arr['discount_total'] );
			jQuery( document.getElementById( 'ec_cart_discount_mobile' ) ).html( data_arr['discount_total'] );
			if( data_arr['has_discount'] ){
				jQuery( '.ec_no_discount' ).show( );
			}
			if( jQuery( document.getElementById( 'ec_cart_tax' ) ).length ){
				jQuery( document.getElementById( 'ec_cart_tax' ) ).html( data_arr['tax_total'] );
				jQuery( document.getElementById( 'ec_cart_tax_mobile' ) ).html( data_arr['tax_total'] );
			}
			if( jQuery( document.getElementById( 'ec_cart_vat' ) ).length ){
				jQuery( document.getElementById( 'ec_cart_vat' ) ).html( data_arr['vat_total'] );
				jQuery( document.getElementById( 'ec_cart_vat_mobile' ) ).html( data_arr['vat_total'] );
			}
			
			if( jQuery( document.getElementById( 'ec_cart_hst' ) ).length ){
				jQuery( document.getElementById( 'ec_cart_hst' ) ).html( data_arr['hst_total'] );
				jQuery( document.getElementById( 'ec_cart_hst_mobile' ) ).html( data_arr['hst_total'] );
				jQuery( document.getElementById( 'ec_cart_hst_rate' ) ).html( data_arr['hst_rate'] );
				jQuery( document.getElementById( 'ec_cart_hst_rate_mobile' ) ).html( data_arr['hst_rate'] );
				if( data_arr['hst_rate'] > 0 ){
					jQuery( document.getElementById( 'ec_cart_hst_row' ) ).show( );
					jQuery( document.getElementById( 'ec_cart_hst_row_mobile' ) ).show( );
				}else{
					jQuery( document.getElementById( 'ec_cart_hst_row' ) ).hide( );
					jQuery( document.getElementById( 'ec_cart_hst_row_mobile' ) ).hide( );
				}
			}
			
			if( jQuery( document.getElementById( 'ec_cart_pst' ) ).length ){
				jQuery( document.getElementById( 'ec_cart_pst' ) ).html( data_arr['pst_total'] );
				jQuery( document.getElementById( 'ec_cart_pst_mobile' ) ).html( data_arr['pst_total'] );
				jQuery( document.getElementById( 'ec_cart_pst_rate' ) ).html( data_arr['pst_rate'] );
				jQuery( document.getElementById( 'ec_cart_pst_rate_mobile' ) ).html( data_arr['pst_rate'] );
				if( data_arr['pst_rate'] > 0 ){
					jQuery( document.getElementById( 'ec_cart_pst_row' ) ).show( );
					jQuery( document.getElementById( 'ec_cart_pst_row_mobile' ) ).show( );
				}else{
					jQuery( document.getElementById( 'ec_cart_pst_row' ) ).hide( );
					jQuery( document.getElementById( 'ec_cart_pst_row_mobile' ) ).hide( );
				}
			}
			
			if( jQuery( document.getElementById( 'ec_cart_gst' ) ).length ){
				jQuery( document.getElementById( 'ec_cart_gst' ) ).html( data_arr['gst_total'] );
				jQuery( document.getElementById( 'ec_cart_gst_mobile' ) ).html( data_arr['gst_total'] );
				jQuery( document.getElementById( 'ec_cart_gst_rate' ) ).html( data_arr['gst_rate'] );
				jQuery( document.getElementById( 'ec_cart_gst_rate_mobile' ) ).html( data_arr['gst_rate'] );
				if( data_arr['gst_rate'] > 0 ){
					jQuery( document.getElementById( 'ec_cart_gst_row' ) ).show( );
					jQuery( document.getElementById( 'ec_cart_gst_row_mobile' ) ).show( );
				}else{
					jQuery( document.getElementById( 'ec_cart_gst_row' ) ).hide( );
					jQuery( document.getElementById( 'ec_cart_gst_row_mobile' ) ).hide( );
				}
			}
			jQuery( document.getElementById( 'ec_cart_total' ) ).html( data_arr['grand_total'] );
			jQuery( document.getElementById( 'ec_cart_total_mobile' ) ).html( data_arr['grand_total'] );
			
			if( Number( data_arr['has_tax'] ) == 1 ){ 
				jQuery( '#ec_cart_tax_row' ).show( ); 
				jQuery( '#ec_cart_tax_row_mobile' ).show( ); 
			}else{ 
				jQuery( '#ec_cart_tax_row' ).hide( );
				jQuery( '#ec_cart_tax_row_mobile' ).hide( );
			}
			if( Number( data_arr['has_vat'] ) == 1 ){
				jQuery( '#ec_cart_vat_row' ).show( );
				jQuery( '#ec_cart_vat_row_mobile' ).show( );
			}else{
				jQuery( '#ec_cart_vat_row' ).hide( );
				jQuery( '#ec_cart_vat_row_mobile' ).hide( );
			}
		} 
	} );
}

function ec_apply_gift_card( ){
	
	jQuery( document.getElementById( 'ec_apply_gift_card' ) ).hide( );
	jQuery( document.getElementById( 'ec_applying_gift_card' ) ).show( );
	
	var data = {
		action: 'ec_ajax_redeem_gift_card',
		ec_v3_24: 'true',
		giftcard: jQuery( document.getElementById( 'ec_gift_card' ) ).val( )
	};
	
	jQuery.ajax( {
		url: wpeasycart_ajax_object.ajax_url, 
		type: 'post', 
		data: data, 
		success: function( data ){ 
			jQuery( document.getElementById( 'ec_apply_gift_card' ) ).show( );
			jQuery( document.getElementById( 'ec_applying_gift_card' ) ).hide( );
			
			// Get Response Data
			var response_obj = JSON.parse( data );
			
			// Update Cart
			ec_update_cart( response_obj );
			
			// Update Gift Card Info
			if( response_obj.is_giftcard_valid ){
				jQuery( document.getElementById( 'ec_gift_card_error' ) ).hide( );
				jQuery( document.getElementById( 'ec_gift_card_success' ) ).html( response_obj.giftcard_message ).show( );
			}else{
				jQuery( document.getElementById( 'ec_gift_card_success' ) ).hide( );
				jQuery( document.getElementById( 'ec_gift_card_error' ) ).html( response_obj.giftcard_message ).show( );
			}
		} 
	} );
}

function ec_estimate_shipping( ){
	
	jQuery( document.getElementById( 'ec_estimate_shipping' ) ).hide( );
	jQuery( document.getElementById( 'ec_estimating_shipping' ) ).show( );
	
	var data = {
		action: 'ec_ajax_estimate_shipping',
		ec_v3_24: 'true',
		zipcode: jQuery( document.getElementById( 'ec_estimate_zip' ) ).val( ),
		country: jQuery( document.getElementById( 'ec_estimate_country' ) ).val( )
	};
	
	jQuery.ajax({
		url: wpeasycart_ajax_object.ajax_url, 
		type: 'post', 
		data: data, 
		success: function( data ){ 
			jQuery( document.getElementById( 'ec_estimate_shipping' ) ).show( );
			jQuery( document.getElementById( 'ec_estimating_shipping' ) ).hide( );
			
			// Get Response Data
			var response_obj = JSON.parse( data );
			
			// Update Cart
			ec_update_cart( response_obj );
			
			// Show the Shipping Row if Hidden
			jQuery( document.getElementById( 'ec_cart_shipping_row' ) ).show( );
		} 
	} );
}

function ec_update_cart( response_obj ){
	
	if( response_obj.cart.length == 0 ){
		ec_reload_cart( );
		
	}else{
		// Update Cart Data
		for( var i=0; i<response_obj.cart.length; i++ ){
			jQuery( document.getElementById( 'ec_quantity_' + response_obj.cart[i].id ) ).val( response_obj.cart[i].quantity );
			jQuery( document.getElementById( 'ec_cartitem_price_' + response_obj.cart[i].id ) ).html( response_obj.cart[i].unit_price );
			jQuery( document.getElementById( 'ec_cartitem_total_' + response_obj.cart[i].id ) ).html( response_obj.cart[i].total_price );
			
			if( response_obj.cart[i].allow_backorders == "1" && response_obj.cart[i].use_optionitem_quantity_tracking == "1" && Number( response_obj.cart[i].quantity ) > Number( response_obj.cart[i].optionitem_stock_quantity ) ){
				jQuery( document.getElementById( 'ec_cartitem_backorder_' + response_obj.cart[i].id ) ).show( );
			
			}else if( response_obj.cart[i].allow_backorders == "1" && response_obj.cart[i].use_optionitem_quantity_tracking == "1" && Number( response_obj.cart[i].quantity ) <= Number( response_obj.cart[i].optionitem_stock_quantity ) ){
				jQuery( document.getElementById( 'ec_cartitem_backorder_' + response_obj.cart[i].id ) ).hide( );
			
			}else if( response_obj.cart[i].allow_backorders == "1" && response_obj.cart[i].use_optionitem_quantity_tracking == "0" && Number( response_obj.cart[i].quantity ) > Number( response_obj.cart[i].stock_quantity ) ){
				jQuery( document.getElementById( 'ec_cartitem_backorder_' + response_obj.cart[i].id ) ).show( );
			
			}else if( response_obj.cart[i].allow_backorders == "1" && response_obj.cart[i].use_optionitem_quantity_tracking == "0" && Number( response_obj.cart[i].quantity ) <= Number( response_obj.cart[i].stock_quantity ) ){
				jQuery( document.getElementById( 'ec_cartitem_backorder_' + response_obj.cart[i].id ) ).hide( );
			
			}
		}
		
		// Update Cart Totals
		jQuery( document.getElementById( 'ec_cart_subtotal' ) ).html( response_obj.order_totals.sub_total );
		jQuery( document.getElementById( 'ec_cart_tax' ) ).html( response_obj.order_totals.tax_total );
		if( response_obj.order_totals.has_tax == '1' ){
			jQuery( document.getElementById( 'ec_cart_tax' ) ).parent( ).show( );
		}else{
			jQuery( document.getElementById( 'ec_cart_tax' ) ).parent( ).hide( );
		}
		jQuery( document.getElementById( 'ec_cart_shipping' ) ).html( response_obj.order_totals.shipping_total );
		jQuery( document.getElementById( 'ec_cart_duty' ) ).html( response_obj.order_totals.duty_total );
		if( response_obj.order_totals.has_duty == '1' ){
			jQuery( document.getElementById( 'ec_cart_duty' ) ).parent( ).show( );
		}else{
			jQuery( document.getElementById( 'ec_cart_duty' ) ).parent( ).hide( );
		}
		jQuery( document.getElementById( 'ec_cart_vat' ) ).html( response_obj.order_totals.vat_total );
		if( response_obj.order_totals.has_vat == '1' ){
			jQuery( document.getElementById( 'ec_cart_vat' ) ).parent( ).show( );
		}else{
			jQuery( document.getElementById( 'ec_cart_vat' ) ).parent( ).hide( );
		}
        if( jQuery( document.getElementById( 'ec_cart_vat_rate' ) ).length ){
            jQuery( document.getElementById( 'ec_cart_vat_rate' ) ).html( response_obj.order_totals.vat_rate_formatted );
        }
		jQuery( document.getElementById( 'ec_cart_gst' ) ).html( response_obj.order_totals.gst_total );
		if( response_obj.order_totals.has_gst == '1' ){
			jQuery( document.getElementById( 'ec_cart_gst' ) ).parent( ).show( );
		}else{
			jQuery( document.getElementById( 'ec_cart_gst' ) ).parent( ).hide( );
		}
		jQuery( document.getElementById( 'ec_cart_hst' ) ).html( response_obj.order_totals.hst_total );
		if( response_obj.order_totals.has_hst == '1' ){
			jQuery( document.getElementById( 'ec_cart_hst' ) ).parent( ).show( );
		}else{
			jQuery( document.getElementById( 'ec_cart_hst' ) ).parent( ).hide( );
		}
		jQuery( document.getElementById( 'ec_cart_pst' ) ).html( response_obj.order_totals.pst_total );
		if( response_obj.order_totals.has_pst == '1' ){
			jQuery( document.getElementById( 'ec_cart_pst' ) ).parent( ).show( );
		}else{
			jQuery( document.getElementById( 'ec_cart_pst' ) ).parent( ).hide( );
		}
        if( jQuery( document.getElementById( 'ec_cart_tip' ) ).length ){
			jQuery( document.getElementById( 'ec_cart_tip' ) ).html( response_obj.order_totals.tip_total );
		}
		jQuery( document.getElementById( 'ec_cart_discount' ) ).html( response_obj.order_totals.discount_total );
		jQuery( document.getElementById( 'ec_cart_total' ) ).html( response_obj.order_totals.grand_total );
		
		jQuery( ".ec_cart_items_total" ).html( response_obj.items_total );
		jQuery( ".ec_cart_price_total" ).html( response_obj.order_totals.grand_total );
		
		// Hide/Show Discount
		if( response_obj.has_discount == '1' ){
			jQuery( '.ec_no_discount' ).show( );
			jQuery( '.ec_has_discount' ).show( );
		}else{
			jQuery( '.ec_no_discount' ).hide( );
			jQuery( '.ec_has_discount' ).hide( );
		}
		
		// Hide/Show Backorder
		if( response_obj.has_backorder ){
			jQuery( document.getElementById( 'ec_cart_backorder_message' ) ).show( );
		}else{
			jQuery( document.getElementById( 'ec_cart_backorder_message' ) ).hide( );
		}
        
        // Hide/Show Minimum
        if( jQuery( '.ec_minimum_purchase_box' ).length ){
            if( Number( response_obj.order_totals.sub_total_amt ) < Number( jQuery( '.ec_minimum_purchase_box' ).attr( 'data-min-cart' ) ) ){
                jQuery( '.ec_minimum_purchase_box' ).show( );
                if( jQuery( document.getElementById( 'sq-walletbox' ) ).length ){
                    jQuery( document.getElementById( 'sq-walletbox' ) ).hide( );
                }
            }else{
                jQuery( '.ec_minimum_purchase_box' ).hide( );
                if( jQuery( document.getElementById( 'sq-walletbox' ) ).length ){
                    jQuery( document.getElementById( 'sq-walletbox' ) ).show( );
                }
            }
        }
        
        // Stripe Update Wallet
        if( jQuery( document.getElementById( 'ec-stripe-wallet-button' ) ).length ){
            jQuery( document.getElementById( 'ec-stripe-wallet-button' ) ).replaceWith( response_obj.stripe_wallet );
        }
	
		// PayPal Express Update
		if( response_obj.paypal_express_button ){
			jQuery( document.getElementById( 'paypal-button-container' ) ).find( '.paypal-button' ).remove( );
			jQuery( document.getElementById( 'paypal-button-container' ) ).append( response_obj.paypal_express_button );
		}
	}
}

function ec_reload_cart( ){
	location.reload( );
}

function ec_open_login_click( ){
	jQuery( document.getElementById( 'ec_alt_login' ) ).slideToggle(300);
	
	return false;
}

function ec_update_shipping_view( ){
	if( jQuery( document.getElementById( 'ec_shipping_selector' ) ).is( ':checked' ) ){
		jQuery( document.getElementById( 'ec_shipping_form' ) ).show( );
	}else{
		jQuery( document.getElementById( 'ec_shipping_form' ) ).hide( );
	}
}

function ec_cart_toggle_login( ){
	if( jQuery( document.getElementById( 'ec_login_selector' ) ).is( ':checked' ) ){
		jQuery( document.getElementById( 'ec_user_login_form' ) ).show( );
	}else{
		jQuery( document.getElementById( 'ec_user_login_form' ) ).hide( );
	}
}

function ec_toggle_create_account( ){
	if( jQuery( document.getElementById( 'ec_user_create_form' ) ).is( ':visible' ) ){
		jQuery( document.getElementById( 'ec_user_create_form' ) ).hide( );
	}else{
		jQuery( document.getElementById( 'ec_user_create_form' ) ).show( );
	}
}

function ec_update_payment_display( ){
	
	var payment_method = "manual_bill";
	
	jQuery( document.getElementById( 'ec_apple_pay_form' ) ).hide( );
	jQuery( document.getElementById( 'ec_manual_payment_form' ) ).hide( );
	jQuery( document.getElementById( 'ec_affirm_form' ) ).hide( );
	jQuery( document.getElementById( 'ec_third_party_form' ) ).hide( );
	jQuery( document.getElementById( 'ec_credit_card_form' ) ).hide( );
	jQuery( document.getElementById( 'ec_ideal_form' ) ).hide( );
	
	if( jQuery( document.getElementById( 'ec_payment_apple' ) ).is( ':checked' ) ){
		jQuery( document.getElementById( 'ec_apple_pay_form' ) ).show( );
		
		if( jQuery( document.getElementById( 'ec_terms_row' ) ).length )
			jQuery( document.getElementById( 'ec_terms_row' ) ).show( );
		
		if( jQuery( document.getElementById( 'ec_terms_agreement_row' ) ).length )
			jQuery( document.getElementById( 'ec_terms_agreement_row' ) ).show( );
		
		if( jQuery( document.getElementById( 'wpeasycart_submit_order_row' ) ).length )
			jQuery( document.getElementById( 'wpeasycart_submit_order_row' ) ).show( );
			
		if( jQuery( document.getElementById( 'wpeasycart_submit_paypal_order_row' ) ).length )
			jQuery( document.getElementById( 'wpeasycart_submit_paypal_order_row' ) ).hide( );
		
		
		payment_method = "apple_pay";
	
	}else if( jQuery( document.getElementById( 'ec_payment_manual' ) ).is( ':checked' ) ){
		jQuery( document.getElementById( 'ec_manual_payment_form' ) ).show( );
		
		if( jQuery( document.getElementById( 'ec_terms_row' ) ).length )
			jQuery( document.getElementById( 'ec_terms_row' ) ).show( );
		
		if( jQuery( document.getElementById( 'ec_terms_agreement_row' ) ).length )
			jQuery( document.getElementById( 'ec_terms_agreement_row' ) ).show( );
		
		if( jQuery( document.getElementById( 'wpeasycart_submit_order_row' ) ).length )
			jQuery( document.getElementById( 'wpeasycart_submit_order_row' ) ).show( );
			
		if( jQuery( document.getElementById( 'wpeasycart_submit_paypal_order_row' ) ).length )
			jQuery( document.getElementById( 'wpeasycart_submit_paypal_order_row' ) ).hide( );
		
		
		payment_method = "manual_bill";
	
	}else if( jQuery( document.getElementById( 'ec_payment_affirm' ) ).is( ':checked' ) ){
		jQuery( document.getElementById( 'ec_affirm_form' ) ).show( );
		
		if( jQuery( document.getElementById( 'ec_terms_row' ) ).length )
			jQuery( document.getElementById( 'ec_terms_row' ) ).show( );
		
		if( jQuery( document.getElementById( 'ec_terms_agreement_row' ) ).length )
			jQuery( document.getElementById( 'ec_terms_agreement_row' ) ).show( );
		
		if( jQuery( document.getElementById( 'wpeasycart_submit_order_row' ) ).length )
			jQuery( document.getElementById( 'wpeasycart_submit_order_row' ) ).show( );
			
		if( jQuery( document.getElementById( 'wpeasycart_submit_paypal_order_row' ) ).length )
			jQuery( document.getElementById( 'wpeasycart_submit_paypal_order_row' ) ).hide( );
			
		payment_method = "affirm";
	
	}else if( jQuery( document.getElementById( 'ec_payment_third_party' ) ).is( ':checked' ) ){
		jQuery( document.getElementById( 'ec_third_party_form' ) ).show( );
		
		if( jQuery( document.getElementById( 'wpeasycart_submit_paypal_order_row' ) ).length ){
			
			jQuery( document.getElementById( 'wpeasycart_submit_paypal_order_row' ) ).show( );
			
			if( jQuery( document.getElementById( 'ec_terms_row' ) ).length )
				jQuery( document.getElementById( 'ec_terms_row' ) ).hide( );
			
			if( jQuery( document.getElementById( 'ec_terms_error' ) ).length )
				jQuery( document.getElementById( 'ec_terms_error' ) ).hide( );
			
			if( jQuery( document.getElementById( 'ec_submit_order_error' ) ).length )
				jQuery( document.getElementById( 'ec_submit_order_error' ) ).hide( );
			
			if( jQuery( document.getElementById( 'ec_terms_agreement_row' ) ).length )
				jQuery( document.getElementById( 'ec_terms_agreement_row' ) ).hide( );
			
			if( jQuery( document.getElementById( 'wpeasycart_submit_order_row' ) ).length )
				jQuery( document.getElementById( 'wpeasycart_submit_order_row' ) ).hide( );
		}
		
		payment_method = "third_party";
	
	}else if( jQuery( document.getElementById( 'ec_payment_credit_card' ) ).is( ':checked' ) ){
		jQuery( document.getElementById( 'ec_credit_card_form' ) ).show( );
		
		if( jQuery( document.getElementById( 'ec_terms_row' ) ).length )
			jQuery( document.getElementById( 'ec_terms_row' ) ).show( );
		
		if( jQuery( document.getElementById( 'ec_terms_agreement_row' ) ).length )
			jQuery( document.getElementById( 'ec_terms_agreement_row' ) ).show( );
		
		if( jQuery( document.getElementById( 'wpeasycart_submit_order_row' ) ).length )
			jQuery( document.getElementById( 'wpeasycart_submit_order_row' ) ).show( );
			
		if( jQuery( document.getElementById( 'wpeasycart_submit_paypal_order_row' ) ).length )
			jQuery( document.getElementById( 'wpeasycart_submit_paypal_order_row' ) ).hide( );
			
		payment_method = "credit_card";
	
	}else if( jQuery( document.getElementById( 'ec_payment_ideal' ) ).is( ':checked' ) ){
		jQuery( document.getElementById( 'ec_ideal_form' ) ).show( );
		
		if( jQuery( document.getElementById( 'ec_terms_row' ) ).length )
			jQuery( document.getElementById( 'ec_terms_row' ) ).show( );
		
		if( jQuery( document.getElementById( 'ec_terms_agreement_row' ) ).length )
			jQuery( document.getElementById( 'ec_terms_agreement_row' ) ).show( );
		
		if( jQuery( document.getElementById( 'wpeasycart_submit_order_row' ) ).length )
			jQuery( document.getElementById( 'wpeasycart_submit_order_row' ) ).show( );
			
		if( jQuery( document.getElementById( 'wpeasycart_submit_paypal_order_row' ) ).length )
			jQuery( document.getElementById( 'wpeasycart_submit_paypal_order_row' ) ).hide( );
			
		payment_method = "ideal";
	}
	
	var data = {
		action: 'ec_ajax_update_payment_method',
		payment_method: payment_method
	};
	
	jQuery.ajax({
		url: wpeasycart_ajax_object.ajax_url, 
		type: 'post', 
		data: data, 
		success: function( data ){ }
	} );
	
}

function ec_show_cc_type( type ){
	
	if( jQuery( document.getElementById( 'ec_card_visa' ) ) ){
		if( type == "visa" || type == "all" ){
			jQuery( document.getElementById( 'ec_card_visa' ) ).show( );
			jQuery( document.getElementById( 'ec_card_visa_inactive' ) ).hide( );
		}else{
			jQuery( document.getElementById( 'ec_card_visa' ) ).hide( );
			jQuery( document.getElementById( 'ec_card_visa_inactive' ) ).show( );
		}
	}
	
	if( jQuery( document.getElementById( 'ec_card_discover' ) ) ){
		if( type == "discover" || type == "all" ){
			jQuery( document.getElementById( 'ec_card_discover' ) ).show( );
			jQuery( document.getElementById( 'ec_card_discover_inactive' ) ).hide( );
		}else{
			jQuery( document.getElementById( 'ec_card_discover' ) ).hide( );
			jQuery( document.getElementById( 'ec_card_discover_inactive' ) ).show( );
		}
	}
	
	if( jQuery( document.getElementById( 'ec_card_mastercard' ) ) ){
		if( type == "mastercard" || type == "all" ){
			jQuery( document.getElementById( 'ec_card_mastercard' ) ).show( );
			jQuery( document.getElementById( 'ec_card_mastercard_inactive' ) ).hide( );
		}else{
			jQuery( document.getElementById( 'ec_card_mastercard' ) ).hide( );
			jQuery( document.getElementById( 'ec_card_mastercard_inactive' ) ).show( );
		}
	}
	
	if( jQuery( document.getElementById( 'ec_card_amex' ) ) ){
		if( type == "amex" || type == "all" ){
			jQuery( document.getElementById( 'ec_card_amex' ) ).show( );
			jQuery( document.getElementById( 'ec_card_amex_inactive' ) ).hide( );
		}else{
			jQuery( document.getElementById( 'ec_card_amex' ) ).hide( );
			jQuery( document.getElementById( 'ec_card_amex_inactive' ) ).show( );
		}
	}
	
	if( jQuery( document.getElementById( 'ec_card_jcb' ) ) ){
		if( type == "jcb" || type == "all" ){
			jQuery( document.getElementById( 'ec_card_jcb' ) ).show( );
			jQuery( document.getElementById( 'ec_card_jcb_inactive' ) ).hide( );
		}else{
			jQuery( document.getElementById( 'ec_card_jcb' ) ).hide( );
			jQuery( document.getElementById( 'ec_card_jcb_inactive' ) ).show( );
		}
	}
	
	if( jQuery( document.getElementById( 'ec_card_diners' ) ) ){
		if( type == "diners" || type == "all" ){
			jQuery( document.getElementById( 'ec_card_diners' ) ).show( );
			jQuery( document.getElementById( 'ec_card_diners_inactive' ) ).hide( );
		}else{
			jQuery( document.getElementById( 'ec_card_diners' ) ).hide( );
			jQuery( document.getElementById( 'ec_card_diners_inactive' ) ).show( );
		}
	}
	
	if( jQuery( document.getElementById( 'ec_card_laser' ) ) ){
		if( type == "laser" || type == "all" ){
			jQuery( document.getElementById( 'ec_card_laser' ) ).show( );
			jQuery( document.getElementById( 'ec_card_laser_inactive' ) ).hide( );
		}else{
			jQuery( document.getElementById( 'ec_card_laser' ) ).hide( );
			jQuery( document.getElementById( 'ec_card_laser_inactive' ) ).show( );
		}
	}
	
	if( jQuery( document.getElementById( 'ec_card_maestro' ) ) ){
		if( type == "maestro" || type == "all" ){
			jQuery( document.getElementById( 'ec_card_maestro' ) ).show( );
			jQuery( document.getElementById( 'ec_card_maestro_inactive' ) ).hide( );
		}else{
			jQuery( document.getElementById( 'ec_card_maestro' ) ).hide( );
			jQuery( document.getElementById( 'ec_card_maestro_inactive' ) ).show( );
		}
	}
	
}

function wpeasycart_bluecheck_verify( ){
	try {
		
        BlueCheckService.showModal();
        return false;
		
    } catch(e) {        
        console.error('[BlueCheckService::customValidation]', e);
        BlueCheckService.BcWebLogger('Error: ' + (e.message || e));
        return false;
    }
}

function ec_validate_cart_details( ){
	
	var login_complete = true;
	var billing_complete = ec_validate_address_block( 'ec_cart_billing' );
	var shipping_complete = true;
	var email_complete = true;
	var create_account_complete = true;
    var recaptcha_complete = true;
	var bluecheck_complete = true;
	
	if( jQuery( document.getElementById( 'ec_login_selector' ) ).is( ':checked' ) )
		login_complete = ec_validate_cart_login( );
	
	if( jQuery( document.getElementById( 'ec_shipping_selector' ) ).is( ':checked' ) )
		shipping_complete = ec_validate_address_block( 'ec_cart_shipping' );
	
	if( jQuery( document.getElementById( 'ec_contact_email' ) ).length )
		email_complete = ec_validate_email_block( 'ec_contact' );
	
	if( jQuery( document.getElementById( 'ec_create_account_selector' ) ).is( ':checked' ) || ( jQuery( document.getElementById( 'ec_create_account_selector' ) ).is(':hidden' ) && jQuery( document.getElementById( 'ec_create_account_selector' ) ).val( ) == "create_account" ) )
		create_account_complete = ec_validate_create_account( 'ec_contact' );
    
    if( document.getElementById( 'ec_account_register_recaptcha' ) ){
        var recaptcha_response = jQuery( document.getElementById( 'ec_grecaptcha_response_register' ) ).val( );
		if( !recaptcha_response.length ){
			jQuery( '#ec_account_register_recaptcha > div' ).css( 'border', '1px solid red' );
			recaptcha_complete = false;
		}else{
			jQuery( '#ec_account_register_recaptcha > div' ).css( 'border', 'none' );
		}
    }
		
	if( document.getElementById( 'bcvTrigger' ) ){
		bluecheck_complete = wpeasycart_bluecheck_verify( );
		if( !bluecheck_complete )
			return false;
	}
		
	if( login_complete && billing_complete && shipping_complete && email_complete && create_account_complete && recaptcha_complete ){
		ec_hide_error( 'ec_checkout' );
		ec_hide_error( 'ec_checkout2' );
		return true;
	}else{
		ec_show_error( 'ec_checkout' );
		ec_show_error( 'ec_checkout2' );
		return false;
	}
	
}

function ec_validate_paypal_express_submit_order( ){
	var terms_complete = ec_validate_terms( );
	if( terms_complete ){
		jQuery( document.getElementById( 'ec_cart_submit_order' ) ).hide( );
		jQuery( document.getElementById( 'ec_cart_submit_order_working' ) ).show( );
		ec_hide_error( 'ec_submit_order' );
		return true;
	}else{
		jQuery( document.getElementById( 'ec_cart_submit_order' ) ).show( );
		jQuery( document.getElementById( 'ec_cart_submit_order_working' ) ).hide( );
		ec_show_error( 'ec_submit_order' );
		return false;
	}
}

function ec_validate_submit_order( ){
	
	var payment_method_complete = ec_validate_payment_method( );
	var terms_complete = ec_validate_terms( );
	
	if( payment_method_complete && terms_complete ){
		if( !document.getElementById( 'ec_stripe_card_row' ) && !document.getElementById( 'wpec_braintree_dropin' ) ){
			jQuery( document.getElementById( 'ec_cart_submit_order' ) ).hide( );
			jQuery( document.getElementById( 'ec_cart_submit_order_working' ) ).show( );
			ec_hide_error( 'ec_submit_order' );
			if( jQuery( document.getElementById( 'ec_card_number' ) ).length )
				jQuery( document.getElementById( 'ec_card_number' ) ).val( jQuery( document.getElementById( 'ec_card_number' ) ).val( ).replace( /\s+/g, '' ) );
		}
		return true;
	}else{
		jQuery( document.getElementById( 'ec_cart_submit_order' ) ).show( );
		jQuery( document.getElementById( 'ec_cart_submit_order_working' ) ).hide( );
		ec_show_error( 'ec_submit_order' );
		return false;
	}
	
}

function ec_validate_submit_invoice( ){
	
	var payment_method_complete = ec_validate_payment_method( );
	var terms_complete = ec_validate_terms( );
	var billing_complete = ec_validate_address_block( 'ec_cart_billing' );
	
	if( payment_method_complete && terms_complete && billing_complete ){
		if( !document.getElementById( 'ec_stripe_card_row' ) && !document.getElementById( 'wpec_braintree_dropin' ) ){
			jQuery( document.getElementById( 'ec_cart_submit_order' ) ).hide( );
			jQuery( document.getElementById( 'ec_cart_submit_order_working' ) ).show( );
			ec_hide_error( 'ec_submit_order' );
			if( jQuery( document.getElementById( 'ec_card_number' ) ).length )
				jQuery( document.getElementById( 'ec_card_number' ) ).val( jQuery( document.getElementById( 'ec_card_number' ) ).val( ).replace( /\s+/g, '' ) );
		}
		return true;
	}else{
		jQuery( document.getElementById( 'ec_cart_submit_order' ) ).show( );
		jQuery( document.getElementById( 'ec_cart_submit_order_working' ) ).hide( );
		ec_show_error( 'ec_submit_order' );
		return false;
	}
	
}

function ec_validate_submit_subscription( ){
	
	var login_complete = true;
	var billing_complete = ec_validate_address_block( 'ec_cart_billing' );
	var shipping_complete = true;
	var email_complete = true;
	var create_account_complete = true;
	var payment_method_complete = ec_validate_payment_method( );
	var terms_complete = ec_validate_terms( );
	
	if( jQuery( document.getElementById( 'ec_login_selector' ) ).is( ':checked' ) )
		login_complete = ec_validate_cart_login( );
	
	if( jQuery( document.getElementById( 'ec_shipping_selector' ) ).is( ':checked' ) )
		shipping_complete = ec_validate_address_block( 'ec_cart_shipping' );
		
	if( jQuery( document.getElementById( 'ec_contact_email' ) ).length )
		email_complete = ec_validate_email_block( 'ec_contact' );
	
	if( jQuery( document.getElementById( 'ec_contact_password' ) ).length )
		create_account_complete = ec_validate_create_account( 'ec_contact' );
		
	if( login_complete && billing_complete && shipping_complete && email_complete && create_account_complete && payment_method_complete && terms_complete ){
		if( !document.getElementById( 'ec_stripe_card_row' ) && !document.getElementById( 'wpec_braintree_dropin' ) ){
			ec_hide_error( 'ec_checkout' );
			jQuery( document.getElementById( 'ec_cart_submit_order' ) ).hide( );
			jQuery( document.getElementById( 'ec_cart_submit_order_working' ) ).show( );
		}
		return true;
	}else{
		ec_show_error( 'ec_checkout' );
		return false;
	}
	
}

function ec_validate_cart_login( ){
	
	var errors = false;
	var email = jQuery( document.getElementById( 'ec_cart_login_email' ) ).val( ).trim( );
	var password = jQuery( document.getElementById( 'ec_cart_login_password' ) ).val( );
	
	if( !ec_validate_email( email ) ){
		errors = true;
		ec_show_error( 'ec_cart_login_email' );
	}else{
		ec_hide_error( 'ec_cart_login_email' );
	}
	
	if( !ec_validate_text( password ) ){
		errors = true;
		ec_show_error( 'ec_cart_login_password' );
	}else{
		ec_hide_error( 'ec_cart_login_password' );
	}
    
    if( jQuery( document.getElementById( 'ec_account_login_recaptcha' ) ).length ){
		var recaptcha_response = jQuery( document.getElementById( 'ec_grecaptcha_response_login' ) ).val( );
		if( !recaptcha_response.length ){
			jQuery( '#ec_account_login_recaptcha > div' ).css( 'border', '1px solid red' );
			errors = true;
		}else{
			jQuery( '#ec_account_login_recaptcha > div' ).css( 'border', 'none' );
		}
	}
	
	return ( !errors );
	
}

function ec_validate_address_block( prefix ){
	
	var errors = false;
	var country = jQuery( document.getElementById( '' + prefix + '_country' ) ).val( );
	var first_name = jQuery( document.getElementById( '' + prefix + '_first_name' ) ).val( );
	var last_name = jQuery( document.getElementById( '' + prefix + '_last_name' ) ).val( );
	var city = jQuery( document.getElementById( '' + prefix + '_city' ) ).val( );
	var address = jQuery( document.getElementById( '' + prefix + '_address' ) ).val( );
	if( jQuery( document.getElementById( '' + prefix + '_state_' + country ) ) )
		var state = jQuery( document.getElementById( '' + prefix + '_state_' + country ) ).val( );
	else
		var state = jQuery( document.getElementById( '' + prefix + '_state' ) ).val( );
	var zip = jQuery( document.getElementById( '' + prefix + '_zip' ) ).val( );
	var phone = jQuery( document.getElementById( '' + prefix + '_phone' ) ).val( );
	
	if( !ec_validate_select( country ) ){
		errors = true;
		ec_show_error( prefix + '_country' );
	}else{
		ec_hide_error( prefix + '_country' );
	}
	
	if( !ec_validate_text( first_name ) ){
		errors = true;
		ec_show_error( prefix + '_first_name' );
	}else{
		ec_hide_error( prefix + '_first_name' );
	}
	
	if( !ec_validate_text( last_name ) ){
		errors = true;
		ec_show_error( prefix + '_last_name' );
	}else{
		ec_hide_error( prefix + '_last_name' );
	}
	
	if( !ec_validate_text( city ) ){
		errors = true;
		ec_show_error( prefix + '_city' );
	}else{
		ec_hide_error( prefix + '_city' );
	}
	
	if( !ec_validate_text( address ) ){
		errors = true;
		ec_show_error( prefix + '_address' );
	}else{
		ec_hide_error( prefix + '_address' );
	}
	
	if( jQuery( document.getElementById( '' + prefix + '_state_' + country ) ).length ){
		if( !ec_validate_select( state ) ){
			errors = true;
			ec_show_error( prefix + '_state' );
		}else{
			ec_hide_error( prefix + '_state' );
		}
	}else{
		ec_hide_error( prefix + '_state' );
	}
	
	if( !ec_validate_zip_code( zip, country ) ){
		errors = true;
		ec_show_error( prefix + '_zip' );
	}else{
		ec_hide_error( prefix + '_zip' );
	}
	
	if( jQuery( document.getElementById( '' + prefix + '_phone_error' ) ).length ){
        if( jQuery( document.getElementById( '' + prefix + '_phone' ) ).length && !ec_validate_text( phone ) ){
            errors = true;
            ec_show_error( prefix + '_phone' );
        }else{
            ec_hide_error( prefix + '_phone' );
        }
    }
	
	return ( !errors );
	
}

function ec_validate_email_block( prefix ){
	
	var errors = false;
	var email = jQuery( document.getElementById( '' + prefix + '_email' ) ).val( );
	var retype_email = "";
	if( jQuery( document.getElementById( '' + prefix + '_email_retype' ) ).length )
		retype_email = jQuery( document.getElementById( '' + prefix + '_email_retype' ) ).val( );
	else
		retype_email = jQuery( document.getElementById( '' + prefix + '_retype_email' ) ).val( );
	
	if( !ec_validate_email( email ) ){
		errors = true;
		ec_show_error( prefix + '_email' );
	}else{
		ec_hide_error( prefix + '_email' );
	}
	
	if( !ec_validate_match( email, retype_email) ){
		errors = true;
		ec_show_error( prefix + '_email_retype' );
	}else{
		ec_hide_error( prefix + '_email_retype' );
	}
	
	return ( !errors );
	
}

function ec_validate_create_account( prefix ){
	
	var errors = false;
	var first_name = jQuery( document.getElementById( '' + prefix + '_first_name' ) ).val( );
	var last_name = jQuery( document.getElementById( '' + prefix + '_last_name' ) ).val( );
	var password = jQuery( document.getElementById( '' + prefix + '_password' ) ).val( );
	var retype_password = jQuery( document.getElementById( '' + prefix + '_password_retype' ) ).val( );
	
	if( jQuery( document.getElementById( '' + prefix + '_first_name' ) ).length && !ec_validate_text( first_name ) ){
		errors = true;
		ec_show_error( prefix + '_first_name' );
	}else{
		ec_hide_error( prefix + '_first_name' );
	}
	
	if( jQuery( document.getElementById( '' + prefix + '_last_name' ) ).length && !ec_validate_text( last_name ) ){
		errors = true;
		ec_show_error( prefix + '_last_name' );
	}else{
		ec_hide_error( prefix + '_last_name' );
	}
	
	if( !ec_validate_password( password ) ){
		errors = true;
		ec_show_error( prefix + '_password' );
	}else{
		ec_hide_error( prefix + '_password' );
	}
	
	if( !ec_validate_match( password, retype_password ) ){
		errors = true;
		ec_show_error( prefix + '_password_retype' );
	}else{
		ec_hide_error( prefix + '_password_retype' );
	}
	
	if( jQuery( document.getElementById( 'ec_terms_agree' ) ).length ){
		if( !ec_validate_terms( ) ){
			errors = true;
		}
	}
	
	return ( !errors );
	
}

function ec_validate_payment_method( ){
	
	var errors = false;
	var payment_method = "credit_card";
	if( jQuery( 'input:radio[name=ec_cart_payment_selection]:checked' ).length ){
		ec_hide_error( 'ec_payment_method' );
		payment_method = jQuery( 'input:radio[name=ec_cart_payment_selection]:checked' ).val( );
	}else if( jQuery( 'input:radio[name=ec_cart_payment_selection]' ).length ){
		ec_show_error( 'ec_payment_method' );
		return false;
	}else{ // free order or no payment methods
		ec_hide_error( 'ec_payment_method' );
		return true;
	}
	
	var card_holder_name = "-1";
	if( document.getElementById( 'ec_card_holder_name' ) ){
		card_holder_name = jQuery( document.getElementById( 'ec_card_holder_name' ) ).val( );
	}
	
	if( payment_method == "affirm" ){
		ec_checkout_with_affirm( );
		ec_hide_error( 'ec_submit_order' );
		return false;
		
	}else if( payment_method == "credit_card" && ( document.getElementById( 'ec_stripe_card_row' ) || document.getElementById( 'wpec_braintree_dropin' ) ) ){ 
		return true;
	
	}else if( payment_method == "credit_card" && document.getElementById( 'cardpointe_token' ) ){
		if( jQuery( document.getElementById( 'cardpointe_token' ) ).val( ) == '' ){
			return false;
		}else{
			return true;
		}
		
	}else if( payment_method == "credit_card" ){
		
		var cardType = jQuery.payment.cardType( jQuery( document.getElementById( 'ec_card_number' ) ).val( ) );
		
		if( card_holder_name != "-1" && card_holder_name == "" ){
			errors = true;
			ec_show_error( 'ec_card_holder_name' );
		}else{
			ec_hide_error( 'ec_card_holder_name' );
		}
		
		if( !jQuery.payment.validateCardNumber( jQuery( document.getElementById( 'ec_card_number' ) ).val( ) ) ){
			errors = true;
			ec_show_error( 'ec_card_number' );
		}else{
			ec_hide_error( 'ec_card_number' );
		}
		
		if( !jQuery.payment.validateCardExpiry( jQuery( document.getElementById( 'ec_cc_expiration' ) ).payment( 'cardExpiryVal' ) ) ){
			errors = true;
			ec_show_error( 'ec_expiration_date' );
		}else{
			ec_hide_error( 'ec_expiration_date' );
		}
		
		if( !jQuery.payment.validateCardCVC( jQuery( document.getElementById( 'ec_security_code' ) ).val( ), cardType) ){
			errors = true;
			ec_show_error( 'ec_security_code' );
		}else{
			ec_hide_error( 'ec_security_code' );
		}
		
	}
	
	return ( !errors );
	
}

function ec_validate_terms( ){
	
	var errors = false;
	
	if( jQuery( document.getElementById( 'ec_terms_agree' ) ).is( ':checked' ) || jQuery( document.getElementById( 'ec_terms_agree' ) ).val( ) == '2' ){
		ec_hide_error( 'ec_terms' );
	}else{
		errors = true;
		ec_show_error( 'ec_terms' );
	}
	
	return ( !errors );
	
}

function ec_validate_email( email ){
	
	return /^([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x22([^\x0d\x22\x5c\x80-\xff]|\x5c[\x00-\x7f])*\x22)(\x2e([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x22([^\x0d\x22\x5c\x80-\xff]|\x5c[\x00-\x7f])*\x22))*\x40([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x5b([^\x0d\x5b-\x5d\x80-\xff]|\x5c[\x00-\x7f])*\x5d)(\x2e([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x5b([^\x0d\x5b-\x5d\x80-\xff]|\x5c[\x00-\x7f])*\x5d))*$/.test( email );

}

function ec_validate_password( pw ){
	
	if( pw && pw.length > 5 )
		return true;
	else
		return false;
	
}

function ec_validate_text( str ){
	
	if( str && str.length > 0 )
		return true;
	else
		return false;
	
}

function ec_validate_select( val ){
	
	if( val && val != 0 )
		return true;
	else
		return false;
	
}

function ec_validate_match( val1, val2 ){
	
	if( val1 == val2 )
		return true;
	else
		return false;
	
}

function ec_validate_zip_code( zip, country ){
	
	zip = zip.trim( );
	
	if( country == "US" )
		return /(^\d{5}$)|(^\d{5}-\d{4}$)/.test( zip );
	else if( country == "AU" )
		return /^([0-9]{4})$/.test( zip );
	else if( country == "CA" ){
		var regex = new RegExp( /^[ABCEGHJKLMNPRSTVXY]\d[ABCEGHJKLMNPRSTVWXYZ]( )?\d[ABCEGHJKLMNPRSTVWXYZ]\d$/i );
    	return regex.test( zip );
	}else if( country == "GB" ){
		var postcode = zip.replace(/\s/g, "");
		var regex = /^(([A-Za-z]{2}[0-9][A-Za-z] ?[0-9][A-Za-z]{2})|([A-Za-z][0-9][A-Za-z] ?[0-9][A-Za-z]{2})|([A-Za-z][0-9] ?[0-9][A-Za-z]{2})|([A-Za-z][0-9]{2} ?[0-9][A-Za-z]{2})|([A-Za-z]{2}[0-9] ?[0-9][A-Za-z]{2})|([A-Za-z]{2}[0-9]{2} ?[0-9][A-Za-z]{2}))$/i;
		return regex.test( postcode );
	}else
		return ec_validate_text( zip );
	
}

function ec_is_state_required( country ){
	if( country == "AU" || country == "BR" || country == "CA" || country == "CN" || country == "GB" || country == "IN" || country == "JP" || country == "US" )
		return true;
	else
		return false; 
}

function ec_get_card_type( card_number ){
	
	var num = card_number;
	
	num = num.replace(/[^\d]/g,'');
	
	if( num.match( /^5[1-5]\d{14}$/ ) )														return "mastercard";
	else if( num.match( /^4\d{15}/ ) || num.match( /^4\d{12}/ ) )							return "visa";
	else if( num.match( /(^3[47])((\d{11}$)|(\d{13}$))/ ) )									return "amex";
	else if( num.match( /^6(?:011\d{12}|5\d{14}|4[4-9]\d{13}|22(?:1(?:2[6-9]|[3-9]\d)|[2-8]\d{2}|9(?:[01]\d|2[0-5]))\d{10})$/ ) )									
																							return "discover";
	else if( num.match( /^(?:5[0678]\d\d|6304|6390|67\d\d)\d{8,15}$/ ) )					return "maestro";
	else if( num.match( /(^(352)[8-9](\d{11}$|\d{12}$))|(^(35)[3-8](\d{12}$|\d{13}$))/ ) )	return "jcb";
	else if( num.match( /(^(30)[0-5]\d{11}$)|(^(36)\d{12}$)|(^(38[0-8])\d{11}$)/ ) )		return "diners";
	else																					return "all";
		
}

function ec_validate_credit_card( card_number ){
	
	var card_type = ec_get_card_type( card_number );
	
	if( card_type == "visa" || card_type == "delta" || card_type == "uke" ){
		if( /^4[0-9]{12}(?:[0-9]{3}|[0-9]{6})?$/.test( card_number ) )								return true;
		else 																						return false;
	
	}else if( card_type == "discover" ){
		if( /^6(?:011\d{12}|5\d{14}|4[4-9]\d{13}|22(?:1(?:2[6-9]|[3-9]\d)|[2-8]\d{2}|9(?:[01]\d|2[0-5]))\d{10})$/.test( card_number ) )	
																									return true;
		else																						return false;
	
	}else if( card_type == "mastercard" || card_type == "mcdebit" ){
		if( /^5[1-5]\d{14}$/.test( card_number ) )													return true;
		else																						return false;
	
	}else if( card_type == "amex" ){
		if( /^3[47][0-9]{13}$/.test( card_number ) )												return true;
		else																						return false;
	
	}else if( card_type == "diners" ){
		if( /^3(?:0[0-5]|[68][0-9])[0-9]{11}$/.test( card_number ) )								return true;
		else																						return false;
	
	}else if( card_type == "jcb" ){
		if( /^(?:2131|1800|35\d{3})\d{11}$/.test( card_number ) )											return true;
		else																						return false;
	
	}else if( card_type == "maestro" ){
		if( /(^(5[0678]\d{11,18}$))|(^(6[^0357])\d{11,18}$)|(^(3)\d{13,20}$)/.test( card_number ) )	return true;
		else																						return false;	
	}
}

function ec_validate_security_code( security_code ){
	
	if( /^[0-9]{3,4}$/.test( security_code ) )													return true;
	else																						return false;

}

function ec_show_error( error_field ){
	jQuery( document.getElementById( '' + error_field + '_error' ) ).show( );
}

function ec_hide_error( error_field ){
	jQuery( document.getElementById( '' + error_field + '_error' ) ).hide( );
}

function ec_cart_shipping_method_change( shipping_method, price ){
	
	var data = {
		action: 'ec_ajax_update_shipping_method',
		shipping_method: shipping_method
	};
	
	jQuery.ajax({
		url: wpeasycart_ajax_object.ajax_url, 
		type: 'post', 
		data: data, 
		success: function( data ){ 
			var data_arr = data.split( '***' );
			jQuery( document.getElementById( 'ec_cart_shipping' ) ).html( data_arr[0] );
			jQuery( document.getElementById( 'ec_cart_total' ) ).html( data_arr[1] );
		}
	} );
	
}

jQuery( document ).ready( function( $ ){
    $( ".ec_menu_vertical" ).accordion({
        accordion:true,
        speed: 500,
        closedSign: '[+]',
        openedSign: '[-]'
    });
});
(function(jQuery){
    jQuery.fn.extend({
    accordion: function(options) {
        
		var defaults = {
			accordion: 'true',
			speed: 300,
			closedSign: '[+]',
			openedSign: '[-]'
		};
		var opts = jQuery.extend(defaults, options);
 		var jQuerythis = jQuery(this);
 		jQuerythis.find("li").each(function() {
 			if(jQuery(this).find("ul").size() != 0){
 				jQuery(this).find("a:first").append("<span>"+ opts.closedSign +"</span>");
 				if(jQuery(this).find("a:first").attr('href') == "#"){
 		  			jQuery(this).find("a:first").click(function(){return false;});
 		  		}
 			}
 		});
 		jQuerythis.find("li.active").each(function() {
 			jQuery(this).parents("ul").slideDown(opts.speed);
 			jQuery(this).parents("ul").parent("li").find("span:first").html(opts.openedSign);
 		});
  		jQuerythis.find("li a").click(function() {
  			if(jQuery(this).parent().find("ul").size() != 0){
  				if(opts.accordion){
  					if(!jQuery(this).parent().find("ul").is(':visible')){
  						parents = jQuery(this).parent().parents("ul");
  						visible = jQuerythis.find("ul:visible");
  						visible.each(function(visibleIndex){
  							var close = true;
  							parents.each(function(parentIndex){
  								if(parents[parentIndex] == visible[visibleIndex]){
  									close = false;
  									return false;
  								}
  							});
  							if(close){
  								if(jQuery(this).parent().find("ul") != visible[visibleIndex]){
  									jQuery(visible[visibleIndex]).slideUp(opts.speed, function(){
  										jQuery(this).parent("li").find("span:first").html(opts.closedSign);
  									});
  									
  								}
  							}
  						});
  					}
  				}
  				if(jQuery(this).parent().find("ul:first").is(":visible")){
  					jQuery(this).parent().find("ul:first").slideUp(opts.speed, function(){
  						jQuery(this).parent("li").find("span:first").delay(opts.speed).html(opts.closedSign);
  					});
  					
  					
  				}else{
  					jQuery(this).parent().find("ul:first").slideDown(opts.speed, function(){
  						jQuery(this).parent("li").find("span:first").delay(opts.speed).html(opts.openedSign);
  					});
  				}
  			}
  		});
    }
});
})(jQuery);

function ec_cart_widget_click( ){
	if( !jQuery('.ec_cart_widget_minicart_wrap').is(':visible') ) 
		jQuery('.ec_cart_widget_minicart_wrap').fadeIn( 200 );
	else
		jQuery('.ec_cart_widget_minicart_wrap').fadeOut( 100 );
}

function ec_cart_widget_mouseover( ){
	if( !jQuery('.ec_cart_widget_minicart_wrap').is(':visible') ){
		jQuery('.ec_cart_widget_minicart_wrap').fadeIn( 200 );
		jQuery('.ec_cart_widget_minicart_bg').css( "display", "block" );
	}
}

function ec_cart_widget_mouseout( ){
	if( jQuery('.ec_cart_widget_minicart_wrap').is(':visible') ) {
		jQuery('.ec_cart_widget_minicart_wrap').fadeOut( 100 );
		jQuery('.ec_cart_widget_minicart_bg').css( "display", "none" );
	}
}

var wpeasycart_last_search = "";
function ec_live_search_update( ){
	
	var code = event.which || event.keyCode;
	var search_val = jQuery( '.ec_search_input' ).val( );
	
	if( code != 16 && code != 17 && code != 18 && code != 20 && code != 37 && code != 38 && code != 39 && code != 40 && wpeasycart_last_search != search_val ){
		
		wpeasycart_last_search = search_val;
		
		var data = {
			action: 'ec_ajax_live_search',
			search_val: search_val
		};
		
		jQuery.ajax({url: wpeasycart_ajax_object.ajax_url, type: 'post', data: data, success: function( data ){
			if( wpeasycart_last_search == search_val ){
				data = JSON.parse( data );
				jQuery( document.getElementById( 'ec_search_suggestions' ) ).empty( );
				for( var i=0; i<data.length; i++ ){
					jQuery( document.getElementById( 'ec_search_suggestions' ) ).append( "<option value='" + data[i].title + "'>" );
				}
			}
		} } );
		
	}
	
}

function ec_account_forgot_password_button_click( ){
	
	var errors = false;
	var email = jQuery( document.getElementById( 'ec_account_forgot_password_email' ) ).val( );
	
	if( !ec_validate_email( email ) ){
		errors = true;
		ec_show_error( 'ec_account_forgot_password_email' );
	}else{
		ec_hide_error( 'ec_account_forgot_password_email' );
	}
	
	return( !errors );
	
}

function ec_account_register_button_click2( ){
	var top_half = ec_account_register_button_click( );
	var bottom_half = true;
	
	if( jQuery( document.getElementById( 'ec_account_billing_information_country' ) ).length )
		bottom_half = ec_account_billing_information_update_click( );
	
	var extra_notes_validated = ec_account_register_validate_notes( );
	
	var recaptcha_validated = true;
	if( jQuery( document.getElementById( 'ec_account_register_recaptcha' ) ).length ){
		var recaptcha_response = jQuery( document.getElementById( 'ec_grecaptcha_response_register' ) ).val( );
		if( !recaptcha_response.length ){
			jQuery( '#ec_account_register_recaptcha > div' ).css( 'border', '1px solid red' );
			recaptcha_validated = false;
		}else{
			jQuery( '#ec_account_register_recaptcha > div' ).css( 'border', 'none' );
		}
	}
	
	if( top_half && bottom_half && extra_notes_validated && recaptcha_validated ){
		return true;
	}else{
		return false;
	}
}

function ec_account_register_button_click( ){
	var email_validated = ec_validate_email_block( 'ec_account_register' );
	var contact_validated = ec_validate_create_account( 'ec_account_register' );
	
	var recaptcha_validated = true;
	if( jQuery( document.getElementById( 'ec_account_register_recaptcha' ) ).length ){
		var recaptcha_response = jQuery( document.getElementById( 'ec_grecaptcha_response_register' ) ).val( );
		if( !recaptcha_response.length ){
			jQuery( '#ec_account_register_recaptcha > div' ).css( 'border', '1px solid red' );
			recaptcha_validated = false;
		}else{
			jQuery( '#ec_account_register_recaptcha > div' ).css( 'border', 'none' );
		}
	}
	
	if( email_validated && contact_validated && recaptcha_validated )
		return true;
	else
		return false;
	
}

function ec_account_billing_information_update_click( ){
	var address_validated = ec_validate_address_block( 'ec_account_billing_information' );
	
	if( address_validated )
		return true;
	else
		return false;
	
}

function ec_account_shipping_information_update_click( ){
	var address_validated = ec_validate_address_block( 'ec_account_shipping_information' );
	
	if( address_validated )
		return true;
	else
		return false;
	
}

function ec_account_personal_information_update_click( ){
	
	var errors = false;
	var email = jQuery( document.getElementById( 'ec_account_personal_information_email' ) ).val( );
	
	if( jQuery( document.getElementById( 'ec_account_personal_information_first_name' ) ).length && !ec_validate_text( jQuery( document.getElementById( 'ec_account_personal_information_first_name' ) ).val( ) ) ){
		errors = true;
		ec_show_error( 'ec_account_personal_information_first_name' );
	}else{
		ec_hide_error( 'ec_account_personal_information_first_name' );
	}
	
	if( jQuery( document.getElementById( 'ec_account_personal_information_last_name' ) ).length && !ec_validate_text( jQuery( document.getElementById( 'ec_account_personal_information_last_name' ) ).val( ) ) ){
		errors = true;
		ec_show_error( 'ec_account_personal_information_last_name' );
	}else{
		ec_hide_error( 'ec_account_personal_information_last_name' );
	}
	
	if( !ec_validate_email( email ) ){
		errors = true;
		ec_show_error( 'ec_account_personal_information_email' );
	}else{
		ec_hide_error( 'ec_account_personal_information_email' );
	}
	
	return( !errors );
}

function ec_account_password_button_click( ){
	
	var errors = false;
	var current_password = jQuery( document.getElementById( 'ec_account_password_current_password' ) ).val( );
	var new_password = jQuery( document.getElementById( 'ec_account_password_new_password' ) ).val( );
	var retype_password = jQuery( document.getElementById( 'ec_account_password_retype_new_password' ) ).val( );
	
	if( !ec_validate_password( current_password ) ){
		errors = true;
		ec_show_error( 'ec_account_password_current_password' );
	}else{
		ec_hide_error( 'ec_account_password_current_password' );
	}
	
	if( !ec_validate_password( new_password ) ){
		errors = true;
		ec_show_error( 'ec_account_password_new_password' );
	}else{
		ec_hide_error( 'ec_account_password_new_password' );
	}
	
	if( !ec_validate_match( new_password, retype_password ) ){
		errors = true;
		ec_show_error( 'ec_account_password_retype_new_password' );
	}else{
		ec_hide_error( 'ec_account_password_retype_new_password' );
	}
	
	return( !errors );
	
}

function ec_account_register_validate_notes( ){
	if( !jQuery( document.getElementById( 'ec_account_register_user_notes' ) ).length || ( jQuery( document.getElementById( 'ec_account_register_user_notes' ) ).length && jQuery( document.getElementById( 'ec_account_register_user_notes' ) ).val( ) != "" ) ){
		ec_hide_error( 'ec_account_register_user_notes' );
		return true;
	}else{
		ec_show_error( 'ec_account_register_user_notes' );
		return false;
	}
}

function ec_account_login_button_click( ){
	
	var errors = false;
	var email = jQuery( document.getElementById( 'ec_account_login_email' ) ).val( );
	var password = jQuery( document.getElementById( 'ec_account_login_password' ) ).val( );
	
	if( !ec_validate_email( email ) ){
		errors = true;
		ec_show_error( 'ec_account_login_email' );
	}else{
		ec_hide_error( 'ec_account_login_email' );
	}
	
	if( !ec_validate_text( password ) ){
		errors = true;
		ec_show_error( 'ec_account_login_password' );
	}else{
		ec_hide_error( 'ec_account_login_password' );
	}
	
	if( jQuery( document.getElementById( 'ec_account_login_recaptcha' ) ).length ){
		var recaptcha_response = jQuery( document.getElementById( 'ec_grecaptcha_response_login' ) ).val( );
		if( !recaptcha_response.length ){
			jQuery( '#ec_account_login_recaptcha > div' ).css( 'border', '1px solid red' );
			errors = true;
		}else{
			jQuery( '#ec_account_login_recaptcha > div' ).css( 'border', 'none' );
		}
	}
	
	return ( !errors );

}

function ec_close_popup_newsletter( ){
	
	jQuery( '.ec_newsletter_container' ).fadeOut( 'slow' );
	
	var data = {
		action: 'ec_ajax_close_newsletter'
	};
	
	jQuery.ajax({url: wpeasycart_ajax_object.ajax_url, type: 'post', data: data, success: function( data ){ } } );
	
}

function ec_submit_newsletter_signup( ){
	
	jQuery( '.ec_newsletter_pre_submit' ).hide( );
	jQuery( '.ec_newsletter_post_submit' ).show( );
		
	var email_address = jQuery( document.getElementById( 'ec_newsletter_email' ) ).val( );
	var newsletter_name = "";
	if( document.getElementById( 'ec_newsletter_name' ) )
		newsletter_name = jQuery( document.getElementById( 'ec_newsletter_name' ) ).val( );
	
	var data = {
		action: 'ec_ajax_submit_newsletter_signup',
		email_address: email_address,
		newsletter_name: newsletter_name
	};
	
	jQuery.ajax({url: wpeasycart_ajax_object.ajax_url, type: 'post', data: data, success: function( data ){ 
	} } );
	
}

function ec_submit_newsletter_signup_widget( ){
	
	jQuery( '.ec_newsletter_pre_submit' ).hide( );
	jQuery( '.ec_newsletter_post_submit' ).show( );
		
	var email_address = jQuery( '#ec_newsletter_email_widget' ).val( );
	var newsletter_name = "";
	if( document.getElementById( 'ec_newsletter_name_widget' ) )
		newsletter_name = jQuery( document.getElementById( 'ec_newsletter_name_widget' ) ).val( );
	
	var data = {
		action: 'ec_ajax_submit_newsletter_signup',
		email_address: email_address,
		newsletter_name: newsletter_name
	};
	
	jQuery.ajax({url: wpeasycart_ajax_object.ajax_url, type: 'post', data: data, success: function( data ){ 
	} } );
	
}

function update_download_count( orderdetail_id ){
	
	if( jQuery( document.getElementById( 'ec_download_count_' + orderdetail_id ) ).length ){
		var count = Number( jQuery( document.getElementById( 'ec_download_count_' + orderdetail_id ) ).html( ) );
		var max_count = Number( jQuery( document.getElementById( 'ec_download_count_max_' + orderdetail_id ) ).html( ) );
		if( count < max_count ){
			count++;
			jQuery( document.getElementById( 'ec_download_count_' + orderdetail_id ) ).html( count );
		}
	}
	
}

function show_billing_info( ){
	jQuery( document.getElementById( 'ec_account_subscription_billing_information' ) ).slideToggle(600);
	return false;
}

function ec_check_update_subscription_info( ){
		
	if( jQuery( document.getElementById( 'ec_account_subscription_billing_information' ) ).is(":visible") ){
		
		var address_validated = ec_validate_address_block( 'ec_account_billing_information' );
		var payment_method_complete = ec_validate_payment_method( );
		var terms_complete = ec_validate_terms( );
		
		if( address_validated && payment_method_complete && terms_complete )
			return true;
		else
			return false;
			
	}else{
		return true;
	}
}

function ec_cancel_subscription_check( confirm_text ){
	return confirm( confirm_text );
}

function ec_details_show_inquiry_form( product_id ){
	jQuery( '.ec_details_inquiry_popup_' + product_id ).fadeIn( 'fast' );
	return false;
}

function ec_details_hide_inquiry_popup( product_id ){
	jQuery( '.ec_details_inquiry_popup_' + product_id ).fadeOut( 'fast' );
}

function ec_details_show_image_popup( model_number ){
	jQuery( document.getElementById( 'ec_details_large_popup_' + model_number ) ).show( );
	jQuery( 'html' ).css( 'overflow', 'hidden' );
}

function ec_details_hide_large_popup( model_number ){
	jQuery( document.getElementById( 'ec_details_large_popup_' + model_number ) ).hide( );
	jQuery( 'html' ).css( 'overflow', 'scroll' );
}

function ec_create_ideal_order_redirect( source ){
	var redirect = source.redirect.url;
	var data = {
		action: 'ec_ajax_create_stripe_ideal_order',
		source: source
	};
	jQuery.ajax({url: wpeasycart_ajax_object.ajax_url, type: 'post', data: data, success: function( data ){
		window.location.href = redirect;
	} } );
}

function ec_notify_submit( product_id ){
	var errors = false;
	var email = jQuery( document.getElementById( 'ec_email_notify_' + product_id ) ).val( );
	if( !ec_validate_email( email ) ){
		errors = true;
		jQuery( document.getElementById( 'ec_email_notify_' + product_id + '_error' ) ).show( );
	}else{
		jQuery( document.getElementById( 'ec_email_notify_' + product_id + '_error' ) ).hide( );
	}
	
	var recaptcha_response = false;
	if( jQuery( document.getElementById( 'ec_grecaptcha_response_product_details' ) ).length ){
		recaptcha_response = jQuery( document.getElementById( 'ec_grecaptcha_response_product_details' ) ).val( );
		if( !recaptcha_response.length ){
			jQuery( '#ec_product_details_recaptcha > div' ).css( 'border', '1px solid red' );
			errors = true;
		}else{
			jQuery( '#ec_product_details_recaptcha > div' ).css( 'border', 'none' );
		}
	}
	
	if( !errors ){
		jQuery( document.getElementById( 'ec_product_details_stock_notify_' + product_id + '_loader_cover' ) ).show( );
		jQuery( document.getElementById( 'ec_product_details_stock_notify_' + product_id + '_loader' ) ).show( );
		var data = {
			action: 'ec_ajax_subscribe_to_stock_notification',
			email: email,
			product_id: product_id,
			recaptcha_response: recaptcha_response
		};
		jQuery.ajax({url: wpeasycart_ajax_object.ajax_url, type: 'post', data: data, success: function( data ){
			jQuery( document.getElementById( 'ec_product_details_stock_notify_complete_' + product_id ) ).show( );
			jQuery( document.getElementById( 'ec_product_details_stock_notify_' + product_id ) ).hide( );
		jQuery( document.getElementById( 'ec_product_details_stock_notify_' + product_id + '_loader_cover' ) ).hide( );
		jQuery( document.getElementById( 'ec_product_details_stock_notify_' + product_id + '_loader' ) ).hide( );
		} } );
	}
	
}

function wpeasycart_load_cart( cart_page, success_code, error_code ){
    var data = {
        action: 'ec_ajax_get_dynamic_cart_page',
        cart_page: cart_page,
        success_code: success_code,
        error_code: error_code,
        language: wpeasycart_ajax_object.current_language
    };
    jQuery.ajax( { url: wpeasycart_ajax_object.ajax_url, type: 'post', data: data, success: function( data ){ 
        jQuery( document.getElementById( 'wpeasycart_cart_holder' ) ).replaceWith( data );
    } } );
}

function wpeasycart_load_account( account_page, page_id, success_code, error_code ){
    var data = {
        action: 'ec_ajax_get_dynamic_account_page',
        account_page: account_page,
        page_id: page_id,
        success_code: success_code,
        error_code: error_code,
        language: wpeasycart_ajax_object.current_language
    };
    jQuery.ajax( { url: wpeasycart_ajax_object.ajax_url, type: 'post', data: data, success: function( data ){ 
        jQuery( document.getElementById( 'wpeasycart_account_holder' ) ).replaceWith( data );
    } } );
}

function wpeasycart_update_tip( tip_rate ){
    jQuery( '.ec_cart_tip_item' ).removeClass( 'ec_tip_selected' );
    var tip_amount = Number( jQuery( document.getElementById( 'ec_cart_tip_custom' ) ).val( ) );
    jQuery( document.getElementById( 'ec_apply_tip_button' ) ).hide( );
    jQuery( document.getElementById( 'ec_applying_tip' ) ).show( );
    var data = {
        action: 'ec_ajax_update_tip_amount',
        tip_rate: tip_rate,
        tip_amount: tip_amount,
        language: wpeasycart_ajax_object.current_language
    };
    jQuery.ajax( { url: wpeasycart_ajax_object.ajax_url, type: 'post', data: data, success: function( data ){ 
        var response_obj = JSON.parse( data );
		ec_update_cart( response_obj );
        jQuery( document.getElementById( 'ec_apply_tip_button' ) ).show( );
        jQuery( document.getElementById( 'ec_applying_tip' ) ).hide( );
    } } );
}