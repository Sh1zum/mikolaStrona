/* GUTENBERG EDITOR */
jQuery( document ).ready( function( ){
    ( function( blocks, i18n, element ) {
		var el = element.createElement;
		//var children = wp.blocks.source.children;
		var BlockControls = wp.blocks.BlockControls;
		var AlignmentToolbar = wp.blocks.AlignmentToolbar;
		var MediaUpload = wp.blocks.MediaUpload;
		var InspectorControls = wp.blocks.InspectorControls;
		var TextControl = wp.components.TextControl;
		var SelectControl = wp.components.SelectControl;
		var CheckboxControl = wp.components.CheckboxControl;
		var BaseControl = wp.components.BaseControl;
		var WPEasyCartShortCodes = [
		  { value: 'ec_store', label: i18n.__( 'Store' ) },
		  { value: 'ec_cart', label: i18n.__( 'Cart' ) },
		  { value: 'ec_account', label: i18n.__( 'Account' ) },
		  { value: 'ec_categories', label: i18n.__( 'Category Standard Display' ) },
		  { value: 'ec_category_view', label: i18n.__( 'Category Grid Display' ) },
		  { value: 'ec_store_table', label: i18n.__( 'Store Table Display' ) },
		  { value: 'ec_product', label: i18n.__( 'Product Display' ) },
		  { value: 'ec_addtocart', label: i18n.__( 'Add to Cart Button' ) },
		  { value: 'ec_cartdisplay', label: i18n.__( 'Cart Display' ) },
		  { value: 'ec_membership', label: i18n.__( 'Membership Content' ) }
		];
		var WPEasyCartTableColumns = [
			{ value: 'product_id', label: i18n.__( 'Product ID' ) },
			{ value: 'model_number', label: i18n.__( 'Model Number' ) },
			{ value: 'title', label: i18n.__( 'Title' ) },
			{ value: 'price', label: i18n.__( 'Price' ) },
			{ value: 'details_link', label: i18n.__( 'Details Link' ) },
			{ value: 'description', label: i18n.__( 'Description' ) },
			{ value: 'specifications', label: i18n.__( 'Specifications' ) },
			{ value: 'stock_quantity', label: i18n.__( 'Stock Quantity' ) },
			{ value: 'weight', label: i18n.__( 'Weight' ) },
			{ value: 'width', label: i18n.__( 'Width' ) },
			{ value: 'height', label: i18n.__( 'Height' ) },
			{ value: 'length', label: i18n.__( 'Length' ) },
		];
		
		blocks.registerBlockType('wp-easycart/shortcode', {
			title: i18n.__( 'WP EasyCart' ), // The title of our block.
			icon: 'cart', // Dashicon icon for our block
			category: 'common', // The category of the block.
			attributes: { // Necessary for saving block content.
				shortcode_type: {
					type: 'select',
					default: 'ec_store'
				},
				store_filter_type: {
					typle: 'select',
					default: ''
				},
				store_category: {
					type: 'select',
					default: '',
				},
				store_manufacturer: {
					type: 'select',
					default: '',
				},
				store_menulevel1: {
					type: 'select',
					default: '',
				},
				store_menulevel2: {
					type: 'select',
					default: '',
				},
				store_menulevel3: {
					type: 'select',
					default: '',
				},
				store_product: {
					type: 'select',
					default: '',
				},
				account_redirect: {
					type: 'url',
					default: '',
				},
				categories_category: {
					type: 'select',
					default: '0',
				},
				category_view_category: {
					type: 'select',
					default: '0',
				},
				category_view_columns: {
					type: 'select',
					default: '3',
				},
				store_table_products: {
					type: 'select',
					default: '',
				},
				store_table_menulevel1: {
					type: 'select',
					default: '',
				},
				store_table_menulevel2: {
					type: 'select',
					default: '',
				},
				store_table_menulevel3: {
					type: 'select',
					default: '',
				},
				store_table_category: {
					type: 'select',
					default: '',
				},
				store_table_label1: {
					type: 'string',
					default: '',
				},
				store_table_column1: {
					type: 'select',
					default: '',
				},
				store_table_label2: {
					type: 'string',
					default: '',
				},
				store_table_column2: {
					type: 'select',
					default: '',
				},
				store_table_label3: {
					type: 'string',
					default: '',
				},
				store_table_column3: {
					type: 'select',
					default: '',
				},
				store_table_label4: {
					type: 'string',
					default: '',
				},
				store_table_column4: {
					type: 'select',
					default: '',
				},
				store_table_label5: {
					type: 'string',
					default: '',
				},
				store_table_column5: {
					type: 'select',
					default: '',
				},
				store_table_link_label: {
					type: 'string',
					default: '',
				},
				product_product: {
					type: 'select',
					default: '',
				},
				product_display_type: {
					type: 'select',
					default: '',
				},
				addtocart_product: {
					type: 'select',
					default: '',
				},
				membership_products: {
					type: 'select',
					default: '',
				}
			},
			edit: function( props ) {
                var focus = props.isSelected;
				var attributes = props.attributes;
                
				function onChangeShortcodeType( update_shortcode_type ) {
					props.setAttributes( { shortcode_type: update_shortcode_type } );
					
                    var ele = jQuery( document.getElementById( 'block-' + props.clientId ) );
                    
					ele.find( '.wp-easycart-block-item' ).each( function( ){
                        jQuery( this ).removeClass( 'hidden' ).addClass( 'hidden' );
					} );
					
					// Show Correct Fields
					if( attributes.shortcode_type == 'ec_store' ){
						ele.find( 'div.wp-easycart-store-shortcode' ).each( function( ){
							jQuery( this ).removeClass( 'hidden' )
						} );
						
						
					}else if( attributes.shortcode_type == 'ec_cart' ){
						// No Options
					
					}else if( attributes.shortcode_type == 'ec_account' ){
						ele.find( 'div.wp-easycart-account-shortcode' ).each( function( ){
							jQuery( this ).removeClass( 'hidden' )
						} );
					
					}else if( attributes.shortcode_type == 'ec_categories' ){
						ele.find( 'div.wp-easycart-categories-shortcode' ).each( function( ){
							jQuery( this ).removeClass( 'hidden' )
						} );
						
					}else if(  attributes.shortcode_type == 'ec_category_view' ){
						ele.find( 'div.wp-easycart-category-view-shortcode' ).each( function( ){
							jQuery( this ).removeClass( 'hidden' )
						} );
						
					}else if(  attributes.shortcode_type == 'ec_store_table' ){
						ele.find( 'div.wp-easycart-store-table-shortcode' ).each( function( ){
							jQuery( this ).removeClass( 'hidden' )
						} );
					
					}else if( attributes.shortcode_type == 'ec_product' ){
						ele.find( 'div.wp-easycart-product-shortcode' ).each( function( ){
							jQuery( this ).removeClass( 'hidden' )
						} );
						
					}else if( attributes.shortcode_type == 'ec_addtocart' ){
						ele.find( 'div.wp-easycart-addtocart-shortcode' ).each( function( ){
							jQuery( this ).removeClass( 'hidden' )
						} );
						
					}else if( attributes.shortcode_type == 'ec_cartdisplay' ){
						// No Options
					
					}else if( attributes.shortcode_type == 'ec_membership' ){
						ele.find( 'div.wp-easycart-membership-shortcode' ).each( function( ){
							jQuery( this ).removeClass( 'hidden' )
						} );
					}
				}
				
				function onChangeStoreFilters( changedVal ){
					props.setAttributes( { store_filter_type: changedVal } );
					
					var ele = jQuery( document.getElementById( 'block-' + props.clientId ) );
                    
                    jQuery( this ).find( 'div.wp-easycart-filter-category' ).each( function( ){
						if( jQuery( this ).parent( ).parent( ).hasClass( 'components-base-control' ) ){
                            jQuery( this ).parent( ).parent( ).hide( );
                        }
					} );
					
					jQuery( this ).find( 'div.wp-easycart-filter-menu' ).each( function( ){
						if( jQuery( this ).parent( ).parent( ).hasClass( 'components-base-control' ) ){
                            jQuery( this ).parent( ).parent( ).hide( );
                        }
					} );
					
					jQuery( this ).find( 'div.wp-easycart-filter-manufacturer' ).each( function( ){
						if( jQuery( this ).parent( ).parent( ).hasClass( 'components-base-control' ) ){
                            jQuery( this ).parent( ).parent( ).hide( );
                        }
					} );
					
					jQuery( this ).find( 'div.wp-easycart-filter-product' ).each( function( ){
						if( jQuery( this ).parent( ).parent( ).hasClass( 'components-base-control' ) ){
                            jQuery( this ).parent( ).parent( ).hide( );
                        }
					} );
					
					if( changedVal == 'category' ){
						jQuery( this ).find( 'div.wp-easycart-filter-category' ).each( function( ){
							jQuery( this ).removeClass( 'hidden' )
						} );
					
					}else if( changedVal == 'menu1' ){
						jQuery( this ).find( 'div.wp-easycart-filter-menu1' ).each( function( ){
							jQuery( this ).removeClass( 'hidden' )
						} );
					
					}else if( changedVal == 'menu2' ){
						jQuery( this ).find( 'div.wp-easycart-filter-menu2' ).each( function( ){
							jQuery( this ).removeClass( 'hidden' )
						} );
					
					}else if( changedVal == 'menu3' ){
						jQuery( this ).find( 'div.wp-easycart-filter-menu3' ).each( function( ){
							jQuery( this ).removeClass( 'hidden' )
						} );
					
					}else if( changedVal == 'manufacturer' ){
						jQuery( this ).find( 'div.wp-easycart-filter-manufacturer' ).each( function( ){
							jQuery( this ).removeClass( 'hidden' )
						} );
					
					}else if( changedVal == 'product' ){
						jQuery( this ).find( 'div.wp-easycart-filter-product' ).each( function( ){
							jQuery( this ).removeClass( 'hidden' )
						} );
					}
				}
				
				function onChangeStoreCategory( changedVal ){
					props.setAttributes( { store_category: changedVal } );
					props.setAttributes( { store_manufacturer: '' } );
					props.setAttributes( { store_menulevel1: '' } );
					props.setAttributes( { store_menulevel2: '' } );
					props.setAttributes( { store_menulevel3: '' } );
					props.setAttributes( { store_product: '' } );
				}
				
				function onChangeStoreManufacturer( changedVal ){
					props.setAttributes( { store_category: '' } );
					props.setAttributes( { store_manufacturer: changedVal } );
					props.setAttributes( { store_menulevel1: '' } );
					props.setAttributes( { store_menulevel2: '' } );
					props.setAttributes( { store_menulevel3: '' } );
					props.setAttributes( { store_product: '' } );
				}
				
				function onChangeStoreMenuLevel1( changedVal ){
					props.setAttributes( { store_category: '' } );
					props.setAttributes( { store_manufacturer: '' } );
					props.setAttributes( { store_menulevel1: changedVal } );
					props.setAttributes( { store_menulevel2: '' } );
					props.setAttributes( { store_menulevel3: '' } );
					props.setAttributes( { store_product: '' } );
				}
				
				function onChangeStoreMenuLevel2( changedVal ){
					props.setAttributes( { store_category: '' } );
					props.setAttributes( { store_manufacturer: '' } );
					props.setAttributes( { store_menulevel1: '' } );
					props.setAttributes( { store_menulevel2: changedVal } );
					props.setAttributes( { store_menulevel3: '' } );
					props.setAttributes( { store_product: '' } );
				}
				
				function onChangeStoreMenuLevel3( changedVal ){
					props.setAttributes( { store_category: '' } );
					props.setAttributes( { store_manufacturer: '' } );
					props.setAttributes( { store_menulevel1: '' } );
					props.setAttributes( { store_menulevel2: '' } );
					props.setAttributes( { store_menulevel3: changedVal } );
					props.setAttributes( { store_product: '' } );
				}
				
				function onChangeStoreProduct( changedVal ){
					props.setAttributes( { store_category: '' } );
					props.setAttributes( { store_manufacturer: '' } );
					props.setAttributes( { store_menulevel1: '' } );
					props.setAttributes( { store_menulevel2: '' } );
					props.setAttributes( { store_menulevel3: '' } );
					props.setAttributes( { store_product: changedVal } );
				}
				
				function onChangeAccountRedirect( changedVal ){
					props.setAttributes( { account_redirect: changedVal } );
				}
				
				function onChangeCategoriesCategory( changedVal ){
					props.setAttributes( { categories_category: changedVal } );
				}
				
				function onChangeCategoryViewCategory( changedVal ){
					props.setAttributes( { category_view_category: changedVal } );
				}
				
				function onChangeCategoryViewColumns( changedVal ){
					props.setAttributes( { category_view_columns: changedVal } );
				}
				
				function onChangeStoreTableProducts( e ){
					var val = e.target.value;
					
					var start_arr = props.attributes.store_table_products;
					console.log( start_arr );
					if( !start_arr ){
						start_arr = [];
					}
					if( start_arr.includes( val ) ){
						var f_index = start_arr.indexOf( val );
						if( f_index > -1 ){
							start_arr.splice( f_index, 1 );
						}
						e.target.checked = false;
					}else{
						e.target.checked = true;
						start_arr.push( val );
					}
					props.setAttributes({ store_table_products: start_arr });
				}
				
				function onChangeStoreTableMenulevel1( changedVal ){
					props.setAttributes( { store_table_menulevel1: changedVal } );
				}
				
				function onChangeStoreTableMenulevel2( changedVal ){
					props.setAttributes( { store_table_menulevel2: changedVal } );
				}
				
				function onChangeStoreTableMenulevel3( changedVal ){
					props.setAttributes( { store_table_menulevel3: changedVal } );
				}
				
				function onChangeStoreTableCategory( changedVal ){
					props.setAttributes( { store_table_categories: changedVal } );
				}
				
				function onChangeStoreTableLabel1( changedVal ){
					props.setAttributes( { store_table_label1: changedVal } );
				}
				
				function onChangeStoreTableColumn1( changedVal ){
					props.setAttributes( { store_table_column1: changedVal } );
				}
				
				function onChangeStoreTableLabel2( changedVal ){
					props.setAttributes( { store_table_label2: changedVal } );
				}
				
				function onChangeStoreTableColumn2( changedVal ){
					props.setAttributes( { store_table_column2: changedVal } );
				}
				
				function onChangeStoreTableLabel3( changedVal ){
					props.setAttributes( { store_table_label3: changedVal } );
				}
				
				function onChangeStoreTableColumn3( changedVal ){
					props.setAttributes( { store_table_column3: changedVal } );
				}
				
				function onChangeStoreTableLabel4( changedVal ){
					props.setAttributes( { store_table_label4: changedVal } );
				}
				
				function onChangeStoreTableColumn4( changedVal ){
					props.setAttributes( { store_table_column4: changedVal } );
				}
				
				function onChangeStoreTableLabel5( changedVal ){
					props.setAttributes( { store_table_label5: changedVal } );
				}
				
				function onChangeStoreTableColumn5( changedVal ){
					props.setAttributes( { store_table_column5: changedVal } );
				}
				
				function onChangeStoreTableLinkLabel( changedVal ){
					props.setAttributes( { store_table_link_label: changedVal } );
				}
				
				function onChangeProductProduct( changedVal ){
					props.setAttributes( { product_product: changedVal } );
				}
				
				function onChangeProductDisplayType( changedVal ){
					props.setAttributes( { product_display_type: changedVal } );
				}
				
				function onChangeAddToCartProduct( changedVal ){
					props.setAttributes( { addtocart_product: changedVal } );
				}
				
				function onChangeMembershipProducts( changedVal ){
					props.setAttributes( { membership_products: changedVal } );
				}
				
				function selected_shortcode_type( ){
					for( var i=0; i<WPEasyCartShortCodes.length; i++ ){
						if( WPEasyCartShortCodes[i].value == attributes.shortcode_type ){
							return WPEasyCartShortCodes[i].label;
						}
					}
				}
                
                function WPEasyCartTestChange( changedVal ){
                    //props.setAttributes( { testChangeVal: changedVal } );
                }
				
				function wpeasycart_gutenberg_checkbox_group( label, options, values, classes, onchange ){
					console.log( 'selected values: ' );
					console.log( values );
					return el(
                        'div',
                        {
                            className: 'wpeasycart-gt-group ' + classes
                        },
                        [ 
                            
                            el( 
                                'h3',
                                {inline:true},
                                label
                            ),
                            el(
                                'div',
                                { className: 'wpeasycart-gt-multiselect' },
                                [
                                    el(
                                        'div',
                                        {
                                            className: 'wpeasycart-gt-multiselect-container'
                                        },
                                        [    
                                            options.map( item => {
                                                return el(
                                                    CheckboxControl,
                                                    {
                                                        label: item.label,
                                                        value: item.value,
														checked: ( values.includes( item.value ) ) ? true : false,
														onClick: onchange,
														onChange: WPEasyCartTestChange,
														useState: true
                                                    }
                                                );
                                            } )
                                        ]
                                    )
                                ]
                            )
                        ]
                    );
				}
				
				return [
					!focus && el(
						'h3',
						{},
						'WP EasyCart Shortcode - ' + selected_shortcode_type( )
					),
					focus && el(
						SelectControl,
						{
							type: 'string',
							label: i18n.__( 'WP EasyCart Shortcode' ),
							value: attributes.shortcode_type,
							onChange: onChangeShortcodeType,
							options: WPEasyCartShortCodes
						}
					),
					focus && el (
						'h3',
						{className: ( ( attributes.shortcode_type != 'ec_store' ) ? 'hidden ' : '' ) + 'wp-easycart-store-shortcode'},
						'Add Product Filters'
					),
					focus && el(
						SelectControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_store' ) ? 'hidden ' : '' ) + 'wp-easycart-store-shortcode',
							type: 'string',
							label: i18n.__( 'Add Filters' ),
							value: attributes.store_filter_type,
							onChange: onChangeStoreFilters,
							options: [
								{ value: '', label: i18n.__( 'Show Featured Items Only' ) },
								{ value: 'category', label: i18n.__( 'Filter by Category' ) },
								{ value: 'menu1', label: i18n.__( 'Filter by Menu' ) },
								{ value: 'menu2', label: i18n.__( 'Filter by Sub-Menu' ) },
								{ value: 'menu3', label: i18n.__( 'Filter by Sub-Sub-Menu' ) },
								{ value: 'manufacturer', label: i18n.__( 'Filter by Manufacturer' ) },
								{ value: 'product', label: i18n.__( 'Filter by Product' ) },
							]
						}
					),
					focus && wp_easycart_categories.length < 2000 && el(
						SelectControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_store' || attributes.store_filter_type != 'category' ) ? 'hidden ' : '' ) + 'wp-easycart-filter-category wp-easycart-block-item',
							type: 'string',
							label: i18n.__( 'Category Filter' ),
							value: attributes.store_category,
							onChange: onChangeStoreCategory,
							options: [
							  { value: '', label: i18n.__( 'Show all Categories' ) },
							].concat( wp_easycart_categories )
						}
					),
					focus && wp_easycart_categories.length >= 2000 && el(
						TextControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_store' || attributes.store_filter_type != 'category' ) ? 'hidden ' : '' ) + 'wp-easycart-filter-category wp-easycart-block-item',
							tagName: 'input',
							label: i18n.__( 'Enter Category ID' ),
							value: attributes.store_category,
							onChange: onChangeStoreCategory
						}
					),
					focus && wp_easycart_manufacturers.length < 2000 && el(
						SelectControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_store' || attributes.store_filter_type != 'manufacturer' ) ? 'hidden ' : '' ) + 'wp-easycart-filter-manufacturer wp-easycart-block-item',
							type: 'string',
							label: i18n.__( 'Manufacturer Filter' ),
							value: attributes.store_manufacturer,
							onChange: onChangeStoreManufacturer,
							options: [
							  { value: '', label: i18n.__( 'Show all Manufacturers' ) },
							].concat( wp_easycart_manufacturers )
						}
					),
					focus && wp_easycart_manufacturers.length >= 2000 && el(
						TextControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_store' || attributes.store_filter_type != 'manufacturer' ) ? 'hidden ' : '' ) + 'wp-easycart-filter-manufacturer wp-easycart-block-item',
							tagName: 'input',
							label: i18n.__( 'Enter Manufacturer ID' ),
							value: attributes.store_manufacturer,
							onChange: onChangeStoreManufacturer
						}
					),
					focus && wp_easycart_menulevel1.length < 2000 && el(
						SelectControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_store' || attributes.store_filter_type != 'menu1' ) ? 'hidden ' : '' ) + 'wp-easycart-filter-menu1 wp-easycart-block-item',
							type: 'string',
							label: i18n.__( 'Menu Filter' ),
							value: attributes.store_menulevel1,
							onChange: onChangeStoreMenuLevel1,
							options: [
							  { value: '', label: i18n.__( 'Show all Menu' ) },
							].concat( wp_easycart_menulevel1 )
						}
					),
					focus && wp_easycart_menulevel1.length >= 2000 && el(
						TextControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_store' || attributes.store_filter_type != 'menu1' ) ? 'hidden ' : '' ) + 'wp-easycart-filter-menu1 wp-easycart-block-item',
							tagName: 'input',
							label: i18n.__( 'Enter Menu Level 1 ID' ),
							value: attributes.store_menulevel1,
							onChange: onChangeStoreMenuLevel1
						}
					),
					focus && wp_easycart_menulevel2.length < 2000 && el(
						SelectControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_store' || attributes.store_filter_type != 'menu2' ) ? 'hidden ' : '' ) + 'wp-easycart-filter-menu2 wp-easycart-block-item',
							type: 'string',
							label: i18n.__( 'Sub Menu Filter' ),
							value: attributes.store_menulevel2,
							onChange: onChangeStoreMenuLevel2,
							options: [
							  { value: '', label: i18n.__( 'Show all Sub Menu' ) },
							].concat( wp_easycart_menulevel2 )
						}
					),
					focus && wp_easycart_menulevel2.length >= 2000 && el(
						TextControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_store' || attributes.store_filter_type != 'menu2' ) ? 'hidden ' : '' ) + 'wp-easycart-filter-menu2 wp-easycart-block-item',
							tagName: 'input',
							label: i18n.__( 'Enter Menu Level 2 ID' ),
							value: attributes.store_menulevel2,
							onChange: onChangeStoreMenuLevel2
						}
					),
					focus && wp_easycart_menulevel3.length < 2000 && el(
						SelectControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_store' || attributes.store_filter_type != 'menu3' ) ? 'hidden ' : '' ) + 'wp-easycart-filter-menu3 wp-easycart-block-item',
							type: 'string',
							label: i18n.__( 'Sub Sub Menu Filter' ),
							value: attributes.store_menulevel3,
							onChange: onChangeStoreMenuLevel3,
							options: [
							  { value: '', label: i18n.__( 'Show all Sub Sub Menu' ) },
							].concat( wp_easycart_menulevel3 )
						}
					),
					focus && wp_easycart_menulevel3.length >= 2000 && el(
						SelectControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_store' || attributes.store_filter_type != 'menu3' ) ? 'hidden ' : '' ) + 'wp-easycart-filter-menu3 wp-easycart-block-item',
							tagName: 'input',
							label: i18n.__( 'Enter Menu Level 3 ID' ),
							value: attributes.store_menulevel3,
							onChange: onChangeStoreMenuLevel3
						}
					),
					focus && wp_easycart_products_model.length < 2000 && el(
						SelectControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_store' || attributes.store_filter_type != 'product' ) ? 'hidden ' : '' ) + 'wp-easycart-filter-product wp-easycart-block-item',
							type: 'string',
							label: i18n.__( 'Product to Display' ),
							value: attributes.store_product,
							onChange: onChangeStoreProduct,
							options: [
							  { value: '', label: i18n.__( 'No Product Filter' ) },
							].concat( wp_easycart_products_model )
						}
					),
					focus && wp_easycart_products_model.length >= 2000 && el(
						TextControl,
						{
							tagName: 'input',
							className: ( ( attributes.shortcode_type != 'ec_store' || attributes.store_filter_type != 'product' ) ? 'hidden ' : '' ) + 'wp-easycart-filter-product wp-easycart-block-item',
							label: i18n.__( 'Enter Product SKU' ),
							value: attributes.store_product,
							onChange: onChangeStoreProduct,
						}
					),
					focus && el(
						TextControl,
						{
							tagName: 'input',
							className: ( ( attributes.shortcode_type != 'ec_account' ) ? 'hidden ' : '' ) + 'wp-easycart-account-shortcode wp-easycart-block-item',
							label: i18n.__( 'On Success Redirect URL (optional, default is the account dashboard)' ),
							value: attributes.account_redirect,
							onChange: onChangeAccountRedirect,
						}
					),
					focus && el(
						SelectControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_categories' ) ? 'hidden ' : '' ) + 'wp-easycart-categories-shortcode wp-easycart-block-item',
							type: 'string',
							label: i18n.__( 'Categories to Display' ),
							value: attributes.categories_category,
							onChange: onChangeCategoriesCategory,
							options: [
							  { value: '0', label: i18n.__( 'Show Featured Categories' ) },
							  { value: '-1', label: i18n.__( 'Show Top Level Categories' ) },
							].concat( wp_easycart_categories )
						}
					),
					focus && el(
						SelectControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_category_view' ) ? 'hidden ' : '' ) + 'wp-easycart-category-view-shortcode wp-easycart-block-item',
							type: 'string',
							label: i18n.__( 'Categories to Display' ),
							value: attributes.category_view_category,
							onChange: onChangeCategoryViewCategory,
							options: [
							  { value: '0', label: i18n.__( 'Show Featured Categories' ) },
							  { value: '-1', label: i18n.__( 'Show Top Level Categories' ) },
							].concat( wp_easycart_categories )
						}
					),
					focus && el(
						SelectControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_category_view' ) ? 'hidden ' : '' ) + 'wp-easycart-category-view-shortcode wp-easycart-block-item',
							type: 'string',
							label: i18n.__( 'Columns' ),
							value: attributes.category_view_columns,
							onChange: onChangeCategoryViewColumns,
							options: [
							  { value: '1', label: i18n.__( '1 Column' ) },
							  { value: '2', label: i18n.__( '2 Columns' ) },
							  { value: '3', label: i18n.__( '3 Columns' ) },
							  { value: '4', label: i18n.__( '4 Columns' ) },
							  { value: '5', label: i18n.__( '5 Columns' ) },
							  { value: '6', label: i18n.__( '6 Columns' ) }
							]
						}
					),
					focus && wp_easycart_products.length < 2000 && el(
						SelectControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_store_table' ) ? 'hidden ' : '' ) + 'wp-easycart-store-table-shortcode wp-easycart-block-item',
							type: 'string',
							label: i18n.__( 'Products to Display' ),
							value: attributes.store_table_products,
							onChange: onChangeStoreTableProducts,
							multiple: 'multiple',
							options: wp_easycart_products
						}
					),
					focus && wp_easycart_products.length >= 2000 && el(
						TextControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_store_table' ) ? 'hidden ' : '' ) + 'wp-easycart-store-table-shortcode wp-easycart-block-item',
							tagName: 'input',
							label: i18n.__( 'Enter Product IDs (Comma Separated)' ),
							value: attributes.store_table_products,
							onChange: onChangeStoreTableProducts
						}
					),
					focus && wp_easycart_menulevel1.length < 2000 && el(
						SelectControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_store_table' ) ? 'hidden ' : '' ) + 'wp-easycart-store-table-shortcode wp-easycart-block-item',
							type: 'string',
							label: i18n.__( 'Menus to Display' ),
							value: attributes.store_table_menulevel1,
							onChange: onChangeStoreTableMenulevel1,
							multiple: 'multiple',
							options: wp_easycart_menulevel1
						}
					),
					focus && wp_easycart_menulevel1.length >= 2000 && el(
						TextControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_store_table' ) ? 'hidden ' : '' ) + 'wp-easycart-store-table-shortcode wp-easycart-block-item',
							tagName: 'input',
							label: i18n.__( 'Enter Menu Level 1 IDs (Comma Separated)' ),
							value: attributes.store_table_menulevel1,
							onChange: onChangeStoreTableMenulevel1
						}
					),
					focus && wp_easycart_menulevel2.length < 2000 && el(
						SelectControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_store_table' ) ? 'hidden ' : '' ) + 'wp-easycart-store-table-shortcode wp-easycart-block-item',
							type: 'string',
							label: i18n.__( 'Sub-Menus to Display' ),
							value: attributes.store_table_menulevel2,
							onChange: onChangeStoreTableMenulevel2,
							multiple: 'multiple',
							options: wp_easycart_menulevel2
						}
					),
					focus && wp_easycart_menulevel2.length >= 2000 && el(
						TextControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_store_table' ) ? 'hidden ' : '' ) + 'wp-easycart-store-table-shortcode wp-easycart-block-item',
							tagName: 'input',
							label: i18n.__( 'Enter Menu Level 2 IDs (Comma Separated)' ),
							value: attributes.store_table_menulevel2,
							onChange: onChangeStoreTableMenulevel2
						}
					),
					focus && wp_easycart_menulevel3.length < 2000 && el(
						SelectControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_store_table' ) ? 'hidden ' : '' ) + 'wp-easycart-store-table-shortcode wp-easycart-block-item',
							type: 'string',
							label: i18n.__( 'Sub-Sub-Menus to Display' ),
							value: attributes.store_table_menulevel3,
							onChange: onChangeStoreTableMenulevel3,
							multiple: 'multiple',
							options: wp_easycart_menulevel3
						}
					),
					focus && wp_easycart_menulevel3.length >= 2000 && el(
						TextControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_store_table' ) ? 'hidden ' : '' ) + 'wp-easycart-store-table-shortcode wp-easycart-block-item',
							tagName: 'input',
							label: i18n.__( 'Enter Menu Level 3 IDs (Comma Separated)' ),
							value: attributes.store_table_menulevel3,
							onChange: onChangeStoreTableMenulevel3
						}
					),
					focus && wp_easycart_categories.length < 2000 && el(
						SelectControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_store_table' ) ? 'hidden ' : '' ) + 'wp-easycart-store-table-shortcode wp-easycart-block-item',
							type: 'string',
							label: i18n.__( 'Categories to Display' ),
							value: attributes.store_table_categories,
							onChange: onChangeStoreTableCategory,
							multiple: 'multiple',
							options: wp_easycart_categories
						}
					),
					focus && wp_easycart_categories.length >= 2000 && el(
						TextControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_store_table' ) ? 'hidden ' : '' ) + 'wp-easycart-store-table-shortcode wp-easycart-block-item',
							tagName: 'input',
							label: i18n.__( 'Enter Category IDs (Comma Separated)' ),
							value: attributes.store_table_categories,
							onChange: onChangeStoreTableCategory
						}
					),
					focus && el(
						TextControl,
						{
							tagName: 'input',
							className: ( ( attributes.shortcode_type != 'ec_store_table' ) ? 'hidden ' : '' ) + 'wp-easycart-store-table-shortcode wp-easycart-block-item',
							label: i18n.__( 'Column 1 Label' ),
							value: attributes.store_table_label1,
							onChange: onChangeStoreTableLabel1,
						}
					),
					focus && el(
						SelectControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_store_table' ) ? 'hidden ' : '' ) + 'wp-easycart-store-table-shortcode wp-easycart-block-item',
							type: 'string',
							label: i18n.__( 'Column 1 Data' ),
							value: attributes.store_table_column1,
							onChange: onChangeStoreTableColumn1,
							options: WPEasyCartTableColumns
						}
					),
					focus && el(
						TextControl,
						{
							tagName: 'input',
							className: ( ( attributes.shortcode_type != 'ec_store_table' ) ? 'hidden ' : '' ) + 'wp-easycart-store-table-shortcode wp-easycart-block-item',
							label: i18n.__( 'Column 2 Label' ),
							value: attributes.store_table_label2,
							onChange: onChangeStoreTableLabel2,
						}
					),
					focus && el(
						SelectControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_store_table' ) ? 'hidden ' : '' ) + 'wp-easycart-store-table-shortcode wp-easycart-block-item',
							type: 'string',
							label: i18n.__( 'Column 2 Data' ),
							value: attributes.store_table_column2,
							onChange: onChangeStoreTableColumn2,
							options: WPEasyCartTableColumns
						}
					),
					focus && el(
						TextControl,
						{
							tagName: 'input',
							className: ( ( attributes.shortcode_type != 'ec_store_table' ) ? 'hidden ' : '' ) + 'wp-easycart-store-table-shortcode wp-easycart-block-item',
							label: i18n.__( 'Column 3 Label' ),
							value: attributes.store_table_label3,
							onChange: onChangeStoreTableLabel3,
						}
					),
					focus && el(
						SelectControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_store_table' ) ? 'hidden ' : '' ) + 'wp-easycart-store-table-shortcode wp-easycart-block-item',
							type: 'string',
							label: i18n.__( 'Column 3 Data' ),
							value: attributes.store_table_column3,
							onChange: onChangeStoreTableColumn3,
							options: WPEasyCartTableColumns
						}
					),
					focus && el(
						TextControl,
						{
							tagName: 'input',
							className: ( ( attributes.shortcode_type != 'ec_store_table' ) ? 'hidden ' : '' ) + 'wp-easycart-store-table-shortcode wp-easycart-block-item',
							label: i18n.__( 'Column 4 Label' ),
							value: attributes.store_table_label4,
							onChange: onChangeStoreTableLabel4,
						}
					),
					focus && el(
						SelectControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_store_table' ) ? 'hidden ' : '' ) + 'wp-easycart-store-table-shortcode wp-easycart-block-item',
							type: 'string',
							label: i18n.__( 'Column 4 Data' ),
							value: attributes.store_table_column4,
							onChange: onChangeStoreTableColumn4,
							options: WPEasyCartTableColumns
						}
					),
					focus && el(
						TextControl,
						{
							tagName: 'input',
							className: ( ( attributes.shortcode_type != 'ec_store_table' ) ? 'hidden ' : '' ) + 'wp-easycart-store-table-shortcode wp-easycart-block-item',
							label: i18n.__( 'Column 5 Label' ),
							value: attributes.store_table_label5,
							onChange: onChangeStoreTableLabel5,
						}
					),
					focus && el(
						SelectControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_store_table' ) ? 'hidden ' : '' ) + 'wp-easycart-store-table-shortcode wp-easycart-block-item',
							type: 'string',
							label: i18n.__( 'Column 5 Data' ),
							value: attributes.store_table_column5,
							onChange: onChangeStoreTableColumn5,
							options: WPEasyCartTableColumns
						}
					),
					focus && el(
						TextControl,
						{
							tagName: 'input',
							className: ( ( attributes.shortcode_type != 'ec_store_table' ) ? 'hidden ' : '' ) + 'wp-easycart-store-table-shortcode wp-easycart-block-item',
							label: i18n.__( 'Link Label (Optional)' ),
							value: attributes.store_table_link_label,
							onChange: onChangeStoreTableLinkLabel,
						}
					),
					focus && wp_easycart_products.length < 2000 && el(
						SelectControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_product' ) ? 'hidden ' : '' ) + 'wp-easycart-product-shortcode wp-easycart-block-item',
							type: 'string',
							label: i18n.__( 'Product to Display' ),
							value: attributes.product_product,
							onChange: onChangeProductProduct,
							options: wp_easycart_products
						}
					),
					focus && wp_easycart_products.length >= 2000 && el(
						TextControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_product' ) ? 'hidden ' : '' ) + 'wp-easycart-product-shortcode wp-easycart-block-item',
							tagName: 'input',
							label: i18n.__( 'Enter Product ID' ),
							value: attributes.product_product,
							onChange: onChangeProductProduct
						}
					),
					focus && el(
						SelectControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_product' ) ? 'hidden ' : '' ) + 'wp-easycart-product-shortcode wp-easycart-block-item',
							type: 'string',
							label: i18n.__( 'Display Type' ),
							value: attributes.product_display_type,
							onChange: onChangeProductDisplayType,
							options: [
								{ value: '1', label: i18n.__( 'Default Product Display Type' ) },
								{ value: '2', label: i18n.__( 'Same as Product Widget Display' ) },
								{ value: '3', label: i18n.__( 'Custom Display Type 1' ) }
							]
						}
					),
					focus && wp_easycart_products.length < 2000 && el(
						SelectControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_addtocart' ) ? 'hidden ' : '' ) + 'wp-easycart-addtocart-shortcode wp-easycart-block-item',
							type: 'string',
							label: i18n.__( 'Product to Display' ),
							value: attributes.addtocart_product,
							onChange: onChangeAddToCartProduct,
							options: wp_easycart_products
						}
					),
					focus && wp_easycart_products.length >= 2000 && el(
						TextControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_addtocart' ) ? 'hidden ' : '' ) + 'wp-easycart-addtocart-shortcode wp-easycart-block-item',
							tagName: 'input',
							label: i18n.__( 'Enter Product ID' ),
							value: attributes.addtocart_product,
							onChange: onChangeAddToCartProduct
						}
					),
					focus && wp_easycart_products.length < 2000 && el(
						SelectControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_membership' ) ? 'hidden ' : '' ) + 'wp-easycart-membership-shortcode wp-easycart-block-item',
							type: 'string',
							label: i18n.__( 'Purchase Required for Access (only one neeeded for access if multiple selected)' ),
							value: attributes.membership_products,
							onChange: onChangeMembershipProducts,
							multiple:'multiple',
							options: wp_easycart_products
						}
					),
					focus && wp_easycart_products.length >= 2000 && el(
						TextControl,
						{
							className: ( ( attributes.shortcode_type != 'ec_membership' ) ? 'hidden ' : '' ) + 'wp-easycart-membership-shortcode wp-easycart-block-item',
							tagName: 'input',
							label: i18n.__( 'Purchase Required for Access - Enter Product IDs (Comma Separated)' ),
							value: attributes.membership_products,
							onChange: onChangeMembershipProducts
						}
					)
				];
			},
			save: function( props ) {
				var attributes = props.attributes;
				if( attributes.shortcode_type == "ec_store" ){
					var storeShortcode = '[ec_store';
					
					if( attributes.store_category != '' )
						storeShortcode += ' groupid="' + attributes.store_category + '"';
					else if( attributes.store_manufacturer != '' )
						storeShortcode += ' manufacturerid="' + attributes.store_manufacturer + '"';
					else if( attributes.store_product != '' )
						storeShortcode += ' modelnumber="' + attributes.store_product + '"';
					else if( attributes.store_menulevel3 != '' )
						storeShortcode += ' subsubmenuid="' + attributes.store_menulevel3 + '"';
					else if( attributes.store_menulevel2 != '' )
						storeShortcode += ' submenuid="' + attributes.store_menulevel2 + '"';
					else if( attributes.store_menulevel1 != '' )
						storeShortcode += ' menuid="' + attributes.store_menulevel1 + '"';
					
					return storeShortcode + ']';
					
				}else if( attributes.shortcode_type == "ec_account" ){
					var accountShortcode = '[ec_account';
					
					if( attributes.account_redirect != '' )
						accountShortcode += ' redirect="' + attributes.account_redirect + '"';
						
					return accountShortcode + ']';
					
				}else if( attributes.shortcode_type == "ec_categories" ){
					return '[ec_categories groupid="' + attributes.categories_category + '"]';
					
				}else if( attributes.shortcode_type == "ec_category_view" ){
					return '[ec_category_view groupid="' + attributes.category_view_category + '" columns="' + attributes.category_view_columns + '"]';
					
				}else if( attributes.shortcode_type == "ec_store_table" ){
					return '[ec_store_table productid="' + attributes.store_table_products + '" menuid="' + attributes.store_table_menulevel1 + '" submenuid="' + attributes.store_table_menulevel2 + '" subsubmenuid="' + attributes.store_table_menulevel3 + '" categoryid="' + attributes.store_table_categories + '" labels="' + attributes.store_table_label1 + ',' + attributes.store_table_label2 + ',' + attributes.store_table_label3 + ',' + attributes.store_table_label4 + ',' + attributes.store_table_label5 + '" columns="' + attributes.store_table_column1 + ',' + attributes.store_table_column2 + ',' + attributes.store_table_column3 + ',' + attributes.store_table_column4 + ',' + attributes.store_table_column5 + '"]';
					
				}else if( attributes.shortcode_type == "ec_product" ){
					return '[ec_product productid="' + attributes.product_product + '" style="' + attributes.product_display_type + '"]';
					
				}else if( attributes.shortcode_type == "ec_addtocart" ){
					return '[ec_addtocart productid="' + attributes.addtocart_product + '"]';
					
				}else if( attributes.shortcode_type == "ec_cartdisplay" ){
					return '[ec_cartdisplay]';
					
				}else if( attributes.shortcode_type == "ec_membership" ){
					return '[ec_membership productid="' + attributes.membership_product + '"]MEMBER CONTENT HERE[/ec_membership][ec_membership_alt productid="' + attributes.membership_product + '"]NON-MEMBER NOTICE HERE[/ec_membership_alt]';
				
				}else{
					return "["+attributes.shortcode_type+"]";
				}
			}
		} );
	} )(
	   window.wp.blocks,
	   window.wp.i18n,
	   window.wp.element
	);
});