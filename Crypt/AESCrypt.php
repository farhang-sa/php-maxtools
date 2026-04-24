<?php namespace MaxCrypt ;

class AESCrypt {

	private static $default_crypto_iv = null ;

	public static function setDefaultIV( $iv = '!1275K036#8@A9+4' ){
        $letters = '1234567890-!@#$%^&*()ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' ;
	    while( strlen($iv) < 16 )
	        $iv .= substr($letters , rand(0,strlen($letters)-1) , 1);
        self::$default_crypto_iv = $iv ;
	}

   	public static function SSL_Encrypt( $DataToEnCrypt , $Key , $algo = 'aes-256-cbc' , $iv = null ){

	   	if ( $DataToEnCrypt == null || $Key == null || ! is_string( $Key ) )
	   	    return null ;

	  	if ( strlen( $Key ) != 32 )
	  	    return null ;

        if( $algo === null )
            $algo = 'aes-256-cbc' ;

       	try {

       		$iv = $iv ? $iv : self::$default_crypto_iv ;

       		return openssl_encrypt( $DataToEnCrypt , $algo , $Key , 0 , $iv ) ;

       	} catch (Exception $e) {} return null ;
   }

   	public static function SSL_Decrypt( $DataToDeCrypt , $Key , $algo = 'aes-256-cbc' , $iv = null ){

	   	if ( $DataToDeCrypt == null || $Key == null || ! is_string( $Key ) )
	   	    return null ;

	  	if ( strlen( $Key ) != 32 )
	  	    return null ;

        if( $algo === null )
            $algo = 'aes-256-cbc' ;

       	try {

       		$iv = $iv ? $iv : self::$default_crypto_iv ;

       		return openssl_decrypt( $DataToDeCrypt , $algo , $Key , 0 , $iv ) ;

       	} catch (Exception $e) {} return null ;

    }

    // deprecated
   	public static function AES_ECB_PKCS5_Encrypt( $DataToEnCrypt , $Key ) {

	   	if ( $DataToEnCrypt == null || $Key == null || ! is_string( $Key ) ) return null ;

	  	if ( strlen( $Key ) != 32 ) return null ;

       	try {

       		if( function_exists( 'mcrypt_encrypt' ) )
       			return mcrypt_encrypt( MCRYPT_RIJNDAEL_128 , $Key , $DataToEnCrypt , MCRYPT_MODE_ECB ) ;

       		if( function_exists( 'openssl_encrypt' ) )
       			return self::SSL_Encrypt( $DataToEnCrypt , $Key ) ;

       	} catch (Exception $e) {} return null ;
   }

    // deprecated
   	public static function AES_ECB_PKCS5_Decrypt( $DataToDeCrypt , $Key ){

	   	if ( $DataToDeCrypt == null || $Key == null || ! is_string( $Key ) ) return null ;

	  	if ( strlen( $Key ) != 32 ) return null ;

       	try {

       		if( function_exists( 'mcrypt_decrypt' ) )
       			return mcrypt_decrypt( MCRYPT_RIJNDAEL_128 , $Key , $DataToDeCrypt , MCRYPT_MODE_ECB ) ;

       		if( function_exists( 'openssl_decrypt' ) )
       			return self::SSL_Decrypt( $DataToDeCrypt , $Key ) ;

       	} catch (Exception $e) {} return null ;

   }

} ?>