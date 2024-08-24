<?php namespace MaxSMS ;

defined( 'MaxSMSInit' ) or die( 'Access Denied' );

abstract class WebOneSMSBase extends SMS {

	protected abstract function UserName();
	protected abstract function PassWord();
	protected abstract function FromNumb();

    public function GetRESTUrlPost(){ 
    	return 'http://webone-sms.ir/SMSInOutBox/Send'; }

    public function GetRESTUrlGET(){ 
    	return 'http://webone-sms.ir/SMSInOutBox/SendSms'; }

    public function GetSOAPUrl(){ 
    	return 'http://payamakapi.ir/SendService.svc?wsdl'; }

	protected function BuildPostDataArray( $msg , $to ){
		return array( 
			'UserName' 	=> $this->UserName() , // YES UserName not Username
		    'Password' 	=> $this->PassWord() ,
		    'From' 		=> $this->FromNumb() ,
		    'To' 		=> $to ,
		    'Message' 	=> $msg 
		);
	}

	public function SendWithRESTCurl( $msg , $to , $PatternId = null ){

		$handler = $this->BuildPostDataArray( $msg , $to );
		$handler = $this->BuildCurlContext( $handler );
		
		$response = curl_exec( $handler );
		$response = json_decode( $response );
		$res_code = $response[0];
		$res_data = $response[1];
		
		return $res_data;

	}

	public function SendWithRESTPost( $msg , $to , $PatternId = null ){

		$handler = $this->BuildPostDataArray( $msg , $to );
		$handler = $this->BuildHttpPostRequestContext( $handler );

		return @file_get_contents( $this->GetRESTUrlPost() , false, $handler );

	}

	public function SendWithRESTGet( $msg , $to , $PatternId = null ) {

		$handler = array(
			'username' 	=> $this->UserName() ,
		    'password' 	=> $this->PassWord() ,
		    'from' 	   	=> $this->FromNumb() ,
		    'to'		=> $to ,
		    'text'		=> $msg 
		);
		
		$handler = $this->GetRESTUrlGET() . '?' . http_build_query( $handler ) ;

		return @file_get_contents( $handler ) ;

	}

	public function SendWithSOAP( $msg , $to , $PatternId = null ){

		$client = $this->BuildSoapClient();

		try {
			$handler['userName'] = $this->UserName() ;
			$handler['password'] = $this->PassWord() ;
			$handler['fromNumber'] = $this->FromNumb() ;
			$handler['toNumbers'] = array( $to );
			$handler['messageContent'] = $msg ;
			$handler['isFlash'] = false;
			$recId = array();
			$status = array();
			$handler['recId'] = &$recId ;
			$handler['status'] = &$status ;
			return $client->SendSMS( $handler )->SendSMSResult;
		} catch ( Exception $e ) { return false; }

	}

}

?>