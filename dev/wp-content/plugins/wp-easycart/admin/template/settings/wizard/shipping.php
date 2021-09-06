<form action="" method="POST" name="wpeasycart_admin_setup_wizard_form" id="wpeasycart_admin_setup_wizard_form" novalidate="novalidate">
<input type="hidden" name="ec_admin_form_action" id="ec_admin_form_action" value="process-wizard-shipping">
<h3><?php _e( 'Shipping', 'wp-easycart' ); ?></h3>
<p><?php _e( 'WP EasyCart offers static shipping rates, weight based rates, cart total based rates, and a few more by default. You can upgrade to PRO and activate live shipping rates with UPS, USPS, FedEx, DHL, CanadaPost, or Australia Post later. For now, please choose a preferred method below and let EasyCart install some common shipping rates for you and your store\'s location.', 'wp-easycart' ); ?></p>
<div class="ec_admin_wizard_input_row">
	<div class="ec_admin_wizard_input_row_title"><?php _e( 'Shipping Method', 'wp-easycart' ); ?></div>
	<div class="ec_admin_wizard_input_row_input"><select name="shipping_method" id="wp_easycart_shipping_method" class="select2-basic">
    	<option value="static"><?php _e( 'Static Rates', 'wp-easycart' ); ?></option>
        <option value="price"><?php _e( 'Cart Total Based Rates', 'wp-easycart' ); ?></option>
        <option value="weight"><?php _e( 'Weight Based Rates', 'wp-easycart' ); ?></option>
    </select></div>
</div>
<?php $trial_note = '<div class="ec_admin_wizard_input_row">
	<div class="ec_admin_wizard_input_row_title">' . __( 'Live Shipping Rates', 'wp-easycart' ) . '</div>
	<div class="ec_admin_wizard_input_row_input" style="padding-right:100px;">' . __( 'UPS, FedEx, USPS, DHL, CanadaPost, and Australia Post are all available in Professional or Premium', 'wp-easycart' ) . '<br /><a href="http://docs.wpeasycart.com/wp-easycart-administrative-console-guide/?section=shipping-rates" target="_blank">' . __( 'VIEW DETAILS', 'wp-easycart' ) . '</a> | <a href="admin.php?page=wp-easycart-registration&ec_trial=start" target="_blank">' . __( 'TRY WITH 14 DAY FREE TRIAL', 'wp-easycart' ) . '</a></div>
    <div style="clear:both;"></div>
</div>';
echo apply_filters( 'wp_easycart_trial_start_content', $trial_note ); ?>
<div class="ec_admin_wizard_button_bar">
	<a href="admin.php?page=wp-easycart-settings&ec_admin_form_action=skip-wizard" class="ec_admin_wizard_quit_button"><?php _e( 'Skip Setup Wizard', 'wp-easycart' ); ?></a>
    <a href="admin.php?page=wp-easycart-products&subpage=products"><?php _e( 'Setup Later', 'wp-easycart' ); ?></a>
    <input type="submit" class="ec_admin_wizard_next_button" value="<?php _e( 'Save &amp; Continue', 'wp-easycart' ); ?>" />
</div>