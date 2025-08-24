<?php namespace MaxPayment ;

define( 'MaxPaymentInit' , true );

interface Payment {
    
    public function charge_url( $trans_id );
    
    public function send( $amount , $redirect, 
    	$mobile = null, $factorNumber = null, $description = null);
    
    public function verify( $amount , $trans_id );

}

$dirPath = realpath( __DIR__ ) . DIRECTORY_SEPARATOR ;
include_once $dirPath . 'sysNextPay.php' ;
include_once $dirPath . 'sysPayDotIR.php' ;
include_once $dirPath . 'sysZarinPal.php' ;

?>