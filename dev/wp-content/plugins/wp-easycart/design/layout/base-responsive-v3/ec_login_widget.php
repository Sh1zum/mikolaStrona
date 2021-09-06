<?php if( $GLOBALS['ec_user']->user_id == "" || $GLOBALS['ec_user']->user_id == 0 ){ ?>

<form action="<?php echo $account_page; ?>" method="POST">    
    
    <div class="ec_cart_input_row">
        <label for="ec_account_login_email"><?php echo $GLOBALS['language']->get_text( 'account_login', 'account_login_email_label' )?>*</label>
        <input type="text" name="ec_account_login_email_widget" id="ec_account_login_email_widget" class="ec_account_login_input_field" autocomplete="off" autocapitalize="off" />
    </div>
    
    <div class="ec_cart_error_row" id="ec_account_login_email_error">
        <?php echo $GLOBALS['language']->get_text( 'cart_form_notices', 'cart_notice_please_enter_your' ); ?> <?php echo $GLOBALS['language']->get_text( 'cart_login', 'cart_login_email_label' ); ?>
    </div>
    
    <div class="ec_cart_input_row">
        <label for="ec_account_login_password_widget"><?php echo $GLOBALS['language']->get_text( 'account_login', 'account_login_password_label' )?>*</label>
        <input type="password" name="ec_account_login_password_widget" id="ec_account_login_password_widget" class="ec_account_login_input_field" />
    </div>
    <div class="ec_cart_error_row" id="ec_account_login_password_error">
        <?php echo $GLOBALS['language']->get_text( 'cart_form_notices', 'cart_notice_please_enter_your' ); ?> <?php echo $GLOBALS['language']->get_text( 'cart_login', 'cart_login_password_label' ); ?>
    </div>
    
     <div class="ec_cart_button_row">
        <input type="submit" value="<?php echo $GLOBALS['language']->get_text( 'account_login', 'account_login_button' ); ?>" class="ec_login_widget_button" />
    </div>
    
    <input type="hidden" name="ec_account_form_action" value="login">
    
</form>

<?php }else{ ?>

<strong><?php echo $GLOBALS['language']->get_text( 'ec_login_widget', 'hello_text' ); ?>, <?php echo $GLOBALS['ec_user']->first_name; ?></strong><br />
<a href="<?php echo $account_page; ?>"><?php echo $GLOBALS['language']->get_text( 'ec_login_widget', 'dashboard_text' ); ?></a><br />
<a href="<?php echo $account_page; ?>?ec_page=orders"><?php echo $GLOBALS['language']->get_text( 'ec_login_widget', 'order_history_text' ); ?></a><br />
<a href="<?php echo $account_page; ?>?ec_page=billing_information"><?php echo $GLOBALS['language']->get_text( 'ec_login_widget', 'billing_info_text' ); ?></a><br />
<a href="<?php echo $account_page; ?>?ec_page=shipping_information"><?php echo $GLOBALS['language']->get_text( 'ec_login_widget', 'shipping_info_text' ); ?></a><br />
<a href="<?php echo $account_page; ?>?ec_page=password"><?php echo $GLOBALS['language']->get_text( 'ec_login_widget', 'change_password_text' ); ?></a><br />
<a href="<?php echo $account_page; ?>?ec_page=logout"><?php echo $GLOBALS['language']->get_text( 'ec_login_widget', 'sign_out_text' ); ?></a>

<?php } ?>