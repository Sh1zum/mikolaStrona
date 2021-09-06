<form action="admin.php?page=wp-easycart-settings&subpage=initial-setup" method="POST" name="wpeasycart_admin_form" id="wpeasycart_admin_form" novalidate="novalidate">
    
    <input type="hidden" name="ec_admin_form_action" value="save-initial-setup" />
    
	<?php do_action( 'wpeasycart_admin_initial_setup_success' ); ?>
    
    <div class="ec_admin_settings_panel">
        
        <script>
			var wp_easycart_help_player;
			var tag = document.createElement( 'script' );
			tag.src = "https://www.youtube.com/iframe_api";
			var firstScriptTag = document.getElementsByTagName( 'script' )[0];
			firstScriptTag.parentNode.insertBefore( tag, firstScriptTag );
			
			function onYouTubeIframeAPIReady( ){
				wp_easycart_help_player = new YT.Player( 'wp_easycart_admin_help_video_player', {
					width: '100%',
					height: '450',
					videoId: '1GlbtKmWO7c'
				});
			}
			
			jQuery( '.ec_admin_help_video_container > .ec_admin_upsell_popup_close > a' ).on( 'click', function( ){
				wp_easycart_help_player.pauseVideo( ); wp_easycart_admin_close_video_help( ); return false;
			} );
		</script>
        
        <div class="ec_admin_help_video_box">
            <h1>
                <div class="dashicons-before dashicons-video-alt3"></div>
                <span><?php _e( 'Watch the WP EasyCart Installation Video', 'wp-easycart' ); ?></span>
                <a href="https://www.youtube.com/watch?v=Z1bW6RsZXiA" onclick="wp_easycart_admin_open_video_help( ); wp_easycart_help_player.playVideo( ); return false;">
                    <div class="dashicons-before dashicons-controls-play"></div>
                    <?php _e( 'Play Now', 'wp-easycart' ); ?>
                </a>
            </h1>
            
            <p style="padding:0 3px 0;"><?php echo sprintf( __( 'Looking for the setup wizard? All setup features, and more, are available in the settings section of the cart! If you want to return to the wizard, %sclick here%s.', 'wp-easycart' ), '<a href="admin.php?page=wp-easycart-settings&subpage=setup-wizard&step=1">', '</a>' ); ?></p>
            
        </div>
    	
        <div class="ec_admin_important_numbered_list">
                
            <?php do_action( 'wpeasycart_admin_intial_setup' ); ?>
            
        </div>
    
    </div>
</form>