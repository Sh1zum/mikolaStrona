<form action="admin.php?page=wp-easycart-settings&subpage=tax" method="POST" name="wpeasycart_admin_form" id="wpeasycart_admin_form_tax_setup" novalidate="novalidate">

    <input type="hidden" name="ec_admin_form_action" value="save-taxes" />
    
	<?php do_action( 'wpeasycart_admin_taxes_success' ); ?>
    
    <div class="ec_admin_settings_panel">
        
        <div class="ec_admin_important_numbered_list">
                
            <?php do_action( 'wpeasycart_admin_tax_setup' ); ?>
            
        </div>
        
    </div>
</form>