<?php namespace MaxPayment ;

defined( 'MaxPaymentInit' ) or die( 'Access Denied' );

abstract class NextPayBase implements Payment {
    
    protected $url_charge   = "https://nextpay.org/nx/gateway/payment/" ;
    protected $url_send     = "https://nextpay.org/nx/gateway/token" ;  
    protected $url_verify   = "https://nextpay.org/nx/gateway/verify" ;  
    
    protected abstract function getApi();
    
    public function charge_url( $trans_id ){
    	return $this->url_charge . $trans_id ;
    }
    
    public function send( $amount , $redirect, $mobile = null, 
    		$factorNumber = null, $description = null){
    
        $curl = curl_init();
        $pf = 'api_key=' . $this->getApi() ;
        $pf .= '&amount=' . $amount ;
        $pf .= '&order_id=' . $factorNumber ;
        $pf .= '&customer_phone=' . $mobile ;
        if( $description )
            $pf .= '&custom_json_fields={ "description":"' . $description . '"}' ;
        $pf .= '&callback_uri=' . $redirect ;
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->url_send ,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $pf ,
        ));
        
        $response = curl_exec($curl);
        curl_close($curl);
        
        $response = json_decode( $response , true );
        $code = $response[ "code" ]; // -1 : success
        $trid = $response[ "trans_id" ];
        
        if( $code >= 0 )
        	return -1 ; // error 
        return $response ;
    
    }
    
    public function verify( $amount , $trans_id ){
    
        $curl = curl_init();
        $pf = 'api_key=' . $this->getApi() ;
        $pf .= '&amount=' . $amount ;
        $pf .= '&trans_id=' . $trans_id ;
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->url_verify ,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $pf ,
        ));
        
        $response = curl_exec($curl);
        curl_close($curl);
        
        $response = json_decode( $response , true );
        // $response[ 'code' ] = 0 : success
        // $response[ 'Shaparak_Ref_Id' ] - Peigiri
        // $respomse[ 'order_id' ]
        
        return $response ;
    
    }

} ?>