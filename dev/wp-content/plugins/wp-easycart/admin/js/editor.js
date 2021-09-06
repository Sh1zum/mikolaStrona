// JavaScript Document
(function() {
    tinymce.create('tinymce.plugins.wpeasycart', {
        /**
         * Initializes the plugin, this will be executed after the plugin has been created.
         * This call is done before the editor instance has finished it's initialization so use the onInit event
         * of the editor instance to intercept that event.
         *
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init : function(ed, url) {
			ed.addButton('ec_show_editor', {
				title : 'Insert EasyCart Item',
				cmd : 'ec_show_editor',
				icon: ' dashicons-before dashicons-cart'
			});
			
			ed.addCommand('ec_show_editor', function() {
                jQuery( '#ec_editor_window' ).show( );
				jQuery( '#ec_editor_bg' ).show( );
				//var selected_text = ed.selection.getContent();
				//var return_text = '[ec_store]';
                //return_text = '<span class="dropcap">' + selected_text + '</span>';
                //ed.execCommand('mceInsertContent', 0, return_text);
            });
        },
 
        /**
         * Creates control instances based in the incomming name. This method is normally not
         * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
         * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
         * method can be used to create those.
         *
         * @param {String} n Name of the control to create.
         * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
         * @return {tinymce.ui.Control} New control instance or null if no control was created.
         */
        createControl : function(n, cm) {
            return null;
        },
 
        /**
         * Returns information about the plugin as a name/value array.
         * The current keys are longname, author, authorurl, infourl and version.
         *
         * @return {Object} Name/value array containing information about the plugin.
         */
        getInfo : function() {
            return {
                longname : 'WP EasyCart Buttons',
                author : 'WP EasyCart',
                authorurl : 'http://wpeasycart.com',
                infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/example',
                version : "0.1"
            };
        }
    });
 
    // Register plugin
    tinymce.PluginManager.add( 'wpeasycart', tinymce.plugins.wpeasycart );
})();

/***************************************************************************
/ Basic EasyCart Shortcode Editor Functions
/****************************************************************************/
//Close Editor/Reset Editor
function ec_close_editor( ){
	jQuery( '#ec_editor_window' ).hide( );
	jQuery( '#ec_editor_bg' ).hide( );
	ec_editor_hide_panels( );
}

//Function for onclick of any shortcode button
jQuery( '.ec_column_holder li' ).click( function( ){
	var panel = jQuery(this).attr( "data-ecshortcode" );
	ec_editor_show_panel( panel );
});

//Function to show the correct panel based on the button clicked
function ec_editor_show_panel( panel ){
	if( panel == "ec_store" ){
		// Just add the store shortcode and hide the popup
		tinyMCE.activeEditor.execCommand( 'mceInsertContent', 0, "[ec_store]");
		ec_close_editor( );
	}else if( panel == "ec_categories" ){
		// Hide shorcode menu, show the menu add panel
		jQuery( '#ec_shortcode_menu' ).hide( );
		jQuery( '#ec_categories' ).show( );
	}else if( panel == "ec_category_view" ){
		// Hide shorcode menu, show the menu add panel
		jQuery( '#ec_shortcode_menu' ).hide( );
		jQuery( '#ec_category_view' ).show( );
	}else if( panel == "ec_store_table" ){
		// Hide shorcode menu, show the menu add panel
		jQuery( '#ec_shortcode_menu' ).hide( );
		jQuery( '#ec_store_table' ).show( );
	}else if( panel == "ec_menu" ){
		// Hide shorcode menu, show the menu add panel
		jQuery( '#ec_shortcode_menu' ).hide( );
		jQuery( '#ec_product_menu' ).show( );
	}else if( panel == "ec_category" ){
		// Hide shorcode menu, show the category add panel
		jQuery( '#ec_shortcode_menu' ).hide( );
		jQuery( '#ec_product_category' ).show( );
		
	}else if( panel == "ec_manufacturer" ){
		// Hide shorcode menu, show the category add panel
		jQuery( '#ec_shortcode_menu' ).hide( );
		jQuery( '#ec_manufacturer_group' ).show( );
		
	}else if( panel == "ec_productdetails" ){
		// Hide shorcode menu, show the category add panel
		jQuery( '#ec_shortcode_menu' ).hide( );
		jQuery( '#ec_productdetails_menu' ).show( );
		
	}else if( panel == "ec_cart" ){
		// Just add the cart shortcode and hide the popup
		tinyMCE.activeEditor.execCommand( 'mceInsertContent', 0, "[ec_cart]");
		ec_close_editor( );
		
	}else if( panel == "ec_account" ){
		// Just add the account shortcode and hide the popup
		tinyMCE.activeEditor.execCommand( 'mceInsertContent', 0, "[ec_account]");
		ec_close_editor( );
		
	}else if( panel == "ec_singleitem" ){
		jQuery( '#ec_shortcode_menu' ).hide( );
		jQuery( '#ec_single_product' ).show( );
		
	}else if( panel == "ec_selecteditems" ){
		jQuery( '#ec_shortcode_menu' ).hide( );
		jQuery( '#ec_multiple_products' ).show( );
		
	}else if( panel == "ec_addtocart" ){
		jQuery( '#ec_shortcode_menu' ).hide( );
		jQuery( '#ec_add_to_cart' ).show( );
		
	}else if( panel == "ec_cartdisplay" ){
		// Just add the card display shortcode and hide the popup
		tinyMCE.activeEditor.execCommand( 'mceInsertContent', 0, "[ec_cartdisplay]");
		ec_close_editor( );
	}else if( panel == "ec_membership" ){
		jQuery( '#ec_shortcode_menu' ).hide( );
		jQuery( '#ec_membership_menu' ).show( );
		
	}
}

jQuery( '.ec_editor_button.backlink' ).click( function( ){
	ec_editor_hide_panels( );
});

function ec_editor_hide_panels( ){
	jQuery( '#ec_shortcode_menu' ).show( );
	jQuery( '#ec_categories' ).hide( );
	jQuery( '#ec_category_view' ).hide( );
	jQuery( '#ec_store_table' ).hide( );
	jQuery( '#ec_product_menu' ).hide( );
	jQuery( '#ec_product_category' ).hide( );
	jQuery( '#ec_manufacturer_group' ).hide( );
	jQuery( '#ec_productdetails_menu' ).hide( );
	jQuery( '#ec_single_product' ).hide( );
	jQuery( '#ec_multiple_products' ).hide( );
	jQuery( '#ec_add_to_cart' ).hide( );
	jQuery( '#ec_membership_menu' ).hide( );
}

/***************************************************************************
/ Insert Product Menu Shortcode Functions
/****************************************************************************/
//Submit function for adding Menu Item Shortcode
jQuery( '#ec_add_product_menu' ).click( function( ){
	var menuid = jQuery( '#ec_editor_menu_select' ).val( );
	var submenuid = jQuery( '#ec_editor_submenu_select' ).val( );
	var subsubmenuid = jQuery( '#ec_editor_subsubmenu_select' ).val( );
	
	if( subsubmenuid > 0 ){
		tinyMCE.activeEditor.execCommand( 'mceInsertContent', 0, "[ec_store subsubmenuid=\"" + subsubmenuid + "\"]" );
		ec_close_editor( );
		ec_reset_product_menu( );
	}else if( submenuid > 0 ){
		tinyMCE.activeEditor.execCommand( 'mceInsertContent', 0, "[ec_store submenuid=\"" + submenuid + "\"]" );
		ec_close_editor( );
		ec_reset_product_menu( );
	}else if( menuid > 0 ){
		tinyMCE.activeEditor.execCommand( 'mceInsertContent', 0, "[ec_store menuid=\"" + menuid + "\"]" );
		ec_close_editor( );
		ec_reset_product_menu( );
	}else{
		//show error
		jQuery( '#ec_product_menu_error' ).show( );
	}
});

//On change function for the product menu select box
function ec_editor_select_menu_change( ){
	var menuid = jQuery( '#ec_editor_menu_select' ).val( );
	jQuery( '#ec_editor_submenu_holder' ).html( "loading sub menu items..." );
	var data = {
		action: 'ec_editor_update_sub_menu',
		id: 'ec_editor_submenu_select',
		menuid: menuid
	};

	jQuery.post( ajaxurl, data, function( response ){
		jQuery( '#ec_editor_submenu_holder' ).html( response );
		ec_editor_select_submenu_change( );
	});
}

//On change function for the product sub menu select box
function ec_editor_select_submenu_change( ){
	var submenuid = jQuery( '#ec_editor_submenu_select' ).val( );
	jQuery( '#ec_editor_subsubmenu_holder' ).html( "loading subsub menu items..." );
	var data = {
		action: 'ec_editor_update_subsub_menu',
		id: 'ec_editor_subsubmenu_select',
		submenuid: submenuid
	};

	jQuery.post( ajaxurl, data, function( response ){
		jQuery( '#ec_editor_subsubmenu_holder' ).html( response );
	});
}

//Function to reset the product menu panel
function ec_reset_product_menu( ){
	jQuery( '#ec_product_menu_error' ).hide( );
	jQuery( '#ec_editor_menu_select' ).val( "0" );
	jQuery( '#ec_editor_submenu_select' ).val( "0" );
	jQuery( '#ec_editor_subsubmenu_select' ).val( "0" );
}

/***************************************************************************
/ Insert Product Category Shortcode Functions
/****************************************************************************/
//Submit function for adding product category shortcode
jQuery( '#ec_add_product_category' ).click( function( ){
	var groupid = jQuery( '#ec_editor_category_select' ).val( );
	
	if( groupid > 0 ){
		tinyMCE.activeEditor.execCommand( 'mceInsertContent', 0, "[ec_store groupid=\"" + groupid + "\"]" );
		ec_close_editor( );
		ec_reset_product_category( );
	}else{
		//show error
		jQuery( '#ec_product_category_error' ).show( );
	}
});

//Function to reset the product category panel
function ec_reset_product_category( ){
	jQuery( '#ec_product_category_error' ).hide( );
	jQuery( '#ec_editor_category_select' ).val( "0" );
}

/***************************************************************************
/ Insert Manufacturer Group Shortcode Functions
/****************************************************************************/
//Submit function for adding product category shortcode
jQuery( '#ec_add_manufacturer_group' ).click( function( ){
	var manufacturerid = jQuery( '#ec_editor_manufacturer_select' ).val( );
	
	if( manufacturerid > 0 ){
		tinyMCE.activeEditor.execCommand( 'mceInsertContent', 0, "[ec_store manufacturerid=\"" + manufacturerid + "\"]" );
		ec_close_editor( );
		ec_reset_manufacturer_group( );
	}else{
		//show error
		jQuery( '#ec_manufacturer_group_error' ).show( );
	}
});

//Function to reset the product category panel
function ec_reset_manufacturer_group( ){
	jQuery( '#ec_manufacturer_group_error' ).hide( );
	jQuery( '#ec_editor_manufacturer_select' ).val( "0" );
}

/***************************************************************************
/ Insert Product Details Shortcode Functions
/****************************************************************************/
//Submit function for adding product details shortcode
jQuery( '#ec_add_productdetails' ).click( function( ){
	var model_number = jQuery( '#ec_editor_productdetails_select' ).val( );
	
	if( model_number != "0" ){
		tinyMCE.activeEditor.execCommand( 'mceInsertContent', 0, "[ec_store modelnumber=\"" + model_number + "\"]" );
		ec_close_editor( );
		ec_reset_productdetails( );
	}else{
		//show error
		jQuery( '#ec_productdetails_error' ).show( );
	}
});

//Function to reset the product category panel
function ec_reset_productdetails( ){
	jQuery( '#ec_productdetails_error' ).hide( );
	jQuery( '#ec_editor_productdetails_select' ).val( "0" );
}

/***************************************************************************
/ Insert Single Product Shortcode Functions
/****************************************************************************/
//Submit function for adding product category shortcode
jQuery( '#ec_add_single_product' ).click( function( ){
	var productid = jQuery( '#ec_editor_single_product_select' ).val( );
	var display_type = jQuery( '#ec_editor_single_product_display_type' ).val( );
	
	if( productid > 0 ){
		tinyMCE.activeEditor.execCommand( 'mceInsertContent', 0, "[ec_product productid=\"" + productid + "\" style=\"" + display_type + "\"]" );
		ec_close_editor( );
		ec_reset_single_product( );
	}else{
		//show error
		jQuery( '#ec_single_product_error' ).show( );
	}
});

//Function to reset the product category panel
function ec_reset_single_product( ){
	jQuery( '#ec_single_product_error' ).hide( );
	jQuery( '#ec_editor_single_product_select' ).val( "0" );
}

/***************************************************************************
/ Insert Multiple Products Shortcode Functions
/****************************************************************************/
//Submit function for adding product category shortcode
jQuery( '#ec_add_multiple_products' ).click( function( ){
	var productids = jQuery( '#ec_editor_multiple_products_select' ).val( );
	var display_type = jQuery( '#ec_editor_multiple_products_display_type' ).val( );
	var columns = 0;
	if( jQuery( '#ec_editor_multiple_products_columns' ).length ){
		columns = jQuery( '#ec_editor_multiple_products_columns' ).val( );
	}
	var selected_products = ( productids.length > 0 ) ? productids.join( ',' ) : '';
	var added = productids.length;
	
    if( added > 0 ){
		var shortcode = "[ec_product productid=\"" + selected_products + "\" style=\"" + display_type + "\" ";
		if( columns ){
			shortcode = shortcode + "columns=\"" + columns + "\"";
		}
		shortcode = shortcode + "]";
		tinyMCE.activeEditor.execCommand( 'mceInsertContent', 0, shortcode );
		ec_close_editor( );
		ec_reset_multiple_products( );
	}else{
		//show error
		jQuery( '#ec_multiple_products_error' ).show( );
	}
});

//Function to reset the product category panel
function ec_reset_multiple_products( ){
	jQuery( '#ec_multiple_products_error' ).hide( );
	jQuery( '#ec_editor_multiple_products_select > option' ).attr( "selected", false );
}

/***************************************************************************
/ Insert Add to Cart Shortcode Functions
/****************************************************************************/
//Submit function for adding product category shortcode
jQuery( '#ec_add_add_to_cart' ).click( function( ){
	var productid = jQuery( '#ec_editor_add_to_cart_product_select' ).val( );
	
	if( productid > 0 ){
		tinyMCE.activeEditor.execCommand( 'mceInsertContent', 0, "[ec_addtocart productid=\"" + productid + "\"]" );
		ec_close_editor( );
		ec_reset_add_to_cart( );
	}else{
		//show error
		jQuery( '#ec_add_to_cart_error' ).show( );
	}
});

//Function to reset the product category panel
function ec_reset_add_to_cart( ){
	jQuery( '#ec_add_to_cart_error' ).hide( );
	jQuery( '#ec_editor_add_to_cart_product_select' ).val( "0" );
}

/***************************************************************************
/ Insert Membership Content Shortcode Functions
/****************************************************************************/
//Submit function for adding the membership content shortcode
jQuery( '#ec_add_membership' ).click( function( ){
	var productids = jQuery( '#ec_editor_membership_multiple_product_select' ).val( );
	var selected_products = productids.join( ',' ); 
	var added = productids.length;
    
	jQuery( '#ec_editor_membership_multiple_product_select option' ).each( function( ){
		if( jQuery( this ).attr( "selected" ) ){
			if( added ){
				selected_products = selected_products + "," + jQuery( this ).val( );
			}else{
				selected_products = jQuery( this ).val( );
			}
			added++;
		}
	});
	
	if( added > 0 ){
		tinyMCE.activeEditor.execCommand( 'mceInsertContent', 0, "[ec_membership productid=\"" + selected_products + "\"]MEMBER CONTENT HERE[/ec_membership][ec_membership_alt productid=\"" + selected_products + "\"]NON-MEMBER NOTICE HERE[/ec_membership_alt]" );
		ec_close_editor( );
		ec_reset_multiple_membership_products( );
	}else{
		//show error
		jQuery( '#ec_membership_error' ).show( );
	}
});

//Function to reset the product category panel
function ec_reset_multiple_membership_products( ){
	jQuery( '#ec_membership_error' ).hide( );
	jQuery( '#ec_editor_membership_multiple_product_select > option' ).attr( "selected", false );
}

/***************************************************************************
/ Insert Store Table Shortcode Functions
/****************************************************************************/
//Submit function for adding product category shortcode
jQuery( '#ec_add_store_table' ).click( function( ){
	var productids = ec_editor_get_multiple_select_string( 'ec_editor_table_product_select' );
	var menuids = ec_editor_get_multiple_select_string( 'ec_editor_table_menu_select' );
	var submenuids = ec_editor_get_multiple_select_string( 'ec_editor_table_submenu_select' );
	var subsubmenuids = ec_editor_get_multiple_select_string( 'ec_editor_table_subsubmenu_select' );
	var categoryids = ec_editor_get_multiple_select_string( 'ec_editor_table_category_select' );
	
	// Get Column Information
	var column0_label = jQuery( '#ec_editor_table_label_0' ).val( );
	var column1_label = jQuery( '#ec_editor_table_label_1' ).val( );
	var column2_label = jQuery( '#ec_editor_table_label_2' ).val( );
	var column3_label = jQuery( '#ec_editor_table_label_3' ).val( );
	
	var column0_field = jQuery( '#ec_editor_table_field_0' ).val( );
	var column1_field = jQuery( '#ec_editor_table_field_1' ).val( );
	var column2_field = jQuery( '#ec_editor_table_field_2' ).val( );
	var column3_field = jQuery( '#ec_editor_table_field_3' ).val( );
	
	var view_details_text = jQuery( '#ec_editor_table_view_details_text' ).val( );
	
	var shortcode = "[ec_store_table productid=\"" + productids + "\" menuid=\"" + menuids + "\" submenuid=\"" + submenuids + "\" subsubmenuid=\"" + subsubmenuids + "\" categoryid=\"" + categoryids + "\" labels=\"" + column0_label + "," + column1_label + "," + column2_label + "," + column3_label + "\" columns=\"" + column0_field + "," + column1_field + "," + column2_field + "," + column3_field + "\" view_details=\"" + view_details_text + "\"]";
	tinyMCE.activeEditor.execCommand( 'mceInsertContent', 0, shortcode );
	ec_close_editor( );
	
	ec_editor_reset_multiple_select( 'ec_editor_table_product_select' );
	ec_editor_reset_multiple_select( 'ec_editor_table_menu_select' );
	ec_editor_reset_multiple_select( 'ec_editor_table_submenu_select' );
	ec_editor_reset_multiple_select( 'ec_editor_table_subsubmenu_select' );
	ec_editor_reset_multiple_select( 'ec_editor_table_category_select' );
	
	jQuery( '#ec_editor_table_label_0' ).val( "SKU" );
	jQuery( '#ec_editor_table_label_1' ).val( "Product Name" );
	jQuery( '#ec_editor_table_label_2' ).val( "Price" );
	jQuery( '#ec_editor_table_label_3' ).val( "More" );
	
	jQuery( '#ec_editor_table_field_0' ).val( "model_number" );
	jQuery( '#ec_editor_table_field_1' ).val( "title" );
	jQuery( '#ec_editor_table_field_2' ).val( "price" );
	jQuery( '#ec_editor_table_field_3' ).val( "details_link" );
	
	jQuery( '#ec_editor_table_view_details_text' ).val( "view more" );
	
});

/***************************************************************************
/ Insert Categories Shortcode Functions
/****************************************************************************/
//Submit function for adding product category shortcode
jQuery( '#ec_add_categories' ).click( function( ){
	var groupid = jQuery( '#ec_editor_categories_category_select' ).val( );

	tinyMCE.activeEditor.execCommand( 'mceInsertContent', 0, "[ec_categories groupid=\"" + groupid + "\"]" );
	ec_close_editor( );
	ec_reset_categories( );
	
});

//Function to reset the product category panel
function ec_reset_categories( ){
	jQuery( '#ec_editor_categories_category_select' ).val( "0" );
}

/***************************************************************************
/ Insert Category View Shortcode Functions
/****************************************************************************/
//Submit function for adding product category shortcode
jQuery( '#ec_add_category_view' ).click( function( ){
	var groupid = jQuery( '#ec_editor_category_view_category_select' ).val( );
	var columns = jQuery( '#ec_editor_category_view_columns' ).val( );

	tinyMCE.activeEditor.execCommand( 'mceInsertContent', 0, "[ec_category_view parentid=\"" + groupid + "\" columns=\"" + columns + "\"]" );
	ec_close_editor( );
	ec_reset_category_view( );
	
});

function ec_editor_get_multiple_select_string( id ){
	var columns = 0;
	if( jQuery( '#' + id ).length ){
		columns = jQuery( '#' + id ).val( );
	}
	var selected_items = ""; 
	var added = 0;

	jQuery( '#' + id + ' option' ).each( function( ){
		if( jQuery( this ).attr( "selected" ) ){
			if( added ){
				selected_items = selected_items + "," + jQuery( this ).val( );
			}else{
				selected_items = jQuery( this ).val( );
			}
			added++;
		}
	});
	return selected_items;
}

function ec_editor_reset_multiple_select( id ){
	jQuery( '#' + id + ' > option' ).attr( "selected", false );
}