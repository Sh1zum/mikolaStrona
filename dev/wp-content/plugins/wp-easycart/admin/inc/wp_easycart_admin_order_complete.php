<?php
class wp_easycart_admin_order_complete{
	
	private $wpdb;
	
	public $order_file;
	public $order_receipt_file;
	public $order_success_file;
		
	public function __construct( ){
		// Keep reference to wpdb
		global $wpdb;
		$this->wpdb =& $wpdb;
		
		// Setup File Names 
		$this->order_file	 				= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/order/order.php';
		$this->order_receipt_file		 	= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/order/order-receipt.php';
		$this->order_success_file		 	= WP_PLUGIN_DIR . "/" . EC_PLUGIN_DIRECTORY . '/admin/template/settings/order/order-success.php';
		
		// Actions
		add_action( 'wpeasycart_admin_order_complete', array( $this, 'load_success_messages' ) );
		add_action( 'wpeasycart_admin_order_complete', array( $this, 'load_order_receipt_settings' ) );
		add_action( 'wpeasycart_admin_order_complete', array( $this, 'load_order_success_settings' ) );
	}
	
	public function load_order_complete( ){
		include( $this->order_file );
	}
	
	public function load_success_messages( ){
		include( $this->success_messages_file );
	}
	
	public function load_order_receipt_settings( ){
		include( $this->order_receipt_file );
	}
	
	public function load_order_success_settings( ){
		include( $this->order_success_file );
	}
	
}