<div class="ec_product item<?php echo $i+1; ?>">

	<div class="ec_product_images">
		
		<?php $product->display_product_image_set( "medium", "ec_image_", "true; ec_image_click" ); ?>

	</div>

    <div class="ec_product_title">

      <?php $product->display_product_title_link(); ?>

    </div>

	<?php if( ( $product->is_catalog_mode && get_option( 'ec_option_hide_price_seasonal' ) ) || 
			  ( $product->is_inquiry_mode && get_option( 'ec_option_hide_price_inquiry' ) ) ){ // don't show price
		  }else{ ?>
	<div class="ec_price_container_type1">
		<?php if( $product->list_price > 0 ){ ?>
			<span class="ec_list_price_type1"><?php 
		
					$list_price = $GLOBALS['currency']->get_currency_display( $product->list_price );
					$list_price = apply_filters( 'wp_easycart_product_list_price_display', $list_price, $product->list_price );
					echo $list_price;
			
				?></span>
		<?php }?>
		<span class="ec_price_type1"><?php 
		
			$display_price = $GLOBALS['currency']->get_currency_display( $product->price );
			if( $product->pricing_per_sq_foot && !get_option( 'ec_option_enable_metric_unit_display' ) ){ 
				$display_price .= "/sq ft";
			}else if( $product->pricing_per_sq_foot && get_option( 'ec_option_enable_metric_unit_display' ) ){ 
				$display_price .= "/sq m";
			}
			
			$display_price = apply_filters( 'wp_easycart_product_price_display', $display_price, $product->price, $product->product_id );
			echo $display_price;
		
		?><?php if( $GLOBALS['ec_vat_included'] && $product->vat_rate == 1 && get_option( 'ec_option_show_multiple_vat_pricing' ) ){ ?> <span class="ec_inc_vat_text"><?php echo $GLOBALS['language']->get_text( 'product_page', 'product_inc_vat_text' ); ?></span><?php }else if( $GLOBALS['ec_vat_added'] && $product->vat_rate == 1 && get_option( 'ec_option_show_multiple_vat_pricing' ) ){ ?> <span class="ec_inc_vat_text"><?php echo $GLOBALS['language']->get_text( 'product_page', 'product_excluding_vat_text' ); ?></span><?php }?></span>
	</div>
	<?php }?>

</div>