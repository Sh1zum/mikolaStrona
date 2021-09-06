<div class="ec_admin_head_section">

    <div class="ec_admin_head_content">
        <h1><?php _e( 'Reports', 'wp-easycart' ); ?></h1>
        <h3><?php _e( 'Track your progress and your sales grow', 'wp-easycart' ); ?></h3>
    </div>
    
    <div class="ec_admin_head_buttons_row">
        <div class="ec_admin_head_button">
        	<span><a href="admin.php?page=wp-easycart-users" title="<?php _e( 'Store Users', 'wp-easycart' ); ?>"></a></span>
            <div class="dashicons-before dashicons-groups"></div>
        </div>
        <div class="ec_admin_head_button">
        	<span><a href="admin.php?page=wp-easycart-products&amp;subpage=customer-reviews" title="<?php _e( 'Customer Reviews', 'wp-easycart' ); ?>"></a></span>
            <div class="dashicons-before dashicons-admin-comments"></div>
        </div>
        <div class="ec_admin_head_button">
        	<span><a href="admin.php?page=wp-easycart-orders" title="<?php _e( 'Orders', 'wp-easycart' ); ?>"></a></span>
            <div class="dashicons-before dashicons-tag"></div>
        </div>
        <div class="ec_admin_head_button">
        	<span><a href="admin.php?page=wp-easycart-settings" title="<?php _e( 'Settings', 'wp-easycart' ); ?>"></a></span>
            <div class="dashicons-before dashicons-admin-tools"></div>
        </div>
    </div>

</div>
	
<div class="ec_admin_graph_header">
	
	<select id="ec_admin_chart_data_type_select">
    	<option value="orders"><?php _e( 'Orders', 'wp-easycart' ); ?></option>
    	<option value="customers"><?php _e( 'Customers', 'wp-easycart' ); ?></option>
    	<option value="stock"><?php _e( 'Stock', 'wp-easycart' ); ?></option>
    </select>
    
    <select id="ec_admin_chart_date_type_select">
    	<option value="daily"><?php _e( 'Daily', 'wp-easycart' ); ?></option>
    	<option value="weekly"><?php _e( 'Weekly', 'wp-easycart' ); ?></option>
    	<option value="monthly"><?php _e( 'Monthly', 'wp-easycart' ); ?></option>
    	<option value="yearly"><?php _e( 'Yearly', 'wp-easycart' ); ?></option>
    </select>
    
    <select id="ec_admin_chart_type_select">
    	<option value="sales"><?php _e( 'Sales', 'wp-easycart' ); ?> (<?php echo get_option( 'ec_option_base_currency' ); ?>)</option>
        <option value="items_sold"><?php _e( 'Items Sold', 'wp-easycart' ); ?></option>
    	<option value="abandonment"><?php _e( 'Carts Abandoned', 'wp-easycart' ); ?></option>
    </select>

</div>
