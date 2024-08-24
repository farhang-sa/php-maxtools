<?php namespace MaxSMS ;

defined( 'MaxSMSInit' ) or die( 'Access Denied' );

abstract class MeliPayamakBase extends SMS {

    protected abstract function UserName();
	protected abstract function PassWord();
	protected abstract function FromNumb();

    public function GetRESTUrlPost(){ 
    	return 'https://rest.payamak-panel.com/api/SendSMS/SendSMS'; }

    public function GetRESTUrlGET(){ // soap as get-api !!!
    	return 'http://api.payamak-panel.com/post/Send.asmx/SendByBaseNumber2' ; }

    public function GetSOAPUrl(){ 
    	return 'http://api.payamak-panel.com/post/send.asmx?wsdl'; }

	protected function BuildDataArray( $msg , $to ){
		$data = array( 
			'username' 	=> $this->UserName() , // YES UserName not Username
		    'password' 	=> $this->PassWord() ,
		    'to' 		=> $to ,
		    'text' 		=> $msg 
		);

		if( $this->FromNumb() )
			$data[ 'from' ] = $this->FromNumb();

		return $data ;
	}

	public function SendWithRESTCurl( $msg , $to , $PatternId = null ){
	    
		$handler = $this->BuildDataArray( $msg , $to );
		if( $PatternId )
			$handler[ 'bodyId' ] = $PatternId ;
		$handler = $this->BuildCurlContext( $handler );

        $response = curl_exec( $handler );
        
        if( is_string( $response ) && strlen( $response ) >= 15 ) 
            return true ;
		return false ;

	}

	public function SendWithRESTPost( $msg , $to , $PatternId = null ){

		$handler = $this->BuildDataArray( $msg , $to );
		if( $PatternId )
			$handler[ 'bodyId' ] = $PatternId ;
		$handler = $this->BuildHttpPostRequestContext( $handler );

		return @file_get_contents( $this->GetRESTUrlPost() , false, $handler );

	}

	public function SendWithRESTGet( $msg , $to , $PatternId = null ){

		$handler = $this->BuildDataArray( $msg , $to );
		if( $PatternId )
			$handler[ 'bodyId' ] = $PatternId ;
		
		$handler = $this->GetRESTUrlGET() . '?' . http_build_query( $handler ) ;

		return @file_get_contents( $handler ) ;

	}

	public function SendWithSOAP( $msg , $to , $PatternId = null ){

		$client = $this->BuildSoapClient();

		try { 

			if( ! is_array( $msg ) )
				$msg = array( $msg ); // fills {0}

			$handler = $this->BuildDataArray( $msg , $to );

			if( $PatternId ){

				$handler[ 'bodyId' ] = $PatternId ;

				return $client->SendSimpleSMS( $handler )->SendSimpleSMSResult;

			} else return $client->SendSimpleSMS( $handler )->SendSimpleSMSResult;

		} catch (SoapFault $ex) { return $ex->faultstring; }

	}

} ?>