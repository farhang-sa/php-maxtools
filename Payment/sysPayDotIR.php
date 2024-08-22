<?php namespace MaxPayment ;

defined( 'MaxPaymentInit' ) or die( 'Access Denied' );

abstract class PayDotIRBase implements Payment { 

	protected $url_charge = "https://pay.ir/pg" ;
	protected $url_verify = "https://pay.ir/pg/verify" ;
	protected $url_send   = "https://pay.ir/pg/send" ;
	
	protected abstract function getApi();

	public function charge_url( $token ){
		return $this->url_charge . "/{$token}" ;
	}

	public function send( $amount , $redirect, $mobile = null, 
			$factorNumber = null, $description = null) {
		return $this->curl_post($this->url_send, [
			'api'          => $this->getApi() ,
			'amount'       => $amount,
			'redirect'     => $redirect,
			'mobile'       => $mobile,
			'factorNumber' => $factorNumber,
			'description'  => $description,
		]);
	}

	public function verify( $token , $interfaceSecond = false ) {
		return $this->curl_post( $this->url_verify , [
			'api' 	=> $this->getApi() ,
			'token' => $token,
		]);
	}

	private function curl_post($url, $params)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
		]); $res = curl_exec($ch);
		curl_close($ch);
		return $res;
	}

}

?>