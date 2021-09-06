<?php
global $wpdb;

$subscriber_id_array = array( );
if( isset( $_GET['ec_admin_form_action'] ) && $_GET['ec_admin_form_action'] == 'export-subscribers-csv' && isset( $_GET['bulk'] ) ){
	if( is_array( $_GET['bulk'] ) ){
		foreach( $_GET['bulk'] as $user_id ){
			$subscriber_id_array[] = $wpdb->prepare( '%d', $user_id );
		}
	}else{
		$subscriber_id_array[] = $wpdb->prepare( '%d', $_GET['bulk'] );
	}
}

$header = "";
$data = "";

$sql = "SELECT 
		*
	FROM
	  ec_subscriber";
	  
if( count( $subscriber_id_array ) > 0 ){
	$subscriber_id_sql = implode( ',', $subscriber_id_array );
	$sql .= "
	WHERE ec_subscriber.subscriber_id IN (" . $subscriber_id_sql . ")";
}

$results = $wpdb->get_results( $sql, ARRAY_A );
if( $results ){
	$keys = array_keys( $results[0] );
}else{
	$keys = array( );
}
header( 'Content-Type: text/csv; charset=utf-8' );
header( 'Content-Disposition: attachment; filename=subscribers-export-' . date( 'Y-m-d' ). '.csv' );
$output = fopen( 'php://output', 'w' );
fputcsv($output, $keys);
foreach( $results as $result ){
	fputcsv( $output, $result );
}
die( );
?>