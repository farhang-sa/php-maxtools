<?php namespace MaxTools ;

// get json decoded to array
function json_str_to_array( $str ){

  if( ! $str )
    return null ;

  $ex = @json_decode( $str , true ) ;

  if ( ! is_array( $ex ) ){

    $ex = str_replace( "\'" , "'" , $str );
    $ex = str_replace( '\\"' , '"' , $ex);
    $ex = str_replace( '\\\\"' , '\\"' , $ex);
    $ex = preg_replace( '/\s+/' , ' ', $ex );
    $ex = @json_decode( $ex , true , 512 , 
      JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ;

  } return $ex ;

} 

// get array to json string
function json_array_to_str( $array , $pretty = false ){

    if( $pretty ) 
      return @json_encode( $array , 
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) ;
    else return @json_encode( $array , 
      JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ;

}

?>