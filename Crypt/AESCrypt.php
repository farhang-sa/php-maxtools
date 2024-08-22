<?php namespace MaxCrypt ;

class AESCrypt {

   	public static function SSL_Encrypt( $DataToEnCrypt , $Key , $algorithm = 'aes-128-cbc' ) {
	   	
	   	if ( $DataToEnCrypt == null || $Key == null || ! is_string( $Key ) ) return null ;
	  
	  	if ( strlen( $Key ) != 32 ) return null ;
       
       	try { 

       		return openssl_encrypt( $DataToEnCrypt , $algorithm , $Key ) ; 

       	} catch (Exception $e) {} return null ;
   }

   	public static function SSL_Decrypt( $DataToDeCrypt , $Key , $algorithm = 'aes-128-cbc' ){

	   	if ( $DataToDeCrypt == null || $Key == null || ! is_string( $Key ) ) return null ;

	  	if ( strlen( $Key ) != 32 ) return null ;
       
       	try { 

       		return openssl_decrypt( $DataToDeCrypt , $algorithm , $Key ) ; 

       	} catch (Exception $e) {} return null ;

   }


   	public static function AES_ECB_PKCS5_Encrypt( $DataToEnCrypt , $Key ) {
	   	
	   	if ( $DataToEnCrypt == null || $Key == null || ! is_string( $Key ) ) return null ;
	  
	  	if ( strlen( $Key ) != 32 ) return null ;
       
       	try { 

       		if( function_exists( 'mcrypt_encrypt' ) )

       			return mcrypt_encrypt( MCRYPT_RIJNDAEL_128 , $Key , $DataToEnCrypt , MCRYPT_MODE_ECB ) ; 

       		if( function_exists( 'openssl_encrypt' ) )

       			return openssl_encrypt( $DataToEnCrypt , 'aes-128-ecb' , $Key ) ; 

       	} catch (Exception $e) {} return null ;
   }

   	public static function AES_ECB_PKCS5_Decrypt( $DataToDeCrypt , $Key ){

	   	if ( $DataToDeCrypt == null || $Key == null || ! is_string( $Key ) ) return null ;

	  	if ( strlen( $Key ) != 32 ) return null ;
       
       	try { 

       		if( function_exists( 'mcrypt_decrypt' ) )

       			return mcrypt_decrypt( MCRYPT_RIJNDAEL_128 , $Key , $DataToDeCrypt , MCRYPT_MODE_ECB ) ; 

       		if( function_exists( 'openssl_decrypt' ) )

       			return openssl_decrypt( $DataToDeCrypt , 'aes-128-ecb' , $Key ) ; 

       	} catch (Exception $e) {} return null ;

   }

} ?>