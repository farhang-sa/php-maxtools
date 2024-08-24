<?php namespace MaxSMS ;

define( 'MaxSMSInit' , true );

abstract class SMS {

    public abstract function GetRESTUrlPost();
    public abstract function GetRESTUrlGET();
    public abstract function GetSOAPUrl();

    public abstract function SendWithRESTCurl( $msg , $to , $PatternId ); // Curl Post
    public abstract function SendWithRESTPost( $msg , $to , $PatternId ); // Http Post
    public abstract function SendWithRESTGet( $msg , $to , $PatternId ); // Http Get

    public abstract function SendWithSOAP( $msg , $to , $PatternId ); // Soap Service
    
    public function Send( $msg , $to , $PatternId = null ){ // REST Api
        
        // Pattern! send via soap
        if( is_array( $msg ) )

            return $this->SendWithSOAP( $msg , $to , $PatternId );

        // try order : rest-curl , rest-post , rest-get , soap
        if( $this->GetRESTUrlPost() ){
            
            if( extension_loaded( 'curl' ) && function_exists( 'curl_init' ) )
                return $this->SendWithRESTCurl( $msg , $to , $PatternId );
            else return $this->SendWithRESTPost( $msg , $to , $PatternId ); 

        } else if( $this->GetRESTUrlGET() ) 

            return $this->SendWithRESTGet( $msg , $to , $PatternId ); 

        else return $this->SendWithSOAP( $msg , $to , $PatternId );

    }

    // Builds CURL-POST Context
    protected function BuildCurlContext( $data ){
        
        $handle = curl_init( $this->GetRESTUrlPost() );
        
        curl_setopt($handle, CURLOPT_HTTPHEADER, array(
            'content-type' => 'application/x-www-form-urlencoded'
        ));
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'POST' );
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data );

        return $handle ;

    }

    // Build HTTP POST/GET Context
    protected function BuildHttpPostRequestContext( $data ) {

        // Build Http query using params
        $query = http_build_query( $data );

        // Create Http context details
        $contextData = array ( 
            'method' => 'POST' , 
            'header' => "Connection: close\r\n" .
                "Content-Length: " . strlen( $query ) ."\r\n" . 
                "Content-type: application/x-www-form-urlencoded\r\n" , 
            'content'=> $query );

        // Create context resource for our request
        return stream_context_create( array( 'http' => $contextData ) );
    }

    protected function BuildSoapClient(){
        
        ini_set("soap.wsdl_cache_enabled", "0");
        
        return new SoapClient( $this->GetSOAPUrl() , array( 'encoding' => 'UTF-8' ) );

    }

}

include_once 'sysFardaPayamak.php' ;
include_once 'sysMeliPayamak.php' ;
include_once 'sysWebOneSMS.php' ;

?>