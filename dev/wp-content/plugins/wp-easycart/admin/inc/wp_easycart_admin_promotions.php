<?php
class wp_easycart_admin_promotions{
	public function load_promotions_list( ){
		if( ( isset( $_GET['promotion_id'] ) && isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'edit' ) || 
			( isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'add-new' ) ){
			do_action( 'wp_easycart_admin_promotion_details' );
		}else{
			do_action( 'wp_easycart_admin_promotion_list' );
		}
	}
}
