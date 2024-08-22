<?php namespace MaxSMS ;

define( 'MaxSMSInit' , true );

interface SMS {
    
    public function SendByCurl( $msg , $to , $PatternId );

    public function SendByGet( $msg , $to , $PatternId );

    public function SendBySoap( $msg , $to , $PatternId );
    
    public function SendVerificationCodeBySoap( $verification , $to , $PatternId );

    public function SendUnlockKeyBySoap( $key , $to , $PatternId );

}

include_once 'sysFardaPayamak.php' ;
include_once 'sysMeliPayamak.php' ;
include_once 'sysWebOneSMS.php' ;

?>