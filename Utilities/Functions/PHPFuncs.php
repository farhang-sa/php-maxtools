<?php namespace MaxTools ;

// check if we are in cli ( command line , terminal , ... )
function isCli() { 
  return ( PHP_SAPI === 'cli' ) ? true : false ; }

// Find Exec available functions
function ExecFunctions(){

  $list = array( 'exec' , 'shell_exec' , 'system' , 'passthru' , 'backticks' );

  for( $i = 0 ; $i <= count( $list ) - 1 ; $i++ )
      if( ! function_exists( $list[$i] ) )
        unset( $list[$i] );

  return array_values( $list );
  
}

// Include_once file if found in $root ( Default $root is TPath_Root )
function Import( $import , $root = null , $ext = 'php' ) {

  if( ! $import || ! $root )
    return false ;
  
  // Trim The Import From " \/"
  $import = trim( $import , " /\\");
  
  // Check The Root Searching Area
  $inc = $root ;

  // Explode The File Package Name 
  $dires = explode( '.' , $import );  

  foreach( $dires as $k => $value ) {
    
    if ( $value == '*' ) {
          
      $scaned = scandir( $inc );
      
      foreach( $scaned as $phpFiles ) {
            
        $cFile = $inc . MaxTools_DS . $phpFiles;
            
        $info = pathinfo( $cFile );
            
        if ( is_file( $cFile ) && strtolower( $info['extension'] ) == 'php' ) 
          include_once $cFile;
            
        
          
      } return true;
        
    } else if ( stristr( $value , '*' ) && strlen( $value ) >= 2 ) {
          
      $starCounter = str_ireplace( '*' , '.*' , $value );
          
      $scaned = scandir( $inc );
      
      foreach( $scaned as $phpFiles ) {
        
        $match = array ();
        $exists = preg_match_all( "|{$starCounter}|" , $phpFiles , $match );
            
        if ( $exists ) {
              
          $cFile = $inc . MaxTools_DS . $match[0][0];
          
          if ( is_file( $cFile ) ) 
          	include_once( $cFile );
            
        }
          
      } return true;
        
    } else 
    	$inc .= MaxTools_DS . $value;
      
  } $ext = ( $ext ) ? $ext : 'php' ;
  
  $inc .= ".{$ext}" ;
  
  $inc = realpath( $inc ) ;

  if ( file_exists( $inc ) ) {
        
    include_once $inc;  
    return true;
      
  }
  
}

// Find if classes exists ( case insensitive )
function FindClass(){

  $funcArgs = func_get_args();

  $Class = array() ;

  foreach ( $funcArgs  as $value ) {

    $Class[] = trim( $value , " /\\");
    $Class[] = strtolower( $value );
    $Class[] = strtoupper( $value );
    $Class[] = ucfirst( $value );
    
  } $className = null ;

  foreach ( $Class as $value ) {
    
    if ( class_exists( $value ) ) {
      $className = $value ;
      break;
    }

  } return $className ;

}

// check if this array is indexed by numbers
function isIndexedArray( $array ){

  // php 8.1
  if ( function_exists('array_is_list') )
    return array_is_list($array);

  $i = 0 ;
  while( $i <= count( $array ) - 1 ){

    if( ! array_key_exists( $i , $array ) )
      return false ;
    $i++ ;

  } return true ;

}

// check if this array is Assoc
function isAssocArray( $array ){
  return ! isIndexedArray( $array ) ; }

// check if array is one dimensinal
function isOneDimensionalArray( $array ){

  foreach ( $array as $value ) 
    if( is_array( $value ) )
      return false ;

  return true ;

}

// get NEW-LINE : [ cli ? \n( PHP_EOL ) : <br />
function br( $c = 1 ) {

  $r = '';
  $e = MaxTools\isCli() ? PHP_EOL : PHP_EOL . '<br />' ;

  for( $i = 1 ; $i <= ( int ) $c ; $i++ ) 
    $r .= ' ' . $e ;
  
  return $r;
    
}

// Get ENTER replaced by \n ( new line )
function addSlashe( $StringsArray = array() ){

  $E = '
';
  foreach( $StringsArray as $k => $v ) {

    if ( is_array( $v ) ) foreach ( $v as $k1 => $v1 ) {

      if ( is_array( $v1 ) ) 
        $v[ $k1 ] = addSlashe( $v1 ) ;
      else if ( is_string( $v1 ) ) 
        $v[ $k1 ] = str_ireplace( $E , "\\n" , trim( addslashes( $v1 ) ) ) ;

    } else if ( is_string( $v ) )
      $v = str_ireplace( $E , "\\n" , trim( addslashes( $v ) ) ) ;

    $StringsArray[ $k ] = $v ;

  } return $StringsArray ;

}