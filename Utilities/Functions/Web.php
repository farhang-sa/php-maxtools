<?php namespace MaxTools ;

// check if we are in cli ( command line , terminal , ... )
function isCli() { 
  return ( PHP_SAPI === 'cli' ) ? true : false ; }

// get name of entering script file name ( start of php interpretation )
function ScriptFile(){
  
  $FileName = null ;
  
  if ( isset( $_SERVER[ 'SCRIPT_FILENAME' ] ) ) 
    $FileName = realpath( $_SERVER[ 'SCRIPT_FILENAME' ] );
  else if ( isCli() && isset( $argv[ 0 ] ) ) 
    $FileName = $argv[ 0 ] ;
  else $FileName = 'Unknown' ;
  
  return $FileName;
  
}

// get web schame ( http , https , ...)
function WebSchame(){
  
  $schame = ( isset( $_SERVER['REQUEST_SCHEME'] ) ) ? strtolower( $_SERVER['REQUEST_SCHEME'] ) : null ;
  $schame = $schame ? $schame : 'http' ;
  if( is_string( $schame ) ) 
    $schame = trim( $schame , " /\\") ;
  return isCli() ? 'cmd' : strtolower( $schame ) ;
  
}

// get domain name
function WebDomain(){
  
  // just ted.ir || www.ted.ir || localhost || 127.0.0.1  
  $domain = ( isset( $_SERVER[ 'SERVER_NAME' ] ) ) ? $_SERVER[ 'SERVER_NAME' ] : null ;

  if ( $domain === null && isset( $_SERVER[ 'HTTP_HOST' ] ) ) {
    
    $domain = $_SERVER[ 'HTTP_HOST' ] ;
    
    if( stristr( $domain , ':' ) ) {
      
      $domain = trim( $domain , ':' );
      $explo = explode( ':' , $domain , 2 );
      $domain = $explo[ 0 ] ;
      
    }
    
  } is_string( $domain ) && $domain = trim( $domain , ' /' ) ;
  
  return $domain ;
  
}

// get full domain name
function WebDomainFull(){
 
  	//full http/https Acess
  	//http://user@xxx.ir:8080 OR http://user@www.xxx.ir:8080 OR 
  	//http://user@localhost:8080 OR http://user@127.0.0.1:8080

  	$scheme = WebSchame();
  	$domain = WebDomain();

  	if( ! isCli() && $domain === null )
  		die( 'WebDomainFull() error' );

	$port = isset( $_SERVER['SERVER_PORT'] ) ? ( int ) $_SERVER['SERVER_PORT'] : 0 ;
	$port = $port === 0 && $scheme === 'http' ? 80 : $port ;
	$port = $port === 0 && $scheme === 'https' ? 443 : $port ;
  	
  	$domain = trim( $domain , '/' );
  	
	if( $scheme === 'http' ) // no need 80 for http
		$domain .= ( $port !== 80 ) ? ':' . $port : '' ; 
	else if( $scheme === 'https' ) // no need 80/443 for https
		$domain .= ( $port !== 443 && $port !== 80 ) ? ':' . $port : '' ; 
  
  	$authUser = ( isset( $_SERVER['PHP_AUTH_USER'] ) ) ? $_SERVER['PHP_AUTH_USER'] : null ;
  	// $authPass = ( isset( $_SERVER['PHP_AUTH_PW'] ) ) ? $_SERVER['PHP_AUTH_PW'] : null ;
  	$authInfo = ( $authUser ) ? $authUser : null ;
  	// no pass in url!!!! 
  	// $authInfo && $authInfo .= ( $authPass ) ? ":" . $authPass : "" ;
    
  	$domain = ( $authInfo !== null ) ? "{$authInfo}@{$domain}" : $domain ;
  	
  	return trim( $scheme . '://' . $domain , ' /' );
  
}

// finds exec dir's web-path relavent to site's root;
// SCRIPT_FILENAME 	-> F:\my\path\to\site.com\dir1\dir2\file.php
// SCRIPT_NAME 		-> /dir1/dir2/file.php
// Returns -> /dir1/dir2/
function WebPath( $root ){
	
	// different than ScriptFile()
	$host = ( isset( $_SERVER['SCRIPT_NAME'] ) ) ? $_SERVER['SCRIPT_NAME'] : null ;
	if ( ! $host ) 
		return __FILE__ ;

	$host = str_ireplace( MaxTools_DS , '/' , dirname( $host ) ) ;
	if ( $host === '/' ) 
		return '/' ;

	$path = null ;

	$execPath = realpath( dirname( ScriptFile() ) ); 

	if ( stristr( $execPath , $root ) !== false ){

		// dire3
		$path = str_ireplace( $root , '' , $execPath );
		$path = str_ireplace( MaxTools_DS , '/' , $path );
		$path = trim( $path , " /\\") ;

		if ( strlen( $path ) == 0 ) 
			$path = $host ;
		else {

			$e = explode( '/' , $path );
			$sp = $host ;

			foreach ($e as $value ) {
				
				$sp = str_replace( $value , '' , $sp );
				$sp  = str_ireplace( "//" , '' , $sp );
				$sp  = trim( $sp , " /\\") ;

			} $path = $sp ;

		}
		
	} else {
			
		$FilePath = explode( MaxTools_DS , $execPath );
		$RootPath = explode( MaxTools_DS , $root );

		foreach ( $RootPath as $key => $value ) 
			foreach ( $FilePath as $key2 => $value2 ) 
				if ( $value === $value2 ) 
					unset( $FilePath[ $key2 ] , $RootPath[ $key ] ) ;

		$sp = $host ;

		foreach ( $FilePath as $key => $value) {

			$replace = '' ;
			
			if ( isset( $RootPath[ $key ] ) ){

				$replace = $RootPath[ $key ] ; 
				unset( $RootPath[ $key ] );

			} $sp = str_ireplace( $value , $replace , $sp );

			$sp  = str_ireplace( "//" , '' , $sp );
			$sp  = trim( $sp , " /\\") ;

		} $sp .= '/' . join( $RootPath , '/' ) ;
		
		$path = $sp ;

	} return '/' . trim( $path , " /\\" ) ;
	
}

// Get Web-Link ( Direct-Link ) of a file
function FindWebPath( $address = null , $root ){
    
	if ( ! file_exists( $address ) ) 
		return false;
    
	$webAddress = '' ;
	
    //$WebRoot = TWeb_URL ;
	$WebRoot = WebDomainFull() . WebPath( $root ) ; 

	if ( stristr( $address , $root ) ){
		
		$webAddress = str_ireplace( $root , '' , $address );
		$webAddress = str_ireplace( MaxTools_DS , '/' , $webAddress );	
		$webAddress = trim( $webAddress , " \\/");
		$webAddress = trim( $WebRoot , " \\/") . '/' . $webAddress;
		
	} else {
	    
	    // Find Base Web Break
		$TedUrl = explode( "//" , $WebRoot , 2 ) ;
		$http = $TedUrl[ 0 ] ;
		unset( $TedUrl[ 0 ] );
		$TedUrl = explode( '/' , $TedUrl[ 1 ] ) ;
		$http .= "//" . $TedUrl[ 0 ] ;
		unset( $TedUrl[ 0 ] );
		
		$dif = str_ireplace( $http , "" , $WebRoot );
		$dif = trim( $dif , ' /' ); 
		$dif = explode( '/' , $dif ); // Last Path

		$FilePath = explode( MaxTools_DS , $address );
		$RootPath = explode( MaxTools_DS , $root );
		$Route = array();
		$isPathClear = false ;
		
		foreach( $RootPath as $n => $v ) 
		    if( isset( $FilePath[ $n ] ) ) 
		    	if ( strtolower( $v ) == strtolower( $FilePath[ $n ] ) ){
				
			unset( $RootPath[ $n ] , $FilePath[ $n ] ) ;
			
			$Route[] = $v ;
			
			if( ! $isPathClear ) foreach( $dif as $np ) 
                if( strtolower( $np ) == strtolower( $v ) )
                    $isPathClear = true ;
				
		} // Route Now Contains Path To First Breaking Of Roots
		
		$FilePath = array_values( $FilePath ) ;
		
		foreach( $FilePath as $newPath ) 
		    $Route[] = $newPath ;
		
		$TedUrl = array_values( $TedUrl ) ;
		
		if( ! $isPathClear ) // Not Same Path : Just Add FilePath To Http !
		
		    $http .= '/' . implode( '/' , $FilePath ) ;
		
		else foreach( $TedUrl as $k => $v ){
			
			$break = false ;
			
			foreach( $Route as $l => $p ){
				
				if ( $v == $p ) {
					
					$np = array_slice( $Route , $l ) ;
					$http .= '/' . implode( '/' , $np ) ;
					$break = true ;
					break;
					
				}
				
			} if ( $break ) 
				break ;
			else $http .= '/' . $v ;
			
		} $webAddress = $http ;
		
	} $webAddress = trim( $webAddress , " /\\" ) ;
	
	return $webAddress ;

}


// Handle Multipart file uploads ( just give the upload handle )
function ListMultipartUploads( $MPUF ) {

	if ( ! isset( $MPUF[ 'name' ] ) ) 
		return $MPUF ;

  	if ( ! is_array( $MPUF[ 'name' ] ) ) 
  		return array( $MPUF ) ;

	$len = count( $MPUF[ 'name' ] ) - 1 ;

	$nMPUF = array() ;

	for ( $i = 0 ; $i <= $len ; $i++ ) { 

		$nf = array() ;

		if ( isset( $MPUF[ 'name' ][$i] ) )   
		  $nf['name']   = $MPUF[ 'name' ][$i] ;

		if ( isset( $MPUF[ 'type' ] ) && isset( $MPUF[ 'type' ][$i] ) )   
		  $nf['type']   = $MPUF[ 'type' ][$i] ;

		if ( isset( $MPUF[ 'tmp_name' ] ) && isset( $MPUF[ 'tmp_name' ][$i] ) ) 
		  $nf['tmp_name' ] = $MPUF[ 'tmp_name' ][$i] ;

		if ( isset( $MPUF[ 'error' ] ) && isset( $MPUF[ 'error' ][$i] ) ) 
		  $nf['error']  = $MPUF[ 'error' ][$i] ;

		if ( isset( $MPUF[ 'size' ] ) && isset( $MPUF[ 'size' ][$i] ) ) 
		  $nf[ 'size' ]   = $MPUF[ 'size' ][$i] ;

		if ( count( $nf ) < 4  ) 
			continue ;
		if ( isset( $nf['error'] ) && $nf['error'] === 0 ) 
			$nMPUF[$i] = $nf ;

	} return $nMPUF ;

}

?>