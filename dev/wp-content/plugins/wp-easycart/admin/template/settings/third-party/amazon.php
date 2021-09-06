<div class="ec_admin_list_line_item ec_admin_demo_data_line">
            
	<?php wp_easycart_admin( )->preloader->print_preloader( "ec_admin_amazon_settings_loader" ); ?>
    
    <div class="ec_admin_settings_label">
        <div class="dashicons-before dashicons-admin-generic"></div>
        <span><?php _e( 'Amazon S3 Setup', 'wp-easycart' ); ?></span>
        <a href="<?php echo wp_easycart_admin( )->helpsystem->print_docs_url('settings', 'third-party', 'amazon');?>" target="_blank" class="ec_help_icon_link">
            <div class="dashicons-before ec_help_icon dashicons-info"></div> <?php _e( 'Help', 'wp-easycart' ); ?>
        </a>
        <?php echo wp_easycart_admin( )->helpsystem->print_vids_url('settings', 'third-party', 'amazon');?>
    </div>
    
    <div class="ec_admin_settings_input ec_admin_settings_live_payment_section">
   		<?php if( version_compare( phpversion( ), "5.3" ) < 0 ){ ?>
        <span><?php _e( 'YOU MUST HAVE PHP 5.3 OR HIGHER TO USE AMAZON S3!', 'wp-easycart' ); ?> <br /><?php _e( 'PLEASE UPGRADE BEFORE ATTEMPTING TO USE.', 'wp-easycart' ); ?></span>
        <?php }?>
        <div><?php _e( 'Amazon Key', 'wp-easycart' ); ?><input name="ec_option_amazon_key" id="ec_option_amazon_key" type="text" value="<?php echo get_option('ec_option_amazon_key'); ?>" /></div>
        <div><?php _e( 'Amazon Secret', 'wp-easycart' ); ?><input name="ec_option_amazon_secret" id="ec_option_amazon_secret" type="text" value="<?php echo get_option('ec_option_amazon_secret'); ?>" /></div>
        <div><?php _e( 'Amazon Download Bucket', 'wp-easycart' ); ?><input name="ec_option_amazon_bucket" id="ec_option_amazon_bucket" type="text" value="<?php echo get_option('ec_option_amazon_bucket'); ?>" /></div>
        <div><?php _e( 'Amazon Download Bucket Region', 'wp-easycart' ); ?><select name="ec_option_amazon_bucket_region" id="ec_option_amazon_bucket_region" style="width:100%;">
    		<option value=""<?php if( get_option('ec_option_amazon_bucket_region') == "" ){ echo " selected=\"selected\""; }?>>None Selected</option>
        	<option value="us-east-2"<?php 		if( get_option('ec_option_amazon_bucket_region') == "us-east-2" ){ echo " selected=\"selected\""; }?>>US East (Ohio)</option>
        	<option value="us-east-1"<?php 		if( get_option('ec_option_amazon_bucket_region') == "us-east-1" ){ echo " selected=\"selected\""; }?>>US East (N. Virginia)</option>
        	<option value="us-west-1"<?php 		if( get_option('ec_option_amazon_bucket_region') == "us-west-1" ){ echo " selected=\"selected\""; }?>>US West (N. California)</option>
        	<option value="us-west-2"<?php 		if( get_option('ec_option_amazon_bucket_region') == "us-west-2" ){ echo " selected=\"selected\""; }?>>US West (Oregon)</option>
        	<option value="ca-central-1"<?php 	if( get_option('ec_option_amazon_bucket_region') == "ca-central-1" ){ echo " selected=\"selected\""; }?>>Canada (Central)</option>
        	<option value="ap-south-1"<?php 	if( get_option('ec_option_amazon_bucket_region') == "ap-south-1" ){ echo " selected=\"selected\""; }?>>Asia Pacific (Mumbai)</option>
        	<option value="ap-northeast-2"<?php if( get_option('ec_option_amazon_bucket_region') == "ap-northeast-2" ){ echo " selected=\"selected\""; }?>>Asia Pacific (Seoul)</option>
        	<option value="ap-southeast-1"<?php if( get_option('ec_option_amazon_bucket_region') == "ap-southeast-1" ){ echo " selected=\"selected\""; }?>>Asia Pacific (Singapore)</option>
        	<option value="ap-southeast-2"<?php if( get_option('ec_option_amazon_bucket_region') == "ap-southeast-2" ){ echo " selected=\"selected\""; }?>>Asia Pacific (Sydney)</option>
        	<option value="ap-northeast-1"<?php if( get_option('ec_option_amazon_bucket_region') == "ap-northeast-1" ){ echo " selected=\"selected\""; }?>>Asia Pacific (Tokyo)</option>
        	<option value="eu-central-1"<?php 	if( get_option('ec_option_amazon_bucket_region') == "eu-central-1" ){ echo " selected=\"selected\""; }?>>EU (Frankfurt)</option>
        	<option value="eu-west-1"<?php 		if( get_option('ec_option_amazon_bucket_region') == "eu-west-1" ){ echo " selected=\"selected\""; }?>>EU (Ireland)</option>
        	<option value="eu-west-2"<?php 		if( get_option('ec_option_amazon_bucket_region') == "eu-west-2" ){ echo " selected=\"selected\""; }?>>EU (London)</option>
        	<option value="sa-east-1"<?php 		if( get_option('ec_option_amazon_bucket_region') == "sa-east-1" ){ echo " selected=\"selected\""; }?>>South America (Sao Paulo)</option>
        </select>
        </div><br />
       	
        <div class="ec_admin_settings_input">
            <input type="submit" class="ec_admin_settings_simple_button" onclick="return ec_admin_save_amazon_settings( );" value="<?php _e( 'Save Setup', 'wp-easycart' ); ?>" />
        </div>
    </div>
</div>