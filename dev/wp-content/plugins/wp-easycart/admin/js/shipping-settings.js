jQuery( document ).ready( function( ){
    jQuery( '#wp_easycart_country_ship_table input[type="checkbox"]' ).on( 'change', function( ){
        var selected_country = jQuery( this ).parent( ).parent( ).find( '.wp-easycart-editable-table-read-only' ).html( );
        var is_enabled = false;
        if( jQuery( this ).is( ':checked' ) ){
            is_enabled = true;
        }
        jQuery( '#wp_easycart_state_ship_table input[type="checkbox"]' ).each( function( ){
            var country = jQuery( this ).parent( ).parent( ).find( 'td:nth-child(2) > div' ).html( );
            if( selected_country == country ){
                jQuery( this ).prop( "checked", is_enabled );
            }
        } );
    } );
} );

function ec_admin_add_shipping_zone( data, response ){
    jQuery( '.wp-easycart-zone-list' ).append( '<option value="' + response + '">' + data.zone_name + '</option>' );
}

function ec_admin_delete_shipping_zone( data, zone_id ){
    jQuery( '.wp-easycart-zone-list > option[value="' + zone_id + '"]:selected' ).parent( ).parent( ).parent( ).remove( );
    jQuery( '.wp-easycart-zone-list > option[value="' + zone_id + '"]' ).remove( );
}

function ec_admin_save_shipping_options( this_ele ){
    jQuery( this_ele ).parent( ).find( '.wp_easycart_toggle_saving' ).show( );
	
	var val = 0;
		
	if( jQuery( this_ele ).is( ':checked' ) )
		val = 1;
		
	var data = {
		action: 'ec_admin_ajax_save_shipping_settings',
		update_var: jQuery( this_ele ).attr( 'id' ),
		val: val
	}
	
	jQuery.ajax({url: ajax_object.ajax_url, type: 'post', data: data, success: function(data){ 
		jQuery( this_ele ).parent( ).find( '.wp_easycart_toggle_saving' ).hide( );
		jQuery( this_ele ).parent( ).find( '.wp_easycart_toggle_saved' ).fadeIn( ).delay( 500 ).fadeOut( 'slow' );
	} } );
	
	return false;
}

function ec_admin_save_shipping_text_setting( this_ele ){
	jQuery( this_ele ).parent( ).find( '.wp_easycart_toggle_saving' ).show( );
	jQuery( this_ele ).parent( ).find( '.wp-easycart-admin-icon-close-check' ).hide( );

	var val = jQuery( this_ele ).val( );

	var data = {
		action: 'ec_admin_ajax_save_shipping_settings',
		update_var: jQuery( this_ele ).attr( 'id' ),
		val: val
	}

	jQuery.ajax({url: ajax_object.ajax_url, type: 'post', data: data, success: function(data){ 
		jQuery( this_ele ).parent( ).find( '.wp_easycart_toggle_saving' ).hide( );
		jQuery( this_ele ).parent( ).find( '.wp_easycart_toggle_saved' ).fadeIn( ).delay( 500 ).fadeOut( 'slow' );
		jQuery( this_ele ).parent( ).find( '.wp-easycart-admin-icon-close-check' ).delay( 900 ).fadeIn( 'slow' );
	} } );
	
	return false;
}