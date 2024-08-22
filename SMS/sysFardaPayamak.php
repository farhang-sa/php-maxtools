<?php namespace MaxSMS ;

defined( 'MaxSMSInit' ) or die( 'Access Denied' );

abstract class FardaPayamakBase implements SMS {

	private $CurlUrl = "https://ippanel.com/services.jspd" ;
	private $GetUrl  = "http://ippanel.com/class/sms/webservice/send_url.php" ;
	private $SoapUrl = "http://ippanel.com/class/sms/wsdlservice/server.php?wsdl" ;
	
    protected abstract function UserName();
	protected abstract function PassWord();
	protected abstract function FromNumb();

	public function SendByCurl( $msg , $to , $PatternId = null ){

		$url = "";
		
		$rcpt_nm = array( $to );
		$param = array (
			'uname'     => $this->UserName() ,
			'pass'      => $this->PassWord() ,
			'from'      => $this->FromNumb() ,
			'message'   => $msg ,
			'to'=>json_encode($rcpt_nm),
			'op'=>'send'
		);
					
		$handler = curl_init($this->CurlUrl);             
		curl_setopt($handler, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($handler, CURLOPT_POSTFIELDS, $param);                       
		curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
		$response2 = curl_exec($handler);
		
		$response2 = json_decode($response2);
		$res_code = $response2[0];
		$res_data = $response2[1];
		
		return $res_data;

	}

	public function SendByGet( $msg , $to , $PatternId = null ) {

		$SMS = $this->GetUrl ;
		$SMS .= "?uname=" . $this->UserName() ;
		$SMS .= "&pass=" . $this->PassWord() ;
		$SMS .= "&from=" . $this->FromNumb() ;
		$SMS .= "&to=" . $to ;
		$SMS .= "&msg=" . $msg ;
		return @file_get_contents( $SMS ) ;

	}

	public function SendBySoap( $msg , $to , $PatternId = null ){

		ini_set("soap.wsdl_cache_enabled", "0");
		try { $client = new SoapClient( $this->SoapUrl , array('encoding'=>'UTF-8') );
			$user 		= $this->UserName() ;
		    $pass 		= $this->PassWord() ;
		    $fromNum 	= $this->FromNumb() ;
		    $toNum 		= array( $to );
		    $messageContent = $msg ;
			$op  		= "send";
			
			//If you want to send in the future  ==> $time = '2016-07-30' //$time = '2016-07-30 12:50:50'
			$time = '';
	
			return  $client->SendSMS( $fromNum, $toNum, $messageContent, $user, $pass, $time, $op);
		} catch (SoapFault $ex) { return $ex->faultstring; }

	}

	public function SendVerificationCodeBySoap( $verification , $to , $PatternId = null ){
		$client = new SoapClient( $this->SoapUrl , array('encoding'=>'UTF-8') ) ;
		$user 		= $this->UserName() ;
	    $pass 		= $this->PassWord() ;
	    $fromNum 	= $this->FromNumb() ;
		$toNum = array( $to ); 
		$pattern_code = "jr0x27j55s"; 
		$input_data = array( "amuzak-verification-code" => $verification ); 

		return $client->sendPatternSms( $fromNum, $toNum, $user, $pass, $pattern_code, $input_data);
	}

	public function SendUnlockKeyBySoap( $key , $to , $PatternId = null ){
		$client = new SoapClient( $this->SoapUrl , array('encoding'=>'UTF-8') ) ;
		$user 		= $this->UserName() ;
	    $pass 		= $this->PassWord() ;
	    $fromNum 	= $this->FromNumb() ;
		$toNum = array( $to ); 
		$pattern_code = "tsi26hppv4"; 
		$input_data = array( "unlock-key" => $key ); 

		return $client->sendPatternSms( $fromNum, $toNum, $user, $pass, $pattern_code, $input_data);
	}

}

?>