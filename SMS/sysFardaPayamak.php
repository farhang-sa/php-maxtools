<?php namespace MaxSMS ;

defined( 'MaxSMSInit' ) or die( 'Access Denied' );

abstract class FardaPayamakBase extends SMS {
	
    protected abstract function UserName();
	protected abstract function PassWord();
	protected abstract function FromNumb();

    public function GetRESTUrlPost(){ 
    	return 'https://ippanel.com/services.jspd'; }

    public function GetRESTUrlGET(){ 
    	return 'http://ippanel.com/class/sms/webservice/send_url.php'; }

    public function GetSOAPUrl(){ 
    	return 'http://ippanel.com/class/sms/wsdlservice/server.php?wsdl'; }

	protected function BuildDataArray( $msg , $to ){
		return array (
			'uname'     => $this->UserName() ,
			'pass'      => $this->PassWord() ,
			'from'      => $this->FromNumb() ,
			'message'   => $msg ,
			'to' 		=> json_encode( [ $to ] ),
			'op' 		=>'send'
		);
	}

	public function SendWithRESTCurl( $msg , $to , $PatternId = null ){

		$handler = $this->BuildDataArray( $msg , $to );
		$handler = $this->BuildCurlContext( $handler );
		
		$response = curl_exec( $handler );
		$response = json_decode( $response );
		$res_code = $response[0];
		$res_data = $response[1];
		
		return $res_data;

	}

	public function SendWithRESTPost( $msg , $to , $PatternId = null ){

		$handler = $this->BuildDataArray( $msg , $to );
		$handler = $this->BuildHttpPostRequestContext( $handler );
		
		return @file_get_contents( $this->GetRESTUrlPost() , false, $handler );

	}

	public function SendWithRESTGet( $msg , $to , $PatternId = null ) {

		$handler = array(
			'uname' 	=> $this->UserName() ,
		    'pass' 		=> $this->PassWord() ,
		    'from' 	   	=> $this->FromNumb() ,
		    'to'		=> $to ,
		    'msg'		=> $msg 
		);
		
		$handler = $this->GetRESTUrlGET() . '?' . http_build_query( $handler ) ;

		return @file_get_contents( $handler ) ;

	}

	public function SendWithSOAP( $msg , $to , $PatternId = null ){

		$client = $this->BuildSoapClient();

		try {
			$user 		= $this->UserName() ;
		    $pass 		= $this->PassWord() ;
		    $fromNum 	= $this->FromNumb() ;
		    $toNum 		= array( $to );
		    $messageContent = $msg ;
			$op  		= "send";
			//If you want to send in the future  ==> $time = '2016-07-30' //$time = '2016-07-30 12:50:50'
			$time = '';
			return  $client->SendSMS( $fromNum, $toNum, $messageContent, $user, $pass, $time, $op );
		} catch (SoapFault $ex) { return $ex->faultstring; }

	}

}

?>