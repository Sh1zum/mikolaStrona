function ec_admin_save_woo_importer( ){
	jQuery( document.getElementById( "ec_admin_woo_importer" ) ).fadeIn( 'fast' );
	var data = {
		action: 'ec_admin_ajax_save_woo_importer'
	};
	jQuery.ajax({url: ajax_object.ajax_url, type: 'post', data: data, success: function(data){ 
		ec_admin_hide_loader( 'ec_admin_woo_importer' );
	} } );
	return false;
}

function ec_admin_save_oscommerce_importer( ){
	jQuery( document.getElementById( "ec_admin_oscommerce_importer" ) ).fadeIn( 'fast' );
	var data = {
		action: 'ec_admin_ajax_save_oscommerce_importer',
	};
	jQuery.ajax({url: ajax_object.ajax_url, type: 'post', data: data, success: function(data){ 
		ec_admin_hide_loader( 'ec_admin_oscommerce_importer' );
	} } );
	return false;
}

function wpeasycart_start_square_import( ){
    jQuery( document.getElementById( 'wpeasycart_square_import_progress_bar' ) ).show( );
    jQuery( document.getElementById( 'wpeasycart_square_processing_button' ) ).show( );
    jQuery( document.getElementById( 'wpeasycart_square_start_button' ) ).hide( );
    jQuery( '.ec_admin_progress_bar > div' ).width( '23%' );
    wpeasycart_continue_square_modifier_items_import( '', 0 );
}

function wpeasycart_continue_square_modifier_items_import( cursor, curr_count ){
    jQuery( document.getElementById( 'wpeasycart_square_import_progress_bar' ) ).show( );
    var data = {
		action: 'ec_admin_ajax_square_modifier_items_import',
        cursor: cursor,
        curr_count: curr_count,
        sync_modifiers: ec_admin_get_value( 'wpeasycart_square_sync_matches', 'checkbox' )
	};
	jQuery.ajax({url: ajax_object.ajax_url, type: 'post', data: data, success: function(result){
        var result_arr = JSON.parse( result );
		if( result_arr.has_more ){
            jQuery( '#wpeasycart_square_import_progress_bar .ec_admin_process_status > span' ).html( result_arr.curr_count + ' Modifier Items Imported' );
            wpeasycart_continue_square_modifier_items_import( result_arr.cursor, result_arr.curr_count );
        }else{
            jQuery( '#wpeasycart_square_import_progress_bar .ec_admin_process_status > span' ).html( 'All Modifiers Imported, Starting Products.' );
            jQuery( '.ec_admin_progress_bar > div' ).width( '53%' );
            wpeasycart_continue_square_import( '', 0 );
        }
	} } );
}

function wpeasycart_continue_square_modifier_import( cursor, curr_count ){ /* Not Needed right now */
    jQuery( document.getElementById( 'wpeasycart_square_import_progress_bar' ) ).show( );
    var data = {
		action: 'ec_admin_ajax_square_modifier_import',
        cursor: cursor,
        curr_count: curr_count,
        sync_modifiers: ec_admin_get_value( 'wpeasycart_square_sync_matches', 'checkbox' )
	};
	jQuery.ajax({url: ajax_object.ajax_url, type: 'post', data: data, success: function(result){
        var result_arr = JSON.parse( result );
		if( result_arr.has_more ){
            jQuery( '#wpeasycart_square_import_progress_bar .ec_admin_process_status > span' ).html( result_arr.curr_count + ' Modifiers Imported' );
            wpeasycart_continue_square_modifier_import( result_arr.cursor, result_arr.curr_count );
        }else{
            jQuery( '#wpeasycart_square_import_progress_bar .ec_admin_process_status > span' ).html( 'All Modifier Items Imported, Starting Products' );
            jQuery( '.ec_admin_progress_bar > div' ).width( '65%' );
            wpeasycart_continue_square_import( '', 0 );
        }
	} } );
}

function wpeasycart_continue_square_import( cursor, curr_count ){
    jQuery( document.getElementById( 'wpeasycart_square_import_progress_bar' ) ).show( );
    var data = {
		action: 'ec_admin_ajax_square_import',
        cursor: cursor,
        curr_count: curr_count,
        sync_products: ec_admin_get_value( 'wpeasycart_square_sync_matches', 'checkbox' )
	};
	jQuery.ajax({url: ajax_object.ajax_url, type: 'post', data: data, success: function(result){
        var result_arr = JSON.parse( result );
		if( result_arr.has_more ){
            jQuery( '#wpeasycart_square_import_progress_bar .ec_admin_process_status > span' ).html( result_arr.curr_count + ' Items Imported' );
            wpeasycart_continue_square_import( result_arr.cursor, result_arr.curr_count );
        }else{
            jQuery( '#wpeasycart_square_import_progress_bar .ec_admin_process_status > span' ).html( 'All Products Imported!' );
            jQuery( '.ec_admin_progress_bar > div' ).width( '90%' ).addClass( 'done' );
        }
	} } );
}

function wpeasycart_start_shopify_import( ){
    jQuery( document.getElementById( 'wpeasycart_shopify_processing_button' ) ).show( );
    jQuery( document.getElementById( 'wpeasycart_shopify_start_button' ) ).hide( );
    jQuery( document.getElementById( 'wpeasycart_shopify_import_progress_bar' ) ).show( );
    jQuery( document.getElementById( 'wpeasycart_shopify_import_errors' ) ).hide( );
    jQuery( document.getElementById( 'wpeasycart_shopify_input_fields' ) ).hide( );
    jQuery( '.ec_admin_progress_bar > div' ).width( '23%' );
    wpeasycart_continue_shopify_products_import( '', 0 );
}

function wpeasycart_continue_shopify_products_import( cursor, curr_count ){
    var data = {
		action: 'ec_admin_ajax_shopify_import_products',
        cursor: cursor,
        curr_count: curr_count,
        wpeasycart_shopify_api_key: jQuery( document.getElementById( 'wpeasycart_shopify_api_key' ) ).val( ),
        wpeasycart_shopify_api_password: jQuery( document.getElementById( 'wpeasycart_shopify_api_password' ) ).val( ),
        wpeasycart_shopify_domain: jQuery( document.getElementById( 'wpeasycart_shopify_domain' ) ).val( )
	};
	
	jQuery.ajax({url: ajax_object.ajax_url, type: 'post', data: data, success: function(result){
        var result_arr = JSON.parse( result );
        if( result_arr.has_errors ){
            jQuery( document.getElementById( 'wpeasycart_shopify_processing_button' ) ).hide( );
            jQuery( document.getElementById( 'wpeasycart_shopify_start_button' ) ).show( );
            jQuery( document.getElementById( 'wpeasycart_shopify_import_progress_bar' ) ).hide( );
            jQuery( document.getElementById( 'wpeasycart_shopify_import_errors' ) ).show( );
            jQuery( document.getElementById( 'wpeasycart_shopify_input_fields' ) ).show( );
            
        }else if( result_arr.has_more ){
            jQuery( '#wpeasycart_shopify_import_progress_bar .ec_admin_process_status > span' ).html( result_arr.curr_count + ' Items Imported' );
            wpeasycart_continue_shopify_products_import( result_arr.cursor, result_arr.curr_count );
        
        }else{
            jQuery( '#wpeasycart_shopify_import_progress_bar .ec_admin_process_status > span' ).html( 'All Products Imported!' );
            jQuery( '.ec_admin_progress_bar > div' ).width( '65%' );
            wpeasycart_continue_shopify_categories_import( '', 0 );
            
        }
	} } );
}

function wpeasycart_continue_shopify_categories_import( cursor, curr_count ){
    var data = {
		action: 'ec_admin_ajax_shopify_import_categories',
        cursor: cursor,
        curr_count: curr_count,
        wpeasycart_shopify_api_key: jQuery( document.getElementById( 'wpeasycart_shopify_api_key' ) ).val( ),
        wpeasycart_shopify_api_password: jQuery( document.getElementById( 'wpeasycart_shopify_api_password' ) ).val( ),
        wpeasycart_shopify_domain: jQuery( document.getElementById( 'wpeasycart_shopify_domain' ) ).val( )
	};
	
	jQuery.ajax({url: ajax_object.ajax_url, type: 'post', data: data, success: function(result){
        var result_arr = JSON.parse( result );
        if( result_arr.has_errors ){
            jQuery( document.getElementById( 'wpeasycart_shopify_processing_button' ) ).hide( );
            jQuery( document.getElementById( 'wpeasycart_shopify_start_button' ) ).show( );
            jQuery( document.getElementById( 'wpeasycart_shopify_import_progress_bar' ) ).hide( );
            jQuery( document.getElementById( 'wpeasycart_shopify_import_errors' ) ).show( );
            jQuery( document.getElementById( 'wpeasycart_shopify_input_fields' ) ).show( );
            
        }else if( result_arr.has_more ){
            jQuery( '#wpeasycart_shopify_import_progress_bar .ec_admin_process_status > span' ).html( result_arr.curr_count + ' Categories Imported' );
            wpeasycart_continue_shopify_categories_import( result_arr.cursor, result_arr.curr_count );
        
        }else{
            jQuery( '#wpeasycart_shopify_import_progress_bar .ec_admin_process_status > span' ).html( 'All Categories Imported!' );
            jQuery( '.ec_admin_progress_bar > div' ).width( '85%' );
            wpeasycart_continue_shopify_users_import( '', 0 );
            
        }
	} } );
}

function wpeasycart_continue_shopify_users_import( cursor, curr_count ){
    jQuery( document.getElementById( 'wpeasycart_shopify_import_progress_bar' ) ).show( );
    var data = {
		action: 'ec_admin_ajax_shopify_import_users',
        cursor: cursor,
        curr_count: curr_count,
        wpeasycart_shopify_api_key: jQuery( document.getElementById( 'wpeasycart_shopify_api_key' ) ).val( ),
        wpeasycart_shopify_api_password: jQuery( document.getElementById( 'wpeasycart_shopify_api_password' ) ).val( ),
        wpeasycart_shopify_domain: jQuery( document.getElementById( 'wpeasycart_shopify_domain' ) ).val( )
	};
	
	jQuery.ajax({url: ajax_object.ajax_url, type: 'post', data: data, success: function(result){
        var result_arr = JSON.parse( result );
		if( result_arr.has_errors ){
            jQuery( document.getElementById( 'wpeasycart_shopify_processing_button' ) ).hide( );
            jQuery( document.getElementById( 'wpeasycart_shopify_start_button' ) ).show( );
            jQuery( document.getElementById( 'wpeasycart_shopify_import_progress_bar' ) ).hide( );
            jQuery( document.getElementById( 'wpeasycart_shopify_import_errors' ) ).show( );
            jQuery( document.getElementById( 'wpeasycart_shopify_input_fields' ) ).show( );
            
        }else if( result_arr.has_more ){
            jQuery( '#wpeasycart_shopify_import_progress_bar .ec_admin_process_status > span' ).html( result_arr.curr_count + ' Customers Imported' );
            wpeasycart_continue_shopify_users_import( result_arr.cursor, result_arr.curr_count );
        
        }else{
            jQuery( '#wpeasycart_shopify_import_progress_bar .ec_admin_process_status > span' ).html( 'All Customers Imported!' );
            jQuery( '.ec_admin_progress_bar > div' ).width( '100%' ).addClass( 'done' );
        }
	} } );
}