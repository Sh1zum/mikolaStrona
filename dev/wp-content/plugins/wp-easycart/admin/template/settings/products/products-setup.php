<form action="admin.php?page=wp-easycart-settings&subpage=products" method="POST" name="wpeasycart_admin_form" id="wpeasycart_admin_form" novalidate="novalidate">
    <input type="hidden" name="ec_admin_form_action" value="save-products-setup" />
    
    <?php do_action( 'wpeasycart_admin_products_success' ); ?>
    
    <div class="ec_admin_settings_panel">
        
        <div class="ec_admin_important_numbered_list">
            
            <?php do_action( 'wpeasycart_admin_products_setup' ); ?>
            
        </div>
    
    </div>
</form>