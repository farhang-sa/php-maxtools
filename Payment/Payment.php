<?php namespace MaxPayment ;

define( 'MaxPaymentInit' , true );

interface Payment {
    
    public function charge_url( $trans_id );
    
    public function send( $amount , $redirect, 
    	$mobile = null, $factorNumber = null, $description = null);
    
    public function verify( $amount , $trans_id );

}

include_once 'sysNextPay.php' ;
include_once 'sysPayDotIR.php' ;
include_once 'sysZarinPal.php' ;

?>