<?php namespace MaxSMS ;

defined( 'MaxSMSInit' ) or die( 'Access Denied' );

abstract class WebOneSMSBase implements SMS {

	protected $PostUrl = "http://webone-sms.ir/SMSInOutBox/Send" ;
	protected $GetUrl  = "http://webone-sms.ir/SMSInOutBox/SendSms" ;
	protected $SoapUrl = "http://payamak-service.ir/SendService.svc?wsdl" ;
	
	protected abstract function UserName();
	protected abstract function PassWord();
	protected abstract function FromNumb();

	public function SendByPost( $msg , $to , $PatternId = null ){

		$params = array( "UserName" => $this->UserName() , 
		    "Password" => $this->PassWord() );
		$params[ "From" ] = $this->FromNumb() ;
		$params[ "To" ] = $to ;
		$params[ "Message" ] = $msg ;

		// Build Http query using params
		$query = http_build_query ($params);

		// Create Http context details
		$contextData = array ( 
			'method' => 'POST', 
			'header' => "Connection: close\r\n".
			"Content-Length: ".strlen($query)."\r\n" , 
			"Content-type: application/x-www-form-urlencoded\r\n" , 
			'content'=> $query);

		// Create context resource for our request
		$context = stream_context_create (array( 'http' => $contextData ));

		// Read page rendered as result of your POST request
		return @file_get_contents( $this->PostUrl, false, $context);

	}

	public function SendByGet( $msg , $to , $PatternId = null ) {

		$SMS = $this->GetUrl ;
		$SMS .= "?username=" . $this->UserName() ;
		$SMS .= "&password=" . $this->PassWord() ;
		$SMS .= "&from=" . $this->FromNumb() ;
		$SMS .= "&to=" . $to ;
		$SMS .= "&text=" . $msg ;

		return @file_get_contents( $SMS ) ;

	}

	public function SendBySoap( $msg , $to , $PatternId = null ){

		ini_set("soap.wsdl_cache_enabled", "0");
		$sms_client = new SoapClient( $this->SoapUrl , array('encoding'=>'UTF-8'));

		try {
			$parameters['userName'] = $this->UserName() ;
			$parameters['password'] = $this->PassWord() ;
			$parameters['fromNumber'] = $this->FromNumb() ;
			$parameters['toNumbers'] = array( $to );
			$parameters['messageContent'] = $msg ;
			$parameters['isFlash'] = false;
			$recId = array();
			$status = array();
			$parameters['recId'] = &$recId ;
			$parameters['status'] = &$status ;
			return $sms_client->SendSMS($parameters)->SendSMSResult;
		} catch (Exception $e) { return false; }

	}

}

?>