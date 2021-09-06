<?php
class wp_easycart_admin_online_docs{
	
	public function __construct( ){
		$this->admin_guide_url = 'http://docs.wpeasycart.com/wp-easycart-administrative-console-guide/?wpeasycartadmin=1';
		$this->admin_guide_section_url = 'http://docs.wpeasycart.com/wp-easycart-administrative-console-guide/?wpeasycartadmin=1&section=';
		$this->installation_guide_url = 'http://docs.wpeasycart.com/wp-easycart-installation-guide/?wpeasycartadmin=1&section=';
		$this->extension_guide_url = 'http://docs.wpeasycart.com/wp-easycart-extensions-guide/?wpeasycartadmin=1&section=';
	}
	
	//section (installation, settings, extensions...)
	//category (taxes, shipping rates, checkout, design, email, third party...)
	//panel (google adwords, deconetwork, taxcloud, amazon, order receipts...)
	public function print_docs_url($section, $category, $panel) {
		
		if ($section == 'products' ) {
			if ($category == 'products') {
				if( $panel == "importer" )
					return $this->admin_guide_section_url . $category . '-importer';
				else
					return $this->admin_guide_section_url . $category;
			} else if($category == 'inventory') {
				return $this->admin_guide_section_url . $category;
			} else if($category == 'option-sets') {
				return $this->admin_guide_section_url . $category;
			} else if($category == 'categories') {
				return $this->admin_guide_section_url . $category;
			} else if($category == 'menus') {
				return $this->admin_guide_section_url . $category;
			} else if($category == 'manufacturers') {
				return $this->admin_guide_section_url . $category;
			} else if($category == 'product-reviews') {
				return $this->admin_guide_section_url . $category;
			} else if($category == 'subscription-plans') {
				return $this->admin_guide_section_url . $category;
			}  else {
				////REGULAR GUIDE PAGE
				return $this->admin_guide_url ;
			}
		}
		
		
		if ($section == 'orders' ) {
			if ($category == 'order-management') {
				////Orders
				return $this->admin_guide_section_url . $category;
			} else if($category == 'subscriptions') {
				return $this->admin_guide_section_url . $category;
			} else if($category == 'manage-downloads') {
				return $this->admin_guide_section_url . $category;
			}  else {
				////REGULAR GUIDE PAGE
				return $this->admin_guide_url ;
			}
		}
		
		if ($section == 'users' ) {
			if ($category == 'user-accounts') {
				////users
				return $this->admin_guide_section_url . $category;
			} else if($category == 'user-roles') {
				return $this->admin_guide_section_url . $category;
			} else if($category == 'subscribers') {
				return $this->admin_guide_section_url . $category;
			}  else {
				////REGULAR GUIDE PAGE
				return $this->admin_guide_url ;
			}
		}
			
		if ($section == 'marketing' ) {	
			if($category == 'coupons') {
				////marketing
				return $this->admin_guide_section_url . $category;
			} else if($category == 'gift-cards') {
				return $this->admin_guide_section_url . $category;
			} else if($category == 'promotions') {
				return $this->admin_guide_section_url . $category;
			} else if($category == 'abandoned-carts') {
				return $this->admin_guide_section_url . $category;
			} else {
				////REGULAR GUIDE PAGE
				return $this->admin_guide_url ;
			}
		}
		if ($section == 'settings') {
			if($category == 'initial-setup') {
				////SETTINGS
				return $this->admin_guide_section_url . $category;	
			} else if($category == 'product-settings') {
				return $this->admin_guide_section_url . $category;	
			} else if($category == 'taxes') {
				return $this->admin_guide_section_url . $category;	
			} else if($category == 'shipping-settings') {
				return $this->admin_guide_section_url . $category;	
			} else if($category == 'shipping-rates') {
				return $this->admin_guide_section_url . $category;	
			} else if($category == 'payment') {
				return $this->admin_guide_section_url . $category;	
			} else if($category == 'checkout') {
				return $this->admin_guide_section_url . $category;	
			} else if($category == 'accounts') {
				return $this->admin_guide_section_url . $category;	
			} else if($category == 'additional-settings') {
				return $this->admin_guide_section_url . $category;	
			} else if($category == 'language-editor') {
				return $this->admin_guide_section_url . $category;	
			} else if($category == 'design') {
				return $this->admin_guide_section_url . $category;	
			} else if($category == 'email-setup') {
				return $this->admin_guide_section_url . $category;	
			} else if($category == 'third-party') {
				return $this->admin_guide_section_url . $category;	
			} else if($category == 'cart-importer') {
				return $this->admin_guide_section_url . $category;	
			} else if($category == 'countries') {
				return $this->admin_guide_section_url . $category;	
			} else if($category == 'states-territories') {
				return $this->admin_guide_section_url . $category;	
			} else if($category == 'per-page-options') {
				return $this->admin_guide_section_url . $category;	
			} else if($category == 'price-points') {
				return $this->admin_guide_section_url . $category;	
			} else if($category == 'log-entries') {
				return $this->admin_guide_section_url . $category;	
			} else if($category == 'store-status') {
				////STORE STATUS
				return $this->admin_guide_section_url . $category;	
			} else if($category == 'registration') {
				////REGISTRATION
				return $this->admin_guide_section_url . $category;	
			} else if($category == 'gift-cards') {
				////MARKETING
				return $this->admin_guide_section_url . $category;	
			} else if($category == 'coupons') {
				return $this->admin_guide_section_url . $category;	
			} else if($category == 'promotions') {
				return $this->admin_guide_section_url . $category;	
			} else if($category == 'abandon-cart') {
				return $this->admin_guide_section_url . $category;	
			} else {
				////REGULAR GUIDE PAGE
				return $this->admin_guide_url ;
			}
		} else {
			////REGULAR GUIDE PAGE
			return $this->admin_guide_url ;
		}
	}
	
	public function print_vids_url($section, $category, $panel) {

		$videoID = false;
		
		if ($section == 'products' ) {
			if ($category == 'products') {
				if( $panel == "importer" ){
					$videoID = 'ua50lCD4ROA'; //older
				} else {
					$videoID = 'XZmXGI02i6Y';  //updated
				}

			} else if($category == 'option-sets') {
				 $videoID = 'T0dByKX67iY'; //updated
			} else if($category == 'categories') {
				 $videoID = 'rRHm0XvqXto'; //updated
			} else if($category == 'menus') {
			  
			} else if($category == 'manufacturers') {
				  
			} else if($category == 'product-reviews') {
			
			} else if($category == 'subscription-plans') {
				$videoID = 'w53tzohUv2k';  //updated
			}
		}
		
		
		if ($section == 'orders' ) {
			if ($category == 'order-management') {
				 $videoID = 'sAfCkLgtGQY';  //updated
			} else if($category == 'subscriptions') {
				 $videoID = 'w53tzohUv2k';   //updated
			} else if($category == 'manage-downloads') {
				 $videoID = 'Vpp-lXFIndo'; //updated
			}
		}
		
		if ($section == 'users' ) {
			if ($category == 'user-accounts') {
				$videoID = 'w-4oLtHGGa4'; //updated
			} else if($category == 'user-roles') {
				 $videoID = ' h2THe0-Zt-M'; //older
			} else if($category == 'subscribers') {
				  
			}
		}
		if ($section == 'marketing' ) {	
			if($category == 'coupons') {
				  $videoID = '9rR8CwEeqG4';  //updated
			} else if($category == 'gift-cards') {
				  $videoID = '2KzoV89GeTM';  //updated
			} else if($category == 'promotions') {
				  $videoID = 'r-6WaGDig_k';  //updated
			} else if($category == 'abandoned-carts') {
				 $videoID = 'UeqYbbCRdiQ';  //updated
			}
		}
		if ($section == 'settings') {
			if($category == 'initial-setup') {
				if($panel == 'product-page') {
					 $videoID = 'Pc3bCSgR-xM';
				} else if ($panel == 'account-page') {
					 $videoID = 'Pc3bCSgR-xM';
				} else if ($panel == 'cart-page') {
					 $videoID = 'Pc3bCSgR-xM';
				} else if ($panel == 'demo-data') {
				
				} else if ($panel == 'currency') {
					 
				} else if ($panel == 'goals') {
					 
				}
			} else if($category == 'product-settings') {
				if($panel == 'product-list') {
					
				} else if ($panel == 'product-display') {
					  $videoID = 'Ai1Ie-IeLkg'; //updated
				} else if ($panel == 'customer-review') {
					 
				} else if ($panel == 'product-details') {
					 
				} else if ($panel == 'price-display') {
					 
				} else if ($panel == 'inventory') {
					
				}
			} else if($category == 'taxes') {
				if($panel == 'tax-by-state-setup') {
					 $videoID = 'NXgU7tc1850'; //updated
				} else if ($panel == 'vat-setup') {
					 $videoID = '8MAXc16HObg'; //updated
				} else if ($panel == 'tax-by-country-setup') {
					$videoID = 'NXgU7tc1850'; //updated
				} else if ($panel == 'global-tax-setup') {
					 $videoID = 'NXgU7tc1850'; //updated
				} else if ($panel == 'duty-tax-setup') {
					 $videoID = 'NXgU7tc1850'; //updated
				} else if ($panel == 'canada-tax-setup') {
					 $videoID = 'pRAMxO2XEl0'; //updated
				} else if ($panel == 'tax-cloud-setup') {
					 $videoID = '36LEWLY6HE4';  //updated
				}
			} else if($category == 'shipping-settings') {
				if ($panel == 'usps') {
					$videoID = 'MeHz8lazvcI'; //updated
				} else if ($panel == 'ups') {
					$videoID = 'MeHz8lazvcI'; //updated
				} else if ($panel == 'fedex') {
					$videoID = 'MeHz8lazvcI'; //updated
				} else if ($panel == 'dhl') {
					$videoID = 'MeHz8lazvcI'; //updated
				} else if ($panel == 'canada-post') {
					$videoID = 'MeHz8lazvcI'; //updated
				} else if ($panel == 'australia-post') {
					$videoID = 'MeHz8lazvcI'; //updated
				}

			} else if($category == 'shipping-rates') {
				if ($panel == 'shipping-method') {
					$videoID = 'Mx_z_ciKerw'; //updated
				} else if ($panel == 'country-list') {
					$videoID = '8HoUdEqXWNM'; //updated
				} else if ($panel == 'state-list') {
					$videoID = '8HoUdEqXWNM'; //updated
				} else if ($panel == 'shipping-zones') {
					$videoID = '8HoUdEqXWNM'; //updated
				} else if ($panel == 'shipping-basic-options') {
					
				}
				
			} else if($category == 'payment') {
				////////////////////all new section needs to be added here to settings -> payment
				if($panel == 'bill-later') {
					 $videoID = 'WBnAke8lt-c'; //updated
				} else if ($panel == 'paypal') {
					 $videoID = 'A1wDK3ujO70'; //updated
				} else if ($panel == 'stripe') {
					 $videoID = 'GzrlmpSqzRU'; //updated
				} else if ($panel == 'square') {
					 $videoID = '37Ci-Jz5BjM'; //updated
				}
				 	
			} else if($category == 'checkout') {
				if($panel == 'settings') {
					 
				} else if ($panel == 'form-settings') {
					 
				} else if ($panel == 'stock-control') {
					 
				}
				 	
			} else if($category == 'accounts') {
				if($panel == 'settings') {
					 
				}	
			} else if($category == 'additional-settings') {
				if($panel == 'search-options') {
					 
				} else if ($panel == 'additional-options') {
					 
				}
			} else if($category == 'language-editor') {
				if($panel == 'current-language') {
					 $videoID = 'wiifQ2IhvNY'; //updated
				} else if ($panel == 'installed-languages') {
					 $videoID = 'wiifQ2IhvNY';  //updated
				} 	
			} else if($category == 'design') {
				if($panel == 'cart') {
					 $videoID = 'e59LM9CcyCM'; //updated
				} else if ($panel == 'custom-css') {
					 $videoID = 'e59LM9CcyCM'; //updated
				} else if ($panel == 'colors') {
					 $videoID = 'e59LM9CcyCM'; //updated
				} else if ($panel == 'templates') {
					 $videoID = 'e59LM9CcyCM'; //updated
				} else if ($panel == 'product') {
					 $videoID = 'e59LM9CcyCM'; //updated
				} else if ($panel == 'product-details') {
					 $videoID = 'e59LM9CcyCM'; //updated
				} else if ($panel == 'settings') {
					 $videoID = 'e59LM9CcyCM'; //updated
				}
				 	
			} else if($category == 'email-setup') {
				if($panel == 'customer-email') {
					 $videoID = 'p96NBca16N0'; //updated
				} else if ($panel == 'email-settings') {
					 $videoID = 'p96NBca16N0'; //updated
				} else if ($panel == 'order-receipt-language') {
					 $videoID = 'p96NBca16N0'; //updated
				} else if ($panel == 'order-receipt') {
					 $videoID = 'p96NBca16N0'; //updated
				} 	
			} else if($category == 'third-party') {
				if($panel == 'amazon') {
					 
				} else if ($panel == 'deconetwork') {
					 
				} else if ($panel == 'google adwords') {
					 
				} else if ($panel == 'google-analytics') {
					 
				} else if ($panel == 'google-merchant') {
					 
				} 	
			} else if($category == 'cart-importer') {
				if($panel == 'woo') {
					 
				} else if ($panel == 'oscommerce') {
					 
				} 	
			} else if($category == 'manage-countries') {
				  	$videoID = 'doRi9r5yOAY'; //updated
			} else if($category == 'manage-states') {
				  	$videoID = 'doRi9r5yOAY'; //updated
			} else if($category == 'manage-per-page') {
				  	
			} else if($category == 'manage-price-points') {
				  	
			} else if($category == 'logs') {
				  	
			} else if($category == 'store-status') {
				  	
			} else if($category == 'registration') {
				 if($panel == 'registration') {
					$videoID = 'r3Q4FJiUwWY';  //older
				} else if ($panel == 'none') {
					$videoID = 'r3Q4FJiUwWY';  //older
				} else if ($panel == 'expired') {
					$videoID = 'r3Q4FJiUwWY';  //older
				} 	
			}
		}
		
		if($videoID != false) {
			$video_script = '<script>';
			$video_script .= '	var wp_easycart_help_player;';
			$video_script .= '	var tag = document.createElement( "script" );';
			$video_script .= '	tag.src = "https://www.youtube.com/iframe_api";';
			$video_script .= '	var firstScriptTag = document.getElementsByTagName( "script" )[0];';
			$video_script .= '	firstScriptTag.parentNode.insertBefore( tag, firstScriptTag );';
				
			$video_script .= '	function onYouTubeIframeAPIReady( ){';
			$video_script .= '		wp_easycart_help_player = new YT.Player( "wp_easycart_admin_help_video_player", {';
			$video_script .= '			width: "100%",';
			$video_script .= '			height: "450",';
			$video_script .= '			videoId: "'.$videoID.'"';
			$video_script .= '		});';
			$video_script .= '	}';
				
			$video_script .= '	jQuery( ".ec_admin_help_video_container > .ec_admin_upsell_popup_close > a" ).on( "click", function( ){';
			$video_script .= '		wp_easycart_help_player.pauseVideo( ); wp_easycart_admin_close_video_help( ); return false;';
			$video_script .= '	} );';
			$video_script .= '</script>';
			
			
			
			$video_script .= ' <a href="https://www.youtube.com/watch?v='.$videoID.'"  onclick="wp_easycart_admin_open_video_help(\''.$videoID.'\'); wp_easycart_help_player.playVideo( ); return false;" class="ec_help_icon_link"><div class="dashicons-before ec_help_icon dashicons-format-video"></div> ' . __( 'Video', 'wp-easycart' ) . '</a>';
			
			return $video_script;
		}
	}
	

		
}
