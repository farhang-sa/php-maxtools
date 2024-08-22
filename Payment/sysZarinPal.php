<?php namespace MaxPayment ;

defined( 'MaxPaymentInit' ) or die( 'Access Denied' );

abstract class ZarinPalBase implements Payment { 

	protected $url_charge = "https://www.zarinpal.com/pg/StartPay/" ;
	protected $url_verify = "https://api.zarinpal.com/pg/v4/payment/verify.json" ;
	protected $url_send   = "https://api.zarinpal.com/pg/v4/payment/request.json" ;
	
	protected abstract function getMid();
	
	public function charge_url( $authority ){
		return $this->url_charge . $authority ;
	}

	public function send( $amount , $redirect , $mobile = null, 
			$factorNumber = null, $description = null){
		$mobile = $mobile != null ? $mobile : "09017553442" ;
		$description = $description != null ? $description : "Facture Checkout" ;
        
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $this->url_send ,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>'{
		  "merchant_id": "' . $this->getMid() . '" ,
		  "amount": "' . $amount . '" ,
		  "callback_url": "' . $redirect . '" ,
		  "description": "' . $description . '" ,
		  "metadata": {
		    "mobile": "' . $mobile . '"
		  }
		}',
		  CURLOPT_HTTPHEADER => array(
		    'Content-Type: application/json',
		    'Accept: application/json'
		  ),
		));

		$response = curl_exec($curl);
		curl_close($curl);

		$response = json_decode( $response , true );
		$data = $response[ "data" ];
		
		if( empty( $data ) && isset( $response["error"] ) )
			return -1 ;
		return $data;

	}

	public function verify( $amount , $authority ){

		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $this->url_verify ,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>'{
		  "merchant_id": "' . $this->getMid() .'",
		  "amount": "' . $amount . '",
		  "authority": "' . $authority . '"
		}',
		  CURLOPT_HTTPHEADER => array(
		    'Content-Type: application/json',
		    'Accept: application/json'
		  ),
		));

		$response = curl_exec( $curl );
		curl_close($curl);
		
		$response = json_decode( $response , true );
		$data = $response[ "data" ];
		
		if( empty( $data ) && isset( $response["error"] ) )
			return -1 ;

		return $data ;

	}

} ?>