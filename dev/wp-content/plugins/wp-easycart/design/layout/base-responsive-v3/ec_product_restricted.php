<?php 
if( isset( $_GET['account_error'] ) ){
	$error_text = $GLOBALS['language']->get_text( "ec_errors", $_GET['account_error'] );
	if( $error_text )
		echo "<div class=\"ec_account_error\"><div>" . $error_text . "</div></div>";
}
?>
<div class="ec_restricted"><?php echo $GLOBALS['language']->get_text( 'product_page', 'product_page_restricted_line_1' ); ?></div>

<?php if( $GLOBALS['ec_user']->user_id == "" || $GLOBALS['ec_user']->user_id == 0 ){ ?>    
<div class="ec_account_left ec_account_login">

<form action="<?php echo $this->account_page; ?>" method="POST">  

	<input type="hidden" name="ec_goto_page" value="store" />   
    
    <div class="ec_cart_header ec_top">
    	<?php echo $GLOBALS['language']->get_text( 'account_login', 'account_login_title' )?>
    </div>
    <div class="ec_account_subheader">
   		<?php echo $GLOBALS['language']->get_text( 'account_login', 'account_login_sub_title' )?>
    </div>
    
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

</div>

<div class="ec_account_right ec_account_login">
    
    <div class="ec_cart_header ec_top">
    	<?php echo $GLOBALS['language']->get_text( 'account_login', 'account_new_user_title' )?>
    </div>
    
    <div class="ec_account_subheader">
        <?php echo $GLOBALS['language']->get_text( 'account_login', 'account_new_user_sub_title' )?>
    </div>
    
    <div class="ec_cart_input_row">
        <?php echo $GLOBALS['language']->get_text( 'account_login', 'account_new_user_message' )?>
    </div>
    
    <div class="ec_cart_button_row">
    	<a href="<?php echo $this->account_page; ?>?ec_page=register" class="ec_account_login_create_account_button"><?php echo $GLOBALS['language']->get_text( 'account_login', 'account_new_user_button' ); ?></a>
    </div>
    
</div>
<?php }?>