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


class ec_admin_taxes{		
	
	private $db;

	function __construct( ){
		
		global $wpdb;
		$this->db = $wpdb;

	}
	
	public function _getMethodRoles($methodName){
	   if ($methodName == 'gettaxes') return array('admin');
	   else if($methodName == 'savetax') return array('admin');
	   else if($methodName == 'deletetax') return array('admin');
	   else  return null;
	}

	function gettaxes() {
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS ec_taxrate.* FROM ec_taxrate";
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
	
	function savetax( $taxrates ){
		
		// Delete, then add to prevent errors
		if( $taxrates->taxcountryenable == 1 ){
			$success1 = $this->db->query( "DELETE FROM ec_taxrate WHERE ec_taxrate.tax_by_country = 1" );
			$sql = "INSERT into ec_taxrate( country_code, country_rate, tax_by_country ) VALUES( %s, %s, 1 )";
			$success2 = $this->db->query( $this->db->prepare( $sql, $taxrates->taxcountryid, $taxrates->taxcountryrate ) );
			
		}else if($taxrates->taxallenable == 1) {
			$success1 = $this->db->query( "DELETE FROM ec_taxrate WHERE ec_taxrate.tax_by_all = 1" );
			$sql = "INSERT into ec_taxrate( all_rate, tax_by_all ) VALUES( %s, 1 )";
			$success2 = $this->db->query( $this->db->prepare( $sql, $taxrates->taxallrate ) );
			
		}else if($taxrates->taxdutyenable == 1) {
			$success1 = $this->db->query( "DELETE FROM ec_taxrate WHERE ec_taxrate.tax_by_duty = 1" );
			$sql = "INSERT into ec_taxrate( duty_exempt_country_code, duty_rate, tax_by_duty ) VALUES( %s, %s, 1 )";
			$success2 = $this->db->query( $this->db->prepare( $sql, $taxrates->taxdutycountryid, $taxrates->taxdutyrate ) );
			
		}else if($taxrates->vattaxcountry == 1) {
			$success1 = $this->db->query( "DELETE FROM ec_taxrate WHERE ec_taxrate.tax_by_vat = 1 OR ec_taxrate.tax_by_single_vat = 1" );
			$sql = "INSERT into ec_taxrate( vat_country_code, vat_rate, tax_by_vat, vat_added, vat_included ) VALUES( %s, %s, 1, %s, %s )";
			$success2 = $this->db->query( $this->db->prepare( $sql, $taxrates->taxvatcountryid, $taxrates->taxvatrate, $taxrates->vatadded, $taxrates->vatincluded ) );
		
		}else if($taxrates->vattaxglobally == 1) {
			$success1 = $this->db->query( "DELETE FROM ec_taxrate WHERE ec_taxrate.tax_by_vat = 1 OR ec_taxrate.tax_by_single_vat = 1" );
			$sql = "INSERT into ec_taxrate( vat_country_code, vat_rate, tax_by_single_vat, vat_added, vat_included ) VALUES( %s, %s, 1, %s, %s )";
			$success2 = $this->db->query( $this->db->prepare( $sql, $taxrates->taxvatcountryid, $taxrates->taxvatrate, $taxrates->vatadded, $taxrates->vatincluded ) );
		
		}else if($taxrates->taxstateenable == 1 && $taxrates->taxstaterate != '') {
			$sql = "DELETE FROM ec_taxrate WHERE ec_taxrate.tax_by_state = 1 AND ec_taxrate.state_code = %s";
			$success1 = $this->db->query( $this->db->prepare( $sql, $taxrates->taxstateid ) );
			$sql = "INSERT into ec_taxrate( state_code, state_rate, tax_by_state ) VALUES( %s, %s, 1 )";
			$success2 = $this->db->query( $this->db->prepare( $sql, $taxrates->taxstateid, $taxrates->taxstaterate ) );
		
		}else{ // Should never hit here, but we don't want random non-existent errors?
			$success1 = $success2 = true;
		}
		
		if( $success1 === FALSE ){
			return array( "error" );
		}else if( $success2 === FALSE ){
			return array( "error" );
		}else{
			return array( "success" );
		}
		
	}	
	
	function deletetax($taxrates) {
		
		$success = true; // In case something happens where none of the if/else are hit.
		if( $taxrates->removetaxcountry == 1 )
			$success = $this->db->query( "DELETE FROM ec_taxrate WHERE ec_taxrate.tax_by_country = 1" );
		
		else if( $taxrates->removetaxall == 1 )
			$success = $this->db->query( "DELETE FROM ec_taxrate WHERE ec_taxrate.tax_by_all = 1" );
		  
		else if( $taxrates->removetaxduty == 1 )
			$success = $this->db->query( "DELETE FROM ec_taxrate WHERE ec_taxrate.tax_by_duty = 1" );
		  
		else if( $taxrates->removetaxvat == 1 )
			$success = $this->db->query( "DELETE FROM ec_taxrate WHERE ec_taxrate.tax_by_vat = 1 OR ec_taxrate.tax_by_single_vat = 1" );
			
		else if( $taxrates->removetaxstate == 1 )
			$success = $this->db->query( $this->db->prepare( "DELETE FROM ec_taxrate WHERE ec_taxrate.tax_by_state = 1 AND ec_taxrate.taxrate_id = %s", $taxrates->keyfield ) );
			
		else if( $taxrates->removetaxstate == 2 )
			$success = $this->db->query( "DELETE FROM ec_taxrate WHERE ec_taxrate.tax_by_state = 1" );
		
		if( $success === FALSE ){
			return array( "error" );
		}else{
			return array( "success" );
		}
		
	}		
	
}
?>