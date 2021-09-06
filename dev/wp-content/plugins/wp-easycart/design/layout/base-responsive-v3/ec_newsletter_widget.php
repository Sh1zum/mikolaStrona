<div class="ec_newsletter_widget">
    
    <div class="ec_newsletter_post_submit"><?php echo $GLOBALS['language']->get_text( 'ec_newsletter_popup', 'signup_form_success_subtitle' ); ?></div>

	<div class="ec_newsletter_pre_submit">
    <form action="#" method="POST">
        <?php if( isset( $widget_name_label ) && $widget_name_label != "" ){ ?>
		<div><?php echo $widget_name_label; ?>: <input type="text" name="ec_newsletter_name" id="ec_newsletter_name_widget" /></div>
    	<?php }?>
		<div><?php echo $widget_label; ?>: <input type="text" name="ec_newsletter_email" id="ec_newsletter_email_widget" /></div>
		<input type="submit" value="<?php echo $widget_submit; ?>" onclick="ec_submit_newsletter_signup_widget( ); return false;" />
	</form>
    </div>
    
</div>