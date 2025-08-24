<?php namespace MaxTools ;

define( 'MaxTools_Root' , realpath( __DIR__ ) );
define( 'MaxTools_DS' , DIRECTORY_SEPARATOR );

if( ! class_exists( 'MaxCrypt\AESCrypt' ) )
	include_once MaxTools_Root . MaxTools_DS . 'Crypt' . MaxTools_DS . 'AESCrypt.php' ;

if( ! class_exists( 'MaxDatabaseManager\Server' ) )
	include_once MaxTools_Root . MaxTools_DS . 'DBManager' . MaxTools_DS . 'Server.php' ;

if( ! interface_exists( 'MaxPayment\Payment' ) )
	include_once MaxTools_Root . MaxTools_DS . 'Payment' . MaxTools_DS . 'Payment.php' ;

if( ! class_exists( 'MaxSMS\SMS' ) )
	include_once MaxTools_Root . MaxTools_DS . 'SMS' . MaxTools_DS . 'SMS.php' ;

if( ! class_exists( 'MaxTools\Secrets' ) )
	include_once MaxTools_Root . MaxTools_DS . 'Utilities' . MaxTools_DS . 'Utilities.php' ;

?>