<?php namespace MaxTools ;

#[AllowDynamicProperties]
abstract class Secrets {

	protected abstract function SecretsArray();

	public function __call( $name , $params ) {

		$Secrets = $this->SecretsArray();

		if( $Secrets === null )
			return null ;

		if( ! is_array( $Secrets ) )
			return null ;

		if( isset( $Secrets[ $name ] ) )
			return $Secrets[ $name ] ;

		if( strlen( $name ) <= 4 )
			return null ;

		if( strtowloer( substr( $name , 0 , 3 ) ) === 'get' )
			$name = substr( $name , 3 ) ;
		
		if( isset( $Secrets[ $name ] ) )
			return $Secrets[ $name ] ;

		return null ;

	}

}

?>