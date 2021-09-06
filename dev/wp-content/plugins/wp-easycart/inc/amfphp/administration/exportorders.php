<?php 
/*
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//All Code and Design is copyrighted by Level Four Development, llc
//
//Level Four Development, LLC provides this code "as is" without warranty of any kind, either express or implied,     
//including but not limited to the implied warranties of merchantability and/or fitness for a particular purpose.         
//
//Only licensed users may use this code and storfront for live purposes. All other use is prohibited and may be 
//subject to copyright violation laws. If you have any questions regarding proper use of this code, please
//contact Level Four Development, llc and EasyCart prior to use.
//
//All use of this storefront is subject to our terms of agreement found on Level Four Development, llc's  website.
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
*/

//load our connection settings
ob_get_clean( );
ob_start( NULL, 4096 );

require_once( '../../../../../../wp-load.php' );

ob_end_clean( );

global $wpdb;

$requestID = "-1";
if( isset( $_GET['reqID'] ) )
	$requestID = $_GET['reqID'];
	
if( isset( $_GET['startdate'] ) ){
 	$startdate = $_GET['startdate'];
	$startdate = new DateTime( $startdate );
}

if( isset( $_GET['enddate'] ) ){
 	$enddate = $_GET['enddate'];
	$enddate = new DateTime( $enddate );
}

if( isset( $_GET['format'] ) )
	$format = $_GET['format'];

if( isset( $_GET['alldata'] ) )
	$alldata = $_GET['alldata'];

$user_sql = "SELECT  ec_user.*, ec_role.admin_access FROM ec_user LEFT JOIN ec_role ON (ec_user.user_level = ec_role.role_label) WHERE ec_user.password = %s AND  (ec_user.user_level = 'admin' OR ec_role.admin_access = 1)";
$users = $wpdb->get_results( $wpdb->prepare( $user_sql, $requestID ) );

if( !empty( $users ) ){
	
	if( $format == 'excel' ){

		$header = "";
		$data = "";
		
		if( $alldata == 'true' ){
			$sql = "SELECT 
						ec_order.order_date,
						ec_orderstatus.order_status,
						ec_orderdetail.orderdetail_id,
						ec_orderdetail.order_id,
						ec_order.payment_method,
						ec_order.sub_total,
						ec_order.tax_total,
						ec_order.shipping_total,
						ec_order.discount_total,
						ec_order.vat_total,
						ec_order.vat_rate,
						ec_order.duty_total,
						ec_order.gst_total,
						ec_order.gst_rate,
						ec_order.pst_total,
						ec_order.pst_rate,
						ec_order.hst_total,
						ec_order.hst_rate,
						ec_order.grand_total,
						ec_order.user_id,
						ec_order.use_expedited_shipping,
						ec_order.shipping_method,
						ec_order.shipping_carrier,
						ec_order.shipping_service_code,
						ec_order.tracking_number,
						ec_order.giftcard_id as gift_card_used,
						ec_order.promo_code as promo_code_used,
						ec_orderdetail.product_id,
						ec_orderdetail.title,
						ec_orderdetail.model_number,
						ec_orderdetail.unit_price,
						ec_orderdetail.total_price,
						ec_orderdetail.quantity,
						ec_orderdetail.optionitem_name_1,
						ec_orderdetail.optionitem_name_2,
						ec_orderdetail.optionitem_name_3,
						ec_orderdetail.optionitem_name_4,
						ec_orderdetail.optionitem_name_5,
						ec_order.order_notes,
						ec_order.order_customer_notes,
						ec_order.user_email,
						ec_order.user_level,
						ec_order.billing_first_name,
						ec_order.billing_last_name,
						ec_order.billing_company_name,
						ec_order.billing_address_line_1,
						ec_order.billing_address_line_2,
						ec_order.billing_city,
						ec_order.billing_state,
						ec_order.billing_zip,
						ec_order.billing_country,
						billing_country.name_cnt as billing_country_name, 
						ec_order.billing_phone,
						ec_order.shipping_first_name,
						ec_order.shipping_last_name,
						ec_order.shipping_company_name,
						ec_order.shipping_address_line_1,
						ec_order.shipping_address_line_2,
						ec_order.shipping_city,
						ec_order.shipping_state,
						ec_order.shipping_zip,
						ec_order.shipping_country,
						shipping_country.name_cnt as shipping_country_name,
						ec_order.shipping_phone,
						ec_order.vat_registration_number,
						ec_order.agreed_to_terms,
						ec_order.order_ip_address,
						ec_orderdetail.use_advanced_optionset,
						ec_orderdetail.giftcard_id,
						ec_orderdetail.shipper_id,
						ec_orderdetail.shipper_first_name,
						ec_orderdetail.shipper_last_name,
						ec_orderdetail.gift_card_message,
						ec_orderdetail.gift_card_from_name,
						ec_orderdetail.gift_card_to_name,
						ec_orderdetail.gift_card_email,
						ec_orderdetail.download_file_name,
						ec_orderdetail.download_key,
						ec_orderdetail.deconetwork_id,
						ec_orderdetail.deconetwork_name,
						ec_orderdetail.deconetwork_product_code,
						ec_orderdetail.deconetwork_options,
						ec_orderdetail.deconetwork_color_code,
						ec_orderdetail.deconetwork_product_id,
						ec_orderdetail.deconetwork_image_link,
						ec_orderdetail.subscription_signup_fee,
						ec_order.order_weight,
						ec_order.order_gateway,
						ec_order.card_holder_name,
						ec_order.creditcard_digits,
						ec_order.cc_exp_month,
						ec_order.cc_exp_year,
						ec_order.subscription_id,
						ec_order.stripe_charge_id,
						ec_order.nets_transaction_id,
						ec_order.gateway_transaction_id,
						ec_order.paypal_email_id,
						ec_order.paypal_transaction_id,
						ec_order.paypal_payer_id,
						ec_order.fraktjakt_order_id,
						ec_order.fraktjakt_shipment_id,
						ec_response.response_text as gateway_response
					FROM 
						ec_order 
						LEFT OUTER JOIN ec_orderdetail ON ec_order.order_id = ec_orderdetail.order_id
						LEFT JOIN ec_country as billing_country ON billing_country.iso2_cnt = ec_order.billing_country 
						LEFT JOIN ec_country as shipping_country ON shipping_country.iso2_cnt = ec_order.shipping_country 
						LEFT JOIN ec_orderstatus ON ec_orderstatus.status_id = ec_order.orderstatus_id
						LEFT JOIN ec_response ON ec_response.order_id = ec_order.order_id
					WHERE ec_orderstatus.is_approved = 1 OR ec_orderstatus.is_approved = 0 
					ORDER BY ec_order.order_id ASC";	
		
		}else{
			$sql = "SELECT 
						ec_order.order_date,
						ec_orderstatus.order_status,
						ec_orderdetail.orderdetail_id,
						ec_orderdetail.order_id,
						ec_order.payment_method,
						ec_order.sub_total,
						ec_order.tax_total,
						ec_order.shipping_total,
						ec_order.discount_total,
						ec_order.vat_total,
						ec_order.vat_rate,
						ec_order.duty_total,
						ec_order.gst_total,
						ec_order.gst_rate,
						ec_order.pst_total,
						ec_order.pst_rate,
						ec_order.hst_total,
						ec_order.hst_rate,
						ec_order.grand_total,
						ec_order.user_id,
						ec_order.use_expedited_shipping,
						ec_order.shipping_method,
						ec_order.shipping_carrier,
						ec_order.shipping_service_code,
						ec_order.tracking_number,
						ec_order.giftcard_id as gift_card_used,
						ec_order.promo_code as promo_code_used,
						ec_orderdetail.product_id,
						ec_orderdetail.title,
						ec_orderdetail.model_number,
						ec_orderdetail.unit_price,
						ec_orderdetail.total_price,
						ec_orderdetail.quantity,
						ec_orderdetail.optionitem_name_1,
						ec_orderdetail.optionitem_name_2,
						ec_orderdetail.optionitem_name_3,
						ec_orderdetail.optionitem_name_4,
						ec_orderdetail.optionitem_name_5,
						ec_order.order_notes,
						ec_order.order_customer_notes,
						ec_order.user_email,
						ec_order.user_level,
						ec_order.billing_first_name,
						ec_order.billing_last_name,
						ec_order.billing_company_name,
						ec_order.billing_address_line_1,
						ec_order.billing_address_line_2,
						ec_order.billing_city,
						ec_order.billing_state,
						ec_order.billing_zip,
						ec_order.billing_country,
						billing_country.name_cnt as billing_country_name, 
						ec_order.billing_phone,
						ec_order.shipping_first_name,
						ec_order.shipping_last_name,
						ec_order.shipping_company_name,
						ec_order.shipping_address_line_1,
						ec_order.shipping_address_line_2,
						ec_order.shipping_city,
						ec_order.shipping_state,
						ec_order.shipping_zip,
						ec_order.shipping_country,
						shipping_country.name_cnt as shipping_country_name,
						ec_order.shipping_phone,
						ec_order.vat_registration_number,
						ec_order.agreed_to_terms,
						ec_order.order_ip_address,
						ec_orderdetail.use_advanced_optionset,
						ec_orderdetail.giftcard_id,
						ec_orderdetail.shipper_id,
						ec_orderdetail.shipper_first_name,
						ec_orderdetail.shipper_last_name,
						ec_orderdetail.gift_card_message,
						ec_orderdetail.gift_card_from_name,
						ec_orderdetail.gift_card_to_name,
						ec_orderdetail.gift_card_email,
						ec_orderdetail.download_file_name,
						ec_orderdetail.download_key,
						ec_orderdetail.deconetwork_id,
						ec_orderdetail.deconetwork_name,
						ec_orderdetail.deconetwork_product_code,
						ec_orderdetail.deconetwork_options,
						ec_orderdetail.deconetwork_color_code,
						ec_orderdetail.deconetwork_product_id,
						ec_orderdetail.deconetwork_image_link,
						ec_orderdetail.subscription_signup_fee,
						ec_order.order_weight,
						ec_order.order_gateway,
						ec_order.card_holder_name,
						ec_order.creditcard_digits,
						ec_order.cc_exp_month,
						ec_order.cc_exp_year,
						ec_order.subscription_id,
						ec_order.stripe_charge_id,
						ec_order.nets_transaction_id,
						ec_order.gateway_transaction_id,
						ec_order.paypal_email_id,
						ec_order.paypal_transaction_id,
						ec_order.paypal_payer_id,
						ec_order.fraktjakt_order_id,
						ec_order.fraktjakt_shipment_id,
						ec_response.response_text as gateway_response
					FROM 
						ec_order 
						LEFT OUTER JOIN ec_orderdetail ON ec_order.order_id = ec_orderdetail.order_id
						LEFT JOIN ec_country as billing_country ON billing_country.iso2_cnt = ec_order.billing_country 
						LEFT JOIN ec_country as shipping_country ON shipping_country.iso2_cnt = ec_order.shipping_country 
						LEFT JOIN ec_orderstatus ON ec_orderstatus.status_id = ec_order.orderstatus_id
						LEFT JOIN ec_response ON ec_response.order_id = ec_order.order_id
					WHERE (
							ec_orderstatus.is_approved = 1  OR ec_orderstatus.is_approved = 0
						)  AND 
							ec_order.order_date >= '" . date_format( $startdate, 'Y-m-d' ) . " 00:00:00' AND 
							ec_order.order_date <= '" . date_format( $enddate, 'Y-m-d' ) . " 23:59:59' 
					ORDER BY ec_order.order_id ASC";
		
		}
		
		$results = $wpdb->get_results( $sql, ARRAY_A );
		$keys = array_keys( $results[0] );
		$dataset = array( );
		$single_use_key_names = apply_filters( 'wp_easycart_order_export_single_keys', 
								array( 	"sub_total", "tax_total", "tax_total", "shipping_total", "discount_total", "vat_total", 
										"vat_rate", "hst_total", "hst_rate", "pst_total", "pst_rate", "gst_total", "gst_rate", "grand_total",
										"order_date", "order_status", "payment_method", "shipping_method", "tracking_number", "promo_code_used",
										"order_customer_notes", "agreed_to_terms", "order_ip_address", "order_weight", 
										"order_gateway", "card_holder_name", "creditcard_digits", "cc_exp_month", "cc_exp_year", "stripe_charge_id", "order_notes", 
										"gateway_response" ) );
		
		
		$keys[] = "advanced_product_options";
		$keys = apply_filters( 'wp_easycart_order_export_keys', $keys );
		
		$prev_order = 0;
		$is_new_order = false;
		
		foreach( $results as $result ){
			
			if( $result['order_id'] != $prev_order ){
				$prev_order = $result['order_id'];
				$is_new_order = true;
			}
			
			if( $result['order_gateway'] == "authorize" ){
				$response_exploded = explode( ",", $result['gateway_response'] );
				if( count( $response_exploded ) > 3 ){
					 $result['gateway_response'] = $response_exploded[3];
				}
			}else if( $result['order_gateway'] == "paypal" ){
				preg_match_all( "/\[payment_status\] \=\> (.*)\n/", $result['gateway_response'], $output_array );
				if( count( $output_array ) > 1 ){
					 $result['gateway_response'] = $output_array[1][0];
				}
			}else if( $result['order_gateway'] == "stripe" ){
				preg_match_all( "/\[status\] \=\> (.*)\n/", $result['gateway_response'], $output_array );
				if( count( $output_array ) > 1 ){
					 $result['gateway_response'] = $output_array[1][0];
				}
			}
			
			$new_line = array( );
			
			foreach( $keys as $key ){
				
				if( $key == "advanced_product_options" ){
					$option_sql = "SELECT 
							ec_order_option.option_value 
						   FROM 
						   	ec_order_option 
						   WHERE 
						   	ec_order_option.orderdetail_id = %s 
						   ORDER BY 
							ec_order_option.order_option_id ASC";
					$option_results = $wpdb->get_results( $wpdb->prepare( $option_sql, $result['orderdetail_id'] ) );
					
					$optionlist = '';
					$first = true;
					foreach( $option_results as $option_row ){
						if( !$first )
							$optionlist .= ', ';
						$optionlist .= $option_row->option_value;
						$first = false;
					}
					$new_line[] = $optionlist;
					
				}else{
				
					$value = $result[$key];
		
					if( in_array( $key, $single_use_key_names ) && !$is_new_order ){
						$new_line[] = "0.00";
						
					}else if( !isset( $value ) || $value == "" ){
						$new_line[] = "";
		
					}else if( $key == 'billing_zip' || $key == 'shipping_zip' ){
						$new_line[] = "=\"" . $value . "\"";
						
					}else{
						$new_line[] = $value;
						
					}
					
				}
				
			}
			
			
			$dataset[] = $new_line;
			
			$is_new_order = false;
	
		}
		
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=order-export-' . date( 'Y-m-d' ). '.csv' );
		$output = fopen('php://output', 'w');
		fputcsv($output, $keys);
		foreach( $dataset as $result ){
			fputcsv($output, $result);
		}
		die( ); 
	
	}else if( $format == 'pdf' ){
		
		require_once('fpdf/fpdf.php');

		class PDF extends FPDF{
			
			function LoadData($file){
				
				$lines = file($file);
				$data = array();
				foreach($lines as $line)
					$data[] = explode(';',trim($line));
				return $data;
			
			}

			
			function FancyTable( $header, $startdate, $enddate, $alldata ){
				
				global $wpdb;
				
				$this->SetFillColor(23, 183, 15);
				$this->SetTextColor(255);
				$this->SetDrawColor(128,128, 128);
				$this->SetLineWidth(.3);
				
				$this->SetFont('Arial','B',10);
				$w = array(20, 30, 45, 40, 40, 20, 35, 35);
				for($i=0;$i<count($header);$i++)
					$this->Cell($w[$i],7,$header[$i],1,0,'C',true);
				$this->Ln();
				
				$this->SetFillColor(224,235,255);
				$this->SetTextColor(0);
				$this->SetFont('Arial','',8);
				
				$fill = false;
				
				$data = "";
				
				if( $alldata == 'true' ){
					$sql = "SELECT  
								ec_order.order_id, 
								ec_order.order_date, 
								
								ec_order.billing_first_name, 
								ec_order.billing_last_name, 
								ec_orderdetail.model_number, 
								ec_orderdetail.quantity, 
								ec_orderdetail.unit_price, 
								ec_orderdetail.total_price, 
								ec_orderdetail.optionitem_name_1, 
								ec_orderdetail.optionitem_name_2, 
								ec_orderdetail.optionitem_name_3, 
								ec_orderdetail.optionitem_name_4,
								ec_orderdetail.optionitem_name_5, 
								ec_orderdetail.use_advanced_optionset,
								ec_orderdetail.orderdetail_id,
								ec_orderstatus.*,
								ec_order.orderstatus_id
							FROM ec_order LEFT OUTER JOIN ec_orderdetail ON ( ec_order.order_id = ec_orderdetail.order_id ) 
							     LEFT JOIN ec_orderstatus ON ec_orderstatus.status_id = ec_order.orderstatus_id
							WHERE ec_orderstatus.is_approved = 1
							ORDER BY ec_order.order_id ASC";
							
				}else{
					$sql = "SELECT  
								ec_order.order_id, 
								ec_order.order_date, 
								ec_order.billing_first_name, 
								ec_order.billing_last_name, 
								ec_orderdetail.model_number, 
								ec_orderdetail.quantity, 
								ec_orderdetail.unit_price, 
								ec_orderdetail.total_price, 
								ec_orderdetail.optionitem_name_1, 
								ec_orderdetail.optionitem_name_2, 
								ec_orderdetail.optionitem_name_3, 
								ec_orderdetail.optionitem_name_4,
								ec_orderdetail.optionitem_name_5, 
								ec_orderdetail.use_advanced_optionset,
								ec_orderdetail.orderdetail_id,
								ec_order.orderstatus_id
							FROM ec_order LEFT OUTER JOIN ec_orderdetail ON (ec_order.order_id = ec_orderdetail.order_id)
							      LEFT JOIN ec_orderstatus ON ec_orderstatus.status_id = ec_order.orderstatus_id
							WHERE ec_orderstatus.is_approved = 1 AND
							      ec_order.order_date >= '".date_format($startdate, 'Y-m-d')." 00:00:00' AND 
								  ec_order.order_date <= '".date_format($enddate, 'Y-m-d')." 23:59:59' 
							ORDER BY ec_order.order_id asc";
				}
				$results = $wpdb->get_results( $sql, ARRAY_N );
				
				foreach( $results as $row ){
					$neworder = true;
					$currentorderid = $row[0];
					
					$optionlist = '';
					foreach($row as $line)
					{
						//get basic options, or advanced options
						if ($row[13] == '0') {
							$optionlist = $row[8];
							if($row[9]) $optionlist .= ', '. $row[9];
							if($row[10]) $optionlist .= ', '. $row[10];
							if($row[11]) $optionlist .= ', '. $row[11];
							if($row[12]) $optionlist .= ', '. $row[12];
						} else {
							$option_sql = "SELECT 
											ec_order_option.option_value 
										   FROM 
											ec_order_option 
										   WHERE 
											ec_order_option.orderdetail_id = %s 
										   ORDER BY 
											ec_order_option.order_option_id ASC";
							$option_results = $wpdb->get_results( $wpdb->prepare( $option_sql, $row[14] ) );
							$optionlist = '';
							$first = true;
							foreach( $option_results as $option_row ){
								if( !$first )
									$optionlist .= ', ';
								$optionlist .= $option_row->option_value;
								$first = false;
							}
						}

					}
					
					$orderdate = new DateTime($row[1]);
					//now fill the row cells
					$this->Cell($w[0],6,$row[0],'LR',0,'C',$fill);
					$this->Cell($w[1],6,date_format($orderdate, 'F j, Y'),'LR',0,'C',$fill);
					$this->Cell($w[2],6,$row[4],'LR',0,'C',$fill);
					$this->Cell($w[3],6,substr($optionlist, 0, 25),'LR',0,'C',$fill);
					$this->Cell($w[4],6,$row[3] . ', ' . $row[2],'LR',0,'C',$fill);
					$this->Cell($w[5],6,number_format($row[5], 2),'LR',0,'C',$fill);
					$this->Cell($w[6],6,number_format($row[6], 2),'LR',0,'C',$fill);
					$this->Cell($w[7],6,number_format($row[7], 2),'LR',0,'C',$fill);
					$this->Ln();
					$fill = !$fill;
					
				}

				// Closing line
				$this->Cell(array_sum($w),0,'','T');
			}
		}
		
		//create 2 variables for use later on
		$header = array('Order ID', 'Order Date', 'Product SKU/Model', 'Product Options', 'Customer Name', 'Quantity', 'Individual Price', 'Total Price');
		
		$pdf = new PDF('L');

		$pdf->AddPage();
		//report data
		$pdf->SetFont('Arial','',14);
		
		//get grand total for report
		if($alldata == 'true') {
			$sql = "SELECT SUM(ec_order.grand_total) as grand_total, ec_order.orderstatus_id FROM ec_order LEFT JOIN ec_orderstatus ON ec_orderstatus.status_id = ec_order.orderstatus_id WHERE ec_orderstatus.is_approved = 1  ";
			$reporttitle = 'Sales Report for All Orders';
			$pdf->Cell(60,10,$reporttitle,0,1,'C');
		} else {
			$sql = "SELECT SUM(ec_order.grand_total) as grand_total, ec_order.orderstatus_id FROM ec_order LEFT JOIN ec_orderstatus ON ec_orderstatus.status_id = ec_order.orderstatus_id WHERE ec_orderstatus.is_approved = 1 AND ec_order.order_date >= '".date_format($startdate, 'Y-m-d')."' AND ec_order.order_date <= '".date_format($enddate, 'Y-m-d')."'";
			$reporttitle = 'Sales Report for ' . date_format($startdate, 'F j, Y') . ' to ' . date_format($enddate, 'F j, Y');
			$pdf->Cell(130,10,$reporttitle,0,1,'C');
		}
		$results = $wpdb->get_results( $sql );
		foreach( $results as $row ){
			$grandtotal = number_format($row->grand_total, 2);
		}
		
		$reporttotals = 'Grand Total for the Report Period: '. $grandtotal;
		
		$pdf->FancyTable($header, $startdate, $enddate, $alldata);
		
		//report totals
		$pdf->SetFont('Arial','',14);
		$pdf->Ln();
		$pdf->Ln();
		$pdf->Cell(0,20,$reporttotals,0,1,'L');
		
		//output report
		ob_end_flush( );
		$pdf->Output();

	}

}else{
	echo "Not Authorized...";
}
?>