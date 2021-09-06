<?php
/*
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//All Code and Design is copyrighted by Level Four Development, llc
//
//Level Four Development, LLC provides this code "as is" without warranty of any kind, either express or implied,     
//including but not limited to the implied warranties of merchantability and/or fitness for a particular purpose.         
//
//Only licnesed users may use this code and storfront for live purposes. All other use is prohibited and may be 
//subject to copyright violation laws. If you have any questions regarding proper use of this code, please
//contact Level Four Development, llc and EasyCart prior to use.
//
//All use of this storefront is subject to our terms of agreement found on Level Four Development, llc's  website.
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
*/


class ec_admin_subscriptions{		
	
	private $db;
	
	function __construct( ){
		
		global $wpdb;
		$this->db = $wpdb;

	}	
	
	public function _getMethodRoles( $methodName ){
		if($methodName == 'cancelstripesubscription') return array('admin');
		else if($methodName == 'updatestripesubscription') return array('admin');
		else if($methodName == 'getsubscriptions') return array('admin');
		else if($methodName == 'getsubscriptionplans') return array('admin');
		else if($methodName == 'getcustomerpayments') return array('admin');
		else if($methodName == 'getsubscriptionplanrecords') return array('admin');
		else if($methodName == 'deletesubscriptionplanrecord') return array('admin');
		else if($methodName == 'updatesubscriptionplanrecord') return array('admin');
		else if($methodName == 'addsubscriptionplanrecord') return array('admin');
		else  return null;
	}
	
	function getcustomerpayments($subscription_id, $user_id) {
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS ec_order.*, UNIX_TIMESTAMP(ec_order.order_date) AS order_date FROM ec_order WHERE ec_order.user_id = %d AND ec_order.subscription_id = %d";
		$results = $this->db->get_results( $this->db->prepare( $sql, $user_id, $subscription_id ) );
		$totalrows = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
		if( $totalrows > 0 ){
			foreach( $results as $row ){
				$row->totalrows = $totalrows;
				$returnArray[] = $row;
			}
			return $returnArray;
		
		}else{
			return array( "noresults" );
		}
	
	}

	
	function getsubscriptions( $startrecord, $limit, $orderby, $ordertype, $filter ){
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS ec_subscription.*, ec_user.stripe_customer_id, ec_user.default_card_type, ec_user.default_card_last4, UNIX_TIMESTAMP(ec_subscription.start_date) AS start_date FROM ec_subscription LEFT JOIN ec_user ON ec_subscription.user_id = ec_user.user_id WHERE ec_subscription.subscription_id != '' ".$filter." ORDER BY ".  $orderby ." ".  $ordertype . " LIMIT ".  $startrecord .", ".  $limit;
		$results = $this->db->get_results( $sql );
		$totalrows = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
		if( $totalrows > 0 ){
			foreach( $results as $row ){
				$row->totalrows = $totalrows;
				$returnArray[] = $row;
			}
			return $returnArray;
		
		}else{
			return array( "noresults" );
		}
		
	}		
	
	function cancelstripesubscription( $user, $subscription_id ){
		
		$stripe_user = (object)array( "stripe_customer_id" => $user);
		
		if( get_option( 'ec_option_payment_process_method' ) == 'stripe' )
			$stripe = new ec_stripe;
		else
			$stripe = new ec_stripe_connect;
			
		$response = $stripe->cancel_subscription( $stripe_user, $subscription_id);
		
		if( $response ){
			
			$sql = "UPDATE ec_subscription SET ec_subscription.subscription_status = 'Canceled' WHERE ec_subscription.stripe_subscription_id = %s";
			$success = $this->db->query( $this->db->prepare( $sql, $subscription_id ) );
			
			if( $success === FALSE ){
				return array( "failed" );
			}else{
				return array( "success" );
			}
		  }else{
			  return array( "failed" );
		  }
		  
	}
	
	function updatestripesubscription( $user, $subscription_id, $product_id){
		
		$sql = "SELECT ec_product.title, ec_product.model_number, ec_product.price, ec_product.subscription_bill_length, ec_product.subscription_bill_period, ec_product.subscription_prorate, ec_product.subscription_unique_id FROM ec_product WHERE ec_product.product_id = %d";
		$plan_product = $this->db->get_row( $this->db->prepare( $sql, $product_id ) );
		
		$stripe_user = (object)array( "stripe_customer_id" => $user );
		$stripe_product = (object)array( "product_id" => $product_id, "subscription_unique_id" => $plan_product->subscription_unique_id );
		
		if( get_option( 'ec_option_payment_process_method' ) == 'stripe' )
			$stripe = new ec_stripe( );
		else
			$stripe = new ec_stripe_connect( );
		$response = $stripe->update_subscription( $stripe_product, $stripe_user, NULL, $subscription_id, NULL, $plan_product->subscription_prorate );
		
		if( $response ){
			
			$sql = "UPDATE ec_subscription SET ec_subscription.title = %s, ec_subscription.product_id = %d, ec_subscription.model_number = %s, ec_subscription.price = %s,  ec_subscription.payment_length = %s, ec_subscription.payment_period = %s WHERE ec_subscription.stripe_subscription_id = %s";
			$success = $this->db->query( $this->db->prepare( $sql, $plan_product->title, $product_id, $plan_product->model_number, $plan_product->price, $plan_product->subscription_bill_length, $plan_product->subscription_bill_period, $subscription_id ) );
			
			$sql = "SELECT ec_subscription.*, ec_user.stripe_customer_id, ec_user.default_card_type, ec_user.default_card_last4, UNIX_TIMESTAMP(ec_subscription.start_date) AS start_date FROM ec_subscription LEFT JOIN ec_user ON ec_subscription.email = ec_user.email WHERE ec_subscription.stripe_subscription_id = %s";
			$row = $this->db->get_row( $this->db->prepare( $sql, $subscription_id ) );
			
			if( $row ){
				return array( $row );
			}
		}
		
		return array( "failed" );
		
	}
	
	function getsubscriptionplans( ){
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS ec_subscription_plan.* FROM ec_subscription_plan";
		$results = $this->db->get_results( $sql );
		$totalrows = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
		if( $totalrows > 0 ){
			foreach( $results as $row ){
				$row->totalrows = $totalrows;
				$returnArray[] = $row;
			}
			return $returnArray;
		
		}else{
			return array( "noresults" );
		}
		
	}
	
	function getcommonplans( $productid ){
		
		$sql = "SELECT ec_product.subscription_plan_id FROM ec_product WHERE ec_product.product_id = %d";
		$plan_id = $this->db->get_var( $this->db->prepare( $sql, $productid ) );
		
		$sql = "SELECT ec_product.product_id, ec_product.title FROM ec_product WHERE ec_product.is_subscription_item = '1' AND ec_product.subscription_plan_id = %s";
		$results = $this->db->get_results( $this->db->prepare( $sql, $plan_id ) );
		
		if( count( $results ) > 0 ){
			 foreach( $results as $row ){
				$returnArray[] = $row;
			}
			return $returnArray;
		}else{
			return array( "noresults" );
		}
		
	}
	
	function getsubscriptionplanrecords( $startrecord, $limit, $orderby, $ordertype, $filter ){
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS ec_subscription_plan.* FROM ec_subscription_plan  WHERE ec_subscription_plan.subscription_plan_id != '' ".$filter." ORDER BY ".  $orderby ." ".  $ordertype . " LIMIT ".  $startrecord .", ".  $limit."";
		$results = $this->db->get_results( $sql );
		$totalrows = $this->db->get_var( "SELECT FOUND_ROWS( )" );
		
		if( $totalrows > 0 ){
			foreach( $results as $row ){
				$row->totalrows = $totalrows;
				$returnArray[] = $row;
			}
			return $returnArray;
		
		}else{
			return array( "noresults" );
		}
		
	}
	
	
	function deletesubscriptionplanrecord( $subscription_plan_id ){
		
		$sql = "DELETE FROM ec_subscription_plan WHERE ec_subscription_plan.subscription_plan_id = %d";
		$success = $this->db->query( $this->db->prepare( $sql, $subscription_plan_id ) );
		
		if( $success === FALSE ){
			return array( "error" );
		}else{
			return array( "success" );
		}
		
	}
	function updatesubscriptionplanrecord( $subscription_plan_id, $subscription ){
		  
		$sql = "UPDATE ec_subscription_plan SET ec_subscription_plan.plan_title = %s, ec_subscription_plan.can_downgrade = %s WHERE ec_subscription_plan.subscription_plan_id = %d";
		$this->db->query( $this->db->prepare( $sql, $subscription->subscriptionplantitle, $subscription->candowngrade, $subscription_plan_id ) );
		
		return array( "success" );
		
	}
	function addsubscriptionplanrecord( $subscription ){
		
		$sql = "INSERT INTO ec_subscription_plan( plan_title, can_downgrade ) VALUES( %s, %s )";
		$success = $this->db->query( $this->db->prepare( $sql, $subscription->subscriptionplantitle, $subscription->candowngrade ) );
		
		if( $success === FALSE ){
			return array( "error" );
		}else{
			return array( "success" );
		}
		
	}
	
}
?>