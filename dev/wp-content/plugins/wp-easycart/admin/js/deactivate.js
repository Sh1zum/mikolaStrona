jQuery( document ).ready( function( ){
	jQuery( 'table.plugins > tbody > tr' ).each( function( ){
		if( jQuery( this ).attr( 'data-slug' ) == 'wp-easycart' ){
			jQuery( this ).find( 'span.deactivate > a' ).each( function( ){
                jQuery( this ).on( 'click', function( ){
					event.preventDefault( );
					var deactivate_content = '<div class="ec-admin-deactivate-container">';
						deactivate_content += '<div class="ec-admin-deactivate-bg" onclick="wpeasycart_deactivate_cancel( );"></div>';
						deactivate_content += '<div class="ec-admin-deactivate-box">';
							deactivate_content += '<div class="ec-admin-deactivate-box-header">QUICK FEEDBACK</div>';
							deactivate_content += '<div class="ec-admin-deactivate-box-content">';
								deactivate_content += '<h4>If you have a moment, please let us know why you are deactiving.</h4>';
								deactivate_content += '<ul class="ec-admin-deactivate-box-list">';
									
									deactivate_content += '<li><label>';
										deactivate_content += '<span><input type="radio" name="wpeasycart_deactivate_reason" id="wpeasycart_reason1" value="1" onchange="wpeasycart_deactivate_reason_update( );"></span>';
										deactivate_content += '<span>The plugin didn\'t work.</span>';
									deactivate_content += '</label></li>';
									
									deactivate_content += '<li><label>';
										deactivate_content += '<span><input type="radio" name="wpeasycart_deactivate_reason" id="wpeasycart_reason2" value="2" onchange="wpeasycart_deactivate_reason_update( );"></span>';
										deactivate_content += '<span>I found a better plugin.</span>';
										deactivate_content += '<div class="ec-admin-deactivate-reason" id="wpeasycart_reason2_row">';
											deactivate_content += '<span class="ec-admin-deactivate-reason-title"></span>';
											deactivate_content += '<input type="text" id="wpeasycart_reason2_extra" placeholder="What\'s the plugin\'s name?">';
										deactivate_content += '</div>';
									deactivate_content += '</label></li>';
									
									deactivate_content += '<li><label>';
										deactivate_content += '<span><input type="radio" name="wpeasycart_deactivate_reason" id="wpeasycart_reason3" value="3" onchange="wpeasycart_deactivate_reason_update( );"></span>';
										deactivate_content += '<span>I need a PRO feature and the upgrade cost is too high.</span>';
									deactivate_content += '</label></li>';
									
									deactivate_content += '<li><label>';
										deactivate_content += '<span><input type="radio" name="wpeasycart_deactivate_reason" id="wpeasycart_reason4" value="4" onchange="wpeasycart_deactivate_reason_update( );"></span>';
										deactivate_content += '<span>Plugin is missing a feature that my project requires.</span>';
										deactivate_content += '<div class="ec-admin-deactivate-reason" id="wpeasycart_reason4_row">';
											deactivate_content += '<span class="ec-admin-deactivate-reason-title"></span>';
											deactivate_content += '<input type="text" id="wpeasycart_reason4_extra" placeholder="What feature is missing?">';
										deactivate_content += '</div>';
									deactivate_content += '</label></li>';
									
									deactivate_content += '<li><label>';
										deactivate_content += '<span><input type="radio" name="wpeasycart_deactivate_reason" id="wpeasycart_reason5" value="5" onchange="wpeasycart_deactivate_reason_update( );"></span>';
										deactivate_content += '<span>It\'s a temporary deactivation. I\'m just debugging an issue.</span>';
									deactivate_content += '</label></li>';
									
									deactivate_content += '<li><label>';
										deactivate_content += '<span><input type="radio" name="wpeasycart_deactivate_reason" id="wpeasycart_reason6" value="6" onchange="wpeasycart_deactivate_reason_update( );"></span>';
										deactivate_content += '<span>Other.</span>';
										deactivate_content += '<div class="ec-admin-deactivate-reason" id="wpeasycart_reason6_row">';
											deactivate_content += '<span class="ec-admin-deactivate-reason-title">Please tell us the reason so we can improve.</span>';
											deactivate_content += '<input type="text" id="wpeasycart_reason6_extra">';
										deactivate_content += '</div>';
									deactivate_content += '</label></li>';
									
								deactivate_content += '</ul>';
							deactivate_content += '</div>';
							deactivate_content += '<div class="ec-admin-deactivate-box-footer">';
								deactivate_content += '<label class="ec-admin-deactivate-anonymous"><strong>Feedback is Anonymous</strong></label>';
								deactivate_content += '<a href="' + jQuery( this ).attr( 'href' ) + '" class="button button-secondary button-deactivate allow-deactivate" onclick="return wpeasycart_deactivate_submit( );">Skip & Deactivate</a>';
								deactivate_content += '<a href="#" class="button button-primary button-close" onclick="wpeasycart_deactivate_cancel( );">Cancel</a>';
							deactivate_content += '</div>';
						deactivate_content += '</div>';
					deactivate_content += '</div>';
					
					jQuery( 'body' ).prepend( deactivate_content );
				} );
            } );
		}
	} );
} );

function wpeasycart_deactivate_reason_update( ){
	var reason1 = jQuery( '#wpeasycart_reason1' ).is( ':checked' );
	var reason2 = jQuery( '#wpeasycart_reason2' ).is( ':checked' );
	var reason2_extra = jQuery( '#wpeasycart_reason2_extra' ).val( );
	var reason3 = jQuery( '#wpeasycart_reason3' ).is( ':checked' );
	var reason4 = jQuery( '#wpeasycart_reason4' ).is( ':checked' );
	var reason4_extra = jQuery( '#wpeasycart_reason4_extra' ).val( );
	var reason5 = jQuery( '#wpeasycart_reason5' ).is( ':checked' );
	var reason6 = jQuery( '#wpeasycart_reason6' ).is( ':checked' );
	var reason6_extra = jQuery( '#wpeasycart_reason6_extra' ).val( );
	
	if( reason1 || reason2 || reason3 || reason4 || reason5 || reason6 ){
		jQuery( '.ec-admin-deactivate-box-footer > a.allow-deactivate' ).html( 'Submit & Deactivate' );
	}
	
	if( reason2 ){
		jQuery( '#wpeasycart_reason2_row' ).show( );
		jQuery( '#wpeasycart_reason4_row' ).hide( );
		jQuery( '#wpeasycart_reason6_row' ).hide( );
	
	}else if( reason4 ){
		jQuery( '#wpeasycart_reason2_row' ).hide( );
		jQuery( '#wpeasycart_reason4_row' ).show( );
		jQuery( '#wpeasycart_reason6_row' ).hide( );
	}else if( reason6 ){
		jQuery( '#wpeasycart_reason2_row' ).hide( );
		jQuery( '#wpeasycart_reason4_row' ).hide( );
		jQuery( '#wpeasycart_reason6_row' ).show( );
	}else{
		jQuery( '#wpeasycart_reason2_row' ).hide( );
		jQuery( '#wpeasycart_reason4_row' ).hide( );
		jQuery( '#wpeasycart_reason6_row' ).hide( );
	}
}

function wpeasycart_deactivate_cancel( ){
	jQuery( '.ec-admin-deactivate-container' ).remove( );
}

function wpeasycart_deactivate_submit( ){
	var reason = 0;
	if( jQuery( '#wpeasycart_reason1' ).is( ':checked' ) )			reason = 1;
	else if( jQuery( '#wpeasycart_reason2' ).is( ':checked' ) )		reason = 2;
	else if( jQuery( '#wpeasycart_reason3' ).is( ':checked' ) )		reason = 3;
	else if( jQuery( '#wpeasycart_reason4' ).is( ':checked' ) )		reason = 4;
	else if( jQuery( '#wpeasycart_reason5' ).is( ':checked' ) )		reason = 5;
	else if( jQuery( '#wpeasycart_reason6' ).is( ':checked' ) )		reason = 6;
	
	if( reason > 0 ){
		
		var data = {
			action: 'ec_admin_ajax_custom_deactivate',
			reason: reason
		};
		if( reason == 2 )
			data.plugin = jQuery( '#wpeasycart_reason2_extra' ).val( );
		
		else if( reason == 4 )
			data.feature = jQuery( '#wpeasycart_reason4_extra' ).val( );
		
		else if( reason == 6 )
			data.other = jQuery( '#wpeasycart_reason6_extra' ).val( );
		
		jQuery( '.ec-admin-deactivate-container' ).remove( );
		jQuery.ajax({url: ajax_object.ajax_url, type: 'post', data: data, success: function( data ){ } } );
	}
	return true;
}