// JavaScript Document

function ec_admin_save_amazon_settings( ){
	jQuery( document.getElementById( "ec_admin_amazon_settings_loader" ) ).fadeIn( 'fast' );
	
	var data = {
		action: 'ec_admin_ajax_save_amazon_settings',
		ec_option_amazon_key:  ec_admin_get_value( 'ec_option_amazon_key', 'text' ),
		ec_option_amazon_secret:  ec_admin_get_value( 'ec_option_amazon_secret', 'text' ),
		ec_option_amazon_bucket:  ec_admin_get_value( 'ec_option_amazon_bucket', 'text' ),
		ec_option_amazon_bucket_region:  ec_admin_get_value( 'ec_option_amazon_bucket_region', 'select' )
	};
	
	jQuery.ajax({url: ajax_object.ajax_url, type: 'post', data: data, success: function(data){ 
		ec_admin_hide_loader( 'ec_admin_amazon_settings_loader' );
	} } );
	
	return false;
	
}

function ec_admin_save_deconetwork_settings( ){
	jQuery( document.getElementById( "ec_admin_deconetwork_loader" ) ).fadeIn( 'fast' );
	
	var data = {
		action: 'ec_admin_ajax_save_deconetwork_settings',
		ec_option_deconetwork_url:  ec_admin_get_value( 'ec_option_deconetwork_url', 'text' ),
		ec_option_deconetwork_password:  ec_admin_get_value( 'ec_option_deconetwork_password', 'text' )
	};
	
	jQuery.ajax({url: ajax_object.ajax_url, type: 'post', data: data, success: function(data){ 
		ec_admin_hide_loader( 'ec_admin_deconetwork_loader' );
	} } );
	
	return false;
	
}
function ec_admin_save_google_analytics( ){
	jQuery( document.getElementById( "ec_admin_google_analytics_loader" ) ).fadeIn( 'fast' );
	
	var ec_option_googleanalyticsid = jQuery( document.getElementById( 'ec_option_googleanalyticsid' ) ).val( );
	
	var data = {
		action: 'ec_admin_ajax_save_google_analytics',
		ec_option_googleanalyticsid: ec_option_googleanalyticsid
	};
	
	jQuery.ajax({url: ajax_object.ajax_url, type: 'post', data: data, success: function(data){ 
		ec_admin_hide_loader( 'ec_admin_google_analytics_loader' );
	} } );
	
	return false;
	
}


function ec_admin_save_google_adwords( ){
	jQuery( document.getElementById( "ec_admin_google_adwords_loader" ) ).fadeIn( 'fast' );
	
	var ec_option_google_adwords_conversion_id = jQuery( document.getElementById( 'ec_option_google_adwords_conversion_id' ) ).val( );
	var ec_option_google_adwords_language = jQuery( document.getElementById( 'ec_option_google_adwords_language' ) ).val( );
	var ec_option_google_adwords_format = jQuery( document.getElementById( 'ec_option_google_adwords_format' ) ).val( );
	var ec_option_google_adwords_color = jQuery( document.getElementById( 'ec_option_google_adwords_color' ) ).val( );
	var ec_option_google_adwords_currency = jQuery( document.getElementById( 'ec_option_google_adwords_currency' ) ).val( );
	var ec_option_google_adwords_label = jQuery( document.getElementById( 'ec_option_google_adwords_label' ) ).val( );
	
	var ec_option_google_adwords_remarketing_only = "false";
	if( jQuery( document.getElementById( 'ec_option_google_adwords_remarketing_only' ) ).is( ':checked' ) )
		ec_option_google_adwords_remarketing_only = "true";
	
	var data = {
		action: 'ec_admin_ajax_save_google_adwords',
		ec_option_google_adwords_conversion_id: ec_option_google_adwords_conversion_id,
		ec_option_google_adwords_language: ec_option_google_adwords_language,
		ec_option_google_adwords_format: ec_option_google_adwords_format,
		ec_option_google_adwords_color: ec_option_google_adwords_color,
		ec_option_google_adwords_currency: ec_option_google_adwords_currency,
		ec_option_google_adwords_label: ec_option_google_adwords_label,
		ec_option_google_adwords_remarketing_only: ec_option_google_adwords_remarketing_only
	};
	
	jQuery.ajax({url: ajax_object.ajax_url, type: 'post', data: data, success: function(data){ 
		ec_admin_hide_loader( 'ec_admin_google_adwords_loader' );
	} } );
	
	return false;
	
}

function ec_admin_save_facebook_settings( ){
	jQuery( document.getElementById( "ec_admin_facebook_settings_loader" ) ).fadeIn( 'fast' );
	
	var data = {
		action: 'ec_admin_ajax_save_facebook_settings',
		ec_option_fb_pixel:  ec_admin_get_value( 'ec_option_fb_pixel', 'text' )
	};
	
	jQuery.ajax({url: ajax_object.ajax_url, type: 'post', data: data, success: function(data){ 
		ec_admin_hide_loader( 'ec_admin_facebook_settings_loader' );
	} } );
	
	return false;
	
}