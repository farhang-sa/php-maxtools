<?php namespace MaxTools ;

if( ! class_exists( 'MaxCrypt\AESCrypt' ) )
	include_once 'Crypt/AESCrypt.php' ;

if( ! class_exists( 'MaxDatabaseManager\Server' ) )
	include_once 'DbManager/Server.php' ;

if( ! interface_exists( 'MaxPayment\Payment' ) )
	include_once 'Payment/Payment.php' ;

if( ! class_exists( 'MaxSMS\SMS' ) )
	include_once 'SMS/SMS.php' ;

if( ! class_exists( 'MaxTools\Secrets' ) )
	include_once 'Utilities/Utilities.php' ;

?>