<?php namespace MaxSMS ;

defined( 'MaxSMSInit' ) or die( 'Access Denied' );

abstract class MeliPayamakBase implements SMS {

	private $CurlUrl = "https://rest.payamak-panel.com/api/SendSMS/BaseServiceNumber" ;
	private $GetUrl  = "http://api.payamak-panel.com/post/Send.asmx/SendByBaseNumber2" ;
	private $SoapUrl = "http://api.payamak-panel.com/post/send.asmx?wsdl" ;
    
    protected abstract function UserName();
	protected abstract function PassWord();
	protected abstract function PatternId( $pid );

	public function SendByCurl( $msg , $to , $PatternId = null ){
	    
	    $data = array (
			'username'  => $this->UserName() ,
			'password'  => $this->PassWord() ,
			'text'      => $msg ,
			'to'        => $to,
			'bodyId'    => $this->PatternId( $PatternId )
		);
        $post_data = http_build_query($data);
        $handle = curl_init( $this->CurlUrl );
        curl_setopt($handle, CURLOPT_HTTPHEADER, array(
            'content-type' => 'application/x-www-form-urlencoded'
        ));
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
        $response = curl_exec( $handle );
        
        if( is_string( $response ) && strlen( $response ) >= 15 ) 
            return true ;
		return false ;

	}

	public function SendByGet( $msg , $to , $PatternId = null ){

		$SMS = $this->GetUrl ;
		$SMS .= "?username=" . $this->UserName() ;
		$SMS .= "&password=" . $this->PassWord() ;
		$SMS .= "&to=" . $to ;
		$SMS .= "&text=" . $msg ;
		$SMS .= "&bodyId=" . $this->PatternId( $PatternId ) ;
		return @file_get_contents( $SMS ) ;

	}

	public function SendBySoap( $msg , $to , $PatternId = null ){

		ini_set("soap.wsdl_cache_enabled", "0");
		try { 
		    $sms = new SoapClient( $this->SoapUrl ,array('encoding'=>'UTF-8') );
			$data = array(
                "username"  => $this->UserName() ,
                "password"  => $this->PassWord() ,
                "text"      => array( $msg ) ,
                "to"        => $to ,
                "bodyId"    => $this->PatternId( $PatternId ) );
			return  $sms->SendByBaseNumber($data)->SendByBaseNumberResult;
		} catch (SoapFault $ex) { return $ex->faultstring; }

	}
	
	public function SendVerificationCodeBySoap( $verification , $to , $PatternId = null ){
        
		ini_set("soap.wsdl_cache_enabled", "0");
		try { 
		    $sms = new SoapClient( $this->SoapUrl ,array('encoding'=>'UTF-8') );
			$data = array(
                "username"  => $this->UserName() ,
                "password"  => $this->PassWord() ,
                "text"      => array( $verification ) ,
                "to"        => $to ,
                "bodyId"    => $this->PatternId( $PatternId ) 
            );
			$data = $sms->SendByBaseNumber($data)->SendByBaseNumberResult;
			return $data ;
		} catch (SoapFault $ex) { return $ex->faultstring; }
		
	}

	public function SendUnlockKeyBySoap( $key , $to , $PatternId = null ){

		ini_set("soap.wsdl_cache_enabled", "0");
		try { 
		    $sms = new SoapClient( $this->SoapUrl ,array('encoding'=>'UTF-8') );
			$data = array(
                "username"  => $this->UserName() ,
                "password"  => $this->PassWord() ,
                "text"      => array( $key ) ,
                "to"        => $to ,
                "bodyId"    => $this->PatternId( $PatternId ) 
            );
			$data = $sms->SendByBaseNumber($data)->SendByBaseNumberResult;
			return $data ;
		} catch (SoapFault $ex) { return $ex->faultstring; }
	}

} ?>