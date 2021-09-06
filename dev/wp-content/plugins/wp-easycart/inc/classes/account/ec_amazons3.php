<?php

class ec_amazons3{

	public function download_file( $file_name ){
		
		if( get_option( 'ec_option_amazon_bucket_region' ) ){
		$client = Aws\S3\S3Client::factory(array(
			'signature' => 'v4',
			'region'  => utf8_encode( stripslashes( get_option( 'ec_option_amazon_bucket_region' ) ) ),
    		'credentials' => array(
				'key' 		=> utf8_encode( stripslashes( get_option( 'ec_option_amazon_key' ) ) ),
				'secret' 	=> utf8_encode( stripslashes( get_option( 'ec_option_amazon_secret' ) ) )
			)
		));
		}else{
		$client = Aws\S3\S3Client::factory(array(
			'signature' => 'v4',
			'region'  => '',
    		'credentials' => array(
				'key' 		=> utf8_encode( stripslashes( get_option( 'ec_option_amazon_key' ) ) ),
				'secret' 	=> utf8_encode( stripslashes( get_option( 'ec_option_amazon_secret' ) ) )
			)
		));	
		}
		
		$command = $client->getCommand('GetObject', array(
			'Bucket' => stripslashes( get_option( 'ec_option_amazon_bucket' ) ),
			'Key' => $file_name,
			'ResponseContentDisposition' => 'attachment; filename="' . $file_name . '"'
		));
		
		$signedUrl = $command->createPresignedUrl('+100 seconds');
		
		header( "location:" . $signedUrl );
		
		die( );
		
	}
	
	public function get_aws_files( ){
		
		$returnArray = array( );
		
		if( get_option( 'ec_option_amazon_bucket_region' ) ){
		$client = Aws\S3\S3Client::factory(array(
			'signature' => 'v4',
			'region'  => utf8_encode( stripslashes( get_option( 'ec_option_amazon_bucket_region' ) ) ),
    		'credentials' => array(
				'key' 		=> utf8_encode( stripslashes( get_option( 'ec_option_amazon_key' ) ) ),
				'secret' 	=> utf8_encode( stripslashes( get_option( 'ec_option_amazon_secret' ) ) )
			)
		));
		}else{
		$client = Aws\S3\S3Client::factory(array(
			'signature' => 'v4',
			'region'  => '',
    		'credentials' => array(
				'key' 		=> utf8_encode( stripslashes( get_option( 'ec_option_amazon_key' ) ) ),
				'secret' 	=> utf8_encode( stripslashes( get_option( 'ec_option_amazon_secret' ) ) )
			)
		));	
		}
		
		$result = $client->listObjects( array( 'Bucket' => stripslashes( get_option( 'ec_option_amazon_bucket' ) ) ) );

		foreach( $result['Contents'] as $object ){
			if( substr( $object['Key'], 0, 5 ) != "logs/" ){
				$returnArray[] = $object['Key'];
			}
		}
		
		return $returnArray;
		
	}
		
}

?>