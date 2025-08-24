<?php namespace MaxTools ;

// DS!
function DS(){
  return DIRECTORY_SEPARATOR ; }

// Require functions
$dirPath = realpath( __DIR__ ) . DS() . 'Functions' . DS() ;
foreach( scandir( $dirPath ) as $file )
  if( $file === '.' || $file === '..' )
    continue ;
  else if( strtolower( substr( $file , -4) ) === '.php' )
    include_once $dirPath . $file ;
  else die( $dirPath . $file );

// Get Human-Readable price
function getHumanPrice( $price ){
  return HumanPrice( $price ); }
function HumanPrice( $price ){
  return number_format( $price ); }

// Get Human-Readable File size
function getHumanFileSize( $size , $unit = '' ){
  return HumanFileSize($size , $unit ); }
function HumanFileSize( $size , $unit = '' ) {
    
  if( (!$unit && $size >= 1<<30) || $unit == 'GB' )
  
    return number_format($size/(1<<30),2) . 'GB';
    
  if( (!$unit && $size >= 1<<20) || $unit == 'MB' )
  
    return number_format($size/(1<<20),2) . 'MB' ;
    
  if( (!$unit && $size >= 1<<10) || $unit == 'KB' )
  
    return number_format($size/(1<<10),2) . 'KB' ;
    
  return number_format($size). ' bytes';
  
}

  
?>