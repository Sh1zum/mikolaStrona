<option value="price" <?php if( wp_easycart_admin( )->settings->shipping_method == 'price' ) echo ' selected'; ?>>
	<?php _e( 'Price Trigger System', 'wp-easycart' ); ?>
</option>
<option value="weight" <?php if( wp_easycart_admin( )->settings->shipping_method == 'weight' ) echo ' selected'; ?>>
	<?php _e( 'Weight Trigger System', 'wp-easycart' ); ?>
</option>
<option value="quantity" <?php if( wp_easycart_admin( )->settings->shipping_method == 'quantity' ) echo ' selected'; ?>>
	<?php _e( 'Quantity Trigger System', 'wp-easycart' ); ?>
</option>
<option value="percentage" <?php if( wp_easycart_admin( )->settings->shipping_method == 'percentage' ) echo ' selected'; ?>>
	<?php _e( 'Percentage Based Shipping', 'wp-easycart' ); ?>
</option>
<option value="method" <?php if( wp_easycart_admin( )->settings->shipping_method == 'method' ) echo ' selected'; ?>>
	<?php _e( 'Static Shipping Method', 'wp-easycart' ); ?>
</option>
<option value="fraktjakt" <?php if( wp_easycart_admin( )->settings->shipping_method == 'fraktjakt' ) echo ' selected'; ?>>
	<?php _e( 'Fraktjakt', 'wp-easycart' ); ?>
</option>