<?php if( isset( $_GET['success'] ) && $_GET['success'] == "easycart-initial-setup" ){ ?>
	<div class="ec_admin_success_message"><div><?php _e( 'Your initial setup has been updated successfully.', 'wp-easycart' ); ?></div></div>
<?php }else if( isset( $_GET['success'] ) && $_GET['success'] == "easycart-storepage-added" ){ ?>
	<div class="ec_admin_success_message"><div><?php _e( 'The product page has been created successfully.', 'wp-easycart' ); ?></div></div>
<?php }else if( isset( $_GET['success'] ) && $_GET['success'] == "easycart-cartpage-added" ){ ?>
	<div class="ec_admin_success_message"><div><?php _e( 'The cart page has been created successfully.', 'wp-easycart' ); ?></div></div>
<?php }else if( isset( $_GET['success'] ) && $_GET['success'] == "easycart-accountpage-added" ){ ?>
	<div class="ec_admin_success_message"><div><?php _e( 'The account page has been created successfully.', 'wp-easycart' ); ?></div></div>
<?php }else if( isset( $_GET['success'] ) && $_GET['success'] == "easycart-demo-data-installed" ){ ?>
	<div class="ec_admin_success_message"><div><?php _e( 'The demo data has been installed successfully.', 'wp-easycart' ); ?></div></div>
<?php }else if( isset( $_GET['success'] ) && $_GET['success'] == "easycart-demo-data-uninstalled" ){ ?>
	<div class="ec_admin_success_message"><div><?php _e( 'The demo data has been uninstalled successfully.', 'wp-easycart' ); ?></div></div>
<?php } ?>