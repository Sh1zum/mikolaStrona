<form action="" method="POST" name="wpeasycart_admin_setup_wizard_form" id="wpeasycart_admin_setup_wizard_form" novalidate="novalidate">
<input type="hidden" name="ec_admin_form_action" id="ec_admin_form_action" value="process-wizard-payments">
<p style="background:#f3f3f3; padding:10px; text-align:center; font-weight: bold;"><?php _e( 'If you need to make changes to items from the setup wizard, all features and more are available at any time in the settings of the WP EasyCart.', 'wp-easycart' ); ?></p>
<h3><?php _e( 'You\'re Done!', 'wp-easycart' ); ?></h3>
<p><?php _e( 'Thank you for taking the time to complete setup, now onwards to create your first product and make your first sale!', 'wp-easycart' ); ?></p>
<div class="wp_easycart_wizard_success_container">
    <?php if( !get_option( 'ec_option_demo_data_installed' ) ){ ?>
    <div class="wp_easycart_wizard_success_box" id="easycart_wizard_demo_data">
        <?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_demo_data_loader" ); ?>
    	<div class="wp_easycart_wizard_success_box_left">
            <div class="wp_easycart_wizard_success_box_title"><?php _e( 'JUST TRYING THE CART?', 'wp-easycart' ); ?></div>
            <div class="wp_easycart_wizard_success_box_content"><?php _e( 'If you are new to EasyCart, try our demo data first.', 'wp-easycart' ); ?></div>
        </div>
        <div class="wp_easycart_wizard_success_box_right">
            <div class="wp_easycart_wizard_success_box_button"><a href="admin.php?page=wp-easycart-settings&subpage=initial-setup&action=easycart-install-demo-data" onclick="return ec_admin_install_demo_data( );"><?php _e( 'Install Demo Data', 'wp-easycart' ); ?></a></div>
        </div>
    	<div style="clear:both;"></div>
    </div>
    <div class="wp_easycart_wizard_success_box" id="easycart_wizard_demo_data_done" style="display:none;">
    	<div class="wp_easycart_wizard_success_box_left">
            <div class="wp_easycart_wizard_success_box_title"><?php _e( 'DEMO DATA INSTALLED!', 'wp-easycart' ); ?></div>
            <div class="wp_easycart_wizard_success_box_content"><?php _e( 'You are all set! Now check out your new store.', 'wp-easycart' ); ?></div>
        </div>
        <div class="wp_easycart_wizard_success_box_right">
            <div class="wp_easycart_wizard_success_box_button"><a href="<?php $storepageid = get_option( 'ec_option_storepage' ); $store_page = get_permalink( $storepageid ); echo $store_page; ?>" style="background:#03A9F4;" target="_blank"><?php _e( 'View Your Store', 'wp-easycart' ); ?></a></div>
        </div>
    	<div style="clear:both;"></div>
    </div>
    <?php }?>
    <div class="wp_easycart_wizard_success_box">
        <div class="wp_easycart_wizard_success_box_left">
            <div class="wp_easycart_wizard_success_box_title"><?php _e( 'NEXT STEP', 'wp-easycart' ); ?></div>
            <div class="wp_easycart_wizard_success_box_content"><?php _e( 'You\'re ready to add your first product.', 'wp-easycart' ); ?></div>
        </div>
        <div class="wp_easycart_wizard_success_box_right">
            <div class="wp_easycart_wizard_success_box_button"><a href="admin.php?page=wp-easycart-products&subpage=products&ec_admin_form_action=add-new" onclick="wp_easycart_admin_open_slideout( 'new_product_box' ); return false;"><?php _e( 'Create a Product', 'wp-easycart' ); ?></a></div>
        </div>
    	<div style="clear:both;"></div>
    </div>
    <?php if( class_exists( "WooCommerce" ) ){ ?>
    <div class="wp_easycart_wizard_success_box">
        <div class="wp_easycart_wizard_success_box_left">
            <div class="wp_easycart_wizard_success_box_title"><?php _e( 'IMPORT FROM WOOCOMMERCE', 'wp-easycart' ); ?></div>
            <div class="wp_easycart_wizard_success_box_content"><?php _e( 'It looks like you already have WooCommerce installed -- Import automatically to EasyCart now!', 'wp-easycart' ); ?></div>
        </div>
        <div class="wp_easycart_wizard_success_box_right">
            <div class="wp_easycart_wizard_success_box_button"><a href="admin.php?page=wp-easycart-settings&subpage=cart-importer" target="_blank"><?php _e( 'Import Products', 'wp-easycart' ); ?></a></div>
        </div>
    	<div style="clear:both;"></div>
    </div>
    <?php }?>
    <?php if( get_option( 'ec_option_square_access_token' ) != '' ){ ?>
    <div class="wp_easycart_wizard_success_box">
        <div class="wp_easycart_wizard_success_box_left">
            <div class="wp_easycart_wizard_success_box_title"><?php _e( 'IMPORT FROM SQUARE', 'wp-easycart' ); ?></div>
            <div class="wp_easycart_wizard_success_box_content"><?php _e( 'It looks like you are connected with SquareUp Payments -- Import automatically to EasyCart now!', 'wp-easycart' ); ?></div>
        </div>
        <div class="wp_easycart_wizard_success_box_right">
            <div class="wp_easycart_wizard_success_box_button"><a href="admin.php?page=wp-easycart-settings&subpage=cart-importer" target="_blank"><?php _e( 'Import Products', 'wp-easycart' ); ?></a></div>
        </div>
    	<div style="clear:both;"></div>
    </div>
    <?php }?>
    <?php $trial_note = '<div class="wp_easycart_wizard_success_box">
        <div class="wp_easycart_wizard_success_box_left">
            <div class="wp_easycart_wizard_success_box_title">' . __( 'TRY PRO FREE', 'wp-easycart' ) . '</div>
            <div class="wp_easycart_wizard_success_box_content">' . __( 'Want to try all the features WP EasyCart has to offer? Try our 14 day FREE PRO Trial.', 'wp-easycart' ) . '</div>
        </div>
        <div class="wp_easycart_wizard_success_box_right">
            <div class="wp_easycart_wizard_success_box_button"><a href="admin.php?page=wp-easycart-registration&ec_trial=start" target="_blank">' . __( 'Install PRO Trial', 'wp-easycart' ) . '</a></div>
        </div>
    	<div style="clear:both;"></div>
    </div>';
	echo apply_filters( 'wp_easycart_trial_start_content', $trial_note );
	?>
    <div class="wp_easycart_wizard_success_box">
        <div class="wp_easycart_wizard_success_box_title"><?php _e( 'Learn More', 'wp-easycart' ); ?></div>
        <div><a href="http://support.wpeasycart.com/video-tutorials/" target="_blank"><?php _e( 'Watch our video tutorials', 'wp-easycart' ); ?></a></div>
        <div><a href="http://docs.wpeasycart.com/wp-easycart-administrative-console-guide/" target="_blank"><?php _e( 'Read our documentation guide', 'wp-easycart' ); ?></a></div>
        <div><a href="https://www.wpeasycart.com/contact-information/" target="_blank"><?php _e( 'Submit a sales question', 'wp-easycart' ); ?></a></div>
    </div>
</div>
<?php
	wp_easycart_admin( )->load_new_slideout( 'product' );
	wp_easycart_admin( )->load_new_slideout( 'manufacturer' );
	wp_easycart_admin( )->load_new_slideout( 'optionset' );
?>