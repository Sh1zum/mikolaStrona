<?php
class wp_easycart_admin_subscriptions{
	public function load_subscriptions_list( ){
		if( ( isset( $_GET['subscription_id'] ) && isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'edit' ) || 
			( isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'add-new' ) ){
			do_action( 'wp_easycart_admin_subscriptions_details' );
		}else{
			do_action( 'wp_easycart_admin_subscriptions_list' );
		}
	}
}