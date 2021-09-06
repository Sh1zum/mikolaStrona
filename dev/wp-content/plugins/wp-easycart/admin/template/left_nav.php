<?php
$admin_page_variable = "";
if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-products" ){
	$admin_page_variable = "wp-easycart-products";
}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-orders" ){
	$admin_page_variable = "wp-easycart-orders";
}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-users" ){
	$admin_page_variable = "wp-easycart-users";
}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-rates" ){
	$admin_page_variable = "wp-easycart-rates";
}else if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-registration" ){
	$admin_page_variable = "wp-easycart-registration";
}

$license_data = false; $days_left = 0;
if( function_exists( 'wp_easycart_admin_license' ) ){
    $license_data = wp_easycart_admin_license( )->license_data;
    $test_now = time( );
    $test_expiration = strtotime( $license_data->support_end_date );
    $test_diff = $test_expiration - $test_now;
    $days_left = round( $test_diff / ( 60 * 60 * 24 ) );
    $days_left = ( $days_left < 0 ) ? 0 : $days_left; // No Negative
}
?>

<!--DASHBOARD-->
<div class="ec_admin_left_nav_item ec_admin_default_color2-border ec_admin_default_color1-background-gradient<?php if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-dashboard" ){ ?> ec_admin_left_nav_selected<?php }?>">
	<div class="ec_admin_left_nav_icon dashicons-before dashicons-analytics"></div>
	<div class="ec_admin_left_nav_label"><a href="admin.php?page=wp-easycart-dashboard"><?php _e( 'Reports', 'wp-easycart' ); ?></a></div>
</div>
 
<!--STORE STATUS-->
<?php if( $license_data && $license_data->is_trial && $days_left <= 4 ){ ?>
<div class="ec_admin_left_nav_item ec_admin_default_color2-border ec_admin_default_color1-background-gradient<?php if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-license-status" ){ ?> ec_admin_left_nav_selected<?php }?>" style="background:#d43636 !important;">
	<div class="ec_admin_left_nav_icon dashicons-before dashicons-clipboard"></div>
	<div class="ec_admin_left_nav_label"><a href="admin.php?page=wp-easycart-license-status"><?php _e( 'Store Status', 'wp-easycart' ); ?> <span class="dashicons dashicons-warning"></span></a></div>
</div>

<?php }else if( $license_data && $license_data->is_trial && $days_left <= 9 ){ ?>
<div class="ec_admin_left_nav_item ec_admin_default_color2-border ec_admin_default_color1-background-gradient<?php if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-license-status" ){ ?> ec_admin_left_nav_selected<?php }?>" style="background:#dd9d22 !important;">
	<div class="ec_admin_left_nav_icon dashicons-before dashicons-clipboard"></div>
	<div class="ec_admin_left_nav_label"><a href="admin.php?page=wp-easycart-license-status"><?php _e( 'Store Status', 'wp-easycart' ); ?> <span class="dashicons dashicons-warning"></span></a></div>
</div>

<?php }else if( $license_data && $license_data->is_trial ){ ?>
<div class="ec_admin_left_nav_item ec_admin_default_color2-border ec_admin_default_color1-background-gradient<?php if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-license-status" ){ ?> ec_admin_left_nav_selected<?php }?>">
	<div class="ec_admin_left_nav_icon dashicons-before dashicons-clipboard"></div>
	<div class="ec_admin_left_nav_label"><a href="admin.php?page=wp-easycart-license-status"><?php _e( 'Store Status', 'wp-easycart' ); ?> <span class="dashicons dashicons-warning"></span></a></div>
</div>

<?php }else if( $license_data && $days_left <= 0 ){ ?>
<div class="ec_admin_left_nav_item ec_admin_default_color2-border ec_admin_default_color1-background-gradient<?php if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-license-status" ){ ?> ec_admin_left_nav_selected<?php }?>" style="background:#d43636 !important;">
	<div class="ec_admin_left_nav_icon dashicons-before dashicons-clipboard"></div>
	<div class="ec_admin_left_nav_label"><a href="admin.php?page=wp-easycart-license-status"><?php _e( 'Store Status', 'wp-easycart' ); ?> <span class="dashicons dashicons-warning"></span></a></div>
</div>

<?php }else if( $license_data && $days_left <= 35 ){ ?>
<div class="ec_admin_left_nav_item ec_admin_default_color2-border ec_admin_default_color1-background-gradient<?php if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-license-status" ){ ?> ec_admin_left_nav_selected<?php }?>" style="background:#e03333 !important;">
	<div class="ec_admin_left_nav_icon dashicons-before dashicons-clipboard"></div>
	<div class="ec_admin_left_nav_label"><a href="admin.php?page=wp-easycart-license-status"><?php _e( 'Store Status', 'wp-easycart' ); ?> <span class="dashicons dashicons-warning"></span></a></div>
</div>

<?php }else if( $license_data && $days_left <= 69 ){ ?>
<div class="ec_admin_left_nav_item ec_admin_default_color2-border ec_admin_default_color1-background-gradient<?php if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-license-status" ){ ?> ec_admin_left_nav_selected<?php }?>" style="background:#dd9d22 !important;">
	<div class="ec_admin_left_nav_icon dashicons-before dashicons-clipboard"></div>
	<div class="ec_admin_left_nav_label"><a href="admin.php?page=wp-easycart-license-status"><?php _e( 'Store Status', 'wp-easycart' ); ?> <span class="dashicons dashicons-warning"></span></a></div>
</div>

<?php }else{ ?>
<div class="ec_admin_left_nav_item ec_admin_default_color2-border ec_admin_default_color1-background-gradient<?php if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-license-status" ){ ?> ec_admin_left_nav_selected<?php }?>">
	<div class="ec_admin_left_nav_icon dashicons-before dashicons-clipboard"></div>
	<div class="ec_admin_left_nav_label"><a href="admin.php?page=wp-easycart-license-status"><?php _e( 'Store Status', 'wp-easycart' ); ?></a></div>
</div>
<?php }?>

<!--PRODUCTS-->
<div class="ec_admin_left_nav_item ec_admin_default_color2-border ec_admin_default_color1-background-gradient<?php if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-products" ){ ?> ec_admin_left_nav_selected<?php }?>">
	<div class="ec_admin_left_nav_icon dashicons-before dashicons-products"></div>
	<div class="ec_admin_left_nav_label"><a href="admin.php?page=wp-easycart-products&subpage=products"><?php _e( 'Products', 'wp-easycart' ); ?></a></div>
</div>

<div class="ec_admin_left_submenu<?php if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-products" ){ ?> ec_admin_left_submenu_open<?php }?>" id="ec_admin_products_submenu">
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-products" && ( ( isset( $_GET['subpage'] ) && $_GET['subpage'] == "products" ) || !isset( $_GET['subpage'] ) ) ){ ?> ec_admin_left_nav_selected<?php }?>" id="ec_admin_products_submenu_item">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-products&subpage=products"><?php _e( 'Products', 'wp-easycart' ); ?></a></div>
    </div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "inventory" ){ ?> ec_admin_left_nav_selected<?php }?>" id="ec_admin_options_submenu_item">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-products&subpage=inventory"><?php _e( 'Inventory', 'wp-easycart' ); ?></a></div>
    </div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && ( $_GET['subpage'] == "option" || $_GET['subpage'] == "optionitems" ) ){ ?> ec_admin_left_nav_selected<?php }?>" id="ec_admin_options_submenu_item">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-products&subpage=option"><?php _e( 'Option Sets', 'wp-easycart' ); ?></a></div>
    </div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && ( $_GET['subpage'] == "category" || $_GET['subpage'] == "category-products" || $_GET['subpage'] == "category-products-manage" ) ){ ?> ec_admin_left_nav_selected<?php }?>" id="ec_admin_categories_submenu_item">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-products&subpage=category"><?php _e( 'Categories', 'wp-easycart' ); ?></a></div>
    </div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && ( $_GET['subpage'] == "menus" || $_GET['subpage'] == "submenus" || $_GET['subpage'] == "subsubmenus" ) ){ ?> ec_admin_left_nav_selected<?php }?>" id="ec_admin_menus_submenu_item">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-products&subpage=menus"><?php _e( 'Menus', 'wp-easycart' ); ?></a></div>
    </div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "manufacturers" ){ ?> ec_admin_left_nav_selected<?php }?>" id="ec_admin_manufacturers_submenu_item">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-products&subpage=manufacturers"><?php _e( 'Manufacturers', 'wp-easycart' ); ?></a></div>
    </div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "reviews" ){ ?> ec_admin_left_nav_selected<?php }?>" id="ec_admin_reviews_submenu_item">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-products&subpage=reviews"><?php _e( 'Product Reviews', 'wp-easycart' ); ?></a></div>
    </div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "subscriptionplans" ){ ?> ec_admin_left_nav_selected<?php }?>" id="ec_admin_subscriptionplans_submenu_item">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-products&subpage=subscriptionplans"><?php _e( 'Subscription Plans', 'wp-easycart' ); ?><?php echo apply_filters( 'wp_easycart_admin_lock_icon', ' <span class="dashicons dashicons-lock" style="color:#FC0; float:right;"></span>' ); ?></a></div>
    </div>
</div>

<!--ORDERS-->
<div class="ec_admin_left_nav_item ec_admin_default_color2-border ec_admin_default_color1-background-gradient<?php if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-orders" ){ ?> ec_admin_left_nav_selected<?php }?>">
	<div class="ec_admin_left_nav_icon dashicons-before dashicons-tag"></div>
	<div class="ec_admin_left_nav_label"><a href="admin.php?page=wp-easycart-orders&subpage=orders"><?php _e( 'Orders', 'wp-easycart' ); ?></a></div>
</div>

<div class="ec_admin_left_submenu<?php if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-orders" ){ ?> ec_admin_left_submenu_open<?php }?>" id="ec_admin_orders_submenu">
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-orders" && ( ( isset( $_GET['subpage'] ) && $_GET['subpage'] == "orders" ) || !isset( $_GET['subpage'] ) ) ){ ?> ec_admin_left_nav_selected<?php }?>" id="ec_admin_orders_submenu_item">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-orders&subpage=orders"><?php _e( 'Orders', 'wp-easycart' ); ?></a></div>
    </div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "subscriptions" ){ ?> ec_admin_left_nav_selected<?php }?>" id="ec_admin_subscriptions_submenu_item">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-orders&subpage=subscriptions"><?php _e( 'Subscriptions', 'wp-easycart' ); ?><?php echo apply_filters( 'wp_easycart_admin_lock_icon', ' <span class="dashicons dashicons-lock" style="color:#FC0; float:right;"></span>' ); ?></a></div>
    </div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "downloads" ){ ?> ec_admin_left_nav_selected<?php }?>" id="ec_admin_downloads_submenu_item">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-orders&subpage=downloads"><?php _e( 'Manage Downloads', 'wp-easycart' ); ?><?php echo apply_filters( 'wp_easycart_admin_lock_icon', ' <span class="dashicons dashicons-lock" style="color:#FC0; float:right;"></span>' ); ?></a></div>
    </div>
</div>

<!--USERS-->
<div class="ec_admin_left_nav_item ec_admin_default_color2-border ec_admin_default_color1-background-gradient<?php if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-users" ){ ?> ec_admin_left_nav_selected<?php }?>">
	<div class="ec_admin_left_nav_icon dashicons-before dashicons-groups"></div>
	<div class="ec_admin_left_nav_label"><a href="admin.php?page=wp-easycart-users&subpage=accounts"><?php _e( 'Users', 'wp-easycart' ); ?></a></div>
</div>

<div class="ec_admin_left_submenu<?php if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-users" ){ ?> ec_admin_left_submenu_open<?php }?>" id="ec_admin_users_submenu">
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-users" && ( ( isset( $_GET['subpage'] ) && $_GET['subpage'] == "accounts" ) || !isset( $_GET['subpage'] ) ) ){ ?> ec_admin_left_nav_selected<?php }?>" id="ec_admin_accounts_submenu_item">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-users&subpage=accounts"><?php _e( 'User Accounts', 'wp-easycart' ); ?></a></div>
    </div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "user-roles" ){ ?> ec_admin_left_nav_selected<?php }?>" id="ec_admin_subscribers_submenu_item">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-users&subpage=user-roles"><?php _e( 'User Roles', 'wp-easycart' ); ?></a></div>
    </div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "subscribers" ){ ?> ec_admin_left_nav_selected<?php }?>" id="ec_admin_subscribers_submenu_item">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-users&subpage=subscribers"><?php _e( 'Subscribers', 'wp-easycart' ); ?></a></div>
    </div>

</div>

<!--MARKETING-->
<div class="ec_admin_left_nav_item ec_admin_default_color2-border ec_admin_default_color1-background-gradient<?php if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-rates" ){ ?> ec_admin_left_nav_selected<?php }?>">
	<div class="ec_admin_left_nav_icon dashicons-before dashicons-performance"></div>
	<div class="ec_admin_left_nav_label"><a href="admin.php?page=wp-easycart-rates&subpage=coupons"><?php _e( 'Marketing', 'wp-easycart' ); ?><?php echo apply_filters( 'wp_easycart_admin_lock_icon', ' <span class="dashicons dashicons-lock" style="color:#FC0; float:right;"></span>' ); ?></a></div>
</div>

<div class="ec_admin_left_submenu<?php if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-rates" ){ ?> ec_admin_left_submenu_open<?php }?>" id="ec_admin_rates_submenu">
    
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-rates" && ( ( isset( $_GET['subpage'] ) && $_GET['subpage'] == "coupons" ) || !isset( $_GET['subpage'] ) ) ){ ?> ec_admin_left_nav_selected<?php }?>">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-rates&subpage=coupons"><?php _e( 'Coupons', 'wp-easycart' ); ?><?php echo apply_filters( 'wp_easycart_admin_lock_icon', ' <span class="dashicons dashicons-lock" style="color:#FC0; float:right;"></span>' ); ?></a></div>
    </div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "promotions" ){ ?> ec_admin_left_nav_selected<?php }?>">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-rates&subpage=promotions"><?php _e( 'Promotions', 'wp-easycart' ); ?><?php echo apply_filters( 'wp_easycart_admin_lock_icon', ' <span class="dashicons dashicons-lock" style="color:#FC0; float:right;"></span>' ); ?></a></div>
    </div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "gift-cards" ){ ?> ec_admin_left_nav_selected<?php }?>">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-rates&subpage=gift-cards"><?php _e( 'Gift Cards', 'wp-easycart' ); ?><?php echo apply_filters( 'wp_easycart_admin_lock_icon', ' <span class="dashicons dashicons-lock" style="color:#FC0; float:right;"></span>' ); ?></a></div>
    </div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "abandon-cart" ){ ?> ec_admin_left_nav_selected<?php }?>">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-rates&subpage=abandon-cart"><?php _e( 'Abandoned Cart', 'wp-easycart' ); ?><?php echo apply_filters( 'wp_easycart_admin_lock_icon', ' <span class="dashicons dashicons-lock" style="color:#FC0; float:right;"></span>' ); ?></a></div>
    </div>
</div>

<!--SETTINGS-->
<div class="ec_admin_left_nav_item ec_admin_default_color2-border ec_admin_default_color1-background-gradient<?php if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" ){ ?> ec_admin_left_nav_selected<?php }?>">
	<div class="ec_admin_left_nav_icon dashicons-before dashicons-admin-tools"></div>
	<div class="ec_admin_left_nav_label"><a href="admin.php?page=wp-easycart-settings"><?php _e( 'Settings', 'wp-easycart' ); ?></a></div>
</div>

<div class="ec_admin_left_submenu<?php if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-settings" ){ ?> ec_admin_left_submenu_open<?php }?>" id="ec_admin_settings_submenu">
    <div class="ec_admin_left_nav_subitem_headitem"><?php _e( 'Panel Settings', 'wp-easycart' ); ?></div>
    <div class="ec_admin_left_nav_subitem<?php if( !isset( $_GET['subpage'] ) || ( isset( $_GET['subpage'] ) && $_GET['subpage'] == "initial-setup" ) ){ ?> ec_admin_left_nav_selected<?php }?>">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-settings&subpage=initial-setup"><?php _e( 'Initial Setup', 'wp-easycart' ); ?></a></div>
    </div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "products" ){ ?> ec_admin_left_nav_selected<?php }?>">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-settings&subpage=products"><?php _e( 'Products', 'wp-easycart' ); ?></a></div>
    </div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "checkout" ){ ?> ec_admin_left_nav_selected<?php }?>">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-settings&subpage=checkout"><?php _e( 'Checkout', 'wp-easycart' ); ?></a></div>
    </div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "account" ){ ?> ec_admin_left_nav_selected<?php }?>">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-settings&subpage=account"><?php _e( 'Accounts', 'wp-easycart' ); ?></a></div>
    </div>
    
    <div class="ec_admin_left_nav_subitem_headitem"><?php _e( 'Financial Settings', 'wp-easycart' ); ?></div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "payment" ){ ?> ec_admin_left_nav_selected<?php }?>">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-settings&subpage=payment"><?php _e( 'Payment', 'wp-easycart' ); ?></a></div>
    </div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "tax" ){ ?> ec_admin_left_nav_selected<?php }?>">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-settings&subpage=tax"><?php _e( 'Taxes', 'wp-easycart' ); ?></a></div>
    </div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "shipping-settings" ){ ?> ec_admin_left_nav_selected<?php }?>">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-settings&subpage=shipping-settings"><?php _e( 'Shipping Settings', 'wp-easycart' ); ?></a></div>
    </div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "shipping-rates" ){ ?> ec_admin_left_nav_selected<?php }?>">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-settings&subpage=shipping-rates"><?php _e( 'Shipping Rates', 'wp-easycart' ); ?></a></div>
    </div>
    
    <div class="ec_admin_left_nav_subitem_headitem"><?php _e( 'Customize', 'wp-easycart' ); ?></div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "miscellaneous" ){ ?> ec_admin_left_nav_selected<?php }?>">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-settings&subpage=miscellaneous"><?php _e( 'Additional Settings', 'wp-easycart' ); ?></a></div>
    </div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "design" ){ ?> ec_admin_left_nav_selected<?php }?>">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-settings&subpage=design"><?php _e( 'Design', 'wp-easycart' ); ?></a></div>
    </div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "language-editor" ){ ?> ec_admin_left_nav_selected<?php }?>">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-settings&subpage=language-editor"><?php _e( 'Language', 'wp-easycart' ); ?></a></div>
    </div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "email-setup" ){ ?> ec_admin_left_nav_selected<?php }?>">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-settings&subpage=email-setup"><?php _e( 'Email', 'wp-easycart' ); ?></a></div>
    </div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "country" ){ ?> ec_admin_left_nav_selected<?php }?>">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-settings&subpage=country"><?php _e( 'Countries', 'wp-easycart' ); ?></a></div>
    </div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "states" ){ ?> ec_admin_left_nav_selected<?php }?>">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-settings&subpage=states"><?php _e( 'States/Territories', 'wp-easycart' ); ?></a></div>
    </div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "perpage" ){ ?> ec_admin_left_nav_selected<?php }?>">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-settings&subpage=perpage"><?php _e( 'Per Page Options', 'wp-easycart' ); ?></a></div>
    </div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "pricepoint" ){ ?> ec_admin_left_nav_selected<?php }?>">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-settings&subpage=pricepoint"><?php _e( 'Price Points', 'wp-easycart' ); ?></a></div>
    </div>
    
    <div class="ec_admin_left_nav_subitem_headitem"><?php _e( 'Integrations', 'wp-easycart' ); ?></div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "third-party" ){ ?> ec_admin_left_nav_selected<?php }?>">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-settings&subpage=third-party"><?php _e( 'Third Party', 'wp-easycart' ); ?></a></div>
    </div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "cart-importer" ){ ?> ec_admin_left_nav_selected<?php }?>">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-settings&subpage=cart-importer"><?php _e( 'Cart Importer', 'wp-easycart' ); ?></a></div>
    </div>
    
    <div class="ec_admin_left_nav_subitem_headitem"><?php _e( 'Troubleshoot', 'wp-easycart' ); ?></div>
    <div class="ec_admin_left_nav_subitem<?php if( isset( $_GET['subpage'] ) && $_GET['subpage'] == "logs" ){ ?> ec_admin_left_nav_selected<?php }?>">
        <div class="ec_admin_left_nav_sublabel"><a href="admin.php?page=wp-easycart-settings&subpage=logs"><?php _e( 'Log Entries', 'wp-easycart' ); ?></a></div>
    </div>
</div>
 
<!--DIAGNOSTICS-->
<div class="ec_admin_left_nav_item ec_admin_default_color2-border ec_admin_default_color1-background-gradient<?php if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-status" ){ ?> ec_admin_left_nav_selected<?php }?>">
	<div class="ec_admin_left_nav_icon dashicons-before dashicons-hammer"></div>
	<div class="ec_admin_left_nav_label"><a href="admin.php?page=wp-easycart-status&subpage=store-status"><?php _e( 'Diagnostics', 'wp-easycart' ); ?></a></div>
</div>

<!--registration-->
<div class="ec_admin_left_nav_item ec_admin_default_color2-border ec_admin_default_color1-background-gradient<?php if( isset( $_GET['page'] ) && $_GET['page'] == "wp-easycart-registration" ){ ?> ec_admin_left_nav_selected<?php }?>">
	<div class="ec_admin_left_nav_icon dashicons-before dashicons-unlock"></div>
	<div class="ec_admin_left_nav_label"><a href="admin.php?page=wp-easycart-registration&subpage=registration"><?php _e( 'Registration', 'wp-easycart' ); ?><?php echo apply_filters( 'wp_easycart_admin_lock_icon', ' <span class="dashicons dashicons-lock" style="color:#FC0; float:right;"></span>' ); ?></a></div>
</div>